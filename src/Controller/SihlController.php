<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Sihl;
use AcMarche\Sepulture\Form\SihlType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Sihl controller.
 */
#[Route(path: '/sihl')]
class SihlController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Lists all Sihl entities.
     */
    #[Route(path: '/', name: 'sihl', methods: ['GET'])]
    public function index(): Response
    {
        $em = $this->managerRegistry->getManager();
        $entities = $em->getRepository(Sihl::class)->findAll();

        return $this->render(
            '@Sepulture/sihl/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Creates a form to create a Sihl entity.
     *
     * @param Sihl $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Sihl $entity): FormInterface
    {
        return      $this->createForm(
            SihlType::class,
            $entity,
            [
                'action' => $this->generateUrl('sihl_new'),
                'method' => 'POST',
            ]
        );
    }

    /**
     * Displays a form to create a new Sihl entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/new', name: 'sihl_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entity = new Sihl();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Le sihl a bien été ajouté');

            return $this->redirectToRoute('sihl');
        }

        return $this->render(
            '@Sepulture/sihl/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Sihl entity.
     */
    #[Route(path: '/{id}', name: 'sihl_show', methods: ['GET'])]
    public function show(Sihl $sihl): Response
    {
        $deleteForm = $this->createDeleteForm($sihl->getId());

        return $this->render(
            '@Sepulture/sihl/show.html.twig',
            [
                'entity' => $sihl,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Sihl entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'sihl_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sihl $sihl): Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createEditForm($sihl);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le sihl a bien été modifié');

            return $this->redirectToRoute('sihl');
        }

        return $this->render(
            '@Sepulture/sihl/edit.html.twig',
            [
                'entity' => $sihl,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Sihl entity.
     *
     * @param Sihl $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Sihl $entity): FormInterface
    {
        return $this->createForm(
            SihlType::class,
            $entity,
            [
                'action' => $this->generateUrl('sihl_edit', [
                    'id' => $entity->getId(),
                ]),
                'method' => 'POST',
            ]
        );
    }

    /**
     * Deletes a Sihl entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/delete', name: 'sihl_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $entity = $em->getRepository(Sihl::class)->find($id);

            if (null === $entity) {
                throw $this->createNotFoundException('Unable to find Sihl entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('success', 'Le sihl a bien été supprimé');
        }

        return $this->redirectToRoute('sihl');
    }

    /**
     * Creates a form to delete a Sihl entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sihl_delete', [
                'id' => $id,
            ]))

            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
                'attr' => [
                    'class' => 'btn-danger',
                    
                ], ])
            ->getForm();
    }
}
