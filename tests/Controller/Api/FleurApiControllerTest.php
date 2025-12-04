<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FleurApiControllerTest extends WebTestCase
{
    public function testGetAllFleurs(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/fleurs');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }

    public function testGetFleurById(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/fleurs/1');

        // Test both success case (if flower exists) and 404 case
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            $statusCode === Response::HTTP_OK || 
            $statusCode === Response::HTTP_NOT_FOUND
        );
        
        if ($statusCode === Response::HTTP_OK) {
            $this->assertResponseHeaderSame('Content-Type', 'application/json');
            $responseData = json_decode($client->getResponse()->getContent(), true);
            
            $this->assertArrayHasKey('id', $responseData);
            $this->assertArrayHasKey('nom', $responseData);
            $this->assertArrayHasKey('prix', $responseData);
        }
    }

        public function testGetFleurByIdNotFound(): void
        {
            $client = static::createClient();
            $client->request('GET', '/api/fleurs/99999');

            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }

        public function testApiFleursStructure(): void
        {
            $client = static::createClient();
            $client->request('GET', '/api/fleurs');

            $this->assertResponseIsSuccessful();
            $responseData = json_decode($client->getResponse()->getContent(), true);

            if (!empty($responseData)) {
                $fleur = $responseData[0];
                $expectedKeys = ['id', 'nom', 'prix', 'stock_status', 'image_url'];
                
                foreach ($expectedKeys as $key) {
                    $this->assertArrayHasKey($key, $fleur, "Missing key: $key");
                }
            }
        }
    }
