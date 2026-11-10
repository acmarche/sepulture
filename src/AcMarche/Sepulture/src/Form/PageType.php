<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Entity\Page;
use Mati365\CKEditor5Symfony\Form\Type\CKEditor5Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add(
                'contenu',
                CKEditor5Type::class,
                [
                    'preset' => 'sepulture',
                    'editable_height' => 300,
                    'language' => 'fr',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Page::class,
            ]
        );
    }
}
