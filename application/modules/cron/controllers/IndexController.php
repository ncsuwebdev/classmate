<?php
/**
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    Cron
 * @subpackage Cron_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Main cron controller
 *
 * @package    Cron_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Cron_IndexController extends Zend_Controller_Action 
{    
	/**
	 * Unix timestamp of the date the cron job was last run.
	 *
	 * @var int
	 */
	protected $_lastRunDt = 0;
	
    /**
     * Initialization function
     *
     */
    public function init()
    {
    	set_time_limit(0);
    	
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNeverRender();
    	
		$action = $this->_request->getActionName();
		
		$cs = new Ot_Cron_Status();
		
		if (!$cs->isEnabled($action)) {
			die();
		}
		
		$this->_lastRunDt = $cs->getLastRunDt($action);
		
		$cs->executed($action, time());
		
    	parent::init();
    }

    
    /**
     * Cron job to process the email queue
     *
     */
    public function emailQueueAction()
    {       	
		$eq = new Ot_Email_Queue();
		
		$messages = $eq->getWaitingEmails(20);
		
		foreach ($messages as $m) {
		    try {
		        $m['zendMailObject']->send();
		
		        $m['status'] = 'sent';
		        $m['sentDt'] = time();
		
		    } catch (Exception $e) {
		        $m['status'] = 'error';
		        $m['sentDt'] = 0;
		    }
		
		    $where = $eq->getAdapter()->quoteInto('queueId = ?', $m['queueId']);
		
		    $eq->update($m, $where);
		    
		    $logOptions = array(
                    'attributeName' => 'queueId',
                    'attributeId'   => $m['queueId'],
            );
                
            $this->_helper->log(Zend_Log::INFO, 'Mail Sent', $logOptions);
		}    	
    }
    
    /**
     * Cron job to process event evaluation notifications.
     * 
     */
    public function workshopEvaluationNotificationAction() {
    	$config = Zend_Registry::get('config');
		
		$checkDtStart = new Zend_Date($this->_lastRunDt);
		
		$checkDtEnd = new Zend_Date();
        
		$event = new Event();
		
		$events = $event->getEvents(null, null, null, $checkDtStart->getTimestamp(), $checkDtEnd->getTimestamp(), 'open');
		
		$location   = new Location();
		$workshop   = new Workshop();
		$instructor = new Event_Instructor();
		$attendee   = new Event_Attendee();
		$eu         = new Evaluation_User();
		
		foreach ($events as $e) {
		    
		    $startDt = strtotime($e->date . ' ' . $e->startTime);
            $endDt   = strtotime($e->date . ' ' . $e->endTime);
            
		    if ($checkDtStart->getTimestamp() < $endDt && $checkDtEnd->getTimestamp() >= $endDt) {
		    	
		    	echo 'Event to Send:';
		    	var_dump($e->toArray(), '<br /><br />');
    			    
    			$thisLocation = $location->find($e->locationId);
    			if (is_null($thisLocation)) {
    				throw new Ot_Exception_Data('msg-error-noLocation');
    			}
    			    
    			$thisWorkshop = $workshop->find($e->workshopId);        
    			if (is_null($thisWorkshop)) {
    				throw new Ot_Exception_Data('msg-error-noWorkshop');
    			}
    			                    
    		    $instructors = $instructor->getInstructorsForEvent($e->eventId);
    		                    
    		    $instructorNames = array();
    		    $instructorEmails = array();
    		            
    		    foreach ($instructors as $i) {
    		        $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
    		        $instructorEmails[] = $i['emailAddress'];
    		    }       
    		            
    		    $data = array(
    		        'workshopName'              => $thisWorkshop->title,
    		        'workshopDate'              => date('m/d/Y', $startDt),
    		        'workshopStartTime'         => date('g:i a', $startDt),
    		        'workshopEndTime'           => date('g:i a', $endDt),
    		        'workshopMinimumEnrollment' => $e->minSize,
    		        'workshopCurrentEnrollment' => $e->roleSize,
    		        'locationName'              => $thisLocation->name,
    		        'locationAddress'           => $thisLocation->address,
    		        'instructorNames'           => implode(', ', $instructorNames),
    		        'instructorEmails'          => implode(', ', $instructorEmails),
    		    );      
    		        
    		    $attenders = $attendee->getAttendeesForEvent($e->eventId, 'attending', true);
    		    
    		    
    
    		    foreach ($attenders as $a) {
    		        	$trigger = new Ot_Trigger();
    		            $trigger->setVariables($data);
    		            
    		            $trigger->accountId   = $a['accountId'];
    		            $trigger->studentEmail = $a['emailAddress'];
    		            $trigger->studentName  = $a['firstName'] . ' ' . $a['lastName'];
    		            $trigger->studentUsername = $a['username'];
    		            
    			        $trigger->dispatch('Event_Evaluation_Notification');
    	    	}
		    }
		}
    }
    
    /**
     * Cron job to process the workshop evaluation reminder
     *
     */
    public function workshopEvaluationReminderAction()
    {       	
		$config = Zend_Registry::get('config');
		
		$checkDtStart = new Zend_Date($this->_lastRunDt);
		$checkDtStart->subHour($config->user->numHoursEvaluationReminder->val);
		
		$checkDtEnd = new Zend_Date();
        $checkDtEnd->subHour($config->user->numHoursEvaluationReminder->val);
        
		$event = new Event();
		
		$events = $event->getEvents(null, null, null, $checkDtStart->getTimestamp(), $checkDtEnd->getTimestamp(), 'open');
		
		$location   = new Location();
		$workshop   = new Workshop();
		$instructor = new Event_Instructor();
		$attendee   = new Event_Attendee();
		$eu         = new Evaluation_User();
		
		foreach ($events as $e) {
		    
		    $startDt = strtotime($e->date . ' ' . $e->startTime);
            $endDt   = strtotime($e->date . ' ' . $e->endTime);
            
		    if ($checkDtStart->getTimestamp() < $endDt && $checkDtEnd->getTimestamp() >= $endDt) {
		    	
		    	
		    
    		    $evalAvailableDt = new Zend_Date($endDt);
    		    $evalAvailableDt->addHour($config->user->numHoursEvaluationAvailability->val);
    		    
    		    if ($evalAvailableDt->getTimestamp() > time()) {
    		        
    			    $taken = $eu->getCompleted($e->eventId);    
    			    
    			    $thisLocation = $location->find($e->locationId);
    			    if (is_null($thisLocation)) {
    			        throw new Ot_Exception_Data('msg-error-noLocation');
    			    }
    			    
    			    $thisWorkshop = $workshop->find($e->workshopId);        
    			    if (is_null($thisWorkshop)) {
    			        throw new Ot_Exception_Data('msg-error-noWorkshop');
    			    }
    			                    
    			    $instructors = $instructor->getInstructorsForEvent($e->eventId);
    			                    
    			    $instructorNames = array();
    			    $instructorEmails = array();
    			            
    			    foreach ($instructors as $i) {
    			        $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
    			        $instructorEmails[] = $i['emailAddress'];
    			    }       
    			            
    			    $data = array(
    			        'workshopName'              => $thisWorkshop->title,
    			        'workshopDate'              => date('m/d/Y', $startDt),
    			        'workshopStartTime'         => date('g:i a', $startDt),
    			        'workshopEndTime'           => date('g:i a', $endDt),
    			        'workshopMinimumEnrollment' => $e->minSize,
    			        'workshopCurrentEnrollment' => $e->roleSize,
    			        'locationName'              => $thisLocation->name,
    			        'locationAddress'           => $thisLocation->address,
    			        'instructorNames'           => implode(', ', $instructorNames),
    			        'instructorEmails'          => implode(', ', $instructorEmails),
    			    );      
    			        
    			    $attending = $attendee->getAttendeesForEvent($e->eventId, 'attending');
    
    			    foreach ($attending as $a) {
    			    	if ($a['attended'] == 1 && !in_array($a['accountId'], $taken)) {
    			    		
    			        	$trigger = new Ot_Trigger();
    			            $trigger->setVariables($data);
    			            
    			            $trigger->accountId   = $a['accountId'];
    			            $trigger->studentEmail = $a['emailAddress'];
    			            $trigger->studentName  = $a['firstName'] . ' ' . $a['lastName'];
    			            $trigger->studentUsername = $a['username'];
    			            
    				        $trigger->dispatch('Event_Evaluation_Reminder');
    				        
    			        }	
    			    }
    		    }
		    }
		}
    }    
    
    public function workshopSignupLowAttendanceAction()
    {
		$config = Zend_Registry::get('config');
		
		$event = new Event();
		
		$events = $event->getEvents(null, null, null, time(), null, 'open');
		
		$location = new Location();
		$workshop = new Workshop();
		$instructor = new Event_Instructor();
		
		$checkDt = new Zend_Date($this->_lastRunDt);
		$checkDt->addHour($config->user->numHoursLowAttendanceNotification->val);
		
		foreach ($events as $e) {
			
			if ($e->roleSize < $e->minSize) {
			    
				$startDt = strtotime($e->date . ' ' . $e->startTime);
				$endDt   = strtotime($e->date . ' ' . $e->endTime);
		
				if ($checkDt->getTimestamp() > $startDt && $this->_lastRunDt  < $startDt) {
				
			        $thisLocation = $location->find($e->locationId);
			        if (is_null($thisLocation)) {
			            throw new Ot_Exception_Data('msg-error-noLocation');
			        }
			
			        $thisWorkshop = $workshop->find($e->workshopId);        
			        if (is_null($thisWorkshop)) {
			            throw new Ot_Exception_Data('msg-error-noWorkshop');
			        }
			                
			        $instructors = $instructor->getInstructorsForEvent($e->eventId);
			                
			        $instructorNames = array();
			        $instructorEmails = array();
			        
			        foreach ($instructors as $i) {
			            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
			            $instructorEmails[] = $i['emailAddress'];
			        }	
			
			        $data = array(
			            'workshopName'              => $thisWorkshop->title,
			            'workshopDate'              => date('m/d/Y', $startDt),
			            'workshopStartTime'         => date('g:i a', $startDt),
			            'workshopEndTime'           => date('g:i a', $endDt),
			            'workshopMinimumEnrollment' => $e->minSize,
			            'workshopCurrentEnrollment' => $e->roleSize,
			            'locationName'              => $thisLocation->name,
			            'locationAddress'           => $thisLocation->address,
			            'instructorNames'           => implode(', ', $instructorNames),
			            'instructorEmails'          => implode(', ', $instructorEmails),
			        );      
			
			        $trigger = new Ot_Trigger();
			        $trigger->setVariables($data);
			        
			        $trigger->dispatch('Event_LowAttendance');
				}
			}
		}
    }
    
    public function workshopSignupReminderAction()
    {
    	$config = Zend_Registry::get('config');

		$event = new Event();
		
		$events = $event->getEvents(null, null, null, time(), null, 'open');
		
		$location   = new Location();
		$workshop   = new Workshop();
		$instructor = new Event_Instructor();
		$attendees  = new Event_Attendee();
		
		$lastRunDt = new Zend_Date($this->_lastRunDt);
		$currentDt = new Zend_Date();

		foreach ($events as $e) {
		    
		    $startDt = strtotime($e->date . ' ' . $e->startTime);
		    $endDt   = strtotime($e->date . ' ' . $e->endTime);
            
		    $firstDt = new Zend_Date($startDt);
		    $firstDt->subHour($config->user->numHoursFirstReminder->val);
		    
		    $finalDt = new Zend_Date($startDt);
		    $finalDt->subHour($config->user->numHoursFinalReminder->val);
		    
		    $notification = null;
		    
		    if ($firstDt->getTimestamp() > $lastRunDt->getTimestamp() && $firstDt->getTimestamp() < $currentDt->getTimestamp()) {
		        $notification = 'first';	
		    }
		    
		    if ($finalDt->getTimestamp() > $lastRunDt->getTimestamp() && $finalDt->getTimestamp() < $currentDt->getTimestamp()) {
		        $notification = 'final';    
		    }
		    
		    if (!is_null($notification)) {
		        $thisLocation = $location->find($e->locationId);
		        if (is_null($thisLocation)) {
		            throw new Ot_Exception_Data('msg-error-noLocation');
		        }
		    
		        $thisWorkshop = $workshop->find($e->workshopId);        
		        if (is_null($thisWorkshop)) {
		            throw new Ot_Exception_Data('msg-error-noWorkshop');
		        }
		                    
		        $instructors = $instructor->getInstructorsForEvent($e->eventId);
		                    
		        $instructorNames = array();
		        $instructorEmails = array();
		            
		        foreach ($instructors as $i) {
		            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
		            $instructorEmails[] = $i['emailAddress'];
		        }       
		            
		        $data = array(
		            'workshopName'              => $thisWorkshop->title,
		            'workshopDate'              => date('m/d/Y', $startDt),
		            'workshopStartTime'         => date('g:i a', $startDt),
		            'workshopEndTime'           => date('g:i a', $endDt),
		            'workshopMinimumEnrollment' => $e->minSize,
		            'workshopCurrentEnrollment' => $e->roleSize,
		            'locationName'              => $thisLocation->name,
		            'locationAddress'           => $thisLocation->address,
		            'instructorNames'           => implode(', ', $instructorNames),
		            'instructorEmails'          => implode(', ', $instructorEmails),
		        );      
		        
		        $attending = $attendees->getAttendeesForEvent($e->eventId, 'attending');
		        
		        foreach ($attending as $a) {
		        	$trigger = new Ot_Trigger();
		            $trigger->setVariables($data);
		            $trigger->accountId = $a['accountId'];
		            $trigger->studentEmail = $a['emailAddress'];
		            $trigger->studentName = $a['firstName'] . ' ' . $a['lastName'];
			                 
		            if ($notification == 'final') {
		            	$trigger->dispatch('Event_Attendee_Final_Reminder');
		            } else {
		            	$trigger->dispatch('Event_Attendee_First_Reminder');
		            }   
		        }	
		        
		        $trigger = new Ot_Trigger();
		        $trigger->setVariables($data);
		        
		        if ($notification == 'final') {
		            $trigger->dispatch('Event_Instructor_Final_Reminder');
		        } else {
		        	$trigger->dispatch('Event_Instructor_First_Reminder');
		        }
		    }
		}    	
    }

}
