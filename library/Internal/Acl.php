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
 * @package    Cyclone
 * @subpackage Internal_Acl
 * @category   ACL
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Acl.php 189 2007-07-31 19:27:49Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Manages all ACL's for the application.
 *
 * @package    Cyclone
 * @subpackage Internal_Acl
 * @category   Access Control
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Internal_Acl extends Zend_Acl
{
    /**
     * Default role given to a user that doesn't have any other defined role
     *
     * @var string
     */
    protected $_defaultRole = 'guest';

    /**
     * Creates a new instance of the ACL's
     *
     */
    public function __construct()
    {

        $config = Zend_Registry::get('config');

        try {
            $xml = simplexml_load_file($config->aclConfigFile);
        } catch (Exception $e) {
            die ($e->getMessage());
        }


        $this->add(new Zend_Acl_Resource('default_index'));
        $this->add(new Zend_Acl_Resource('default_documentation'));
        $this->add(new Zend_Acl_Resource('default_faq'));
        $this->add(new Zend_Acl_Resource('default_bug'));
        $this->add(new Zend_Acl_Resource('default_error'));
        $this->add(new Zend_Acl_Resource('default_soap'));
        $this->add(new Zend_Acl_Resource('admin_index'));
        $this->add(new Zend_Acl_Resource('admin_user'));
        $this->add(new Zend_Acl_Resource('admin_acl'));
        $this->add(new Zend_Acl_Resource('admin_emailqueue'));
        $this->add(new Zend_Acl_Resource('admin_log'));
        $this->add(new Zend_Acl_Resource('admin_location'));
        $this->add(new Zend_Acl_Resource('admin_cron'));
        $this->add(new Zend_Acl_Resource('admin_api'));
        $this->add(new Zend_Acl_Resource('admin_custom'));
        $this->add(new Zend_Acl_Resource('admin_config'));
        $this->add(new Zend_Acl_Resource('admin_email'));
        $this->add(new Zend_Acl_Resource('login_index'));
        $this->add(new Zend_Acl_Resource('profile_index'));
        $this->add(new Zend_Acl_Resource('calendar_index'));
        
        foreach ($xml->role as $x) {

            $this->addRole(new Zend_Acl_Role(trim($x->name)), (trim($x->inherit) != '') ? trim($x->inherit) : null);

            foreach ($x->allows->allow as $a) {
                $this->allow(trim($x->name), (trim($a->resource) == '*') ? null : trim($a->resource), (trim($a->privilege) == '*') ? null : trim($a->privilege));
            }

            foreach ($x->denys->deny as $d) {
                $this->deny(trim($x->name), (trim($d->resource) == '*') ? null : trim($d->resource), (trim($d->privilege) == '*') ? null : trim($d->privilege));
            }
        }
    }

    /**
     * Gets the default role
     *
     * @return string
     */
    public function getDefaultRole()
    {
        return $this->_defaultRole;
    }

    public function getAvailableRoles($role = '')
    {

        $config = Zend_Registry::get('config');

        try {
            $xml = simplexml_load_file($config->aclConfigFile);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $roles = array();
        foreach ($xml->role as $x) {
            $temp = array();

            $temp['name']     = (string)$x->name;
            $temp['inherit']  = (string)$x->inherit;
            $temp['editable'] = ((int)$x->editable == 1);
            $temp['allows']   = array();
            $temp['denys']    = array();

            foreach ($x->allows->allow as $a) {
                $tempAllow = array();

                $tempAllow['resource'] = (string)$a->resource;
                $tempAllow['privilege'] = (string)$a->privilege;

                $temp['allows'][] = $tempAllow;
            }

            foreach ($x->denys->deny as $d) {
                $tempDeny = array();

                $tempDeny['resource'] = (string)$d->resource;
                $tempDeny['privilege'] = (string)$d->privilege;

                $temp['denys'][] = $tempDeny;
            }

            if ($role != '' && $role == $temp['name']) {
                return $temp;
            }

            $roles[] = $temp;
        }

        return $roles;
    }

    public function addCustomRole($data)
    {
        if ($this->hasRole($data['name'])) {
            throw new Exception('Role already exists.  Can not create new role');
        }

        $this->_writeAclConfig($data);
    }

    public function editCustomRole($data)
    {
        $this->_writeAclConfig($data);
    }

    public function deleteCustomRole($roleName)
    {
        $this->_writeAclConfig(array('name' => $roleName), true);
    }

    /**
     * This is a brother to isAllowed, but instead of returning false is a role
     * has access to all privleges in a resource, it will return true if and only
     * if the role has access to at least one privlege within the resource.
     *
     * @param string $role
     * @param string $resource
     * @param string $privilege
     * @return boolean
     */
    public function hasSomeAccess($role, $resource)
    {
        $r = $role;
        $res = $resource;

        if (null !== $role) {
            $role = $this->_getRoleRegistry()->get($role);
        }

        if (null !== $resource) {
            $resource = $this->get($resource);
        }

        $rules = $this->_getRules($resource, $role);

        if (null !== $rules) {
            if (isset($rules['allPrivileges']['type']) && $rules['allPrivileges']['type'] === self::TYPE_ALLOW) {
                return true;
            }

            foreach ($rules['byPrivilegeId'] as $privilege => $rule) {
                if (self::TYPE_ALLOW === ($ruleTypeOnePrivilege = $this->_getRuleType($resource, $role, $privilege))) {
                    return true;
                }
            }
        }

        return $this->isAllowed($role, $resource);
    }

    public function getResourcesWithSomeAccess($role)
    {
        $rules = $this->_rules;

        if (isset($rules['allResources']['byRoleId'][$role]) && $rules['allResources']['byRoleId'][$role]['allPrivileges']['type'] === self::TYPE_ALLOW) {
            return '*';
        }

        $access = array();

        foreach ($rules['byResourceId'] as $resource => $privilege) {
            if (isset($privilege['byRoleId'][$role])) {
                if (isset($privilege['byRoleId'][$role]['allPrivileges']) && $privilege['byRoleId'][$role]['allPrivileges']['type'] === self::TYPE_ALLOW) {
                    $access[] = $resource;
                } else {
                    foreach ($privilege['byRoleId'][$role]['byPrivilegeId'] as $priv => $act) {
                        if ($act['type'] === self::TYPE_ALLOW) {
                            $access[] = $resource;
                            break;
                        }
                    }
                }
            }
        }

        $parents = $this->_getRoleRegistry()->getParents($role);

        if (count($parents) != 0) {

            foreach ($parents as $key => $value) {
                $access = array_unique(array_merge($access, $this->getResourcesWithSomeAccess($key)));
            }
        }

        return $access;
    }

    protected function _writeAclConfig($data, $remove=false)
    {
        $current = $this->getAvailableRoles();

        $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;

        $root = $doc->createElement('roles');
        $root = $doc->appendChild($root);

        if (!$this->hasRole($data['name'])) {
            $current = array_merge($current, array($data));
        }

        $roles = array();
        foreach ($current as $r) {
            if ($r['name'] == $data['name']) {
                if ($remove) {
                    continue;
                }
                $r = $data;

                if (isset($r['newName'])) {
                    $r['name'] = $r['newName'];
                }
            }

            $role = $doc->createElement('role');
            $role = $root->appendChild($role);

            $name = $doc->createElement('name');
            $name = $role->appendChild($name);

            $nameValue = $doc->createTextNode($r['name']);
            $nameValue = $name->appendChild($nameValue);

            $inherit = $doc->createElement('inherit');
            $inherit = $role->appendChild($inherit);

            $inheritValue = $doc->createTextNode($r['inherit']);
            $inheritValue = $inherit->appendChild($inheritValue);

            $editable = $doc->createElement('editable');
            $editable = $role->appendChild($editable);

            $editableValue = $doc->createTextNode($r['editable']);
            $editableValue = $editable->appendChild($editableValue);

            $allows = $doc->createElement('allows');
            $allows = $role->appendChild($allows);

            foreach ($r['allows'] as $a) {
                $allow = $doc->createElement('allow');
                $allow = $allows->appendChild($allow);

                $resource = $doc->createElement('resource');
                $resource = $allow->appendChild($resource);

                $resourceValue = $doc->createTextNode($a['resource']);
                $resourceValue = $resource->appendChild($resourceValue);

                $privilege = $doc->createElement('privilege');
                $privilege = $allow->appendChild($privilege);

                $privilegeValue = $doc->createTextNode($a['privilege']);
                $privilegeValue = $privilege->appendChild($privilegeValue);
            }

            $denys = $doc->createElement('denys');
            $denys = $role->appendChild($denys);

            foreach ($r['denys'] as $d) {
                $deny = $doc->createElement('deny');
                $deny = $denys->appendChild($deny);

                $resource = $doc->createElement('resource');
                $resource = $deny->appendChild($resource);

                $resourceValue = $doc->createTextNode($d['resource']);
                $resourceValue = $resource->appendChild($resourceValue);

                $privilege = $doc->createElement('privilege');
                $privilege = $deny->appendChild($privilege);

                $privilegeValue = $doc->createTextNode($d['privilege']);
                $privilegeValue = $privilege->appendChild($privilegeValue);
            }

        }

        $config = Zend_Registry::get('config');
        $doc->save($config->aclConfigFile);
    }
}