<?php

namespace MAC\Models\User\DTO;

use Illuminate\Http\Request;

final readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public bool $isAdmin,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->string('name')->toString(),
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString() ?: null,
            isAdmin: $request->boolean('is_admin'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'is_admin' => $this->isAdmin,
        ], fn ($value) => $value !== null);
    }
}
