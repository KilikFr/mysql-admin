<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'required' => true,
                'label' => 'field.name.label',
            ]
        );
        $builder->add(
            'description',
            TextType::class,
            [
                'required' => false,
                'label' => 'field.description.label',
            ]
        );
        $builder->add(
            'host',
            TextType::class,
            [
                'required' => true,
                'label' => 'field.host.label',
            ]
        );
        $builder->add(
            'port',
            TextType::class,
            [
                'required' => true,
                'label' => 'field.port.label',
            ]
        );
        $builder->add(
            'user',
            TextType::class,
            [
                'required' => false,
                'label' => 'field.user.label',
            ]
        );
        $builder->add(
            'password',
            TextType::class,
            [
                'required' => false,
                'label' => 'field.password.label',
                'attr' => ['readonly' => true],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'App\Entity\Server',
                'labels' => [],
            ]
        );
    }
}
