<?php

namespace AcMarche\Sepulture\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CimetiereControllerTest extends WebTestCase
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
        $crawler = $this->client->request('GET', '/cimetiere/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/cimetiere/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'cimetiere[nom]' => 'Cimetiere de aie',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Cimetiere de aie")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/cimetiere/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Cimetiere de aie')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Cimetiere de aie")')->count());
        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form([
            'cimetiere[nom]' => 'Cimetiere de Aye',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Cimetiere de Aye")')->count());
    }
}
