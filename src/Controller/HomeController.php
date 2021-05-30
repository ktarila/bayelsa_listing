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
use App\Utils\Helpers;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $advertRepository;
    private $paginator;
    private $helpers;

    public function __construct(Helpers $helpers, AdvertRepository $advertRepository, PaginatorInterface $paginator)
    {
        $this->advertRepository = $advertRepository;
        $this->paginator = $paginator;
        $this->helpers = $helpers;
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
            'hasItems' => \count($this->helpers->iterable_to_array($pagination->getItems())) === $this->getParameter('page_limit'),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/my-ads', name: 'my_ads', methods: ['GET', 'POST'])]
    public function myAds(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AdvertSearchType::class, null, []);

        $page = $request->query->getInt('page', 1);

        $form_data = [];
        if ($request->isMethod('post') && 0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $form_data = json_decode($request->getContent(), true);
        }

        $queryBuilder = $this->advertRepository->advancedFilter($form_data);

        $queryBuilder
            ->andWhere('d.email = :email OR d.user = :user_id')
            ->setParameter('email', $user->getEmail())
            ->setParameter('user_id', $user->getId())
        ;

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
            'hasItems' => \count($this->helpers->iterable_to_array($pagination->getItems())) === $this->getParameter('page_limit'),
        ]);
    }

    #[Route('/buyers', name: 'buyers', methods: ['GET', 'POST'])]
    public function listBuyers(Request $request): Response
    {
        $form = $this->createForm(AdvertSearchType::class, null, ['category' => 'buy']);

        $page = $request->query->getInt('page', 1);

        $form_data = [];
        if ($request->isMethod('post') && 0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $form_data = json_decode($request->getContent(), true);
        }

        $queryBuilder = $this->advertRepository->advancedFilter($form_data);

        $queryBuilder
            ->andWhere('c.name = :buy')
            ->setParameter('buy', 'buy')
        ;

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
            'hasItems' => \count($this->helpers->iterable_to_array($pagination->getItems())) === $this->getParameter('page_limit'),
        ]);
    }

    #[Route('/sellers', name: 'sellers', methods: ['GET', 'POST'])]
    public function listSellers(Request $request): Response
    {
        $form = $this->createForm(AdvertSearchType::class, null, ['category' => 'sell']);

        $page = $request->query->getInt('page', 1);

        $form_data = [];
        if ($request->isMethod('post') && 0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $form_data = json_decode($request->getContent(), true);
        }

        $queryBuilder = $this->advertRepository->advancedFilter($form_data);

        $queryBuilder
            ->andWhere('c.name != :buy')
            ->setParameter('buy', 'buy')
        ;

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
            'hasItems' => \count($this->helpers->iterable_to_array($pagination->getItems())) === $this->getParameter('page_limit'),
        ]);
    }
}
