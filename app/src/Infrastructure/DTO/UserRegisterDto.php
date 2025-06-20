<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[UniqueEntity(fields: ['email'], message: 'This email is already in use.')]
class UserRegisterDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
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
