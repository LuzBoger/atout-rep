<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\ObjectHS;
use App\Enum\StateObject;
use App\Enum\StatusRequest;
use App\Form\Type\EnumType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectHSType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'objet',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('state', EnumType::class, [
                'class' => StateObject::class,
                'label' => 'État de l\'objet',
                'choice_label_callback' => fn(StateObject $state) => match ($state) {
                    StateObject::NEW => 'Neuf',
                    StateObject::GOOD => 'Bon état',
                    StateObject::FAIR => 'Moyen',
                    StateObject::DAMAGED => 'Endommagé',
                    StateObject::BROKEN => 'Cassé',
                    StateObject::UNREPAIRABLE => 'Irréparable',
                },
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge de l\'objet',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('Details', TextareaType::class, [
                'label' => 'Détails supplémentaires',
                'attr' => [
                    'class' => $inputCssClass . " col-span-2",
                    'rows' => 4,
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObjectHS::class,
        ]);
    }
}
