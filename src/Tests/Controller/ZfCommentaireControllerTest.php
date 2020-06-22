<?php

namespace AcMarche\Sepulture\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentaireControllerTest extends WebTestCase
{
    private $client;

    public function __construct()
    {
        $this->client = static::createClient([], []);
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/sepulture/lmv-55');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Ajouter')->form([
            'commentaire[nom]' => 'Sénéchal',
            'commentaire[email]' => 'jf@marche.be',
            'commentaire[remarques]' => 'Mon commentaire',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Le commentaire a bien été ajouté")')->count());
    }

    public function testIndex()
    {
        $this->client = static::createClient([], [
                    'PHP_AUTH_USER' => 'admin',
                    'PHP_AUTH_PW' => 'admin',
        ]);

        $crawler = $this->client->request('GET', '/commentaire/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
