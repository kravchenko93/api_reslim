<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Ingredient;
use App\Entity\Dish;
use App\Entity\UserDishChoicePerDate;
use App\Entity\UserDishExcludedPerDate;
use App\Entity\Subscription;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Html');
    }

    public function configureMenuItems(): iterable
    {
        return [
             MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),
             MenuItem::linkToCrud('Users', 'fa fa-users', User::class),
             MenuItem::linkToCrud('Ingredient', 'fa fa-list', Ingredient::class),
             MenuItem::linkToCrud('Dish', 'fa fa-cutlery', Dish::class),
//             MenuItem::section('logs'),
             MenuItem::linkToCrud('UserDishChoice', null, UserDishChoicePerDate::class),
             MenuItem::linkToCrud('UserDishExcluded', null, UserDishExcludedPerDate::class),
            MenuItem::linkToCrud('Subscription settings', null, Subscription::class)
        ];
    }
}
