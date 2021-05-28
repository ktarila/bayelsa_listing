<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller;

use App\Form\AdvertSearchType;
use App\Repository\AdvertRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $advertRepository;
    private $paginator;

    public function __construct(AdvertRepository $advertRepository, PaginatorInterface $paginator)
    {
        $this->advertRepository = $advertRepository;
        $this->paginator = $paginator;
    }

    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(AdvertSearchType::class, null, []);

        $page = $request->query->getInt('page', 1);

        $form_data = [];
        if ($request->isMethod('post') && 0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $form_data = json_decode($request->getContent(), true);
        }

        $queryBuilder = $this->advertRepository->advancedFilter($form_data);

        $pagination = $this->paginator->paginate(
            $queryBuilder->getQuery(), /* query NOT result */
            $page/*page number*/ ,
            $this->getParameter('page_limit'), /*limit per page*/ /*limit per page*/
            [
                'pageParameterName' => 'page',
            ]
        );

        return $this->render('advert/index.html.twig', [
            'adverts' => $pagination,
            'next' => $page + 1,
            'form' => $form->createView(),
            'hasItems' => \count($pagination->getItems()) > 0,
        ]);
    }
}
