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
 
class IndexController extends Zend_Controller_Action 
{
	/**
	 * Index action
	 *
	 */
	public function indexAction() 
	{
        $this->_redirect($this->_helper->url('list', 'photos'));
	}

} 
