<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp() : void

    {
        $this->client = static::createClient();
    }

    public function testIndex()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Bienvenue sur Todo List', $crawler->filter('h1')->text());
    }
}
