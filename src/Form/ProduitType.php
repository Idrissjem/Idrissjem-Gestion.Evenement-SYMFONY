<?php
namespace App\Form;

use App\Entity\Produit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom du produit'
            ])
            ->add('prix', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Prix'
            ])
            /*->add('marque', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Marque'
            ])
            */
            // ->add('marque', ChoiceType::class, [
            //     'choices' => [
            //         'Veuillez sélectionner la marque' => '',
            //         'Audi' => 'audi',
            //         'Mercedes-Benz' => 'mercedes',
            //         'BMW' => 'bmw',
            //         'Porsche' => 'porsche',
            //         'Lamborghini' => 'lamborghini',
            //         'Ferrari' => 'ferrari',
            //         'Tesla' => 'tesla',
            //         'Ford' => 'ford',
            //         'Chevrolet' => 'chevrolet',
            //         'Toyota' => 'toyota',
            //         'Honda' => 'honda',
            //         'Nissan' => 'nissan',
            //     ],
            //     'label' => 'Marque',
            //     'attr' => ['class' => 'form-control'],
            // ])
            ->add('image', FileType::class, [
                'label' => 'Image du Produit (JPG, JPEG, PNG file)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*'
                ],
            ])
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => function(User $user) {
            //         return $user->getName();
            //     },
            //     'label' => 'Assigné à l\'utilisateur',
            //     'attr' => ['class' => 'form-control'],
            // ])
            ->add('category')

            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mt-3'],
                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
