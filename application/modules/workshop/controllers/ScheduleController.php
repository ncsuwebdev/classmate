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
 * @package    Workshop_ScheduleController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with scheduling workshops
 *
 * @package    Workshop_ScheduleController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_ScheduleController extends Zend_Controller_Action 
{   
    /**
     * The main scheduler page.  This allows a user to view and edit the schedule.  Users
     * will almost certainly need access to this entire controller to make the
     * scheduler work properly and look right.
     *
     */
    public function indexAction()
    {       
        $this->view->acl = array(
           'addEvent' => $this->_helper->hasAccess('add-event')
        );
        
        $get = Zend_Registry::get('getFilter');
        
        if (isset($get->workshopId)) {
            $workshopId = $get->workshopId;
            
            $this->view->workshopId = $workshopId;
            $this->view->startInAddMode = 1;
        }
        
        if (isset($get->startYear)) {
            $this->view->startYear = $get->startYear;
        } else {
            $this->view->startYear = date('Y');
        }
        
        if (isset($get->startMonth)) {
            $this->view->startMonth = $get->startMonth;
        } else {
            $this->view->startMonth = date('m');
        }
        
        $eventId = null;
        if (isset($get->eventId)) {
            $eventId = $get->eventId;
            
            $this->view->eventId = $eventId;
            $this->view->startInEditMode = 1;
            
            $e = new App_Model_DbTable_Event();
            $thisEvent = $e->find($eventId)->toArray();
            
            $this->view->locationId = $thisEvent['locationId'];
        }
        
        $zd = new Zend_Date();
        
        $this->view->workshopLength = mktime(1, 0, 0, 1, 1, 1970);
        $this->view->startTime      = mktime(0, 0, 0, 1, 1, 1970);
        $this->view->endTime        = mktime(23, 30, 0, 1, 1, 1970);
        $this->view->baseTime       = mktime(0, 0, 0, 1, 1, 1970);
        
        $this->view->today = $zd->get(Zend_Date::MONTH) . "/" .
                             $zd->get(Zend_Date::DAY) . "/" . 
                             $zd->get(Zend_Date::YEAR);

        $this->view->thisYear = $zd->get(Zend_Date::YEAR);
        $this->view->thisWeek = $zd->get(Zend_Date::WEEK);
                             
        if (!is_null($eventId)) {
            $tmpDate = explode('-', $thisEvent['date']);
            $zd->setYear($tmpDate[0]);
            $zd->setMonth($tmpDate[1]);
            $zd->setDay($tmpDate[2]);
        }
        
        $this->view->year = $zd->get(Zend_Date::YEAR);
        $this->view->week = $zd->get(Zend_Date::WEEK);                             
        
        $this->_helper->pageTitle('workshop-schedule-index:title');
        
        $workshop = new App_Model_DbTable_Workshop();
        $where = $workshop->getAdapter()->quoteInto('status = ?', 'enabled');
        $workshops = $workshop->fetchAll($where, 'title');
        
        $workshopList = array();
        $workshopList[0] = "";
        foreach ($workshops as $w) {
            $workshopList[$w->workshopId] = $w->title;
        }
        
        $this->view->workshops = $workshopList;
        
        $location = new App_Model_DbTable_Location();
        $where = $location->getAdapter()->quoteInto('status = ?', 'enabled');
        $locations = $location->fetchAll($where, 'name');
        
        if (count($locations) == 0) {
            $this->_helper->redirector->gotoUrl('/workshop/schedule/noLocationsFound');
        }
        
        foreach ($locations as $l) {
            $locationList[$l->locationId] = $l->name;
        }
        
        $this->view->locationList = $locationList;
        
        //get all the users available for the instructor list
        $profile = new Ot_Account();
        $profiles = $profile->fetchAll(null, array('lastName', 'firstName'))->toArray();
        
        $instructors = array();
        
        foreach ($profiles as $p) {
            $instructors[$p['username']] = $p['lastName'] . ", " . $p['firstName'];            
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->instructors = $instructors;
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/scripts/jMonthCalendar-1.1.0.js');
        //$this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jMonthCalendar-1.2.2.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/scripts/jquery.bt.min.js');
               
    }
    
    /**
     * Gets the events for a given month.  Used in the scheduler as an AJAX call
     */
    public function getEventsAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        $this->_helper->layout->disableLayout();
        
        $get = Zend_Registry::get('getFilter');
        
        if (isset($get->date)) {
            if (!empty($get->date)) {
                $date = new Zend_Date($get->date);
            } else {
                $date = new Zend_Date();
            }
        } else {
            $date = new Zend_Date();
        }
        
        $locationId = null;
        if (isset($get->locationId) && !empty($get->locationId)) {
            $locationId = $get->locationId;   
        }
        
        $startDate = clone $date;
        $endDate = clone $date;
        
        $startDate->setHour(0)->setMinute(0)->setSecond(0);
        $startDate->setDay(1);
        $startDate->subDay($startDate->get(Zend_Date::WEEKDAY_DIGIT));
        
        $endDate->setHour(23)->setMinute(59)->setSecond(59);
        $endDate->setDay($endDate->get(Zend_Date::MONTH_DAYS));
        $endDate->addDay(6 - $endDate->get(Zend_Date::WEEKDAY_DIGIT));
        
        $event = new App_Model_DbTable_Event();
               
        $events = $event->getEvents(null, null, $locationId, $startDate->getTimestamp(), $endDate->getTimestamp(), array('open', 'closed'), null)->toArray();
        
        $workshop = new App_Model_DbTable_Workshop();
        
        foreach ($events as &$e) {
            $e['startTime'] = strftime('%l:%M %p', strtotime($e['startTime']));
            $e['endTime'] = strftime('%l:%M %p', strtotime($e['endTime']));
            
            $e['workshop'] = $workshop->find($e['workshopId'])->toArray();
        }
        
        echo Zend_Json::encode($events);
    }
    
    /**
     * Called by the Javascript frontend to get the details about an event
     *
     */
    public function eventDetailsAction()
    {           
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $event = new App_Model_DbTable_Event();
        
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $userEventStatus = false;
        } else {
            $userEventStatus = $event->getStatusOfUserForEvent(Zend_Auth::getInstance()->getIdentity()->accountId, $get->eventId); 
        }

        
            $i = new App_Model_DbTable_EventInstructor();
        $where = $i->getAdapter()->quoteInto('eventId = ?', $get->eventId);
        $results = $i->fetchAll($where);
        
        $currentInstructors = array();
        foreach ($results as $r) {
            $currentInstructors[] = $r->accountId;
        }
        
        $this->view->acl = array(
                            'editEvent'              => $this->_helper->hasAccess('edit-event'),
                            'cancelEvent'            => $this->_helper->hasAccess('cancel-event'),
                            'signup'                 => $this->_helper->hasAccess('signup', 'workshop_signup'),
                            'viewAllInstructorPages' => $this->_helper->hasAccess('view-all-instructor-pages', 'workshop_instructor'),
                            'userEventStatus'        => $userEventStatus 
                           );

        $this->view->reservationCancelable = $event->isReservationCancelable($get->eventId);
        
        // shortened display when an ajax call version)
        $short = false;
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $short = true;
        }
        
        $this->view->shortDisplay = $short;
        
        $workshop = new App_Model_DbTable_Workshop();
        $location = new App_Model_DbTable_Location();
        
        $thisEvent = $event->find($get->eventId);
        
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        $i = new App_Model_DbTable_EventInstructor();
        $currentInstructors = $i->getInstructorsForEvent($get->eventId);
        $instructor = array();
        foreach ($currentInstructors as $r) {
            $instructor[] = $r['firstName'] . ' ' . $r['lastName'];
        }
        
        $this->view->instructors = $instructor;

        $thisEvent = $thisEvent->toArray();
            
        $thisEvent['startTime'] = strftime('%l:%M %p', strtotime($thisEvent['startTime']));
        $thisEvent['endTime'] = strftime('%l:%M %p', strtotime($thisEvent['endTime']));
        
        $this->view->location = $location->find($thisEvent['locationId'])->toArray();            
        $this->view->workshop = $workshop->find($thisEvent['workshopId'])->toArray();
        
        $this->view->event = $thisEvent;
    }
    
    /**
     * Allows a user to edit an events details
     *
     */
    public function editEventAction()
    {
        $messages = array();
        
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $event = new App_Model_DbTable_Event();
        
        $thisEvent = $event->find($get->eventId);
        
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        $i = new App_Model_DbTable_EventInstructor();
        $where = $i->getAdapter()->quoteInto('eventId = ?', $get->eventId);
        $results = $i->fetchAll($where);
        
        $currentInstructors = array();
        foreach ($results as $r) {
            $currentInstructors[] = $r->accountId;
        }
                
        if (!$this->_helper->hasAccess('view-all-instructor-pages')
            && !in_array(Zend_Auth::getInstance()->getIdentity()->accountId, $currentInstructors)) {
                throw new Ot_Exception_Access('msg-error-noWorkshopAccess');
        }
        
        $thisEvent = $thisEvent->toArray();
        
        if($thisEvent['evaluationType'] == 'google') {
            $evaluationKey = new App_Model_DbTable_Evalutaion_Key();
            $keys = $evaluationKey->find($get->eventId);
            
            if(is_null($keys)) {
                throw new Ot_Exception_Data('Missing Form Keys');
            }
            
            $thisEvent['formKey'] = $keys['formKey'];
            $thisEvent['answerKey'] = $keys['answerKey'];
        }
        
        $thisEvent['instructorIds'] = $currentInstructors;
        
        $originalMaxSize = $thisEvent['maxSize'];
        
        $form = $event->form($thisEvent);
                    
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {

                $eventId          = $form->getValue('eventId');
                $workshopId       = $form->getValue('workshop');
                $locationId       = $form->getValue('location');
                $startTime        = $form->getValue('startTime');
                $endTime          = $form->getValue('endTime');
                $date             = $form->getValue('date');
                $minSize          = $form->getValue('minSize');
                $maxSize          = $form->getValue('maxSize');
                $waitlistSize     = $form->getValue('waitlistSize');
                $instructors      = $form->getValue('instructors');
                $evaluationType    = $form->getValue('evaluationType');

                if($evaluationType == 'google') {
                    if (ctype_alnum($form->getValue('formKey'))) {
                        $formKey = $form->getValue('formKey');
                    } else {
                        $regex = '(?<=key\=)\w*';
                        $matches = array();
                        preg_match_all ('/'.$regex.'/is', $form->getValue('formKey'), $matches);
                        
                        if(isset($matches[0][0])) {
                            $formKey = $matches[0][0];
                        } else {
                            throw new Ot_Exception_Data('The Google Form Key is incorrect');
                        }
                    }
                    
                    if (ctype_alnum($form->getValue('answerKey'))) {
                        $answerKey = $form->getValue('answerKey');
                    } else {
                        $regex = '(?<=key\=)\w*';
                          preg_match_all ("/".$regex."/is", $form->getValue('answerKey'), $matches);
                          if(count($matches) > 0) {
                            $answerKey = $matches[0][0];
                          } else {
                              throw new Ot_Exception_Data('The Google Answer Key is incorrect');
                          }
                    }
                }
                
                $date = strtotime($date);
                $date = strftime('%Y', $date) . "-" . strftime('%m', $date) . "-" . strftime('%d', $date);               
                
                if (strtolower($startTime['meridian']) == "pm" && $startTime['hour'] < 12) {
                    $startTime['hour'] += 12;
                }
                
                if (strtolower($startTime['meridian']) == "am" && $startTime['hour'] == 12) {
                    $startTime['hour'] = 0;
                }
                
                if (strtolower($endTime['meridian']) == "pm" && $endTime['hour'] < 12) {
                    $endTime['hour'] += 12;
                }
                
                if (strtolower($endTime['meridian']) == "am" && $endTime['hour'] == 12) {
                    $endTime['hour'] = 0;
                }
                
                $timesOk = true;
                
                $st = new Zend_Date($date);
                $st->setHour($startTime['hour'])->setMinute($startTime['minute']);
                $et = new Zend_Date($date);
                $et->setHour($endTime['hour'])->setMinute($endTime['minute']);
                
                if ($st->isLater($et)) {
                    $timesOk = false;
                    $messages[] = "msg-error-eventStartsAfter";
                } else if ($st->equals($et)) {
                    $timesOk = false;
                    $messages[] = "msg-error-eventTimesEqual";
                }                
                
                $startTime = $startTime['hour'] . ":" . $startTime['minute'] . ":00";
                $endTime   = $endTime['hour'] . ":" . $endTime['minute'] . ":00";

                $where = $event->getAdapter()->quoteInto('date = ?', $date)
                       . " AND " . $event->getAdapter()->quoteInto('locationId = ?', $locationId)
                       . " AND " . $event->getAdapter()->quoteInto('eventId != ?', $eventId)
                       . " AND " . $event->getAdapter()->quoteInto('status = ?', 'open');
                
                $possibleConflicts = $event->fetchAll($where);
                
                $conflictFound = false;
                
                if ($possibleConflicts->count() > 0) {
                    
                    $startTs = strtotime($startTime);
                    $endTs   = strtotime($endTime);
                    
                    foreach($possibleConflicts as $pc) {
                        
                        $pcStart = strtotime($pc->startTime);
                        $pcEnd   = strtotime($pc->endTime);
                        
                        if ($startTs == $pcStart) {
                            $conflictFound = true;
                        } else if (($startTs < $pcStart) && ($endTs > $pcStart)) {
                            $conflictFound = true;
                        } else if (($startTs >= $pcStart) && ($endTs <= $pcEnd)) { 
                            $conflictFound = true;
                        } else if (($startTs < $pcEnd) && ($endTs >= $pcEnd)) {
                            $conflictFound = true;
                        } else if (($startTs < $pcStart) && ($endTime > $pcEnd)) {
                            $conflictFound = true;
                        }                       
                        
                        if ($conflictFound) {
                            $messages[] = "msg-error-eventAlreadyScheduled";
                            break;
                        }
                    }
                }
                
                $evaluationCheck = true;
                
                /*
                 * TODO: Make this work better (see the regex section above)
                 */
                if ($evaluationType == 'google') {
                    $evaluationCheck = isset($formKey) && isset($answerKey);
                } else {
                    $evaluationCheck = $evaluationType == 'default';
                }
                
                if (!$evaluationCheck) {
                    $messages[] = 'msg-error-eventFormKeyMissing';
                }

                if (!$conflictFound && $timesOk && $evaluationCheck) {
                    
                    $data = array('eventId'          => $eventId,
                                  'locationId'       => $locationId,
                                  'workshopId'       => $workshopId,
                                  'startTime'        => $startTime,
                                  'endTime'          => $endTime,
                                  'date'             => $date,
                                  'minSize'          => $minSize,
                                  'maxSize'          => $maxSize,
                                  'waitlistSize'     => $waitlistSize,
                                  'evaluationType'    => $evaluationType,
                                  'formKey'            => $formKey,
                                  'answerKey'        => $answerKey
                                 );
                    
                    $event->update($data, null);
                    
                    $instructor = new App_Model_DbTable_EventInstructor();
                    
                    $where = $instructor->getAdapter()->quoteInto('eventId = ?', $eventId);
                    $instructor->delete($where);
                    
                    foreach ($instructors as $i) {
                        $instructor->insert(array('accountId' => $i, 'eventId' => $eventId));            
                    }
                    
                    // move people on the waitlist (if any) to the newly added spots
                    if ($maxSize > $originalMaxSize) {
                        $attendee = new App_Model_DbTable_EventAttendee();
                        $attendee->fillEvent($eventId);
                    }
                    
                    $this->_helper->flashMessenger->addMessage('msg-info-eventSaved');
                    if (isset($get->itools)) {
                        $this->_helper->redirector->gotoUrl('/workshop/instructor?eventId=' . $eventId);
                    } else {
                        $date = explode('-', $date);
                        $this->_helper->redirector->gotoUrl('/workshop/schedule?startYear=' . $date[0] . '&startMonth=' . (int)$date[1]);
                    }
                }
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/workshop/schedule/help.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.autocomplete.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.tooltip.min.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.autocomplete.css');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/workshop/schedule/help.css');
        $this->view->form = $form;
        $this->_helper->pageTitle('workshop-schedule-editEvent:title');    
    }
    
    /**
     * Allows a user to create an event.
     *
     */
    public function addEventAction()
    {
        $messages = array();
        
        $get = Zend_Registry::get('getFilter');
        
        $event = new App_Model_DbTable_Event();
        
        $values = array();
        if (isset($get->date)) {
            $values['date'] = $get->date;    
        }
        
        $form = $event->form($values);
            
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $workshopId       = $form->getValue('workshop');
                $locationId       = $form->getValue('location');
                $startTime        = $form->getValue('startTime');
                $endTime          = $form->getValue('endTime');
                $date             = $form->getValue('date');
                $minSize          = $form->getValue('minSize');
                $maxSize          = $form->getValue('maxSize');
                $waitlistSize     = $form->getValue('waitlistSize');
                $instructors      = $form->getValue('instructors');
                $password          = $form->getValue('password');
                $evaluationType    = $form->getValue('evaluationType');
                $formKey        = $form->getValue('formKey');
                $answerKey        = $form->getValue('answerKey'); 
                
                if (isset($formKey) && $formKey != '') {
                    $regex = '(?<=key\=)\w*';
                    $matches = array();
                    preg_match_all ("/".$regex."/is", $form->getValue('formKey'), $matches);
                    $formKey = $matches[0][0];
                }
                
                if (isset($answerKey) && $answerKey != '') {
                    $regex = '(?<=key\=)\w*';
                    $matches = array();
                      preg_match_all ("/".$regex."/is", $form->getValue('answerKey'), $matches);
                    $answerKey = $matches[0][0];
                }
                
                $date = strtotime($date);
                $date = strftime('%Y', $date) . "-" . strftime('%m', $date) . "-" . strftime('%d', $date);
                
                if (strtolower($startTime['meridian']) == "pm" && $startTime['hour'] < 12) {
                    $startTime['hour'] += 12;
                }
                
                if (strtolower($startTime['meridian']) == "am" && $startTime['hour'] == 12) {
                    $startTime['hour'] = 0;
                }
                
                if (strtolower($endTime['meridian']) == "pm" && $endTime['hour'] < 12) {
                    $endTime['hour'] += 12;
                }
                
                if (strtolower($endTime['meridian']) == "am" && $endTime['hour'] == 12) {
                    $endTime['hour'] = 0;
                }
                
                $timesOk = true;
                
                $st = new Zend_Date($date);
                $st->setHour($startTime['hour'])->setMinute($startTime['minute']);
                $et = new Zend_Date($date);
                $et->setHour($endTime['hour'])->setMinute($endTime['minute']);
                
                if ($st->isLater($et)) {
                    $timesOk = false;
                    $messages[] = "msg-error-eventStartsAfter";
                } else if ($st->equals($et)) {
                    $timesOk = false;
                    $messages[] = "msg-error-eventTimesEqual";
                }
                
                $startTime = $startTime['hour'] . ":" . $startTime['minute'] . ":00";
                $endTime   = $endTime['hour'] . ":" . $endTime['minute'] . ":00";
                                
                $where = $event->getAdapter()->quoteInto('date = ?', $date)
                       . " AND " . $event->getAdapter()->quoteInto('locationId = ?', $locationId)
                       . " AND " . $event->getAdapter()->quoteInto('status = ?', 'open');
                
                $possibleConflicts = $event->fetchAll($where);
                
                $conflictFound = false;
                
                if ($possibleConflicts->count() > 0) {
                    
                    $startTs = strtotime($startTime);
                    $endTs   = strtoTime($endTime);
                    
                    foreach($possibleConflicts as $pc) {
                        
                        $pcStart = strtotime($pc->startTime);
                        $pcEnd   = strtotime($pc->endTime);
                        
                        if ($startTs == $pcStart) {
                            $conflictFound = true;
                        } else if (($startTs < $pcStart) && ($endTs > $pcStart)) {
                            $conflictFound = true;
                        } else if (($startTs >= $pcStart) && ($endTs <= $pcEnd)) { 
                            $conflictFound = true;
                        } else if (($startTs < $pcEnd) && ($endTs >= $pcEnd)) {
                            $conflictFound = true;
                        } else if (($startTs < $pcStart) && ($endTime > $pcEnd)) {
                            $conflictFound = true;
                        }                       
                        
                        if ($conflictFound) {
                            $messages[] = "msg-error-eventAlreadyScheduled";
                            break;
                        }
                    }
                }
                
                $evaluationCheck = true;
                
                if ($evaluationType == 'google') {
                    $evaluationCheck = isset($formKey) && isset($answerKey);
                } else {
                    $evaluationCheck = $evaluationType == 'default';
                }
                
                if (!$evaluationCheck) {
                    $messages[] = 'msg-error-eventFormKeyMissing';
                }

                if (!$conflictFound && $timesOk && $evaluationCheck) {
                    
                    $data = array('locationId'       => $locationId,
                                  'workshopId'       => $workshopId,
                                  'startTime'        => $startTime,
                                  'endTime'          => $endTime,
                                  'date'             => $date,
                                  'minSize'          => $minSize,
                                  'maxSize'          => $maxSize,
                                  'waitlistSize'     => $waitlistSize,
                                  'password'         => $password,
                                  'evaluationType'    => $evaluationType,
                                  'formKey'            => $formKey,
                                  'answerKey'        => $answerKey
                                 );
                    
                    $eventId = $event->insert($data);
                    
                    $instructor = new App_Model_DbTable_EventInstructor();
                    
                    foreach ($instructors as $i) {
                        $instructor->insert(array('accountId' => $i, 'eventId' => $eventId));
                    }
                    
                    $this->_helper->flashMessenger->addMessage('msg-info-eventAdded');
                    $date = explode('-', $date);
                    $this->_helper->redirector->gotoUrl('/workshop/schedule?startYear=' . $date[0] . '&startMonth=' . (int)$date[1]);
                }
            } else {
                $messages[] = "msg-error-formSubmitProblem";
            }
        }
        
        $this->view->messages = $messages;
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/jquery.autocomplete.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/workshop/schedule/help.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/jquery.autocomplete.css');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/workshop/schedule/help.css');
        $this->view->form = $form;
        $this->_helper->pageTitle('workshop-schedule-addEvent:title');
    }
    
    /**
     * Allows a user to cancel an event 
     * 
     */
    public function cancelEventAction()
    {        
        $get = Zend_Registry::get('getFilter');
        
        if (!isset($get->eventId)) {
            throw new Ot_Exception_Input('msg-error-eventIdNotSet');
        }
        
        $workshop = new App_Model_DbTable_Workshop();
        $event = new App_Model_DbTable_Event();
        $location = new App_Model_DbTable_Location();
        
        $thisEvent = $event->find($get->eventId);
        
        if (is_null($thisEvent)) {
            throw new Ot_Exception_Data('msg-error-noEvent');
        }
        
        $i = new App_Model_DbTable_EventInstructor();
        $where = $i->getAdapter()->quoteInto('eventId = ?', $get->eventId);
        $results = $i->fetchAll($where);
        
        $currentInstructors = array();
        foreach ($results as $r) {
            $currentInstructors[] = $r->accountId;
        }
                
        if (!$this->_helper->hasAccess('view-all-instructor-pages')
            && !in_array(Zend_Auth::getInstance()->getIdentity()->accountId, $currentInstructors)) {
                throw new Ot_Exception_Access('msg-error-noWorkshopAccess');
        }        
        
        $thisEvent = $thisEvent->toArray();
            
        $thisEvent['startTime'] = strftime('%l:%M %p', strtotime($thisEvent['startTime']));
        $thisEvent['endTime'] = strftime('%l:%M %p', strtotime($thisEvent['endTime']));
        
        $thisEvent['location'] = $location->find($thisEvent['locationId'])->toArray();            
        $thisEvent['workshop'] = $workshop->find($thisEvent['workshopId'])->toArray();
        
        $this->view->event = $thisEvent;

        $form = Ot_Form_Template::delete('eventDelete', 'workshop-schedule-cancelEvent:cancel');
        
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $dba = $event->getAdapter();
        
            $dba->beginTransaction();
            
            $where = $dba->quoteInto('eventId = ?', $get->eventId);
            $data = array('status'=>'canceled');
            
            try {
                $result = $event->update($data, $where);
            } catch (Exception $e) {
                $dba->rollback();
                throw $e;
            }
            
            $attendee = new App_Model_DbTable_EventAttendee();
            try {
                $attendee->update($data, $where);
            } catch (Exception $e) {
                $dba->rollback();
                throw $e;
            }
            
            $dba->commit();

            $this->_helper->flashMessenger->addMessage('msg-info-eventCanceled');
           
            $date = explode('-', $thisEvent['date']);
            $this->_helper->redirector->gotoUrl('/workshop/schedule?startYear=' . $date[0] . '&startMonth=' . (int)$date[1]);
        }
        
        $this->_helper->pageTitle('workshop-schedule-cancelEvent:title');
        $this->view->form = $form;
    }
}