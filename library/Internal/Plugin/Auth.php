<?php
/**
 * Cyclone
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
 * @subpackage Internal_Plugin_Auth
 * @category   Front Controller Plugin
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Auth.php 189 2007-07-31 19:27:49Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Auth plugin for the Front Controller of the applicaiton.  This plugin looks at
 * the requested module, controller, and action and determines if the logged-in
 * user has access to the action based on the ACL.
 *
 * @package    Cyclone
 * @subpackage Internal_Plugin_Auth
 * @category   Front Controller Plugin
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Internal_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * Authentication object
     *
     * @var Auth Adapter object
     */
    protected $_auth = null;

    /**
     * ACL object
     *
     * @var Billboard_Acl
     */
    protected $_acl = null;

    /**
     * Path to the plugins directory for the authorization
     *
     * @var string
     */
    protected $_pluginPath = null;

    /**
     * Arguments for the controller if user doesn't have access in the ACL
     *
     * @var array
     */
    private $_noAcl = array('module'     => 'default',
                            'controller' => 'index',
                            'action'     => 'access'
                            );

    /**
     * Arguments for the controller if the user hasn't Authed.
     *
     * @var unknown_type
     */
    private $_noAuth = array('module'     => 'login',
                             'controller' => 'index',
                             'action'     => 'index');

    /**
     * Enter description here...
     *
     * @param Auth Adapter $auth
     * @param Billboard_Acl $acl
     * @param string $pluginPath
     */
    public function __construct($auth, $acl)
    {
        $this->_auth = $auth;
        $this->_acl = $acl;
    }

    /**
     * Pre-dispatch code that checks with the ACL to see if the loggedin user has
     * access to view the page that is being requested.  If they don't we rewrite
     * the requests controller, module and action to go to a standard "no access"
     * page.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Get the requested module, controller, and action
        $module     = $request->module;
        $controller = $request->controller;
        $action     = $request->action;

        $resource = strtolower($module . '_' . $controller);

        if (!$this->_acl->has($resource)) {
            $resource = null;
        }

        if ($this->_acl->isAllowed($this->_acl->getDefaultRole(), $resource, $action)) {
            return;
        }


        // Run autologin stuff here
        $config = Zend_Registry::get('config');
        Zend_Loader::loadClass($config->authentication);

        $role = '';

        // We check to see if the adapter allows auto logging in, if it does we do it
        if (call_user_func(array($config->authentication, 'autoLogin'))) {
            // Set up the authentication adapter
            $authAdapter = new $config->authentication();

            // Attempt authentication, saving the result
            $result = $this->_auth->authenticate($authAdapter);

            if (!$result->isValid()) {
                $this->printError('Login Error', $result->getMessages());
            }
        }

        if ($this->_auth->hasIdentity() && $this->_auth->getIdentity() != '' && !is_null($this->_auth->getIdentity())) {

            $config = Zend_Registry::get('config');
            Zend_Loader::loadClass($config->authorization);

            $authz = Ot_Authz::getInstance();

            $roles = $authz->authorize(new $config->authorization($this->_auth->getIdentity()));

            if ($roles->isValid()) {
                $role = $authz->getRole();
            }
        }

        if ($role == '') {
            $role = $this->_acl->getDefaultRole();
        }

        //echo "$role is trying to access $resource $action";

        if (!$this->_acl->isAllowed($role, $resource, $action)) {
            if (!$this->_auth->hasIdentity()) {
                $module     = $this->_noAuth['module'];
                $controller = $this->_noAuth['controller'];
                $action     = $this->_noAuth['action'];
            } else {
                $module     = $this->_noAcl['module'];
                $controller = $this->_noAcl['controller'];
                $action     = $this->_noAcl['action'];
            }
        }

        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);


        try {
            $resources = $this->_acl->getResourcesWithSomeAccess($role);
        } catch (Exception $e) {
            die($e->getMessage());
        }

        $vr = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $vr->view;

        $viewTabs = array();
        $subTabs  = array();

        foreach ($config->navigation as $tabs) {
            if ($resources == '*') {
                $viewTabs[] = $tabs->toArray();
            } else {
                foreach ($resources as $r) {
                    if (preg_match('/^' . strtolower($tabs->module) . '\_/', $r)) {
                        $viewTabs[] = $tabs->toArray();
                        break;
                    }
                }
            }

            if ($tabs->module == $request->module) {
                if ($tabs->submenu instanceof Zend_Config) {
                    foreach ($tabs->submenu as $s) {
                        if ($this->_acl->hasSomeAccess($role, strtolower($tabs->module . '_' . $s->controller))) {
                            $temp = $s->toArray();
                            
                            if (preg_match('/^\//', $temp['link'])) {
                            	$temp['link'] = $view->sitePrefix . $temp['link'];
                            } else if (!preg_match('/^http/', $temp['link'])) {
                            	$temp['link'] = $view->sitePrefix . '/' . (($tabs->module == 'default') ? '' : $tabs->module . '/') . $s->controller . '/' . $temp['link'];
                            }
                            
                            $temp['target'] = (preg_match('/^http/', $temp['link'])) ? '_blank' : '';
                            $subTabs[] = $temp;
                        }
                    }
                }
            }
        }

        $view->branch = $request->module;
        $view->tabs   = $viewTabs;
        $view->subnav = $subTabs;
    }
}