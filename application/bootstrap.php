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
	get_include_path(),
	dirname(__FILE__) . '/library',
	dirname(__FILE__) . '/models', 
	dirname(__FILE__) . '/forms',
	dirname(__FILE__) . '/views/helpers',
);
set_include_path(implode(PATH_SEPARATOR, $includePath));

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

/*
 * Set up defines
 */
define('PUBLIC_PATH', dirname(__FILE__) . '/../public');
define('APPLICATION_PATH', dirname(__FILE__));

/*
 * Date settings
 */
date_default_timezone_set('UTC');

/*
 * Load configs
 */
$environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'development';

$atomConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configuration/atom.ini', $environment);
$authConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configuration/auth.ini', $environment);
$authIdentitiesConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configuration/auth-identities.ini', $environment);
$routeConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configuration/routes.ini', $environment);
$dsConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configuration/db.ini', $environment);

Zend_Registry::set('atom-config', $atomConfig);
Zend_Registry::set('auth-config', $authConfig);
Zend_Registry::set('auth-identities-config', $authIdentitiesConfig);

$db = Zend_Db::factory($dsConfig->db);
Zend_Db_Table_Abstract::setDefaultAdapter($db);
Zend_Registry::set('db', $db);

/*
 * Error handling
 */
error_reporting(E_ALL);
ini_set('display_errors', ENVIRONMENT == 'development');
ini_set('error_log', dirname(__FILE__) . '../logs/php_error_log');

/*
 * View settings
 */

Zend_View_Helper_PaginationControl::setDefaultViewPartial('_search_pagination_control.phtml');
Zend_Paginator::setDefaultScrollingStyle('Sliding');

$layoutOptions = array(
    'layout'     => 'standard',
    'layoutPath' => dirname(__FILE__) . '/views/layouts',
);
$layout = Zend_Layout::startMvc($layoutOptions);

/*
 * Set up the fron controller 
 */
$front = Zend_Controller_Front::getInstance();

$front->setParam('noErrorHandler', ENVIRONMENT == 'development');
$front->throwExceptions(ENVIRONMENT == 'development');

$front->getRouter()->addConfig($routeConfig, 'routes');

$front->registerPlugin(new Yag_Controller_Plugin_Auth($authConfig));

$front->run(dirname(__FILE__) . '/controllers');

