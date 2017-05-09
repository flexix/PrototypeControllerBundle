<?php

namespace Flexix\PrototypeControllerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ConfigurationPass implements CompilerPassInterface {

    const SERVICE_NAME = 'flexix_prototype_controller.configuration_factory';
    const METHOD_NAME = 'addConfiguration';
    const TAG_ID = 'flexix_prototype_controller.controller_configuration';
    const MODULE = 'module';
    const ALIAS = 'alias';
    const ACTION = 'action';

    public function process(ContainerBuilder $container) {

        $taggedServices = $container->findTaggedServiceIds(self::TAG_ID);
        $this->findTaggedServices($taggedServices, $this->getDefinition($container));
    }

    protected function getDefinition(ContainerBuilder $container) {

        if (!$container->hasDefinition(self::SERVICE_NAME)) {
            return;
        }

        return $container->getDefinition(self::SERVICE_NAME);
    }

    protected function findTaggedServices($taggedServices, $definition) {

        
        foreach ($taggedServices as $id => $tags) {
            $this->addServices($id, $tags, $definition);
        }
    }

    protected function addServices($id, $tags, $definition) {

        foreach ($tags as $attributes) {

            if (!array_key_exists(self::MODULE, $attributes)) {

                $attributes[self::MODULE]=null;
            }
            
            if (!array_key_exists(self::ALIAS, $attributes)) {

                throw new \Exception(sprintf('There is no "%s" parameter for "%s" named service', self::ALIAS, $id));
            }           
            
//            if (!array_key_exists(self::ACTION, $attributes)) {
//
//                throw new \Exception(sprintf('There is no "%s" parameter for "%s" named service', self::ACTION, $id));
//            }
            

            $definition->addMethodCall(self::METHOD_NAME, array(
                new Reference($id),
                /*$attributes[self::ACTION],*/ $attributes[self::ALIAS],$attributes[self::MODULE]
            ));
        }
    }

}
