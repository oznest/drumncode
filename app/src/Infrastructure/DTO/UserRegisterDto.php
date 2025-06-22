<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO;

use App\Application\Validator\UniqueEmail;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserRegisterDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[UniqueEmail]
    public string $email;

    #[Assert\NotBlank]
    public string $password;

    #[Assert\NotBlank]
    #[SerializedName('confirm_password')]
    public string $confirmPassword;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->password !== $this->confirmPassword) {
            $context->buildViolation('Passwords do not match.')
                ->atPath('confirmPassword')
                ->addViolation();
        }
    }
}
