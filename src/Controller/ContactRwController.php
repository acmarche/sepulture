<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\ContactRw;
use AcMarche\Sepulture\Form\ContactRwType;
use AcMarche\Sepulture\Repository\ContactRwRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/contact/rw')]
class ContactRwController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'contact_rw_index', methods: ['GET'])]
    public function index(ContactRwRepository $contactRwRepository): Response
    {
        return $this->render(
            '@Sepulture/contact_rw/index.html.twig',
            [
                'contacts' => $contactRwRepository->findAll(),
            ]
        );
    }

    #[Route(path: '/new', name: 'contact_rw_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $contactRw = new ContactRw();
        $contactRw->setDateExpiration(new DateTime());
        $contactRw->setDateRapport(new DateTime());
        $form = $this->createForm(ContactRwType::class, $contactRw);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($contactRw);
            $entityManager->flush();
            $this->addFlash('success', 'Le contact a bien été ajouté');

            return $this->redirectToRoute('contact_rw_show', [
                'id' => $contactRw->getId(),
            ]);
        }

        return $this->render(
            '@Sepulture/contact_rw/new.html.twig',
            [
                'contact' => $contactRw,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'contact_rw_show', methods: ['GET'])]
    public function show(ContactRw $contactRw): Response
    {
        return $this->render(
            '@Sepulture/contact_rw/show.html.twig',
            [
                'contact' => $contactRw,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'contact_rw_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContactRw $contactRw): Response
    {
        $form = $this->createForm(ContactRwType::class, $contactRw);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();

            $this->addFlash('success', 'Le contact a bien été modifié');

            return $this->redirectToRoute('contact_rw_show', [
                'id' => $contactRw->getId(),
            ]);
        }

        return $this->render(
            '@Sepulture/contact_rw/edit.html.twig',
            [
                'contact' => $contactRw,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'contact_rw_delete', methods: ['POST'])]
    public function delete(Request $request, ContactRw $contactRw): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$contactRw->getId(), $request->request->get('_token'))) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($contactRw);
            $entityManager->flush();
            $this->addFlash('success', 'Le contact a bien été supprimé');
        }

        return $this->redirectToRoute('contact_rw_index');
    }
}
