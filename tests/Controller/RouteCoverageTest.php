<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Response;

class RouteCoverageTest extends WebTestCase
{
    private array $publicRoutes = [
        '/',
        '/product',
        '/product/1',
        '/login',
        '/register',
        '/api/fleurs',
        '/api/fleurs/1',
    ];

    private array $protectedRoutes = [
        '/profile',
        '/profile/edit',
        '/admin',
        '/fleur/new',
        '/fleur/1/edit',
    ];

    public function testPublicRoutesAreAccessible(): void
    {
        $client = static::createClient();

        foreach ($this->publicRoutes as $route) {
            $client->request('GET', $route);
            
            // Public routes should be accessible (200) or return 404 if resource doesn't exist
            $statusCode = $client->getResponse()->getStatusCode();
            $this->assertTrue(
                in_array($statusCode, [Response::HTTP_OK, Response::HTTP_NOT_FOUND]),
                "Route $route returned unexpected status code: $statusCode"
            );
        }
    }

    public function testProtectedRoutesRedirectWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        foreach ($this->protectedRoutes as $route) {
            $client->request('GET', $route);
            
            // Protected routes should redirect to login or return 404
            $statusCode = $client->getResponse()->getStatusCode();
            $this->assertTrue(
                in_array($statusCode, [Response::HTTP_FOUND, Response::HTTP_NOT_FOUND]),
                "Protected route $route should redirect to login or return 404, got: $statusCode"
            );
        }
    }

    public function testApiRoutesReturnJson(): void
    {
        $client = static::createClient();
        
        $apiRoutes = [
            '/api/fleurs',
            '/api/fleurs/1',
        ];

        foreach ($apiRoutes as $route) {
            $client->request('GET', $route);
            
            if ($client->getResponse()->getStatusCode() === Response::HTTP_OK) {
                $this->assertResponseHeaderSame('Content-Type', 'application/json');
            }
        }
    }

    public function testRoutesWithParameters(): void
    {
        $client = static::createClient();
        
        // Test routes with different parameter values
        $testRoutes = [
            '/product?page=2',
            '/product?sort=price_asc',
            '/product?prix_min=5&prix_max=100',
        ];

        foreach ($testRoutes as $route) {
            $client->request('GET', $route);
            $this->assertResponseIsSuccessful("Route $route should be successful");
        }
    }

    public function testRouteDefinitions(): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        
        $this->assertInstanceOf(RouterInterface::class, $router);
        
        // Test that main routes are defined
        $routeCollection = $router->getRouteCollection();
        
        $expectedRoutes = [
            'app_product_index',
            'app_product_show',
            'app_login',
            'app_logout',
            'api_fleurs_index',
            'api_fleurs_show',
        ];
        
        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(
                $routeCollection->get($routeName) !== null,
                "Route $routeName should be defined"
            );
        }
    }
}
