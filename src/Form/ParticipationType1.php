<?php

namespace App\Form;

use App\Entity\Participation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationType1 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('tel')
            ->add('date', DateType::class, [
                'widget' => 'single_text', // Utilise un élément input de type date, permettant aux navigateurs de fournir un sélecteur de date
                'attr' => ['class' => 'my-date-class'], // Classe CSS personnalisée si nécessaire
                'label' => 'Date de l\'événement',
                // autres options...
            ])
            ->add('user', EntityType::class, [
                'label' => 'Utilisateur',
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getName();                 },
               
            ])
          
            // ->add('event')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
