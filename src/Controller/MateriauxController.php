<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Materiaux;
use AcMarche\Sepulture\Form\MateriauxType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Materiaux controller.
 *
 * @Route("/materiaux")
 */
class MateriauxController extends AbstractController
{
    /**
     * Lists all Materiaux entities.
     *
     * @Route("/", name="materiaux", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Materiaux::class)->findAll();

        return $this->render(
            '@Sepulture/materiaux/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Creates a form to create a Materiaux entity.
     *
     * @param Materiaux $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Materiaux $entity)
    {
        $form = $this->createForm(
            MateriauxType::class,
            $entity,
            [
                'action' => $this->generateUrl('materiaux_new'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Materiaux entity.
     *
     * @Route("/new", name="materiaux_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function newAction(Request $request)
    {
        $entity = new Materiaux();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Le matériaux a bien été ajouté');

            return $this->redirectToRoute('materiaux');
        }

        return $this->render(
            '@Sepulture/materiaux/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Materiaux entity.
     *
     * @Route("/{id}", name="materiaux_show", methods={"GET"})
     */
    public function showAction(Materiaux $materiaux)
    {
        $deleteForm = $this->createDeleteForm($materiaux->getId());

        return $this->render(
            '@Sepulture/materiaux/show.html.twig',
            [
                'entity' => $materiaux,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Materiaux entity.
     *
     * @Route("/{id}/edit", name="materiaux_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function editAction(Request $request, Materiaux $materiaux)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($materiaux);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le matériaux a bien été modifié');

            return $this->redirectToRoute('materiaux');
        }

        return $this->render(
            '@Sepulture/materiaux/edit.html.twig',
            [
                'entity' => $materiaux,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Materiaux entity.
     *
     * @param Materiaux $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Materiaux $entity)
    {
        $form = $this->createForm(
            MateriauxType::class,
            $entity,
            [
                'action' => $this->generateUrl('materiaux_edit', ['id' => $entity->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Deletes a Materiaux entity.
     *
     * @Route("/{id}", name="materiaux_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Materiaux::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Materiaux entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('success', 'Le matériaux a bien été supprimé');
        }

        return $this->redirectToRoute('materiaux');
    }

    /**
     * Creates a form to delete a Materiaux entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('materiaux_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }
}
