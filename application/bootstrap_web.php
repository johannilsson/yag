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
 * @copyright  Copyright (c) 2008 Johan Nilsson. (http://www.markupartist.com)
 * @license    New BSD License
 */

require_once dirname(__FILE__) . '/bootstrap.php';

/*
 * View settings
 */
Zend_View_Helper_PaginationControl::setDefaultViewPartial('_search_pagination_control.phtml');
Zend_Paginator::setDefaultScrollingStyle('Sliding');

$layout = Zend_Layout::startMvc(array(
    'layout'     => 'standard',
    'layoutPath' => APPLICATION_PATH . '/views/layouts',
));

$view = $layout->getView();
$view->addHelperPath('Yag/View/Helper/', 'Yag_View_Helper');

/*
 * Set up the front controller 
 */
$front = Zend_Controller_Front::getInstance();

$front->setParam('noErrorHandler', ENVIRONMENT == 'development');
$front->throwExceptions(ENVIRONMENT == 'development');

$front->getRouter()->addConfig($routeConfig, 'routes');

$front->registerPlugin(new Yag_Controller_Plugin_Auth($authConfig));

$front->run(APPLICATION_PATH . '/controllers');

