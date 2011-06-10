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
 * @package    Workshop_SignupController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with workshops
 *
 * @package    Workshop_SignupController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_SignupController extends Zend_Controller_Action 
{	
    /**
     * The main page a person sees when they want to sign up for an event
     *
     */
    public function indexAction()
    {    	
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->eventId)) {
        	throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $event = new Event();
        $thisEvent = $event->find($get->eventId);
        
        if (is_null($thisEvent)) {
        	throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        $status = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity()->accountId, $thisEvent->eventId);
        
        $this->view->event = $thisEvent->toArray();
        
        if($this->_request->isPost()) {

        	// Check if a password is required
        	if(isset($thisEvent->password)) {
        		$post = Zend_Registry::get('postFilter');
        		
        		// Check if the submitted password is correct
  				if($post->password == $thisEvent->password) {
  					$this->_redirect('/workshop/signup/reserve/eventId/'. $get->eventId, null);
  				} else {
  					$this->view->error = '* The password provided is incorrect';
  				}      	
  
        	} else {
        		$this->_redirect('/workshop/signup/reserve/eventId/'. $get->eventId, null);
        	}
        }
        
        $location = new Location();        
        $thisLocation = $location->find($thisEvent->locationId);        
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }
        $this->view->location = $thisLocation->toArray();
        
        
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
        	throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        $this->view->workshop = $thisWorkshop->toArray();
        
        
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);   

        $inst = array();
        foreach ($instructors as $i) {
        	$inst[] = $i['firstName'] . ' ' . $i['lastName'];
        }
        
        $this->view->instructors = $inst;
        
        $this->view->title = $this->view->translate("workshop-signup-index:signupFor", $thisWorkshop->title);
        $this->view->hideTitle = true;
        
        $events = $event->getEvents($thisWorkshop->workshopId, null, null, time(), null, 'open')->toArray();
        
        $newEvents = array();
          
	    foreach ($events as $e) {
	    	if ($e['eventId'] != $thisEvent->eventId) {
	            $e['status'] = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity()->accountId, $e['eventId']);
    			$e['workshop'] = $thisWorkshop->toArray();
    			$newEvents[] = $e;
	    	}
	    }   
	    
	    $this->view->events = $newEvents;
        
        $this->view->status = $status;
        
   		$this->view->layout()->setLayout('twocolumn');
    	$this->view->layout()->rightContent = $this->view->render('signup/right.phtml');
    }
    
    /**
     * Allows a user to sign up for an event.
     *
     */
    public function reserveAction()
    {
        $get = Zend_Registry::get('getFilter');          
        
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $event = new Event();
        $thisEvent = $event->find($get->eventId);
        
        if (is_null($thisEvent)) {
        	throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        
        if (is_null($thisLocation)) {
        	throw new Ot_Exception_Data('msg-error-noLocation');
        }

        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
        
        $instructorNames = array();
        $instructorEmails = array();
        
        foreach ($instructors as $i) {
            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
            $instructorEmails[] = $i['emailAddress'];
        }
        
        $status = 'attending';
        $attendee = new Event_Attendee();
        
        $status = $attendee->makeReservation(Zend_Auth::getInstance()->getIdentity()->accountId, $thisEvent->eventId);
        $thisEvent->roleSize++;

        $this->_helper->flashMessenger->addMessage($this->view->translate('msg-info-signedUp', $thisWorkshop->title));
        
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
            'instructorNames'           => implode(', ', $instructorNames),
            'instructorEmails'          => implode(', ', $instructorEmails),
            'studentEmail'              => Zend_Auth::getInstance()->getIdentity()->emailAddress,
            'studentName'               => Zend_Auth::getInstance()->getIdentity()->firstName . ' ' . Zend_Auth::getInstance()->getIdentity()->lastName,
            'studentUsername'           => Zend_Auth::getInstance()->getIdentity()->username,
        );
        
        if ($status == 'waitlist') {
            $waiting = $attendee->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
                    
            $position = 1;
                   
            foreach ($waiting as $w) {
                if ($data['userId'] == $w['userId']) {
                    break;
                }
                $position++;
            }
    
            $data['waitlistPosition'] = $position;
        }

        $trigger = new Ot_Trigger();
        $trigger->setVariables($data);
        
        if ($status == 'waitlist') {
        	$trigger->dispatch('Event_Signup_Waitlist');
        } else {
            $trigger->dispatch('Event_Signup');
        }
        
        if ($thisEvent->roleSize == $thisEvent->maxSize) {
        	$trigger->dispatch('Event_Signup_Full');
        }
        
        $this->_redirect('/');       
    }
    
    /**
     * Allows a user to cancel their reservation for an event.
     *
     */
    public function cancelAction()
    {
        $get = Zend_Registry::get('getFilter');	        
        
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $event      = new Event();
        $location   = new Location(); 
        $workshop   = new Workshop();
        $instructor = new Event_Instructor();
        $attendee   = new Event_Attendee();
        
        $thisEvent = $event->find($get->eventId);
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        $this->view->event = $thisEvent->toArray();

        $status = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity()->accountId, $thisEvent->eventId);
        if ($status != 'waitlist' && $status != 'attending') {
            throw new Ot_Exception_Data('msg-error-notAttending');
        }
        $this->view->status = $status;  
        
        $this->view->reservationCancelable = $event->isReservationCancelable($thisEvent->eventId);
        
        $thisLocation = $location->find($thisEvent->locationId);        
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }
        $this->view->location = $thisLocation->toArray();
        
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        $this->view->workshop = $thisWorkshop->toArray();        
        
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);        
        $inst = array();
        foreach ($instructors as $i) {
        	$inst[] = $i['firstName'] . ' ' . $i['lastName'];
        }
        $this->view->instructors = $inst;
        
        
        $events = $event->getEvents($thisWorkshop->workshopId, null, null, time(), null, 'open')->toArray();
        
        $newEvents = array();  
        foreach ($events as $e) {
            if ($e['eventId'] != $thisEvent->eventId) {
               $e['status'] = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity()->accountId, $e['eventId']);
               $e['workshop'] = $thisWorkshop->toArray();
               $newEvents[] = $e;
            }
        }   
        
        $this->view->events = $newEvents;
    	
        $form = Ot_Form_Template::delete('cancelReservation', 'workshop-signup-cancel:cancel', 'workshop-signup-cancel:keep');
        
    	if ($this->_request->isPost() && $form->isValid($_POST)) {
 
	        $instructorNames = array();
	        $instructorEmails = array();
	        
	        foreach ($instructors as $i) {
	            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
	            $instructorEmails[] = $i['emailAddress'];
	        }
        	        
            $attendee->cancelReservation(Zend_Auth::getInstance()->getIdentity()->accountId, $thisEvent->eventId);
            
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
	            'instructorNames'           => implode(', ', $instructorNames),
	            'instructorEmails'          => implode(', ', $instructorEmails),
	            'studentEmail'              => Zend_Auth::getInstance()->getIdentity()->emailAddress,
	            'studentName'               => Zend_Auth::getInstance()->getIdentity()->firstName . ' ' . Zend_Auth::getInstance()->getIdentity()->lastName,
	            'studentUsername'           => Zend_Auth::getInstance()->getIdentity()->username
	        );       
	        
            $this->_helper->flashMessenger->addMessage($this->view->translate('msg-info-canceled', $thisWorkshop->title));
        
            $trigger = new Ot_Trigger();
            $trigger->setVariables($data);
            $trigger->dispatch('Event_Cancel_Reservation');   	        

            $account = new Ot_Account();
            
            if ($status != 'waitlist') {
		    	$waiting = $attendee->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
		        if (count($waiting) != 0) {	            
		            $newAccount = $account->find($waiting[0]['accountId']);
		            
		            if (!is_null($newAccount)) {
		            	$attendee->makeReservation($newAccount->accountId, $thisEvent->eventId);
		            	
			            $data['studentEmail']    = $newAccount->emailAddress;
			            $data['studentName']     = $newAccount->firstName . ' ' . $newAccount->lastName;
			            $data['studentUsername'] = $newAccount->username;
			            
			            $trigger = new Ot_Trigger();
			            $trigger->setVariables($data);
			            $trigger->dispatch('Event_Waitlist_To_Attending'); 
		            }  	            
		        }	
            }        
        
            $this->_redirect('/');
    	}
    	
    	$this->view->form = $form;
    	$this->view->layout()->setLayout('twocolumn');
    	$this->view->layout()->rightContent = $this->view->render('signup/right.phtml'); 
    }
    
    public function editAllReservationsAction()
    {}
}