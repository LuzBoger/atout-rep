<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Product;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Poids',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => $inputCssClass . " col-span-2",
                    'rows' => 4,
                ],
            ])
            ->add('length', NumberType::class, [
                'label' => 'Longueur',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('width', NumberType::class, [
                'label' => 'Largeur',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('height', NumberType::class, [
                'label' => 'Hauteur',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Stock disponible',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

