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
 * @subpackage Workshop_LocationController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with workshop locations
 *
 * @package    Classmate
 * @subpackage Workshop_LocationController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_LocationController extends Internal_Controller_Action 
{	
    /**
     * Allows a user to view the list of locations.
     *
     */
    public function indexAction()
    {
        $this->view->title = "Our Teaching Labs";      
        
        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'viewDisabled' => $this->_acl->isAllowed($this->_role, $this->_resource, 'viewDisabled'),
            );
            
        $location = new Location();
            
        $get = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $where = null;

        $locations = $location->fetchAll($where, 'name')->toArray();
        
        $this->view->locations = $locations;       
    }
    
    /**
     * If a user has access to this action, they can view the locations that are
     * disabled.
     *
     */    
    public function viewDisabledAction()
    {}

    /**
     * Allows a user to view the details of a location.
     *
     */
    public function detailsAction()
    {
    	$get = Zend_Registry::get('get');
    	$filter = Zend_Registry::get('inputFilter');
    	
    	if (!isset($get['locationId'])) {
    		throw new Internal_Exception_Input('Location ID not found in query string');
    	}
    	
    	$locationId = $filter->filter($get['locationId']);
    	
    	$location = new Location();
    	
    	$thisLocation = $location->find($locationId);
    	
    	if (is_null($thisLocation)) {
    		throw new Internal_Exception_Data('Location not found');
    	}
    	
        $this->view->acl = array(
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            );    	
    	
        $this->view->useInlineEditor = true;
    	$this->view->hideTitle = true;
    	$this->view->location = $thisLocation->toArray();
    	$this->view->title = 'Location details: ' . $thisLocation->name;
    	
    	
    }
    
    /**
     * Allows a user to add a location.
     *
     */
    public function addAction()
    {
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            $htmlFilter = Zend_Registry::get('htmlFilter');
            
            $data = array(
                        'name'        => $filter->filter($post['name']),
                        'capacity'    => $filter->filter($post['capacity']),
                        'description' => $htmlFilter->filter($post['description']),
                        'address'     => $filter->filter($post['address']),
                    );
            
            $location = new Location();
            
            $locationId = $location->insert($data);
                    
            $trigger = new EmailTrigger();
            $data['userId'] = Zend_Auth::getInstance()->getIdentity();
            $trigger->setVariables($data);
            $trigger->dispatch('Location_Add');
            
            $this->_redirect('/workshop/location/details/?locationId=' . $locationId);
        }
        
        $this->view->title = "Add a Location";
        $this->view->javascript = array('tiny_mce/tiny_mce.js', 'tinyMceConfig.js');
    }
    
    /**
     * Allows a user to edit a location.
     *
     */
    public function editAction()
    {
        $this->_helper->getExistingHelper('viewRenderer')->setNeverRender();
        
        $editable = array('name', 'description', 'address', 'capacity', 'status');
        
        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            if (!isset($post['locationId'])) {
                echo 'location ID not set';
                return;
            }
            
            $locationId = $filter->filter($post['locationId']);
            
            if ($locationId == '') {
                echo 'location ID can not be blank';
                return;
            }
            
            $data = array(
                'locationId' => $locationId,
            );
            
            $htmlFilter = Zend_Registry::get('htmlFilter');
            
            foreach ($editable as $e) {
                if (isset($post[$e])) {
                    $data[$e] = $htmlFilter->filter($post[$e]);
                }
            }
            
            $location = new Location();
            $location->update($data, null);
            
            echo 'Location saved successfully';
            return;         
        }
    }
}