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

/*
 * Set include path
 */
$includePath = array(
	dirname(__FILE__) . '/library',
	dirname(__FILE__) . '/models', 
	dirname(__FILE__) . '/forms',
	dirname(__FILE__) . '/views/helpers',
	get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $includePath));

define('PUBLIC_PATH', dirname(__FILE__) . '/../public');

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

/*
 * Load configs
 */
$config = new Zend_Config_Ini(dirname(__FILE__) . '/configuration/atom.ini', 'production');
Zend_Registry::set('atom-config', $config);
$authConfig = new Zend_Config_Ini(dirname(__FILE__) . '/configuration/auth.ini', 'production');
Zend_Registry::set('auth-config', $authConfig);
$authIdentitiesConfig = new Zend_Config_Ini(dirname(__FILE__) . '/configuration/auth-identities.ini', 'production');
Zend_Registry::set('auth-identities-config', $authIdentitiesConfig);
/*
 * Date settings
 */
date_default_timezone_set('UTC');

$partial = '_search_pagination_control.phtml';
Zend_View_Helper_PaginationControl::setDefaultViewPartial($partial);
Zend_Paginator::setDefaultScrollingStyle('Sliding');

//$routeConfig = new Zend_Config_Ini(dirname(__FILE__) . '/configuration/routes.ini', 'production');
//$yagConfig = new Zend_Config_Ini(dirname(__FILE__) . '/configuration/yag.ini', 'production');
$dsConfig = new Zend_Config_Ini(dirname(__FILE__) . '/configuration/db.ini', 'production');

$db = Zend_Db::factory($dsConfig->db);
Zend_Db_Table_Abstract::setDefaultAdapter($db);
Zend_Registry::set('db', $db);


$layoutOptions = array(
    'layout'     => 'standard',
    'layoutPath' => dirname(__FILE__) . '/views/layouts',
);
$layout = Zend_Layout::startMvc($layoutOptions);

$front = Zend_Controller_Front::getInstance();

$router = $front->getRouter();

$route = new Zend_Controller_Router_Route(
    'photo/',
    array(
        'controller' => 'photo',
        'action'     => 'index'
    )
);
$router->addRoute('photos', $route);

$route = new Zend_Controller_Router_Route(
    'photo/:id/',
    array(
        'controller' => 'photo',
        'action'     => 'show'
    ),
    array('id' => '\d+')
);
$router->addRoute('photo', $route);

$route = new Zend_Controller_Router_Route(
    'tags/show/:name/',
    array(
        'controller' => 'tags',
        'action'     => 'show'
    )
);
$router->addRoute('tags-name', $route);

//$router->addConfig($routeConfig, 'routes');

/*
 * Error handling
 */
$front->setParam('noErrorHandler', true);
$front->throwExceptions(true);
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('error_log', dirname(__FILE__) . '../logs/php_error_log');

/*
 * Controller plugins
 */
$front->registerPlugin(new Yag_Controller_Plugin_Auth($authConfig));

/*
 * And run
 */
$front->run(dirname(__FILE__) . '/controllers');

