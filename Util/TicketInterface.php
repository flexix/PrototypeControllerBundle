<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexix\PrototypeControllerBundle\Util;

/**
 * Description of Ticket
 *
 * @author Mariusz Piela <mariuszpiela@tmsolution.pl>
 */
interface TicketInterface {
   
    public function getDriver();
    public function getObject();

}
