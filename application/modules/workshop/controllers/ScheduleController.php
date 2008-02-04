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
     * Action when going to the main login page
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
                                "cnet/common/js.widgets/stickyWinFx.js",
                                "cnet/common/js.widgets/stickyWinFx.Drag.js",
                                "cnet/common/layout.widgets/MooScroller.js"
                            );
        
        $zd = new Zend_Date();
        
        $this->view->workshopLength = mktime(1, 0, 0, 1, 1, 1970);
        $this->view->startTime = mktime(0, 0, 0, 1, 1, 1970);
	    $this->view->endTime = mktime(23, 30, 0, 1, 1, 1970);
        $this->view->baseTime = mktime(0, 0, 0, 1, 1, 1970);
        $this->view->year = $zd->get(Zend_Date::YEAR);
        $this->view->week = $zd->get(Zend_Date::WEEK);  
        
        $this->view->title = "Schedule Workshops";
        
        $workshop = new Workshop();
        $workshops = $workshop->fetchAll(null, 'title');
        
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
    
    public function noLocationsFoundAction() {
        
    }
    
    public function searchAction()
    {
        $this->_helper->viewRenderer->setNeverRender();

        $this->view->acl = array(
           'delete'     => $this->_acl->isAllowed($this->_role, $this->_resource, 'deleteEvent'),
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
    
    public function createEventAction()
    {
        $this->_helper->viewRenderer->setNeverRender();

        $post   = Zend_Registry::get('post');
        $filter = Zend_Registry::get('inputFilter');

        $workshopId   = $filter->filter($post['workshopId']);
        $locationId   = $filter->filter($post['locationId']);
        $startTime    = $filter->filter($post['startTime']);
        $endTime      = $filter->filter($post['endTime']);
        $date         = $filter->filter($post['date']);
        $minSize      = $filter->filter($post['workshopMinSize']);
        $maxSize      = $filter->filter($post['workshopMaxSize']);
        $waitListSize = $filter->filter($post['workshopWaitListSize']);
        $instructors  = $filter->filter($post['instructors']);
        
        if ($instructors == "none") {
            $instructors = "";
        }

        $instructorList = explode(":", $instructors);
        
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
        
        $e = new Event();

        $eventId = $e->insert($data);
        
        $instructor = new Instructor();
        
        foreach ($instructorList as $i) {
            $instructor->insert(array("userId"=>$i, "eventId"=>$eventId));            
        }
        
        echo $eventId;
    }
    
    
    public function deleteEventAction()
    {
        $this->_helper->viewRenderer->setNeverRender();

        $post   = Zend_Registry::get('post');
        $filter = Zend_Registry::get('inputFilter');

        $eventId = $filter->filter($post['eventId']);
                
        $e = new Event();

        $where = $e->getAdapter()->quoteInto('eventId = ?', $eventId);
        $data = array('status'=>'canceled');
        echo $e->update($data, $where);
    }
}
