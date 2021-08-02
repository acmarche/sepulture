<?php

namespace AcMarche\Sepulture\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\FormInterface;
use AcMarche\Sepulture\Captcha\Captcha;
use AcMarche\Sepulture\Entity\Commentaire;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Form\CommentaireType;
use AcMarche\Sepulture\Repository\CommentaireRepository;
use AcMarche\Sepulture\Service\CimetiereUtil;
use AcMarche\Sepulture\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Commentaire controller.
 *
 * @Route("/commentaire")
 */
class CommentaireController extends AbstractController
{
    private CimetiereUtil $cimetiereUtil;
    private Mailer $mailer;
    private CommentaireRepository $commentaireRepository;
    private Captcha $captcha;
    private SessionInterface $session;

    public function __construct(
        CommentaireRepository $commentaireRepository,
        CimetiereUtil $cimetiereUtil,
        Mailer $mailer,
        Captcha $captcha,
        SessionInterface $session
    ) {
        $this->cimetiereUtil = $cimetiereUtil;
        $this->mailer = $mailer;
        $this->commentaireRepository = $commentaireRepository;
        $this->captcha = $captcha;
        $this->session = $session;
    }

    /**
     * Lists all Commentaire entities.
     *
     * @Route("/", name="commentaire", methods={"GET"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
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
     *
     * @Route("/new/{id}", name="commentaire_new", methods={"GET","POST"})
     */
    public function new(Request $request, Sepulture $sepulture): Response
    {
        $commentaire = new Commentaire();
        $commentaire->setSepulture($sepulture);

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
            ->add('submit', SubmitType::class, ['label' => 'Create']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('commentaire');
            if ($this->captcha->check($data['captcha'])) {
                $this->mailer->sendCommentaire($commentaire, $this->cimetiereUtil->error);
                $this->commentaireRepository->persist($commentaire);
                $this->commentaireRepository->flush();

                if ($this->session->has(Captcha::SESSION_NAME)) {
                    $this->session->remove(Captcha::SESSION_NAME);
                }

                $this->addFlash('success', 'Le commentaire a bien été ajouté, merci de votre collaboration');
            } else {
                $this->session->set(Captcha::SESSION_NAME, $commentaire);
                $this->addFlash('danger', 'Le contrôle anti-spam a échoué');
            }
        } else {
            $this->addFlash('danger', 'Form error: '.$form->getErrors());
        }

        return $this->redirectToRoute('sepulture_show', array('slug' => $sepulture->getSlug()));
    }

    /**
     * Finds and displays a Commentaire entity.
     *
     * @Route("/{id}", name="commentaire_show", methods={"GET"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
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
     *
     * @Route("/{id}", name="commentaire_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function delete(Request $request, $id): Response
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Commentaire::class)->find($id);

            if ($entity === null) {
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
            ->setAction($this->generateUrl('commentaire_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete'])
            ->getForm();
    }
}
