<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file _LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    RSPM
 * @subpackage Admin_ConfigController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Allows the user to manage all application-wide configuration variables.
 *
 * @package    RSPM
 * @subpackage Admin_ConfigController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 */
class Admin_ConfigController extends Internal_Controller_Action  
{   
    /**
     * Shows all configurable options
     */
    public function indexAction()
    {
        $this->view->title = "Configuration Admin";
        
        $this->view->acl = array(
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
        );
        
        $userConfigFile = Zend_Registry::get('userConfigFile');
        $userConfig = Zend_Registry::get('userConfig');
                
        $uc = new Zend_Config_Xml($userConfigFile, 'production');
        
        $config = array();
        foreach ($uc as $key=>$data) {
            $tmp = array('key'=>$key, 'value'=>$data->value, 'description'=>$data->description);
            $config[] = $tmp;
        }
        
        $this->view->config = $config;

    }

    /**
     * Modifies a configuration variable
     *
     */
    public function editAction()
    {
        $this->view->title = "Edit Application Configuration";
        
        $userConfig = Zend_Registry::get('userConfig');
        $userConfigFile = Zend_Registry::get('userConfigFile');
        $uc = new Zend_Config_Xml($userConfigFile, 'production');
        
        if (!is_writable($userConfigFile)) {
            throw new Internal_Exception_Data('User Config File is not writable, therefore it cannot be edited');
        }
        
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');

            $data = array();
            foreach ($userConfig as $key=>$value) {            
                $data[$key] = htmlentities($filter->filter($post[$key]));
            }

            if (file_exists($userConfigFile)) {
                
                $xml = simplexml_load_file($userConfigFile);
             
            } else {
                throw new Internal_Exception_Data("Error reading user configuration file");
            }
            
            
            foreach ($data as $key=>$value) {
                $xml->production->$key->value = $data[$key];
            }
            
            $xmlStr = $xml->asXml();

            if (!file_put_contents($userConfigFile, $xmlStr, LOCK_EX)) {
                throw new Internal_Exception_Data("Error saving user configuration file to disk");
            }
            
            $this->_logger->setEventItem('attributeName', 'userConfig');
            $this->_logger->setEventItem('attributeId', '0');
            $this->_logger->info("User config was edited");
            
            $this->_redirect('admin/config/index/');
            
        } else {
            $config = array();
            foreach ($uc as $key=>$data) {
                $tmp = array('key'=>$key, 'value'=>$data->value, 'description'=>$data->description);
                $config[] = $tmp;
            }
                       
            $this->view->config = $config;
            
            $this->view->timezoneList = Timezone::getTimezoneList();
            
        }
    }
}