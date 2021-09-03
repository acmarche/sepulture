<?php

namespace AcMarche\Sepulture\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

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
    public function index(): Response
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
     * @return Form The form
     */
    private function createCreateForm(Materiaux $entity): FormInterface
    {
        $form = $this->createForm(
            MateriauxType::class,
            $entity,
            [
                'action' => $this->generateUrl('materiaux_new'),
                'method' => 'POST',
            ]
        );

        $form;

        return $form;
    }

    /**
     * Displays a form to create a new Materiaux entity.
     *
     * @Route("/new", name="materiaux_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function new(Request $request): Response
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
    public function show(Materiaux $materiaux): Response
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
     * @Route("/{id}/edit", name="materiaux_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function edit(Request $request, Materiaux $materiaux): Response
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
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Materiaux entity.
     *
     * @param Materiaux $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Materiaux $entity): FormInterface
    {
        $form = $this->createForm(
            MateriauxType::class,
            $entity,
            [
                'action' => $this->generateUrl('materiaux_edit', ['id' => $entity->getId()]),

            ]
        );



        return $form;
    }

    /**
     * Deletes a Materiaux entity.
     *
     * @Route("/{id}/delete", name="materiaux_delete", methods={"POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function delete(Request $request, $id): Response
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Materiaux::class)->find($id);

            if ($entity === null) {
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
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('materiaux_delete', ['id' => $id]))

            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }
}
