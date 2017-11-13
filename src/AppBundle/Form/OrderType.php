<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', HiddenType::class)
            ->add('endDate', HiddenType::class)
            ->add('participants', HiddenType::class, array('allow_extra_fields' => true, 'mapped' => false))
            ->add('submit', SubmitType::class, array('label' => 'Užsakyti'))
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $order = $event->getData();

                $date = new \DateTime();
                $order->setStartDate($date->setTimestamp(floor($order->getStartDate() / 1000)));
                $date = new \DateTime();
                $order->setEndDate($date->setTimestamp(floor($order->getEndDate() / 1000)));
                $event->setData($order);
        ;
    });

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
       // return 'appbundle_order';
        return 'app_calendar';
    }
}