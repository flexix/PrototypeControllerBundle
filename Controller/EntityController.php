<?php

namespace Flexix\PrototypeControllerBundle\Controller;

use Flexix\PrototypeControllerBundle\Controller\PrototypeController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Entity controller.
 * 
 */
class EntityController extends PrototypeController {

    public function listAction(Request $request, $action, $module, $alias) {

        $driver = $this->getDriver($action, $module, $alias);
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
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'is_xml_http_request' => $request->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    public function filterAction(Request $request, $action, $module, $alias) {

        $driver = $this->getDriver($action, $module, $alias);
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
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    public function newAction(Request $request, $action, $module, $alias) {

        $driver = $this->getDriver($action, $module, $alias);
        $this->isActionAllowed($driver, $request);
        $entityClass = $driver->getEntityClass();
        $adapter = $this->getAdapter($driver);
        $entity = $this->createEntity($entityClass);
        $adapter->setObject($entity);
        $this->denyAccessUnlessGranted(self::_NEW, $this->getSecurityTicket($driver, $adapter->getData()));
        $form = $this->createForm($this->getFormTypeClass($driver), $entity, ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver)]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $result = $this->invokeModelMethod($driver, self::_CREATE, [$entity, $request, $form]);

            if ($driver->shouldRedirect()) {

                $view = $this->redirectView($this->getUrlToRedirect($driver, $adapter->getRedirectionData($result)), 301);
                return $this->handleView($view);
            }
        }

        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'form' => $form->createView(),
                            'is_xml_http_request' => $request->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    /**
     * Finds and displays entity.
     *
     */
    public function getAction(Request $request, $action, $module, $alias, $id) {

        $driver = $this->getDriver($action, $module, $alias, $id);
        $this->isActionAllowed($driver, $request);
        $adapter = $this->getAdapter($driver);
        $entity = $this->invokeModelMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $request]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter);
        $this->denyAccessUnlessGranted(self::_GET, $this->getSecurityTicket($driver, $adapter->getData()));

        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'delete_form' => $deleteForm->createView(),
                            'is_xml_http_request' => $request->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    /**
     * Displays a form to edit an existing entity.
     *
     */
    public function editAction(Request $request, $action, $module, $alias, $id) {

        $driver = $this->getDriver($action, $module, $alias, $id);
        $this->isActionAllowed($driver, $request);

        $adapter = $this->getAdapter($driver);
        $entity = $this->invokeModelMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $request]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter);
        $this->denyAccessUnlessGranted(self::_UPDATE, $this->getSecurityTicket($driver, $adapter->getData()));

        $form = $this->createForm($this->getFormTypeClass($driver), $entity, ['method' => $this->getFormMethod($driver), 'action' => $this->getFormActionUrl($driver, ['id' => $adapter->getData()->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $result = $this->invokeModelMethod($driver, self::_UPDATE, [$entity, $request, $form]);

            if ($driver->shouldRedirect()) {

                $view = $this->redirectView($this->getUrlToRedirect($driver, $adapter->getRedirectionData($result)), 301);
                return $this->handleView($view);
            }
        }
        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'form' => $form->createView(),
                            'is_xml_http_request' => $request->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());

        return $this->handleView($view);
    }

    /**
     * Deletes  entity.
     *
     */
    public function deleteAction(Request $request, $action, $module, $alias, $id) {

        $driver = $this->getDriver($action, $module, $alias, $id);
        $this->isActionAllowed($driver, $request);
        $adapter = $this->getAdapter($driver);
        $entity = $this->invokeModelMethod($driver, self::_GET, [$driver->getEntityClass(), $id, $request]);
        $adapter->setObject($entity);
        $this->checkEntityExists($driver, $adapter->getData());
        $this->denyAccessUnlessGranted(self::_DELETE, $this->getSecurityTicket($driver, $adapter->getData()));

        $form = $this->createFormBuilder()
                ->setAction($this->getFormActionUrl($driver, ['id' => $adapter->getData()->getId()]))
                ->setMethod('DELETE')
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $result = $this->invokeModelMethod($driver, self::_DELETE, [$entity]);
            $view = $this->redirectView($this->getUrlToRedirect($driver, $adapter->getRedirectionData($result)), 301);
            return $this->handleView($view);
        }

        $view = $this->view($adapter->getData(), 200)
                ->setTemplateVar($this->getTemplateVar($driver))
                ->setTemplateData($adapter->getTemplateData([
                            'driver' => $driver,
                            'form' => $form->createView(),
                            'is_xml_http_request' => $request->isXmlHttpRequest(),
                            'is_sub_request' => (boolean) $this->requestStack->getParentRequest()
                ]))
                ->setTemplate($driver->getTemplate());
    }

}
