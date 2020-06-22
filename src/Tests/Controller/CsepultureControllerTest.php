<?php

namespace AcMarche\Sepulture\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CsepultureControllerTest extends WebTestCase
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
        $crawler = $this->client->request('GET', '/sepulture/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/sepulture/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'sepulture_add[parcelle]' => 'LMV-55',
            'sepulture_add[annee_releve]' => 2020,
        ]);

        $cimetiere = $crawler->filter('#sepulture_add_cimetiere option:contains("Cimetiere de Aye")');
        $this->assertEquals(1, count($cimetiere), 'Cimetiere de Aye non trouvée');
        $valueCimetiere = $cimetiere->attr('value');
        $form['sepulture_add[cimetiere]']->select($valueCimetiere);

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("LMV-55")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/sepulture/lmv-55');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form([
            'sepulture[type_autre]' => 'Tour',
            'sepulture[materiaux_autre]' => 'Or',
            'sepulture[sociale]' => 'Ministre',
        ]);

        $visuel = $crawler->filter('#sepulture_visuel option:contains("Moyen")');
        $this->assertEquals(1, count($visuel), 'Moyen non trouvé');
        $valueVisuel = $visuel->attr('value');
        $form['sepulture[visuel]']->select($valueVisuel);

        $legal = $crawler->filter('#sepulture_legal option:contains("Concession en cours")');
        $this->assertEquals(1, count($legal), 'Concession en cours non trouvée');
        $valueLegal = $legal->attr('value');
        $form['sepulture[legal]']->select($valueLegal);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Ministre")')->count());
    }
}
