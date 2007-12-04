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
 * @package    Classmate
 * @subpackage Profile_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Allows the user to set a customized profile linked to their user ID.
 *
 * @package    Classmate
 * @subpackage Profile_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Profile_IndexController extends Internal_Controller_Action 
{
	/**
	 * Shows all users in the system
	 *
	 */
	public function indexAction()
	{
		$get    = Zend_Registry::get('get');
		$filter = Zend_Registry::get('inputFilter');
		$config = Zend_Registry::get('config');
		
		$editable = false;
		if (isset($get['userId'])) {
			$userId = $filter->filter($get['userId']);
		} else {
			$userId = Zend_Auth::getInstance()->getIdentity();
			$editable = true;
		}
		
		if ($this->_acl->isAllowed($this->_role, $this->_resource, 'editAllProfiles')) {
			$editable = true;
		}
        		
		$this->view->acl = array(
		    'edit' => $editable
	    );
	    
        $displayUserId = preg_replace('/@.*$/', '', $userId);
        $realm         = preg_replace('/^[^@]*@/', '', $userId);	    
                
        $adapter = $config->authentication->$realm->toArray();

        $a = new $adapter['class'];
            
        $this->view->adapter = array(
           'realm'       => $realm,
           'name'        => $adapter['name'],
           'description' => $adapter['description'],
        );

        if (!$a->autoLogin()) {
            $au = $a->getUser($userId);
            
            $this->view->email = $au[0]['email'];
        }
        
		$profile = new Profile();		
		$up = $profile->find($userId);
		
		if (is_null($up)) {
			throw new Internal_Exception_Input('No profile exists for this user');
		}
        
        $this->view->profile       = $up->toArray();
        $this->view->displayUserId = $displayUserId;
        $this->view->title         = "Details for " . $displayUserId;
        $this->view->types         = $config->profileTypes->toArray();
        		
		$ca = new CustomAttribute();
		$this->view->custom = $ca->getData('User_Profile', $userId, 'display');
	}
	
	/**
	 * allows a user to edit their profile
	 *
	 */
    public function editAction()
    {
        $filter = Zend_Registry::get('inputFilter');
        $config = Zend_Registry::get('config');
        
        $ca = new CustomAttribute();
        $profile = new Profile();

        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');

            if (isset($post['userId'])) {
                if ($this->_acl->isAllowed($this->_role, $this->_resource, 'editAllProfiles')) {
                    $userId = $filter->filter($post['userId']); 
                } else {
                    $userId = Zend_Auth::getInstance()->getIdentity();
                }
            } else {
                $userId = Zend_Auth::getInstance()->getIdentity();
            }            
            
            $data = array(
                'userId'    => $userId,
                'firstName' => $filter->filter($post['firstName']),
                'lastName'  => $filter->filter($post['lastName']),
                'type'      => $filter->filter($post['type']),
            );
            
            $thisProfile = $profile->find($data['userId']);
            if (is_null($thisProfile)) {
            	throw new Internal_Exception_Data('Profile not found');
            }
            
            $thisProfile = $thisProfile->toArray();
            
            if ($_FILES['pic']['name'] != '') {

                 $image = new Image;

                 $image->resizeImage($filter->filter($_FILES['pic']['tmp_name']), 110, 110);

                 $iData = array(
                    'source' => file_get_contents($filter->filter($_FILES['pic']['tmp_name'])),
                    'alt'    => $data['userId'],
                    'contentType' => $filter->filter($_FILES['pic']['type']),
                    'name'        => $filter->filter($_FILES['pic']['name']),
                    );


                 if (isset($thisProfile['picImageId']) && $thisProfile['picImageId'] != 0) {
                     $image->deleteImage($thisProfile['picImageId']);
                 }

                 $image->insert($iData);

                 $data['picImageId'] = $image->getAdapter()->lastInsertId();
            }            
            
            $profile->update($data, null);
            
            $custom = $post['custom'];
            
            foreach ($custom as &$c) {
                $c = $filter->filter($c);
            }
            
            $ca->saveData('User_Profile', $userId, $custom);
            
            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $data['userId']);
            $this->_logger->info('User changed profile');             
            
            $this->_redirect('/profile/?userId=' . $userId);
            
                
        } else {
        
	        $get = Zend_Registry::get('get');
	        
	        if (isset($get['userId'])) {
	        	if ($this->_acl->isAllowed($this->_role, $this->_resource, 'editAllProfiles')) {
                    $userId = $filter->filter($get['userId']); 
	        	} else {
	        		$userId = Zend_Auth::getInstance()->getIdentity();
	        	}
	        } else {
	            $userId = Zend_Auth::getInstance()->getIdentity();
	        }
	        
	        $this->view->acl = array('editable' => true);
	        
	        $displayUserId = preg_replace('/@.*$/', '', $userId);
	        $realm         = preg_replace('/^[^@]*@/', '', $userId);        
	                
	        $adapter = $config->authentication->$realm->toArray();
	
	        $a = new $adapter['class'];
	            
	        $this->view->adapter = array(
	           'realm'       => $realm,
	           'name'        => $adapter['name'],
	           'description' => $adapter['description'],
	        );
	
	        if (!$a->autoLogin()) {
	            $au = $a->getUser($userId);
	            
	            $this->view->email = $au[0]['email'];
	        }

	        $up = $profile->find($userId);
	        
	        if (is_null($up)) {
	            throw new Internal_Exception_Input('No profile exists for this user');
	        }
        
	        $this->view->types         = $config->profileTypes->toArray();
	        $this->view->profile       = $up->toArray();
	        $this->view->displayUserId = $displayUserId;
	        $this->view->title         = "Details for " . $displayUserId;
	                
	        $ca = new CustomAttribute();
	        $this->view->custom = $ca->getData('User_Profile', $userId, 'form');
        }
    }	
    
    /**
     * allows users to edit anyones profiles
     *
     */
    public function editAllProfilesAction()
    {
    }
	
}
?>