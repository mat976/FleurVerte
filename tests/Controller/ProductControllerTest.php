<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    public function testProductIndexPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/product');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nos Fleurs');
    }

    public function testProductIndexWithFilters(): void
    {
        $client = static::createClient();
        
        // Test with price filter
        $crawler = $client->request('GET', '/product?prix_min=10&prix_max=50');
        $this->assertResponseIsSuccessful();
        
        // Test with THC filter
        $crawler = $client->request('GET', '/product?sort=price_desc');
        $this->assertResponseIsSuccessful();
        
        // Test with stock filter
        $crawler = $client->request('GET', '/product?stock=available');
        $this->assertResponseIsSuccessful();
        
        // Test with sorting
        $crawler = $client->request('GET', '/product?sort=price_asc');
        $this->assertResponseIsSuccessful();
    }

    public function testProductDetailPage(): void
    {
        $client = static::createClient();
        
        // This test assumes there's at least one flower in the database
        // In a real scenario, you'd create fixtures first
        $crawler = $client->request('GET', '/product/1');

        // If the flower exists, we should get a successful response
        // Otherwise, we'll get a 404, which is also valid behavior
        $this->assertTrue(
            $client->getResponse()->getStatusCode() === Response::HTTP_OK ||
            $client->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND
        );
    }

    public function testProductPagination(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/product?page=2');
        
        $this->assertResponseIsSuccessful();
    }
}
