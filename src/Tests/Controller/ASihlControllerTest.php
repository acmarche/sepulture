<?php

namespace AcMarche\Sepulture\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ASihlControllerTest extends WebTestCase
{
    private $client;

    public function __construct()
    {
        $this->client = static::createClient([], [
                    'PHP_AUTH_USER' => 'admin',
                    'PHP_AUTH_PW' => 'admin',
        ]);
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/sihl/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/sihl/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'sihl[nom]' => 'Artistike',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Artistike")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/sihl/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Artistike')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Artistike")')->count());
        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form([
            'sihl[nom]' => 'Artistique',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Artistique")')->count());
    }
}
