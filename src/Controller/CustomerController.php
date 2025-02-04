<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Account;
use App\Entity\Provider;
use App\Form\AddressType;
use App\Form\EditCustomerType;
use App\Form\EditProviderType;
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
        $this->denyAccessUnlessGranted('profil_zone');

        if ($user instanceof Customer) {
            $formCustomer = $this->createForm(EditCustomerType::class, $user);
            $formCustomer->handleRequest($request);

            if ($formCustomer->isSubmitted() && $formCustomer->isValid()) {
                foreach ($user->getAddresses() as $address) {
                    if ($address->getId() === null) {
                        $address->setCustomer($user);
                        $entityManager->persist($address);
                    }
                }
                $user->setRoles(['ROLE_USER']);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Modification du profil effectuée avec succès !');
            }

            return $this->render('customer/profil.html.twig', [
                'formCustomer' => $formCustomer,
                'customer' => $user,
            ]);
        }

        if ($user instanceof Provider) {
            $formProvider = $this->createForm(EditProviderType::class, $user);
            $formProvider->handleRequest($request);

            if ($formProvider->isSubmitted() && $formProvider->isValid()) {
                $user->setRoles(['ROLE_PRESTA']);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Modification du profil effectuée avec succès !');
            }

            return $this->render('provider/profil.html.twig', [
                'formProvider' => $formProvider,
                'provider' => $user,
            ]);
        }

        return $this->render('home/index.html.twig');
    }
}
