<?php

declare(strict_types=1);

namespace Tests\functionals\App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BaseWebTestCase;

class UserControllerTest extends BaseWebTestCase
{
    public function testGetListDenied(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('user_list'));

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testGetListByAdmin(): void
    {
        $admin = $this->createAuthenticatedClient('admin@test.fr');
        $crawler = $admin->request(Request::METHOD_GET, $this->generateUrl('user_list'));

        $this->assertEquals(Response::HTTP_OK, $admin->getResponse()->getStatusCode());
        $this->assertStringContainsString('Liste des utilisateurs', $crawler->filter('h1')->text());
    }


    public function testCreateUserDenied(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request(Request::METHOD_GET, $this->generateUrl('user_create'));

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testCreateUser(): void
    {
        $admin = $this->createAuthenticatedClient('admin@test.fr');
        $userRepository = $admin->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);

        $existingUser = $userRepository->findOneBy(['email' => 'testUser@test.fr']);
        if ($existingUser) {
            $em = $admin->getContainer()->get('doctrine.orm.entity_manager');
            $em->remove($existingUser);
            $em->flush();
        }

        $crawler = $admin->request(Request::METHOD_GET, $this->generateUrl('user_create'));

        $testUsername = 'User test username';
        $plainPassword = 'password';
        $roles = 'ROLE_USER';
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $testUsername;
        $form['user[password][first]'] = $plainPassword;
        $form['user[password][second]'] = $plainPassword;
        $form['user[email]'] = 'testUser@test.fr';
        $form['user[roles]'] = $roles;

        $admin->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $admin->getResponse()->getStatusCode());
        $crawler = $admin->followRedirect();
        $this->assertStringContainsString(
            'L\'utilisateur a bien été ajouté.',
            $crawler->filter('div.alert.alert-success')->text()
        );
        $user = $userRepository->findOneBy(['email' => 'testUser@test.fr']);
        $this->assertNotNull($user);
        $this->assertEquals($testUsername, $user->getUsername());
        $passwordHasher = $admin->getContainer()->get('security.user_password_hasher');
        $this->assertTrue($passwordHasher->isPasswordValid($user, $plainPassword));
    }


    public function testMissingUsernameFieldCreateTask(): void
    {
        $client = $this->createAuthenticatedClient('admin@test.fr');
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('user_create'));

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'testUser@test.fr';
        $form['user[roles]'] = 'ROLE_USER';

        $client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    public function testMissingEmailFieldCreateTask(): void
    {
        $client = $this->createAuthenticatedClient('admin@test.fr');
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('user_create'));

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'User test username';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[roles]'] = 'ROLE_USER';

        $client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    public function testDifferentPasswordsFieldCreateTask(): void
    {
        $client = $this->createAuthenticatedClient('admin@test.fr');
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('user_create'));

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'User test username';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password1';
        $form['user[email]'] = 'testUser2@test.fr';
        $form['user[roles]'] = 'ROLE_USER';

        $client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    public function testEditUserDenied(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request(Request::METHOD_GET, $this->generateUrl('user_edit', ['id' => 1]));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testEditUser(): void
    {
        $client = $this->createAuthenticatedClient('admin@test.fr');
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'user@test.fr']);
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('user_edit', ['id' => $user->getId()]));
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'User test edit username';
        $form['user[password][first]'] = 'password1';
        $form['user[password][second]'] = 'password1';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertStringContainsString(
            'L\'utilisateur a bien été modifié',
            $crawler->filter('div.alert.alert-success')->text()
        );
        $user = $userRepository->findOneBy(['email' => 'user@test.fr']);
        $this->assertEquals('User test edit username', $user->getUsername());
    }
}
