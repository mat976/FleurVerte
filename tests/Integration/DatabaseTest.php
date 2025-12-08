<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Fleur;
use App\Entity\Fleuriste;
use App\Entity\User;

class DatabaseTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testDatabaseConnection(): void
    {
        $connection = $this->entityManager->getConnection();
        $this->assertTrue($connection->isConnected(), 'Database should be connected');
    }

    public function testCreateAndRetrieveFleur(): void
    {
        $fleur = new Fleur();
        $fleur->setNom('Test Fleur');
        $fleur->setPrix(25.99);
        $fleur->setStock(10);

        $this->entityManager->persist($fleur);
        $this->entityManager->flush();

        $retrievedFleur = $this->entityManager->getRepository(Fleur::class)->find($fleur->getId());
        
        $this->assertNotNull($retrievedFleur);
        $this->assertEquals('Test Fleur', $retrievedFleur->getNom());
        $this->assertEquals(25.99, $retrievedFleur->getPrix());

        // Clean up
        $this->entityManager->remove($retrievedFleur);
        $this->entityManager->flush();
    }

    public function testFleurWithFleuristeRelationship(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('testuser');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        $fleuriste = new Fleuriste();
        $fleuriste->setNom('Test Fleuriste');
        $fleuriste->setActif(true);
        $fleuriste->setUser($user);

        $fleur = new Fleur();
        $fleur->setNom('Test Fleur');
        $fleur->setPrix(30.0);
        $fleur->setFleuriste($fleuriste);

        $this->entityManager->persist($user);
        $this->entityManager->persist($fleuriste);
        $this->entityManager->persist($fleur);
        $this->entityManager->flush();

        $retrievedFleur = $this->entityManager->getRepository(Fleur::class)->find($fleur->getId());
        $this->assertNotNull($retrievedFleur->getFleuriste());
        $this->assertEquals('Test Fleuriste', $retrievedFleur->getFleuriste()->getNom());

        // Clean up
        $this->entityManager->remove($retrievedFleur);
        $this->entityManager->remove($retrievedFleur->getFleuriste());
        $this->entityManager->remove($this->entityManager->find(User::class, $user->getId()));
        $this->entityManager->flush();
    }

    public function testQueryBuilderQueries(): void
    {
        // Test the queries used in ProductController
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('f')
            ->from(Fleur::class, 'f')
            ->orderBy('f.nom', 'ASC');

        $fleurs = $queryBuilder->getQuery()->getResult();
        $this->assertIsArray($fleurs);

        // Test with is_pinned filter
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('f')
            ->from(Fleur::class, 'f')
            ->where('f.isPinned = :pinned')
            ->setParameter('pinned', true)
            ->orderBy('f.nom', 'ASC');

        $pinnedFleurs = $queryBuilder->getQuery()->getResult();
        $this->assertIsArray($pinnedFleurs);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
