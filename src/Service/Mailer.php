<?php

namespace AcMarche\Sepulture\Service;

use AcMarche\Sepulture\Entity\Commentaire;
use AcMarche\Sepulture\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Mailer
{
    private FlashBagInterface $flashBag;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private Environment $twig,
        private RouterInterface $router,
        private MailerInterface $mailer,
        RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function send($from, $destinataires, $sujet, $body): void
    {
        $mail = (new Email())
            ->subject($sujet)
            ->from($from)
            ->to($destinataires);
        $mail->text($body);

        try {
            $this->mailer->send($mail);
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', $e->getMessage());
        }
    }

    public function sendCommentaire(Commentaire $commentaire, string $error): void
    {
        $sujet = 'Commentaire sur une sépulture';
        $from = $this->parameterBag->get('acmarche_sepulture_email');
        $to = $this->parameterBag->get('acmarche_sepulture_email');

        $body = $this->twig->render(
            '@Sepulture/commentaire/email.txt.twig',
            [
                'commentaire' => $commentaire,
                'error' => $error,
            ]
        );

        $this->send($from, $to, $sujet, $body);
    }

    public function sendRequestNewPassword(User $user): void
    {
        $from = $this->parameterBag->get('acmarche_sepulture_email');
        $url = $this->router->generate('sepulture_password_reset', [
            'token' => $user->getConfirmationToken(),
        ]);

        $body = $this->twig->render(
            '@Sepulture/security/_request_password.txt.twig',
            [
                'user' => $user,
                'url' => $url,
            ]
        );

        $sujet = "Sépulture, demande d'un nouveau mot de passe";

        $this->send($from, $user->getEmail(), $sujet, $body);
    }

    public function sendCaptchaNotWork(Commentaire $commentaire, string $error): void
    {
        $sujet = '!Commentaire échoué sur une sépulture';
        $from = $this->parameterBag->get('acmarche_sepulture_email');
        $to = 'jf@marche.be';

        $body = $this->twig->render(
            '@Sepulture/commentaire/email.txt.twig',
            [
                'commentaire' => $commentaire,
                'error' => $error,
            ]
        );

        $this->send($from, $to, $sujet, $body);
    }

    public function sendError(string $sujet, string $body): void
    {
        $from = $this->parameterBag->get('acmarche_sepulture_email');
        $to = 'jf@marche.be';

        $this->send($from, $to, $sujet, $body);
    }
}
