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
use AcMarche\Sepulture\Service\Mailer;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Sepulture controller.
 *
 * @Route("/sepulture")
 */
class SepultureController extends AbstractController
{
    private FileHelper $fileHelper;
    private CimetiereUtil $cimetiereUtil;
    private SepultureRepository $sepultureRepository;
    private Captcha $captcha;
    private SessionInterface $session;

    public function __construct(
        SepultureRepository $sepultureRepository,
        FileHelper $fileHelper,
        Mailer $mailer,
        CimetiereUtil $cimetiereUtil,
        ParameterBagInterface $parameterBag,
        Captcha $captcha,
        SessionInterface $session
    ) {
        $this->fileHelper = $fileHelper;
        $this->cimetiereUtil = $cimetiereUtil;
        $this->sepultureRepository = $sepultureRepository;
        $this->captcha = $captcha;
        $this->session = $session;
    }

    /**
     * Lists all Sepulture entities.
     *
     * @Route("/", name="sepulture", methods={"GET"})
     */
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

        if (0 != count($data)) {
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

    /**
     * Displays a form to create a new Sepulture entity.
     *
     * @Route("/new", name="sepulture_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function new(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $sepulture = new Sepulture();
        $date = new DateTime();
        $year = $date->format('Y');
        $sepulture->setAnneeReleve($year);

        $user = $this->getUser();

        $cimetiereId = $this->cimetiereUtil->getCimetiereByDefault($user->getUsername());
        if ($cimetiereId) {
            $cimetiere = $em->getRepository(Cimetiere::class)->find($cimetiereId);
            if ($cimetiere !== null) {
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

            return $this->redirectToRoute('sepulture_show', ['slug' => $sepulture->getSlug()]);
        }

        return $this->render(
            '@Sepulture/sepulture/new.html.twig',
            [
                'entity' => $sepulture,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Sepulture entity.
     *
     * @Route("/{slug}", name="sepulture_show", methods={"GET"})
     */
    public function show(Sepulture $sepulture): Response
    {
        $images = $this->fileHelper->getImages($sepulture->getId());

        if ($this->session->has(Captcha::SESSION_NAME)) {
            $commentaire = $this->session->get(Captcha::SESSION_NAME);
        } else {
            $commentaire = new Commentaire();
            $commentaire->setSepulture($sepulture);
        }

        $deleteForm = $this->createDeleteForm($sepulture->getId());
        try {
            $animals = $this->captcha->getAnimals();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface$e) {
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
        )
            ->add('submit', SubmitType::class, ['label' => 'Envoyer']);

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

    /**
     * Displays a form to edit an existing Sepulture entity.
     *
     * @Route("/{slug}/edit", name="sepulture_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function edit(Request $request, Sepulture $sepulture): Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(SepultureType::class, $sepulture);

        $deleteForm = $this->createDeleteForm($sepulture->getId());
        $images = $this->fileHelper->getImages($sepulture->getId(), 5);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La sépulture a bien été modifiée');

            return $this->redirectToRoute('sepulture_show', ['slug' => $sepulture->getSlug()]);
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

    /**
     * Deletes a Sepulture entity.
     *
     * @Route("/{id}", name="sepulture_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function delete(Request $request, $id): Response
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $this->sepultureRepository->find($id);

            if ($entity === null) {
                throw $this->createNotFoundException('Unable to find Sepulture entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'La sépulture a bien été modifiée');
        }

        return $this->redirectToRoute('sepulture');
    }

    /**
     * Creates a form to delete a Sepulture entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sepulture_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Lists all Sepulture entities.
     *
     * @Route("/interet/sihl/{id}", name="sepulture_sihl", methods={"GET"})
     */
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

    /**
     * Lists all Sepulture entities.
     *
     * @Route("/interet/a1945/{id}", name="sepulture_a1945", methods={"GET"})
     */
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
