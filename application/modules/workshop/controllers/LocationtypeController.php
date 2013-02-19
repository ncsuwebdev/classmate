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
class Workshop_LocationtypeController extends Zend_Controller_Action 
{    
    
    /**
     * Allows a user to view the list of locations type
     *
     */
    public function indexAction()
    {   
        $this->view->acl = array(
            'add'          => $this->_helper->hasAccess('add'),
            'edit'         => $this->_helper->hasAccess('edit'),
            );
            
        $locationType = new App_Model_DbTable_LocationType();

        $locationTypes = $locationType->fetchAll(null, 'name');

        $this->view->locationTypes = $locationTypes;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
        $this->_helper->pageTitle('workshop-location-type-index:Title');
    }
    
    /**
     * Allows a user to view the details of a location type
     *
     */
    public function detailsAction()
    {
        $this->view->acl = array(
                    'edit'   => $this->_helper->hasAccess('edit'),
                    'delete' => $this->_helper->hasAccess('delete')
                );      
        
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->typeId)) {
            throw new Ot_Exception_Input('msg-error-typeIdNotSet');
        }
        
        $locationType = new App_Model_DbTable_LocationType();
        
        $thisLocationType = $locationType->find($get->typeId);
        
        if (is_null($thisLocationType)) {
            throw new Ot_Exception_Data('msg-error-noLocationType');
        }
        
        $this->view->locationType = $thisLocationType;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
        $this->_helper->pageTitle("workshop-location-type-details:title", $thisLocationType->name);
    }
    
    /**
     * Allows a user to add a location type
     *
     */
    public function addAction()
    {
        $messages = array();
        $locationType = new App_Model_DbTable_LocationType();
        
        $form = $locationType->form();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
            
                $data = array(
                            'name'        => $form->getValue('name'),
                            'description' => $form->getValue('description'),
                        );
                
                $typeId = $locationType->insert($data);
                
                $trigger = new Ot_Trigger();
                $data['accountId'] = Zend_Auth::getInstance()->getIdentity()->accountId;
                $trigger->setVariables($data);
                $trigger->dispatch('LocationType_Add');
    
                $this->_helper->flashMessenger->addMessage('msg-info-locationTypeAdded');
                
                $this->_helper->redirector->gotoUrl('/workshop/locationType/details/?typeId=' . $typeId);
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form     = $form;
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.wysiwyg.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.wysiwyg.css');
        
        $this->_helper->pageTitle('workshop-location-type-add:Title');
    }
    
    /**
     * Allows a user to edit a location type
     *
     */
    public function editAction()
    {
        $messages = array();
        
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->typeId)) {
            throw new Ot_Exception_Input('msg-error-typeIdNotSet');
        }      
        
        $locationType = new App_Model_DbTable_LocationType();
        
        $thisLocationType = $locationType->find($get->typeId);
        
        if (is_null($thisLocationType)) {
            throw new Ot_Exception_Data('msg-error-noLocationType');
        }       
        
        $form = $locationType->form($thisLocationType->toArray());
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
            
                $data = array(
                            'typeId'      => $form->getValue('typeId'),
                            'name'        => $form->getValue('name'),
                            'description' => $form->getValue('description')
                        );
                
                $locationType->update($data, null);
    
                $trigger = new Ot_Trigger();
                $data['accountId'] = Zend_Auth::getInstance()->getIdentity()->accountId;
                $trigger->setVariables($data);
                $trigger->dispatch('LocationType_Edit');
                
                $this->_helper->flashMessenger->addMessage('msg-info-locationTypeModified');
                
                $this->_helper->redirector->gotoUrl('/workshop/locationType/details/?typeId=' . $form->getValue('typeId'));
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form     = $form;
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/workshop/location-type/edit.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.wysiwyg.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.wysiwyg.css');
        
        $this->_helper->pageTitle('workshop-location-edit:title');
    }
    
    /**
     * Allows a user to delete a location type
     *
     */
    public function deleteAction()
    {       
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->typeId)) {
            throw new Ot_Exception_Input('msg-error-typeIdNotSet');
        }
        
        $locationType = new App_Model_DbTable_LocationType();
        
        $thisLocationType = $locationType->find($get->typeId);
        
        if (is_null($thisLocationType)) {
            throw new Ot_Exception_Data('msg-error-noLocationType');        
        }
        
        $this->view->locationType = $thisLocationType;
        
        $form = Ot_Form_Template::delete('deleteForm'); 
        
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $where = $locationType->getAdapter()->quoteInto('typeId = ?', $get->typeId);
            $locationType->delete($where);
            
            $this->_helper->flashMessenger->addMessage('msg-info-locationTypeDeleted');
            
            $this->_helper->redirector->gotoUrl('/workshop/locationType/index');
        }
        
        $this->_helper->pageTitle('workshop-location-type-delete:title');
        $this->view->form = $form;
    } 
}