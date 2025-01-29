<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Customer;
use App\Entity\Product;
use App\Enum\StatusCart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(Product $product, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        /** @var Customer $customer */
        $customer = $security->getUser();

        dump($customer);
        if (!$customer) {
            return $this->redirectToRoute('app_login'); // Redirige vers la connexion si non connecté
        }

        $quantity = (int) $request->request->get('quantity');

        // Validation du stock
        if ($quantity < 1 || $quantity > $product->getStock()) {
            $this->addFlash('error', 'Quantité invalide.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        // Récupérer ou créer le panier de l'utilisateur
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['customer' => $customer]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setCustomer($customer);
            $cart->setStatusCart(StatusCart::ACTIVE);
            $entityManager->persist($cart);
        }

        // Vérifier si le produit est déjà dans le panier
        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy([
            'cart' => $cart,
            'product' => $product
        ]);
        dump($cartProduct);
        dump($quantity);
        dump($customer);

        if ($cartProduct) {
            // Mise à jour de la quantité
            $cartProduct->setQuantity($cartProduct->getQuantity() + $quantity);
        } else {
            // Création d'un nouvel élément dans le panier
            $cartProduct = new CartProduct();
            $cartProduct->setCart($cart);
            $cartProduct->setProduct($product);
            $cartProduct->setQuantity($quantity);
            $entityManager->persist($cartProduct);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Produit ajouté au panier avec succès !');

        return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
    }

    #[Route('/cart', name: 'app_cart_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le panier du client
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['customer' => $user]);

        if (!$cart) {
            return $this->render('cart/index.html.twig', [
                'cartProducts' => [],
                'totalPrice' => 0,
            ]);
        }

        // Récupérer les produits du panier
        $cartProducts = $cart->getCartProducts();

        // Calcul du prix total
        $totalPrice = array_reduce($cartProducts->toArray(), function ($total, CartProduct $cartProduct) {
            return $total + ($cartProduct->getProduct()->getPrice() * $cartProduct->getQuantity());
        }, 0);

        return $this->render('cart/index.html.twig', [
            'cartProducts' => $cartProducts,
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function remove(CartProduct $cartProduct, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();

        // Vérification que l'utilisateur est bien propriétaire du panier
        if ($cartProduct->getCart()->getCustomer() !== $user) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer un produit qui n'est pas dans votre panier.");
        }

        // Suppression du produit du panier
        $entityManager->remove($cartProduct);
        $entityManager->flush();

        // Message de confirmation
        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateQuantity(CartProduct $cartProduct, Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        // Vérifier que l'utilisateur est bien propriétaire du panier
        if ($cartProduct->getCart()->getCustomer() !== $user) {
            return new JsonResponse(['error' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }

        // Récupérer la nouvelle quantité envoyée
        $data = json_decode($request->getContent(), true);
        $newQuantity = isset($data['quantity']) ? (int) $data['quantity'] : 0;

        // Vérification des valeurs de quantité
        if ($newQuantity < 1 || $newQuantity > $cartProduct->getProduct()->getStock()) {
            return new JsonResponse(['error' => "Quantité invalide"], Response::HTTP_BAD_REQUEST);
        }

        // Mettre à jour la quantité dans la base de données
        $cartProduct->setQuantity($newQuantity);
        $entityManager->flush();

        // Calculer le nouveau total du panier
        $cart = $cartProduct->getCart();
        $totalPrice = array_reduce($cart->getCartProducts()->toArray(), function ($total, CartProduct $cartProduct) {
            return $total + ($cartProduct->getProduct()->getPrice() * $cartProduct->getQuantity());
        }, 0);

        return new JsonResponse([
            'success' => true,
            'newQuantity' => $newQuantity,
            'totalPrice' => number_format($totalPrice, 2, ',', ' ')
        ]);
    }
}