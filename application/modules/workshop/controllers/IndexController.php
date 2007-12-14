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
    
    public function detailsAction()
    {
    	$get = Zend_Registry::get('get');
    	$filter = Zend_Registry::get('inputFilter');
    	
    	if (!isset($get['workshopId'])) {
    		throw new Internal_Exception_Input('Workshop ID not set in query string.');
    	}
    	
    	$this->view->acl = array(
    	   'edit' => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
    	);
    	
    	$workshop = new Workshop();
    	
    	$thisWorkshop = $workshop->find($filter->filter($get['workshopId']));
    	
    	if (is_null($thisWorkshop)) {
    		throw new Internal_Exception_Data('Workshop not found');
    	}
    	
    	$document = new Document();
    	$this->view->documents = $document->getDocumentsForAttribute('workshopId', $thisWorkshop->workshopId);
    	
    	$tag = new Tag();
    	$this->view->tags = $tag->getTagsForAttribute('workshopId', $thisWorkshop->workshopId);
    	
    	$event = new Event();
    	$this->view->events = $event->getEventsForWorkshop($thisWorkshop->workshopId, time(), null, 'open')->toArray();
    	
    	$location = new Location();
    	$locations = $location->fetchAll();
    	
    	$locs = array();
    	foreach ($locations as $l) {
    		$locs[$l->locationId] = $l->toArray();
    	}
    	
    	$this->view->locations = $locs;
    	
    	$this->view->javascript = array('mootabs1.2.js');
    	$this->view->useInlineEditor = true;
    	$this->view->title    = $thisWorkshop->title;
    	$this->view->hideTitle = true;
    	$this->view->workshop = $thisWorkshop->toArray();
    }
    
    public function addAction()
    {
    	
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
    	    
    	    echo 'Workshop saved successfully';
    	    return;    	    
    	}
    }
}