<?php

namespace App\Form;

use App\Entity\ConferenceRoom;
use App\Entity\Reservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('conferenceRoom', EntityType::class, [
                'class' => ConferenceRoom::class,
                'choice_value' => 'id',
                'constraints' => [
                    new Assert\NotNull(['message' => 'Conference room is required']),
                ],
            ])
            ->add('reserverName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Reserver name cannot be empty']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Reserver name cannot exceed {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotNull(['message' => 'Reservation date is required']),
                ],
            ])
            ->add('startTime', TimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotNull(['message' => 'Start time is required']),
                ],
            ])
            ->add('endTime', TimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotNull(['message' => 'End time is required']),
                ],
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 500,
                        'maxMessage' => 'Notes cannot exceed {{ limit }} characters',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'csrf_protection' => false,
        ]);
    }
}
