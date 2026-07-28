<?php

namespace MAC\Models\Auth\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use MAC\Models\Auth\Mail\OtpMail;
use MAC\Models\Auth\PasswordResetOtp;
use MAC\Models\User\User;

final class RequestPasswordResetOtpAction
{
    private const int EXPIRES_IN_MINUTES = 15;

    public function handle(string $email): void
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return;
        }

        PasswordResetOtp::query()->where('email', $email)->delete();

        $code = (string) random_int(100000, 999999);

        PasswordResetOtp::create([
            'email' => $email,
            'code' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRES_IN_MINUTES),
        ]);

        Mail::to($email)->send(new OtpMail($code, self::EXPIRES_IN_MINUTES));
    }
}
