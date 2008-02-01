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
    	);
    	
        $this->view->title = "Our Workshops";
        
        $workshop = new Workshop();
        
        $this->view->workshops = $workshop->fetchAll(null, 'title')->toArray();
    }
    
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
    	
    	if (!isset($get['workshopId'])) {
    		throw new Internal_Exception_Input('Workshop ID not set in query string.');
    	}
    	
    	$this->view->acl = array(
    	   'edit' => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
    	   'addDocuments' => $this->_acl->isAllowed($this->_role, $this->_resource, 'addDocuments'),
    	   'editDocument' => $this->_acl->isAllowed($this->_role, $this->_resource, 'editDocument'),
    	   'deleteDocument' => $this->_acl->isAllowed($this->_role, $this->_resource, 'deleteDocument'),
    	   'addLink'       => $this->_acl->isAllowed($this->_role, $this->_resource, 'addLinks'),
    	   'deleteLink'     => $this->_acl->isAllowed($this->_role, $this->_resource, 'deleteLink'),
    	   'editLink'       => $this->_acl->isAllowed($this->_role, $this->_resource, 'editLink'),
    	   'addEvent'       => $this->_acl->isAllowed($this->_role, 'workshop_schedule', 'index'),
    	);
    	
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
    	
    	$this->view->javascript = array(
    	    'Stickman.MultiUpload.js'
    	);
    	
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
            $wsl->update($data, null);
            
            echo 'Link saved successfully';
            return;         
        }
    }    
    
    /**
     * AJAX function that returns the events for the day the user has
     * hovered over
     */
    public function getEventDetailsAction()
    {
    	
        $this->_helper->viewRenderer->setNeverRender();
        
        $ret = array();
        
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $eventId = $filter->filter($get['eventId']);
        
        $event = new Event();               
        $thisEvent = $event->find($eventId);
        
        if (is_null($thisEvent)) {
        	echo "No Event Found";
        	return;
        }
        
        $this->view->status = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity(), $eventId);
        
        $this->view->event = $thisEvent->toArray();
        
        $location = new Location();        
        $thisLocation = $location->find($thisEvent->locationId);
        
        if (is_null($thisLocation)) {
        	echo "No Location Found";
        	return;
        }
        
        $this->view->location = $thisLocation->toArray();
        
        $instructor = new Instructor();
        //$instructors = $instructor->getInstructorsForEvent($eventId);

        $this->_response->setBody($this->view->render('index/getEventDetails.tpl'));

    }    

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