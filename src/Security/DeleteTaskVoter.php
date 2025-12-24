<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeleteTaskVoter extends Voter
{

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::DELETE) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            $vote?->addReason('L\'utilisateur n\'est pas connecté');
            return false;
        }

        $task = $subject;

        if ($user === $task->author) {
            return true;
        }

        if ($task->author->getUsername() === 'anonyme' && $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']
            )) {
            return true;
        }

        if (!$task->author && $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']
            )) {
            return true;
        }

        $vote?->addReason('Vous n\'avez pas la permission de supprimer cette tâche. Veuillez contacter un administrateur.');

        return false;
    }
}