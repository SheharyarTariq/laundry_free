<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Service\EmailService;
use App\Entity\User;

#[AutoconfigureTag('api_platform.state_processor')]
class RegisterProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EmailService $emailService
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {//Todo: what is below line doing instead of Assert::isInstanceOf($data, User::class, 'Expected User entity');
        /** @var User $user */
        $user = $data;

        $password = $user->password;
        $user->password = password_hash($password, PASSWORD_DEFAULT);

        $verificationCode = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $user->verificationCode = $verificationCode;

        $user->verificationCodeExpiry = time() + (15 * 60);

        $user->isActive = false;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $emailSent = $this->emailService->sendVerificationCode($user->email, $verificationCode);

        if (!$emailSent) {
            throw new \RuntimeException('Failed to send verification email. Please try again.');
        }

        return $user;
    }
}
