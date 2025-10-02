<?php

declare(strict_types=1);

namespace Tests\functionals\App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BaseWebTestCase;

class UserControllerTest extends BaseWebTestCase
{
    public function testGetList()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('user_list'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Liste des utilisateurs', $crawler->filter('h1')->text());
    }

    public function testCreateUser()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);

        $existingUser = $userRepository->findOneBy(['email' => 'testUser@test.fr']);
        if ($existingUser) {
            $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
            $em->remove($existingUser);
            $em->flush();
        }

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('user_create'));
        $testUsername = 'User test username';
        $plainPassword = 'password';
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $testUsername;
        $form['user[password][first]'] = $plainPassword;
        $form['user[password][second]'] = $plainPassword;
        $form['user[email]'] = 'testUser@test.fr';

        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('L\'utilisateur a bien été ajouté.', $crawler->filter('div.alert.alert-success')->text());
        $user = $userRepository->findOneBy(['email' => 'testUser@test.fr']);
        $this->assertNotNull($user);
        $this->assertEquals($testUsername, $user->getUsername());
        $passwordHasher = $this->client->getContainer()->get('security.user_password_hasher');
        $this->assertTrue($passwordHasher->isPasswordValid($user, $plainPassword));
    }


    public function testMissingUsernameFieldCreateTask()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('user_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'testUser@test.fr';
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testMissingEmailFieldCreateTask()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('user_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'User test username';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testDifferentPasswordsFieldCreateTask()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('user_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'User test username';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password1';
        $form['user[email]'] = 'testUser2@test.fr';
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUser()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'user@test.fr']);
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('user_edit', ['id' => $user->getId()]));
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'User test edit username';
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('L\'utilisateur a bien été modifié', $crawler->filter('div.alert.alert-success')->text());
        $user = $userRepository->findOneBy(['email' => 'user@test.fr']);
        $this->assertEquals('User test edit username', $user->getUsername());
    }
}
