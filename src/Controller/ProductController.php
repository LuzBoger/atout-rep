<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Product;
use App\Entity\Provider;
use App\Form\PhotosCollectionType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/marketplace')]
final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, Request $request, Security $security): Response
    {
        $this->denyAccessUnlessGranted('repair_house');
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        // Récupération des produits avec pagination
        $pagination = $productRepository->findPaginatedProducts($page, $limit);

        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        $roles = $user ? $user->getRoles() : [];

        return $this->render('product/index.html.twig', [
            'products' => $pagination['items'],
            'currentPage' => $page,
            'totalPages' => $pagination['totalPages'],
            'roles' => $roles, // 👈 Ajout des rôles dans le rendu
        ]);
    }


    #[Route('/products/presta', name: 'app_product_index_presta', methods: ['GET'])]
    #[IsGranted('ROLE_PRESTA')]
    public function indexPresta(ProductRepository $productRepository, Request $request, Security $security): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        // Récupérer le Provider lié à l'utilisateur connecté
        /** @var Provider $provider */
        $provider = $security->getUser();
        dump($provider);

        // Vérifiez si le provider existe
        if (!$provider) {
            throw $this->createNotFoundException('Provider not found for the current user.');
        }

        // Appel à la méthode dans le repository
        $pagination = $productRepository->findPaginatedProductsByProvider($page, $limit, $provider->getId());

        return $this->render('product/index.html.twig', [
            'products' => $pagination['items'],
            'currentPage' => $page,
            'totalPages' => $pagination['totalPages'],
        ]);
    }


    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer le Provider lié à l'utilisateur connecté
        /** @var Provider $provider */
        $provider = $security->getUser();

        // Vérifiez si le provider existe
        if (!$provider) {
            throw $this->createNotFoundException('Provider not found for the current user.');
        }

        $step = (int) $request->get('step', 1);
        $session = $request->getSession();

        $formProduct = $this->createForm(ProductType::class, new Product());
        $formPhotos = $this->createForm(PhotosCollectionType::class, ['photos' => []]);

        $formProduct->handleRequest($request);
        $formPhotos->handleRequest($request);

        if ($step === 1 && $formProduct->isSubmitted() && $formProduct->isValid()) {
            $product = $formProduct->getData();
            $product->setDeleted(false);
            $product->setProvider($provider);
            $entityManager->persist($product);
            $entityManager->flush();

            // Stocke l’ID du produit en session
            $session->set('productId', $product->getId());

            return $this->redirectToRoute('app_product_new', ['step' => 2]);
        }

        if ($step === 2 && $formPhotos->isSubmitted() && $formPhotos->isValid()) {
            $productId = $session->get('productId');
            $product = $entityManager->getRepository(Product::class)->find($productId);

            if (!$product) {
                return $this->redirectToRoute('app_product_new'); // Recommencer si invalide
            }

            foreach ($formPhotos->get('photos') as $photoForm) {
                $file = $photoForm->get('photoPath')->getData();
                if ($file) {
                    $newFilename = uniqid() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('photos_directory'), $newFilename);

                    $photo = new Photo();
                    $photo->setPhotoPath($newFilename);
                    $photo->setProduct($product);
                    $photo->setName("");
                    $photo->setUploadDate(new \DateTime());
                    $entityManager->persist($photo);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'step' => $step,
            'formProduct' => $formProduct->createView(),
            'formPhotos' => $formPhotos->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product, Security $security): Response
    {
        $user = $security->getUser();
        $roles = $user ? $user->getRoles() : [];

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'roles' => $roles,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, Security $security): Response
    {
        /** @var Provider $provider */
        $provider = $security->getUser();

        if (!$provider) {
            throw $this->createNotFoundException('Provider not found for the current user.');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des photos
            foreach ($form->get('photos') as $photoForm) {
                $file = $photoForm->get('photoPath')->getData();

                if ($file) {
                    $newFilename = uniqid() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('photos_directory'), $newFilename);

                    $photo = new Photo();
                    $photo->setPhotoPath($newFilename);
                    $photo->setProduct($product);
                    $photo->setUploadDate(new \DateTime());

                    $entityManager->persist($photo);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
