<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre : "
            ])
            ->add('description', TextareaType::class, [
                'label' => "Description : "
            ])
            ->add('city', TextType::class, [
                'label' => "Ville : "
            ])
            ->add('zip', IntegerType::class, [
                'label' => "Code postal : "
            ])
            ->add('price', IntegerType::class, [
                'label' => "Prix : "
            ])
            ->add('category', EntityType::class, [
                'label' => "Catégorie : ",
                'class' => Category::class,
                'multiple' => false,
                'expanded' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
