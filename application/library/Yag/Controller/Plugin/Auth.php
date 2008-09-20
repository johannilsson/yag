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
    /**
     * Configuration
     *
     * @var Zend_Config
     */
    private $config = array();

    /**
     * Constructor
     *
     * @param array $securedAreas
     */
    public function __construct(Zend_Config $config)
    {
        $this->config = $config;
    }
    
    /**
     * Will alter the request and point to a login action if the requested
     * action is configured as private.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (false === Zend_Auth::getInstance()->hasIdentity()) {
	        foreach ($this->config->auth->securedAreas->toArray() as $controller => $actions) {
	            if ($controller == $this->_request->getControllerName() && 
	                in_array($this->_request->getActionName(), $actions)
	            ) {
	                $request->setControllerName($this->config->auth->login->controller);
	                $request->setActionName($this->config->auth->login->action);
	            }
	        }
        }
    }
}
