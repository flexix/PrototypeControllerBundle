<?php

namespace Flexix\PrototypeControllerBundle\Controller;


use Flexix\PrototypeControllerBundle\Controller\PrototypeController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Entity controller.
 * 
 */
class EntityController extends PrototypeController {

    public function listAction(Request $request, $action,$applicationPath,$entitiesPath) {

        $driver = $this->getDriver($action,$applicationPath,$entitiesPath);

        $this->isActionAllowed($driver, $request);
        $entityClass = $driver->getEntityClass();
        $adapter = $this->getAdapter($driver);
        $entity = $this->createEntity($entityClass);
        $formTypeClass = $this->getFormTypeClass($driver, false);
        $form = null;

        if ($formTypeClass) {

            $form = $this->createForm($this->getFormTypeClass($driver), $entity, ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver)]);
            $form->handleRequest($request);
        }

        $result = $this->invokeModelMethod($driver, self::_LIST, [$driver->getEntityClass(), $request, $form], true);
        $adapter->setObject($result);
        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData(['driver' => $driver, 'is_xml_http_request' => $request->isXmlHttpRequest(),'is_sub_request' => (boolean)$this->requestStack->getParentRequest()]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    public function filterAction(Request $request, $action,$applicationPath,$entitiesPath) {

        $driver = $this->getDriver( $action,$applicationPath,$entitiesPath);
        $this->isActionAllowed($driver, $request);
        $entityClass = $driver->getEntityClass();
        $adapter = $this->getAdapter($driver);
        $entity = $this->createEntity($entityClass);
        $adapter->setObject($entity);
        $this->denyAccessUnlessGranted(self::_LIST, $this->getSecurityTicket($driver, $adapter->getData()));
        $form = $this->createForm($this->getFormTypeClass($driver), $entity, ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver)]);
        $result = $this->invokeModelMethod($driver, self::_LIST, [$entity], true);
        $form->handleRequest($request);
        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'form' => $form->createView(),
                            'is_xml_http_request' => $request->isXmlHttpRequest(),
                            'is_sub_request' => (boolean)$this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    public function newAction(Request $request, $action,$applicationPath,$entitiesPath) {

        $driver = $this->getDriver( $action,$applicationPath,$entitiesPath);
        $this->isActionAllowed($driver, $request);
        $entityClass = $driver->getEntityClass();
        $adapter = $this->getAdapter($driver);
        $entity = $this->createEntity($entityClass);
        $adapter->setObject($entity);
        $this->denyAccessUnlessGranted(self::_NEW, $this->getSecurityTicket($driver, $adapter->getData()));
        $form = $this->createForm($this->getFormTypeClass($driver), $entity, ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver)]);
        $result = $this->invokeModelMethod($driver, self::_NEW, [$entity], true);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //save
            $result = $this->invokeModelMethod($driver, self::_CREATE, [$entity, $request, $form]);
            $data['id'] = $adapter->getData()->getId();
            $this->addResultToData($driver, self::_CREATE, $data, $result, false, $this->getDataParameter($driver, self::_CREATE));

            if ($driver->shouldRedirect()) {

                $view = $this->redirectView($this->getUrlToRedirect($driver, $data), 301);
                return $this->handleView($view);
            }
        }

        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'form' => $form->createView(),
                            'is_xml_http_request' => $request->isXmlHttpRequest(),
                            'is_sub_request' => (boolean)$this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    /**
     * Finds and displays entity.
     *
     */
    public function getAction(Request $request, $action,$applicationPath,$entitiesPath, $id) {

        $driver = $this->getDriver($action,$applicationPath,$entitiesPath,$id);
        $this->isActionAllowed($driver, $request);
        $adapter = $this->getAdapter($driver);
        $entity = $this->invokeModelMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $request]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter);
        $this->denyAccessUnlessGranted(self::_GET, $this->getSecurityTicket($driver, $adapter->getData()));


        $deleteForm = $this->createDeleteForm($driver, $entity);

        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'delete_form' => $deleteForm->createView(),
                            'is_xml_http_request' => $request->isXmlHttpRequest(),
                            'is_sub_request' => (boolean)$this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    /**
     * Displays a form to edit an existing entity.
     *
     */
    public function editAction(Request $request, $action,$applicationPath,$entitiesPath, $id) {

        $driver = $this->getDriver( $action,$applicationPath,$entitiesPath,$id);
        $this->isActionAllowed($driver, $request);
        //find
        $adapter = $this->getAdapter($driver);
        $entity = $this->invokeModelMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $request]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter);
        $this->denyAccessUnlessGranted(self::_EDIT, $this->getSecurityTicket($driver, $adapter->getData()));


        $deleteForm = $this->createDeleteForm($driver, $entity);

        $form = $this->createForm($this->getFormTypeClass($driver), $entity, ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver, ['id' => $adapter->getData()->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = [];
            $result = $this->invokeModelMethod($driver, self::_UPDATE, [$entity, $request, $form]);
            $data['id'] = $adapter->getData()->getId();
            $this->addResultToData($driver, self::_UPDATE, $data, $result);

            if ($driver->shouldRedirect()) {

                $view = $this->redirectView($this->getUrlToRedirect($driver, [$data]), 301);
                return $this->handleView($view);
            }
        }
        $view = $this->view($entity, 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData([
                    'driver' => $driver,
                    'delete_form' => $deleteForm->createView(),
                    'form' => $form->createView(),
                    'is_xml_http_request' => $request->isXmlHttpRequest(),
                    'is_sub_request' => (boolean)$this->requestStack->getParentRequest()
                ])
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    /**
     * Deletes a product entity.
     *
     */
    public function deleteAction(Request $request, $action,$applicationPath,$entitiesPath, $id) {

        $driver = $this->getDriver( $action,$applicationPath,$entitiesPath,$id);
        $this->isActionAllowed($driver, $request);
        $adapter = $this->getAdapter($driver);
        $entity = $this->invokeModelMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $request]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter->getData());
        $this->denyAccessUnlessGranted(self::_DELETE, $this->getSecurityTicket($driver, $adapter->getData()));

        $form = $this->createDeleteForm($driver, $adapter->getData());
        $form->handleRequest($request);

        $data = [];

        if ($form->isValid()) {

            $result = $this->invokeModelMethod($driver, self::_DELETE, [$entity]);
            $this->addResultToData($driver, self::_UPDATE, $data, $result, false, $this->getDataParameter($driver, self::_DELETE));
        }

        $view = $this->redirectView($this->getUrlToRedirect($driver, $data), 301);
        return $this->handleView($view);
    }

    private function createDeleteForm($driver,$entity) {

        $url = $this->generateUrl('delete', [self::APPLICATION_PATH => $driver->getApplicationPath(), self::ENTITIES_PATH => $driver->getEntitiesPath(), "id" => $entity->getId()]);
        return $this->createFormBuilder()
                        ->setAction($url)
                        ->setMethod('DELETE')
                        ->getForm();
    }



}
