<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Customer;
use App\Entity\OrderHistory;
use App\Entity\OrderHistoryProduct;
use App\Enum\StatusCart;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function checkout(EntityManagerInterface $entityManager, Security $security): Response
    {
        $this->denyAccessUnlessGranted('user_zone');
        /** @var Customer $customer */
        $customer = $security->getUser();

        $cart = $entityManager->getRepository(Cart::class)->findOneBy([
            'customer' => $customer,
            'statusCart' => StatusCart::ACTIVE // Statut panier en attente
        ]);

        if (!$cart || $cart->getCartProducts()->isEmpty()) {
            return $this->redirectToRoute('app_cart_index'); // Redirige si panier vide
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $lineItems = [];
        $totalPrice = 0;

        foreach ($cart->getCartProducts() as $cartProduct) {
            $price = $cartProduct->getProduct()->getPrice();
            $quantity = $cartProduct->getQuantity();
            $totalPrice += $price * $quantity;

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $cartProduct->getProduct()->getName(),
                    ],
                    'unit_amount' => $price * 100, // Prix en centimes
                ],
                'quantity' => $quantity,
            ];
        }

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($checkoutSession->url);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(EntityManagerInterface $entityManager): Response
    {
        $cart = $entityManager->getRepository(Cart::class)->findOneBy([
            'customer' => $this->getUser(),
            'statusCart' => StatusCart::ACTIVE
        ]);

        if ($cart) {
            // Création de l'historique de la commande
            $orderHistory = new OrderHistory();
            $orderHistory->setCustomer($cart->getCustomer());
            $orderHistory->setTotalPrice(array_reduce(
                $cart->getCartProducts()->toArray(),
                fn($carry, $cartProduct) => $carry + ($cartProduct->getProduct()->getPrice() * $cartProduct->getQuantity()),
                0
            ));

            // Copier les produits du panier dans l'historique
            foreach ($cart->getCartProducts() as $cartProduct) {
                $orderProduct = new OrderHistoryProduct();
                $orderProduct->setOrderHistory($orderHistory);
                $orderProduct->setProductName($cartProduct->getProduct()->getName());
                $orderProduct->setQuantity($cartProduct->getQuantity());
                $orderProduct->setPrice($cartProduct->getProduct()->getPrice());

                $entityManager->persist($orderProduct);
                $orderHistory->addOrderProduct($orderProduct);

                // Supprimer les CartProducts sans toucher au Cart
                $entityManager->remove($cartProduct);
            }

            // Sauvegarde de l'historique de commande
            $entityManager->persist($orderHistory);

            // Mettre le panier à vide après suppression des CartProducts
            $cart->getCartProducts()->clear();
            $entityManager->persist($cart);

            // Appliquer les modifications en base
            $entityManager->flush();
        }

        return $this->render('payment/success.html.twig');
    }



    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
}

