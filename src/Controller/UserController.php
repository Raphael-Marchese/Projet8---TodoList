<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    private $hasher;

    private $doctrine;

    public function __construct(UserPasswordHasherInterface $hasher, ManagerRegistry $doctrine)
    {
        $this->hasher = $hasher;
        $this->doctrine = $doctrine;
    }

    #[Route('/users', name: 'user_list')]
    public function listAction(): Response
    {
        return $this->render('user/list.html.twig', ['users' => $this->doctrine->getRepository(User::class)->findAll()]
        );
    }

    #[Route('/users/create', name: 'user_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();

            $password = $form->get('password')->getData();

            if ($password) {
                $user->setPassword($this->hasher->hashPassword($user, $password));
            }

            $role = $form->get('roles')->getData();
            $user->setRoles([$role]);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('user/create.html.twig', [
                'form' => $form->createView(),
            ], new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editAction(User $user, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);

        $currentRole = $user->getRoles()[0] ?? 'ROLE_USER';
        $form->get('roles')->setData($currentRole);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();

            if ($password) {
                $user->setPassword($this->hasher->hashPassword($user, $password));
            }

            $role = $form->get('roles')->getData();
            $user->setRoles([$role]);

            $this->doctrine->getManager()->persist($user);

            $this->doctrine->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
