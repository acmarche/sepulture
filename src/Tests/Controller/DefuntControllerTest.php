<?php

namespace AcMarche\Sepulture\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefuntControllerTest extends WebTestCase
{
    private $client;

    public function __construct()
    {
        $this->client = static::createClient([], [
                    'PHP_AUTH_USER' => 'admin',
                    'PHP_AUTH_PW' => 'admin',
        ]);
    }

    public function testPage()
    {
        $crawler = $this->client->request('GET', '/patronyme/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test add a defunt
     * Test edit defunt
     * Click edit
     * Update.
     */
    public function testAddDefunt()
    {
        // Create a new entry in the database
        $crawler = $this->client->request('GET', '/sepulture/lmv-55');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Ajouter un défunt')->link());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouveau défunt")')->count());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Ajouter')->form([
            'defunt[nom]' => 'Sénéchal',
            'defunt[prenom]' => 'Jf',
        ]);

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Sénéchal Jf")')->count());

        $crawler = $this->client->click($crawler->selectLink('Sénéchal Jf')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Sénéchal Jf")')->count());

        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        //  print_r($client->getResponse()->getContent());

        $form = $crawler->selectButton('Mettre à jour')->form([
            'defunt[prenom]' => 'Jean-Francois',
            'defunt[lieu_naissance]' => 'Bastogne',
        ]);

        //print_r($client->getResponse()->getContent());
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Sénéchal Jean-Francois")')->count());
    }

    public function testPatronyme()
    {
        $crawler = $this->client->request('GET', '/patronyme/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Sénéchal')->link());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Sénéchal")')->count());
    }
}
