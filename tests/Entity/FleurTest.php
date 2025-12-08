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
        $this->assertNull($fleur->getPrix());
    }

    public function testFleurSettersAndGetters(): void
    {
        $fleur = new Fleur();
        
        $fleur->setNom('Test Fleur');
        $fleur->setPrix(25.99);
        
        $this->assertEquals('Test Fleur', $fleur->getNom());
        $this->assertEquals(25.99, $fleur->getPrix());
    }

    public function testFleurStockStatus(): void
    {
        $fleur = new Fleur();
        
        // Test low stock (<= 10)
        $fleur->setStock(5);
        $this->assertEquals('low_stock', $fleur->getStockStatus());
        
        // Test medium stock (<= 50)
        $fleur->setStock(20);
        $this->assertEquals('medium_stock', $fleur->getStockStatus());

        // Test in stock (> 50)
        $fleur->setStock(60);
        $this->assertEquals('in_stock', $fleur->getStockStatus());
        
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
        $this->assertStringContainsString('flower', $fleur->getImageUrl());
        
        // Test with custom image
        $fleur->setImageName('custom-image.jpg');
        $this->assertStringContainsString('uploads/fleurs/custom-image.jpg', $fleur->getImageUrl());
    }
}
