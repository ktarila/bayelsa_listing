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
use App\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/new/{advert}', name: 'comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Advert $advert): Response
    {
        $user = $this->getUser();
        $comment = new Comment();
        $comment->setAdvert($advert)->setUser($user);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('comment_show', ['id' => $comment->getId()]);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'comment_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Comment $comment): Response
    {
        $formView = null;
        if ($this->isGranted('ROLE_USER')) {
            $replyComment = new Comment();
            $replyComment->setUser($this->getUser())
                ->setParent($comment)
                ->setAdvert($comment->getAdvert())
                    ;
            $form = $this->createForm(CommentType::class, $replyComment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($replyComment);
                $entityManager->flush();

                return $this->redirectToRoute('comment_show', ['id' => $replyComment->getId()]);
            }
            $formView = $form->createView();
        }

        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
            'replyForm' => $formView,
        ]);
    }

    #[Route('/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment): Response
    {
        $advert = $comment->getAdvert();
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
    }
}
