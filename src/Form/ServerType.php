<?php

namespace App\Form;

use App\Entity\Cluster;
use App\Entity\Server;
use App\Repository\ClusterRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            'cluster',
            EntityType::class,
            [
                'class' => Cluster::class,
                'query_builder' => function (ClusterRepository $repository) {
                    return $repository->createQueryBuilder('cluster')
                        ->orderBy('cluster.name', 'ASC');
                },
                'required' => false,
                'attr' => [
                    'class' => 'select2 clusters',
                ],
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
            NumberType::class,
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
            'password-edit',
            HiddenType::class,
            [
                'mapped' => false,
                'data' => null,
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
                'data_class' => Server::class,
                'labels' => [],
            ]
        );
    }
}
