<?php

namespace AcMarche\Sepulture\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ALegalControllerTest extends WebTestCase
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
        $crawler = $this->client->request('GET', '/legal/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/legal/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'legal[nom]' => 'Contrefacan',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Contrefacan")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/legal/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Contrefacan')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Contrefacan")')->count());
        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form([
            'legal[nom]' => 'Contrefacon',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Contrefacon")')->count());
    }
}
