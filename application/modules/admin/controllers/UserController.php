<?php
/**
 * Aerial
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
 * @package    Aerial (Admin)
 * @subpackage Admin_UserController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: UserController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Controller to show all users who have access to the application
 *
 * @package    Aerial (Admin)
 * @subpackage Admin_UserController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Admin_UserController extends Internal_Controller_Action 
{
    /**
     * Authz adapter
     *
     * @var Itdcs_Authz_Adapter
     */
    protected $_authzAdapter = null;

    /**
     * Runs when the class is initialized.  Sets up the view instance and the
     * various models used in the class.
     *
     */
    public function init()
    {
        $config = Zend_Registry::get('config');

        $this->_authzAdapter = new $config->authorization(Zend_Auth::getInstance()->getIdentity());
        
        parent::init();
    }


    /**
     * Displays all users with access to the system
     *
     */
    public function indexAction()
    {
        $users = $this->_authzAdapter->getUsers();

        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            'log'    => $this->_acl->isAllowed($this->_role, 'admin_log', 'index'),
        );

        if (count($users) != 0) {
            $this->view->javascript = 'sortable.js';
        }

        $config = Zend_Registry::get('config');
        $this->view->realms = $config->authentication->toArray();
        
        $this->view->title = "Manage Users";
        $this->view->users = $users;
    }

    /**
     * Adds a user to the system
     *
     */
    public function addAction()
    {
        if (!$this->_authzAdapter->manageLocally()) {
            throw new Internal_Exception_Data(
                'The authorization adapter provided is using an external source ' .
                'to manage user lists, meaning this application can not manage ' .
                'the lists locally.');
        }

        $config = Zend_Registry::get('config');
        
        $roles = $this->_acl->getAvailableRoles();

        $temp = array();
        foreach ($roles as $r) {
            $temp[$r['name']] = $r['name'];
        }

        $roles = $temp;

        if ($this->_request->isPost()) {

            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');

            $uf = new Zend_Filter();
            $uf->addFilter(new Zend_Filter_Alnum());
            $uf->addFilter(new Zend_Filter_StringTrim());
            $uf->addFilter(new Zend_Filter_StripTags());
                        
            $userId = $uf->filter($post['userId']);
            $role   = $filter->filter($post['role']);
            $realm  = $filter->filter($post['realm']);
            
            $authAdapter = new $config->authentication->$realm->class();
            
            if ($userId == '') {
                throw new Internal_Exception_Input('No valid user ID was entered');
            }
            
            $userId .= '@' . $realm;
            
            if (!in_array($role, $roles)) {
                throw new Internal_Exception_Input("The role '$role' is not a valid role");
            }            
            
            if ($authAdapter->manageLocally()) {
            	$email = $filter->filter($post['email']);
            	
	            if ($email == '') {
	                throw new Internal_Exception_Input('No email address was entered');
	            }
	            
	            $ev = new Zend_Validate_EmailAddress();
	            if (!$ev->isValid($email)) {
	                throw new Internal_Exception_Input('Email address is not valid');
	            }
	            
                $user = $authAdapter->getUser($userId);
            
	            if (count($user) != 0) {
	                throw new Internal_Exception_Input('User ID is taken.  Please select a different ID');
	            }
	            
	            $password = $authAdapter->addAccount($userId, '', $email);	    

                $trigger = new EmailTrigger();
                $trigger->password = $password;
                $trigger->userId = $userId;
                $trigger->role = $role;
                $trigger->dispatch('Admin_User_Add');	            
            }        
            
            $this->_authzAdapter->addUser($userId, $role);

            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $userId);
            $this->_logger->info('Account was added for ' . $userId . '.');

            $this->_redirect('/admin/user');            

        } 
        
        $adapters = $config->authentication->toArray();
        
        $auth = array();
        foreach ($adapters as $key => $value) {
            $a = new $value['class'];
            
            $auth[] = array(
               'realm'       => $key,
               'name'        => $value['name'],
               'description' => $value['description'],
               'autoLogin'  => $a->autoLogin(),
               'signup'     => $a->allowUserSignUp(),
            );
        }
        
        $this->view->authAdapters = $auth;
                
        $this->view->title = 'Add User';
        $this->view->roles = $roles;

    }

    /**
     * Edits an existing user
     *
     */
    public function editAction()
    {
        if (!$this->_authzAdapter->manageLocally()) {
            throw new Internal_Exception_Data(
                'The authorization adapter provided is using an external source ' .
                'to manage user lists, meaning this application can not manage ' .
                'the lists locally.');
        }

        $config = Zend_Registry::get('config');
        
        $roles = $this->_acl->getAvailableRoles();

        $temp = array();
        foreach ($roles as $r) {
            $temp[$r['name']] = $r['name'];
        }

        $roles = $temp;

        $filter = Zend_Registry::get('inputFilter');

        if ($this->_request->isPost()) {

            $post = Zend_Registry::get('post');
                        
            $userId = $filter->filter($post['userId']);
            $role   = $filter->filter($post['role']);

            if (!in_array($role, $roles)) {
                throw new Internal_Exception_Input("The role '$role' is not a valid role");
            }

            if ($userId == '') {
                throw new Internal_Exception_Input('User ID is required');
            }
            
            $realm = preg_replace('/^[^@]*@/', '', $userId);
            
            $authAdapter = new $config->authentication->$realm->class();

            if ($authAdapter->manageLocally()) {
                $email = $filter->filter($post['email']);
                                           
                if ($email == '') {
                    throw new Internal_Exception_Input('No email address was entered');
                }
                                
            	$ev = new Zend_Validate_EmailAddress();
                if (!$ev->isValid($email)) {
                    throw new Internal_Exception_Input('Email address is not valid');
                }            	
            	
            	$authAdapter->editAccount($userId, '', $email);
            }
                
            $this->_authzAdapter->editUser($userId, $role);

            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $userId);
            $this->_logger->info('Account was modified for ' . $userId . '.');

            $this->_redirect('/admin/user');

        }

        $get = Zend_Registry::get('get');
        $userId = $filter->filter($get['userId']);
        $user = $this->_authzAdapter->getUser($userId);
        
        $realm = preg_replace('/^[^@]*@/', '', $userId);
        
        
        $adapter = $config->authentication->$realm->toArray();

        $a = new $adapter['class'];
            
        $this->view->adapter = array(
           'realm'       => $realm,
           'name'        => $adapter['name'],
           'description' => $adapter['description'],
           'autoLogin'   => $a->autoLogin(),
           'signup'      => $a->allowUserSignUp(),
        );

        if (!$a->autoLogin()) {
        	$au = $a->getUser($userId);
        	
        	$this->view->email = $au[0]['email'];
        }
        
        $this->view->userId = $userId;
        $this->view->displayUserId = preg_replace('/@.*$/', '', $userId);;
        $this->view->role   = $user['role'];
        $this->view->title  = 'Edit User';
        $this->view->roles  = $roles;

    }

    /**
     * Deletes a user
     *
     */
    public function deleteAction()
    {
        if (!$this->_authzAdapter->manageLocally()) {
            throw new Internal_Exception_Data(
                'The authorization adapter provided is using an external source ' .
                'to manage user lists, meaning this application can not manage ' .
                'the lists locally.');
        }
        
        $config = Zend_Registry::get('config');

        $filter = Zend_Registry::get('inputFilter');

        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');

            $userId = $filter->filter($post['userId']);

            if ($userId == '') {
                throw new Internal_Exception_Input('User ID is required');
            }

            $realm = preg_replace('/^[^@]*@/', '', $userId);
            
            $authAdapter = new $config->authentication->$realm->class();
            
            if ($authAdapter->manageLocally()) {
            	$authAdapter->deleteAccount($userId);
            }
            
            $this->_authzAdapter->deleteUser($userId);

            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $userId);
            $this->_logger->info('Account was deleted for ' . $userId . '.');

            $this->_redirect('/admin/user');

        }

        $get = Zend_Registry::get('get');
        $userId = $filter->filter($get['userId']);

        $realm = preg_replace('/^[^@]*@/', '', $userId);
            
        $this->view->realmName = $config->authentication->$realm->name;
                
        $user = $this->_authzAdapter->getUser($userId);

        $this->view->userId = $userId;
        $this->view->displayUserId = preg_replace('/@.*$/', '', $userId);
        $this->view->role   = $user['role'];
        $this->view->title  = 'Delete User';
        
    }
}