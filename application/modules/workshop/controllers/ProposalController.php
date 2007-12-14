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
 * @subpackage Workshop_ProposalController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with proposed workshops
 *
 * @package    Classmate
 * @subpackage Workshop_ProposalController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_ProposalController extends Internal_Controller_Action 
{	
	public function indexAction()
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
		$this->view->title = 'Teach a New Workshop!';
		
		$this->view->javascript = 'tiny_mce/tiny_mce.js';
	}
	
    /**
     * Action when going to the main login page
     *
     */
    public function waitingAction()
    {    	
    	$this->view->acl = array(
    	   'add'     => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
    	   'details' => $this->_acl->isAllowed($this->_role, $this->_resource, 'details'),
    	);
    	
        $this->view->title = "Our Workshops";
        
        $workshop = new Workshop();
        
        $where = $workshop->getAdapter()->quoteInto('status = ?', 'proposed');
        
        $this->view->workshops = $workshop->fetchAll($where, 'title')->toArray();
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
    	
    	$this->view->javascript = 'mootabs1.2.js';
    	$this->view->title    = $thisWorkshop->title;
    	$this->view->workshop = $thisWorkshop->toArray();
    }
    
    public function addAction()
    {
    	
    }
    
    public function editAction()
    {
    	
    }
}