<?php

namespace Kunstmaan\HealthBundle;

use Kunstmaan\HealthBundle\DependencyInjection\Compiler\KunstmaanHealthCompilerPass;
use Kunstmaan\HealthBundle\DependencyInjection\Compiler\KunstmaanHealthConfigurationPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanHealthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
//        $container->addCompilerPass(new KunstmaanHealthConfigurationPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new KunstmaanHealthCompilerPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}
