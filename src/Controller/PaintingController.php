<?php

namespace App\Controller;

use App\Entity\Dates;
use App\Entity\Painting;
use App\Entity\Photo;
use App\Enum\StatusRequest;
use App\Form\DatesType;
use App\Form\PaintingType;
use App\Form\PhotosCollectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/painting')]
final class PaintingController extends AbstractController
{

    #[Route('/new', name: 'app_painting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('repair_house');

        $step = (int) $request->get('step', 1);

        $formPainting = $this->createForm(PaintingType::class, new Painting());
        $formDates = $this->createForm(DatesType::class, new Dates(), [
            'customer' => $this->getUser(),
        ]);
        $formPhotos = $this->createForm(PhotosCollectionType::class, ['photos' => []]);

        $formPainting->handleRequest($request);
        $formDates->handleRequest($request);
        $formPhotos->handleRequest($request);

        $session = $request->getSession();

        if ($step === 1 && $formPainting->isSubmitted() && $formPainting->isValid()) {
            $painting = $formPainting->getData();
            $painting->setClient($this->getUser());
            $painting->setCreationDate(new \DateTime());
            $painting->setModificationDate(new \DateTime());
            $painting->setStatus(StatusRequest::PENDING);

            $entityManager->persist($painting);
            $entityManager->flush();

            // Stocker l'identifiant en session
            $session->set('paintingId', $painting->getId());

            return $this->redirectToRoute('app_painting_new', ['step' => 2]);
        }

        if ($step === 2 && $formDates->isSubmitted() && $formDates->isValid()) {
            $paintingId = $session->get('paintingId');
            $painting = $entityManager->getRepository(Painting::class)->find($paintingId);

            if (!$painting) {
                return $this->redirectToRoute('app_painting_new'); // Recommencer si invalide
            }

            $dates = $formDates->getData();
            $dates->setRequest($painting);

            $entityManager->persist($dates);
            $entityManager->flush();

            // Stocker l'identifiant des dates
            $session->set('datesId', $dates->getId());

            return $this->redirectToRoute('app_painting_new', ['step' => 3]);
        }

        if ($step === 3 && $formPhotos->isSubmitted() && $formPhotos->isValid()) {
            $paintingId = $session->get('paintingId');
            $datesId = $session->get('datesId');

            $painting = $entityManager->getRepository(Painting::class)->find($paintingId);
            $dates = $entityManager->getRepository(Dates::class)->find($datesId);

            if (!$painting || !$dates) {
                return $this->redirectToRoute('app_painting_new'); // Recommencer si invalide
            }

            foreach ($formPhotos->get('photos') as $photoForm) {
                /** @var Photo $photo */
                $photo = $photoForm->getData();
                $file = $photoForm->get('photoPath')->getData(); // Récupérer le fichier téléchargé

                if ($file) {
                    $newFilename = uniqid() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('photos_directory'), $newFilename);
                    $photo->setPhotoPath($newFilename);
                } else {
                    throw new \RuntimeException('Aucun fichier valide trouvé.');
                }

                $photo->setHomeRepair($painting);
                $photo->setUploadDate(new \DateTime());

                $entityManager->persist($photo);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home_repair_index');
        }

        return $this->render('painting/new.html.twig', [
            'step' => $step,
            'formPainting' => $formPainting->createView(),
            'formDates' => $formDates->createView(),
            'formPhotos' => $formPhotos->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_painting_show', methods: ['GET'])]
    public function show(Painting $painting): Response
    {
        $this->denyAccessUnlessGranted('repair_house');
        return $this->render('painting/show.html.twig', [
            'painting' => $painting,
        ]);
    }

    #[Route('/{id}', name: 'app_painting_delete', methods: ['POST'])]
    public function delete(Request $request, Painting $painting, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('repair_house');
        if ($this->isCsrfTokenValid('delete'.$painting->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($painting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home_repair_index', [], Response::HTTP_SEE_OTHER);
    }
}
