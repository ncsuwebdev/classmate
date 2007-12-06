<?php
/**
 * ClassMate
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
 * @package    ClassMate (Admin)
 * @subpackage Admin_LocationController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: LogController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */

class Admin_LocationController extends Internal_Controller_Action 
{
    
    public function indexAction()
    {
        $this->view->title = "Location Management";      
        
        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            );
            
        $location = new Location();
            
        $locations = $location->fetchAll()->toArray();
        
        $this->view->locations = $locations;       
    }
    
    public function addAction()
    {
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            $data = array(
                        'name'        => $filter->filter($post['name']),
                        'capacity'    => $filter->filter($post['capacity']),
                        'description' => $filter->filter($post['description'])
                    );
            
            $location = new Location();
            
            $location->insert($data);
            
            $this->_redirect('/admin/location/index');
        }
        
        $this->view->title = "Add a Location";
    }
    
    public function editAction()
    {
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            $data = array(
                        'locationId'  => $filter->filter($post['locationId']),
                        'name'        => $filter->filter($post['name']),
                        'capacity'    => $filter->filter($post['capacity']),
                        'description' => $filter->filter($post['description'])
                    );
            
            $location = new Location();
            
            $location->update($data, null);
            
            $this->_redirect('/admin/location/index');
            
        } else {
        
            $get    = Zend_Registry::get('get');
            $filter = Zend_Registry::get('inputFilter');
            
            $locationId = $filter->filter($get['locationId']);
            
            $location = new Location();
            
            $l = $location->find($locationId)->toArray();
            
            $this->view->title = "Edit " . $l['name'];
            
            $this->view->location = $l;
        }
    }
    
    public function deleteAction()
    {       
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $locationId = $filter->filter($get['locationId']);
        
        $location = new Location();
        
        $where = $location->getAdapter()->quoteInto('locationId = ?', $locationId);
        $location->delete($where);
        
        $this->_redirect('/admin/location/index');
    }
       
}