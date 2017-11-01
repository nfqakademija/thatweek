<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, array('label' => 'Vardas'))
            ->add('lastName', TextType::class, array('label' => 'Pavardė'))
            ->add('age', TextType::class, array('label' => 'Amžius'))
            ->add('gender', ChoiceType::class, array(
                'label' => 'Lytis',
                'choices' => array(
                    '' => '',
                    'vyras' => 'm',
                    'moteris' => 'f'
                )
            ))
            ->add('submit', SubmitType::class, array('label' => 'Pridėti'))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Participant'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_participant';
    }


}
