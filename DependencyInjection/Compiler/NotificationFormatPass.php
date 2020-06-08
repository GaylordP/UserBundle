<?php

namespace GaylordP\UserBundle\DependencyInjection\Compiler;

use GaylordP\UserBundle\NotificationFormat\NotificationFormat;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NotificationFormatPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(NotificationFormat::class);

        foreach ($container->findTaggedServiceIds('place_locator') as $id => $tags) {
            $definition->addMethodCall('addNotificationFormat', [new Reference($id)]);
        }
    }
}
