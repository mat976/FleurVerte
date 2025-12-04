<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="login_form"]');
        $this->assertSelectorExists('input[name="login_form[username]"]');
        $this->assertSelectorExists('input[name="login_form[password]"]');
    }

    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        
        // Create a test user in the database or use fixtures
        $crawler = $client->request('GET', '/login');
        
        $form = $crawler->selectButton('Se connecter')->form([
            'login_form[username]' => 'testuser',
            'login_form[password]' => 'testpassword',
        ]);

        $client->submit($form);

        // Should redirect after successful login
        $this->assertTrue(
            $client->getResponse()->isRedirect() ||
            $client->getResponse()->getStatusCode() === Response::HTTP_FOUND
        );
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/login');
        
        $form = $crawler->selectButton('Se connecter')->form([
            'login_form[username]' => 'invaliduser',
            'login_form[password]' => 'wrongpassword',
        ]);

        $client->submit($form);

        // Should stay on login page with error
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.alert-danger');
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        
        // First login (simplified test)
        $client->request('GET', '/logout');
        
        // Logout should always redirect
        $this->assertTrue($client->getResponse()->isRedirect());
    }
}
