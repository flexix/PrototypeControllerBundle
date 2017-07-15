<?php

namespace Flexix\PrototypeControllerBundle\Util;

interface DataAdapterInterface
{
    public function setDriver($driver); 
    public function setObject($object);    
    public function getData();
    public function getTemplateData($templateData); 
    public function getRedirectionData($templateData); 
    public function getFormActionParameters($urlParameters); 
} 