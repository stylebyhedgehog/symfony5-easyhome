<?php

namespace App\Controller\Admin;

use App\Entity\Ad;
use App\Entity\AdImage;
use App\Entity\Application;
use App\Entity\Client;
use App\Entity\PersonalData;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashBoardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EasyHome');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::linkToCrud('Ads', 'fa fa-tags', Ad::class),
            MenuItem::linkToCrud('AdImage', 'fa fa-tags', AdImage::class),
            MenuItem::linkToCrud('Application', 'fa fa-tags', Application::class),
            MenuItem::linkToCrud('Client', 'fa fa-tags', Client::class),
            MenuItem::linkToCrud('PersonalData', 'fa fa-tags', PersonalData::class),


        ];
    }
}
