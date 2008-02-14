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
 * @subpackage Workshop_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with workshops
 *
 * @package    Classmate
 * @subpackage Workshop_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_IndexController extends Internal_Controller_Action 
{	
    /**
     * Action when going to the main login page
     *
     */
    public function indexAction()
    {    	
    	$this->view->acl = array(
    	   'add'     => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
    	   'details' => $this->_acl->isAllowed($this->_role, $this->_resource, 'details'),
    	   'viewDisabled' => $this->_acl->isAllowed($this->_role, $this->_resource, 'viewDisabled'),
    	);
    	
        $this->view->title = "Our Workshops";
        
        $wc = new WorkshopCategory();
        $categories = $wc->fetchAll(null, 'name');
        
        $workshops = array();
        foreach ($categories as $c) {
        	$workshops[$c->workshopCategoryId]['category'] = $c->toArray();
        }
        
        $workshop = new Workshop();
        $event    = new Event();
        
        $result = $workshop->fetchAll(null, array('workshopCategoryId', 'title'));
        foreach ($result as $r) {
        	$temp = $r->toArray();
        	
        	$next = $event->getEvents($r->workshopId, null, time(), null, 'open', 1);
        	
        	if ($next->count() == 0) {
        		$next = null;
        	} else {
        		$next = $next->current()->toArray();
        	}
        	
        	$temp['nextEvent'] = $next;
        	$workshops[$r->workshopCategoryId]['workshops'][] = $temp;
        }
        
        $this->view->workshops = $workshops;
    }
    
    public function viewDisabledAction()
    {}
    
    public function addAction()
    {
        if ($this->_request->isPost()) {
            $post       = Zend_Registry::get('post');
            $filter     = Zend_Registry::get('inputFilter');
            $htmlFilter = Zend_Registry::get('htmlFilter');
            
            $data = array(
                'title'              => $filter->filter($post['title']),
                'description'        => $htmlFilter->filter($post['description']),
                'prerequisites'      => $htmlFilter->filter($post['prerequisites']),
                'softwareDependency' => $filter->filter($post['softwareDependency']), 
            );
            
            $tags = explode(',', $filter->filter($post['tags']));
            
            foreach ($tags as &$t) {
                $t = $filter->filter($t);
            }

            $workshop = new Workshop();
            $data['workshopId'] = $workshop->insert($data);         
            
            $tag = new Tag();
            $tag->setTagsForAttribute('workshopId', $data['workshopId'], $tags);
            
            $this->_redirect('/workshop/');
        }
        $this->view->title = 'Add a Workshop';
        
        $this->view->javascript = array(
            'tiny_mce/tiny_mce.js',
            'workshop/proposal/index.js'
        );    	
    }
    
    public function detailsAction()
    {
    	$get = Zend_Registry::get('get');
    	$filter = Zend_Registry::get('inputFilter');
    	$config = Zend_Registry::get('config');
    	
    	if (!isset($get['workshopId'])) {
    		throw new Internal_Exception_Input('Workshop ID not set in query string.');
    	}
    	
    	$workshop = new Workshop();
    	
    	$thisWorkshop = $workshop->find($filter->filter($get['workshopId']));
    	
    	if (is_null($thisWorkshop)) {
    		throw new Internal_Exception_Data('Workshop not found');
    	}
    	
    	$wc = new WorkshopCategory();
    	$this->view->category = $wc->find($thisWorkshop->workshopCategoryId)->toArray();
    	    	
    	$document = new Document();
    	$this->view->documents = $document->getDocumentsForAttribute('workshopId', $thisWorkshop->workshopId);
    	
    	$tag = new Tag();
    	$this->view->tags = $tag->getTagsForAttribute('workshopId', $thisWorkshop->workshopId);
    	
    	$event = new Event();
    	$events = $event->getEvents($thisWorkshop->workshopId, null, time(), null, 'open')->toArray();
    	
    	foreach ($events as &$e) {
    		$e['status'] = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity(), $e['eventId']);
    	}
    	
    	$this->view->events = $events;
    	
    	$wl = new WorkshopLink();
    	$this->view->links = $wl->getLinksForWorkshop($thisWorkshop->workshopId)->toArray();
    	
    	$location = new Location();
    	$locations = $location->fetchAll();
    	
    	$locs = array();
    	foreach ($locations as $l) {
    		$locs[$l->locationId] = $l->toArray();
    	}
    	
    	$this->view->locations = $locs;
    	
        $we = new WorkshopEditor();        
        
        $isEditor = false;        
        if ($this->_acl->isAllowed($this->_role, $this->_resource, 'editAllWorkshops')) {
        	$isEditor = true;
        } else {
            $isEditor = $we->isEditor($thisWorkshop->workshopId, Zend_Auth::getInstance()->getIdentity());
        }
        
        $this->view->acl = array(
           'edit'           => $isEditor,
           'addDocuments'   => $isEditor,
           'editDocument'   => $isEditor,
           'deleteDocument' => $isEditor,
           'addLink'        => $isEditor,
           'deleteLink'     => $isEditor,
           'editLink'       => $isEditor,
           'addEvent'       => $this->_acl->isAllowed($this->_role, 'workshop_schedule', 'index'),
           'options'        => $this->_acl->isAllowed($this->_role, $this->_resource, 'options'),
        );
        
    	if ($this->view->acl['edit']) {
	    	$this->view->javascript = array(
	    	    'Stickman.MultiUpload.js',
	    	    "cnet/common/utilities/dbug.js",
	            "cnet/mootools.extended/Native/element.shortcuts.js",
	            "cnet/mootools.extended/Native/element.dimensions.js",
	            "cnet/mootools.extended/Native/element.position.js",
	            "cnet/mootools.extended/Native/element.pin.js", 
	            "cnet/common/browser.fixes/IframeShim.js",
	            "cnet/common/js.widgets/modalizer.js",
	            "cnet/common/js.widgets/stickyWin.default.layout.js",
	            "cnet/common/js.widgets/stickyWin.js",
	            "cnet/common/js.widgets/stickyWin.Modal.js",
	            "cnet/common/js.widgets/stickyWinFx.js",
	            "cnet/common/js.widgets/stickyWinFx.Drag.js", 	
	    	);
	    	
	    	$wc = new WorkshopCategory();
	    	$this->view->categories = $wc->fetchAll(null, 'name')->toArray();
	    	
	        //get all the users available for the instructor list
	        $profile = new Profile();
	        $profiles = $profile->fetchAll(null, array('lastName', 'firstName'))->toArray();
	        
	        $users = array('instructors' => 'All Upcoming Instructors');
	        
	        foreach ($profiles as $p) {
	            $users[$p['userId']] = $p['lastName'] . ", " . $p['firstName'];            
	        }
	        
	        $this->view->users = $users;	 

	        $we = new WorkshopEditor();
            $where = $we->getAdapter()->quoteInto('workshopId = ?', $thisWorkshop->workshopId);
            $results = $we->fetchAll($where);
            
            $currentEditors = array();
            foreach ($results as $r) {
                $currentEditors[] = $r->userId;
            }
            
            $this->view->currentEditors = $currentEditors;	        
    	}
    	
    	$this->view->useInlineEditor = true;
    	$this->view->title    = $thisWorkshop->title;
    	$this->view->hideTitle = true;
    	$this->view->workshop = $thisWorkshop->toArray();
    	$this->view->sessionID = session_id();
    }
    
    public function editAction()
    {
    	$this->_helper->getExistingHelper('viewRenderer')->setNeverRender();
    	
    	$editable = array('wsTitle', 'description', 'prerequisites');
    	
    	if ($this->_request->isPost()) {
    		$post = Zend_Registry::get('post');
    	    $filter = Zend_Registry::get('inputFilter');
    	    
    	    if (!isset($post['workshopId'])) {
    	    	echo 'workshop ID not set';
    	    	return;
    	    }
    	    
    	    $workshopId = $filter->filter($post['workshopId']);
    	    
    	    if ($workshopId == '') {
    	    	echo 'workshop ID can not be blank';
    	    	return;
    	    }
    	    
            $we = new WorkshopEditor();        
            if (!$this->_acl->isAllowed($this->_role, $this->_resource, 'editAllWorkshops') && 
                !$we->isEditor($workshopId, Zend_Auth::getInstance()->getIdentity())) {
            	echo 'You do not have access to edit this';
            	return;    	    
            }
    	    
    	    $data = array(
    	        'workshopId' => $workshopId,
    	    );
    	    
    	    $htmlFilter = Zend_Registry::get('htmlFilter');
    	    
    	    foreach ($editable as $e) {
    	    	if (isset($post[$e])) {
    	    	    if ($e == 'wsTitle') {
    	    	    	$data['title'] = $htmlFilter->filter($post[$e]);
    	    	    } else {
        	    		$data[$e] = $htmlFilter->filter($post[$e]);
    	    	    }
    	    	}
    	    }
    	    
    	    $workshop = new Workshop();
    	    $workshop->update($data, null);
    	    
    	    if (isset($post['taglist'])) {
	            $tags = explode(',', $filter->filter($post['taglist']));
	            
	            foreach ($tags as &$t) {
	                $t = $filter->filter($t);
	            }       
	            
	            $tag = new Tag();
	            $tag->setTagsForAttribute('workshopId', $data['workshopId'], $tags);   
    	    }
    	    
    	    $workshop->index($workshopId);
    	    
    	    echo 'Workshop saved successfully';
    	    return;    	    
    	}
    }
    
    public function optionsAction()
    {
    	if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            if (!isset($post['workshopId'])) {
                echo 'workshop ID not set';
                return;
            }
            
            $workshopId = $filter->filter($post['workshopId']);
            
            if ($workshopId == '') {
                echo 'workshop ID can not be blank';
                return;
            }
            
            $we = new WorkshopEditor();
            $where = $we->getAdapter()->quoteInto('workshopId = ?', $workshopId);
            $results = $we->fetchAll($where);
            
            $currentEditors = array();
            foreach ($results as $r) {
                $currentEditors[] = $r->userId;
            }

            $newEditors = array();
            
            if (isset($post['editor']) && is_array($post['editor'])) {
	            foreach ($post['editor'] as $e) {
	            	$e = $filter->filter($e);
	            	
	            	if ($e != '') {
	            	    if (!in_array($e, $currentEditors)) {
				            $data = array(
				                'workshopId' => $workshopId,
				                'userId'     => $e,
				            );             		
				            
				            $we->insert($data);
		            	}
		            	
		            	$newEditors[] = $e;
	            	}
	            } 
            }  
            
            $deletable = array_diff($currentEditors, $newEditors);
            
            $dba = $we->getAdapter();
            
            foreach ($deletable as $userId) {
            	$where = $dba->quoteInto('workshopId = ?', $workshopId) . 
            	   ' AND ' . 
            	   $dba->quoteInto('userId = ?', $userId);
            	   
            	$we->delete($where);
            }
            
            $data = array(
                'workshopId' => $workshopId,
                'workshopCategoryId' => $filter->filter($post['workshopCategoryId']),
                'status'             => $filter->filter($post['status']),
            );         
            
            $workshop = new Workshop();
            $workshop->update($data, null);
        }
        
        $this->_redirect('/workshop/index/details?workshopId=' . $workshopId);
    }
    
    public function deleteDocumentAction()
    {
    	$this->_helper->getExistingHelper('viewRenderer')->setNeverRender();
    	
        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            if (!isset($post['documentId'])) {
                echo 'document ID not set';
                return;
            }
            
            $documentId = $filter->filter($post['documentId']);
            
            if ($documentId == '') {
                echo 'document ID can not be blank';
                return;
            }  
            
            $document = new Document();
            
            $d = $document->find($documentId);
            
            $we = new WorkshopEditor();        
            if (!$this->_acl->isAllowed($this->_role, $this->_resource, 'editAllWorkshops') && 
                !$we->isEditor($d->workshopId, Zend_Auth::getInstance()->getIdentity())) {
                echo 'You do not have access to edit this';
                return;         
            }            
            
            $document->deleteDocument($documentId);
            
            echo "Document successfully deleted.";
            return;
        }
    }
    
    
    public function addDocumentsAction()
    {
        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            if (!isset($post['attributeName']) || !isset($post['attributeId'])) {
                throw new Internal_Exception_Input('attribute name and ID not set');
            }
            
            $attributeName = $filter->filter($post['attributeName']);
            $attributeId   = $filter->filter($post['attributeId']);
            
            if ($attributeId == '' || $attributeName == '') {
                throw new Internal_Exception_Input('attribute name or ID can not be blank');
            }  
            
            $we = new WorkshopEditor();        
            if (!$this->_acl->isAllowed($this->_role, $this->_resource, 'editAllWorkshops') && 
                !$we->isEditor($attributeId, Zend_Auth::getInstance()->getIdentity())) {
                throw new Internal_Exception_Access('You do not have access to add this');     
            }  
                        
            $document = new Document();
            
            foreach ($_FILES['uploadDocuments']['error'] as $key => $value) {
            	
            	if ($value == '0') {
		            $data = array(
		                'name'        => $filter->filter($_FILES['uploadDocuments']['name'][$key]),
		                'title'       => '',
		                'path'        => '',
		                'description' => $filter->filter($_FILES['uploadDocuments']['type'][$key]),
		                'type'        => $this->_getDocumentType($filter->filter($_FILES['uploadDocuments']['type'][$key])),
		                'uploadDt'    => time(),
		                'filesize'    => $filter->filter($_FILES['uploadDocuments']['size'][$key]),
		            );
		            
		            $documentId = $document->insert($data);
		            
		            $docMap = new DocumentMap();
		            
		            $data = array(
		                'attributeName' => $attributeName,
		                'attributeId'   => $attributeId,
		                'documentId'    => $documentId,
		            );
		            
		            $docMap->insert($data);            		
            	}
            	
            }
            
            $this->_redirect('/workshop/index/details/?workshopId=' . $attributeId);
        }
    }
    
    public function editDocumentAction()
    {
        $this->_helper->getExistingHelper('viewRenderer')->setNeverRender();
        
        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            if (!isset($post['documentId'])) {
                echo 'document ID not set';
                return;
            }
            
            $documentId = $filter->filter($post['documentId']);
            
            if ($documentId == '') {
                echo 'document ID can not be blank';
                return;
            }
            
            $data = array(
                'documentId' => $documentId,
            );
            
            foreach ($post as $key => $value) {
            	if (preg_match('/^documentTitle_/', $key)) {
            		$data['title'] = $filter->filter($value);
            	}
            	
            	if (preg_match('/^documentDescription_/', $key)) {
            		$data['description'] = $filter->filter($value);
            	}
            }
            
            $htmlFilter = Zend_Registry::get('htmlFilter');
            
            $document = new Document();
            
            $d = $document->find($documentId);
            
            $we = new WorkshopEditor();        
            if (!$this->_acl->isAllowed($this->_role, $this->_resource, 'editAllWorkshops') && 
                !$we->isEditor($d->workshopId, Zend_Auth::getInstance()->getIdentity())) {
                echo 'You do not have access to edit this';
                return;         
            }
                        
            $document->update($data, null);
            
            echo 'Document saved successfully';
            return;         
        }
    }
    
    public function deleteLinkAction()
    {
        $this->_helper->getExistingHelper('viewRenderer')->setNeverRender();
        
        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            if (!isset($post['workshopLinkId'])) {
                echo 'link ID not set';
                return;
            }
            
            $workshopLinkId = $filter->filter($post['workshopLinkId']);
            
            if ($workshopLinkId == '') {
                echo 'link ID can not be blank';
                return;
            }  
            
            $wsl = new WorkshopLink();
            
            $l = $wsl->find($workshopLinkId);
            
            $we = new WorkshopEditor();        
            if (!$this->_acl->isAllowed($this->_role, $this->_resource, 'editAllWorkshops') && 
                !$we->isEditor($l->workshopId, Zend_Auth::getInstance()->getIdentity())) {
                echo 'You do not have access to edit this';
                return;         
            }            
            
            $wsl->deleteWorkshopLink($workshopLinkId);
            
            echo "Link successfully deleted.";
            return;
        }
    }
    
    
    public function addLinkAction()
    {
        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            $wsl = new WorkshopLink();
            $data = array(
                'name'       => $filter->filter($post['name']),
                'url'        => $filter->filter($post['url']),
                'workshopId' => $filter->filter($post['workshopId']),
            );
            
            if (!preg_match('/:\/\//', $data['url'])) {
            	$data['url'] = 'http://' . $data['url'];
            }
            
            if ($data['name'] == '') {
                $data['name'] = $data['url'];
            }            
            
            $we = new WorkshopEditor();        
            if (!$this->_acl->isAllowed($this->_role, $this->_resource, 'editAllWorkshops') && 
                !$we->isEditor($data['workshopId'], Zend_Auth::getInstance()->getIdentity())) {
                echo 'You do not have access to edit this';
                return;         
            }            
            
            $wsl->insert($data);
            
            $this->_redirect('/workshop/index/details/?workshopId=' . $data['workshopId']);
        }
    }
    
    public function editLinkAction()
    {
        $this->_helper->getExistingHelper('viewRenderer')->setNeverRender();
        
        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            if (!isset($post['workshopLinkId'])) {
                echo 'link ID not set';
                return;
            }
            
            $workshopLinkId = $filter->filter($post['workshopLinkId']);
            
            if ($workshopLinkId == '') {
                echo 'link ID can not be blank';
                return;
            }
            
            $data = array(
                'workshopLinkId' => $workshopLinkId,
                'name'       => $filter->filter($post['name']),
                'url'        => $filter->filter($post['url']),
            );
            
            if (!preg_match('/:\/\//', $data['url'])) {
                $data['url'] = 'http://' . $data['url'];
            }         

            if ($data['name'] == '') {
            	$data['name'] = $data['url'];
            }
            
            $wsl = new WorkshopLink();
            
            $l = $wsl->find($workshopLinkId);
            
            $we = new WorkshopEditor();        
            if (!$this->_acl->isAllowed($this->_role, $this->_resource, 'editAllWorkshops') && 
                !$we->isEditor($l->workshopId, Zend_Auth::getInstance()->getIdentity())) {
                echo 'You do not have access to edit this';
                return;         
            } 
                        
            $wsl->update($data, null);
            
            echo 'Link saved successfully';
            return;         
        }
    }    
    
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