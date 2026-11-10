<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Entity\Defunt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefuntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'fonction',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Remarque',
                    'attr' => [
                        'rows' => 5,
                    ],
                ]
            )
            ->add(
                'lieu_naissance',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'birthday',
                TextType::class,
                [
                    'label' => 'Né le',
                    'required' => false,
                    'help' => 'jj/mm/aaaa',
                    'attr' => [
                        'placeholder' => 'jj/mm/aaaa',
                    ],
                ]
            )
            ->add(
                'date_deces',
                TextType::class,
                [
                    'label' => 'Date de décès',
                    'required' => false,
                    'help' => 'jj/mm/aaaa',
                    'attr' => [
                        'placeholder' => 'jj/mm/aaaa',
                    ],
                ]
            )
            ->add(
                'lieu_deces',
                TextType::class,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Defunt::class,
            ]
        );
    }
}
