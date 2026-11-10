<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Smoke test: the kernel boots with SepultureBundle and its config, and a page renders.
 */
class KernelBootTest extends WebTestCase
{
    public function testLoginPageRenders(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
    }
}
