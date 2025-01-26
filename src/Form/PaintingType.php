<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Painting;
use App\Enum\PaintType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaintingType extends HomeRepairType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';

        $builder
            ->add('surfaceArea', NumberType::class, [
                'label' => 'Surface à peindre (en m²)',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('paintType', ChoiceType::class, [
                'label' => 'Type de peinture',
                'choices' => [
                    'Intérieur' => [
                        'Acrylique' => PaintType::ACRYLIC,
                        'Latex' => PaintType::LATEX,
                        'Mat' => PaintType::MATTE,
                        'Satiné' => PaintType::SATIN,
                        'Semi-brillant' => PaintType::SEMI_GLOSS,
                        'Brillant' => PaintType::GLOSSY,
                    ],
                    'Extérieur' => [
                        'À base d\'huile' => PaintType::OIL_BASED,
                        'À base d\'eau' => PaintType::WATER_BASED,
                        'Époxy' => PaintType::EPOXY,
                        'Texturée' => PaintType::TEXTURED,
                    ],
                    'Polyvalent (intérieur/extérieur)' => [
                        'Sous-couche (Primer)' => PaintType::PRIMER,
                    ],
                ],
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du projet',
                'attr' => [
                    'class' => $inputCssClass . " col-span-2",
                    'rows' => 4,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Painting::class,
        ]);
    }
}
