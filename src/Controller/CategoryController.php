<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\AdvertSearchType;
use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use App\Utils\Helpers;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
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

    #[Route('/', name: 'category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/{id}/{slug}', name: 'category_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Category $category): Response
    {
        $form = $this->createForm(AdvertSearchType::class, null, []);

        $page = $request->query->getInt('page', 1);

        $form_data = [];
        if ($request->isMethod('post') && 0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $form_data = json_decode($request->getContent(), true);
        }

        $queryBuilder = $this->advertRepository->advancedFilter($form_data);
        $queryBuilder->andWhere('d.category = :cat_id')
            ->setParameter('cat_id', $category->getId())
        ;

        $pagination = $this->paginator->paginate(
            $queryBuilder->getQuery(), /* query NOT result */
            $page/*page number*/ ,
            $this->getParameter('page_limit'), /*limit per page*/ /*limit per page*/
            [
                'pageParameterName' => 'page',
            ]
        );

        return $this->render('category/show.html.twig', [
            'adverts' => $pagination,
            'category' => $category,
            'next' => $page + 1,
            'form' => $form->createView(),
            'hasItems' => \count($this->helpers->iterable_to_array($pagination->getItems())) === $this->getParameter('page_limit'),
        ]);
    }
}
