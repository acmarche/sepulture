<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Form\User\LostPasswordType;
use AcMarche\Sepulture\Form\User\ResettingFormType;
use AcMarche\Sepulture\Repository\UserRepository;
use AcMarche\Sepulture\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegisterController.
 *
 * @Route("password/lost")
 */
class ResettingController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        Mailer $mailer
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="sepulture_password_lost", methods={"GET", "POST"})
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function request(Request $request)
    {
        $form = $this->createForm(LostPasswordType::class)
            ->add('submit', SubmitType::class, ['label' => 'Demander un nouveau mot de passe']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['email' => $form->getData()->getEmail()]);
            if (!$user) {
                $this->addFlash('warning', 'Aucun utilisateur trouvé');

                return $this->redirectToRoute('sepulture_password_lost');
            }
            $token = $this->generateToken();
            $user->setConfirmationToken($token);
            $this->userRepository->save();
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

    /**
     * @Route("/confirmation", name="sepulture_password_confirmation", methods={"GET"})
     *
     * @return Response
     */
    public function requestConfirmed()
    {
        return $this->render(
            'resetting/confirmed.html.twig'
        );
    }

    /**
     * Reset user password.
     *
     * @Route("/reset/{token}", name="sepulture_password_reset", methods={"GET","POST"})
     *
     * @param string $token
     *
     * @return Response
     */
    public function reset(Request $request, $token)
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $token]);

        if (null === $user) {
            $this->addFlash('warning', 'Jeton non trouvé');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResettingFormType::class, $user)
            ->add('submit', SubmitType::class, ['label' => 'Valider']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $form->getData()->getPlainPassword()));
            $user->setConfirmationToken(null);
            $this->userRepository->save();

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
     * @throws \Exception
     */
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
