<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('videoId', TextType::class, [
                'label' => 'ID Vidéo',
                'attr' => ['class' => 'form-control']
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
        ->add('playlist', EntityType::class, [
                'label' => 'Playlist',
                'class' => Playlist::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez une playlist',
                'attr' => ['class' => 'form-control']
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Catégories',
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['class' => 'form-select', 'size' => 5]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
