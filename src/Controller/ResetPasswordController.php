<?php

namespace App\Controller;

use App\Form\PasswordResetType;
use App\Repository\AccountRepository;
use App\Service\AuthMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;


class ResetPasswordController extends AbstractController
{
    #[Route('/forgot', name: 'app_forgot_password')]
    public function forgot(
        Request                $request,
        AccountRepository      $userRepository,
        AuthMailer             $authMailer,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_home'); // Redirige vers une autre page
        }

        $email = $request->get('email');

        if ($email) {
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email');
            } else {
                $resetPasswordToken = Uuid::v4()->toRfc4122();
                $user->setResetPasswordToken($resetPasswordToken);

                // Ajout d'une expiration (optionnel, dans l'entité Account)
                $user->setResetTokenExpireAt(new \DateTimeImmutable('+1 hour'));

                $entityManager->flush();
                $authMailer->sendForgotEmail($user);

                $this->addFlash('success', 'Email envoyé avec succès !');
            }
        }

        return $this->render(view: 'reset_password/forgot.html.twig');
    }

    #[Route(path: '/reset/{uid}', name: 'app_reset_password')]
    public function reset(
        string                 $uid,
        AccountRepository      $userRepository,
        Request                $request,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_home'); // Redirige vers une autre page
        }

        $user = $userRepository->findOneBy(['resetPasswordToken' => $uid]);

        if (!$user || ($user->getResetPasswordToken() && $user->getResetTokenExpireAt() < new \DateTimeImmutable())) {
            $this->addFlash('error', 'Token de réinitialisation invalide ou expiré');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(PasswordResetType::class);
        $form->setData(['email' => $user->getEmail()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['plainPassword'] === $data['repeatPassword']) {
                $user->setResetPasswordToken(null);
                $user->setPlainPassword($data['plainPassword']); // Hachage dans un `prePersist` ou un service dédié.
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe réinitialisé avec succès');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Les mots de passe ne correspondent pas');
        }

        return $this->render(view: 'reset_password/reset.html.twig', parameters: [
            'customer' => $user,
            'form' => $form->createView(),
        ]);
    }
}
