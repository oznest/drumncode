<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Infrastructure\DTO\User\UserRegisterDto;

readonly class RegisterUserCommand
{
    public string $email;
    public string $password;
    public function __construct(
        UserRegisterDto $userRegisterDto
    ) {
        $this->email = $userRegisterDto->email;
        $this->password = $userRegisterDto->password;
    }
}
