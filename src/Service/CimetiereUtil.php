<?php

namespace AcMarche\Sepulture\Service;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\ContactRw;
use AcMarche\Sepulture\Entity\Preference;
use AcMarche\Sepulture\Repository\ContactRwRepository;
use AcMarche\Sepulture\Repository\PreferenceRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Util.
 */
class CimetiereUtil
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var PreferenceRepository
     */
    private $preferenceRepository;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var string
     */
    public $error;
    /**
     * @var ContactRwRepository
     */
    private $contactRwRepository;

    public function __construct(
        ParameterBagInterface $parameterBag,
        PreferenceRepository $preferenceRepository,
        HttpClientInterface $httpClient,
        ContactRwRepository $contactRwRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->preferenceRepository = $preferenceRepository;
        $this->httpClient = $httpClient;
        $this->error = '';
        $this->contactRwRepository = $contactRwRepository;
    }

    public static function getStatuts()
    {
        $statut_tmp = ['Terminé', 'A relire'];
        $statuts = [];
        foreach ($statut_tmp as $statut) {
            $statuts[$statut] = $statut;
        }

        return $statuts;
    }

    /**
     * @param string $token
     *
     * @return bool
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function captchaverify(string $token = null): bool
    {
        if (!$token) {
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

        $data = json_decode($response->getContent(), true);
        $success = (bool)$data['success'];

        if (false === $success) {
            foreach ($data['error-codes'] as $error) {
                $this->error = $error;
            }
        }

        return $success;
    }

    public function getCimetiereByDefault($username)
    {
        $preference = $this->preferenceRepository->findOneBy(
            [
                'clef' => 'cimetiere',
                'username' => $username,
            ]
        );

        if ($preference) {
            return $preference->getValeur();
        }

        return null;
    }

    public function setCimetiereByDefault($username, Cimetiere $cimetiere)
    {
        $preference = $this->preferenceRepository->findOneBy(
            [
                'clef' => 'cimetiere',
                'username' => $username,
            ]
        );

        if (!$preference) {
            $preference = new Preference();
            $preference->setClef('cimetiere');
            $preference->setNom('Cimetiere par defaut pour encodage');
            $preference->setUsername($username);
            $this->preferenceRepository->persist($preference);
        }

        $preference->setValeur($cimetiere->getId());

        $this->preferenceRepository->save();
    }

    public function getContactRw(): ?ContactRw
    {
        return $this->contactRwRepository->findOne();
    }
}
