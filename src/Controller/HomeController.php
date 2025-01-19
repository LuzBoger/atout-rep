<?php

namespace App\Controller;

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
    #[Route('/home', name: 'app_home_content')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function app_home_content(Security $security, ObjectHSRepository $objectHSRepository): Response
    {
        $user = $security->getUser();
        $objectHS = $objectHSRepository->findByUserWithMaximumOfThree($user);
        return $this->render('home/content.html.twig', [
            'object_hs' => $objectHS,
        ]);
    }
}
