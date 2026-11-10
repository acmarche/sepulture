<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\Ossuaire;
use AcMarche\Sepulture\Repository\CimetiereRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OssuaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true,
            ])
            ->add(
                'cimetiere',
                EntityType::class,
                [
                    'class' => Cimetiere::class,
                    'query_builder' => fn (CimetiereRepository $cr) => $cr->getForList(),
                    'placeholder' => 'Sélectionnez un cimetière',
                    'label' => 'Cimetière',
                ]
            )
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 8,
                ],
            ])
            ->add('document', DocumentType::class, [
                'label' => 'Image',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ossuaire::class,
        ]);
    }
}
