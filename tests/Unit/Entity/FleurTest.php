<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Fleur;
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

    public function testImageUrlFallsBackToDefaultWhenNoUpload(): void
    {
        $fleur = new Fleur();

        // Without an uploaded file, a deterministic default flower image is used.
        $this->assertStringContainsString('/img/flowerimg/flower', $fleur->getImageUrl());
    }

    public function testImageUrlPointsToUploadWhenFileExists(): void
    {
        $fleur = new Fleur();

        // An image that actually exists in public/uploads/fleurs is served from there.
        $existingUpload = 'twitter-profile-image-67494962a7c18600583970.png';
        $fleur->setImageName($existingUpload);

        $this->assertSame('/uploads/fleurs/' . $existingUpload, $fleur->getImageUrl());
    }
}
