<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User;
        $user->setEmail('user@test.fr');
        $user->setUsername('user');
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);

        $testUser = new User;
        $testUser->setEmail('test@test.fr');
        $testUser->setUsername('test');
        $test_password = $this->hasher->hashPassword($testUser, 'test');
        $testUser->setPassword($test_password);
        $manager->persist($testUser);

        $admin = new User;
        $admin->setEmail('admin@test.fr');
        $admin->setUsername('admin');
        $admin_password = $this->hasher->hashPassword($admin, 'admin');
        $admin->setPassword($admin_password);
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $manager->flush();

        $task = new Task;
        $task->setTitle('Test task');
        $task->setContent('Test content');
        $manager->persist($task);

        $manager->flush();
    }
}
