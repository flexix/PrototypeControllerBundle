<?php

namespace Flexix\PrototypeControllerBundle\Tests\Util;

use Flexix\PrototypeControllerBundle\Util\Ticket;
use Symfony\Component\Yaml\Yaml;
use Flexix\PrototypeControllerBundle\Util\ControllerDriver;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfiguration;
use Flexix\MapperBundle\Util\ApplicationMapper;
use Flexix\PrototypeControllerBundle\Sample\SampleLogger;
use Flexix\PrototypeControllerBundle\Controller\PrototypeController;
use Flexix\MapperBundle\Util\EntityMapper;
use Flexix\ConfigurationBundle\Util\Configuration;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfigurationFactory;
use Flexix\RequestAnalyzerBundle\Util\RequestAnalyzer;
use Symfony\Component\HttpFoundation\Request;
use \PHPUnit\Framework\TestCase;

/**
 * 
 * php  app/phpunit.phar   --bootstrap=./app/autoload.php ./src/Flexix/PrototypeBundle/Tests/Util/TicketTest.php
 * @author Mariusz Piela <mariuszpiela@tmsolution.pl>,TomDyg
 */
class TicketTest extends TestCase {

    const _ALIAS = 'payment-frequency';
    const _applicationPath = 'admin/some/other/path';
    const _entitiesPath = 'discount/2/measure-unit/3/payment-frequency';
    const _ID = '7';
    const _MODEL_NAME = 'get';

    static protected $ticket;
    static protected $mapperConfiguration;
    static protected $prototypeConfiguration;
    static protected $developerConfiguration;

    public static function setupBeforeClass() {
        self::$ticket = new Ticket();
    }

    protected function getDriver() {

        $configuration = Yaml::parse(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testConfiguration.yml'));
        $controllerConfiguration = new ControllerConfiguration($configuration['flexix_prototype_controller']['base']); 
        return new ControllerDriver($controllerConfiguration);
    }

    
    public function testGetDriver() {
        $controllerDriver = $this->getDriver();
        self::$ticket->setDriver($controllerDriver);
        $this->assertEquals($controllerDriver, self::$ticket->getDriver());
    }

    public function testGetObject() {
        $driverObject = $this->getDriver();
        self::$ticket->setObject($driverObject);
        $this->assertEquals($driverObject, self::$ticket->getDriver());
    }

    public function testSetObject() {
        $driverObject = $this->getDriver();
        self::$ticket->setObject($driverObject);
        $this->assertEquals($driverObject, self::$ticket->getDriver());
    }

    public function testSetDriver() {
        $controllerDriver = $this->getDriver();
        self::$ticket->setDriver($controllerDriver);
        $this->assertEquals($controllerDriver, self::$ticket->getDriver());
    }

}
