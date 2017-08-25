<?php

namespace Flexix\PrototypeControllerBundle\Util;

use Flexix\PrototypeControllerBundle\Util\DataAdapterInterface;

class DataAdapter implements DataAdapterInterface {

    protected $object;
    protected $driver;
    protected $request;
    
    
   
    public function setDriver($driver)
    {
        $this->driver=$driver;
    } 
    
    
    
    public function setRequest($request)
    {
        $this->request=$request;
    }
    
    
    
    public function getRequest()
    {
       return $this->request;
    }
    
    public function setObject($object) {

        $this->object = $object;
    }
    
    public function getObject() {
      
        return $this->object;
    }

    public function getData() {
      
        return $this->object;
    }

    public function getTemplateData($templateData) {
        return $templateData;
    }
    
    public function getRedirectionData($redirectionData) {
        
        return  ["id",$this->object->getId()];
    }
    
    
    public function getFormActionParameters($parameters)
    {
        
        return $parameters;
    } 

}
