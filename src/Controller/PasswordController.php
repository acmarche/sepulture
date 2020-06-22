<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Form\User\UserPasswordType;
use AcMarche\Sepulture\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Password controller.
 *
 * @Route("/security/password")
 * @IsGranted("ROLE_SEPULTURE_ADMIN")
 */
class PasswordController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, UserRepository $userRepository)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * Displays a form to edit an existing Abonnement entity.
     *
     * @Route("/{id}", name="user_change_password", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user)
    {
        $form = $this->createForm(UserPasswordType::class, $user)
            ->add('Update', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            $passwordCrypted = $this->userPasswordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($passwordCrypted);

            $this->userRepository->save();

            $this->addFlash('success', 'Le mot de passe a bien été modifié.');

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
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
