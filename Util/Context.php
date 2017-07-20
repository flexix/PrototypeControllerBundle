<?php

namespace Flexix\PrototypeControllerBundle\Util;

use Flexix\PrototypeControllerBundle\Util\ContextInterface;

class Context implements ContextInterface {

    protected $requestStack;
    protected $context;

    public function __construct($requestStack) {
        
        $this->requestStack = $requestStack;
        $request = $this->requestStack->getCurrentRequest();
        $this->context = $request->query->get('context');
    }

    public function get($name) {
        
        if (is_array($this->context) && array_key_exists($name, $this->context)) {
            return $this->context[$name];
        } else {
            return false;
        }
    }
    
    public function getContextArray()
    {
        return $this->context;
        
    }
    

}
