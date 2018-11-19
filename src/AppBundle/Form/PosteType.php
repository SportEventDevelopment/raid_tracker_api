<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class PosteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idPoint')
            ->add('type', TextType::class)
            ->add('nombre', IntegerType::class)
            ->add('heureDebut', TimeType::class, array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'format' => 'HH:mm'
            ))
            ->add('heureFin', TimeType::class, array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'format' => 'HH:mm'
            ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Poste',
            "csrf_protection" => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_poste';
    }


}
