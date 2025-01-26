<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Roofing;
use App\Enum\RoofMaterial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoofingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';

        $builder
            ->add('roofMaterial', ChoiceType::class, [
                'label' => 'Matériau du toit',
                'choices' => [
                    'Tuiles en asphalte' => RoofMaterial::ASPHALT_SHINGLES,
                    'Métal (aluminium, acier, zinc)' => RoofMaterial::METAL,
                    'Tuiles en terre cuite' => RoofMaterial::CLAY_TILES,
                    'Tuiles en béton' => RoofMaterial::CONCRETE_TILES,
                    'Bardeaux en bois' => RoofMaterial::WOOD_SHINGLES,
                    'Ardoise' => RoofMaterial::SLATE,
                    'Matériaux synthétiques' => RoofMaterial::SYNTHETIC,
                    'Toit végétalisé' => RoofMaterial::GREEN_ROOF,
                    'Goudron et gravier' => RoofMaterial::TAR_AND_GRAVEL,
                    'Membrane (PVC ou EPDM)' => RoofMaterial::MEMBRANE,
                ],
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('needInsulation', ChoiceType::class, [
                'label' => 'Isolation nécessaire ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'attr' => [
                    'class' => 'flex gap-4', // Exemple pour un rendu aligné des boutons radio.
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
            'data_class' => Roofing::class,
        ]);
    }
}