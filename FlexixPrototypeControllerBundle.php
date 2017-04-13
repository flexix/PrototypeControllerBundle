<?php

namespace Flexix\PrototypeControllerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Flexix\PrototypeControllerBundle\DependencyInjection\Compiler\ConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FlexixPrototypeControllerBundle extends Bundle
{
   
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigurationPass());
    }
    
}
