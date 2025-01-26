<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\RegistrationCustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        CustomerRepository $customerRepository
    ): Response {
        // Empêche les utilisateurs connectés d'accéder à cette route
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_home'); // Redirige vers une autre page
        }

        $customer = new Customer();

        $form = $this->createForm(RegistrationCustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifie si l'email existe déjà
            if ($customerRepository->findOneBy(['email' => $customer->getEmail()])) {
                $this->addFlash('error', 'Un compte avec ce mail existe déjà !');
                return $this->redirectToRoute('app_register');
            }

            // Rôle par défaut
            $customer->setRoles(['ROLE_USER']);

            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte à bien été créer, vous pouvez maintenant vous connecter !');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
