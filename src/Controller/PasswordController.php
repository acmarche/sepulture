<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Form\User\UserPasswordType;
use AcMarche\Sepulture\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_SEPULTURE_ADMIN')]
#[Route(path: '/security/password')]
class PasswordController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordEncoder,
        private UserRepository $userRepository
    ) {
    }

    #[Route(path: '/{id}', name: 'user_change_password', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            $passwordCrypted = $this->userPasswordEncoder->hashPassword($user, $plainPassword);
            $user->setPassword($passwordCrypted);

            $this->userRepository->flush();

            $this->addFlash('success', 'Le mot de passe a bien été modifié.');

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@Sepulture/password/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
