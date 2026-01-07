<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Processor\User\RegisterProcessor;
use App\Processor\User\ProfileProcessor;

use App\Provider\User\ProfileProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
  operations: [
    new Post(
      uriTemplate: '/users/register',
      processor: RegisterProcessor::class,
      validationContext: [
        'groups' => ['Default', 'Valid(User:Register)'],
      ],
      normalizationContext: [
        'groups' => ['User:V$Create']
      ],
      denormalizationContext: [
        'groups' => ['User:W$Create']
      ],
    ),
    new Get(
      uriTemplate: '/user/profile',
      provider: ProfileProvider::class,
      security: "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')",
      normalizationContext: [
        'groups' => ['User:V$Profile']
      ],
    ),
    new Put(
      uriTemplate: '/profile',
      processor: ProfileProcessor::class,
      validationContext: [
        'groups' => ['Default', 'Valid(User:UpdateProfile)'],
      ],
      validate: false,
      security: "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')",
      denormalizationContext: [
        'groups' => ['User:W$UpdateProfile']
      ],
    ),
  ]
)]

#[UniqueEntity(fields: ['email'], message: 'This email is already registered. Please use a different email or try logging in.')]
#[ORM\Entity(repositoryClass: UserRepository::class)] //Todo: why repositoryClass is UserRepository. why we created it. (AI)
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'user__email', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
  #[Groups([
    'User:V$Create',
    'User:V$Profile'
  ])]
  public Uuid $id;

  #[ORM\Column(length: 180)]
  #[Assert\Email(
    groups: ['Valid(User:Register)'],
  )]
  #[Groups([
    'User:V$Create',
    'User:W$Create',
    'User:V$Profile',
  ])]
  public string $email;

  #[ORM\Column(length: 255)]
  #[Groups([
    'User:V$Create',
    'User:W$Create',
    'User:V$Profile',
    'User:W$UpdateProfile'
  ])]
  #[Assert\NotBlank(
    groups: [
      'Valid(User:Register)',
      'Valid(User:UpdateProfile)'
    ]
  )]
  #[Assert\Length(
    min: 2,
    max: 255,
    minMessage: 'Full name must be at least {{ limit }} characters long',
    maxMessage: 'Full name cannot be longer than {{ limit }} characters'
  )]
  public string $fullName;

  #[ORM\Column(length: 20)]
  #[Groups([
    'User:V$Create',
    'User:W$Create',
    'User:V$Profile',
    'User:W$UpdateProfile'
  ])]
  #[Assert\NotBlank(
    groups: [
      'Valid(User:UpdateProfile)',
      'Valid(User:Register)'
    ]
  )]
  #[Assert\Regex(
    pattern: '/^\+44\d{10}$/',
    message: 'Phone number must be in UK format: +44 followed by 10 digits (e.g., +441234567890)'
  )]
  public string $phoneNumber;

  #[ORM\Column]
  #[Groups([
    'User:V$Create',
    'User:V$Profile'
  ])]
  public array $roles = [];

  #[ORM\Column]
  #[Assert\NotBlank(
    groups: ['Valid(User:Register)'],
  )]
  #[Groups([
    'User:W$Create',
  ])]
  public string $password;

  #[ORM\Column]
  #[Groups([
    'User:V$Create',
    'User:V$Profile'
  ])]
  public bool $isActive = false;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups([
    'User:V$Profile',
    'User:W$UpdateProfile'
  ])]
  #[Assert\NotBlank(groups: ['Valid(User:UpdateProfile)'])]
  #[Assert\Regex(
    pattern: '/^([A-Z]{1,2}\d[A-Z\d]?|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?\d[A-Z]{2}$/i',
    message: 'Please enter a valid UK postcode (e.g., SW1A 1AA, EC1A 1BB)'
  )]
  public ?string $postcode = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups([
    'User:V$Profile',
    'User:W$UpdateProfile'
  ])]
  #[Assert\NotBlank(groups: ['Valid(User:UpdateProfile)'])]
  public ?string $addressLine1  = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups([
    'User:V$Profile',
    'User:W$UpdateProfile'
  ])]
  public ?string $addressLine2 = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups([
    'User:V$Profile',
    'User:W$UpdateProfile'
  ])]
  public ?string $city = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups([
    'User:V$Profile',
    'User:W$UpdateProfile'
  ])]
  public ?string $country = null;


  #[ORM\Column]
  public string $verificationCode = '';

  #[ORM\Column]
  public int $verificationCodeExpiry = 0;

  #[ORM\Column(type: 'datetime_immutable', nullable: true)]
  #[Groups(['User:V$Profile'])]
  public ?\DateTimeImmutable $emailVerifiedAt = null;

  #[ORM\Column(type: 'datetime_immutable', nullable: true)]
  #[Groups(['User:V$Profile'])]
  public ?\DateTimeImmutable $emailVerificationSentAt = null;

  #[ORM\Column(type: 'datetime_immutable', nullable: true)]
  #[Groups(['User:V$Profile'])]
  public ?\DateTimeImmutable $passwordResetRequestedAt = null;

  #[ORM\Column(type: 'datetime_immutable', nullable: true)]
  #[Groups(['User:V$Profile'])]
  public ?\DateTimeImmutable $lastActiveAt = null;

  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  public function getRoles(): array
  {
    $roles = $this->roles;
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): static
  {
    $this->roles = $roles;

    return $this;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  public function getPostCode(): ?string
  {
    return $this->postcode;
  }

  public function setPostCode(string $postcode): static
  {
    // Always uppercase the postcode before storing
    $this->postcode = strtoupper($postcode);

    return $this;
  }

  #[\Deprecated]
  public function eraseCredentials(): void
  {
    // @deprecated, to be removed when upgrading to Symfony 8
  }
}
