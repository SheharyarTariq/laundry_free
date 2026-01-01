<?php

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use Webmozart\Assert\Assert;

#[AutoconfigureTag('api_platform.state_processor')]
class ProfileProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        /** @var User $user */
        $user = $this->security->getUser();

        Assert::notNull($user, 'User not authenticated');

        // Update user profile fields only if they are provided
        if (isset($data->fullName)) {
            Assert::notEmpty($data->fullName, 'Full name cannot be empty');
            Assert::minLength($data->fullName, 2, 'Full name must be at least 2 characters long');
            Assert::maxLength($data->fullName, 255, 'Full name cannot be longer than 255 characters');
            $user->fullName = $data->fullName;
        }

        if (isset($data->phoneNumber)) {
            Assert::regex($data->phoneNumber, '/^\+44\d{10}$/', 'Phone number must be in UK format: +44 followed by 10 digits (e.g., +441234567890)');
            $user->phoneNumber = $data->phoneNumber;
        }

        if (isset($data->postcode)) {
            Assert::regex($data->postcode, '/^([A-Z]{1,2}\d[A-Z\d]?|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?\d[A-Z]{2}$/i', 'Please enter a valid UK postcode (e.g., SW1A 1AA, EC1A 1BB)');
            // The setter will automatically uppercase the postcode
            $user->setPostcode($data->postcode);
        }

        if (isset($data->addressLine1)) {
            Assert::notEmpty($data->addressLine1, 'Address line 1 cannot be empty');
            $user->addressLine1 = $data->addressLine1;
        }

        if (isset($data->addressLine2)) {
            $user->addressLine2 = $data->addressLine2;
        }

        if (isset($data->city)) {
            $user->city = $data->city;
        }

        if (isset($data->country)) {
            $user->country = $data->country;
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'fullName' => $user->fullName,
                'phoneNumber' => $user->phoneNumber,
                'postcode' => $user->getPostcode(),
                'addressLine1' => $user->addressLine1,
                'addressLine2' => $user->addressLine2,
                'city' => $user->city,
                'country' => $user->country,
                'isActive' => $user->isActive,
            ]
        ]);
    }
}
