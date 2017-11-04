<?php
/**
 * Created by PhpStorm.
 * User: martynas
 * Date: 17.11.3
 * Time: 11.11
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('week', HiddenType::class, array('allow_extra_fields' => true, 'mapped' => false))
            ->add('participants', HiddenType::class, array('allow_extra_fields' => true, 'mapped' => false))
            ->add('submit', SubmitType::class, array('label' => 'Užsakyti'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Order'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_order';
    }
}