<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Captcha\Captcha;
use AcMarche\Sepulture\Entity\Commentaire;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Form\CommentaireType;
use AcMarche\Sepulture\Repository\CommentaireRepository;
use AcMarche\Sepulture\Service\CimetiereUtil;
use AcMarche\Sepulture\Service\Mailer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
/**
 * Commentaire controller.
 */
#[Route(path: '/commentaire')]
class CommentaireController extends AbstractController
{
    public function __construct(
        private CommentaireRepository $commentaireRepository,
        private CimetiereUtil $cimetiereUtil,
        private Mailer $mailer,
        private Captcha $captcha,
        private ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Lists all Commentaire entities.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/', name: 'commentaire', methods: ['GET'])]
    public function index(): Response
    {
        $entities = $this->commentaireRepository->findAll();

        return $this->render(
            '@Sepulture/commentaire/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Displays a form to create a new Commentaire entity.
     */
    #[Route(path: '/new/{id}', name: 'commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Sepulture $sepulture): RedirectResponse
    {
        $commentaire = new Commentaire();
        $commentaire->setSepulture($sepulture);
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        $session = $request->getSession();
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all('commentaire');
            if ($this->captcha->check($data['captcha'])) {
                $this->mailer->sendCommentaire($commentaire, $this->cimetiereUtil->error);
                $this->commentaireRepository->persist($commentaire);
                $this->commentaireRepository->flush();

                if ($session->has(Captcha::SESSION_NAME)) {
                    $session->remove(Captcha::SESSION_NAME);
                }

                $this->addFlash('success', 'Le commentaire a bien été ajouté, merci de votre collaboration');
            } else {
                $session->set(Captcha::SESSION_NAME, $commentaire);
                $this->addFlash('danger', 'Le contrôle anti-spam a échoué');
            }
        } else {
            $this->addFlash('danger', 'Form error: '.$form->getErrors());
        }

        return $this->redirectToRoute('sepulture_show', [
            'slug' => $sepulture->getSlug(),
        ]);
    }

    /**
     * Finds and displays a Commentaire entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}', name: 'commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        $deleteForm = $this->createDeleteForm($commentaire->getId());

        return $this->render(
            '@Sepulture/commentaire/show.html.twig',
            [
                'entity' => $commentaire,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Commentaire entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/delete', name: 'commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $entity = $em->getRepository(Commentaire::class)->find($id);

            if (null === $entity) {
                throw $this->createNotFoundException('Unable to find Commentaire entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirectToRoute('commentaire');
    }

    /**
     * Creates a form to delete a Commentaire entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commentaire_delete', [
                'id' => $id,
            ]))
            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
            ])
            ->getForm();
    }
}
