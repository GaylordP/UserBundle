<?php

namespace GaylordP\UserBundle\DependencyInjection\Compiler;

use GaylordP\UserBundle\UserNotificationFormat\UserNotificationFormat;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UserNotificationFormatPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(UserNotificationFormat::class);

        foreach ($container->findTaggedServiceIds('user.notification_format') as $id => $tags) {
            $definition->addMethodCall('addUserNotificationFormat', [new Reference($id)]);
        }
    }
}
