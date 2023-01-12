<?php

namespace App\Form;

use App\Classes\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('string', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre recherche...',
                    'class' => 'form-control-sm' //ajout d'une classe à l'input string
                ]
            ]) //input

            ->add('categories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'multiple' => true, //permet de sélectionner plusieurs valeurs
                'expanded' => true //permet d'avoir une vue en checkbox et de sélectionner plusieurs valeurs
            ]) //input

            ->add('submit', SubmitType::class, [
                'label' => "Filtrer",
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]

            ]); //input
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'crsf_protection' => false, //pas besoin de crypting ou de securite
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; //evite d'avoir SEARCH marqué dans l'URL
    }
}
