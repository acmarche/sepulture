<?php

namespace AcMarche\Sepulture\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ATypeSepultureControllerTest extends WebTestCase
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
        $crawler = $this->client->request('GET', '/typesepulture/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/typesepulture/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'type_sepulture[nom]' => 'Croi',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Croi")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/typesepulture/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Croi')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Croi")')->count());
        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form([
            'type_sepulture[nom]' => 'Croix',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Croix")')->count());
    }
}
