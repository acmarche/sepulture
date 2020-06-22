<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Entity\Page;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add(
                'contenu',
                CKEditorType::class,
                [
                    'config_name' => 'sepulture_config',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'imageFile',
                FileType::class,
                [
                    'label' => 'Image',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Page::class,
            ]
        );
    }
}
