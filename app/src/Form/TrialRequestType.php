<?php

namespace App\Form;

use App\Entity\TrialRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as T;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrialRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', T\TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Ce champ est requis.'])]
            ])
            ->add('lastName',  T\TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Ce champ est requis.'])]
            ])
            ->add('email',     T\EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Ce champ est requis.'])]
            ])
            ->add('phone',     T\TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Ce champ est requis.'])]
            ])
            ->add('goal', T\ChoiceType::class, [
                'label'       => 'Objectif principal',
                'placeholder' => 'Choisir…',
                'required'    => false,
                'choices'     => [
                    'Remise en forme' => 'Remise en forme',
                    'Perte de poids'  => 'Perte de poids',
                    'Prise de muscle' => 'Prise de muscle',
                    'Performance'     => 'Performance',
                    'Découverte'      => 'Découverte',
                ],
            ])
            ->add('trialDate', DateType::class, [
                'label'       => 'Date souhaitée',
                'widget'      => 'single_text',
                'html5'       => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Merci de choisir une date.']),
                ],
                'attr'        => [
                    'autocomplete' => 'off',
                    'class'        => 'js-trial-date',
                ],
            ])
            
            // Champ caché mappé (JSON du créneau), validé par Symfony
            ->add('trialSlot', HiddenType::class, [
                'required' => true,
                'attr' => [
                                'class' => 'js-trial-slot-hidden'
                            ]
            ])
            ->add('message', T\TextareaType::class, [
                'label'    => 'Message',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => "Merci d'écrire un message."]),
                ],
                'attr'     => ['rows' => 4],
            ])
            ->add('send', T\SubmitType::class, [
                'label' => "Envoyer ma demande",
                'attr'  => ['class' => 'btn btn-primary btn-lg'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TrialRequest::class]);
    }
}
