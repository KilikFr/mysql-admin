<?php

namespace App\Form;

use App\Entity\Cluster;
use App\Entity\Server;
use App\Repository\ServerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClusterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cluster = $builder->getData();
        
        $builder->add(
            'name',
            TextType::class,
            [
                'required' => true,
                'label' => 'field.name.label',
            ]
        );

        $builder->add(
            'servers',
            EntityType::class,
            [
                'class' => Server::class,
                'query_builder' => function (ServerRepository $repository) use ($cluster) {
                    $qb = $repository->createQueryBuilder('server')
                        ->orderBy('server.name', 'ASC')
                        ->where('server.cluster IS NULL');
                    if ($cluster->getId() !== null) {
                        $qb->orWhere('server.cluster = :cluster')
                            ->setParameter('cluster', $cluster);   
                    }
                    
                    return $qb;
                },
                'required' => false,
                'attr' => [
                    'class' => 'select2 servers',
                ],
                'multiple' => true,
                'by_reference' => false,
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
                'data_class' => Cluster::class,
                'labels' => [],
            ]
        );
    }
}
