<?php

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\User\ResetPassword;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Webmozart\Assert\Assert;

#[AutoconfigureTag('api_platform.state_processor')]
class ChangePasswordProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly Security $security,
    private readonly UserPasswordHasherInterface $passwordHasher
  ) {}

  /**
   * @param ResetPassword $data
   */
  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
  {
    $user = $this->security->getUser();
    Assert::isInstanceOf($user, User::class, 'You must be logged in to change your password.');

    /** @var User $user */
    if (!$this->passwordHasher->isPasswordValid($user, $data->currentPassword)) {
      return new JsonResponse([
        'success' => false,
        'message' => 'Invalid current password.',
      ], 400);
    }

    $user->setPassword($this->passwordHasher->hashPassword($user, $data->newPassword));
    $this->entityManager->flush();

    return new JsonResponse([
      'success' => true,
      'message' => 'Password has been changed successfully.',
    ]);
  }
}
