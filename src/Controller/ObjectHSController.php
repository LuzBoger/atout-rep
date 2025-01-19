<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Dates;
use App\Entity\ObjectHS;
use App\Entity\Photo;
use App\Enum\StatusRequest;
use App\Form\DatesType;
use App\Form\ObjectHSType;
use App\Form\PhotosCollectionType;
use App\Repository\ObjectHSRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/request-object-hs')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ObjectHSController extends AbstractController
{
    #[Route(name: 'app_object_hs_index', methods: ['GET'])]
    public function index(ObjectHSRepository $objectHSRepository, Security $security): Response
    {
        $user = $security->getUser();

        $objectHS = $objectHSRepository->findByUser($user);

        return $this->render('object_hs/index.html.twig', [
            'object_hs' => $objectHS,
        ]);
    }

    #[Route('/new', name: 'app_object_hs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $step = (int) $request->get('step', 1);

        $formObjectHS = $this->createForm(ObjectHSType::class, new ObjectHS());
        $formDates = $this->createForm(DatesType::class, new Dates(), [
            'user' => $this->getUser(),
        ]);
        $formPhotos = $this->createForm(PhotosCollectionType::class, ['photos' => []]);

        $formObjectHS->handleRequest($request);
        $formDates->handleRequest($request);
        $formPhotos->handleRequest($request);

        $session = $request->getSession();

        if ($step === 1 && $formObjectHS->isSubmitted() && $formObjectHS->isValid()) {
            $objectHS = $formObjectHS->getData();
            $objectHS->setClient($this->getUser());
            $objectHS->setCreationDate(new \DateTime());
            $objectHS->setModificationDate(new \DateTime());
            $objectHS->setStatus(StatusRequest::PENDING);

            $entityManager->persist($objectHS);
            $entityManager->flush();

            // Stocker l'identifiant en session
            $session->set('objectHSId', $objectHS->getId());

            return $this->redirectToRoute('app_object_hs_new', ['step' => 2]);
        }

        if ($step === 2 && $formDates->isSubmitted() && $formDates->isValid()) {
            $objectHSId = $session->get('objectHSId');
            $objectHS = $entityManager->getRepository(ObjectHS::class)->find($objectHSId);

            if (!$objectHS) {
                return $this->redirectToRoute('app_object_hs_new'); // Recommencer si invalide
            }

            $dates = $formDates->getData();
            $dates->setRequest($objectHS);

            $entityManager->persist($dates);
            $entityManager->flush();

            // Stocker l'identifiant des dates
            $session->set('datesId', $dates->getId());

            return $this->redirectToRoute('app_object_hs_new', ['step' => 3]);
        }

        if ($step === 3 && $formPhotos->isSubmitted() && $formPhotos->isValid()) {
            $objectHSId = $session->get('objectHSId');
            $datesId = $session->get('datesId');

            $objectHS = $entityManager->getRepository(ObjectHS::class)->find($objectHSId);
            $dates = $entityManager->getRepository(Dates::class)->find($datesId);

            if (!$objectHS || !$dates) {
                return $this->redirectToRoute('app_object_hs_new'); // Recommencer si invalide
            }

            foreach ($formPhotos->get('photos') as $photoForm) {
                /** @var Photo $photo */
                $photo = $photoForm->getData();
                $file = $photoForm->get('photoPath')->getData(); // Récupérer le fichier téléchargé

                if ($file) {
                    // Générer un nom unique pour le fichier
                    $newFilename = uniqid() . '.' . $file->guessExtension();

                    // Déplacer le fichier vers le répertoire configuré
                    $file->move($this->getParameter('photos_directory'), $newFilename);

                    // Mettre à jour la propriété `photoPath` dans l'entité
                    $photo->setPhotoPath($newFilename);
                } else {
                    throw new \RuntimeException('Aucun fichier valide trouvé.');
                }

                // Ajouter des informations supplémentaires
                $photo->setObjectHS($objectHS);
                $photo->setUploadDate(new \DateTime());

                $entityManager->persist($photo);
            }



            $entityManager->flush();

            return $this->redirectToRoute('app_object_hs_index');
        }

        return $this->render('object_hs/new.html.twig', [
            'step' => $step,
            'formObjectHS' => $formObjectHS->createView(),
            'formDates' => $formDates->createView(),
            'formPhotos' => $formPhotos->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_object_hs_show', methods: ['GET'])]
    public function show(ObjectHS $objectHS, Security $security): Response
    {
        // Vérifiez si l'utilisateur est connecté
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifiez si l'utilisateur connecté est bien le propriétaire de l'objet
        if ($objectHS->getClient() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cet objet.');
        }
        dump($objectHS->getDates()->get(0)->getAddress());

        return $this->render('object_hs/show.html.twig', [
            'object_h' => $objectHS,
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
