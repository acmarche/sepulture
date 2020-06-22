<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Entity\ContactRw;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactRwType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'gestionnaire',
                TextType::class,
                [
                    'label' => 'Gestionnaire public',
                ]
            )
            ->add(
                'adresse',
                TextType::class,
                [
                    'help' => 'Adresse complète : Rue numéro, code postal localité',
                ]
            )
            ->add(
                'codeIns',
                TextType::class,
                [
                ]
            )
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Personne de contact',
                    'help' => 'Nom + prénom',
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'dateRapport',
                DateType::class,
                [
                    'html5' => true,
                    'label' => 'Date du rapport',
                ]
            )
            ->add(
                'dateExpiration',
                DateType::class,
                [
                    'html5' => true,
                    'label' => 'Date expiration',
                    'help' => 'Le délai laissé pour reprendre les signes indicatifs de sépulture expire le ',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ContactRw::class,
            ]
        );
    }
}
