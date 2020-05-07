<?php

namespace GaylordP\UserBundle;

use Doctrine\ORM\EntityManagerInterface;
use GaylordP\UserBundle\Repository\Filter\DeletedAtFilter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class UserCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /*
         * Voir mon POST Sur
         * Stackoverflow
        $container->getDefinition('doctrine.orm.default_configuration')
            ->addMethodCall('addFilter', [
                'deleted_at',
                DeletedAtFilter::class,
            ])
            ->addMethodCall('getFilterClassName', [
                'deleted_at',
            ])
        ;

        $def = $container->findDefinition('doctrine.orm.default_entity_manager');

        $def->addMethodCall('getFilters', [], true);
        $def->addMethodCall('enable', [
            'deleted_at',
        ]);
        */
    }
}
