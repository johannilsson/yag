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

/**
 * Auth controller
 * 
 * 
 */
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
            $config = Zend_Registry::get('auth-config');
            $this->_redirect($this->_helper->url->simple(
                $config->auth->login->welcome->action, 
                $config->auth->login->welcome->controller));
        } else if ('' != $this->_request->getParam('openid_mode')) {
            return $this->_forward('verify');
        }

        $form = new LoginOpenIdForm();
        $form->setAction($this->_helper->url->simple('login'));
        $this->view->form = $form;

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                $openIdIdentifier = $this->_request->getPost('openid_identifier');
                $openIdAdapter = new Zend_Auth_Adapter_OpenId($openIdIdentifier);
                $auth->authenticate($openIdAdapter); // will redirect to the open id provider
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * verify login action
     *
     */
    public function verifyAction()
    {
        $auth = Zend_Auth::getInstance();
        if ('' != $this->_request->getParam('openid_mode')) {
            $openIdAdapter = new Zend_Auth_Adapter_OpenId();
            $result = $auth->authenticate($openIdAdapter);
            $config = Zend_Registry::get('auth-config');
            $identities = Zend_Registry::get('auth-identities-config');
            // TODO: fix proper error message for this.
            if ($result->isValid() && in_array($result->getIdentity(), $identities->auth->identities->toArray())) {
                $auth->getStorage()->write($result->getIdentity());
                $this->_redirect($this->_helper->url->simple(
                    $config->auth->login->welcome->action, 
                    $config->auth->login->welcome->controller));
            } else {
                $auth->clearIdentity();
                $this->view->errorMessages = $result->getMessages(); // change to use flash
                $this->_redirect($this->_helper->url->simple('login'));
            }
        }
    }
}
