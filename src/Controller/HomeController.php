<?php

namespace App\Controller;

use App\Entity\HomeRepair;
use App\Repository\HomeRepairRepository;
use App\Repository\ObjectHSRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/dashboard', name: 'app_dashboard_content')]
    #[IsGranted('ROLE_USER')]
    public function app_dashboard_content(Security $security, ObjectHSRepository $objectHSRepository, HomeRepairRepository $homeRepairRepository): Response
    {
        $user = $security->getUser();
        $objectHS = $objectHSRepository->findByUserWithMaximumOfThree($user);
        $homeRepairs = $homeRepairRepository->findByUserWithMaximumOfThree($user);

        // Ajouter les types aux objets
        $homeRepairsWithTypes = array_map(function (HomeRepair $repair) {
            return [
                'repair' => $repair,
                'type' => $repair->getType(), // Appel de la méthode définie dans les enfants
            ];
        }, $homeRepairs);

        return $this->render('home/content.html.twig', [
            'object_hs' => $objectHS,
            'home_repairs_with_types' => $homeRepairsWithTypes,
        ]);
    }
}
