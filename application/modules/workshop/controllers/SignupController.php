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
     * The main page a person sees when they want to sign up for an event
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
	    
        // lookup the workshop category
        $wc = new WorkshopCategory();
        $category = $wc->find($thisWorkshop->workshopCategoryId);
        if (is_null($category)) {
            throw new Internal_Exception_Data('Category not found');
        }
        $this->view->category = $category->toArray();	    

	    $this->view->upcomingEvents = $newEvents;
        
        $this->view->status = $status;
    }
    
    /**
     * Allows a user to sign up for an event.
     *
     */
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
        
        $event = new Event();
        $thisEvent = $event->find($eventId);
            
        if (is_null($thisEvent)) {
            throw new Internal_Exception_Data('Event not found');
        }
        
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        
        if (is_null($thisLocation)) {
        	throw new Internal_Exception_Data('Location Not Found');
        }

        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Internal_Exception_Data('Workshop not found');
        }
                
        
        $profile = new Profile();       
        $up = $profile->find(Zend_Auth::getInstance()->getIdentity());
        
        if (is_null($up)) {
            throw new Internal_Exception_Input('No profile exists for this user');
        }
        
        $instructor = new Instructor();
        $instructors = $instructor->getInstructorsForEvent($eventId);
                
        $instructorNames = array();
        $instructorEmails = array();
        
        foreach ($instructors as $i) {
            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
            $instructorEmails[] = $i['emailAddress'];
        }

        $status = 'attending';
        $attendees = new Attendees();
        $status = $attendees->makeReservation(Zend_Auth::getInstance()->getIdentity(), $eventId);
                
        $fm = $this->getHelper('FlashMessenger');
        $fm->setNamespace('login');
        $fm->addMessage('You have successfully signed up for <b>' . $thisWorkshop->title . '</b>.<br /><br />Your reservation ' . 
            'will show up below under &quot;My Reservations&quot;.  Should you need to cancel your reservation, you ' . 
            'can get back to this page by clicking on the &quot;My Appointments&quot; link in the navigation bar.');
        
        $startDt = strtotime($thisEvent->date . ' ' . $thisEvent->startTime);
        $endDt   = strtotime($thisEvent->date . ' ' . $thisEvent->endTime);
        
        $data = array(
            'workshopName'              => $thisWorkshop->title,
            'workshopDate'              => date('m/d/Y', $startDt),
            'workshopStartTime'         => date('g:i a', $startDt),
            'workshopEndTime'           => date('g:i a', $endDt),
            'workshopMinimumEnrollment' => $thisEvent->minSize,
            'locationName'              => $thisLocation->name,
            'locationAddress'           => $thisLocation->address,
            'userId'                    => Zend_Auth::getInstance()->getIdentity(),
            'instructorNames'           => implode(', ', $instructorNames),
            'instructorEmails'          => implode(', ', $instructorEmails),
            'studentEmail'              => $up->emailAddress,
            'studentName'               => $up->firstName . ' ' . $up->lastName,
        );
        
        if ($status == 'waitlist') {
            $waiting = $attendees->getAttendeesForEvent($e['eventId'], 'waitlist');
                    
            $position = 1;
                   
            foreach ($waiting as $w) {
                if ($data['userId'] == $w['userId']) {
                    break;
                }
                $position++;
            }
    
            $data['waitlistPosition'] = $position;
        }
        
        $trigger = new EmailTrigger();
        $trigger->setVariables($data);
        
        if ($status == 'waitlist') {
        	$trigger->dispatch('Event_Signup_Waitlist');
        } else {
            $trigger->dispatch('Event_Signup');
        }
        
        $this->_redirect('profile/');       
    }
    
    /**
     * Allows a user to cancel their reservation for an event.
     *
     */
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
            
	        $event = new Event();
	        $thisEvent = $event->find($eventId);
	            
	        if (is_null($thisEvent)) {
	            throw new Internal_Exception_Data('Event not found');
	        }
	
	        $workshop = new Workshop();
	        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
	        if (is_null($thisWorkshop)) {
	            throw new Internal_Exception_Data('Workshop not found');
	        }
	        
    	    $location = new Location();
	        $thisLocation = $location->find($thisEvent->locationId);
	        
	        if (is_null($thisLocation)) {
	            throw new Internal_Exception_Data('Location Not Found');
	        }	        
            
    	    $profile = new Profile();       
	        $up = $profile->find(Zend_Auth::getInstance()->getIdentity());
	        
	        if (is_null($up)) {
	            throw new Internal_Exception_Input('No profile exists for this user');
	        }
	        
	        $instructor = new Instructor();
	        $instructors = $instructor->getInstructorsForEvent($eventId);
	                
	        $instructorNames = array();
	        $instructorEmails = array();
	        
	        foreach ($instructors as $i) {
	            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
	            $instructorEmails[] = $i['emailAddress'];
	        }
        	        
            $status = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity(), $eventId);
            if ($status != 'waitlist' && $status != 'attending') {
                throw new Internal_Exception_Data('You are not atteding this class, so you cannot cancel it');
            }   

            $attendees = new Attendees();
            $attendees->cancelReservation(Zend_Auth::getInstance()->getIdentity(), $eventId);
            
            $startDt = strtotime($thisEvent->date . ' ' . $thisEvent->startTime);
            $endDt   = strtotime($thisEvent->date . ' ' . $thisEvent->endTime);
        
	        $data = array(
	            'workshopName'              => $thisWorkshop->title,
	            'workshopDate'              => date('m/d/Y', $startDt),
	            'workshopStartTime'         => date('g:i a', $startDt),
	            'workshopEndTime'           => date('g:i a', $endDt),
	            'workshopMinimumEnrollment' => $thisEvent->minSize,
	            'locationName'              => $thisLocation->name,
	            'locationAddress'           => $thisLocation->address,
	            'userId'                    => Zend_Auth::getInstance()->getIdentity(),
	            'instructorNames'           => implode(', ', $instructorNames),
	            'instructorEmails'          => implode(', ', $instructorEmails),
	            'studentEmail'              => $up->emailAddress,
	            'studentName'               => $up->firstName . ' ' . $up->lastName,
	        );       
	        
            $fm = $this->getHelper('FlashMessenger');
            $fm->setNamespace('login');
            $fm->addMessage('You have successfully canceled your reservation for <b>' . $thisWorkshop->title . '</b>.');
        
            $trigger = new EmailTrigger();
            $trigger->setVariables($data);
            $trigger->dispatch('Event_Cancel_Reservation');   	        

	    	$waiting = $attendees->getAttendeesForEvent($eventId, 'waitlist');
	        if (count($waiting) != 0) {	            
	            $up = $profile->find($waiting[0]['userId']);
	            
	            if (!is_null($up)) {
	            	$attendees->makeReservation($up->userId, $eventId);
	            	
		            $data['studentEmail'] = $up->emailAddress;
		            $data['studentName']  = $up->firstName . ' ' . $up->lastName;
		            $data['userId']       = $up->userId;
		            
		            $trigger = new EmailTrigger();
		            $trigger->setVariables($data);
		            $trigger->dispatch('Event_Waitlist_To_Attending'); 
	            }  	            
	        }	                 
            
            $this->_redirect('profile/');
            
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
	        
            // lookup the workshop category
	        $wc = new WorkshopCategory();
	        $category = $wc->find($thisWorkshop->workshopCategoryId);
	        if (is_null($category)) {
	            throw new Internal_Exception_Data('Category not found');
	        }
	        $this->view->category = $category->toArray();   
        	        
	        $this->view->upcomingEvents = $newEvents;
        	        
	        $this->view->hideTitle = true;
	    }
    }
}