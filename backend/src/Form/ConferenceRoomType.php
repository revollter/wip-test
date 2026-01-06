<?php

namespace App\Form;

use App\Entity\ConferenceRoom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ConferenceRoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Room name cannot be empty']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Room name cannot exceed {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 500,
                        'maxMessage' => 'Description cannot exceed {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('capacity', IntegerType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'Capacity is required']),
                    new Assert\Positive(['message' => 'Capacity must be a positive number']),
                ],
            ])
            ->add('location', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'Location cannot exceed {{ limit }} characters',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ConferenceRoom::class,
            'csrf_protection' => false,
        ]);
    }
}
