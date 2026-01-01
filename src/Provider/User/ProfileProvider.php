<?php


namespace App\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Webmozart\Assert\Assert;

class ProfileProvider implements ProviderInterface
{
  public function __construct(
    private readonly Security $security,
    private readonly EntityManagerInterface $entityManager
  ) {}

  /**
   * Retrieve the current authenticated user's profile
   * 
   * @return User The authenticated user
   * @throws UnauthorizedHttpException if no user is authenticated
   */
  public function provide(Operation $operation, array $uriVariables = [], array $context = []): User
  {
    $myUser = $this->security->getUser();

    Assert::isInstanceOf($myUser, User::class, 'User not found');

    // Update last active timestamp
    $myUser->lastActiveAt = new \DateTimeImmutable();
    $this->entityManager->flush();

    return $myUser;
  }
}
