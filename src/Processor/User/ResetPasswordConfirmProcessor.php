<?php

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\User\ResetPassword;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Webmozart\Assert\Assert;

#[AutoconfigureTag('api_platform.state_processor')]
class ResetPasswordConfirmProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly UserRepository $userRepository
  ) {}

  /**
   * @param ResetPassword $data
   */
  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
  {
    $user = $this->userRepository->findOneBy(['email' => $data->email]);

    Assert::notNull($user, 'User not found');
    Assert::same($user->verificationCode, $data->token, 'Invalid reset code');
    Assert::greaterThan($user->verificationCodeExpiry, time(), 'Reset code has expired. Please request a new one.');

    $user->password = password_hash($data->newPassword, PASSWORD_DEFAULT);
    $user->verificationCode = '';
    $user->verificationCodeExpiry = 0;

    $this->entityManager->flush();

    return new JsonResponse([
      'success' => true,
      'message' => 'Password has been reset successfully. You can now login with your new password.',
    ]);
  }
}
