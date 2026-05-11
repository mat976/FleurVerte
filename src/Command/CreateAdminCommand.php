<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Crée un compte administrateur')]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => 'admin@fleurverte.fr']);
        if ($existing) {
            $output->writeln('Admin existe déjà !');
            $output->writeln('Email: admin@fleurverte.fr');
            return Command::SUCCESS;
        }

        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@fleurverte.fr');
        $user->setNom('Admin');
        $user->setPrenom('System');
        $user->setTelephone('0000000000');
        $user->setDateNaissance(new \DateTime('1990-01-01'));
        $user->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin123');
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('✅ Compte admin créé avec succès !');
        $output->writeln('Email: admin@fleurverte.fr');
        $output->writeln('Mot de passe: admin123');

        return Command::SUCCESS;
    }
}
