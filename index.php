<?php
/**
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    
 * @category   Main Bootstrap
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id:$
 */

$dbConfig     = array();

require_once('./application/appConfig.php');

error_reporting(E_ALL|E_STRICT);

set_include_path('.'
                  . PATH_SEPARATOR . './library'
                  . PATH_SEPARATOR . './application/models/'
                  . PATH_SEPARATOR . get_include_path());

$baseUrl = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/index.php'));

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$inputFilter = new Zend_Filter();
$inputFilter->addFilter(new Zend_Filter_StripTags())
            ->addFilter(new Zend_Filter_StringTrim());

Zend_Registry::set('inputFilter', $inputFilter);

$allowedTags = array('b','i','em','strong','u','img','li','ul','ol','p','a',
    'span','font','h1','h2','h3','h4','h5','div','table','tr','td','th','tbody','thead','tfoot');
            
$allowedAttributes = array('href','id','class','face','size','src','target',
    'border','align','color','name','style','alt','width','height','hspace',
    'vspace', 'cell-spacing', 'cell-padding', 'valign');
            
$htmlFilter = new Zend_Filter();
$htmlFilter->addFilter(new Zend_Filter_StripTags($allowedTags, $allowedAttributes));
$htmlFilter->addFilter(new Zend_Filter_StringTrim());

Zend_Registry::set('htmlFilter', $htmlFilter);

Zend_Registry::set('sitePrefix', $baseUrl);

//register input filters
Zend_Registry::set('post', $_POST);
Zend_Registry::set('get', $_GET);

//load configuration
$config = new Zend_Config_Xml('./application/config.xml', 'production');
Zend_Registry::set('config', $config);

date_default_timezone_set($config->timezone);

$userConfig = new Zend_Config_Xml('./library/Internal/Config/userConfig.xml', 'production');
Zend_Registry::set('userConfigFile', $config->userConfigFile);
Zend_Registry::set('userConfig', $userConfig->toArray());

$db = Zend_Db::factory($dbConfig['adapter'], $dbConfig);
Zend_Db_Table::setDefaultAdapter($db);
Zend_Registry::set('dbAdapter', $db);

// Get the current instance of Zend_Auth
$auth = Zend_Auth::getInstance();
$auth->setStorage(new Ot_Auth_Storage_Session($_SERVER['SERVER_NAME'] . $baseUrl . 'auth'));

// Setup gcLogger
$writer = new Zend_Log_Writer_Db($db, 'tbl_log');

$logger = new Zend_Log($writer);
if (!is_null($auth->getIdentity())) {
    $logger->setEventItem('userId', $auth->getIdentity());
    $logger->setEventItem('role', Ot_Authz::getInstance()->getRole());
    $logger->setEventItem('sid', session_id());
    $logger->setEventItem('timestamp', time());
    $logger->setEventItem('request', str_replace($baseUrl, '', $_SERVER['REQUEST_URI']));
}

Zend_Registry::set('logger', $logger);

// register the view we are going to use
$view = new Internal_View_Smarty();
$view->sitePrefix = $baseUrl;
$view->copyrightDate = date('Y');
$view->date = date("l, F d, Y");
$view->appTitle = $config->branding->appTitle;
$view->config = $config->display->toArray();
$view->userConfig = $userConfig->toArray();
$view->addScriptPath('./application/views/scripts/');


$branch = explode('/', str_replace($view->sitePrefix . '/', '', $_SERVER['REQUEST_URI']));

$front = Zend_Controller_Front::getInstance();

$vr = new Zend_Controller_Action_Helper_ViewRenderer();
$vr->setView($view);
$vr->setViewSuffix('tpl');
Zend_Controller_Action_HelperBroker::addHelper($vr);

$front->setBaseUrl($baseUrl)
      ->addModuleDirectory('./application/modules')
      ->setRouter(new Zend_Controller_Router_Rewrite())
      ->setDispatcher(new Zend_Controller_Dispatcher_Standard())
      ;

// Create new instance of the ACL
$acl = new Internal_Acl();
      
$front->setParam('acl', $acl)
      ->registerPlugin(new Internal_Plugin_Auth($auth, $acl))
      ->registerPlugin(new Internal_Plugin_View())
      ->registerPlugin(new Internal_Plugin_Htmlheader())
      ->registerPlugin(new Zend_Controller_Plugin_ErrorHandler())
      ;

try {
    $front->dispatch();
} catch (Exception $e) {
	$req = new Zend_Session_Namespace('request');
	$req->uri = '';
}
