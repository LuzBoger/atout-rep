<?php

namespace App\Controller;

use App\Entity\HomeRepair;
use App\Entity\Painting;
use App\Entity\Roofing;
use App\Form\HomeRepair1Type;
use App\Form\PaintingType;
use App\Form\RoofingType;
use App\Repository\HomeRepairRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/request-repair-house')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class HomeRepairController extends AbstractController
{
    #[Route(name: 'app_home_repair_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(HomeRepairRepository $homeRepairRepository, Security $security): Response
    {
        $user = $security->getUser();
        $homeRepairs = $homeRepairRepository->findByUser($user);

        // Ajouter les types aux objets
        $homeRepairsWithTypes = array_map(function (HomeRepair $repair) {
            return [
                'repair' => $repair,
                'type' => $repair->getType(), // Appel de la méthode définie dans les enfants
            ];
        }, $homeRepairs);

        return $this->render('home_repair/index.html.twig', [
            'home_repairs_with_types' => $homeRepairsWithTypes,
        ]);
    }


    #[Route('/{id}', name: 'app_home_repair_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(HomeRepair $homeRepair): Response
    {
        // Vérifie le type de l'entité et redirige vers la route correspondante
        if ($homeRepair instanceof Roofing) {
            return $this->redirectToRoute('app_roofing_show', ['id' => $homeRepair->getId()]);
        } elseif ($homeRepair instanceof Painting) {
            return $this->redirectToRoute('app_painting_show', ['id' => $homeRepair->getId()]);
        }

        // Par défaut, renvoie une erreur ou une page générique si le type n'est pas reconnu
        throw $this->createNotFoundException('Type de réparation inconnu.');
    }

    #[Route('/{id}/edit', name: 'app_home_repair_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, HomeRepair $homeRepair, EntityManagerInterface $entityManager): Response
    {
        if ($homeRepair instanceof Roofing) {
            $form = $this->createForm(RoofingType::class, $homeRepair);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_roofing_show', ['id' => $homeRepair->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->render('roofing/edit.html.twig', [
                'formRoofing' => $form,
                'roofing' => $homeRepair
            ]);
        }elseif ($homeRepair instanceof Painting) {
            $form = $this->createForm(PaintingType::class, $homeRepair);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_painting_show', ['id' => $homeRepair->getId()], Response::HTTP_SEE_OTHER);

            }

            return $this->render('painting/edit.html.twig', [
                'formPainting' => $form,
                'painting' => $homeRepair
            ]);
        }
        throw $this->createNotFoundException('Type de réparation inconnu.');

    }

    #[Route('/{id}', name: 'app_home_repair_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, HomeRepair $homeRepair, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$homeRepair->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($homeRepair);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home_repair_index', [], Response::HTTP_SEE_OTHER);
    }

}
