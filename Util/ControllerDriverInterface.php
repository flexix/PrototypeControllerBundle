<?php

namespace Flexix\PrototypeControllerBundle\Util;


interface ControllerDriverInterface {

  
    public function getActionAllowed();

    public function getEntityClass(); 
    
    public function getApplicationPath();
    
    public function getEntitiesPath(); 
    
    public function returnResultToView($modelName);
    
    public function getResultParameter($modelName);
    
    public function shouldRedirect();
        
    public function getRedirectionRoute();
    
    public function getRedirectionRouteParameters();
    
    public function getService($name);
    
    public function hasService($name);

    public function getFormTypeClass();
    
    public function getFormAction();

    public function getTemplate(); 

    
}
