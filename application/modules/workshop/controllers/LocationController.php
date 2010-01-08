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
 * @package    Workshop_LocationController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with workshop locations
 *
 * @package    Workshop_LocationController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_LocationController extends Zend_Controller_Action 
{	
    /**
     * Allows a user to view the list of locations.
     *
     */
    public function indexAction()
    {   
        $this->view->acl = array(
            'add'          => $this->_helper->hasAccess('add'),
            'edit'         => $this->_helper->hasAccess('edit'),
            'viewDisabled' => $this->_helper->hasAccess('view-disabled')
            );
            
        $location = new Location();

        $locations = $location->fetchAll(null, 'name');

        $this->view->locations = $locations;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
        $this->_helper->pageTitle('workshop-location-index:title');
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
        $this->view->acl = array(
                    'edit'   => $this->_helper->hasAccess('edit'),
                    'delete' => $this->_helper->hasAccess('delete')
                );      
        
    	$get = Zend_Registry::get('getFilter');
    	
    	if (!isset($get->locationId)) {
    		throw new Ot_Exception_Input('msg-error-locationIdNotSet');
    	}
    	
    	$location = new Location();
    	
    	$thisLocation = $location->find($get->locationId);
    	
    	if (is_null($thisLocation)) {
    		throw new Ot_Exception_Data('msg-error-noLocation');
    	}
    	
    	$this->view->location = $thisLocation;
    	$this->view->messages = $this->_helper->flashMessenger->getMessages();
    	
    	$this->_helper->pageTitle("workshop-location-details:title", $thisLocation->name);
    }
    
    /**
     * Allows a user to add a location.
     *
     */
    public function addAction()
    {
        $messages = array();
        $location = new Location();
        
        $form = $location->form();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
            
                $data = array(
                            'name'        => $form->getValue('name'),
                            'capacity'    => $form->getValue('capacity'),
                            'description' => $form->getValue('description'),
                            'address'     => $form->getValue('address'),
                            'status'      => $form->getValue('status')
                        );
                
                $locationId = $location->insert($data);
    
                $trigger = new Ot_Trigger();
                $data['accountId'] = Zend_Auth::getInstance()->getIdentity()->accountId;
                $trigger->setVariables($data);
                $trigger->dispatch('Location_Add');
                
                $this->_helper->flashMessenger->addMessage('msg-info-locationAdded');
                
                $this->_helper->redirector->gotoUrl('/workshop/location/details/?locationId=' . $locationId);
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form     = $form;
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.wysiwyg.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.wysiwyg.css');
        
        $this->_helper->pageTitle('workshop-location-add:title');
    }
    
    /**
     * Allows a user to edit a location.
     *
     */
    public function editAction()
    {
        $messages = array();
        
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->locationId)) {
            throw new Ot_Exception_Input('msg-error-locationIdNotSet');
        }      
        
        $location = new Location();
        
        $thisLocation = $location->find($get->locationId);
        
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }       
        
        $form = $location->form($thisLocation->toArray());
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
            
                $data = array(
                            'locationId'  => $form->getValue('locationId'),
                            'name'        => $form->getValue('name'),
                            'capacity'    => $form->getValue('capacity'),
                            'description' => $form->getValue('description'),
                            'address'     => $form->getValue('address'),
                            'status'      => $form->getValue('status')
                        );
                
                $location->update($data, null);
    
                $trigger = new Ot_Trigger();
                $data['accountId'] = Zend_Auth::getInstance()->getIdentity()->accountId;
                $trigger->setVariables($data);
                $trigger->dispatch('Location_Edit');
                
                $this->_helper->flashMessenger->addMessage('msg-info-locationModified');
                
                $this->_helper->redirector->gotoUrl('/workshop/location/details/?locationId=' . $form->getValue('locationId'));
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form     = $form;
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.wysiwyg.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.wysiwyg.css');
        
        $this->_helper->pageTitle('workshop-location-edit:title');
    }
    
    /**
     * Allows a user to delete a location
     *
     */
    public function deleteAction()
    {       
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->locationId)) {
            throw new Ot_Exception_Input('msg-error-locationIdNotSet');
        }
        
        $location = new Location();
        
        $thisLocation = $location->find($get->locationId);
        
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');        
        }
        
        $this->view->location = $thisLocation;
        
        $form = Ot_Form_Template::delete('deleteForm'); 
        
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $where = $location->getAdapter()->quoteInto('locationId = ?', $get->locationId);
            $location->delete($where);
            
            $this->_helper->flashMessenger->addMessage('msg-info-locationDeleted');
            
            $this->_helper->redirector->gotoUrl('/workshop/location/index');
        }
        
        $this->_helper->pageTitle('workshop-location-delete:title');
        $this->view->form = $form;
    } 
}