<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexix\PrototypeControllerBundle\Util;

use Flexix\PrototypeControllerBundle\Util\TicketInterface;
use Flexix\PrototypeControllerBundle\Util\ControllerDriverInterface;

/**
 * Description of Ticket
 *
 * @author Mariusz Piela <mariuszpiela@tmsolution.pl>
 */
class Ticket implements  TicketInterface {
   
    protected $driver;
    protected $object;

    public function getDriver() {
        return $this->driver;
    }

    public function getObject() {
        return $this->object;
    }

    public function setDriver(ControllerDriverInterface $driver) {
        $this->driver = $driver;
    }

    public function setObject($object) {
        $this->object = $object;
    }


    
}
