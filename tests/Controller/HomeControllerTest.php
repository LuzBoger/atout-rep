<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageLoadsSuccessfully()
    {
        $client = static::createClient();

        // Effectue une requête GET sur la page d'accueil
        $client->request('GET', '/');

        // Vérifie que la réponse est bien 200 (OK)
        $this->assertResponseIsSuccessful();
    }
}
