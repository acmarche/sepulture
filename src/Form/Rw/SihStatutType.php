<?php

namespace AcMarche\Sepulture\Form\Rw;

use AcMarche\Sepulture\Entity\Sepulture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SihStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $status = [1 => 'Privée', 2 => 'Revenue en propriété communale'];

        $builder
            ->add(
                'statutSih',
                ChoiceType::class,
                [
                    'choices' => array_flip($status),
                    'multiple' => false,
                    'expanded' => true,
                    'choice_attr' => ['class' => 'radio-inline'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Sepulture::class,
            ]
        );
    }
}
