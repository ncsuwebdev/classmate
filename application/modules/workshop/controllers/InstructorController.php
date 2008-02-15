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
        
        $this->_checkValidViewer($instructors);
        
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
        		
        $this->view->globalAcl = array(
            'editEvent'       => $this->_acl->isAllowed($this->_role, 'workshop_schedule', 'editEvent'),
        );
        
        $this->_helper->viewRenderer('template');
        
	}
	
	protected function _checkValidViewer($instructorList)
	{
	    $iList = array();
        foreach ($instructorList as $i) {
            $iList[] = $i['userId'];
        }
        
        if (!$this->_acl->isAllowed($this->_role, $this->_resource, 'viewAllInstructorPages')
            && !in_array(Zend_Auth::getInstance()->getIdentity(), $iList)) {
                throw new Internal_Exception_Access('You do not have access to view this workshop');
        }		
	}
	
	/**
	 * Allows a user to view all the intructor pages
	 *
	 */
	public function viewAllInstructorPagesAction()
	{}

	
    /**
     * The main instructor page for an event.
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

        $this->view->acl = array(
            'addAttendee'     => $this->_acl->isAllowed($this->_role, $this->_resource, 'addAttendee'),
            'deleteAttendee'  => $this->_acl->isAllowed($this->_role, $this->_resource, 'deleteAttendee'),
            'promoteAttendee' => $this->_acl->isAllowed($this->_role, $this->_resource, 'promoteAttendee'),
        );
        
        if ($this->view->acl['addAttendee']) {
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
            
            $userExclude = array();
            foreach ($attendeeList as $a) {
            	$userExclude[] = $a['userId'];
            }
            
            foreach ($waitlist as $w) {
            	$userExclude[] = $w['userId'];
            }
            
            $instructors = $this->view->instructors;
            
            foreach ($instructors as $i) {
            	$userExclude[] = $i['userId'];
            }
            
            //get all the users available for the instructor list
            $profile = new Profile();
            
            $where = null;
            if (count($userExclude) != 0) {
                $where = $profile->getAdapter()->quoteInto('userId NOT IN (?)', $userExclude);
            }
            
            $profiles = $profile->fetchAll($where, array('lastName', 'firstName'))->toArray();            
            foreach ($profiles as $p) {
                $users[$p['userId']] = $p['lastName'] . ", " . $p['firstName'];            
            }
            
            $this->view->users = $users;        
        }
        
        $this->view->toolTemplate = $this->view->render('instructor/index.tpl');
    }
    
    /**
     * Allows a user to add an attendee to the event.
     *
     */
    public function addAttendeeAction()
    {
    	if ($this->_request->isPost()) {
    		
    		$post = Zend_Registry::get('post');
    		
    		$filter = Zend_Registry::get('inputFilter');
    		
	        if (!isset($post['eventId'])) {
	            throw new Internal_Exception_Input('Event ID not set');
	        }
	        
	        // lookup the event
	        $event = new Event();
	        $thisEvent = $event->find($filter->filter($post['eventId']));
	        if (is_null($thisEvent)) {
	            throw new Internal_Exception_Data('Event not found');
	        }
	        
	        // lookup the instructors
	        $instructor = new Instructor();
	        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
	        
	        $this->_checkValidViewer($instructors);
	        
	        // lookup the attendees of the event
	        $attendees = new Attendees();
	        $attendeeList = $attendees->getAttendeesForEvent($thisEvent->eventId, 'attending');
	        
	        $waitlist = $attendees->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
	        
    	    $userExclude = array();
            foreach ($attendeeList as $a) {
                $userExclude[] = $a['userId'];
            }
            
            foreach ($waitlist as $w) {
                $userExclude[] = $w['userId'];
            }

            foreach ($instructors as $i) {
                $userExclude[] = $i['userId'];
            }	       

            foreach ($post['userId'] as $u) {
            	$u = $filter->filter($u);
            	
            	if (!in_array($userExclude)) {
            		$attendees->makeReservation($u, $thisEvent->eventId, $filter->filter($post['type']));
            	}
            }
            
            $this->_redirect('/workshop/instructor/?eventId=' . $thisEvent->eventId);
    	}
    }
    
    /**
     * Allows a user to delete an attendee from an event.
     *
     */
    public function deleteAttendeeAction()
    {

        $get = Zend_Registry::get('get');
            
        $filter = Zend_Registry::get('inputFilter');
            
        if (!isset($get['eventId'])) {
            throw new Internal_Exception_Input('Event ID not set');
        }
            
        // lookup the event
        $event = new Event();
        $thisEvent = $event->find($filter->filter($get['eventId']));
        if (is_null($thisEvent)) {
            throw new Internal_Exception_Data('Event not found');
        }    	
        
        // lookup the instructors
        $instructor = new Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);        
        $this->_checkValidViewer($instructors);
        
        $attendees = new Attendees();
        $attendees->cancelReservation($filter->filter($get['userId']), $thisEvent->eventId);

        
        $this->_redirect('/workshop/instructor/?eventId=' . $thisEvent->eventId);
    }
    
    /**
     * Allows a user to set the attendance status of a user
     *
     */
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
    		
            // lookup the instructors
            $instructor = new Instructor();
            $instructors = $instructor->getInstructorsForEvent($eventId);    		
    		$this->_checkValidViewer($instructors);
    		
    		$attendees = new Attendees();
    		
    		$where = $attendees->getAdapter()->quoteInto('eventId = ?', $eventId) . ' AND ' . 
    		    $attendees->getAdapter()->quoteInto('userId = ?', $userId);
    		    
    		$attendees->update($data, $where);
    		
    		echo "Updated!";
    	}
    }
    
    /**
     * Allows a user to contact all the people in their event.
     *
     */
    public function contactAction()
    {
    	$filter = Zend_Registry::get('inputFilter');
    	
        
        $profile = new Profile();
        $thisProfile = $profile->find(Zend_Auth::getInstance()->getIdentity())->toArray();
            	
    	if ($this->_request->isPost()) {
    		$post = Zend_Registry::get('post');
    		
	    	if (!isset($post['eventId'])) {
	            throw new Internal_Exception_Input('Event ID not set');
	        }
	        
	        // lookup the event
	        $event = new Event();
	        $thisEvent = $event->find($filter->filter($post['eventId']));
	        if (is_null($thisEvent)) {
	            throw new Internal_Exception_Data('Event not found');
	        }
	        
    		$recipients = array();
    		$attendees = new Attendees();
    		
    		if (isset($post['attending']) && $filter->filter($post['attending']) == 'ON') {
                $attendeeList = $attendees->getAttendeesForEvent($thisEvent->eventId, 'attending');
	            $recipients = array_merge($recipients, $attendeeList);
    		}
    		
    	    if (isset($post['waitlist']) && $filter->filter($post['waitlist']) == 'ON') {
                $attendeeList = $attendees->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
                $recipients = array_merge($recipients, $attendeeList);
            }    

            $instructor = new Instructor();
            $instructorList = $instructor->getInstructorsForEvent($thisEvent->eventId);
                            
            if (isset($post['instructors']) && $filter->filter($post['instructors']) == 'ON') {
            	$recipients = array_merge($recipients, $instructorList);
            }
            
            $this->_checkValidViewer($instructorList);

            $mail = new Zend_Mail();
            $mail->setFrom($thisProfile['emailAddress'], $thisProfile['firstName'] . ' ' . $thisProfile['lastName']);
            $mail->setSubject($filter->filter($post['subject']));
            $mail->setBodyText($filter->filter($post['message']));
            
            foreach ($recipients as $r) {
            	$mail->addTo($r['emailAddress']);
            }
            
            $eq = new EmailQueue();
            
            $data = array(
                'attributeName'  => 'eventId',
                'attributeId'    => $thisEvent->eventId,
                'zendMailObject' => $mail,
            );
    		
            $eq->queueEmail($data);
            
    		$this->_redirect('/workshop/instructor/contactConfirm/?eventId=' . $thisEvent->eventId);
    	}
    	
    	$this->view->profile = $thisProfile;
    	
    	$this->_setupTemplate();
    	
    	$this->view->toolTemplate = $this->view->render('instructor/contact.tpl');
    }
    
    
    /**
     * Displays the results of an evaluation as long as the user requesting the
     * page is an instructor of the event.
     *
     */
    public function evaluationResultsAction()
    {
        $filter = Zend_Registry::get('inputFilter');        
        $userId = Zend_Auth::getInstance()->getIdentity();
        
        $get = Zend_Registry::get('get');
        
        if (!isset($get['eventId'])) {
            throw new Internal_Exception_Input('Event ID not set');
        }
        
        $eventId = $filter->filter($get['eventId']);
        
        if ($eventId == '') {
            throw new Internal_Exception_Input('Event ID has no value');
        }
        
        $this->view->eventId = $eventId;

        $event = new Event();
        
        $status = $event->getStatusOfUserForEvent($userId, $eventId);
        
        if ($status != "instructor") {
            throw new Internal_Exception_Data('You do not appear to be an instructor for this event.');
        }
        
        // get the evaluationId from the eventId
        $evaluation = new Evaluation();
        $where = $evaluation->getAdapter()->quoteInto('eventId = ?', $eventId);
        $evaluations = $evaluation->fetchAll($where);
        if (is_null($evaluations) || $evaluations->count() == 0) {
            $this->view->noEvaluationsYet = true;
        }
        
        $this->view->totalEvaluations = $evaluations->count();
        
        $ca = new CustomAttribute();
        
        $evaluationResults = array();
        
        $questions = $ca->getAttributesForNode('evaluations');
        
        foreach ($questions as &$q) {
            $q['options'] = $ca->convertOptionsToArray($q['options']);
            
            $answers = array();
            foreach ($evaluations as $e) {
                $tmpAnswers = $ca->getData($q['nodeId'], $e->evaluationId);
                
                $tmp = array();
                foreach ($tmpAnswers as $ta) {
                    $tmp[$ta['attribute']['attributeId']] = $ta['value'];
                }
                
                $answers[] = $tmp;
            }
           
            if ($q['type'] == 'ranking' || $q['type'] == 'select' || $q['type'] == 'radio') {
                
                foreach ($q['options'] as $key => $value) {
                    
                    $answerCount = 0;
                    
                    foreach ($answers as $a) {
                        if ($a[$q['attributeId']] == $value) {
                            $answerCount++;
                        }
                    }
                    
                    $q['results'][] = array('answerLabel' => $value, 'answerCount' => $answerCount);
                }
                
            } else {
                
                foreach ($answers as $a) {
                    $q['results'][] = $a[$q['attributeId']];   
                }
            }
        }
        
        $this->view->evaluationResults = $questions;              
        
        $this->view->javascript = array('excanvas.js', 'plootr.js', 'tabletochart.js', 'slidingTabs.js');
        
        $this->_setupTemplate();
        
        $this->_helper->viewRenderer('template');
        
        $this->view->toolTemplate = $this->view->render('instructor/evaluationresults.tpl');
    }
    
    /**
     * Allows a user to see a confirmation that their emails were queued.
     *
     */
    public function contactConfirmAction()
    {
    	$this->_setupTemplate();
    	$this->view->toolTemplate = $this->view->render('instructor/contactconfirm.tpl');
    }
}