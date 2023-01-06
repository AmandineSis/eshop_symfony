<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class,[
                'disabled'=> true,
                'label'=>'Prénom'
            ])
            ->add('lastname', TextType::class,[
                'disabled'=> true,
                'label'=>'Nom'
            ])
            ->add('email', EmailType::class,[
                'disabled'=> true,
                'label'=>'Email'
            ])
            ->add('old_password', PasswordType::class, [
                'mapped'=> false,
                'label' => "Mot de passe actuel",
                'attr' => ['
                placeholder'=> 'Veuillez saisir votre mot depasse actuel']
            ])
            ->add('new_password', RepeatedType::class,[
                'mapped'=> false,
                'type'=> PasswordType::class,
                'invalid_message'=>'les mots de passe doivent être identiques',
                'label'=>'Mon nouveau mot de passe',
                'required'=>true,
                'first_options'=>[
                    'label'=> 'Nouveau mot de passe',
                    'attr'=>['placeholder'=>'Entrez un nouveau mot de passe']
                ],
                'second_options'=>[
                    'label'=>'Confirmez le mot de passe',
                    'attr'=>['placeholder'=>'Confirmez le mot de passe']
                    ]
            ]) //input
            ->add('submit', SubmitType::class,[
                'label'=>"Mettre à jour",
                
            ]) //input
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
