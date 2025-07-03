<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void

    {
        $this->client = static::createClient();
    }

    public function testGetList()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/tasks');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer une tâche', $crawler->filter('.create-task-btn')->text());
    }


}