<?php

namespace Flexix\PrototypeControllerBundle\Tests\Util;

use Symfony\Component\Yaml\Yaml;
use Flexix\PrototypeControllerBundle\Util\ControllerDriver;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfiguration;
use Flexix\MapperBundle\Util\ApplicationMapper;
use Flexix\PrototypeControllerBundle\Sample\SampleLogger;
use Flexix\PrototypeControllerBundle\Controller\PrototypeController;
use Flexix\MapperBundle\Util\EntityMapper;
use Flexix\ConfigurationBundle\Util\Configuration;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfigurationFactory;
use Flexix\PathAnalyzerBundle\Util\PathAnalyzer;
use Symfony\Component\HttpFoundation\Request;
use \PHPUnit\Framework\TestCase;

/**
 * 
 * php  app/phpunit.phar   --bootstrap=./app/autoload.php ./src/Flexix/PrototypeBundle/Tests/Util/ControllerDriverTest.php
 * @author Mariusz Piela <mariuszpiela@tmsolution.pl>,Tomasz Dyguda <tomasz.dyguda@gmail.com> 
 */
class ControllerDriverTest extends TestCase {

    const _ALIAS = 'payment-frequency';
    const _module = 'admin/some/other/path';
    const _alias = 'discount/2/measure-unit/3/payment-frequency';
    const _ID = '7';
    const _MODEL_NAME = 'get';

    static protected $expected_entity_name = 'Flexix\EntityAnalyzerBundle\Entity\PaymentFrequency';
    static protected $expected_module = 'admin/some/other/path';
    static protected $expected_alias = 'discount/2/measure-unit/3/payment-frequency';
    static protected $expected_result_parameter = 'productCategory';
    static protected $expected_form_type = 'Flexix\WizardBundle\Form\ViewTypeType';
    static protected $expected_form_action = 'delete';
    static protected $expected_widget_template = 'viewtype\delete.html.twig';
    static protected $expected_model_array = array(
        'name' => 'some_model',
        'method' => 'findOneById',
        'return_result_to_view' => true,
        'result_parameter' => 'productCategory',
        'type' => 'service'
    );
    static protected $expected_route_name = 'list';
    static protected $mapperConfiguration;
    static protected $prototypeConfiguration;
    static protected $developerConfiguration;
    static protected $controllerDriver;
    static protected $request;

    public static function setupBeforeClass() {

        $configuration = Yaml::parse(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testConfiguration.yml'));
        $controllerConfiguration = new ControllerConfiguration($configuration['flexix_prototype_controller']['base']);
        self::$controllerDriver = new ControllerDriver($controllerConfiguration);

        self::$request = new Request(['id' => self::_ID], [], [
            'module' => self::_module,
            'alias' => self::_alias
        ]);
        self::$mapperConfiguration = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testMapper.yml');
        self::$prototypeConfiguration = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testPrototypeConfiguration.yml');
        self::$developerConfiguration = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testDeveloperConfiguration.yml');
    }

    protected function getDeleteActionConfiguration() {
        $configuration = Yaml::parse(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testConfiguration.yml'));
        $controllerConfiguration = new ControllerConfiguration($configuration['flexix_prototype_controller']['actions']['delete']);
        return new ControllerDriver($controllerConfiguration);
    }

    protected function getDriver() {

        $mapperConfiguration = Yaml::parse(self::$mapperConfiguration);

        $appliactionMapper = new ApplicationMapper($mapperConfiguration['flexix_mapper']['applications']);

        $entityMapper = new EntityMapper($mapperConfiguration['flexix_mapper']['entities']);

        $requestAnalyzer = new PathAnalyzer($appliactionMapper, $entityMapper);

        $prototypeConfiguration = Yaml::parse(self::$prototypeConfiguration);
        $developerConfiguration = Yaml::parse(self::$developerConfiguration);

        $baseConfiguration = new Configuration($prototypeConfiguration['flexix_prototype_controller']);
        $configurationFactory = new ControllerConfigurationFactory($baseConfiguration, $requestAnalyzer);
        $developerConfiguration = new Configuration($developerConfiguration['flexix_prototype_controller']);
        $configurationFactory->addConfiguration($developerConfiguration, self::_module, self::_ALIAS);
        $controllerConfiguration = new ControllerConfiguration($configurationFactory->createConfiguration(new ControllerConfiguration(), 'new', self::_module, self::_alias,self::_ID));
        return new ControllerDriver($controllerConfiguration);
    }

    public function testGetActionAllowed() {

        $this->assertTrue(self::$controllerDriver->getActionAllowed());
    }

    public function testGetEntityClass() {

        $controllerDriver = $this->getDriver();
        $this->assertEquals($controllerDriver->getEntityClass(), self::$expected_entity_name);
    }

    public function testGetApplicationPath() {

        $controllerDriver = $this->getDriver();
        $applicationPath = $controllerDriver->getApplicationPath();
        $this->assertEquals($applicationPath, self::$expected_module);
    }

    public function testGetEntitiesPath() {

        $controllerDriver = $this->getDriver();
        $entitiesPath = $controllerDriver->getEntitiesPath();
        $this->assertEquals($entitiesPath, self::$expected_alias);
    }

    public function testReturnResultToView() {

        $this->assertTrue(self::$controllerDriver->returnResultToView(self::_MODEL_NAME));
    }

    public function testGetResultParameter() {

        $this->assertEquals(self::$controllerDriver->getResultParameter(self::_MODEL_NAME), self::$expected_result_parameter);
    }

    public function testShouldRedirect() {
        $controllerDriver = $this->getDeleteActionConfiguration();
        $this->assertTrue($controllerDriver->shouldRedirect());
    }

    public function testGetRedirectionRouteParameters() {

        $controllerDriver = $this->getDeleteActionConfiguration();
        $this->assertEquals($controllerDriver->getRedirectionRouteParameters(), []);
    }

    public function testGetModel() {

        $model = self::$controllerDriver->getModel(self::_MODEL_NAME);
        $this->assertEquals($model, self::$expected_model_array);
    }

    public function testHasModel() {
        $this->assertTrue(self::$controllerDriver->hasModel(self::_MODEL_NAME));
    }

    public function testGetFormTypeClass() {
        $controllerDriver = $this->getDeleteActionConfiguration();
        $this->assertEquals($controllerDriver->getFormTypeClass(), self::$expected_form_type);  
    }

    public function testGetFormAction() {
        $controllerDriver = $this->getDeleteActionConfiguration();
        $this->assertEquals($controllerDriver->getFormAction(), self::$expected_form_action);
    }

    public function testGetTemplate() { 
        $controllerDriver = $this->getDeleteActionConfiguration();
        $this->assertEquals($controllerDriver->getTemplate(), self::$expected_widget_template);
    }

}
