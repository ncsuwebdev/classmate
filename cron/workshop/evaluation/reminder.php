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
 * @package    cron
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */
require_once '../../../library/Internal/Cron.php';

Internal_Cron::setup('../../../');

Zend_Loader::loadClass('CronStatus');
$cs = new CronStatus;

$cronId = 'workshop_evaluation_reminder';

if (!$cs->isEnabled($cronId)) {
    die();
}

$lastRunDt = $cs->getLastRunDt($cronId);
$ts = time();

$logger = Zend_Registry::get('logger');
$uc = Zend_Registry::get('userConfig');

$checkDt = new Zend_Date();
$checkDt->subHour($uc['numHoursEvaluationReminder']['value']);

$event = new Event();

if ($checkDt->getTimestamp() < $lastRunDt) {
	$events = $event->getEvents(null, null, $checkDt->getTimestamp(), time(), 'open');
} else {
	$events = $event->getEvents(null, null, $lastRunDt, time(), 'open');
}


$location   = new Location();
$workshop   = new Workshop();
$instructor = new Instructor();
$attendees  = new Attendees();
$evaluationUser = new EvaluationUser();

$lastRunDt = new Zend_Date($lastRunDt);
$currentDt = new Zend_Date();

foreach ($events as $e) {
    
    $startDt = strtotime($e->date . ' ' . $e->startTime);
    $endDt   = strtotime($e->date . ' ' . $e->endTime);
    
    $evalAvailableDt = new Zend_Date($endDt);
    $evalAvailableDt->addHour($uc['numHoursEvaluationAvailability']['value']);
    
    if ($evalAvailableDt->getTimestamp() < time()) {
	    $taken = $evaluationUser->getCompleted($e->eventId);    
	    
	    $thisLocation = $location->find($e->locationId);
	    if (is_null($thisLocation)) {
	        Internal_Cron::error('Location Not Found');
	    }
	    
	    $thisWorkshop = $workshop->find($e->workshopId);        
	    if (is_null($thisWorkshop)) {
	        Internal_Cron::error('Workshop not found');
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
	    	if ($a['attended'] == 1 && !in_array($a['userId'], $taken)) {
	        	$trigger = new EmailTrigger();
	            $trigger->setVariables($data);
	            $trigger->userId = $a['userId'];
	            $trigger->studentEmail = $a['emailAddress'];
	            $trigger->studentName = $a['firstName'] . ' ' . $a['lastName'];
	            
		        $trigger->dispatch('Event_Evaluation_Reminder');   
	        }	
	    }
    }
}

$cs->executed($cronId, $ts);