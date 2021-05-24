<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Lga;
use App\Entity\State;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $applicationsUrl = $this->get(CrudUrlGenerator::class)->build()->setController(StateCrudController::class)->generateUrl();

        return $this->redirect($applicationsUrl);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Buy and Sell Admin')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('Main Homepage', 'fas fa-globe', 'home');
        yield MenuItem::linkToCrud('State', 'fas fa-list', State::class);
        yield MenuItem::linkToCrud('LGA', 'fas fa-list', Lga::class);
        yield MenuItem::linkToCrud('Category', 'fas fa-list', Category::class);
    }
}
