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
 * @package    Workshop_InstructorController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with instructors
 *
 * @package    Workshop_InstructorController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_InstructorController extends Zend_Controller_Action 
{    
    
    protected function _checkValidViewer($instructorList)
    {
        $iList = array();
        foreach ($instructorList as $i) {
            $iList[] = $i['accountId'];
        }
        
        if (!$this->_helper->hasAccess('view-all-instructor-pages')
            && !in_array(Zend_Auth::getInstance()->getIdentity()->accountId, $iList)) {
                throw new Ot_Exception_Access('msg-error-noWorkshopAccess');
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
        $get = Zend_Registry::get('getFilter');
                
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        // lookup the event
        $event = new Event();
        $thisEvent = $event->find($get->eventId);
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        $this->view->event = $thisEvent->toArray();
        
        // lookup the instructors
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
        
        $instructorList = array();
        foreach ($instructors as $i) {
            $instructorList[] = $i['firstName'] . ' ' . $i['lastName'];
        }
        $this->view->instructors = $instructorList;
        
        $this->_checkValidViewer($instructors);
        
        // lookup the location of the event
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }
        $this->view->location = $thisLocation->toArray();
        
        // lookup the corresponding workshop
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        $this->view->workshop = $thisWorkshop->toArray();
        
        // lookup the attendees of the event
        $attendee = new Event_Attendee();
        $attendeeList = $attendee->getAttendeesForEvent($thisEvent->eventId, 'attending');
        $this->view->attendeeList = $attendeeList;
        
        $waitlist = $attendee->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
        $this->view->waitlist = $waitlist;     

        $this->view->acl = array(
            'addAttendee'      => $this->_helper->hasAccess('add-attendee'),
            'takeRoll'         => $this->_helper->hasAccess('take-roll'),
            'editEvent'        => $this->_helper->hasAccess('edit-event', 'workshop_schedule'),
            'deleteAttendee'   => $this->_helper->hasAccess('delete-attendee'),
            'promoteAttendee'  => $this->_helper->hasAccess('promote-attendee'),
            'printSignupSheet' => $this->_helper->hasAccess('print-signup-sheet'),
            'contact'          => $this->_helper->hasAccess('contact')
        );
        
        $this->view->isEditable = $event->isEditable($thisEvent->eventId);
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
    
    /**
     * Displays a print view of a signup sheet
     */
    public function exportSignupSheetAction()
    {
        $get = Zend_Registry::get('getFilter');
                
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        // lookup the event
        $event = new Event();
        $thisEvent = $event->find($get->eventId);
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        
        // lookup the instructors
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
        
        $instructorList = array();
        foreach ($instructors as $i) {
            $instructorList[] = $i['firstName'] . ' ' . $i['lastName'];
        }
        
        
        $this->_checkValidViewer($instructors);
        
        // lookup the location of the event
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }
        
        
        // lookup the corresponding workshop
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        
        // lookup the attendees of the event
        $attendee = new Event_Attendee();
        $attendeeList = $attendee->getAttendeesForEvent($thisEvent->eventId, 'attending');
        
        
        if($this->_request->isPost()) {
            
            $post = Zend_Registry::get('postFilter');
            
            if(!isset($post->fileType)) {
                throw new Ot_Exception_Data('msg-error-noFileType');
            }
            
            $thisEvent = $thisEvent->toArray();
            $thisEvent['instructors'] = $instructorList;
            $thisEvent['location'] = $thisLocation['name'];
            $thisEvent['workshopTitle'] = $thisWorkshop['title'];
            $thisEvent['attendeeList'] = $attendeeList;
            
            
            
            $filename = isset($post->fileName) ? $post->fileName : 'Signup_sheet_' . str_replace(' ', '_', $thisWorkshop['name']) . '_' . date('m_d_Y');
            $path = APPLICATION_PATH . '/../cache/';
            
            if($post->fileType == 'pdf') {
                $filename .= '.pdf';
                
                $pdf = new Event_Pdf($filename);
                
                $pdfString = $pdf->generateSignupSheet($thisEvent);
                
                header('Content-Disposition: inline; filname=' . $filename);
                header('Content-length: ' . strlen($pdfString));
                header('Content-type: application/x-pdf');
                echo $pdfString;
                
            } else if ($post->fileType == 'xls') {
                $filename .= '.xlsx';
                $excel = new Event_Excel();
                
                $objPHPExcel = $excel->generateSignupSheet($thisEvent);

                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

                $objWriter->save($path . $filename);

                header('Content-Disposition: attachment; filename=' . $filename);
                header('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                echo file_get_contents($path . $filename);
                
            } else if ($post->fileType == 'csv') {
                $filename .= '.csv';
                
                $outString = 'First Name,Last Name' . chr(10);
                
                foreach ($thisEvent['attendeeList'] as $a) {
                    $outString .= $a['firstName'] . ',' . $a['lastName'] . chr(10);
                }
                
                header('Content-Disposition: attachment; filename=' . $filename);
                header('Content-Type: application/csv');
                
                echo $outString;
            }
            
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNeverRender();
            
        } else {
            $this->view->event = $thisEvent->toArray();
            $this->view->instructors = $instructorList;
            $this->view->location = $thisLocation->toArray();
            $this->view->workshop = $thisWorkshop->toArray();
            $this->view->attendeeList = $attendeeList;
        }
    }
    
    /**
     * Moves a user from the waitlist to the attendee roster
     *
     */
    public function promoteAttendeeAction()
    {
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $eventId = $get->eventId;
        
        if (!isset($get->accountId)) {
            throw new Ot_Exception_Input('msg-error-accountIdNotSet');
        }
        
        $accountId = $get->accountId;
        
        $attendee = new Event_Attendee();
        
        // lookup the event
        $event = new Event();
        $thisEvent = $event->find($eventId);
        
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }           
        
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
           
        $instructorNames = array();
        $instructorEmails = array();
        
        foreach ($instructors as $i) {
            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
            $instructorEmails[] = $i['emailAddress'];
        }
        
        $otAccount = new Ot_Account();       
        $up = $otAccount->find($accountId);
        
        if (is_null($up)) {
            throw new Ot_Exception_Input('msg-error-noAccount');
        }            
                    
        $attendee->makeReservation($accountId, $eventId, 'attending');
        
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
            'studentAccountId'          => $accountId,
            'instructorNames'           => implode(', ', $instructorNames),
            'instructorEmails'          => implode(', ', $instructorEmails),
            'studentEmail'              => $up->emailAddress,
            'studentFirstName'          => $up->firstName,
            'studentLastName'           => $up->lastName,
            'studentUsername'           => $up->username
        );
        
        $trigger = new Ot_Trigger();
        $trigger->setVariables($data);
        $trigger->dispatch('Instructor_Promote_User_Waitlist_To_Attending');
        
        $this->_helper->flashMessenger->addMessage('msg-info-userPromoted');        
        $this->_redirect('/workshop/instructor/?eventId=' . $eventId);
    }
    
    /**
     * Allows a user to add an attendee to the event.
     *
     */
    public function addAttendeeAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
           $this->_helper->layout->disableLayout();    
        } else {
            $this->_helper->pageTitle('workshop-instructor-addAttendee:title');
        } 
        
        $messages = array();
            
        $get = Zend_Registry::get('getFilter');
            
        if (!isset($get->eventId)) {
            throw new Internal_Exception_Input('msg-error-eventIdNotSet');
        }
            
        // lookup the event
        $event = new Event();
        $thisEvent = $event->find($get->eventId);
        if (is_null($thisEvent)) {
            throw new Internal_Exception_Data('msg-error-noEvent');
        }
        
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Internal_Exception_Data('msg-error-noWorkshop');
        }
        
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
        
        if (is_null($thisLocation)) {
            throw new Internal_Exception_Data('msg-error-noLocation');
        }           
        
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
        $this->_checkValidViewer($instructors);
           
        $instructorNames = array();
        $instructorEmails = array();
        
        foreach ($instructors as $i) {
            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
            $instructorEmails[] = $i['emailAddress'];
        }
        
        // lookup the attendees of the event
        $attendee = new Event_Attendee();
        $attendeeList = $attendee->getAttendeesForEvent($thisEvent->eventId, 'attending');
        
        $waitlist = $attendee->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
        
        $userExclude = array();
        foreach ($attendeeList as $a) {
            $userExclude[] = $a['accountId'];
        }
        
        foreach ($waitlist as $w) {
            $userExclude[] = $w['accountId'];
        }

        foreach ($instructors as $i) {
            $userExclude[] = $i['accountId'];
        }

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
        ); 
        
        $form = $instructor->addAttendeeForm(array('eventId' => $thisEvent->eventId));

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                foreach ($form->getValue('users') as $accountId) {
                    
                    if (!in_array($accountId, $userExclude)) {
                        $account = new Ot_Account();       
                        $user = $account->find($accountId);
        
                        if (!is_null($user)) {
                            $status = $attendee->makeReservation($accountId, $thisEvent->eventId, $form->getValue('type'));
                            
                            $data['studentEmail'] = $user->emailAddress;
                            $data['studentFirstName']  = $user->firstName;
                            $data['studentLastName']   = $user->lastName;
                            $data['studentUsername']   = $user->username;
                            $data['studentAccountId']  = $user->accountId;
                            
                            if ($status == 'waitlist') {
                                $waiting = $attendee->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
                                        
                                $position = 1;
                                       
                                foreach ($waiting as $w) {
                                    if ($accountId == $w['accountId']) {
                                        break;
                                    }
                                    $position++;
                                }
                        
                                $data['waitlistPosition'] = $position; 

                                $trigger = new Ot_Trigger();
                                $trigger->setVariables($data);
                                $trigger->dispatch('Instructor_Registered_User_For_Waitlist');
                                
                            } else {
                                
                                $trigger = new Ot_Trigger();
                                $trigger->setVariables($data);
                                $trigger->dispatch('Instructor_Registered_User');
                            }
                        }
                    }
                }
                
                $this->_helper->flashMessenger->addMessage('msg-info-userAdded');
                
                $this->_redirect('/workshop/instructor/?eventId=' . $thisEvent->eventId);
            } else {
                
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.autocomplete.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.autocomplete.css');      
        $this->view->form = $form;

    }
    
    /**
     * Allows a user to delete an attendee from an event.
     *
     */
    public function removeAttendeeAction()
    {
        $get = Zend_Registry::get('getFilter');
            
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
            
        // lookup the event
        $event = new Event();
        $thisEvent = $event->find($get->eventId);
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
            
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
            
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }           
            
        $otAccount = new Ot_Account();       
        $up = $otAccount->find($get->accountId);
            
        if (is_null($up)) {
            throw new Ot_Exception_Input('msg-error-noAccount');
        }
           
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
        $this->_checkValidViewer($instructors);
         
        $instructorNames = array();
        $instructorEmails = array();
            
        foreach ($instructors as $i) {
            $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
            $instructorEmails[] = $i['emailAddress'];
        }     
        
        $attendee = new Event_Attendee();
        $attendee->cancelReservation($get->accountId, $thisEvent->eventId);

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
            'studentAcountId'           => $get->accountId,
            'instructorNames'           => implode(', ', $instructorNames),
            'instructorEmails'          => implode(', ', $instructorEmails),
            'studentEmail'              => $up->emailAddress,
            'studentFirstName'          => $up->firstName,
            'studentLastName'           => $up->lastName,
            'studentUsername'           => $up->username,
            'studentAccountId'          => $up->accountId
        );

        $trigger = new Ot_Trigger();
        $trigger->setVariables($data);
        $trigger->dispatch('Instructor_Cancels_Users_Reservation');  
        
        $waiting = $attendee->getAttendeesForEvent($thisEvent->eventId, 'waitlist');
        if (count($waiting) != 0) {             
            $up = $otAccount->find($waiting[0]['accountId']);
                
            if (!is_null($up)) {
                $attendee->makeReservation($up->accountId, $thisEvent->eventId);
                    
                $data['studentEmail']     = $up->emailAddress;
                $data['studentFirstName'] = $up->firstName;
                $data['studentLastName']  = $up->lastName;
                $data['username']         = $up->username;
                $data['studentAccountId'] = $up->accountId;

                $trigger = new Ot_Trigger();
                $trigger->setVariables($data);
                $trigger->dispatch('User_Automatically_Moved_From_Waitlist_To_Attending');
            }               
        }           
        
        $this->_helper->flashMessenger->addMessage('msg-info-userRemoved');
        $this->_redirect('/workshop/instructor/?eventId=' . $thisEvent->eventId);
    }
    
    /**
     * Allows a user to set the attendance status of a user
     *
     */
    public function takeRollAction()
    {        
        if ($this->_request->isXmlHttpRequest()) {
           $this->_helper->layout->disableLayout();    
        } else {
            $this->_helper->pageTitle('workshop-instructor-takeRoll:title');
        } 
        
        $get = Zend_Registry::get('getFilter');
        
        $messages = array();
        
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $eventId = $get->eventId;
        
        $event = new Event();
        
        $status = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity()->accountId, $eventId);
        
        if ($status != 'instructor' && !$this->_helper->hasAccess('view-all-instructor-pages')) {
            throw new Ot_Exception_Access('msg-error-notInstructor');   
        }
        
        $attendee = new Event_Attendee();
        
        $where = $attendee->getAdapter()->quoteInto('eventId = ?', $eventId);
        
        $attendees = $attendee->fetchAll($where);
        
        $hasAttendedList = array();
        $attendeeList = array();
        foreach ($attendees as $a) {
            $attendeeList[] = $a->accountId;
            if ($a->attended) {
                $hasAttendedList[] = $a->accountId;
            }
        }
        
        if (count($attendeeList) == 0) {
            throw new Ot_Exception_Data('msg-error-noAttendees');
        }
        
        $form = $event->rollForm(array('eventId' => $eventId, 'attendees' => $hasAttendedList));
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
    
                $attendees = $form->getValue('attendees');
                
                if (!is_array($attendees)) {
                    $attendees = array($attendees);
                }
                
                // make checked people as attended
                $where = $attendee->getAdapter()->quoteInto("accountId IN (?)", $attendees)
                       . ' AND '
                       . $attendee->getAdapter()->quoteInto('eventId = ?', $eventId);
                
                $data = array('attended' => 1);
                $attendee->update($data, $where);
                
                // make everyone else as not attended
                $where = $attendee->getAdapter()->quoteInto("accountId NOT IN (?)", $attendees)
                       . ' AND '
                       . $attendee->getAdapter()->quoteInto('eventId = ?', $eventId);
                
                $data = array('attended' => 0);
                $attendee->update($data, $where);                
                
                $this->_helper->flashMessenger->addMessage('msg-info-attendanceRecorded');
                $this->_redirect('/workshop/instructor/?eventId=' . $eventId);
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->form = $form;
    }

    /**
     * Allows a user to contact all the people in their event.
     *
     */
    public function contactAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
           $this->_helper->layout->disableLayout();    
        } else {
            $this->_helper->pageTitle('workshop-instructor-contact:title');
        }        
        
        $get = Zend_Registry::get('getFilter');

        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $messages = array();
        
        $event = new Event();
        
        $eventId = $get->eventId;
        
        $thisEvent = $event->find($eventId);
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        $otAccount = new Ot_Account();
        $thisAccount = $otAccount->find(Zend_Auth::getInstance()->getIdentity()->accountId);
        
        $status = $event->getStatusOfUserForEvent($thisAccount->accountId, $eventId);
        
        if ($status != 'instructor' && !$this->_helper->hasAccess('view-all-instructor-pages')) {
            throw new Ot_Exception_Access('msg-error-notInstructor');
        }
        
        $form = $event->contactForm(array('eventId' => $eventId));
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                                
                $recipients = array();
                $attendees = new Event_Attendee();
                
                $recipients = $attendees->getAttendeesForEvent($thisEvent->eventId, $form->getValue('recipients'));
                                
                if ($form->getValue('emailInstructors')) {
                    $instructor = new Event_Instructor();
                    $instructorList = $instructor->getInstructorsForEvent($thisEvent->eventId);
                    $recipients = array_merge($recipients, $instructorList);
                }
                
                $this->_checkValidViewer($instructorList);
    
                $mail = new Zend_Mail();
                $mail->setFrom($thisAccount->emailAddress, $thisAccount->firstName . ' ' . $thisAccount->lastName);
                $mail->setSubject($form->getValue('subject'));
                $mail->setBodyText($form->getValue('message'));
                $mail->addTo($thisAccount->emailAddress);
                
                foreach ($recipients as $r) {
                    $mail->addBcc($r['emailAddress']);
                }
                
                $eq = new Ot_Email_Queue();
                
                $data = array(
                    'attributeName'  => 'eventId',
                    'attributeId'    => $thisEvent->eventId,
                    'zendMailObject' => $mail,
                );
                
                $eq->queueEmail($data);
                
                //$mail->send();
                $this->_helper->flashMessenger->addMessage('msg-info-emailQueued');
                $this->_redirect('/workshop/instructor/?eventId=' . $thisEvent->eventId);
            } else {
                $messages[] = "msg-error-formSubmitProblem";                
            }
        }
        
        $this->view->messages = $messages;
        $this->view->thisAccount = $thisAccount;
        $this->view->form = $form;
    }
    
    
    /**
     * Displays the results of an evaluation as long as the user requesting the
     * page is an instructor of the event.
     *
     */
    public function evaluationResultsAction()
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
        $this->view->event = $thisEvent->toArray();
        
        $workshop = new Workshop();
        $thisWorkshop = $workshop->find($thisEvent->workshopId);        
        if (is_null($thisWorkshop)) {
            throw new Ot_Exception_Data('msg-error-noWorkshop');
        }
        $this->view->workshop = $thisWorkshop->toArray();
            
        $location = new Location();
        $thisLocation = $location->find($thisEvent->locationId);
            
        if (is_null($thisLocation)) {
            throw new Ot_Exception_Data('msg-error-noLocation');
        }   
        $this->view->location = $thisLocation->toArray();      
        
        $instructor = new Event_Instructor();
        $instructors = $instructor->getInstructorsForEvent($thisEvent->eventId);
        
        $instructorList = array();
        foreach ($instructors as $i) {
            $instructorList[] = $i['firstName'] . ' ' . $i['lastName'];
        }
        $this->view->instructors = $instructorList;
        
        $this->_checkValidViewer($instructors);        
        
        if($thisEvent['evaluationType'] == 'custom') {
            // get the evaluationId from the eventId
            $evaluation = new Evaluation();
            $where = $evaluation->getAdapter()->quoteInto('eventId = ?', $thisEvent->eventId);
            $evaluations = $evaluation->fetchAll($where);
            if ($evaluations->count() == 0) {
                $this->view->noEvaluationsYet = true;
            }
            
            $this->view->totalEvaluations = $evaluations->count();
            
            $ca = new Ot_Custom();
                    
            $questions = $ca->getAttributesForObject('evaluations');
            
            foreach ($questions as &$q) {
                $q['options'] = $ca->convertOptionsToArray($q['options']);
                
                $answers = array();
                foreach ($evaluations as $e) {
                    $tmpAnswers = $ca->getData($q['objectId'], $e->evaluationId);
                    
                    $tmp = array();
                    foreach ($tmpAnswers as $ta) {
                        $tmp[$ta['attribute']['attributeId']] = $ta['value'];
                    }
                    
                    $answers[] = $tmp;
                }
               
                if ($q['type'] == 'ranking' || $q['type'] == 'select' || $q['type'] == 'radio') {
                    foreach ($q['options'] as $value) {
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
        } elseif ($thisEvent['evaluationType'] == 'google') {
            $evaluationKeys = new Evaluation_Key();
            $keys = $evaluationKeys->find($get->eventId);
            
            if (is_null($keys)) {
                throw new Ot_Exception_Data('msg-error-noFormKey');
            }
            
            $this->view->keys = $keys->toArray();
        }
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.gchart.min.js');   
    }
}