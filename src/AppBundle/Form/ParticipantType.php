<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class ParticipantType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, array('label' => 'Vardas', 'attr' => array(
                'data-parsley-required' => 'true',
                'data-parsley-pattern' => '[A-Ž a-ž]+'
            )))
            ->add('lastName', TextType::class, array('label' => 'Pavardė', 'attr' => array(
                'data-parsley-required' => 'true',
                'data-parsley-pattern' => '[A-Ž a-ž]+'
            )))
            ->add('age', TextType::class, array('label' => 'Amžius', 'attr' => array(
                'data-parsley-range' => '[5, 16]'
            )))
            ->add('gender', ChoiceType::class, array(
                'label' => 'Lytis',
                'choices' => array(
                    '' => '',
                    'vyras' => 'm',
                    'moteris' => 'f'
                )
            ))
            ->add('submit', ButtonType::class, array('label' => 'Pridėti'))
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
