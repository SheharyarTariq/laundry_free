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
use App\Processor\User\VerifyProcessor;
use App\Processor\User\ResendVerificationProcessor;
use App\Processor\User\ForgetPasswordProcessor;
use App\Processor\User\ResetPasswordProcessor;
use App\Processor\User\ProfileProcessor;

use App\Provider\User\ProfileProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/register',
            processor: RegisterProcessor::class,
            normalizationContext: [
                'groups' => ['User:V$Create']
            ],
            denormalizationContext: [
                'groups' => ['User:W$Create']
            ],
        ),
        new Get(
            uriTemplate: '/profile',
            provider: ProfileProvider::class,
            security: "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')",
            normalizationContext: [
                'groups' => ['User:V$Profile']
            ],
        ),
        new Put(
            uriTemplate: '/profile',
            processor: ProfileProcessor::class,
            validate: false,
            security: "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')",
            denormalizationContext: [
                'groups' => ['User:W$UpdateProfile']
            ],
        ),
        new Post(
            uriTemplate: '/verify',
            processor: VerifyProcessor::class,
            validate: false, //Todo: why validate is false
            output: false,  //Todo: why output is false. output is for Dto
            normalizationContext: [
                'groups' => ['User:V$Verify']
            ],
            denormalizationContext: [
                'groups' => ['User:W$Verify']
            ],
        ),
        new Post(
            uriTemplate: '/resend-verification',
            processor: ResendVerificationProcessor::class,
            validate: false,
            output: false,
            normalizationContext: [
                'groups' => ['User:V$Resend']
            ],
            denormalizationContext: [
                'groups' => ['User:W$Resend']
            ],
        ),
        new Post(
            uriTemplate: '/forget',
            processor: ForgetPasswordProcessor::class,
            validate: false,
            output: false,
            normalizationContext: [
                'groups' => ['User:V$Forget']
            ],
            denormalizationContext: [
                'groups' => ['User:W$Forget']
            ],
        ),
        new Post(
            uriTemplate: '/reset-password',
            processor: ResetPasswordProcessor::class,
            validate: false,
            output: false,
            normalizationContext: [
                'groups' => ['User:V$ResetPassword']
            ],
            denormalizationContext: [
                'groups' => ['User:W$ResetPassword']
            ],
        )
    ]
)]

#[UniqueEntity(fields: ['email'], message: 'This email is already registered. Please use a different email or try logging in.')]
#[ORM\Entity(repositoryClass: UserRepository::class)] //Todo: why repositoryClass is UserRepository. why we created it. (AI)
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['User:V$Create', 'User:V$Profile'])]
    public Uuid $id;

    #[ORM\Column(length: 180)]
    #[Groups(['User:V$Create', 'User:W$Create', 'User:W$Verify', 'User:W$Resend', 'User:V$Profile', 'User:W$Forget', 'User:W$ResetPassword'])]
    public string $email;

    #[ORM\Column(length: 255)]
    #[Groups(['User:V$Create', 'User:W$Create', 'User:V$Profile', 'User:W$UpdateProfile'])]
    #[Assert\NotBlank(message: 'Full name is required')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Full name must be at least {{ limit }} characters long',
        maxMessage: 'Full name cannot be longer than {{ limit }} characters'
    )]
    public string $fullName;

    #[ORM\Column(length: 20)]
    #[Groups(['User:V$Create', 'User:W$Create', 'User:V$Profile', 'User:W$UpdateProfile'])]
    #[Assert\NotBlank(message: 'Phone number is required')]
    #[Assert\Regex(
        pattern: '/^\+44\d{10}$/',
        message: 'Phone number must be in UK format: +44 followed by 10 digits (e.g., +441234567890)'
    )]
    public string $phoneNumber;

    #[ORM\Column]
    #[Groups(['User:V$Create', 'User:V$Profile'])]
    public array $roles = [];

    #[ORM\Column]
    #[Groups(['User:W$Create', 'User:W$ResetPassword'])]
    public string $password;

    // Not a database column - only used for validation during password reset
    #[Groups(['User:W$ResetPassword'])]
    public ?string $confirmPassword = null;

    #[ORM\Column]
    #[Groups(['User:V$Create', 'User:V$Verify', 'User:V$Profile'])]
    public bool $isActive = false;

    #[ORM\Column(length: 255)]
    #[Groups(['User:W$UpdateProfile', 'User:V$Profile'])]
    #[Assert\NotBlank(message: 'Postcode is required')]
    #[Assert\Regex(
        pattern: '/^([A-Z]{1,2}\d[A-Z\d]?|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?\d[A-Z]{2}$/i',
        message: 'Please enter a valid UK postcode (e.g., SW1A 1AA, EC1A 1BB)'
    )]
    public string $postcode;

    #[ORM\Column(length: 255)]
    #[Groups(['User:W$UpdateProfile', 'User:V$Profile'])]
    #[Assert\NotBlank(message: 'Address line 1 is required')]
    public string $addressLine1;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['User:W$UpdateProfile', 'User:V$Profile'])]
    public ?string $addressLine2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['User:W$UpdateProfile', 'User:V$Profile'])]
    public ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['User:W$UpdateProfile', 'User:V$Profile'])]
    public ?string $country = null;


    #[ORM\Column]
    #[Groups(['User:V$Verify', 'User:W$ResetPassword'])]
    public string $verificationCode = '';

    #[ORM\Column]
    #[Groups(['User:V$Verify'])]
    public int $verificationCodeExpiry = 0;

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

    public function getPostCode(): string
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
