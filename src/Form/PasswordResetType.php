<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'disabled' => true,
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 8]),
                ],
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('repeatPassword', PasswordType::class, [
                'label' => 'Répéter le mot de passe',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ]);
    }
}
