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
 * @subpackage Admin_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: IndexController.php 157 2007-07-20 13:32:41Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Index controller for the Admin tab
 *
 * @package    Aerial (Admin)
 * @subpackage Admin_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Admin_ApiController extends Internal_Controller_Action 
{
    /**
     * Runs when the class is initialized.  Sets up the view instance and the
     * various models used in the class.
     *
     */
    public function init()
    {
        $zcf = Zend_Controller_Front::getInstance();

        $this->_acl      = $zcf->getParam('acl');
        $this->_role     = Itdcs_Authz::getInstance()->getRole();
        $this->_resource = strtolower($zcf->getRequest()->module . '_' . $zcf->getRequest()->controller);

        $this->_logger = Zend_Registry::get('logger');

    }


     /**
     * shows the homepage
     *
     */
    public function indexAction()
    {
        $this->view->title = 'Api Access';

        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            );

        $apiCode = new ApiCode();

        $codes = $apiCode->fetchAll(null, 'userId');
        if ($codes === false) {
            $this->printError('Error getting codes', $apiCode->getMessages());
        }

        $this->view->codes = $codes->toArray();
    }

    public function addAction()
    {
        $filter = Zend_Registry::get('inputFilter');

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');

            $apiCode = new ApiCode();

            $userId = $filter->filter($post['userId']);
            
            $data = array(
                'userId'       => $userId,
                );

            $result = $apiCode->insert($data);

            if ($result === false) {
                $this->printError('Error inserting code', $apiCode->getMessages());
            }

            $this->_logger->setEventItem('attributeName', 'apiCode');
            $this->_logger->setEventItem('attributeId', $data['userId']);
            $this->_logger->info('access code granted');
            
            $this->_redirect('/admin/api/');
            

        } else {
            $this->view->title = 'Add Api Code';
        }
    }

    
    public function deleteAction()
    {
        $filter = Zend_Registry::get('inputFilter');

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');

            $apiCode = new ApiCode();

            $userId = $filter->filter($post['userId']);

            $where = $apiCode->getAdapter()->quoteInto('userId = ?', $userId);
            
            $result = $apiCode->delete($where);

            if ($result === false) {
                $this->printError('Error deleting code', $apiCode->getMessages());
            }
            
            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $userId);
            $this->_logger->info('code removed');

            $this->_redirect('/admin/api/');

        } else {
            $get = Zend_Registry::get('get');

            if (!isset($get['userId'])) {
                $this->printError('Error getting code', 'Link ID not found in query string');
            }

            $this->view->userId = $filter->filter($get['userId']);

            $this->view->title = 'Delete Api Code Link';

        }
    }    
}