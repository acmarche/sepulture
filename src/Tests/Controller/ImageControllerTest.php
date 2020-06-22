<?php

namespace AcMarche\Sepulture\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ImageControllerTest extends WebTestCase
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
        $crawler = $this->client->request('GET', '/');
    }
}
