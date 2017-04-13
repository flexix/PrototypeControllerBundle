<?php

namespace Flexix\PrototypeControllerBundle\Controller;

use Flexix\PrototypeControllerBundle\Controller\PrototypeController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfigurationFactoryInterface;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfiguration;
use Flexix\PrototypeControllerBundle\Util\ControllerDriver;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Entity controller.
 * 
 */
class TestController extends PrototypeController {

    public function testAction(Request $request) {
        $view = $this->view(null, 200)
                ->setTemplateData([
                    'is_xml_http_request' => $request->isXmlHttpRequest(),
                    'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ])
                ->setTemplate('menuitem\index.html.twig');

        return $this->handleView($view);
    }

}
