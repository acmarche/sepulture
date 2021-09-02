<?php


namespace AcMarche\Sepulture\Captcha;


use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

class Captcha
{
    const SESSION_NAME = 'sepul_comment';
    private HttpClientInterface $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    public function check(?string $value): bool
    {
        if (!$value) {
            return false;
        }
        return (bool) preg_match('#kitten#', $value);
    }

    /**
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getDog(): string
    {
        $url = "https://dog.ceo/api/breeds/image/random";
        $response = $this->client->request('GET', $url);

        $content = json_decode($response->getContent(), true);

        return $content['message'];
    }

    public function getCat(): string
    {
        $number = rand(1, 16);

        return 'https://placekitten.com/150/150?image='.$number;
    }

    /**
     * @return string[]
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getAnimals(): array
    {
        $animals = [$this->getDog(), $this->getCat()];
        shuffle($animals);

        return $animals;
    }

    public function getObjects(): void
    {
        // https://picsum.photos/seed/picsum/200/300
        // https://source.unsplash.com/random/200/300
    }

}
