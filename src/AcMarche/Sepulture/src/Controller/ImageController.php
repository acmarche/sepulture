<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Form\ImageDropZoneType;
use AcMarche\Sepulture\Service\FileHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/image')]
class ImageController extends AbstractController
{
    public function __construct(
        private readonly FileHelper $fileHelper,
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/new/{id}', name: 'image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sepulture $sepulture): Response
    {
        $form = $this->createForm(ImageDropZoneType::class);
        $images = $this->fileHelper->getImages($sepulture->getId());
        $deleteForm = $this->createDeleteForm($sepulture->getId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile[] $data
             */
            $data = $form->getData();
            foreach ($data['file'] as $file) {
                if ($file instanceof UploadedFile) {

                    if (!str_contains($file->getMimeType(), 'image')) {
                        $this->addFlash('danger', 'Uniquement des images');

                        return $this->redirectToRoute('image_edit', [
                            'sepulture' => $sepulture->getSlug(),
                        ]);
                    }

                    $fileName = md5(uniqid()).'.'.$file->guessClientExtension();
                    $directory = $this->getParameter(
                            'acmarche_sepulture_upload_sepulture_directory'
                        ).\DIRECTORY_SEPARATOR.$sepulture->getId();

                    try {
                        $this->fileHelper->uploadFile($directory, $file, $fileName);
                    } catch (FileException $error) {
                        $this->addFlash('danger', $error->getMessage());
                    }
                }
            }
        }
        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@Sepulture/image/edit.html.twig',
            [
                'images' => $images,
                'form' => $form,
                'form_delete' => $deleteForm->createView(),
                'sepulture' => $sepulture,
            ]
            , $response
        );
    }

    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/delete/{sepultureId}', name: 'image_delete', methods: ['POST'])]
    public function delete(Request $request, $sepultureId): RedirectResponse
    {
        $em = $this->managerRegistry->getManager();
        $sepulture = $em->getRepository(Sepulture::class)->find($sepultureId);
        if (null === $sepulture) {
            throw $this->createNotFoundException('Unable to find Sepulture entity.');
        }
        $form = $this->createDeleteForm($sepultureId);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->get('img', false);

            if (!$files) {
                $this->addFlash('danger', "Vous n'avez sélectionnez aucune photo");

                return $this->redirectToRoute('image_edit', [
                    'sepulture' => $sepulture->getSlug(),
                ]);
            }

            $directory = $this->getParameter(
                    'acmarche_sepulture_upload_sepulture_directory'
                ).\DIRECTORY_SEPARATOR.$sepulture->getId().\DIRECTORY_SEPARATOR;
            foreach ($files as $filename) {
                try {
                    $this->fileHelper->deleteOneDoc($directory, $filename);
                    $this->addFlash('success', "L'image $filename a bien été supprimée");
                } catch (FileException) {
                    $this->addFlash('danger', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('image_edit', [
            'id' => $sepulture->getId(),
        ]);
    }

    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('image_delete', [
                    'sepultureId' => $id,
                ])
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Supprimer les images sélectionnées',
                    'attr' => [
                        'class' => 'btn-danger btn-xs',

                    ],
                ]
            )
            ->getForm();
    }
}
