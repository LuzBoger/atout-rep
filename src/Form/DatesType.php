<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Dates;
use App\Entity\Request;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['customer'];
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => $inputCssClass,
                    'placeholder' => 'Sélectionnez une date',
                ],
                'label' => 'Date',
            ])
            ->add('address', EntityType::class, [
                'class' => Address::class,
                'attr' => [
                    'class' => $inputCssClass,
                ],
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('a')
                        ->where('a.customer = :user')
                        ->setParameter('user', $user);
                },
                'label' => 'Adresse',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dates::class,
            'customer' => null,
        ]);
    }
}
