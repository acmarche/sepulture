<?php

namespace AcMarche\Sepulture\Service;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\ContactRw;
use AcMarche\Sepulture\Entity\Preference;
use AcMarche\Sepulture\Repository\ContactRwRepository;
use AcMarche\Sepulture\Repository\PreferenceRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Util.
 */
class CimetiereUtil
{
    public string $error;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private PreferenceRepository $preferenceRepository,
        private HttpClientInterface $httpClient,
        private ContactRwRepository $contactRwRepository
    ) {
        $this->error = '';
    }

    public static function getStatuts(): array
    {
        $statut_tmp = ['Terminé', 'A relire'];
        $statuts = [];
        foreach ($statut_tmp as $statut) {
            $statuts[$statut] = $statut;
        }

        return $statuts;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function captchaverify(string $token = null): bool
    {
        if (! $token) {
            return false;
        }

        $secret = $this->parameterBag->get('acmarche_sepulture_captcha_secret_key');

        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $response = $this->httpClient->request(
            'POST',
            $url,
            [
                'body' => [
                    'secret' => $secret,
                    'response' => $token,
                ],
            ]
        );

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $success = (bool) $data['success'];

        if (! $success) {
            foreach ($data['error-codes'] as $error) {
                $this->error = $error;
            }
        }

        return $success;
    }

    public function getCimetiereByDefault($username): ?string
    {
        $preference = $this->preferenceRepository->findOneBy(
            [
                'clef' => 'cimetiere',
                'username' => $username,
            ]
        );

        if (null !== $preference) {
            return $preference->getValeur();
        }

        return null;
    }

    public function setCimetiereByDefault($username, Cimetiere $cimetiere): void
    {
        $preference = $this->preferenceRepository->findOneBy(
            [
                'clef' => 'cimetiere',
                'username' => $username,
            ]
        );

        if (null === $preference) {
            $preference = new Preference();
            $preference->setClef('cimetiere');
            $preference->setNom('Cimetiere par defaut pour encodage');
            $preference->setUsername($username);
            $this->preferenceRepository->persist($preference);
        }

        $preference->setValeur($cimetiere->getId());

        $this->preferenceRepository->flush();
    }

    public function getContactRw(): ?ContactRw
    {
        return $this->contactRwRepository->findOne();
    }
}
