<?php

namespace App\Tests\Entity;

use App\Entity\Fleur;
use App\Entity\Fleuriste;
use PHPUnit\Framework\TestCase;

class FleurTest extends TestCase
{
    public function testFleurCreation(): void
    {
        $fleur = new Fleur();
        
        $this->assertNull($fleur->getId());
        $this->assertNull($fleur->getNom());
        $this->assertNull($fleur->getDescription());
        $this->assertNull($fleur->getThc());
        $this->assertNull($fleur->getPrix());
        $this->assertNull($fleur->getStock());
        $this->assertFalse($fleur->isPinned());
        $this->assertNull($fleur->getFleuriste());
    }

    public function testFleurSettersAndGetters(): void
    {
        $fleur = new Fleur();
        $fleuriste = new Fleuriste();
        
        $fleur->setNom('Test Fleur');
        $fleur->setDescription('Test Description');
        $fleur->setPrix(25.99);
        $fleur->setStock(10);
        $fleur->setPinned(true);
        $fleur->setFleuriste($fleuriste);
        
        $this->assertEquals('Test Fleur', $fleur->getNom());
        $this->assertEquals('Test Description', $fleur->getDescription());
        $this->assertEquals(25.99, $fleur->getPrix());
        $this->assertEquals(10, $fleur->getStock());
        $this->assertTrue($fleur->isPinned());
        $this->assertEquals($fleuriste, $fleur->getFleuriste());
    }

    public function testFleurStockStatus(): void
    {
        $fleur = new Fleur();
        
        // Test in stock
        $fleur->setStock(5);
        $this->assertEquals('available', $fleur->getStockStatus());
        
        // Test out of stock
        $fleur->setStock(0);
        $this->assertEquals('out_of_stock', $fleur->getStockStatus());
        
        // Test null stock
        $fleur->setStock(null);
        $this->assertEquals('unknown', $fleur->getStockStatus());
    }

    public function testFleurImageUrl(): void
    {
        $fleur = new Fleur();
        
        // Test default image URL
        $this->assertStringContains('images/fleurs/default.jpg', $fleur->getImageUrl());
        
        // Test with custom image
        $fleur->setImageName('custom-image.jpg');
        $this->assertStringContains('images/fleurs/custom-image.jpg', $fleur->getImageUrl());
    }
}
