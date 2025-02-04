<?php
namespace App\Form;

use App\Entity\Provider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProviderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du fournisseur',
                'attr' => ['class' => $inputCssClass],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => $inputCssClass],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['class' => $inputCssClass],
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => false,
                'label' => 'Nouveau mot de passe (optionnel)',
                'empty_data' => '',
                'attr' => ['class' => $inputCssClass, 'placeholder' => 'Changer le mot de passe'],
            ])
            ->add('priceFDP', NumberType::class, [
                'label' => 'Frais de port',
                'attr' => ['class' => $inputCssClass],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Provider::class,
        ]);
    }
}
