<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Captcha\Captcha;
use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\Commentaire;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Form\CommentaireType;
use AcMarche\Sepulture\Form\SearchSepultureType;
use AcMarche\Sepulture\Form\SepultureAddType;
use AcMarche\Sepulture\Form\SepultureType;
use AcMarche\Sepulture\Repository\SepultureRepository;
use AcMarche\Sepulture\Service\CimetiereUtil;
use AcMarche\Sepulture\Service\FileHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Route(path: '/sepulture')]
class SepultureController extends AbstractController
{
    public function __construct(
        private readonly SepultureRepository $sepultureRepository,
        private readonly FileHelper $fileHelper,
        private readonly CimetiereUtil $cimetiereUtil,
        private readonly Captcha $captcha,
        private readonly ManagerRegistry $managerRegistry
    ) {
    }


    #[Route(path: '/', name: 'sepulture', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $search = false;
        $data = [];
        if ($session->has('sepulture_search')) {
            $data = unserialize($session->get('sepulture_search'));
        }
        $search_form = $this->createForm(
            SearchSepultureType::class,
            $data,
            [
                'method' => 'GET',
            ]
        );
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $search = true;
        }
        $session->set('sepulture_search', serialize($data));
        $entities = [];
        if (0 != (is_countable($data) ? \count($data) : 0)) {
            $search = true;
            $entities = $this->sepultureRepository->search($data);
        }

        return $this->render(
            '@Sepulture/sepulture/index.html.twig',
            [
                'search' => $search,
                'search_form' => $search_form->createView(),
                'entities' => $entities,
            ]
        );
    }

    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/new', name: 'sepulture_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $em = $this->managerRegistry->getManager();
        $sepulture = new Sepulture();
        $date = new DateTime();
        $year = $date->format('Y');
        $sepulture->setAnneeReleve($year);
        $user = $this->getUser();
        $cimetiereId = $this->cimetiereUtil->getCimetiereByDefault($user->getUserIdentifier());
        if ($cimetiereId) {
            $cimetiere = $em->getRepository(Cimetiere::class)->find($cimetiereId);
            if (null !== $cimetiere) {
                $sepulture->setCimetiere($cimetiere);
            }
        }
        $form = $this->createForm(SepultureAddType::class, $sepulture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $sepulture->setUserAdd($user);

            $em->persist($sepulture);
            $em->flush();

            $this->addFlash('success', 'La sépulture a bien été ajoutée');

            return $this->redirectToRoute('sepulture_show', [
                'slug' => $sepulture->getSlug(),
            ]);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@Sepulture/sepulture/new.html.twig',
            [
                'entity' => $sepulture,
                'form' => $form->createView(),
            ]
            , $response
        );
    }

    #[Route(path: '/{slug}', name: 'sepulture_show', methods: ['GET'])]
    public function show(Request $request, Sepulture $sepulture): Response
    {
        $images = $this->fileHelper->getImages($sepulture->getId());
        $session = $request->getSession();
        if ($session->has(Captcha::SESSION_NAME)) {
            $commentaire = $session->get(Captcha::SESSION_NAME);
        } else {
            $commentaire = new Commentaire();
            $commentaire->setSepulture($sepulture);
        }
        $deleteForm = $this->createDeleteForm($sepulture->getId());
        try {
            $animals = $this->captcha->getAnimals();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface) {
            $animals = [];
        }
        $form = $this->createForm(
            CommentaireType::class,
            $commentaire,
            [
                'action' => $this->generateUrl(
                    'commentaire_new',
                    [
                        'id' => $sepulture->getId(),
                    ]
                ),
            ]
        );

        return $this->render(
            '@Sepulture/sepulture/show.html.twig',
            [
                'form_commentaire' => $form->createView(),
                'sepulture' => $sepulture,
                'images' => $images,
                'animals' => $animals,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/{slug}/edit', name: 'sepulture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sepulture $sepulture): Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(SepultureType::class, $sepulture);
        $deleteForm = $this->createDeleteForm($sepulture->getId());
        $images = $this->fileHelper->getImages($sepulture->getId(), 5);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La sépulture a bien été modifiée');

            return $this->redirectToRoute('sepulture_show', [
                'slug' => $sepulture->getSlug(),
            ]);
        }

        return $this->render(
            '@Sepulture/sepulture/edit.html.twig',
            [
                'entity' => $sepulture,
                'images' => $images,
                'form' => $form->createView(),
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/{id}/delete', name: 'sepulture_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $entity = $this->sepultureRepository->find($id);

            if (!$entity instanceof Sepulture) {
                throw $this->createNotFoundException('Unable to find Sepulture entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'La sépulture a bien été supprimée');
        }

        return $this->redirectToRoute('sepulture');
    }

    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('sepulture_delete', [
                    'id' => $id,
                ])
            )
            ->getForm();
    }

    #[Route(path: '/interet/sihl/{id}', name: 'sepulture_sihl', methods: ['GET'])]
    public function sihl(Cimetiere $cimetiere): Response
    {
        $sepultures = $this->sepultureRepository->getImportanceHistorique($cimetiere);

        return $this->render(
            '@Sepulture/sepulture/sihl.html.twig',
            [
                'sepultures' => $sepultures,
                'cimetiere' => $cimetiere,
            ]
        );
    }

    #[Route(path: '/interet/a1945/{id}', name: 'sepulture_a1945', methods: ['GET'])]
    public function a1945(Cimetiere $cimetiere): Response
    {
        $sepultures = $this->sepultureRepository->getAvant1945($cimetiere);

        return $this->render(
            '@Sepulture/sepulture/a1945.html.twig',
            [
                'sepultures' => $sepultures,
                'cimetiere' => $cimetiere,
            ]
        );
    }
}
