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
 * @subpackage Workshop_ScheduleController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Handles all interaction with scheduling workshops
 *
 * @package    Classmate
 * @subpackage Workshop_ScheduleController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_ScheduleController extends Internal_Controller_Action 
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
           'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'createEvent'),
        );
        
        $filter = Zend_Registry::get('inputFilter');
        $get    = Zend_Registry::get('get');
        
        if (isset($get['workshopId'])) {
            $workshopId = $filter->filter($get['workshopId']);
            
            $this->view->workshopId = $workshopId;
            $this->view->startInAddMode = 1;
        }
        
        $eventId = null;
        if (isset($get['eventId'])) {
            $eventId = $filter->filter($get['eventId']);
            
            $this->view->eventId = $eventId;
            $this->view->startInEditMode = 1;
            
            $e = new Event();
            $thisEvent = $e->find($eventId)->toArray();
            
            $this->view->locationId = $thisEvent['locationId'];
        }
        
        $this->view->javascript = array(
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
                                "cnet/common/js.widgets/stickyWin.Ajax.js",
                                "cnet/mootools.extended/Native/date.js",
                                "cnet/mootools.extended/Native/date.extras.js",
                                "calendar.js"
                            );
        
        $zd = new Zend_Date();
        
        $this->view->workshopLength = mktime(1, 0, 0, 1, 1, 1970);
        $this->view->startTime = mktime(0, 0, 0, 1, 1, 1970);
	    $this->view->endTime = mktime(23, 30, 0, 1, 1, 1970);
        $this->view->baseTime = mktime(0, 0, 0, 1, 1, 1970);
        
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
        
        $this->view->title = "Schedule Workshops";
        
        $workshop = new Workshop();
        $where = $workshop->getAdapter()->quoteInto('status = ?', 'enabled');
        $workshops = $workshop->fetchAll($where, 'title');
        
        $workshopList = array();
        $workshopList[0] = "";
        foreach ($workshops as $w) {
            $workshopList[$w->workshopId] = $w->title;
        }
        
        $this->view->workshops = $workshopList;
        
        $location = new Location();
        $locations = $location->fetchAll(null, 'name');
        
        if (count($locations) == 0) {
            $this->_redirect('/workshop/schedule/noLocationsFound');
        }
        
        foreach ($locations as $l) {
            $locationList[$l->locationId] = $l->name;
        }
        
        $this->view->locations = $locationList;
        
        //get all the users available for the instructor list
        $profile = new Profile();
        $profiles = $profile->fetchAll(null, array('lastName', 'firstName'))->toArray();
        
        $instructors = array();
        
        foreach ($profiles as $p) {
            $instructors[$p['userId']] = $p['lastName'] . ", " . $p['firstName'];            
        }
        
        $this->view->instructors = $instructors;
               
    }
    
    /**
     * Allows a user to edit an events details
     *
     */
    public function editEventAction()
    {
    	$this->_helper->viewRenderer->setNeverRender();
    	
    	$event = new Event();
    	
        $filter = Zend_Registry::get('inputFilter');
    	
    	if (!$this->_request->isPost()) {    		
	        
    	    $get = Zend_Registry::get('get');
        
	        $eventId = $filter->filter($get['eventId']);
	        
	        $this->view->eventId = $eventId;
    		
	        $workshop = new Workshop();
	        $where = $workshop->getAdapter()->quoteInto('status = ?', 'enabled');
	        $workshops = $workshop->fetchAll($where, 'title');
	        
	        $workshopList = array();
	        $workshopList[0] = "";
	        foreach ($workshops as $w) {
	            $workshopList[$w->workshopId] = $w->title;
	        }
	        
	        $this->view->workshops = $workshopList;
	        
	        //get all the users available for the instructor list
	        $profile = new Profile();
	        $profiles = $profile->fetchAll(null, array('lastName', 'firstName'))->toArray();
	        
	        $instructors = array();
	        
	        foreach ($profiles as $p) {
	            $instructors[$p['userId']] = $p['lastName'] . ", " . $p['firstName'];            
	        }
	        
	        $this->view->instructors = $instructors;
	        
	        $i = new Instructor();
	        $where = $i->getAdapter()->quoteInto('eventId = ?', $eventId);
	        $results = $i->fetchAll($where);
	        
	        $currentInstructors = array();
	        foreach ($results as $r) {
	        	$currentInstructors[] = $r->userId;
	        }
	        
	        $this->view->currentInstructors = $currentInstructors;
	        
	        $where = $event->getAdapter()->quoteInto('eventId = ?', $eventId);
	        $e = $event->fetchAll($where)->current()->toArray();
	        
	        $this->view->event = $e;
	        
	        $location = new Location();
	        $where = $location->getAdapter()->quoteInto('status = ?', 'enabled');
            $locations = $location->fetchAll($where, 'name');
            
            $locationList = array();
            foreach($locations as $l) {
            	$locationList[$l->locationId] = $l->name;
            }
            
            $this->view->locations = $locationList;
	        
	        $this->_response->setBody($this->view->render('schedule/edit-event.tpl'));
	        
    	} else {
    		
    		$post = Zend_Registry::get('post');

    		$eventId            = $filter->filter($post['eventId']);
	        $workshopId         = $filter->filter($post['workshopId']);
	        $locationId         = $filter->filter($post['editLocationId']);
	        $startTime          = $post['eventStartTime'];
	        $endTime            = $post['eventEndTime'];
	        $date               = $post['eventDate'];
	        $minSize            = $filter->filter($post['workshopMinSize']);
	        $maxSize            = $filter->filter($post['workshopMaxSize']);
	        $waitListSize       = $filter->filter($post['workshopWaitListSize']);
	        $instructors        = $filter->filter($post['instructors']);
	        
	        $originalEvent = $event->find($eventId);
	        
	        $date = $date['Date_Year'] . "-" . $date['Date_Month'] . "-" . $date['Date_Day'];
	        
	        if (strtolower($startTime['Time_Meridian']) == "pm" && $startTime['Time_Hour'] < 12) {
	            
	            $startTime['Time_Hour'] += 12;
	        }
	        
	        if (strtolower($startTime['Time_Meridian']) == "am" && $startTime['Time_Hour'] == 12) {
	            $startTime['Time_Hour'] = 0;
	        }
	        
    	    if (strtolower($endTime['Time_Meridian']) == "pm" && $endTime['Time_Hour'] < 12) {
                
                $endTime['Time_Hour'] += 12;
            }
            
            if (strtolower($endTime['Time_Meridian']) == "am" && $endTime['Time_Hour'] == 12) {
                $endTime['Time_Hour'] = 0;
            }	        
	        
	        $startTime = $startTime['Time_Hour'] . ":" . $startTime['Time_Minute'] . ":00";
	        $endTime   = $endTime['Time_Hour'] . ":" . $endTime['Time_Minute'] . ":00";
	        	            
            $where = $event->getAdapter()->quoteInto('date = ?', $date);
            $where .= " AND " . $event->getAdapter()->quoteInto('locationId = ?', $locationId);
            $where .= " AND " . $event->getAdapter()->quoteInto('eventId != ?', $eventId);
            $where .= " AND " . $event->getAdapter()->quoteInto('status = ?', 'open');
            
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
                    
                        $ret = array("rc"=>'-1', 
                                     "msg"=>"An event is already scheduled during this time in the selected location.  The event was not changed.");
                        
                        echo Zend_Json::encode($ret);   
                        return false;
                    }
                }
            }
	            
    	    if ($instructors == "none" || $instructors == "undefined") {
                $instructors = "";
            }
	
        	$instructorList = array();
            if ($instructors != "") {
                $instructorList = explode(":", $instructors);
            }
	        
	        $data = array('eventId'      => $eventId,
	                      'locationId'   => $locationId,
	                      'workshopId'   => $workshopId,
	                      'startTime'    => $startTime,
	                      'endTime'      => $endTime,
	                      'date'         => $date,
	                      'minSize'      => $minSize,
	                      'maxSize'      => $maxSize,
	                      'waitlistSize' => $waitListSize
	                     );
	        
	        $event->update($data, null);
	        
	        $instructor = new Instructor();
	        
	        $where = $instructor->getAdapter()->quoteInto('eventId = ?', $eventId);
	        $instructor->delete($where);
	        
	        foreach ($instructorList as $i) {
	            $instructor->insert(array("userId"=>trim($i), "eventId"=>$eventId));            
	        }
	        
	        // move people on the waitlist (if any) to the newly added spots
	        if ($maxSize > $originalEvent->maxSize) {
	            $attendees = new Attendees();
	            $attendees->fillEvent($eventId);
	        }
	        
	        $ret = array("rc"=>$eventId,
	                     "msg" => "Saving event failed"
	                    );
	        
	        echo Zend_Json::encode($ret);
    	}
    }
    
    /**
     * This function is used to prevent the user from seeing the schedule if
     * no locations exist in the system.
     *
     */
    public function noLocationsFoundAction()
    {}
    
    /**
     * Gets the schedule data to display.  Users need access to this if they
     * want to view any of the data on the schedule.
     *
     */
    public function searchAction()
    {
        $this->_helper->viewRenderer->setNeverRender();

        $this->view->acl = array(
           'delete'          => $this->_acl->isAllowed($this->_role, $this->_resource, 'deleteEvent'),
           'instructorTools' => $this->_acl->isAllowed($this->_role, 'workshop_instructor', 'index')
        );
        
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $cal = new Calendar();
        $zd = new Zend_Date();

        $locationId = $filter->filter($get['locationId']);
        
        if (isset($get['year'])) {
            $year = $filter->filter($get['year']);            
        } else {
            $year = $zd->get(Zend_Date::YEAR);                       
        }
        
        if (isset($get['week'])) {
            $week = $filter->filter($get['week']);
        } else {
            $week = $zd->get(Zend_Date::WEEK);
        }
        
        $this->view->startTime = mktime(0, 0, 0, 1, 1, 1970);
        $this->view->displayStartTime = $this->view->startTime + 1800;
        $this->view->endTime   = mktime(23, 30, 0, 1, 1, 1970);
        
        $this->view->year = $year;
        $this->view->week = $week;
        
        $c = $cal->getWeek($week, $year, $locationId);

        $this->view->weekNum     = $c['weekNum'];
        $this->view->year        = $c['year'];
        
        $this->view->nextWeekNum = $c['nextWeekNum'];
        $this->view->nextYear    = $c['nextYear'];
        
        $this->view->prevWeekNum = $c['prevWeekNum'];
        $this->view->prevYear    = $c['prevYear'];
        
        unset($c['year']);
        unset($c['weekNum']);

        unset($c['nextWeekNum']);
        unset($c['nextYear']);
        
        unset($c['prevWeekNum']);
        unset($c['prevYear']);
        
        $this->view->calendar = $c;
        $this->view->today    = date('m/d/y');
        
        $this->_response->setBody($this->view->render('schedule/search.tpl'));
    }
    
    /**
     * Allows a user to create an event.
     *
     */
    public function createEventAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        
        $filter = Zend_Registry::get('inputFilter');

        if (!$this->_request->isPost()) {
        
            $get = Zend_Registry::get('get');
            
            $workshopId = 0;
            
            if (isset($get['workshopId'])) {
                $workshopId = $filter->filter($get['workshopId']);   
            }
            
            $date      = $filter->filter($get['date']);
            $startTime = $filter->filter($get['startTime']);
            $endTime   = $filter->filter($get['endTime']);
            
            $this->view->date      = $date;
            $this->view->startTime = $startTime;
            $this->view->endTime   = $endTime;
            
            $workshop = new Workshop();
            $where = $workshop->getAdapter()->quoteInto('status = ?', 'enabled');
            $workshops = $workshop->fetchAll($where, 'title');
            
            $workshopList = array();
            $workshopList[0] = "";
            foreach ($workshops as $w) {
                $workshopList[$w->workshopId] = $w->title;
            }
            
            $this->view->workshops  = $workshopList;
            $this->view->workshopId = $workshopId;
            
            //get all the users available for the instructor list
            $profile = new Profile();
            $profiles = $profile->fetchAll(null, array('lastName', 'firstName'))->toArray();
            
            $instructors = array();
            
            foreach ($profiles as $p) {
                $instructors[$p['userId']] = $p['lastName'] . ", " . $p['firstName'];            
            }
            
            $this->view->instructors = $instructors;
            
            $this->_response->setBody($this->view->render('schedule/eventpopup.tpl'));
            
        } else {
        
            $post   = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');
            
            $e = new Event();

            $workshopId   = $filter->filter($post['workshopId']);
            $locationId   = $filter->filter($post['locationId']);
            $startTime    = $filter->filter($post['startTime']);
            $endTime      = $filter->filter($post['endTime']);
            $date         = $filter->filter($post['date']);
            $minSize      = $filter->filter($post['workshopMinSize']);
            $maxSize      = $filter->filter($post['workshopMaxSize']);
            $waitListSize = $filter->filter($post['workshopWaitListSize']);
            $instructors  = $filter->filter($post['instructors']);
            
            if ($instructors == "none" || $instructors == "undefined") {
                $instructors = "";
            }
    
            $instructorList = array();
            if ($instructors != "") {
                $instructorList = explode(":", $instructors);
            }
            
            $date = explode("/", $date);
            $dateStr = $date[2] . "-" . $date[0] . "-" . $date[1];
            
            $data = array('workshopId'   => $workshopId,
                          'locationId'   => $locationId,
                          'startTime'    => $startTime,
                          'endTime'      => $endTime,
                          'date'         => $dateStr,
                          'maxSize'      => $maxSize,
                          'minSize'      => $minSize,
                          'waitlistSize' => $waitListSize
                         );
            
            $workshop = new Workshop();
            $thisWorkshop = $workshop->find($workshopId);
    
            $eventId = $e->insert($data);
            
            $instructor = new Instructor();
            
            foreach ($instructorList as $i) {
                $instructor->insert(array("userId"=>trim($i), "eventId"=>$eventId));            
            }
            
            $data = array(
                    'userId'        => Zend_Auth::getInstance()->getIdentity(),
                    'workshopTitle' => $thisWorkshop->title, 
                    'date'          => $dateStr,
                    'startTime'     => $startTime,
                    'endTime'       => $endTime
                );
        
            $trigger = new EmailTrigger();
            $trigger->setVariables($data);
            $trigger->dispatch('Event_Create');
            
            $ret = array("rc" => $eventId,
                         "msg" => "Creating new event failed" // this is only read if rc == 0
                        );
            echo Zend_Json::encode($ret);
        }
    }
    
    /**
     * Allows a user to delete an event from the schedule (it really cancels an
     * event).
     *
     */
    public function deleteEventAction()
    {
        $this->_helper->viewRenderer->setNeverRender();

        $post   = Zend_Registry::get('post');
        $filter = Zend_Registry::get('inputFilter');

        $eventId = $filter->filter($post['eventId']);
                
        $event = new Event();

        $dba = $event->getAdapter();
        
        $dba->beginTransaction();
        
        $where = $dba->quoteInto('eventId = ?', $eventId);
        $data = array('status'=>'canceled');
        
        try {
            $result = $event->update($data, $where);
        } catch (Exception $e) {
        	$dba->rollback();
        	throw $e;
        }
        
        $attendees = new Attendees();
        try {
        	$result = $attendees->update($data, $where);
        } catch (Exception $e) {
        	$dba->rollback();
        	throw $e;
        }
        
        $dba->commit();
        
        echo Zend_Json::encode(array("rc"=>$result));
    }
    
    /**
     * Allows all the user to get all the events that are happening.
     *
     */
    public function allEventsAction()
    {
        $event = new Event();
        
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        $uc     = Zend_Registry::get('userConfig');
        
        $stayOpen = new Zend_Date();
        $stayOpen->subHour($uc['numHoursEvaluationAvailability']['value']);
        
        $startDt = $stayOpen->getTimestamp();
        $endDt   = null;
        
        if (isset($get['time']) && $filter->filter($get['time']) == 'past') {
            $startDt = null;
            $endDt   = $stayOpen->getTimestamp();
        }
        
        $attendees = new Attendees();
        $attendeeEvents = $attendees->getEventsForAttendee(Zend_Auth::getInstance()->getIdentity());
        
        $userStatus = array();
        foreach ($attendeeEvents as $e) {
            $userStatus[$e['eventId']] = $e['status'];
        }
        
        $instructor = new Instructor();
        $instructorEvents = $instructor->getEventsForInstructor(Zend_Auth::getInstance()->getIdentity());
        foreach ($instructorEvents as $e) {
            $userStatus[$e['eventId']] = 'instructor';
        }
        
        $acl = array(
            'viewAllInstructorPages' => $this->_acl->isAllowed($this->_role, 'workshop_instructor', 'viewAllInstructorPages'),
        );      
        
        $events = $event->getEvents(null, null, $startDt, $endDt, 'open')->toArray();
        
        $locationCache = array();
        $location = new Location();
        
        $workshopCache = array();
        $workshop = new Workshop();
        $instructor = new Instructor();
        $profile = new Profile();
        
        $instructorDropDown = array();
        
        foreach ($events as &$e) {  
            $startDt = new Zend_Date(strtotime($e['date'] . ' ' . $e['startTime']));
            $endDt   = new Zend_Date(strtotime($e['date'] . ' ' . $e['endTime']));
            $endDt->addHour($uc['numHoursEvaluationAvailability']['value']);
            
            $e['evaluatable'] = ($startDt->getTimestamp() < time() && $endDt->getTimestamp() > time() && (isset($userStatus[$e['eventId']]) && $userStatus[$e['eventId']] == 'attending'));
            
            $e['signupable'] = ($startDt->getTimestamp() > time() && (!isset($userStatus[$e['eventId']])));
            
            $startDt->subHour($uc['numHoursEventCancel']['value']);
            
            $e['cancelable']  = ($startDt->getTimestamp() > time() && (isset($userStatus[$e['eventId']]) && $userStatus[$e['eventId']] != 'instructor'));
            
            $e['instructor'] = ($acl['viewAllInstructorPages'] || (isset($userStatus[$e['eventId']]) && $userStatus[$e['eventId']] == 'instructor'));

            $where = $instructor->getAdapter()->quoteInto('eventId = ?', $e['eventId']);
            $e['instructors'] = $instructor->fetchAll($where)->toArray();
            
            foreach ($e['instructors'] as &$i) {
                $i['profile'] = $profile->find($i['userId'])->toArray();
                if (!empty($i['profile'])) {
                    $instructorDropDown[$i['userId']] = $i['profile'];
                }
            }
                       
            if (isset($locationCache[$e['locationId']])) {
                $e['location'] = $locationCache[$e['locationId']];
            } else {
                $thisLocation = $location->find($e['locationId']);        
                if (!is_null($thisLocation)) {
                    $e['location'] = $thisLocation->toArray();      
                    $locationCache[$e['locationId']] = $e['location'];
                }
            }   
               
            if (isset($workshopCache[$e['workshopId']])) {
                $e['workshop'] = $workshopCache[$e['workshopId']];
            } else {
                $thisWorskhop = $workshop->find($e['workshopId']);        
                if (!is_null($thisWorskhop)) {
                    $e['workshop'] = $thisWorskhop->toArray();      
                    $workshopCache[$e['workshopId']] = $e['workshop'];
                }
            }
        }   

        sort($instructorDropDown);
        $this->view->instructorDropDown = $instructorDropDown;
        
        $this->view->events = $events;
        $this->view->acl    = $acl;
        
        $wc = new WorkshopCategory();
        $result = $wc->fetchAll(null, 'name')->toArray();
        
        $categories = array();
        foreach ($result as $c) {
            $categories[$c['workshopCategoryId']] = $c;
        }
        
        $this->view->categories = $categories;
        
        $this->view->title = 'All Events';
    }    
}
