<?php

namespace AcMarche\Sepulture\Controller;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Sepulture\Entity\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private $propo;

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $page = $em->getRepository(Page::class)->find(1);
        if ($page === null) {
            $page = $this->createHomePage();
        }

        return $this->render(
            '@Sepulture/default/index.html.twig',
            ['page' => $page]
        );
    }

    /**
     * @Route("/plantage", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
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
        $em = $this->getDoctrine()->getManager();
        $page = new Page();
        $page->setSlug('home');
        $page->setTitre('Bienvenue sur le site des cimetières de la commune');
        $page->setContenu('Editer la page pour modifier le contenu');
        $em->persist($page);

        $em->flush();

        return $page;
    }
}
