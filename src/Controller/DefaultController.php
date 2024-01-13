<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Page;
use AcMarche\Sepulture\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository,
    ) {
    }

    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        $page = $this->pageRepository->find(1);
        if (null === $page) {
            $page = $this->createHomePage();
        }

        return $this->render(
            '@Sepulture/default/index.html.twig',
            [
                'page' => $page,
            ]
        );
    }

    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/plantage', methods: ['GET', 'POST'])]
    public function plantage(): Response
    {
        $this->propo->findAll();

        return $this->render(
            '@Sepulture/default/index.html.twig',
            []
        );
    }

    protected function createHomePage(): Page
    {
        $page = new Page();
        $page->setSlug('home');
        $page->setTitre('Bienvenue sur le site des cimetières de la commune');
        $page->setContenu('Editer la page pour modifier le contenu');
        $this->pageRepository->persist($page);
        $this->pageRepository->flush();

        return $page;
    }
}
