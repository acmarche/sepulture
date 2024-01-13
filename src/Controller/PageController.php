<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Page;
use AcMarche\Sepulture\Form\PageType;
use AcMarche\Sepulture\Service\FileHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
/**
 * Page controller.
 */
#[Route(path: '/page')]
class PageController extends AbstractController
{
    public function __construct(
        private FileHelper $fileHelper,
        private ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Finds and displays a Page entity.
     */
    #[Route(path: '/{slug}', name: 'page_show', methods: ['GET'])]
    public function show(Page $page): Response
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
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page): Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createForm(PageType::class, $page);
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
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Page entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/delete', name: 'page_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $entity = $em->getRepository(Page::class)->find($id);

            if (null === $entity) {
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
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('page_delete', [
                'id' => $id,
            ]))

            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
            ])
            ->getForm();
    }

    private function traitfiles(FormInterface $form, Page $page): void
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
            $em = $this->managerRegistry->getManager();
            $em->flush();
        }
    }
}
