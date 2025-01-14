<?php

namespace App\Controller;

use App\Entity\Dates;
use App\Entity\ObjectHS;
use App\Enum\StatusRequest;
use App\Form\DatesType;
use App\Form\ObjectHSType;
use App\Repository\ObjectHSRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/request-object-hs')]
final class ObjectHSController extends AbstractController
{
    #[Route(name: 'app_object_hs_index', methods: ['GET'])]
    public function index(ObjectHSRepository $objectHSRepository, Security $security): Response
    {
        $user = $security->getUser();

        $objectHS = $objectHSRepository->findBy(['client' => $user]);

        return $this->render('object_hs/index.html.twig', [
            'object_hs' => $objectHS,
        ]);
    }

    #[Route('/new', name: 'app_object_hs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $step = $request->get('step', 1);

        $objectHS = new ObjectHS();
        $dates = new Dates();

        $formObjectHS = $this->createForm(ObjectHSType::class, $objectHS);
        $formDates = $this->createForm(DatesType::class, $dates, [
            'user' => $this->getUser(),
        ]);
        $formObjectHS->handleRequest($request);
        $formDates->handleRequest($request);

        if ($step == 1 && $formObjectHS->isSubmitted() && $formObjectHS->isValid()) {
            $objectHS->setCreationDate(new \DateTime());
            $objectHS->setModificationDate(new \DateTime());
            $objectHS->setStatus(StatusRequest::PENDING);

            $request->getSession()->set('objectHS', $objectHS); // Stocker temporairement dans la session
            return $this->redirectToRoute('app_object_hs_new', ['step' => 2]);
        }
        if($step == 2 && $formDates->isSubmitted() && $formDates->isValid()) {
            $objectHS = $request->getSession()->get('objectHS');

            if (!$objectHS) {
                return $this->redirectToRoute('app_object_hs_new'); // Recommencer si l'objet est manquant
            }

            $objectHS->setClient($this->getUser());
            $dates->setRequest($objectHS);

            $entityManager->persist($objectHS);
            $entityManager->persist($dates);

            $entityManager->flush();

            $request->getSession()->remove('objectHS'); // Nettoyer la session
            return $this->redirectToRoute('app_object_hs_index');
        }

        return $this->render('object_hs/new.html.twig', [
            'step' => $step,
            'formObjectHS' => $formObjectHS->createView(),
            'formDates' => $formDates->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_object_hs_show', methods: ['GET'])]
    public function show(ObjectHS $objectH): Response
    {
        return $this->render('object_hs/show.html.twig', [
            'object_h' => $objectH,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_object_hs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ObjectHS $objectH, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ObjectHSType::class, $objectH);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_object_h_s_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('object_hs/edit.html.twig', [
            'object_h' => $objectH,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_object_h_s_delete', methods: ['POST'])]
    public function delete(Request $request, ObjectHS $objectH, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$objectH->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($objectH);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_object_h_s_index', [], Response::HTTP_SEE_OTHER);
    }
}
