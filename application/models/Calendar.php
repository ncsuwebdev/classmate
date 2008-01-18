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
 * @package    ClassMate
 * @subpackage Calendar
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Building.php 156 2007-07-20 12:57:10Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * This model handles all the calendar creation
 *
 * @package    ClassMate
 * @subpackage Building
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class Calendar 
{
    
    public function getCalendar($month = null, $year = null)
    {
        $zd = new Zend_Date();      
        
        if (is_null($month)) {
            $month = date('n');         
        }
        
        if (is_null($year)) {
            $year = date('Y');
        }
        
       
        $zd->setMonth($month);
        $zd->setYear($year);
        $zd->setDay(1);
        
            
        $calData = array();
    
        $calData['startDay']  = $zd->get(Zend_Date::WEEKDAY_DIGIT);
        $calData['month']     = $zd->get(Zend_Date::MONTH_SHORT);
        $calData['monthName'] = $zd->get(Zend_Date::MONTH_NAME);
        $calData['monthDays'] = $zd->get(Zend_Date::MONTH_DAYS);
        $calData['year']      = $zd->get(Zend_Date::YEAR);
        
        $calData['rows'] = array();
                    
        $dayCounter = 1;

        // make sure we don't have an empty row
        if ($calData['startDay'] > 4) {
            $numRows = intval($calData['monthDays'] / 5);
        } else {
            $numRows = intval($calData['monthDays'] / 6);
        }
        
        for ($x = 0; $x < $numRows; $x++) {
              
            $sd = 0;
            
            if ($x == 0) {
                                
                // this sets the first row to start on the correct day of the week
                for ($y = 0; $y < $calData['startDay']; $y++) {
                    $tmp = array();
                    $tmp['num'] = "";
                    $calData['rows'][$x]['days'][$y] = $tmp; 
                }
                
                $sd = $calData['startDay']; 
            }
            
            $event = new Event();
            
            // put the numbers in the rows
            for ($z = $sd; $z < 7; $z++) {
                               
                $tmp = array();
                if ($dayCounter <= $calData['monthDays']) {
                    
                    $zd->setDay($dayCounter);
                
                    // set the week number  
                    $calData['rows'][$x]['weekNum'] = $zd->get(Zend_Date::WEEK);
                    $calData['rows'][$x]['weekYear'] = $zd->get(Zend_Date::YEAR);

                    if (isset($calData['rows'][$x-1]['weekNum'])) {
                        if ($calData['rows'][$x-1]['weekNum'] == 52) {
                            $calData['rows'][$x]['weekNum'] = "01";
                            $calData['rows'][$x]['weekYear'] = $year + 1;
                        }
                    }
                    
                    $tmp['num'] = $dayCounter;
                    $calData['rows'][$x]['days'][$z] = $tmp;

                                       
                    $where = $event->getAdapter()->quoteInto('date = ?', $year . "-" . $month . "-" . $dayCounter);
                    $where .= " AND ";
                    $where .= $event->getAdapter()->quoteInto('status = ?', 'open');
                
                    
                    $events = $event->fetchAll($where, 'startTime')->toArray();
             
                    $calData['rows'][$x]['days'][$z]['numEvents'] = count($events);
                    
                } else {
                    $tmp['num'] = "";
                    $calData['rows'][$x]['days'][$z] = $tmp;
                }
                $dayCounter++;
            }
        }
        
        return $calData;
    }
    
    public function getWeek($week, $year)
    {
        
        $zd = new Zend_Date();
        $event = new Event();
        $workshop = new Workshop();
        
        if ($week == 1) {
        	
        	$calData['prevWeekNum'] = 52;
        	$calData['prevYear'] = $year - 1;
        	$calData['nextWeekNum'] = 2;
        	$calData['nextYear'] = $year;
        
        } else if ($week == 52) {
        	
        	$calData['prevWeekNum'] = 51;
            $calData['prevYear'] = $year;
            $calData['nextWeekNum'] = 1;
            $calData['nextYear'] = $year + 1;
        	
        } else {
        	
        	$calData['prevWeekNum'] = $week - 1;
            $calData['prevYear'] = $year;
            $calData['nextWeekNum'] = $week + 1;
            $calData['nextYear'] = $year;
        }
        
        // go back to the current week
        $zd->setYear($year);
        $zd->setWeek($week);
        
        // set the weekday to sunday for the display purposes
        $zd->setWeekday("sunday");
        $month = $zd->get(Zend_Date::MONTH_SHORT);
        
        for ($x = 0; $x < 7; $x++) {
            
            $tmp = array();
       
            $tmp['startDay']  = $zd->get(Zend_Date::WEEKDAY_DIGIT);
            $tmp['month']     = $zd->get(Zend_Date::MONTH_SHORT);
            $tmp['day']       = $zd->get(Zend_Date::DAY_SHORT);
            $tmp['monthName'] = $zd->get(Zend_Date::MONTH_NAME);
            $tmp['monthDays'] = $zd->get(Zend_Date::MONTH_DAYS);
            $tmp['year']      = $zd->get(Zend_Date::YEAR);
            $tmp['weekNum']   = $zd->get(Zend_Date::WEEK);
            $tmp['date']      = $zd->getDate();
               
            $calData[$x] = $tmp;
            
            $where = $event->getAdapter()->quoteInto('date = ?', $tmp['year'] . "-" . $tmp['month'] . "-" . $tmp['day']);
            $where .= " AND ";
            $where .= $event->getAdapter()->quoteInto('status = ?', 'open');
        
            $calData[$x]['events'] = $event->fetchAll($where, 'startTime')->toArray();

            for ($y = 0; $y < count($calData[$x]['events']); $y++) {
            
                if (isset($calData[$x]['events'][$y]['workshopId'])) {
                    
                    $tmpStart = strtotime($calData[$x]['events'][$y]['startTime']);
                    $tmpEnd   = strtotime($calData[$x]['events'][$y]['endTime']);
        
                    $calData[$x]['events'][$y]['numMinutes'] = ($tmpEnd - $tmpStart) / 60;
                    
                    $workshopId = $calData[$x]['events'][$y]['workshopId'];
                    $calData[$x]['events'][$y]['workshop'] = $workshop->find($workshopId)->toArray();
                }
            }
            
            $zd->addDay(1);
        }
        
        $calData['weekNum'] = $zd->get(Zend_Date::WEEK);
        
        $calData['year']  = $year;
         
        return $calData;
    }
}