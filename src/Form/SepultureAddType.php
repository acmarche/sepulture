<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Repository\CimetiereRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SepultureAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'parcelle',
                TextType::class,
                [
                    'required' => true,
                    'help' => 'Exempe de nom : CCA/L03-05',
                ]
            )
            ->add(
                'cimetiere',
                EntityType::class,
                [
                    'class' => Cimetiere::class,
                    'query_builder' => fn(CimetiereRepository $cr) => $cr->getForList(),
                    'placeholder' => 'Sélectionnez un cimetière',
                    'label' => 'Cimetière',
                ]
            )
            ->add(
                'annee_releve',
                NumberType::class,
                [
                    'required' => false,
                    'label' => 'Année de relevée',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Sepulture::class,
            ]
        );
    }
}
