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
 * @subpackage Login_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Allows the user to log in and log out of the application, as well as signup
 * for new accounts and reset passwords.
 *
 * @package    RSPM
 * @subpackage Login_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Login_IndexController extends Internal_Controller_Action 
{
	/**
	 * Flash Manager to handle display notifications
	 *
	 * @var object
	 */
	protected $_flashMessenger = null;
	
	/**
	 * Initialization function
	 *
	 */
	public function init()
	{
		$this->_flashMessenger = $this->getHelper('FlashMessenger');
		$this->_flashMessenger->setNamespace('login');
		parent::init();
	}
	
    /**
     * Action when going to the main login page
     *
     */
    public function indexAction()
    {    	
        $this->view->title = "Login";

        $config = Zend_Registry::get('config');
        $filter = Zend_Registry::get('inputFilter');

        $authRealm = new Zend_Session_Namespace('authRealm');
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
        	$this->_redirect('/');
        }
        if ((isset($authRealm->realm) && $authRealm->autoLogin) || strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

            $post   = Zend_Registry::get('post');
            
            if (isset($authRealm->realm)) {
            	$realm = $authRealm->realm;
            } else {
                $realm = $filter->filter($post['realm']);
            }

            // Set up the authentication adapter
            $authAdapter = new $config->authentication->$realm->class($filter->filter($post['userId']),
                                                                      $filter->filter($post['password']));
            $auth = Zend_Auth::getInstance();            
            
            $authRealm->realm = $realm;
            $authRealm->autoLogin = $authAdapter->autoLogin();
            
            // Attempt authentication, saving the result
            $result = $auth->authenticate($authAdapter);

            $authRealm->unsetAll();
            
            if (!$result->isValid()) {
	            $this->_logger->setEventItem('attributeName', 'userId');
	            $this->_logger->setEventItem('attributeId', $filter->filter($post['userId']));
	            $this->_logger->info('Invalid Login Attempt');             	
                throw new Internal_Exception_Data('Invalid Login Credentials');
            }
            
            $authz = new $config->authorization($filter->filter($post['userId']));
            $user = $authz->getUser($filter->filter($post['userId']));
            
            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $filter->filter($post['userId']));
            $this->_logger->info('User Logged In');  
                        
            if ($user['role'] == 'activation_pending') {
            	$this->_redirect('/login/index/changePassword/');
            } else {
            	$this->_redirect('/');
            }

        }
        
        $authRealm->unsetAll();
        
        $adapters = $config->authentication->toArray();
        
        $auth = array();
        foreach ($adapters as $key => $value) {
        	$a = new $value['class'];
        	
        	$auth[] = array(
        	   'realm'       => $key,
        	   'name'        => $value['name'],
        	   'description' => $value['description'],
        	   'autoLogin'  => $a->autoLogin(),
        	);
        }
        
        $this->view->authAdapters = $auth;
        $this->view->messages = $this->_flashMessenger->getMessages();
    }

    /**
     * Action for forgetting a password
     *
     */
    public function forgotAction()
    {
        $config = Zend_Registry::get('config');
        $filter = Zend_Registry::get('inputFilter');

        Zend_Loader::loadClass($config->authentication);

        $auth = new $config->authentication();

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
            return;
        }

        $this->view->title = "Forgot My Password";

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');

            $userId = $filter->filter($post['userId']);

            if ($userId != ''){
                $newPassword = $auth->resetPassword($userId);
                            
                $user = $auth->getUser($userId);
                if (count($user) != 1) {
                	throw new Internal_Exception_Data('User Account not found');
                }
                
                $user = $user[0];
                
	            $trigger = new EmailTrigger();
	            $trigger->newPassword = $newPassword;
	            $trigger->userEmail   = $user['email'];
	            $trigger->userId      = $user['userId'];                 
	            $trigger->dispatch('Login_Index_Forgot');
	                            
	            $this->_flashMessenger->addMessage('Password was reset and an email was sent to the address on file');
		            
	            $this->_logger->setEventItem('attributeName', 'userId');
	            $this->_logger->setEventItem('attributeId', $user['userId']);
	            $this->_logger->info('User Reset Password'); 	            
            }
            
            $this->_redirect('/login/');
        }
    }
    
    /**
     * Logs a user out
     *
     */
    public function logoutAction()
    {
    	$this->_helper->getExistingHelper('viewRenderer')->setNeverRender();
    	
        $config = Zend_Registry::get('config');
        
        foreach ($config->authentication as $a) { 
            $auth = new $a->class;
            $auth->autoLogout();  
        }
        
        Zend_Auth::getInstance()->clearIdentity();
        
        Ot_Authz::getInstance()->clearRole();
                
        $this->_redirect('/login/'); 
    } 
    
    /**
     * allows a user to signup for an account
     *
     */
    public function signupAction()
    {
    	if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    		$filter = Zend_Registry::get('inputFilter');
    		$post   = Zend_Registry::get('post');
    		$config = Zend_Registry::get('config');
    		
    		//Zend_Loader::loadClass($config->authentication->guest);
    		$auth = new $config->authentication->guest->class();
    		$authz = new $config->authorization('nouser');
    		
	    	if (!$auth->manageLocally()) {
	            $this->_redirect('/');
	            return;
	        }
	        
	        $uf = new Zend_Filter();
	        $uf->addFilter(new Zend_Filter_Alnum());
	        $uf->addFilter(new Zend_Filter_StringTrim());
	        $uf->addFilter(new Zend_Filter_StripTags());
	        
	        $data = array(
	           'userId' => $uf->filter($post['userId']),
	           'email'  => $filter->filter($post['email']),
	           'role'   => 'activation_pending',
	           'realm'  => 'guest',
	        );
	        
	        if ($data['userId'] == '') {
	        	throw new Internal_Exception_Input('No user ID was entered');
	        }
	        
	        if ($data['email'] == '') {
	        	throw new Internal_Exception_Input('No email address was entered');
	        }
	        
	        $ev = new Zend_Validate_EmailAddress();
	        if (!$ev->isValid($data['email'])) {
	        	throw new Internal_Exception_Input('Email address is not valid');
	        }
	        
	        $user = $auth->getUser($data['userId']);
	        
	        if (count($user) != 0) {
	        	throw new Internal_Exception_Input('User ID is taken.  Please select a different ID');
	        }
    		
	        $data['password'] = $auth->addAccount($data['userId'], '', $data['email']);
	        
	        $authz->addUser($data['userId'], $data['role'], $data['realm']);
	        
	        $trigger = new EmailTrigger();
	        $trigger->setVariables($data);
	        $trigger->dispatch('Login_Index_Signup');
	        
	        $this->_flashMessenger->addMessage('Account was created and an email has been sent to the address provided');
	        
            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $data['userId']);
            $this->_logger->info('User Successfully signed up'); 	

            $this->_redirect('/login/');
    	}
    	
    	$this->view->title = 'Sign-up for an account';
    }

    /**
     * allows a user to change their password
     *
     */
    public function changePasswordAction()
    {
        $this->view->title = 'Change your password';
        
        if ($this->_request->isPost()) {
            $post     = Zend_Registry::get('post');
            $filter   = Zend_Registry::get('inputFilter');
            $config   = Zend_Registry::get('config');
            
            $auth = new $config->authentication->guest;
                        
            $user = $auth->getUser(Zend_Auth::getInstance()->getIdentity());
            if (count($user) != 1) {
                throw new Internal_Exception_Data('User account not found');
            }
            
            $user = $user[0];
            
            $oldPassword  = $filter->filter($post['oldPassword']);
            $newPassword1 = $filter->filter($post['newPassword1']);
            $newPassword2 = $filter->filter($post['newPassword2']);

            if ($newPassword1 != $newPassword2) {
                throw new Internal_Exception_Input('New passwords do not match');
            }

            if ($auth->encryptPassword($oldPassword) != $user['password']) {
                throw new Internal_Exception_Input('Original Password was incorrect');
            }

            $auth->editAccount($user['userId'], $newPassword1, $user['email']);
            
            if (Ot_Authz::getInstance()->getRole() == 'activation_pending') {
                
                $authz = new $config->authorization(Zend_Auth::getInstance()->getIdentity());
                $authz->editUser($user['userId'], 'proposer');
            }
            
            $this->_flashMessenger->addMessage('Your password has been changed.  You can now log in with your new credentials');
            
            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $user['userId']);
            $this->_logger->info('User changed Password'); 
                            
            $this->logoutAction();
        } 
    }
    
    public function ncsuAction()
    {
    	$config = Zend_Registry::get('config');
    	$auth = Zend_Auth::getInstance();
    	
        // Set up the authentication adapter
        $authAdapter = new $config->authentication->ncsu();
        
        // Attempt authentication, saving the result
        $result = $auth->authenticate($authAdapter);
        
        if (!$result->isValid()) {
            throw new Exception('Error getting login credentials');
        }  

        $this->_redirect('/');
    }
}