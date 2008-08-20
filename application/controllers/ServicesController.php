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
 * Service Controller
 *
 */
class ServicesController extends Zend_Controller_Action 
{
    /**
     * Nokia Service
     *
     * Content-Type: application/isf.sharing.config
     * 
     */
    public function nokiaAction()
    {
        $layout = $this->_helper->getHelper('layout');
        $layout->direct()->disableLayout();

        $this->_response->setHeader('Content-Type', 'application/isf.sharing.config', true);
    }
} 