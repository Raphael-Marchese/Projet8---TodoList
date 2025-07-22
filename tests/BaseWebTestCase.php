<?php

declare(strict_types=1);

namespace Tests;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class BaseWebTestCase extends WebTestCase
{
    protected $client = null;

    public function setUp(): void

    {
        $this->client = static::createClient();
    }

    protected function createAuthenticatedClient(string $email = 'user@test.fr'): Client
    {
        $container = $this->client->getContainer();

        /** @var SessionInterface $session */
        $session = $container->get('session');

        /** @var User $user */
        $user = $container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception("Utilisateur avec l'email {$email} introuvable.");
        }

        $firewallName = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        return $this->client;
    }

    protected function generateUrl(string $route, array $params = []): string
    {
        return $this->client->getContainer()->get('router')->generate($route, $params);
    }
}
