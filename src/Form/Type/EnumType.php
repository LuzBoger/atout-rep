<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => null, // The Enum class to use
            'choice_label_callback' => null, // Optional callback for custom labels
            'choices' => null, // Automatically generated choices from the enum
        ]);

        $resolver->setRequired('class'); // Ensure 'class' is provided
        $resolver->setNormalizer('choices', function (OptionsResolver $options, $value) {
            $enumClass = $options['class'];

            // Generate choices from Enum cases
            return array_combine(
                array_map(
                    $options['choice_label_callback'] ?? fn($enum) => $enum->value,
                    $enumClass::cases()
                ),
                $enumClass::cases()
            );
        });
    }

    public function getParent(): string
    {
        return ChoiceType::class; // Inherits from ChoiceType
    }
}