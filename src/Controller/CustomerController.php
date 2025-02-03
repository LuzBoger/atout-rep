<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Account;
use App\Form\AddressType;
use App\Form\CustomerType;
use App\Form\EditCustomerType;
use App\Form\RegistrationCustomerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CustomerController extends AbstractController
{
    #[Route('/{id}/profil', name: 'app_profil', methods: ['GET', 'POST'])]
    public function index(Request $request, Account $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('user_zone');

        if($user instanceof Customer){
            $formCustomer = $this->createForm(EditCustomerType::class, $user);
            $formCustomer->handleRequest($request);
            if($formCustomer->isSubmitted() && $formCustomer->isValid()) {
                // Rôle par défaut
                dump($user);
                foreach ($user->getAddresses() as $address) {
                    if ($address->getId() === null) { // Si l'adresse est nouvelle
                        $address->setCustomer($user); // S'assurer que la relation est correctement définie
                        $entityManager->persist($address);
                    }
                }
                $user->setRoles(['ROLE_USER']);
                $entityManager->persist($user);
                $entityManager->flush();
                dump($user);
                $this->addFlash('success', 'Modification du profil effectué avec succès !');
            }
            //dump($user);
            return $this->render('customer/profil.html.twig', [
                'formCustomer' => $formCustomer,
                'customer' => $user
            ]);
        }

        return $this->render('home/index.html.twig');

    }
}
