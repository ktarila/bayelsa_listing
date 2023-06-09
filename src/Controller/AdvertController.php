<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Comment;
use App\Form\AdvertType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Utils\Helpers;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/advert')]
class AdvertController extends AbstractController
{
    private $commentRepository;
    private $paginator;
    private $helpers;
    private $security;

    public function __construct(Security $security, Helpers $helpers, CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        $this->commentRepository = $commentRepository;
        $this->paginator = $paginator;
        $this->helpers = $helpers;
        $this->security = $security;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/login/check/{advert}', name: 'redirect_advert_show', methods: ['GET'])]
    public function adRedirect(Request $request, Advert $advert): Response
    {
        return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
    }

    #[Route('/new', name: 'advert_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $advert = new Advert();
        $user = $this->getUser();
        if (null !== $user) {
            $advert->setFullname($user->getFullname())
                ->setPhone($user->getPhone())
                ->setEmail($user->getEmail())
                ->setUser($user)
            ;
        }

        $form = $this->createForm(AdvertType::class, $advert);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($advert);
            $entityManager->flush();

            return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
        }

        return $this->render('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'advert_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Advert $advert): Response
    {
        $parameters = ['advert' => $advert];

        $page = $request->query->getInt('page', 1);

        $queryBuilder = $this->commentRepository->getAdvertComments($advert);

        $comments = $this->paginator->paginate(
            $queryBuilder->getQuery(), /* query NOT result */
            $page/*page number*/ ,
            $this->getParameter('page_limit'), /*limit per page*/ /*limit per page*/
            [
                'pageParameterName' => 'page',
            ]
        );
        $parameters['comments'] = $comments;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            $comment = new Comment();
            $comment->setAdvert($advert)->setUser($user);
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            $parameters['commentForm'] = $form->createView();

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();

                return $this->redirectToRoute('comment_show', ['id' => $comment->getId()]);
            }
        }

        $parameters['next'] = $page + 1;
        $parameters['hasItems'] = \count($this->helpers->iterable_to_array($comments->getItems())) === $this->getParameter('page_limit');

        return $this->render('advert/show.html.twig', $parameters);
    }

    #[Route('/{id}/edit', name: 'advert_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Advert $advert): Response
    {
        if (!$this->isGranted('edit', $advert)) {
            $msg = "Please login with {$advert->getEmail()} to edit.";
            $this->addFlash('errorMsg', $msg);

            return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
        }

        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(AdvertType::class, new Advert());
            $form->handleRequest($request);

            return $this->render('advert/edit.html.twig', [
                'advert' => $advert,
                'form' => $form->createView(),
            ]);
        }

        if (null === $advert->getUser()) {
            $advert->setUser($this->getUser());
        }
        // ensure always updated so images will be attached
        $advert->setUpdatedAt();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$request->isXmlHttpRequest()) {
            // $photo = $form['photo']->getData();
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/remove', name: 'advert_delete', methods: ['POST'])]
    public function delete(Request $request, Advert $advert): Response
    {
        if (!$this->isGranted('edit', $advert)) {
            $msg = "Please login with {$advert->getEmail()} to remove advert.";
            $this->addFlash('errorMsg', $msg);

            return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
        }

        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($advert);
            $entityManager->flush();
            $this->addFlash('infoMsg', 'advert removed');
        }

        return $this->redirectToRoute('home');
    }

    /*
     * IsGranted('ROLE_USER')
    */

    #[Route('/toggle/like/{id}/{csrf}', name: 'advert_like_toggle', methods: ['GET'])]
    public function toggleLike(Advert $advert, $csrf): Response
    {
        $user = $this->getUser();

        $removed = false;
        if ($this->isCsrfTokenValid('togglelike'.$advert->getId(), $csrf) && null !== $user) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($advert->userLiked($user)) {
                $advert->removeUserLike($user);
                $removed = true;
            } else {
                $advert->addUserLike($user);
            }
            $entityManager->flush();
        }

        return $this->json(['removed' => $removed]);
    }

    #[Route('/toggle/dislike/{id}/{csrf}', name: 'advert_dislike_toggle', methods: ['GET'])]
    public function toggleDislike(Advert $advert, $csrf): Response
    {
        $user = $this->getUser();
        $removed = false;
        if ($this->isCsrfTokenValid('toggledislike'.$advert->getId(), $csrf) && null !== $user) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($advert->userDisliked($user)) {
                $advert->removeUserDislike($user);
                $removed = true;
            } else {
                $advert->addUserDislike($user);
            }
            $entityManager->flush();
        }

        return $this->json(['removed' => $removed]);
    }
}
