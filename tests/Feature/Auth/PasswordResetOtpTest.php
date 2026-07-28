<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use MAC\Models\Auth\Mail\OtpMail;
use MAC\Models\Auth\PasswordResetOtp;
use MAC\Models\User\User;
use Tests\TestCase;

class PasswordResetOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_otp_for_existing_email(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);

        $response->assertOk();
        $this->assertDatabaseCount('password_reset_otps', 1);
        Mail::assertSent(OtpMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_forgot_password_returns_generic_response_for_unknown_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/forgot-password', ['email' => 'nao-existe@example.com']);

        $response->assertOk();
        $this->assertDatabaseCount('password_reset_otps', 0);
        Mail::assertNothingSent();
    }

    private function captureOtpCode(string $email): string
    {
        Mail::fake();
        $code = null;

        $this->postJson('/api/auth/forgot-password', ['email' => $email]);

        Mail::assertSent(OtpMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        return $code;
    }

    public function test_can_reset_password_with_valid_code(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        $code = $this->captureOtpCode($user->email);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => $code,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('password_reset_otps', 0);

        $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'new-password-123'])
            ->assertOk();
    }

    public function test_reset_password_fails_with_wrong_code(): void
    {
        $user = User::factory()->create();
        $this->captureOtpCode($user->email);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => '000000',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('code');
        $this->assertEquals(1, PasswordResetOtp::first()->attempts);
    }

    public function test_reset_password_fails_with_expired_code(): void
    {
        $user = User::factory()->create();
        $code = $this->captureOtpCode($user->email);

        PasswordResetOtp::query()->update(['expires_at' => now()->subMinute()]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => $code,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('code');
    }

    public function test_otp_is_invalidated_after_too_many_wrong_attempts(): void
    {
        $user = User::factory()->create();
        $code = $this->captureOtpCode($user->email);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/reset-password', [
                'email' => $user->email,
                'code' => '000000',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);
        }

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => $code,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('code');
        $this->assertDatabaseCount('password_reset_otps', 0);
    }

    public function test_requesting_new_otp_invalidates_previous_one(): void
    {
        $user = User::factory()->create();
        $firstCode = $this->captureOtpCode($user->email);
        $secondCode = $this->captureOtpCode($user->email);

        $this->assertNotEquals($firstCode, $secondCode);
        $this->assertDatabaseCount('password_reset_otps', 1);

        $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => $firstCode,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertStatus(422);
    }
}
