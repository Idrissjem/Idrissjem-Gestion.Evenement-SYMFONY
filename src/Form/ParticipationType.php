<?php
namespace App\Form;

use App\Entity\Participation;
use App\Entity\User; 
use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('tel')
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control my-date-class'], // Ajout de la classe form-control pour Bootstrap
                'label' => 'Date de l\'événement',
            ])
                ->add('user', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => function(User $user) {
                        return $user->getName(); // Assurez-vous d'avoir une méthode getName() dans votre entité User
                    },
                    'label' => 'Utilisateur',
                    'attr' => ['class' => 'form-control'], // Bootstrap styling
                ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => function(Event $event) {
                    return $event->getNom(); // Assurez-vous d'avoir une méthode getNom() dans votre entité Event
                },
                'label' => 'Événement',
                'attr' => ['class' => 'form-control'], // Bootstrap styling
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
