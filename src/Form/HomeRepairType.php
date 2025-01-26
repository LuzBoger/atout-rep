<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\HomeRepair;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomeRepairType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('creationDate', null, [
                'widget' => 'single_text',
            ])
            ->add('ModificationDate', null, [
                'widget' => 'single_text',
            ])
            ->add('status')
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HomeRepair::class,
        ]);
    }
}
