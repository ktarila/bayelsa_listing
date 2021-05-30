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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/advert')]
class AdvertController extends AbstractController
{
    private $commentRepository;
    private $paginator;
    private $helpers;

    public function __construct(Helpers $helpers, CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        $this->commentRepository = $commentRepository;
        $this->paginator = $paginator;
        $this->helpers = $helpers;
    }

    #[Route('/new', name: 'advert_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_show', ['id' => $advert->getId()]);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'advert_delete', methods: ['POST'])]
    public function delete(Request $request, Advert $advert): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }
}
