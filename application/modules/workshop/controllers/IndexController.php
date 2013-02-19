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
 * @package    Workshop_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with workshops
 *
 * @package    Workshop_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
require_once(APPLICATION_PATH . '/models/Workshop/Category.php');

class Workshop_IndexController extends Zend_Controller_Action 
{    
    /**
     * The main workshop page.  It has the list of all the workshops that are 
     * available in the system.
     *
     */
    public function indexAction()
    {   
        $this->view->acl = array(
                               'workshopList' => $this->_helper->hasAccess('workshop-list')
                           );
         
        $get = Zend_Registry::get('getFilter');
        
        $form = new Zend_Form();
        $form->setAttrib('id', 'workshopForm')
             ->setMethod(Zend_Form::METHOD_GET)
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'filterForm')),
                     'Form',
             ));
             
        $searchField = $form->createElement('text', 'search', array('label' => 'workshop-index-index:searchWorkshops'));
        $searchField->setRequired(false)
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->setValue((isset($get->search) ? $get->search : ''));
              
        $category = new Category();
        $categoryList = $category->fetchAll(null, 'name');
        
        $categories = $form->createElement('select', 'categoryId');
        $categories->addMultiOption('', '-- Search By Category -- ');
        foreach($categoryList as $c) {
            $categories->addMultiOption($c['categoryId'], $c['name']);
        }
        
        $categories->setValue(isset($get->categoryId) ? $get->categoryId : '');
                    
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'workshop-index-index:search'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));
        
        $form->addElements(array($searchField, $categories));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit));        

        $this->view->form = $form;
        
        $searchTerm = new App_Model_DbTable_SearchTerm();
        $workshops = array();
        
        if ($get->search != '' || $get->categoryId != 0) {
            $workshop = new App_Model_DbTable_Workshop();
            
            $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
            
            if($get->search != '') {
                $query->addTerm(new Zend_Search_Lucene_Index_Term($get->search), true);
            }
            
            if($get->categoryId != 0) {
                $query->addTerm(new Zend_Search_Lucene_Index_Term($get->categoryId, 'categoryId'), true);
            }
            
            $workshops = $workshop->search($query);

            $searchTerm->increment($get->search);
            
            $this->view->searchTerm = $get->search;
        }  

        $this->view->workshops = $workshops;
        
        $this->view->topTerms = $searchTerm->getTopSearchTerms(10);
        
        
        $this->view->layout()->setLayout('search');
        $this->view->layout()->rightContent = $this->view->render('index/top-terms.phtml');
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.autocomplete.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.autocomplete.css');        
        $this->_helper->pageTitle("workshop-index-index:title");
    }
    
    public function workshopListAction()
    {
        $this->view->acl = array(
            'viewDisabled' => $this->_helper->hasAccess('view-disabled')
        );
        
        $workshop = new App_Model_DbTable_Workshop();
        
        $where = null;
        if (!$this->_helper->hasAccess('view-disabled')) {
            $where = $workshop->getAdapter()->quoteInto('status = ?', 'enabled');   
        }
        
        $workshops = $workshop->fetchAll($where, array('status', 'title ASC'))->toArray();
        
        $this->view->workshops = $workshops;
        
        $this->_helper->pageTitle("workshop-index-workshopList:title");
    }
    
    /**
     * Access to this function is needed in order for a user to see the workshops
     * that are disabled. 
     *
     */
    public function viewDisabledAction()
    {}
    
    
    /**
     * The function to add a new App_Model_DbTable_Workshop to the system.
     *
     */
    public function addAction()
    {       
        $workshop = new App_Model_DbTable_Workshop();
        $tag      = new App_Model_DbTable_Tag();
        $we       = new App_Model_DbTable_WorkshopEditor();
        
        $form = $workshop->form(); 
                
        $messages = array();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
            
                $data = array(
                    'status'        => $form->getValue('status'),
                    'title'         => $form->getValue('title'),
                    'description'   => $form->getValue('description'),
                    'prerequisites' => $form->getValue('prerequisites'),
                    'categoryId'    => $form->getValue('categoryId')
                );
                
                $workshopId = $workshop->insert($data);
                
                if ($form->getValue('tags') != '') {
                    $tags = explode(',', $form->getValue('tags'));     
                    
                    $tag->setTagsForAttribute('workshopId', $workshopId, $tags);   
                }

                $we->setEditorsForWorkshop($workshopId, $form->getValue('editors'));

                $logOptions = array(
                    'attributeName' => 'workshopId',
                    'attributeId'   => $workshopId,
                );
                    
                $this->_helper->log(Zend_Log::INFO, 'Workshop was added', $logOptions);
                                
                $this->_helper->flashMessenger->addMessage('msg-info-workshopAdded');
            
                if ($data['status'] == 'enabled') {
                    $workshop->index($workshopId);
                }
                
                $this->_helper->redirector->gotoUrl('/workshop/index/details/?workshopId=' . $workshopId);                 
                
                
            } else {
                $messages[] = 'msg-error-formSubmitProblem';
            }
        }
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.wysiwyg.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.autocomplete.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.wysiwyg.css');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.autocomplete.css');
                
        $this->_helper->pageTitle('workshop-index-add:title');
        
        $this->view->form = $form;
        $this->view->messages = $messages;
    }
    
    /**
     * Allows a user to view the details of a workshop
     *
     */
    public function detailsAction()
    {
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->workshopId)) {
            throw new Ot_Exception_Input('msg-error-workshopIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        
        $thisWorkshop = $workshop->find($get->workshopId);
        
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
                        
        $document = new App_Model_DbTable_WorkshopDocument();
        $this->view->documents = $document->getDocumentsForWorkshop($thisWorkshop->workshopId);
        
        $tag = new App_Model_DbTable_Tag();
        $this->view->tags = $tag->getTagsForAttribute('workshopId', $thisWorkshop->workshopId);
        
        $event = new App_Model_DbTable_Event();
        $events = $event->getEvents($thisWorkshop->workshopId, null, null, time(), null, 'open')->toArray();
        
        $auth = Zend_Auth::getInstance();
        
        foreach ($events as &$e) {
            if ($auth->hasIdentity()) {    
                $e['status'] = $event->getStatusOfUserForEvent($auth->getIdentity()->accountId, $e['eventId']);
            } else {
                $e['status'] = '';
            }
            $e['workshop'] = $thisWorkshop->toArray();
        }
        
        $this->view->events = $events;
        
        $wl = new App_Model_DbTable_WorkshopLink();
        $this->view->links = $wl->getLinksForWorkshop($thisWorkshop->workshopId)->toArray();
        
        $location = new App_Model_DbTable_Location();
        $locations = $location->fetchAll();
        
        $locs = array();
        foreach ($locations as $l) {
            $locs[$l->locationId] = $l->toArray();
        }
        
        $this->view->locations = $locs;
        
        $we = new App_Model_DbTable_WorkshopEditor();        
        
        $isEditor = false;        
        if ($this->_helper->hasAccess('edit-all-workshops')) {
            $isEditor = true;
        } elseif ($auth->hasIdentity()) {
            $isEditor = $we->isEditor($thisWorkshop->workshopId, $auth->getIdentity()->accountId);
        }
        
        $this->view->acl = array(
           'edit'           => $isEditor,
           'addDocuments'   => $isEditor,
           'editDocument'   => $isEditor,
           'deleteDocument' => $isEditor,
           'addLink'        => $isEditor,
           'deleteLink'     => $isEditor,
           'editLink'       => $isEditor,
           'reorderLink'    => $isEditor,
           'addEvent'       => $this->_helper->hasAccess('index', 'workshop_schedule'),
           'options'        => $this->_helper->hasAccess('options')
        );
        
        if ($this->view->acl['edit']) {

            $we = new App_Model_DbTable_WorkshopEditor();
            $where = $we->getAdapter()->quoteInto('workshopId = ?', $thisWorkshop->workshopId);
            $results = $we->fetchAll($where);
            
            $currentEditors = array();
            foreach ($results as $r) {
                $currentEditors[] = $r->accountId;
            }
            
            if (count($currentEditors) != 0) {
                $account = new Ot_Account();
                $accounts = $account->fetchAll($account->getAdapter()->quoteInto('accountId IN (?)', $currentEditors), array('lastName', 'firstName'));
                
                $currentEditors = $accounts->toArray();
            }
            
            $this->view->editors = $currentEditors;     

        }
        
        $category = new Category();
        $thisCategory = $category->find($thisWorkshop->categoryId);
        
        $this->view->layout()->setLayout('twocolumn');
        $this->view->layout()->rightContent = $this->view->render('index/right.phtml');
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->title    = $thisWorkshop->title;
        $this->view->workshop = $thisWorkshop->toArray();
        $this->view->category = $thisCategory;
    }
    
    /**
     * Allows a user to edit a workshop
     *
     */
    public function editAction()
    {
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->workshopId)) {
            throw new Ot_Exception_Input('msg-error-workshopIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        $tag      = new App_Model_DbTable_Tag();
        $we       = new App_Model_DbTable_WorkshopEditor();
        
        
        $thisWorkshop = $workshop->find($get->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        if (!$this->_helper->hasAccess('edit-all-workshops') && 
            !$we->isEditor($thisWorkshop->workshopId, Zend_Auth::getInstance()->getIdentity()->accountId)) {
            throw new Ot_Exception_Access('msg-error-noAccess');        
        }        
        
        $thisWorkshopArray = $thisWorkshop->toArray();
        $thisWorkshopArray['tags'] = array();
        
        $tags = $tag->getTagsForAttribute('workshopId', $thisWorkshop->workshopId);
        
        foreach ($tags as $t) {
            array_push($thisWorkshopArray['tags'], $t['name']);
        }
        
        $we = new App_Model_DbTable_WorkshopEditor();
        $where = $we->getAdapter()->quoteInto('workshopId = ?', $thisWorkshop->workshopId);
        $results = $we->fetchAll($where);
            
        $thisWorkshopArray['editors'] = array();
        foreach ($results as $r) {
            array_push($thisWorkshopArray['editors'], $r->accountId);
        }        
        
        $form = $workshop->form($thisWorkshopArray); 
                
        $messages = array();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
            
                $data = array(
                    'workshopId'    => $thisWorkshop->workshopId,
                    'status'        => $form->getValue('status'),
                    'title'         => $form->getValue('title'),
                    'description'   => $form->getValue('description'),
                    'prerequisites' => $form->getValue('prerequisites'),
                );
                
                $workshop->update($data, null);
                
                if ($form->getValue('tags') != '') {
                    $tags = explode(',', $form->getValue('tags'));     
                    
                    $tag->setTagsForAttribute('workshopId', $thisWorkshop->workshopId, $tags);   
                }

                $we->setEditorsForWorkshop($thisWorkshop->workshopId, $form->getValue('editors'));

                $logOptions = array(
                    'attributeName' => 'workshopId',
                    'attributeId'   => $thisWorkshop->workshopId,
                );
                    
                $this->_helper->log(Zend_Log::INFO, 'Workshop was modified', $logOptions);
                                
                $this->_helper->flashMessenger->addMessage('msg-info-workshopModified');
            
                if ($data['status'] == 'enabled') {
                    $workshop->index($thisWorkshop->workshopId);
                } else {
                    $workshop->deleteFromIndex($thisWorkshop->workshopId);
                }
                
                $this->_helper->redirector->gotoUrl('/workshop/index/details/?workshopId=' . $thisWorkshop->workshopId);                 
            } else {
                $messages[] = 'msg-error-formSubmitProblem';
            }
        }
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.wysiwyg.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.autocomplete.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.wysiwyg.css');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.autocomplete.css');
                
        $this->_helper->pageTitle('workshop-index-edit:title');
        
        $this->view->form = $form;
        $this->view->messages = $messages;
    }
    
    public function rebuildHandoutsZipAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNeverRender();
            
        $get = Zend_Registry::get('getFilter');
        if (!isset($get->workshopId)) {
            throw new Ot_Exception_Input('msg-error-workshopIdNotSet');
        }
        
        $document = new App_Model_DbTable_WorkshopDocument();

        $config = Zend_Registry::get('config');
            
        if (!is_writable($config->user->fileUploadPathWorkshop->val)) {
            throw new Ot_Exception_Access($this->view->translate('msg-error-targetDirNotWritable', $config->user->fileUploadPathWorkshop->val));
        }

        $document->rebuildZipFile($get->workshopId);                
    }

    public function addDocumentAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        } else {
            $this->_helper->pageTitle('workshop-index-addDocument:title');
        }
        
        $get = Zend_Registry::get('getFilter');
        if (!isset($get->workshopId)) {
            throw new Ot_Exception_Input('msg-error-workshopIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        $document = new App_Model_DbTable_WorkshopDocument();
        
        $thisWorkshop = $workshop->find($get->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $we = new App_Model_DbTable_WorkshopEditor();        
        if (!$this->_helper->hasAccess('edit-all-workshops') && 
            !$we->isEditor($thisWorkshop->workshopId, Zend_Auth::getInstance()->getIdentity()->accountId)) {
            throw new Ot_Exception_Access('msg-error-noAccess');        
        }        

        $config = Zend_Registry::get('config');
            
        if (!is_writable($config->user->fileUploadPathWorkshop->val)) {
            throw new Ot_Exception_Access($this->view->translate('msg-error-targetDirNotWritable', $config->user->fileUploadPathWorkshop->val));
        }

        $form = $document->form(array('workshopId' => $thisWorkshop->workshopId));
        $form->setAction($this->view->baseUrl() . '/workshop/index/add-document/?workshopId=' . $thisWorkshop->workshopId);
        
        $messages = array();
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {

                $fileInfo = $form->document->getFileInfo();
                $fileInfo = $fileInfo['document'];

                $data = array(
                    'workshopId'  => $thisWorkshop->workshopId,
                    'name'        => $fileInfo['name'],
                    'description' => $form->getValue('description'),
                    'type'        => $fileInfo['type'],
                    'uploadDt'    => time(),
                    'filesize'    => $fileInfo['size'],
                );
                
                $targetPath = $config->user->fileUploadPathWorkshop->val . '/' . $thisWorkshop->workshopId;
                
                if (!is_dir($targetPath)) {
                    mkdir($targetPath);
                }
                
                move_uploaded_file($fileInfo['tmp_name'], $targetPath . '/' . $data['name']);
                   
                $document->insert($data);
                
                $document->rebuildZipFile($thisWorkshop->workshopId);
                          
                $logOptions = array(
                    'attributeName' => 'workshopId',
                    'attributeId'   => $thisWorkshop->workshopId,
                );
                    
                $this->_helper->log(Zend_Log::INFO, 'Document was added', $logOptions);
                                
                $this->_helper->flashMessenger->addMessage('msg-info-documentAdded');
            
                $this->_helper->redirector->gotoUrl('/workshop/index/details/?workshopId=' . $thisWorkshop->workshopId);                
            } else {
                $messages[] = 'msg-error-formSubmitProblem';
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form     = $form;
        $this->view->workshop = $thisWorkshop->toArray();
    }
    
    public function editDocumentAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        } else {
            $this->_helper->pageTitle('workshop-index-editDocument');
        }
        
        $get = Zend_Registry::get('getFilter');
        if (!isset($get->workshopDocumentId)) {
            throw new Ot_Exception_Input('msg-error-workshopDocIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        $document = new App_Model_DbTable_WorkshopDocument();
        
        $thisDocument = $document->find($get->workshopDocumentId);
        if (is_null($thisDocument)) {
            throw new Ot_Exception_Data('msg-error-noDocument');
        }
        
        $thisWorkshop = $workshop->find($thisDocument->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $we = new App_Model_DbTable_WorkshopEditor();        
        if (!$this->_helper->hasAccess('edit-all-workshops') && 
            !$we->isEditor($thisWorkshop->workshopId, Zend_Auth::getInstance()->getIdentity()->accountId)) {
            throw new Ot_Exception_Access('msg-error-noAccess');        
        }              

        $config = Zend_Registry::get('config');
            
        if (!is_writable($config->user->fileUploadPathWorkshop->val)) {
            throw new Ot_Exception_Access($this->view->translate('msg-error-targetDirNotWritable', $config->user->fileUploadPathWorkshop->val));
        }
        
        $form = $document->form($thisDocument->toArray());
        $form->setAction($this->view->baseUrl() . '/workshop/index/edit-document/?workshopDocumentId=' . $thisDocument->workshopDocumentId);
        
        $messages = array();
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                
                $data = array(
                    'workshopDocumentId' => $thisDocument->workshopDocumentId,
                    'description'        => $form->getValue('description'),
                );
                
                $document->update($data, null);
                $document->rebuildZipFile($thisWorkshop->workshopId);
                            
                $logOptions = array(
                    'attributeName' => 'workshopId',
                    'attributeId'   => $thisWorkshop->workshopId,
                );
                    
                $this->_helper->log(Zend_Log::INFO, 'Document was modified', $logOptions);
                                    
                $this->_helper->flashMessenger->addMessage('msg-info-documentModified');
            
                $this->_helper->redirector->gotoUrl('/workshop/index/details/?workshopId=' . $thisWorkshop->workshopId);                
            } else {
                $messages = 'msg-error-formSubmitProblem';
            }
        }
        
        $this->view->messages = $messages; 
        $this->view->form     = $form;      
    }
    
    public function deleteDocumentAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        } else {
            $this->_helper->pageTitle('workshop-index-deleteDocument:title');
        }
        
        $get = Zend_Registry::get('getFilter');
        if (!isset($get->workshopDocumentId)) {
            throw new Ot_Exception_Input('msg-error-workshopDocIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        $document = new App_Model_DbTable_WorkshopDocument();
        
        $thisDocument = $document->find($get->workshopDocumentId);
        if (is_null($thisDocument)) {
            throw new Ot_Exception_Data('msg-error-noDocument');
        }
        
        $thisWorkshop = $workshop->find($thisDocument->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $we = new App_Model_DbTable_WorkshopEditor();        
        if (!$this->_helper->hasAccess('edit-all-workshops') && 
            !$we->isEditor($thisWorkshop->workshopId, Zend_Auth::getInstance()->getIdentity()->accountId)) {
            throw new Ot_Exception_Access('msg-error-noAccess');        
        }    

        $config = Zend_Registry::get('config');
            
        if (!is_writable($config->user->fileUploadPathWorkshop->val)) {
            throw new Ot_Exception_Access($this->view->translate('msg-error-targetDirNotWritable', $config->user->fileUploadPathWorkshop->val));
        }
                    
        $form = Ot_Form_Template::delete('deleteDocument');
        $form->setAction($this->view->baseUrl() . '/workshop/index/delete-document/?workshopDocumentId=' . $thisDocument->workshopDocumentId);
        
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            
            $target = $config->user->fileUploadPathWorkshop->val . '/' . $thisWorkshop->workshopId . '/' . $thisDocument->name;
            if (is_file($target)) {
                unlink($target);
            }
            
            $document->delete($document->getAdapter()->quoteInto('workshopDocumentId = ?', $thisDocument->workshopDocumentId));
            $document->rebuildZipFile($thisWorkshop->workshopId);
            
            $logOptions = array(
                 'attributeName' => 'workshopId',
                'attributeId'   => $thisWorkshop->workshopId,
            );
                    
            $this->_helper->log(Zend_Log::INFO, 'Document was deleted', $logOptions); 
                       
            $this->_helper->flashMessenger->addMessage('msg-info-documentDeleted');
            
            $this->_helper->redirector->gotoUrl('/workshop/index/details/?workshopId=' . $thisWorkshop->workshopId);
        }   

        $this->view->form = $form;
    }
    
    public function downloadHandoutsAction()
    {      
        
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->workshopId)) {
            throw new Ot_Exception_Input('msg-error-workshopIdsNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop;
        $document = new App_Model_DbTable_WorkshopDocument();
        
        $thisWorkshop = $workshop->find($get->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $documents = $document->getDocumentsForWorkshop($thisWorkshop->workshopId);
        
        if (count($documents) == 0) {
            throw new Ot_Exception_Data("msg-error-noHandouts");
        }
        
        $config = Zend_Registry::get('config');
            
        if (!is_readable($config->user->fileUploadPathWorkshop->val)) {
            throw new Ot_Exception_Data('msg-error-targetDirNotReadable');
        }
            
        $target = $config->user->fileUploadPathWorkshop->val . '/' . $thisWorkshop->workshopId . '/all_handouts.zip';
                
        $this->_helper->viewRenderer->setNeverRender();
        $this->view->layout()->disableLayout();

        header('Content-Type: application/octetstream');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; ' . 'filename="all_handouts.zip"');
            
        readfile($target);           
    }
    
    /**
     * Allows a user access to download a document from a workshop
     *
     */
    public function downloadDocumentAction()
    {
        $get = Zend_Registry::get('getFilter');
            
        if (!isset($get->workshopDocumentId)) {
            throw new Ot_Exception_Input('msg-error-workshopIdsNotSet');
        }
        
        $document = new App_Model_DbTable_WorkshopDocument();
        
        $thisDocument = $document->find($get->workshopDocumentId);
        if (is_null($thisDocument)) {
            throw new Ot_Exception_Data('msg-error-noDocument');
        }

        $config = Zend_Registry::get('config');
        
        if (!is_readable($config->user->fileUploadPathWorkshop->val)) {
            throw new Ot_Exception_Data('msg-error-targetDirNotReadable');
        }
            
        $target = $config->user->fileUploadPathWorkshop->val . '/' . $thisDocument->workshopId . '/' . $thisDocument->name;
        
        if (!is_file($target)) {
            throw new Ot_Exception_Data('msg-error-fileNotFound');
        }   
            
        $this->_helper->viewRenderer->setNeverRender();
        $this->view->layout()->disableLayout();
            
        header('Content-Type: application/octetstream');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; ' . 'filename="' . $thisDocument->name . '"');
        readfile($target); 
    }
    
    public function addLinkAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        } else {
            $this->_helper->pageTitle('workshop-index-addLink:title');
        }
        
        $get = Zend_Registry::get('getFilter');
        if (!isset($get->workshopId)) {
            throw new Ot_Exception_Input('msg-error-workshopIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        $link = new App_Model_DbTable_WorkshopLink;
        
        $thisWorkshop = $workshop->find($get->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $we = new App_Model_DbTable_WorkshopEditor();        
        if (!$this->_helper->hasAccess('edit-all-workshops') && 
            !$we->isEditor($thisWorkshop->workshopId, Zend_Auth::getInstance()->getIdentity()->accountId)) {
            throw new Ot_Exception_Access('msg-error-noAccess');        
        }    

        $form = $link->form();
        $form->setAction($this->view->baseUrl() . '/workshop/index/add-link/?workshopId=' . $thisWorkshop->workshopId);
        
        $messages = array();
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                
                $data = array(
                    'workshopId' => $thisWorkshop->workshopId,
                    'url'        => ((!preg_match('/:\/\//', $form->getValue('url'))) ? 'http://' : '') . $form->getValue('url'),
                    'name'       => $form->getValue('name'),
                );
                
                $link->insert($data);
                                
                $logOptions = array(
                    'attributeName' => 'workshopId',
                    'attributeId'   => $thisWorkshop->workshopId,
                );
                    
                $this->_helper->log(Zend_Log::INFO, 'Link was added', $logOptions);
                                    
                $this->_helper->flashMessenger->addMessage('msg-info-linkAdded');
            
                $this->_helper->redirector->gotoUrl('/workshop/index/details/?workshopId=' . $thisWorkshop->workshopId);   
            } else {
                $messages[] = 'msg-error-formSubmitProblem';
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form = $form;
        $this->view->workshop = $thisWorkshop->toArray();
    }
    
    public function editLinkAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        } else {
            $this->_helper->pageTitle('workshop-index-editLink:title');
        }
        
        $get = Zend_Registry::get('getFilter');
        if (!isset($get->workshopLinkId)) {
            throw new Ot_Exception_Input('msg-error-workshopLinkIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        $link = new App_Model_DbTable_WorkshopLink;
        
        $thisLink = $link->find($get->workshopLinkId);
        if (is_null($thisLink)) {
            throw new Ot_Exception_Data('msg-error-noLink');
        }
        
        $thisWorkshop = $workshop->find($thisLink->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $we = new App_Model_DbTable_WorkshopEditor();        
        if (!$this->_helper->hasAccess('edit-all-workshops') && 
            !$we->isEditor($thisWorkshop->workshopId, Zend_Auth::getInstance()->getIdentity()->accountId)) {
            throw new Ot_Exception_Access('msg-error-noAccess');        
        }     

        $form = $link->form($thisLink->toArray());
        $form->setAction($this->view->baseUrl() . '/workshop/index/edit-link/?workshopLinkId=' . $thisLink->workshopLinkId);
        
        $messages = array();
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                
                $data = array(
                    'workshopLinkId' => $thisLink->workshopLinkId,
                    'url'            => ((!preg_match('/:\/\//', $form->getValue('url'))) ? 'http://' : '') . $form->getValue('url'),
                    'name'           => $form->getValue('name'),
                );
                
                $link->update($data, null);
                
                $logOptions = array(
                    'attributeName' => 'workshopId',
                    'attributeId'   => $thisWorkshop->workshopId,
                );
                    
                $this->_helper->log(Zend_Log::INFO, 'Link was modified', $logOptions);
                                    
                $this->_helper->flashMessenger->addMessage('msg-info-linkModified');
            
                $this->_helper->redirector->gotoUrl('/workshop/index/details/?workshopId=' . $thisWorkshop->workshopId);   
            } else {
                $messages[] = 'msg-error-formSubmitProblem';
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form = $form;
    }
    
    public function deleteLinkAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        } else {
            $this->_helper->pageTitle('workshop-index-deleteLink:title');
        }
        
        $get = Zend_Registry::get('getFilter');
        if (!isset($get->workshopLinkId)) {
            throw new Ot_Exception_Input('msg-error-workshopLinkIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        $link = new App_Model_DbTable_WorkshopLink;
        
        $thisLink = $link->find($get->workshopLinkId);
        if (is_null($thisLink)) {
            throw new Ot_Exception_Data('msg-error-noLink');
        }
        
        $thisWorkshop = $workshop->find($thisLink->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $we = new App_Model_DbTable_WorkshopEditor();        
        if (!$this->_helper->hasAccess('edit-all-workshops') && 
            !$we->isEditor($thisWorkshop->workshopId, Zend_Auth::getInstance()->getIdentity()->accountId)) {
            throw new Ot_Exception_Access('msg-error-noAccess');        
        }     

        $form = Ot_Form_Template::delete('deleteLink');
        $form->setAction($this->view->baseUrl() . '/workshop/index/delete-link/?workshopLinkId=' . $thisLink->workshopLinkId);
        
        if ($this->_request->isPost() && $form->isValid($_POST)) {
               
            $link->delete($link->getAdapter()->quoteInto('workshopLinkId = ?', $thisLink->workshopLinkId));
            
            $logOptions = array(
                'attributeName' => 'workshopId',
                'attributeId'   => $thisWorkshop->workshopId,
            );
                    
            $this->_helper->log(Zend_Log::INFO, 'Link was deleted', $logOptions);
                                    
            $this->_helper->flashMessenger->addMessage('msg-info-linkDeleted');
            
            $this->_helper->redirector->gotoUrl('/workshop/index/details/?workshopId=' . $thisWorkshop->workshopId);   
        }
        
        $this->view->form = $form;        
    }

    /**
     * If a user has access to this function they have access to edit all the workshops.
     *
     */
    public function editAllWorkshopsAction()
    {}
    
    
    protected function _getDocumentType($mime)
    {
        if (preg_match('/word$/i', $mime)) {
            return 'document';
        }
        
        if (preg_match('/excel$/i', $mime)) {
            return 'spreadsheet';
        }
        
        if (preg_match('/powerpoint$/i', $mime)) {
            return 'presentation';
        }
            
        if (preg_match('/pdf$/i', $mime)) {
            return 'pdf';
        }
        
        if (preg_match('/zip/i', $mime)) {
            return 'zip';
        }
        
        return strtolower(preg_replace('/\/.*$/', '', $mime));
    }
}