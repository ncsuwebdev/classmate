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
        
        $this->view->javascript = array(
                     "cnet/common/utilities/simple.template.parser.js",
                     "cnet/mootools.extended/Native/element.shortcuts.js",
                     "cnet/mootools.extended/Native/element.dimensions.js",
                     "cnet/mootools.extended/Native/element.position.js",
                     "cnet/mootools.extended/Native/element.pin.js",
                     "cnet/common/browser.fixes/IframeShim.js",
                     "cnet/common/js.widgets/modalizer.js",
                     "cnet/common/js.widgets/stickyWin.default.layout.js",
                     "cnet/common/js.widgets/stickyWin.js",
                     "cnet/common/js.widgets/stickyWinFx.js",
                     "cnet/common/js.widgets/stickyWin.Modal.js", 
                     "cnet/common/js.widgets/stickyWin.Ajax.js",
                     "cnet/common/js.widgets/popupdetails.js",
                  );
        
        
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
     * This is an AJAX function that gets the week view data.  If the
     * newWindow flag is set in the query string, then it instructs it
     * to render the entire week view window.  If it's not, it just
     * renders the table of the week data.
     *
     */
    public function weekViewAction()
    {
        
        $this->_helper->viewRenderer->setNeverRender();
        
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $year  = $filter->filter($get['year']);
        $week  = $filter->filter($get['week']);
        
        $newWindow = 0;
        if (isset($get['newWindow'])) {
            $newWindow = $filter->filter($get['newWindow']);
        }
        
        $this->view->year = $year;
        $this->view->week = $week;
        
        $cal = new Calendar();
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
        
        if ($newWindow) {
            $this->_response->setBody($this->view->render('index/weekView.tpl'));
        } else {
            $this->_response->setBody($this->view->render('index/getWeek.tpl'));
        }
        
    }

    
    public function getEventDetailsAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $workshopId = $filter->filter($get['workshopId']);
        $eventId    = $filter->filter($get['eventId']);
                             
        $workshop = new Workshop();
        $event    = new Event();
        
        $w = $workshop->find($workshopId)->toArray();
        $e = $event->find($eventId);
        
        $w['description'] = $this->_truncate(strip_tags($w['description']), 300);
                
        $w['time'] = strftime('%I:%M %p', strtotime($e->startTime)) . " - " . strftime('%I:%M %p', strtotime($e->endTime));
                
        echo Zend_Json_Encoder::encode($w);
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
        $date  = $year . "-" . $month . "-" . $day;
        
        $event = new Event();
        $where = $event->getAdapter()->quoteInto('date = ?', $date);
        $where .= " AND ";
        $where .= $event->getAdapter()->quoteInto('status = ?', 'open');
               
        $events = $event->fetchAll($where)->toArray();
        
        $workshop = new Workshop();
        
        for ($x=0; $x < count($events); $x++) {
            $where = $workshop->getAdapter()->quoteInto('workshopId = ?', $events[$x]['workshopId']);
            $events[$x]['workshopData'] = $workshop->fetchAll($where)->current()->toArray();
        }
        
        echo Zend_Json_Encoder::encode($events);
    }
    
    
    private function _truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
    {
        if ($length == 0)
            return '';
    
        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
            }
            if(!$middle) {
                return substr($string, 0, $length) . $etc;
            } else {
                return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
            }
        } else {
            return $string;
        }
    }
}
