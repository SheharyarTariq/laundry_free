<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates an admin user',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = 'admin@laundry.com';
        $password = 'Admin@123';

        // Check if admin already exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser) {
            $io->warning('Admin user already exists!');
            $io->info('Email: ' . $email);
            return Command::SUCCESS;
        }

        $user = new User();
        $user->email = $email;
        $user->fullName = 'Admin User';
        $user->phoneNumber = '+441234567890';
        $user->postcode = 'SW1A 1AA';
        $user->addressLine1 = 'Admin Address';
        $user->city = 'London';
        $user->country = 'United Kingdom';
        $user->isActive = true;
        $user->roles = ['ROLE_ADMIN'];

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->password = $hashedPassword;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin user created successfully!');
        $io->info('Email: ' . $email);
        $io->info('Password: ' . $password);
        $io->warning('Please change the password after first login!');

        return Command::SUCCESS;
    }
}
