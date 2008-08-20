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
 */

class Yag_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // TODO: Move to constructor?
        $config = Zend_Registry::get('security-config');
        $authConfig = $config->auth;

        if (false === Zend_Auth::getInstance()->hasIdentity())
        {
	        foreach ($authConfig->areas->toArray() as $controller => $actions) {
	            if ($controller == $this->_request->getControllerName() && 
	                in_array($this->_request->getActionName(), $actions)
	            ) {
	                $request->setControllerName($authConfig->controller);
	                $request->setActionName($authConfig->action);
	            }
	        }
        }
    }
}
