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
     * Auth adapter
     *
     * @var Zend_Auth_Adapter
     */
    protected $_authAdapter = null;

    /**
     * Runs when the class is initialized.  Sets up the view instance and the
     * various models used in the class.
     *
     */
    public function init()
    {
        $config = Zend_Registry::get('config');

        $this->_authzAdapter = new $config->authorization(Zend_Auth::getInstance()->getIdentity());
        $this->_authAdapter  = new $config->authentication();
        
        parent::init();
    }


    /**
     * Displays all users with access to the system
     *
     */
    public function indexAction()
    {
        $users = $this->_authzAdapter->getUsers();

        if (!$this->_authzAdapter->manageLocally() && !$this->_authAdapter->manageLocally()) {
            $this->view->acl = array(
                'add'    => false,
                'edit'   => false,
                'delete' => false,
                'log'    => $this->_acl->isAllowed($this->_role, 'admin_log', 'index'),
                );
        } else {
            $this->view->acl = array(
                'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
                'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
                'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
                'log'    => $this->_acl->isAllowed($this->_role, 'admin_log', 'index'),
                );
        }

        if (count($users) != 0) {
            $this->view->javascript = 'sortable.js';
        }

        $this->view->title = "Manage Users";
        $this->view->users = $users;
    }

    /**
     * Adds a user to the system
     *
     */
    public function addAction()
    {
        if (!$this->_authAdapter->manageLocally() && !$this->_authzAdapter->manageLocally()) {
            throw new Internal_Exception_Data(
                'The authentication adapter provided is using an external source ' .
                'to manage user lists, meaning this application can not manage ' .
                'the lists locally.');
        }

        $roles = $this->_acl->getAvailableRoles();

        $temp = array();
        foreach ($roles as $r) {
            $temp[$r['name']] = $r['name'];
        }

        $roles = $temp;

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');

            if ($this->_authAdapter->manageLocally()) {
                if ($this->_authzAdapter->manageLocally()) {
                    throw new Internal_Exception_Data('Autorization Adapter is not supported currently');
                } else {
                    throw new Internal_Exception_Data('Authentication Adapter is not supported currently');
                }
            } else {
                $userId = $filter->filter($post['userId']);
                $role   = $filter->filter($post['role']);

                if (!in_array($role, $roles)) {
                    throw new Internal_Exception_Input("The role '$role' is not a valid role");
                }

                if ($userId == '') {
                    throw new Internal_Exception_Input('User ID is required');
                }

                $this->_authzAdapter->addUser($userId, $role);

                $this->_logger->setEventItem('attributeName', 'userId');
                $this->_logger->setEventItem('attributeId', $userId);
                $this->_logger->info('Account was added for ' . $userId . '.');

                $this->_redirect('/admin/user');
            }

        } else {
            $this->view->title = 'Add User';
            $this->view->roles = $roles;

            if ($this->_authAdapter->manageLocally()) {
                if ($this->_authzAdapter->manageLocally()) {
                    throw new Internal_Exception_Data('Autorization Adapter is not supported currently');
                } else {
                    throw new Internal_Exception_Data('Authentication Adapter is not supported currently');
                }
            }
        }
    }

    /**
     * Edits an existing user
     *
     */
    public function editAction()
    {
        if (!$this->_authAdapter->manageLocally() && !$this->_authzAdapter->manageLocally()) {
            throw new Internal_Exception_Data(
                'The authentication adapter provided is using an external source ' .
                'to manage user lists, meaning this application can not manage ' .
                'the lists locally.');
        }

        $roles = $this->_acl->getAvailableRoles();

        $temp = array();
        foreach ($roles as $r) {
            $temp[$r['name']] = $r['name'];
        }

        $roles = $temp;

        $filter = Zend_Registry::get('inputFilter');

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

            $post = Zend_Registry::get('post');

            if ($this->_authAdapter->manageLocally()) {
                if ($this->_authzAdapter->manageLocally()) {
                    throw new Internal_Exception_Data('Autorization Adapter is not supported currently');
                } else {
                    throw new Internal_Exception_Data('Authentication Adapter is not supported currently');
                }
            } else {
                $userId = $filter->filter($post['userId']);
                $role   = $filter->filter($post['role']);

                if (!in_array($role, $roles)) {
                    throw new Internal_Exception_Input("The role '$role' is not a valid role");
                }

                if ($userId == '') {
                    throw new Internal_Exception_Input('User ID is required');
                }

                $this->_authzAdapter->editUser($userId, $role);

                $this->_logger->setEventItem('attributeName', 'userId');
                $this->_logger->setEventItem('attributeId', $userId);
                $this->_logger->info('Account was modified for ' . $userId . '.');

                $this->_redirect('/admin/user');
            }

        } else {
            $get = Zend_Registry::get('get');

            $userId = $filter->filter($get['userId']);

            $user = $this->_authzAdapter->getUser($userId);

            $this->view->userId = $userId;
            $this->view->role   = $user['role'];
            $this->view->title  = 'Edit User';
            $this->view->roles  = $roles;

            if ($this->_authAdapter->manageLocally()) {
                if ($this->_authzAdapter->manageLocally()) {
                    throw new Internal_Exception_Data('Autorization Adapter is not supported currently');
                } else {
                    throw new Internal_Exception_Data('Authentication Adapter is not supported currently');
                }
            }
        }
    }

    /**
     * Deletes a user
     *
     */
    public function deleteAction()
    {
        if (!$this->_authAdapter->manageLocally() && !$this->_authzAdapter->manageLocally()) {
            throw new Internal_Exception_Data(
                'The authentication adapter provided is using an external source ' .
                'to manage user lists, meaning this application can not manage ' .
                'the lists locally.');
        }

        $filter = Zend_Registry::get('inputFilter');

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');

            if ($this->_authAdapter->manageLocally()) {
                if ($this->_authzAdapter->manageLocally()) {
                    throw new Internal_Exception_Data('Autorization Adapter is not supported currently');
                } else {
                    throw new Internal_Exception_Data('Authentication Adapter is not supported currently');
                }
            } else {
                $userId = $filter->filter($post['userId']);

                if ($userId == '') {
                    throw new Internal_Exception_Input('User ID is required');
                }

                $this->_authzAdapter->deleteUser($userId);

                $this->_logger->setEventItem('attributeName', 'userId');
                $this->_logger->setEventItem('attributeId', $userId);
                $this->_logger->info('Account was deleted for ' . $userId . '.');
            }

            $this->_redirect('/admin/user');

        } else {

            $get = Zend_Registry::get('get');

            $userId = $filter->filter($get['userId']);

            $user = $this->_authzAdapter->getUser($userId);

            $this->view->userId = $userId;
            $this->view->role   = $user['role'];
            $this->view->title  = 'Delete User';

            if ($this->_authAdapter->manageLocally()) {
                if ($this->_authzAdapter->manageLocally()) {
                    throw new Internal_Exception_Data('Autorization Adapter is not supported currently');
                } else {
                    throw new Internal_Exception_Data('Authentication Adapter is not supported currently');
                }
            }
        }
    }
}