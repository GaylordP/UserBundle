<?php

namespace GaylordP\UserBundle;

use GaylordP\UserBundle\DependencyInjection\Compiler\NotificationFormatPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new UserCompilerPass());
        $container->addCompilerPass(new NotificationFormatPass());
    }
}
