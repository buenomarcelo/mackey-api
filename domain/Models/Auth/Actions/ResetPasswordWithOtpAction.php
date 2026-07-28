<?php

namespace MAC\Models\Auth\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use MAC\Models\Auth\PasswordResetOtp;
use MAC\Models\User\User;

final class ResetPasswordWithOtpAction
{
    private const int MAX_ATTEMPTS = 5;

    public function handle(string $email, string $code, string $password): void
    {
        $otp = PasswordResetOtp::query()
            ->where('email', $email)
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();

        if (! $otp) {
            throw ValidationException::withMessages([
                'code' => [__('auth.otp_invalid_or_expired')],
            ]);
        }

        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            $otp->delete();

            throw ValidationException::withMessages([
                'code' => [__('auth.otp_too_many_attempts')],
            ]);
        }

        if (! Hash::check($code, $otp->code)) {
            $otp->increment('attempts');

            throw ValidationException::withMessages([
                'code' => [__('auth.otp_invalid_or_expired')],
            ]);
        }

        $user = User::query()->where('email', $email)->firstOrFail();
        $user->update(['password' => Hash::make($password)]);

        PasswordResetOtp::query()->where('email', $email)->delete();
    }
}
