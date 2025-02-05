<?php

namespace App\Controller;

use App\Entity\HomeRepair;
use App\Entity\ObjectHS;
use App\Entity\Painting;
use App\Entity\Roofing;
use App\Enum\StatusRequest;
use App\Repository\HomeRepairRepository;
use App\Repository\ObjectHSRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/dashboard', name: 'dashboard_admin')]
    public function dashboardAdmin(ObjectHSRepository $objectHSRepository, HomeRepairRepository $homeRepairRepository): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');

        return $this->render('admin/content.html.twig', [
            'object_hs' => $objectHSRepository->findPendingWithLimit(),
            'home_repairs_with_types' => $homeRepairRepository->findPendingWithLimit(),
        ]);
    }

    #[Route('/admin/object-hs', name: 'app_object_hs_index_admin', methods: ['GET'])]
    public function requestObjectHSAll(ObjectHSRepository $objectHSRepository, Security $security, Request $request): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');
        // Récupérer le filtre depuis la requête (sinon mettre "pending" par défaut)
        $status = $request->query->get('status', 'pending');

        // Récupérer les objets filtrés
        $objectHS = $objectHSRepository->findByStatusAndFilterByModificationDate($status);

        return $this->render('admin/object_hs_index.html.twig', [
            'object_hs' => $objectHS,
            'current_status' => $status
        ]);
    }

    #[Route('/admin/home-repair', name: 'app_home_repair_index_admin', methods: ['GET'])]
    public function requestHomeRepairAll(HomeRepairRepository $homeRepairRepository, Security $security, Request $request): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');
        // Récupérer le filtre depuis la requête (sinon mettre "pending" par défaut)
        $status = $request->query->get('status', 'pending');

        // Récupérer les objets filtrés
        $homeRepair = $homeRepairRepository->findByStatusAndFilterByModificationDate($status);

        return $this->render('admin/home_repair_index.html.twig', [
            'home_repairs_with_types' => $homeRepair,
            'current_status' => $status
        ]);
    }

    #[Route('/admin/object_hs/{id}', name: 'app_object_hs_show_admin', methods: ['GET'])]
    public function show(ObjectHS $objectHS): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');

        return $this->render('admin/show_object_hs_admin.html.twig', [
            'object_h' => $objectHS,
        ]);
    }

    #[Route('admin/home_repair/{id}', name: 'app_home_repair_show_admin', methods: ['GET'])]
    public function showHomeRepairAdmin(HomeRepair $homeRepair): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');

        // Vérifie le type de l'entité et redirige vers la route correspondante
        if ($homeRepair instanceof Roofing) {
            return $this->redirectToRoute('app_roofing_show_admin', ['id' => $homeRepair->getId()]);
        } elseif ($homeRepair instanceof Painting) {
            return $this->redirectToRoute('app_painting_show_admin', ['id' => $homeRepair->getId()]);
        }

        // Par défaut, renvoie une erreur ou une page générique si le type n'est pas reconnu
        throw $this->createNotFoundException('Type de réparation inconnu.');
    }

    #[Route('admin/roofing/{id}', name: 'app_roofing_show_admin', methods: ['GET'])]
    public function showRoofingAdmin(Roofing $roofing): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');

        return $this->render('admin/roofing/show.html.twig', [
            'roofing' => $roofing,
        ]);
    }

    #[Route('admin/painting/{id}', name: 'app_painting_show_admin', methods: ['GET'])]
    public function showPaintingAdmin(Painting $painting): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');

        return $this->render('admin/painting/show.html.twig', [
            'painting' => $painting,
        ]);
    }

    #[Route('admin/objectHS/{id}', name: 'app_object_hs_delete_admin', methods: ['POST'])]
    public function deleteObjectHS(Request $request, ObjectHS $objectHS, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('admin_zone');

        if ($this->isCsrfTokenValid('delete'.$objectHS->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($objectHS);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_object_hs_index_admin', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('admin/painting/{id}', name: 'app_painting_delete_admin', methods: ['POST'])]
    public function deletePaintingAdmin(Request $request, Painting $painting, EntityManagerInterface $entityManager, Security $security): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');

        if ($this->isCsrfTokenValid('delete'.$painting->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($painting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home_repair_index_admin', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('admin/roofing/{id}', name: 'app_roofing_delete_admin', methods: ['POST'])]
    public function deleteRoofingAdmin(Request $request, Roofing $roofing, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');
        if ($this->isCsrfTokenValid('delete' . $roofing->getId(), $request->get('_token'))) {
            $entityManager->remove($roofing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home_repair_index_admin', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/request/{id}/validate', name: 'app_request_validate_admin', methods: ['POST'])]
    public function validateRequest(\App\Entity\Request $requestEntity, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');

        if ($this->isCsrfTokenValid('validate' . $requestEntity->getId(), $request->request->get('_token'))) {
            $requestEntity->setStatus(StatusRequest::VALIDATED);
            $entityManager->persist($requestEntity);
            $entityManager->flush();

            $this->addFlash('success', 'La demande a été validée avec succès.');
        }

        return $this->redirectToRoute($this->getShowRoute($requestEntity), ['id' => $requestEntity->getId()]);
    }

    #[Route('/admin/request/{id}/reject', name: 'app_request_reject_admin', methods: ['POST'])]
    public function rejectRequest(\App\Entity\Request $requestEntity, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('admin_zone');

        if ($this->isCsrfTokenValid('reject' . $requestEntity->getId(), $request->request->get('_token'))) {
            $requestEntity->setStatus(StatusRequest::REJECTED);
            $entityManager->persist($requestEntity);
            $entityManager->flush();

            $this->addFlash('error', 'La demande a été rejetée.');
        }

        return $this->redirectToRoute($this->getShowRoute($requestEntity), ['id' => $requestEntity->getId()]);
    }

    private function getShowRoute(\App\Entity\Request $requestEntity): string
    {
        return match (true) {
            $requestEntity instanceof ObjectHS => 'app_object_hs_show_admin',
            $requestEntity instanceof Painting => 'app_painting_show_admin',
            $requestEntity instanceof Roofing => 'app_roofing_show_admin',
            default => 'dashboard_admin', // Redirection par défaut en cas d'erreur
        };
    }

}
