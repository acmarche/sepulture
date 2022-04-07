<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Entity\Legal;
use AcMarche\Sepulture\Entity\Materiaux;
use AcMarche\Sepulture\Entity\Ossuaire;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Entity\Sihl;
use AcMarche\Sepulture\Entity\TypeSepulture;
use AcMarche\Sepulture\Entity\Visuel;
use AcMarche\Sepulture\Repository\LegalRepository;
use AcMarche\Sepulture\Repository\VisuelRepository;
use AcMarche\Sepulture\Service\CimetiereUtil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SepultureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $statuts = CimetiereUtil::getStatuts();

        $builder
            ->add(
                'ossuaire',
                EntityType::class,
                [
                    'class' => Ossuaire::class,
                    'choice_label' => function (Ossuaire $ossuaire) {
                        return $ossuaire->getNom().' ('.$ossuaire->getCimetiere().')';
                    },
                    'required' => false,
                ]
            )
            ->add(
                'types',
                EntityType::class,
                [
                    'class' => TypeSepulture::class,
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                    'label' => 'Type',
                ]
            )
            ->add(
                'type_autre',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Autre type que ci dessus ?',
                ]
            )
            ->add(
                'materiaux',
                EntityType::class,
                [
                    'class' => Materiaux::class,
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                ]
            )
            ->add(
                'materiaux_autre',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Autre materiaux que ci dessus ?',
                    'attr' => [],
                ]
            )
            ->add(
                'sihls',
                EntityType::class,
                [
                    'class' => Sihl::class,
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                    'label' => 'Sépulture Intérêt Historique Locale',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Description sommaire',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'visuel',
                EntityType::class,
                [
                    'class' => Visuel::class,
                    'query_builder' => fn(VisuelRepository $cr) => $cr->getForList(),
                    'required' => false,
                ]
            )
            ->add(
                'legal',
                EntityType::class,
                [
                    'class' => Legal::class,
                    'query_builder' => fn(LegalRepository $cr) => $cr->getForList(),
                    'required' => false,
                ]
            )
            ->add(
                'symbole',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Symboles et leur localisation',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'architectural',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Architecte, entrepreneur, carrier.',
                ]
            )
            ->add(
                'epitaphe',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Epitaphes et devises',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'architectural',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Architecte, entrepreneur, carrier.',
                    'attr' => [],
                ]
            )
            ->add(
                'sociale',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Fonction sociale du defunt',
                ]
            )
            ->add(
                'sociale_check',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Aucune inscription sociale',
                ]
            )
            ->add(
                'guerre',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => '1er inhumation avant 1945 ?',
                ]
            )
            ->add(
                'combattant14',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Ancien combattant 14-18',
                    'attr' => [],
                ]
            )
            ->add(
                'combattant40',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Ancien combattant 40-45',
                    'attr' => [],
                ]
            )
            ->add(
                'contact',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Héritiers et adresse de contact',
                    'help' => 'Non visible sur le site',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'description_autre',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Autres infos',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'statut',
                ChoiceType::class,
                [
                    'choices' => $statuts,
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

    public function getParent(): ?string
    {
        return SepultureAddType::class;
    }
}
