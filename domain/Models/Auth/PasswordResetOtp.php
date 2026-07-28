<?php

namespace MAC\Models\Auth;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['email', 'code', 'attempts', 'expires_at'])]
class PasswordResetOtp extends Model
{
    protected $table = 'password_reset_otps';

    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }
}
