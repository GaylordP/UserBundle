<?php

namespace GaylordP\UserBundle;

use GaylordP\UserBundle\DependencyInjection\Compiler\UserNotificationFormatPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new UserCompilerPass());
        $container->addCompilerPass(new UserNotificationFormatPass());
    }
}
