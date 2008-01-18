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
 * @subpackage Workshop_SignupController
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
 * @subpackage Workshop_SignupController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_SignupController extends Internal_Controller_Action 
{	
    /**
     * Action when going to the main login page
     *
     */
    public function indexAction()
    {    	
        $get = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        if (!isset($get['eventId'])) {
        	throw new Internal_Exception_Input('Event ID not set');
        }
        
        $eventId = $filter->filter($get['eventId']);
        
        if ($eventId == '') {
        	throw new Internal_Exception_Input('Event ID has no value');
        }
        
        $event = new Event();
        $thisEvent = $event->find($eventId);
        
        if (is_null($thisEvent)) {
        	throw new Internal_Exception_Data('Event not found');
        }
        
        $status = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity(), $eventId);
        
        $this->view->event = $thisEvent->toArray();
        
        $location = new Location();        
        $thisLocation = $location->find($thisEvent->locationId);        
        if (is_null($thisLocation)) {
            throw new Internal_Exception_Data('Location not found');
        }
        $this->view->location = $thisLocation->toArray();
        
        
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
        	throw new Internal_Exception_Data('Workshop not found');
        }
        $this->view->workshop = $thisWorkshop->toArray();
        
        
        $instructor = new Instructor();
        $instructors = $instructor->getInstructorsForEvent($eventId);        
        
        $this->view->title = "Signup for " . $thisWorkshop->title;
        $this->view->hideTitle = true;
        
        $events = $event->getEvents($thisWorkshop->workshopId, null, time(), null, 'open')->toArray();
        
        $newEvents = array();
          
	    foreach ($events as $e) {
	    	if ($e['eventId'] != $eventId) {
	           $e['status'] = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity(), $e['eventId']);
	           $newEvents[] = $e;
	    	}
	    }   

	    $this->view->upcomingEvents = $newEvents;
        
        $this->view->status = $status;
    }
    
    public function reserveAction()
    {
        $get = Zend_Registry::get('get'); 
        $filter = Zend_Registry::get('inputFilter');          
        
        if (!isset($get['eventId'])) {
            throw new Internal_Exception_Input('Event ID not set');
        }
            
        $eventId = $filter->filter($get['eventId']);
            
        if ($eventId == '') {
            throw new Internal_Exception_Input('Event ID has no value');
        } 
        
        $attendees = new Attendees();
        $attendees->makeReservation(Zend_Auth::getInstance()->getIdentity(), $eventId);
        
        $this->_redirect('/');        
    }
    
    public function cancelAction()
    {
    	$filter = Zend_Registry::get('inputFilter');
    	$event = new Event();
    	
    	if ($this->_request->isPost()) {
    	    $post = Zend_Registry::get('post');           
            
            if (!isset($post['eventId'])) {
                throw new Internal_Exception_Input('Event ID not set');
            }
            
            $eventId = $filter->filter($post['eventId']);
            
            if ($eventId == '') {
                throw new Internal_Exception_Input('Event ID has no value');
            }
            
            $status = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity(), $eventId);
            if ($status != 'waitlist' && $status != 'attending') {
                throw new Internal_Exception_Data('You are not atteding this class, so you cannot cancel it');
            }   

            $attendees = new Attendees();
            $attendees->cancelReservation(Zend_Auth::getInstance()->getIdentity(), $eventId);
            
            $this->_redirect('/');
            
    	} else {
	        $get = Zend_Registry::get('get');	        
	        
	        if (!isset($get['eventId'])) {
	            throw new Internal_Exception_Input('Event ID not set');
	        }
	        
	        $eventId = $filter->filter($get['eventId']);
	        
	        if ($eventId == '') {
	            throw new Internal_Exception_Input('Event ID has no value');
	        }
	        
	        $status = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity(), $eventId);
	        if ($status != 'waitlist' && $status != 'attending') {
	            throw new Internal_Exception_Data('You are not atteding this class, so you cannot cancel it');
	        }
	        $this->view->status = $status;  
	        
	        $event = new Event();
	        $thisEvent = $event->find($eventId);
	        
	        if (is_null($thisEvent)) {
	            throw new Internal_Exception_Data('Event not found');
	        }
	        
	        $this->view->event = $thisEvent->toArray();
	        
	        $location = new Location();        
	        $thisLocation = $location->find($thisEvent->locationId);        
	        if (is_null($thisLocation)) {
	            throw new Internal_Exception_Data('Location not found');
	        }
	        $this->view->location = $thisLocation->toArray();
	        
	        
	        $workshop = new Workshop();
	        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
	        if (is_null($thisWorkshop)) {
	            throw new Internal_Exception_Data('Workshop not found');
	        }
	        $this->view->workshop = $thisWorkshop->toArray();        
	        
	        $instructor = new Instructor();
	        $instructors = $instructor->getInstructorsForEvent($eventId);        
	        
	        
	        $events = $event->getEvents($thisWorkshop->workshopId, null, time(), null, 'open')->toArray();
	        
	        $newEvents = array();
	          
	        foreach ($events as $e) {
	            if ($e['eventId'] != $eventId) {
	               $e['status'] = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity(), $e['eventId']);
	               $newEvents[] = $e;
	            }
	        }   
	
	        $this->view->upcomingEvents = $newEvents;
        	        
	        $this->view->hideTitle = true;
	    }
    }
}