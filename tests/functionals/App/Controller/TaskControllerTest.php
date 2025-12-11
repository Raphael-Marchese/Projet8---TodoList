<?php

declare(strict_types=1);

namespace Tests\functionals\App\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BaseWebTestCase;

class TaskControllerTest extends BaseWebTestCase
{
    public function testGetTodoList(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_to_do'));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Test to do task', $crawler->filter('.task-title')->text());
    }

    public function testGetDoneList(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_done'));

        $this->assertStringContainsString('Test done task', $crawler->filter('.task-title')->text());
    }

    public function testCreateTask(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Task test title';
        $form['task[content]'] = 'Task test content';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a été bien été ajoutée.',
            $crawler->filter('div.alert.alert-success')->text()
        );

        //Check if new task has a linked user

        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Task test title']);
        $this->assertNotNull($task->author);
    }

    public function testMissingTitleFieldCreateTask(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[content]'] = 'Task test content';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    public function testMissingContentFieldCreateTask(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Task test title';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    public function testEditTask(): void
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Task test title']);
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_edit', ['id' => $task->getId()]));
        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Task test edit title';
        $form['task[content]'] = 'Task test edit content';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a bien été modifiée.',
            $crawler->filter('div.alert.alert-success')->text()
        );
    }

    public function testToggleNotDoneTask(): void
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['isDone' => false]);
        $client->request(Request::METHOD_GET, $this->generateUrl('task_toggle', ['id' => $task->getId()]));
        $crawler = $client->followRedirect();
        $this->assertStringContainsString(
            sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()),
            $crawler->filter('div.alert.alert-success')->text()
        );
    }

    public function testToggleDoneTask(): void
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['isDone' => true]);
        $client->request(Request::METHOD_GET, $this->generateUrl('task_toggle', ['id' => $task->getId()]));
        $crawler = $client->followRedirect();
        $this->assertStringContainsString(
            sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()),
            $crawler->filter('div.alert.alert-success')->text()
        );
    }

    public function testDeleteTaskByAuthorUserSuccess(): void
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Task test edit title']);
        if (null === $task) {
            $task = $taskRepository->findOneBy(['title' => 'Task test title']);
        }
        $client->request(Request::METHOD_GET, $this->generateUrl('task_delete', ['id' => $task->getId()]));
        $crawler = $client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a bien été supprimée.',
            $crawler->filter('div.alert.alert-success')->text()
        );
    }

    public function testDeleteTaskByNonConnectedUserDenied(): void
    {
        $taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Test done task']);
        $this->client->request(Request::METHOD_GET, $this->generateUrl('task_delete', ['id' => $task->getId()]));
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/login', $this->client->getResponse()->headers->get('location'));
    }

    public function testDeleteNonAuthorTaskByUserDenied(): void
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Test done task']);
        $client->request(Request::METHOD_GET, $this->generateUrl('task_delete', ['id' => $task->getId()]));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Vous n\'avez pas la permission de supprimer cette tâche. Veuillez contacter un administrateur.',
            $client->getResponse()->getContent()
        );
    }

    public function testDeleteTaskNotExisting(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request(Request::METHOD_GET, $this->generateUrl('task_delete', ['id' => 100]));
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testDeleteNonAuthorTaskByAdminSuccess(): void
    {
        $client = $this->createAuthenticatedClient('admin@test.fr');
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => 'anonyme']);
        $task = $taskRepository->findOneBy(['author' => $user]);
        $client->request(Request::METHOD_GET, $this->generateUrl('task_delete', ['id' => $task->getId()]));
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString(
            'Superbe ! La tâche a bien été supprimée.',
            $crawler->filter('div.alert.alert-success')->text()
        );
    }
}
