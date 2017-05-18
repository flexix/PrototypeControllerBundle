<?php

namespace Flexix\PrototypeControllerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfigurationFactoryInterface;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfiguration;
use Flexix\PrototypeControllerBundle\Util\ControllerDriver;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Prototype controller.
 * 
 */
class PrototypeController extends FOSRestController {

    const _LIST = 'list';
    const _NEW = 'new';
    const _CREATE = 'create';
    const _GET = 'get';
    const _EDIT = 'edit';
    const _UPDATE = 'update';
    const _DELETE = 'delete';
    const MODULE = 'module';
    const ALIAS = 'alias';

    protected $configurationFactory;
    protected $requestStack;
    protected $defaultAdapter;

    public function __construct(ContainerInterface $container, ControllerConfigurationFactoryInterface $configurationFactory, $requestStack, $defaultAdapter) {
        $this->container = $container;
        $this->configurationFactory = $configurationFactory;
        $this->requestStack = $requestStack;
        $this->defaultAdapter = $defaultAdapter;
    }

    protected function getDriver($action, $module, $alias, $id = null) {
        $driver = $this->createConfiguration($action, $alias, $module, $id);
        return new ControllerDriver($driver);
    }

    protected function createConfiguration($action, $module, $alias, $id = null) {
        return $this->configurationFactory->createConfiguration(new ControllerConfiguration(), $action, $module, $alias, $id);
    }

    protected function getServiceObject($model) {

        if ($model['type'] == 'class') {
            $className = $model['name'];
            $object = new $className;
        } else if ($model['type'] == 'service') {
            $object = $this->get($model['name']);
        }

        return $object;
    }

    protected function invokeServiceMethod($driver, $serviceName, $arguments = [], $optional = false) {


        if ($driver->hasService($serviceName)) {
            $service = $driver->getService($serviceName);
            $object = $this->getServiceObject($service);
            $arguments[] = $driver;
            return call_user_func_array(array($object, $service['method']), $arguments);
        } else {

            if (!$optional) {
                throw new \Exception(sprintf("Service: \"%s\" doesn\'t exists", $serviceName));
            }
        }
    }

    protected function createEntity($entityClass) {
        return new $entityClass;
    }

    protected function isActionAllowed($driver, $request) {

        if ($this->checkActionAllowed($driver, $request)) {
            return true;
        } else {

            throw new NotFoundHttpException('Action not allowed');
        }
    }

    protected function checkActionAllowed($driver, $request) {

        $actionAllowed = $driver->getActionAllowed();
        dump($actionAllowed);

        if (is_array($actionAllowed)) {

            if (in_array('xhttp', $actionAllowed)) {

                if ($request->isXmlHttpRequest()) {
                    return true;
                }
            }

            if (in_array('subrequest', $actionAllowed)) {

                if ($this->requestStack->getParentRequest()) {
                    return true;
                }
            }
        } else if ($actionAllowed == true) {
            return true;
        }


        return false;
    }

    protected function isRequestTypeAllowed($driver, $request) {

        if (!$this->isXHTTP($driver, $request) && !$this->isInner($driver, $request)) {
            throw new NotFoundHttpException('Action not allowed');
        }
    }

    protected function getDataParameter($driver, $model) {
        if ($driver->hasServiceDataParameter($model)) {
            return $driver->getServiceDataParameter($model);
        }
    }

    protected function addResultToData($driver, $modelName, &$data, $result, $required = false, $dataParameter = null) {
        if ($driver->hasService($modelName)) {
            $resultParameter = $driver->getResultParameter($modelName);
            if ($resultParameter) {

                if ($dataParameter) {
                    if (is_array($result) && array_key_exists($dataParameter, $result)) {
                        $data[$resultParameter] = $result[$dataParameter];
                    }
                } else {

                    $data[$resultParameter] = $result;
                }
            } else {

                if ($required) {
                    throw new \Exception('Service "%s" must result parameter. Set paremter name in config - result_parameter', $modelName);
                }
            }
        }

        return $data;
    }

    protected function getSecurityTicket($driver, $object) {
        $ticket = $this->get('flexix_prototype_controller.ticket');
        $ticket->setDriver($driver);
        $ticket->setObject($object);
        return $ticket;
    }

    protected function getRedirectionRouteParameters($driver, $data) {

        $definedParameters = $driver->getRedirectionRouteParameters();
        $resultParameters = [];
        if ($definedParameters) {
            foreach ($definedParameters as $parameter) {
                if (array_key_exists($parameter, $data)) {
                    $resultParameters[$parameter] = $data[$parameter];
                } else {
                    throw new \Exception(sprintf('Parameter %s for action needed, check configuration of your action', $parameter));
                }
            }
        }

        return $resultParameters;
    }

    protected function getUrlToRedirect($driver, $data) {


        $urlParameters = [self::MODULE => $driver->getModule(), self::ALIAS => $driver->getAlias()];
        $redirectionParameters = $this->getRedirectionRouteParameters($driver, $data);
        $parameters = array_merge($urlParameters, $redirectionParameters);

        return $this->generateUrl($driver->getRedirectionRoute(), $parameters);
    }

    protected function getFormActionParameters($formAction) {

        if (is_array($formAction)) {

            if (!array_key_exists('route_name', $formAction)) {
                throw new \Exception('Parameter "route_name" for form "action" doesn\'t exists ');
            }

            if (array_key_exists('parameters', $formAction)) {
                return $formAction['parameters'];
            }
        }
        return [];
    }

    protected function getFormMethod($driver) {
        if ($driver->hasFormMethod()) {
            return $driver->getFormMethod();
        } else {
            return 'POST';
        }
    }

    protected function getFormActionUrl($driver, $extraParameters = []) {

        $formAction = $driver->getFormAction();
        $urlParameters = [self::MODULE => $driver->getModule(), self::ALIAS => $driver->getAlias()];

        if ($formAction) {
            $urlParameters = array_merge($urlParameters, $this->getFormActionParameters($formAction), $extraParameters);
            $uri = $this->generateUrl($formAction, $urlParameters);
        }

        return $uri;
    }

    protected function checkEntityExists($driver, $adapter) {

        $entityClass = $driver->getEntityClass();
        if (!$adapter->getData() || !($adapter->getData() instanceof $entityClass )) {
            throw new \Exception('Entity doesn\'t exists');
        }
    }

    protected function getFormTypeClass($driver, $required = true) {
        if ($driver->hasFormTypeClass()) {

            return $driver->getFormTypeClass();
        } else {
            if ($required == true) {
                throw new \Exception('There is no form_type in configuration');
            }
        }
    }

    protected function getTemplateVar($driver) {
        if ($driver->getTemplateVar()) {

            return $driver->getTemplateVar();
        }
        return 'data';
    }

    protected function getAdapter($driver) {

        $adapter = $driver->getAdapter();
        if ($adapter) {
            return $this->get($adapter);
        } else {
            return $this->defaultAdapter;
        }
    }

}
