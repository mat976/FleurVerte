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
        try {
            $connection->executeQuery('SELECT 1');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail('Database connection failed: ' . $e->getMessage());
        }
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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
