<?php

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\UserRepository;
use Webmozart\Assert\Assert;

#[AutoconfigureTag('api_platform.state_processor')]
class ResetPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        private readonly UserRepository $userRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $request = $this->requestStack->getCurrentRequest();
        $payload = json_decode($request->getContent(), true);

        $email = $payload['email'] ?? null;
        $code = $payload['code'] ?? $payload['verificationCode'] ?? null;
        $newPassword = $payload['password'] ?? null;
        $confirmPassword = $payload['confirmPassword'] ?? null;

        Assert::notEmpty($email, 'Email is required');
        Assert::notEmpty($code, 'Code is required');
        Assert::notEmpty($newPassword, 'New password is required');
        Assert::notEmpty($confirmPassword, 'Confirm password is required');
        Assert::same($newPassword, $confirmPassword, 'Passwords do not match');
        Assert::greaterThanLength($newPassword, 5, 'Password must be at least 6 characters long');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        Assert::notNull($user, 'User not found');

        Assert::same($user->verificationCode, $code, 'Invalid reset code');

        Assert::lessThan($user->verificationCodeExpiry, time(), 'Reset code has expired. Please request a new one.');

        $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->verificationCode = '';
        $user->verificationCodeExpiry = 0;

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Password has been reset successfully. You can now login with your new password.'
        ]);
    }
}
