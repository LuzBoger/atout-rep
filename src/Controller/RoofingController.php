<?php
namespace App\Controller;

use App\Entity\Roofing;
use App\Entity\Dates;
use App\Entity\Photo;
use App\Enum\StatusRequest;
use App\Form\RoofingType;
use App\Form\DatesType;
use App\Form\PhotosCollectionType;
use App\Repository\HomeRepairRepository;
use App\Repository\RoofingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/roofing')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class RoofingController extends AbstractController
{
    #[Route('/new', name: 'app_roofing_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $step = (int) $request->get('step', 1);

        $formRoofing = $this->createForm(RoofingType::class, new Roofing());
        $formDates = $this->createForm(DatesType::class, new Dates(), [
            'customer' => $this->getUser(),
        ]);
        $formPhotos = $this->createForm(PhotosCollectionType::class, ['photos' => []]);

        $formRoofing->handleRequest($request);
        $formDates->handleRequest($request);
        $formPhotos->handleRequest($request);

        $session = $request->getSession();

        if ($step === 1 && $formRoofing->isSubmitted() && $formRoofing->isValid()) {
            $roofing = $formRoofing->getData();
            $roofing->setClient($this->getUser());
            $roofing->setCreationDate(new \DateTime());
            $roofing->setModificationDate(new \DateTime());
            $roofing->setStatus(StatusRequest::PENDING);

            $entityManager->persist($roofing);
            $entityManager->flush();

            // Stocker l'identifiant en session
            $session->set('roofingId', $roofing->getId());

            return $this->redirectToRoute('app_roofing_new', ['step' => 2]);
        }

        if ($step === 2 && $formDates->isSubmitted() && $formDates->isValid()) {
            $roofingId = $session->get('roofingId');
            $roofing = $entityManager->getRepository(Roofing::class)->find($roofingId);

            if (!$roofing) {
                return $this->redirectToRoute('app_roofing_new'); // Recommencer si invalide
            }

            $dates = $formDates->getData();
            $dates->setRequest($roofing);

            $entityManager->persist($dates);
            $entityManager->flush();

            // Stocker l'identifiant des dates
            $session->set('datesId', $dates->getId());

            return $this->redirectToRoute('app_roofing_new', ['step' => 3]);
        }

        if ($step === 3 && $formPhotos->isSubmitted() && $formPhotos->isValid()) {
            $roofingId = $session->get('roofingId');
            $datesId = $session->get('datesId');

            $roofing = $entityManager->getRepository(Roofing::class)->find($roofingId);
            $dates = $entityManager->getRepository(Dates::class)->find($datesId);

            if (!$roofing || !$dates) {
                return $this->redirectToRoute('app_roofing_new'); // Recommencer si invalide
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

                $photo->setHomeRepair($roofing);
                $photo->setUploadDate(new \DateTime());

                $entityManager->persist($photo);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home_repair_index');
        }

        return $this->render('roofing/new.html.twig', [
            'step' => $step,
            'formRoofing' => $formRoofing->createView(),
            'formDates' => $formDates->createView(),
            'formPhotos' => $formPhotos->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_roofing_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Roofing $roofing, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || $roofing->getClient() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce projet.');
        }

        return $this->render('roofing/show.html.twig', [
            'roofing' => $roofing,
        ]);
    }

    #[Route('/{id}', name: 'app_roofing_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Roofing $roofing, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $roofing->getId(), $request->get('_token'))) {
            $entityManager->remove($roofing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home_repair_index', [], Response::HTTP_SEE_OTHER);
    }
}

