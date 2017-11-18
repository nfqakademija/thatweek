<?php

namespace AppBundle\Form;

use AppBundle\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', HiddenType::class, array('constraints' => new NotBlank(array(
                'message' => 'Pasirinkite dienas.')),
                'mapped' => false))
            ->add('endDate', HiddenType::class, array('mapped' => false))
            ->add('participants', HiddenType::class, array(
                'allow_extra_fields' => true,
                'mapped' => false,
                'constraints' => new NotBlank(array('message' => 'Pasirinkite bent vieną dalyvį.'))))
            ->add('submit', SubmitType::class, array('label' => 'Užsakyti'))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /**
                 * @var $order Order
                 */
                $order = $event->getData();
                $form = $event->getForm();

                if(!empty($order->getId()))
                {
                    $form->add('delete', SubmitType::class, array('label' => 'Ištrinti'));
                }
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
        return 'app_order';
    }
}