<?php

namespace AcMarche\Sepulture\Controller;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            '@Sepulture/security/login.html.twig',
            ['last_username' => $lastUsername, 'error' => $error]
        );
    }


    #[Route(path: '/login_check', name: 'app_login_check')]
    public function check(): void
    {
        throw new RuntimeException(
            'You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.'
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     */
    protected function renderLogin(array $data): Response
    {
        return $this->render('@Sepulture/security/login.html.twig', $data);
    }
}
