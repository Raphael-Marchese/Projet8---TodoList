<?php

declare(strict_types=1);

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User;
        $user->setEmail('user@test.fr');
        $user->setUsername('user');
        $password = $this->passwordEncoder->encodePassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);

        $testUser = new User;
        $testUser->setEmail('test@test.fr');
        $testUser->setUsername('test');
        $test_password = $this->passwordEncoder->encodePassword($testUser, 'test');
        $testUser->setPassword($test_password);
        $manager->persist($testUser);

        $task = new Task;
        $task->setTitle('Test task');
        $task->setContent('Test content');
        $manager->persist($task);

        $manager->flush();
    }
}
