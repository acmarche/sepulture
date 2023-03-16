<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Form\CimetiereType;
use AcMarche\Sepulture\Repository\CimetiereRepository;
use AcMarche\Sepulture\Repository\SepultureRepository;
use AcMarche\Sepulture\Service\CimetiereFileService;
use AcMarche\Sepulture\Service\CimetiereUtil;
use AcMarche\Sepulture\Service\FileHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Cimetiere controller.
 */
#[Route(path: '/cimetiere')]
class CimetiereController extends AbstractController
{
    public function __construct(
        private CimetiereRepository $cimetiereRepository,
        private CimetiereUtil $cimetiereUtil,
        private CimetiereFileService $cimetiereFileService,
        private FileHelper $fileHelper,
        private SepultureRepository $sepultureRepository,
        private ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Lists all Cimetiere entities.
     */
    #[Route(path: '/', name: 'cimetiere', methods: ['GET'])]
    public function index(): Response
    {
        $entities = $this->cimetiereRepository->search([]);
        foreach ($entities as $cimetiere) {
            $ihs = $this->sepultureRepository->getImportanceHistorique($cimetiere);
            $cimetiere->setIhsCount(\count($ihs));
            $a1945 = $this->sepultureRepository->getAvant1945($cimetiere);
            $cimetiere->setA1945Count(\count($a1945));
        }

        return $this->render(
            '@Sepulture/cimetiere/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Displays a form to create a new Cimetiere entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/new', name: 'cimetiere_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entity = new Cimetiere();
        $form = $this->createForm(CimetiereType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cimetiere = $this->cimetiereRepository->findoneBy([
                'nom' => $entity->getNom(),
            ]);

            if (null !== $cimetiere) {
                $this->addFlash('error', 'Il ne peut y avoir deux cimetière avec le même nom');

                return $this->redirectToRoute('cimetiere_new');
            }

            $this->cimetiereRepository->persist($entity);
            $this->cimetiereRepository->flush();

            $this->cimetiereFileService->traitfiles($form, $entity);

            $this->addFlash('success', 'Le cimetière a bien été ajouté');

            return $this->redirectToRoute('cimetiere_show', [
                'slug' => $entity->getSlug(),
            ]);
        }

        return $this->render(
            '@Sepulture/cimetiere/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Cimetiere entity.
     */
    #[Route(path: '/{slug}', name: 'cimetiere_show', methods: ['GET'])]
    public function show(Cimetiere $cimetiere): Response
    {
        $sepultures = $this->sepultureRepository->search([
            'cimetiere' => $cimetiere,
        ]);
        $deleteForm = $this->createDeleteForm($cimetiere->getId());
        $deleteFileForm = $this->createFileDeleteForm($cimetiere->getId());

        return $this->render(
            '@Sepulture/cimetiere/show.html.twig',
            [
                'sepultures' => $sepultures,
                'cimetiere' => $cimetiere,
                'delete_form' => $deleteForm->createView(),
                'delete_file_form' => $deleteFileForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Cimetiere entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{slug}/edit', name: 'cimetiere_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cimetiere $cimetiere): Response
    {
        $editForm = $this->createEditForm($cimetiere);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->cimetiereRepository->flush();
            $this->cimetiereFileService->traitfiles($editForm, $cimetiere);

            $this->addFlash('success', 'Le cimetière a bien été modifié');

            return $this->redirectToRoute('cimetiere_show', [
                'slug' => $cimetiere->getSlug(),
            ]);
        }

        return $this->render(
            '@Sepulture/cimetiere/edit.html.twig',
            [
                'entity' => $cimetiere,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Cimetiere entity.
     *
     * @param Cimetiere $entity The entity
     *
     * @return FormInterface The form
     */
    private function createEditForm(Cimetiere $entity): FormInterface
    {
        return $this->createForm(
            CimetiereType::class,
            $entity,
            [
                'action' => $this->generateUrl('cimetiere_edit', [
                    'slug' => $entity->getSlug(),
                ]),
            ]
        );
    }

    /**
     * Deletes a Cimetiere entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}', name: 'cimetiere_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $this->cimetiereRepository->find($id);

            if (! $entity instanceof Cimetiere) {
                throw $this->createNotFoundException('Unable to find Cimetiere entity.');
            }

            $this->cimetiereRepository->remove($entity);
            $this->cimetiereRepository->flush();

            $this->addFlash('success', 'Le cimetière a bien été supprimé');
        }

        return $this->redirectToRoute('cimetiere');
    }

    /**
     * Creates a form to delete a Cimetiere entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cimetiere_delete', [
                'id' => $id,
            ]))
            ->getForm();
    }

    /**
     * Deletes a Image entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/delete/file/{id}', name: 'cimetiere_file_delete', methods: ['POST'])]
    public function deleteFile(Request $request, Cimetiere $cimetiere): RedirectResponse
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createFileDeleteForm($cimetiere->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $request->get('imageName', false);
            $plan = $request->get('planName', false);

            if (! $image && ! $plan) {
                $this->addFlash('danger', "Vous n'avez sélectionnez aucun fichier");

                return $this->redirectToRoute('cimetiere_show', [
                    'slug' => $cimetiere->getSlug(),
                ]);
            }

            $directory = $this->getParameter('acmarche_sepulture_upload_cimetiere_directory');

            if ($image) {
                try {
                    $this->fileHelper->deleteOneDoc($directory, $image);
                    $this->addFlash('success', "Le fichier $image a bien été supprimé");
                    $cimetiere->setImageName(null);
                } catch (FileException) {
                    $this->addFlash('danger', "Le fichier $image n'a pas pu être supprimé.");
                }
            }

            if ($plan) {
                try {
                    $this->fileHelper->deleteOneDoc($directory, $plan);
                    $this->addFlash('success', "Le fichier $plan a bien été supprimé");
                    $cimetiere->setPlanName(null);
                } catch (FileException) {
                    $this->addFlash('danger', "Le fichier $plan n'a pas pu être supprimé.");
                }
            }

            $em->persist($cimetiere);
            $em->flush();
        }

        return $this->redirectToRoute('cimetiere_show', [
            'slug' => $cimetiere->getSlug(),
        ]);
    }

    private function createFileDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cimetiere_file_delete', [
                'id' => $id,
            ]))

            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Supprimer les fichiers sélectionnés',
                    'attr' => [
                        'class' => 'btn-danger btn-xs',
                        
                    ], ]
            )
            ->getForm();
    }

    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/setdefault/{id}', name: 'cimetiere_set_default', methods: ['GET'])]
    public function setDefaultCimetiere(Request $request, Cimetiere $cimetiere): RedirectResponse
    {
        $user = $this->getUser();
        $em = $this->managerRegistry->getManager();
        $cimetiereId = $request->get('cimetiere');
        $cimetiere = $em->getRepository(Cimetiere::class)->find($cimetiereId);
        if (null === $cimetiere) {
            $this->addFlash('error', 'Cimetière non trouvé');

            return $this->redirectToRoute('cimetiere');
        }
        $this->cimetiereUtil->setCimetiereByDefault($user->getUserIdentifier(), $cimetiere);
        $this->addFlash('success', 'Cimetière mis par défaut');

        return $this->redirectToRoute('cimetiere_show', [
            'slug' => $cimetiere->getSlug(),
        ]);
    }
}
