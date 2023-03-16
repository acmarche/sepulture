<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Form\User\UserType;
use AcMarche\Sepulture\Form\User\UtilisateurEditType;
use AcMarche\Sepulture\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_SEPULTURE_ADMIN')]
#[Route(path: '/user')]
class UserController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordEncoder,
        private ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'user_index', methods: 'GET')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('@Sepulture/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route(path: '/new', name: 'user_new', methods: 'GET|POST')]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $user->setPassword($this->userPasswordEncoder->hashPassword($user, $user->getPlainPassword()));
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            '@Sepulture/user/new.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'user_show', methods: 'GET')]
    public function show(User $user): Response
    {
        return $this->render('@Sepulture/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'user_edit', methods: 'GET|POST')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UtilisateurEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();

            return $this->redirectToRoute('user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@Sepulture/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->managerRegistry->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
