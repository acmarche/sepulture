<?php

namespace AcMarche\Sepulture\Tests\Editeur;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function __construct()
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'editeur',
            'PHP_AUTH_PW' => 'editeur',
        ]);
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertTrue($crawler->filter('h3:contains("Les sépultures")')->count() > 0);
    }

    public function testAddLegal()
    {
        $crawler = $this->client->request('GET', '/legal/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAddVisuel()
    {
        $crawler = $this->client->request('GET', '/visuel/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAddMateriel()
    {
        $crawler = $this->client->request('GET', '/materiaux/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAddSihl()
    {
        $crawler = $this->client->request('GET', '/sihl/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAddTypeSepulture()
    {
        $crawler = $this->client->request('GET', '/typesepulture/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAddSepulture()
    {
        $crawler = $this->client->request('GET', '/sepulture/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'sepulture_add[parcelle]' => 'STF-23',
            'sepulture_add[annee_releve]' => 2020,
        ]);

        $cimetiere = $crawler->filter('#sepulture_add_cimetiere option:contains("Cimetiere de Aye")');
        $this->assertEquals(1, count($cimetiere), 'Cimetiere de Aye non trouvée');
        $valueCimetiere = $cimetiere->attr('value');
        $form['sepulture_add[cimetiere]']->select($valueCimetiere);

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("STF-23")')->count());
    }

    public function testEditSepulture()
    {
        $crawler = $this->client->request('GET', '/sepulture/stf-23');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form([
            'sepulture[type_autre]' => 'Tour',
            'sepulture[materiaux_autre]' => 'Or',
            'sepulture[sociale]' => 'Cordonnier',
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

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Cordonnier")')->count());
    }
}
