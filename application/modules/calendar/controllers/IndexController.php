<?php

/**
 * ClassMate
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
 * @package    ClassMate (Calendar)
 * @subpackage IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: IndexController.php 197 2007-08-01 15:17:51Z gplocke@EOS.NCSU.EDU $
 */

/**
 * Main Index controller for the calendar
 */
class Calendar_IndexController extends Internal_Controller_Action 
{

    /**
     * Displays the current month's calendar
     *
     */
    public function indexAction()
    {        
        
        $this->view->title = "ClassMate Calendar";
        
        $cal = new Calendar();
        
        $c = $cal->getCalendar();

        $this->view->calendar = $c;        
    }
    
    /**
     * AJAX function that allows the rendering of the next calendar
     *
     */
    public function getCalAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $year  = $filter->filter($get['year']);
        $month = $filter->filter($get['month']);
        
        $cal = new Calendar();
        $c = $cal->getCalendar($month, $year);
        
        $this->view->calendar = $c;
        
        $this->_response->setBody($this->view->render('index/getCal.tpl'));
    }

    
    /**
     * AJAX function that returns the events for the day the user has
     * hovered over
     */
    public function getEventsAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $year  = $filter->filter($get['year']);
        $month = $filter->filter($get['month']);
        $day   = $filter->filter($get['day']);
        
        $event = new Event();
        //$events = $event->fetchAll();
        
        echo Zend_Json_Encoder::encode(array("Word - 8:00 - 10:00", "Excel - 2:00 - 4:00", "Date: $month-$day-$year"));
    }
}
