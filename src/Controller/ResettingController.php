<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Form\User\LostPasswordType;
use AcMarche\Sepulture\Form\User\ResettingFormType;
use AcMarche\Sepulture\Repository\UserRepository;
use AcMarche\Sepulture\Service\Mailer;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: 'password/lost')]
class ResettingController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly Mailer $mailer
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/', name: 'sepulture_password_lost', methods: ['GET', 'POST'])]
    public function request(Request $request): Response
    {
        $form = $this->createForm(LostPasswordType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Demander un nouveau mot de passe',
            ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy([
                'email' => $form->getData()->getEmail(),
            ]);
            if (! $user instanceof User) {
                $this->addFlash('warning', 'Aucun utilisateur trouvé');

                return $this->redirectToRoute('sepulture_password_lost');
            }
            $token = $this->generateToken();
            $user->setConfirmationToken($token);
            $this->userRepository->flush();
            $this->mailer->sendRequestNewPassword($user);

            return $this->redirectToRoute('sepulture_password_confirmation');
        }

        return $this->render(
            '@Sepulture/resetting/request.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/confirmation', name: 'sepulture_password_confirmation', methods: ['GET'])]
    public function requestConfirmed(): Response
    {
        return $this->render(
            '@Sepulture/resetting/confirmed.html.twig'
        );
    }

    /**
     * Reset user password.
     *
     * @param string $token
     */
    #[Route(path: '/reset/{token}', name: 'sepulture_password_reset', methods: ['GET', 'POST'])]
    public function reset(Request $request, $token): Response
    {
        $user = $this->userRepository->findOneBy([
            'confirmationToken' => $token,
        ]);
        if (! $user instanceof User) {
            $this->addFlash('warning', 'Jeton non trouvé');

            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ResettingFormType::class, $user)
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
            ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $form->getData()->getPlainPassword()));
            $user->setConfirmationToken(null);
            $this->userRepository->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été changé');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            '@Sepulture/resetting/reset.html.twig',
            [
                'token' => $token,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
