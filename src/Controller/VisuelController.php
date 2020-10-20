<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Visuel;
use AcMarche\Sepulture\Form\VisuelType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Visuel controller.
 *
 * @Route("/visuel")
 */
class VisuelController extends AbstractController
{
    /**
     * Lists all Visuel entities.
     *
     * @Route("/", name="visuel", methods={"GET"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Visuel::class)->findAll();

        return $this->render(
            '@Sepulture/visuel/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Creates a form to create a Visuel entity.
     *
     * @param Visuel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Visuel $entity)
    {
        $form = $this->createForm(
            VisuelType::class,
            $entity,
            [
                'action' => $this->generateUrl('visuel_new'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Visuel entity.
     *
     * @Route("/new", name="visuel_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function new(Request $request)
    {
        $entity = new Visuel();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Le visuel a bien été ajouté');

            return $this->redirectToRoute('visuel');
        }

        return $this->render(
            '@Sepulture/visuel/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Visuel entity.
     *
     * @Route("/{id}", name="visuel_show", methods={"GET"})
     */
    public function show(Visuel $visuel)
    {
        $deleteForm = $this->createDeleteForm($visuel->getId());

        return $this->render(
            '@Sepulture/visuel/show.html.twig',
            [
                'entity' => $visuel,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Visuel entity.
     *
     * @Route("/{id}/edit", name="visuel_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function edit(Request $request, Visuel $visuel)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($visuel);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le visuel a bien été modifié');

            return $this->redirectToRoute('visuel');
        }

        return $this->render(
            '@Sepulture/visuel/edit.html.twig',
            [
                'entity' => $visuel,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Visuel entity.
     *
     * @param Visuel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Visuel $entity)
    {
        $form = $this->createForm(
            VisuelType::class,
            $entity,
            [
                'action' => $this->generateUrl('visuel_edit', ['id' => $entity->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Deletes a Visuel entity.
     *
     * @Route("/{id}", name="visuel_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function delete(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Visuel::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Visuel entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('success', 'Le visuel a bien été supprimé');
        }

        return $this->redirectToRoute('visuel');
    }

    /**
     * Creates a form to delete a Visuel entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('visuel_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }
}
