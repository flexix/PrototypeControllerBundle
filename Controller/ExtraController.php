<?php

namespace Flexix\PrototypeControllerBundle\Controller;

use Flexix\PrototypeControllerBundle\Controller\PrototypeController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Entity controller.
 * 
 */
class ExtraController extends PrototypeController {

    /**
     * Finds and displays entity.
     *
     */
    public function tabsAction(Request $request, $action, $module, $alias, $id) {
        
        $driver = $this->getDriver($action, $module, $alias, $id);
        $this->isActionAllowed($driver, $request);
        $adapter = $this->getAdapter($driver);
        $adapter->setRequest($request);
        $entity = $this->invokeServiceMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $adapter->getRequest()]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter);
        $this->denyAccessUnlessGranted(self::_GET, $this->getSecurityTicket($driver, $adapter->getData()));

        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'is_xml_http_request' => $adapter->getRequest()->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

   
}
