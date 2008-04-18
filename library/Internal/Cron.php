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
 * @package    Internal_Cron_Setup
 * @category   Cron Job
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Cron.php 163 2007-07-24 15:04:54Z gplocke@EOS.NCSU.EDU $
 */

/**
 * Sets up the cron jobs in a native environment so that they have access to all
 * models, just as the application does.
 *
 * @package    Internal_Cron_Setup
 * @category   Cron Job
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
final class Internal_Cron
{
    /**
     * Sets up the cron job, mapping include paths to the correct offset.
     *
     * @param string $offset
     */
    public static function setup($offset = './')
    {
        $offset = preg_replace('/\/$/', '', $offset);

		$dbConfig = array();
		
		require_once $offset . '/application/appConfig.php';
		
		error_reporting(E_ALL|E_STRICT);
		
		set_include_path('.'
		                  . PATH_SEPARATOR . $offset . '/library'
		                  . PATH_SEPARATOR . $offset . '/application/models/'
		                  . PATH_SEPARATOR . get_include_path());
		
		require_once 'Zend/Loader.php';
		Zend_Loader::registerAutoload();
				
		//load configuration
		$config = new Zend_Config_Xml($offset . '/application/config.xml', 'production');
		Zend_Registry::set('config', $config);
		
		date_default_timezone_set($config->timezone);
		
		$userConfig = new Zend_Config_Xml($offset . '/library/Internal/Config/userConfig.xml', 'production');
		Zend_Registry::set('userConfigFile', $config->userConfigFile);
		Zend_Registry::set('userConfig', $userConfig->toArray());
		
		$db = Zend_Db::factory($dbConfig['adapter'], $dbConfig);
		Zend_Db_Table::setDefaultAdapter($db);
		Zend_Registry::set('dbAdapter', $db);

        // Setup Logger
        $writer = new Zend_Log_Writer_Db($db, 'tbl_log');

        $logger = new Zend_Log($writer);
        $logger->setEventItem('userId', 'cron');
        $logger->setEventItem('role', 'cron');
        $logger->setEventItem('sid', session_id());
        $logger->setEventItem('timestamp', time());
        $logger->setEventItem('request', $_SERVER['REQUEST_URI']);

        Zend_Registry::set('logger', $logger);        
    }

    /**
     * handles any errors from cron jobs
     *
     * @param mixed $e
     */
    public static function error($e)
    {
        if (is_array($e)) {
            $e = implode(' - ', $e);
        }

        trigger_error($e, E_USER_ERROR);
    }
}