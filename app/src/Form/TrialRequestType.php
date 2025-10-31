<?php

namespace App\Form;

use App\Entity\TrialRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as T;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrialRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', T\TextType::class, ['label'=>'Prénom'])
        ->add('lastName',  T\TextType::class, ['label'=>'Nom'])
        ->add('email',     T\EmailType::class, ['label'=>'Email'])
        ->add('phone',     T\TelType::class,   ['label'=>'Téléphone'])
        ->add('goal', T\ChoiceType::class, [
            'label'=>'Objectif principal',
            'placeholder'=>'Choisir…',
            'required'=>false,
            'choices'=>[
                'Remise en forme'=>'Remise en forme',
                'Perte de poids'=>'Perte de poids',
                'Prise de muscle'=>'Prise de muscle',
                'Performance'=>'Performance',
                'Découverte'=>'Découverte',
            ],
        ])
        ->add('class', T\ChoiceType::class, [
            'label'=>'Cours souhaité',
            'placeholder'=>'Je ne sais pas encore',
            'required'=>false,
            'choices'=>[
                'HIIT'=>'HIIT',
                'Functional Training'=>'Functional Training',
                'Cardioland'=>'Cardioland',
                'Souplesse / Yoga / Pilates'=>'Souplesse / Yoga / Pilates',
                'TRX'=>'TRX',
                'Jump / Trampoline'=>'Jump / Trampoline',
            ],
        ])
        ->add('message', T\TextareaType::class, [
            'label'=>'Message (facultatif)', 'required'=>false, 'attr'=>['rows'=>4]
        ])
        ->add('send', T\SubmitType::class, [
            'label'=>"Envoyer ma demande",
            'attr'=>['class'=>'btn btn-primary btn-lg']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TrialRequest::class]);
    }
}
