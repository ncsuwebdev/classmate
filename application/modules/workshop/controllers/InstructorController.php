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
 * @subpackage Workshop_InstructorController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with instructors
 *
 * @package    Classmate
 * @subpackage Workshop_InstructorController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_InstructorController extends Internal_Controller_Action 
{	
	protected function _setupTemplate()
	{
        $get = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        $uc     = Zend_Registry::get('userConfig');
        
        if (!isset($get['eventId'])) {
            throw new Internal_Exception_Input('Event ID not set');
        }
        
        // lookup the event
        $event = new Event();
        $thisEvent = $event->find($filter->filter($get['eventId']));
        if (is_null($thisEvent)) {
            throw new Internal_Exception_Data('Event not found');
        }
        $this->view->event = $thisEvent->toArray();
        
        // lookup the instructors
        $instructor = new Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
        $this->view->instructors = $instructors;
        
        // lookup the location of the event
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        if (is_null($thisLocation)) {
            throw new Internal_Exception_Data('Location not found');
        }
        $this->view->location = $thisLocation->toArray();
        
        // lookup the corresponding workshop
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Internal_Exception_Data('Workshop not found');
        }
        $this->view->workshop = $thisWorkshop->toArray();
        
        // lookup the workshop category
        $wc = new WorkshopCategory();
        $category = $wc->find($thisWorkshop->workshopCategoryId);
        if (is_null($category)) {
            throw new Internal_Exception_Data('Category not found');
        }
        $this->view->category = $category->toArray();
        
        $this->view->hideTitle = true;
        $this->view->title = 'Instructor Tools for ' . $thisWorkshop->title;
        		
        $this->_helper->viewRenderer('template');
        
	}
    /**
     * 
     *
     */
    public function indexAction()
    {    	
        $this->_setupTemplate();
    	
        $thisEvent = $this->view->event;
        
        // lookup the attendees of the event
        $attendees = new Attendees();
        $attendeeList = $attendees->getAttendeesForEvent($thisEvent['eventId'], 'attending');
        $this->view->attendeeList = $attendeeList;
        
        $waitlist = $attendees->getAttendeesForEvent($thisEvent['eventId'], 'waitlist');
        $this->view->waitlist = $waitlist;     

        $this->view->toolTemplate = $this->view->render('instructor/index.tpl');
    }
    
    public function attendanceAction()
    {
    	$this->_helper->getExistingHelper('viewRenderer')->setNeverRender();
    	
    	if ($this->_request->isPost()) {
    		$post = Zend_Registry::get('post');
    		$filter = Zend_Registry::get('inputFilter');
    		
    		$eventId = $filter->filter($post['eventId']);
    		$userId = $filter->filter($post['userId']);
    		$attended = $filter->filter($post['attended']);
    		
    		$data = array(
    		  'eventId' => $eventId,
    		  'userId'  => $userId,
    		  'attended' => ($attended == 'true'),
    		);
    		
    		$attendees = new Attendees();
    		
    		$where = $attendees->getAdapter()->quoteInto('eventId = ?', $eventId) . ' AND ' . 
    		    $attendees->getAdapter()->quoteInto('userId = ?', $userId);
    		    
    		$attendees->update($data, $where);
    		
    		echo "Updated!";
    	}
    }
    
    public function contactAction()
    {
    	$filter = Zend_Registry::get('inputFilter');
    	
    	if ($this->_request->isPost()) {
    		$post = Zend_Registry::get('post');
    		
	        $attendees = new Attendees();
	        $attendeeList = $attendees->getAttendeesForEvent($thisEvent->eventId, 'attending');
	        $this->view->attendeeList = $attendeeList;
	        
	        $waitlist = $attendees->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
	        $this->view->waitlist = $waitlist;     		
    		
    		$this->_redirect('contactConfirm/?eventId=' . $data['eventId']);
    	}
    	
    	$this->_setupTemplate();
    	
    	$this->view->toolTemplate = $this->view->render('instructor/contact.tpl');
    	
    }
}