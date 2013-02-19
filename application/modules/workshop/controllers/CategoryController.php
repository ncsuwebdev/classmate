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
 * @package    Workshop_EvaluateController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with evalutaions
 *
 * @package    Workshop_EvaluateController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
require_once(APPLICATION_PATH . '/models/Workshop/Category.php');

class WorkshopCategoryController extends Zend_Controller_Action 
{
    
    /**
     * Allows a user to view the list of workshop categories
     *
     */
    public function indexAction() {
        $this->view->acl = array(
            'add'          => $this->_helper->hasAccess('add'),
            'edit'         => $this->_helper->hasAccess('edit'),
            );
            
        $category = new Category();

        $categories = $category->fetchAll(null, 'name');

        $this->view->categories = $categories;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
        $this->_helper->pageTitle('workshop-category-index:title');
    }
    
    /**
     * Allows a user to view the details of workshop category
     *
     */
    public function detailsAction() {
        $this->view->acl = array(
            'edit'   => $this->_helper->hasAccess('edit'),
            'delete' => $this->_helper->hasAccess('delete')
        );      
        
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->categoryId)) {
            throw new Ot_Exception_Input('msg-error-categoryIdNotSet');
        }
        
        $category = new Category();
        
        $thisCategory = $category->find($get->categoryId);
        
        if (is_null($thisCategory)) {
            throw new Ot_Exception_Data('msg-error-category');
        }
        
        $this->view->category = $thisCategory;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
        $this->_helper->pageTitle("workshop-category-details:title", $thisCategory->name);
    }
    
    /**
     * Allows a user to add a workshop category
     *
     */
    public function addAction()
    {
        $messages = array();
        $category = new Category();
        
        $form = $category->form();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
            
                $data = array(
                            'name'        => $form->getValue('name'),
                            'description' => $form->getValue('description'),
                        );
                
                $categoryId = $category->insert($data);
                
                $trigger = new Ot_Trigger();
                $data['accountId'] = Zend_Auth::getInstance()->getIdentity()->accountId;
                $trigger->setVariables($data);
                $trigger->dispatch('Category_Add');
    
                $this->_helper->flashMessenger->addMessage('msg-info-categoryAdded');
                
                $this->_helper->redirector->gotoUrl('/workshop/category/details/?categoryId=' . $categoryId);
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form     = $form;
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.wysiwyg.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.wysiwyg.css');
        
        $this->_helper->pageTitle('workshop-category-add:title');
    }
    
    /**
     * Allows a user to edit a workshop category
     *
     */
    public function editAction()
    {
        $messages = array();
        
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->categoryId)) {
            throw new Ot_Exception_Input('msg-error-categoryIdNotSet');
        }      
        
        $category = new Category();
        
        $thisCategory = $category->find($get->categoryId);
        
        if (is_null($thisCategory)) {
            throw new Ot_Exception_Data('msg-error-category');
        }       
        
        $form = $category->form($thisCategory->toArray());
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
            
                $data = array(
                            'categoryId'  => $form->getValue('categoryId'),
                            'name'        => $form->getValue('name'),
                            'description' => $form->getValue('description')
                        );
                
                $category->update($data, null);
    
                $trigger = new Ot_Trigger();
                $data['accountId'] = Zend_Auth::getInstance()->getIdentity()->accountId;
                $trigger->setVariables($data);
                $trigger->dispatch('Category_Edit');
                
                $this->_helper->flashMessenger->addMessage('msg-info-categoryModified');
                
                $this->_helper->redirector->gotoUrl('/workshop/category/details/?categoryId=' . $form->getValue('categoryId'));
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form     = $form;
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.wysiwyg.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.wysiwyg.css');
        
        $this->_helper->pageTitle('workshop-category-edit:title');
    }
    
    /**
     * Allows a user to delete a workshop category
     *
     */
    public function deleteAction()
    {       
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->categoryId)) {
            throw new Ot_Exception_Input('msg-error-categoryIdNotSet');
        }
        
        $category = new Category();
        
        $thisCategory = $category->find($get->categoryId);
        
        if (is_null($thisCategory)) {
            throw new Ot_Exception_Data('msg-error-category');        
        }
        
        $this->view->category = $thisCategory;
        
        $form = Ot_Form_Template::delete('deleteForm'); 
        
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $where = $category->getAdapter()->quoteInto('categoryId = ?', $get->categoryId);
            $category->delete($where);
            
            $this->_helper->flashMessenger->addMessage('msg-info-categoryDeleted');
            
            $this->_helper->redirector->gotoUrl('/workshop/category/index');
        }
        
        $this->_helper->pageTitle('workshop-category-delete:title');
        $this->view->form = $form;
    } 
}