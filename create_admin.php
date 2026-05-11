<?php
// Script pour créer un compte admin
require_once __DIR__ . '/vendor/autoload.php';

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

/** @var EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
/** @var UserPasswordHasherInterface $passwordHasher */
$passwordHasher = $container->get('security.password_hasher');

// Vérifie si l'admin existe déjà
$existing = $em->getRepository(User::class)->findOneBy(['email' => 'admin@fleurverte.fr']);
if ($existing) {
    echo "Admin existe déjà !\n";
    echo "Email: admin@fleurverte.fr\n";
    echo "Username: " . $existing->getUsername() . "\n";
    exit(0);
}

$user = new User();
$user->setUsername('admin');
$user->setEmail('admin@fleurverte.fr');
$user->setNom('Admin');
$user->setPrenom('FleurVerte');
$user->setTelephone('0123456789');
$user->setDateNaissance(new \DateTime('1990-01-01'));
$user->setRoles(['ROLE_ADMIN']);

$hashedPassword = $passwordHasher->hashPassword($user, 'admin123');
$user->setPassword($hashedPassword);

$em->persist($user);
$em->flush();

echo "✅ Compte admin créé avec succès !\n";
echo "Email: admin@fleurverte.fr\n";
echo "Mot de passe: admin123\n";
echo "Rôle: ROLE_ADMIN\n";
