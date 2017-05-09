<?php

namespace Flexix\PrototypeControllerBundle\Util;

use Flexix\PrototypeControllerBundle\Util\DataAdapterInterface;

class DataAdapter implements DataAdapterInterface {

    protected $object;

    public function setObject($object) {

        $this->object = $object;
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

}
