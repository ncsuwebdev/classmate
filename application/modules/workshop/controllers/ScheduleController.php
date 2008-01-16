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
           'add'     => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
           'details' => $this->_acl->isAllowed($this->_role, $this->_resource, 'details'),
        );
        
        $zd = new Zend_Date();
        
        $this->view->workshopLength = mktime(1, 0, 0, 1, 1, 1970);
        $this->view->startTime = mktime(8, 0, 0, 1, 1, 1970);
	    $this->view->endTime = mktime(20, 0, 0, 1, 1, 1970);
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
        
        foreach ($locations as $l) {
            $locationList[$l->locationId] = $l->name;
        }
        
        $this->view->locations = $locationList;
    }
    
    
    public function searchAction()
    {
        $this->_helper->viewRenderer->setNeverRender();

        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $cal = new Calendar();
        $zd = new Zend_Date();

        $workshopId   = $filter->filter($get['workshopId']);
        $locationId   = $filter->filter($get['locationId']);
        $startTime    = $get['startTime'];
        $endTime      = $get['endTime'];
        
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
        

    	if ($startTime['Time_Meridian'] == "pm") {
    	    $startTime['Time_Hour'] += 12;
    	}
    
    	if ($startTime['Time_Hour'] == 24) {
    	    $startTime['Time_Hour'] = 0;
    	}
    
    	if ($endTime['Time_Meridian'] == "pm") {
    	    $endTime['Time_Hour'] += 12;
    	}
    
    	if ($endTime['Time_Hour'] == 24) {
    	    $endTime['Time_Hour'] = 0;
    	}

    	$this->view->startTime = mktime($startTime['Time_Hour'], $startTime['Time_Minute'], 0, 1, 1, 1970);
    	$this->view->endTime   = mktime($endTime['Time_Hour'], $endTime['Time_Minute'], 0, 1, 1, 1970);
        
        $this->view->year = $year;
        $this->view->week = $week;
        
        $c = $cal->getWeek($week, $year);

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
        
        $this->_response->setBody($this->view->render('schedule/search.tpl'));
    }
}
