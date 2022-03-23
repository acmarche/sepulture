<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Ossuaire;
use AcMarche\Sepulture\Form\OssuaireAddSepultureType;
use AcMarche\Sepulture\Form\OssuaireType;
use AcMarche\Sepulture\Form\SearchSimpleSepultureType;
use AcMarche\Sepulture\Repository\OssuaireRepository;
use AcMarche\Sepulture\Repository\SepultureRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/ossuaire')]
class OssuaireController extends AbstractController
{
    public function __construct(
        private OssuaireRepository $ossuaireRepository,
        private SepultureRepository $sepultureRepository,
    ) {
    }

    /**
     * Lists all Ossuaire entities.
     */
    #[Route(path: '/', name: 'ossuaire', methods: ['GET'])]
    public function index(): Response
    {
        $ossuaires = $this->ossuaireRepository->findAllOrdered();

        return $this->render(
            '@Sepulture/ossuaire/index.html.twig',
            [
                'ossuaires' => $ossuaires,
            ]
        );
    }

    /**
     * Displays a form to create a new Ossuaire entity.
     */
    #[IsGranted(data: 'ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/new', name: 'ossuaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $ossuaire = new Ossuaire();
        $form = $this->createForm(OssuaireType::class, $ossuaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->ossuaireRepository->persist($ossuaire);
            $this->ossuaireRepository->flush();

            $this->addFlash('success', 'L\'ossuaire a bien été ajouté');

            return $this->redirectToRoute('ossuaire_show', [
                'id' => $ossuaire->getId(),
            ]);
        }

        return $this->render(
            '@Sepulture/ossuaire/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'ossuaire_show', methods: ['GET'])]
    public function show(Ossuaire $ossuaire): Response
    {
        $sepultures = $this->sepultureRepository->findSepulturesByOssuraire($ossuaire);
        $deleteForm = $this->createDeleteForm($ossuaire->getId());

        return $this->render(
            '@Sepulture/ossuaire/show.html.twig',
            [
                'sepultures' => $sepultures,
                'ossuaire' => $ossuaire,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Ossuaire entity.
     */
    #[IsGranted(data: 'ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'ossuaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ossuaire $ossuaire): Response
    {
        $editForm = $this->createForm(OssuaireType::class, $ossuaire);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->ossuaireRepository->flush();

            $this->addFlash('success', 'L\'ossuaire a bien été modifié');

            return $this->redirectToRoute('ossuaire_show', [
                'id' => $ossuaire->getId(),
            ]);
        }

        return $this->render(
            '@Sepulture/ossuaire/edit.html.twig',
            [
                'ossuaire' => $ossuaire,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Ossuaire entity.
     */
    #[IsGranted(data: 'ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}', name: 'ossuaire_delete', methods: ['POST'])]
    public function delete(Request $request, Ossuaire $ossuaire): RedirectResponse
    {
        $form = $this->createDeleteForm($ossuaire->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($ossuaire->getDocument()) {
                $this->ossuaireRepository->remove($ossuaire->getDocument());
            }
            $this->ossuaireRepository->remove($ossuaire);
            $this->ossuaireRepository->flush();

            $this->addFlash('success', 'L\'ossuaire a bien été supprimé');
        }

        return $this->redirectToRoute('ossuaire');
    }

    #[IsGranted(data: 'ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/add/sepulture', name: 'ossuaire_add_sepulture', methods: ['GET', 'POST'])]
    public function addSepultures(Request $request, Ossuaire $ossuaire): Response
    {
        $editForm = $this->createForm(OssuaireAddSepultureType::class, null);
        $searchForm = $this->createForm(SearchSimpleSepultureType::class, []);
        $sepultures = [];

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $args = $searchForm->getData();
            $sepultures = $this->sepultureRepository->search($args);
        }

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $sepulturesToAdd = $request->get('sepultures', []);
            foreach ($sepulturesToAdd as $sepultureId) {
                $sepultureToAdd = $this->sepultureRepository->find($sepultureId);
                if ($sepultureToAdd) {
                    $ossuaire->addSepulture($sepultureToAdd);
                }
            }
            $this->ossuaireRepository->flush();

            $this->addFlash('success', 'Les sépultures ont bien été ajoutées');

            return $this->redirectToRoute('ossuaire_show', [
                'id' => $ossuaire->getId(),
            ]);
        }

        return $this->render(
            '@Sepulture/ossuaire/add_sepulture.html.twig',
            [
                'ossuaire' => $ossuaire,
                'form' => $editForm->createView(),
                'search_form' => $searchForm->createView(),
                'search' => $searchForm->isSubmitted(),
                'sepultures' => $sepultures,
            ]
        );
    }


    /**
     * Creates a form to delete a Ossuaire entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('ossuaire_delete', [
                    'id' => $id,
                ])
            )
            ->getForm();
    }

}
