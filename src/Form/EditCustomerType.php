<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditCustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';
        $builder
            ->add('name', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('surname', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => false,
                'label' => 'Nouveau mot de passe (optionnel)',
                'empty_data' => '',
                'attr' => [
                    'class' => $inputCssClass,
                    'placeholder' => 'Changer le mot de passe',
                ],
            ])->add('addresses', CollectionType::class, [
                'entry_type' => AddressType::class, // Formulaire pour chaque adresse
                'entry_options' => ['label' => false],
                'allow_add' => true, // Permet d’ajouter dynamiquement des adresses
                'allow_delete' => true, // Permet de supprimer dynamiquement des adresses
                'by_reference' => false, // Nécessaire pour les collections OneToMany
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
