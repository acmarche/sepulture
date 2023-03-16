<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Page;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        $em = $this->managerRegistry->getManager();
        $page = $em->getRepository(Page::class)->find(1);
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
        $em = $this->managerRegistry->getManager();
        $page = new Page();
        $page->setSlug('home');
        $page->setTitre('Bienvenue sur le site des cimetières de la commune');
        $page->setContenu('Editer la page pour modifier le contenu');
        $em->persist($page);

        $em->flush();

        return $page;
    }
}
