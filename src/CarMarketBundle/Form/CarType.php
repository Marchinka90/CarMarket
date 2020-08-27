<?php

namespace CarMarketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('make', TextType::class)
            ->add('model', TextType::class)
            ->add('year', IntegerType::class)
            ->add('power', TextType::class)
            ->add('travelledDistance', IntegerType::class)
            ->add('color', TextType::class)
            ->add('city', TextType::class)
            ->add('image', TextType::class)
            ->add('note', TextType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CarMarketBundle\Entity\Car'
        ));
    }




}
