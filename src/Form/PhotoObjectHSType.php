<?php

namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class PhotoObjectHSType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputCssClass = 'mt-1 block w-full border p-2 border-gray-300 rounded-md shadow-sm focus:ring-secondary-500 focus:border-secondary-500';

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom photo',
                'attr' => [
                    'class' => $inputCssClass,
                ],
            ])
            ->add('photoPath', FileType::class, [
                'label' => 'Télécharger la photo',
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'class' => $inputCssClass,
                ],
                'constraints' => [
                    new Assert\File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => "Seulement le PNG ou JPEG est autorisé",
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
