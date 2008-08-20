<?php
/**
 * YAG - Yet Another Gallery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Yag
 * @package    Controllers
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 * @version
 */

require_once 'Zend/Controller/Action.php';

class AuthController extends Zend_Controller_Action
{
    /**
     * Logout action
     *
     */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
    }

    /**
     * Login action
     *
     */
    public function loginAction()
    {
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $this->_redirect($this->_helper->url->simple('index'));
        } else if ('' != $this->_request->getParam('openid_mode')) {
            return $this->_forward('verify');
        }

        $form = new LoginOpenIdForm();
        $form->setAction($this->_helper->url->simple('login'));
        $this->view->form = $form;

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData) || '' != $this->_request->getParam('openid_mode')) {
                $openIdIdentifier = $this->_request->getPost('openid_identifier');
                $openIdAdapter = new Zend_Auth_Adapter_OpenId($openIdIdentifier);
                $auth->authenticate($openIdAdapter);
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * verify action
     *
     */
    public function verifyAction()
    {
        $auth = Zend_Auth::getInstance();
        if ('' != $this->_request->getParam('openid_mode')) {
            $openIdAdapter = new Zend_Auth_Adapter_OpenId($openIdIdentifier);
            $result = $auth->authenticate($openIdAdapter);
            if ($result->isValid()) {
                $auth->getStorage()->write($result->getIdentity());

                $config = Zend_Registry::get('security-config');
                $this->_redirect($this->_helper->url->simple(
                    $config->redirect->action, 
                    $config->redirect->controller));
            } else {
                $auth->clearIdentity();
                $this->view->errorMessages = $result->getMessages();
                $this->render('login');
            }
        }
    }
}