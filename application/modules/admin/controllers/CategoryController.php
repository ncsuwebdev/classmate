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
 * @subpackage Admin_CategoryController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: LogController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */

class Admin_CategoryController extends Internal_Controller_Action 
{
    
	/**
	 * Shows all workshop categories
	 *
	 */
    public function indexAction()
    {
        $this->view->title = "Workshop Category Management";      
        
        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            );
            
        $wc = new WorkshopCategory();
            
        $wcs = $wc->fetchAll()->toArray();
        
        $this->view->wcs = $wcs;       
    }
    
    /**
     * Add a new workshop category
     *
     */
    public function addAction()
    {
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            $data = array(
                        'name'        => $filter->filter($post['name']),
                        'description' => $filter->filter($post['description'])
                    );
            
            $wc = new WorkshopCategory();
            
            $files = array('largeIcon', 'smallIcon');
            
            foreach ($files as $f) {
                if ($_FILES[$f]['name'] != '') {
    
                     $image = new Image;
    
                     if ($f == 'largeIcon') {
                         $image->resizeImage($filter->filter($_FILES[$f]['tmp_name']), 32, 32);
                     } else {
                         $image->resizeImage($filter->filter($_FILES[$f]['tmp_name']), 16, 16);
                     }
    
                     $iData = array(
                        'source'      => file_get_contents($filter->filter($_FILES[$f]['tmp_name'])),
                        'alt'         => $f,
                        'contentType' => $filter->filter($_FILES[$f]['type']),
                        'name'        => $filter->filter($_FILES[$f]['name']),
                        );
    
                     $image->insert($iData);
    
                     $data[$f . 'ImageId'] = $image->getAdapter()->lastInsertId();
                }   
            }               
            
            $wc->insert($data);
            
            $this->_redirect('/admin/category/index');
        }
        
        $this->view->title = "Add a Location";
    }
    
    /**
     * edit a category
     *
     */
    public function editAction()
    {
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            $wc = new WorkshopCategory();
            
            $thisCategory = $wc->find($filter->filter($post['workshopCategoryId']))->toArray();
            
            $data = array(
                        'workshopCategoryId'  => $filter->filter($post['workshopCategoryId']),
                        'name'        => $filter->filter($post['name']),
                        'description' => $filter->filter($post['description'])
                    );

            $files = array('largeIcon', 'smallIcon');
            
            foreach ($files as $f) {
	            if ($_FILES[$f]['name'] != '') {
	
	                 $image = new Image;
	
	                 if ($f == 'largeIcon') {
	                     $image->resizeImage($filter->filter($_FILES[$f]['tmp_name']), 32, 32);
	                 } else {
	                 	 $image->resizeImage($filter->filter($_FILES[$f]['tmp_name']), 16, 16);
	                 }
	
	                 $iData = array(
	                    'source'      => file_get_contents($filter->filter($_FILES[$f]['tmp_name'])),
	                    'alt'         => $f,
	                    'contentType' => $filter->filter($_FILES[$f]['type']),
	                    'name'        => $filter->filter($_FILES[$f]['name']),
	                    );
	
	
	                 if (isset($thisCategory[$f . 'ImageId']) && $thisCategory[$f . 'ImageId'] != 0) {
	                     $image->deleteImage($thisCategory[$f . 'ImageId']);
	                 }
	
	                 $image->insert($iData);
	
	                 $data[$f . 'ImageId'] = $image->getAdapter()->lastInsertId();
	            }   
            }              
            
            $wc->update($data, null);
            
            $this->_redirect('/admin/category/index');
            
        } else {
        
            $get    = Zend_Registry::get('get');
            $filter = Zend_Registry::get('inputFilter');
            
            $wcId = $filter->filter($get['workshopCategoryId']);
            
            $wc = new WorkshopCategory();
            
            $l = $wc->find($wcId)->toArray();
            
            $this->view->title = "Edit " . $l['name'];
            
            $this->view->workshopCategory = $l;
        }
    }
    
    /**
     * Delete a category
     *
     */
    public function deleteAction()
    {       
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $wcId = $filter->filter($get['workshopCategoryId']);
        
        $wc = new WorkshopCategory();
        
        $where = $wc->getAdapter()->quoteInto('workshopCategoryId = ?', $wcId);
        $wc->delete($where);
        
        $this->_redirect('/admin/category/index');
    }
       
}