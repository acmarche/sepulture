<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Page;
use AcMarche\Sepulture\Form\PageType;
use AcMarche\Sepulture\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Page controller.
 *
 * @Route("/page")
 */
class PageController extends AbstractController
{
    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * Finds and displays a Page entity.
     *
     * @Route("/{slug}", name="page_show", methods={"GET"})
     */
    public function showAction(Page $page)
    {
        $deleteForm = $this->createDeleteForm($page->getId());

        return $this->render(
            '@Sepulture/page/show.html.twig',
            [
                'page' => $page,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     * @Route("/{id}/edit", name="page_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function editAction(Request $request, Page $page)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(PageType::class, $page)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->traitfiles($editForm, $page);

            $this->addFlash('success', 'La page a bien été modifiée');

            return $this->redirectToRoute('home');
        }

        return $this->render(
            '@Sepulture/page/edit.html.twig',
            [
                'entity' => $page,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Page entity.
     *
     * @Route("/{id}", name="page_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Page::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Page entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'La page a bien été supprimée');

            return $this->redirectToRoute('home');
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Creates a form to delete a Page entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('page_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete'])
            ->getForm();
    }

    private function traitfiles(FormInterface $form, Page $page)
    {
        $image = $form->get('imageFile')->getData();

        $directory = $this->getParameter('acmarche_sepulture_upload_cimetiere_directory');

        $fileName = false;

        if ($image instanceof UploadedFile) {
            $fileName = md5(uniqid('', true)).'.'.$image->guessClientExtension();

            try {
                $this->fileHelper->uploadFile($directory, $image, $fileName);
                $page->setImageName($fileName);
            } catch (FileException $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }

        if ($fileName) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }
    }
}
