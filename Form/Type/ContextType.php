<?php

namespace Flexix\PrototypeControllerBundle\Form\Type;

use Flexix\PrototypeControllerBundle\Util\ContextInterface;
use Symfony\Component\Form\AbstractType;

class ContextType extends AbstractType {

    protected $context;
    protected $entityManager;

    public function __construct(ContextInterface $context, $entityManager) {
        $this->context = $context;
        $this->entityManager=$entityManager;
   
        
    }
    
}
