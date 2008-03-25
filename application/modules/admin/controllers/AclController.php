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
 * @subpackage Admin_AclController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Manages all access control the the application.  Allows the user to build
 * custom roles.
 *
 * @package    RSPM
 * @subpackage Admin_AclController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Admin_AclController extends Internal_Controller_Action 
{
    /**
     * Authz adapter
     *
     * @var mixed
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
     * List of all existing roles in the application.
     *
     */
    public function indexAction()
    {
        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            );

        $this->view->roles = $this->_acl->getAvailableRoles();

        if (count($this->view->roles) != 0) {
            $this->view->javascript = 'sortable.js';
        }

        $this->view->title = "Manage Access Roles";
    }

    /**
     * Add a new role to the ACL
     *
     */
    public function addAction()
    {
    	$config = Zend_Registry::get('config');
            
        if (!is_writable($config->aclConfigFile)) {
            throw new Exception('ACL file is not writable, therefore you can not add roles to it.  Contact system administrator for assistance');
        }

        $roles = $this->_acl->getAvailableRoles();

        $filter = Zend_Registry::get('inputFilter');

        $temp = array();
        foreach ($roles as $r) {
            if ($r['editable'] == 1) {
                $temp[$r['name']] = $r['name'];
            }
        }

        $roles = $temp;

        $this->view->roles = array_merge(array('none' => 'No Inheritance'), $roles);

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

            $post = Zend_Registry::get('post');

            $roleName        = $filter->filter($post['roleName']);
            $inheritRoleName = ($filter->filter($post['inheritRoleName']) == 'none') ? null : $filter->filter($post['inheritRoleName']);

            $data = array(
                'name'     => preg_replace('/[^a-z0-9]/i', '_', $roleName),
                'inherit'  => $inheritRoleName,
                'editable' => 1
                );

            $data = array_merge($data, $this->_processAccessList($post, $inheritRoleName));

            $this->_acl->addCustomRole($data);

            $this->_logger->setEventItem('attributeName', 'accessRole');
            $this->_logger->setEventItem('attributeId', $data['name']);
            $this->_logger->info($data['roleName'] . ' was added as a role');

            $this->_redirect('/admin/acl/details?originalRoleName=' . $data['name']);

        } else {
            $get = Zend_Registry::get('get');

            $roleName        = '';
            $inheritRoleName = '';

            if (isset($get['roleName'])) {
                $this->view->roleName = $filter->filter($get['roleName']);
            }

            if (isset($get['inheritRoleName'])) {
                $this->view->inheritRoleName = ($filter->filter($get['inheritRoleName']) == 'none') ? '' : $filter->filter($get['inheritRoleName']);
            }

            $this->view->action    = 'add';
            $this->view->resources = $this->_getResources($this->view->inheritRoleName);
            $this->view->title     = "Manage Access Roles";
        }
    }

    /**
     * Edit an existing role in the ACL
     *
     */
    public function editAction()
    {
        $config = Zend_Registry::get('config');
            
        if (!is_writable($config->aclConfigFile)) {
            throw new Exception('ACL file is not writable, therefore you can not add roles to it.  Contact system administrator for assistance');
        }
            	
    	$availableRoles = $this->_acl->getAvailableRoles();

        $filter = Zend_Registry::get('inputFilter');

        $temp = array();
        foreach ($availableRoles as $r) {
            $temp[$r['name']] = $r['name'];
        }

        $roles = $temp;

        $this->view->roles = array_merge(array('none' => 'No Inheritance'), $roles);

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

            $post = Zend_Registry::get('post');

            $roleName         = $filter->filter($post['roleName']);
            $originalRoleName = $filter->filter($post['originalRoleName']);
            $inheritRoleName  = ($filter->filter($post['inheritRoleName']) == 'none') ? null : $filter->filter($post['inheritRoleName']);

            $data = array(
                'newName'  => $roleName,
                'name'     => $originalRoleName,
                'inherit'  => $inheritRoleName,
                'editable' => 1
                );

            $result = $this->_processAccessList($post, $inheritRoleName);

            $data = array_merge($data, $result);

            $dba = Zend_Registry::get('dbAdapter');
            $dba->beginTransaction();

            try {
                $this->_acl->editCustomRole($data);
            } catch (Exception $e) {
                $dba->rollback();
                throw $e;
            }

            $this->_logger->setEventItem('attributeName', 'accessRole');
            $this->_logger->setEventItem('attributeId', $data['name']);
            $this->_logger->info($data['name'] . ' was modified');

            try {
                $users = $this->_authzAdapter->getUsers($originalRoleName);
            } catch (Exception $e) {
                $dba->rollback();
                throw $e;
            }

            if ($this->_authzAdapter->manageLocally()) {
                foreach ($users as $u) {

                    try {
                        $this->_authzAdapter->editUser($u['userId'], $roleName);
                    } catch (Exception $e) {
                        $dba->rollback();
                        throw $e;
                    }
                }
            }

            $dba->commit();

            $this->_redirect('/admin/acl/details/?originalRoleName=' . $roleName);

        } else {
            $get = Zend_Registry::get('get');

            $originalRoleName = '';
            $inheritRoleName  = '';
            $roleName         = '';

            if (isset($get['roleName'])) {
                $roleName = $filter->filter($get['roleName']);
            }

            if (isset($get['inheritRoleName'])) {
                $inheritRoleName = ($filter->filter($get['inheritRoleName']) == 'none') ? '' : $filter->filter($get['inheritRoleName']);
            }

            if (!isset($get['originalRoleName'])) {
                throw new Internal_Exception_Input('Role name not set');
            }

            $originalRoleName = $filter->filter($get['originalRoleName']);
            $role             = null;

            foreach ($availableRoles as $r) {
                if ($originalRoleName == $r['name']) {
                    $role = $r;
                }
            }

            if (is_null($role)) {
                throw new Internal_Exception_Input('Role Not Found');
            }

            if (!(boolean)$role['editable']) {
                throw new Internal_Exception_Input('The role passed is not editable');
            }

            $children = $this->_getChildrenOfRole($originalRoleName);

            $temp = array();
            foreach ($children as $key => $value) {
                $t = array();
                $t['name'] = $key;
                $t['from'] = implode(' via ', array_merge(array($key), array_diff(array_reverse($value), array($originalRoleName))));

                $temp[] = $t;
            }

            $this->view->children = $temp;
            
            $resources = $this->_getResources($role['name']);
 
            foreach ($resources as &$r) {
            	foreach ($r as &$c) {
	            	$c['someAccess'] = false;
	            	foreach ($c['part'] as $p) {
	            		if ($p['access']) {
	            			$c['someaccess'] = true;
	            		}
	            	}
            	}
            }

            $this->view->originalRoleName = $originalRoleName;
            $this->view->roleName         = ($roleName == '') ? $originalRoleName : $roleName;
            $this->view->inheritRoleName  = ($inheritRoleName == '') ? $role['inherit'] : $inheritRoleName;
            $this->view->action           = 'edit';
            $this->view->resources        = $resources;
            $this->view->title            = "Edit Role";
        }
    }

    /**
     * Deletes a role from the ACL
     *
     */
    public function deleteAction()
    {
        $config = Zend_Registry::get('config');
            
        if (!is_writable($config->aclConfigFile)) {
            throw new Exception('ACL file is not writable, therefore you can not add roles to it.  Contact system administrator for assistance');
        }
            	
        $filter = Zend_Registry::get('inputFilter');

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');

            $this->_acl->deleteCustomRole($filter->filter($post['originalRoleName']));

            $this->_logger->setEventItem('attributeName', 'accessRole');
            $this->_logger->setEventItem('attributeId', $post['originalRoleName']);
            $this->_logger->info($post['originalRoleName'] . ' was deleted as a role');

            $this->_redirect('/admin/acl/');
        } else {
            $get = Zend_Registry::get('get');

            $this->view->originalRoleName = $filter->filter($get['originalRoleName']);
            $this->view->title            = "Delete Access Role";
        }

    }

    /**
     * Shows the details of a role
     *
     */
    public function detailsAction()
    {

        $filter = Zend_Registry::get('inputFilter');

        $availableRoles = $this->_acl->getAvailableRoles();

        $this->view->acl = array(
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            );

        $get = Zend_Registry::get('get');

        $originalRoleName = '';

        if (!isset($get['originalRoleName'])) {
            throw new Internal_Exception_Input('Role name not set');
        }

        $originalRoleName = $filter->filter($get['originalRoleName']);
        $role             = null;

        foreach ($availableRoles as $r) {
            if ($originalRoleName == $r['name']) {
                $role = $r;
                break;
            }
        }

        if (is_null($role)) {
            throw new Internal_Exception_Data('Role Not Found');
        }

        $this->view->role  = $role;
        $this->view->title = "Access Role Details";

    }

    /**
     * Processes the access list passed through adding and editing a role
     *
     * @param array $data
     * @param string $inheritRoleName
     * @return array
     */
    protected function _processAccessList($data, $inheritRoleName)
    {
        $resources = $this->_getResources($inheritRoleName);

        $allow = array();
        $deny  = array();

        foreach ($resources as $module => $controllers) {
            foreach ($controllers as $controller => $actions) {

                $resource = strtolower($module . '_' . $controller);

                if (isset($data[$module][$controller]['all'])) {
                    if ($data[$module][$controller]['all'] == 'allow') {
                        if (!$this->_acl->isAllowed($inheritRoleName, $resource)) {
                            $allow[] = array(
                                'resource'  => $resource,
                                'privilege' => '*'
                                );
                        }

                        $parts = array_keys($actions['part']);
                        
                        foreach ($parts as $action) {
                            if (isset($data[$module][$controller]['part'][$action])) {
                                if ($data[$module][$controller]['part'][$action] == 'deny') {
                                    $deny[] = array(
                                        'resource'  => $resource,
                                        'privilege' => $action
                                        );
                                }
                            }
                        }
                    } else {
                        if ($this->_acl->isAllowed($inheritRoleName, $resource)) {
                            $deny[] = array(
                                'resource'  => $resource,
                                'privilege' => '*'
                                );
                        }

                        $parts = array_keys($actions['part']);
                        
                        foreach ($parts as $action) {
                            if (isset($data[$module][$controller]['part'][$action])) {
                                if ($data[$module][$controller]['part'][$action] == 'allow' && !$this->_acl->isAllowed($inheritRoleName, $resource, $action)) {
                                    $allow[] = array(
                                        'resource'  => $resource,
                                        'privilege' => $action
                                        );
                                }
                            }
                        }
                    }
                } else {
                	$parts = array_keys($actions['part']);
                	
                    foreach ($parts as $action) {                    	
                        if (isset($data[$module][$controller]['part'][$action])) {
                            if ($data[$module][$controller]['part'][$action] == 'allow' && !$this->_acl->isAllowed($inheritRoleName, $resource, $action)) {
                                $allow[] = array(
                                    'resource'  => $resource,
                                    'privilege' => $action
                                    );
                            }

                            if ($data[$module][$controller]['part'][$action] == 'deny' && $this->_acl->isAllowed($inheritRoleName, $resource, $action)) {
                                $deny[] = array(
                                    'resource'  => $resource,
                                    'privilege' => $action
                                    );
                            }
                        }
                    }
                }
            }
        }

        $ret = array(
            'allows' => $allow,
            'denys'  => $deny
            );

        return $ret;
    }

    /**
     * Gets all the children of a given role
     *
     * @param string $role
     * @param string $roles
     * @param array $children
     * @return array
     */
    protected function _getChildrenOfRole($role, $roles = '', $children = array())
    {
        if ($roles == '') {
            $roles = $this->_acl->getAvailableRoles();
        }

        foreach ($roles as $r) {
            if ($r['inherit'] == $role) {
                if (!isset($children[$r['name']])) {
                    $children[$r['name']] = array();
                }
                if (isset($children[$r['inherit']])) {
                    $children[$r['name']] = array_merge($children[$r['inherit']], array($role));
                } else {
                    $children[$r['name']][] = $role;
                }

                $children = $this->_getChildrenOfRole($r['name'], $roles, $children);
            }
        }

        return $children;
    }


    /**
     * Gets all resources with permissions based on the passed role
     *
     * @param string $role
     * @return array
     */
    protected function _getResources($role = '')
    {
        $controllers = Zend_Controller_Front::getInstance()->getControllerDirectory();

        if (is_array($controllers)) {
            ksort($controllers);
        }

        $roles = $this->_acl->getAvailableRoles();

        $temp = array();

        // gets the role from teh acl, with all allows and denys set
        foreach ($roles as $r) {
            $temp[$r['name']] = $r;
            if ($role != '' && $r['name'] == $role) {
                $role = $r;
                break;
            }
        }

        // Sets the denys for the role
        $denys = array();
        if (isset($role['denys'])) {
            foreach ($role['denys'] as $d) {
                $denys[$d['resource']] = $d['privilege'];
            }
        }

        $roles = $temp;

        $result = array();


        // gets all controllers to get the actions in them
        foreach ($controllers as $key => $value) {
            foreach (new DirectoryIterator($value) as $file) {
                if (preg_match('/controller\.php/i', $file)) {

                    if ($key == 'default') {
                        $classname = preg_replace('/\.php/i', '', $file);
                    } else {
                        $classname = ucwords(strtolower($key)) . '_' .
                            preg_replace('/\.php/i', '', $file);
                    }

                    $controllerName = preg_replace('/^[^_]*\_/', '',
                        preg_replace('/controller/i', '', $classname));

                    $resource = strtolower($key . '_' . $controllerName);

                    $result[$key][$controllerName]['all'] = array('access' => false, 'inherit' => '');

                    $noInheritance = false;
                    $inherit = $role['name'];

                    $allows = array();
                    while (!$noInheritance) {

                        $iAllows = array();
                        $iDenys  = array();

                        if (isset($roles[$inherit]['allows'])) {
                            foreach ($roles[$inherit]['allows'] as $a) {
                                $allows[$a['resource']] = $a['privilege'];
                                $iAllows[$a['resource']] = $a['privilege'];
                            }
                        }

                        if (isset($roles[$inherit]['denys'])) {
                            foreach ($roles[$inherit]['denys'] as $a) {
                                $iDenys[$a['resource']] = $a['privilege'];
                            }
                        }

                        // Checks to see if the inheriting role allows the rource
                        if (in_array('*', array_keys($allows)) || (isset($allows[$resource]) && $allows[$resource] == '*')) {

                            // checks to see that even though the inheriting role allows the resource that the role in question doesnt specifically deny it
                            if (!(isset($denys[$resource]) && $denys[$resource] == '*')) {
                                $result[$key][$controllerName]['all']['access'] = true;
                                if (isset($iAllows[$resource]) && $iAllows[$resource] == '*') {
                                    $result[$key][$controllerName]['all']['inherit'] = $inherit;
                                }
                            }
                        }

                        if (isset($roles[$inherit]['inherit']) && $roles[$inherit]['inherit'] != '') {
                            $inherit = $roles[$inherit]['inherit'];
                        } else {
                            $noInheritance = true;
                        }
                    }

                    require_once($controllers[$key] . DIRECTORY_SEPARATOR . $file);

                    $class = new ReflectionClass($classname);
                    $methods = $class->getMethods();

                    $result[$key][$controllerName]['description'] = $this->_getDescriptionFromCommentBlock($class->getDocComment());
                    
                    foreach ($methods as $m) {
                        if (preg_match('/action/i', $m->name) &&
                            basename($class->getMethod($m->name)->getFileName()) == $file) {

                            $action = preg_replace('/action/i', '', $m->name);
                            if ($role != '') {
                                $result[$key][$controllerName]['part'][$action]['access'] = $this->_acl->isAllowed($role['name'], $resource, $action);
                            } else {
                                $result[$key][$controllerName]['part'][$action]['access'] = false;
                            }
                            
                            $result[$key][$controllerName]['part'][$action]['description'] = $this->_getDescriptionFromCommentBlock($m->getDocComment());

                            $noInheritance = ($role['inherit'] == '');
                            $inherit = $role['inherit'];

                            $result[$key][$controllerName]['part'][$action]['inherit'] = '';

                            while (!$noInheritance) {
                                $iAllows = array();
                                $iDenys  = array();

                                if (isset($roles[$inherit]['allows'])) {
                                    foreach ($roles[$inherit]['allows'] as $a) {
                                        $iAllows[] = $a['resource'] . '_' . $a['privilege'];
                                    }
                                }

                                if (isset($roles[$inherit]['denys'])) {
                                    foreach ($roles[$inherit]['denys'] as $a) {
                                        $iDenys[] = $a['resource'] . '_' . $a['privilege'];
                                    }
                                }

                                if ($result[$key][$controllerName]['part'][$action]['access'] == false) {
                                    if (in_array($resource . '_' . $action, $iDenys) && $result[$key][$controllerName]['part'][$action]['inherit'] == '') {
                                        $result[$key][$controllerName]['part'][$action]['inherit'] = $inherit;
                                    }
                                } else {
                                    if (in_array($resource . '_' . $action, $iAllows) && $result[$key][$controllerName]['part'][$action]['inherit'] == '') {
                                        $result[$key][$controllerName]['part'][$action]['inherit'] = $inherit;
                                    }
                                }

                                if (isset($roles[$inherit]['inherit']) && $roles[$inherit]['inherit'] != '') {
                                    $inherit = $roles[$inherit]['inherit'];
                                } else {
                                    $noInheritance = true;
                                }
                            }
                        }
                    }


                    if (is_array($result[$key])) {
                        ksort($result[$key]);
                    }

                    if (is_array($result[$key][$controllerName]['part'])) {
                        ksort($result[$key][$controllerName]['part']);
                    }
                }
            }
        }

        return $result;
    }
    
    protected function _getDescriptionFromCommentBlock($str)
    {
    	$str = preg_replace('/@[^\n]*/', '', $str);
    	$str = preg_replace('/\s*\*\s/', '', $str);
    	$str = preg_replace('/(\/\*|\*\/)*/', '', $str);
    	
    	return trim($str);
    }

}