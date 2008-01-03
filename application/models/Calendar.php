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

                    // we want to make sure that we don't get the previous year's dates, so 
                    // we make any extra days week 53 in December of the current year instead of 01 of the next year
                    if (isset($calData['rows'][$x-1]['weekNum'])) {
                        if ($calData['rows'][$x-1]['weekNum'] == 52) {
                            $calData['rows'][$x]['weekNum'] = 53;
                        }
                    }
                    
                    $tmp['num'] = $dayCounter;
                    $calData['rows'][$x]['days'][$z] = $tmp;

                                       
                    $where = $event->getAdapter()->quoteInto('date = ?', $year . "-" . $month . "-" . $dayCounter);
                    $where .= " AND ";
                    $where .= $event->getAdapter()->quoteInto('status = ?', 'open');
                
                    
                    $events = $event->fetchAll($where)->toArray();
             
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
               
        $zd->setYear($year);
        $zd->setWeek($week);
        
        $zd->setWeekday("saturday");
        
        $month = $zd->get(Zend_Date::MONTH_SHORT);
        
        $calData = array();       
        
        // go to the week before the current week
        $zd->subWeek(1);
        $calData['prevWeekNum'] = $zd->get(Zend_Date::WEEK);
        $calData['prevYear']    = $zd->get(Zend_Date::YEAR);

        // go to the next week after the current week
        $zd->addWeek(2);
        $calData['nextWeekNum'] = $zd->get(Zend_Date::WEEK);
        $calData['nextYear']    = $zd->get(Zend_Date::YEAR);
        
        // go back to the current week
        $zd->subWeek(1);
                
        
        // set the weekday to sunday for the display purposes
        $zd->setWeekday("sunday");
        
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
        
            $calData[$x]['events'] = $event->fetchAll($where)->toArray();

            for ($y = 0; $y < count($calData[$x]['events']); $y++) {
            
                if (isset($calData[$x]['events'][$y]['workshopId'])) {
                    $workshopId = $calData[$x]['events'][$y]['workshopId'];
                    $calData[$x]['events'][$y]['workshop'] = $workshop->find($workshopId)->toArray();
                }
            }
            
            $zd->addDay(1);
        }
        
        if ($calData[0]['weekNum'] == 52 && $calData[6]['weekNum'] == 1) {
            if ($month == 12) {
                $calData['weekNum'] = 53;
            } else {
                $calData['weekNum'] = 1;
            }
        } else {
            $calData['weekNum'] = $zd->get(Zend_Date::WEEK);
        }
        
        $calData['year']  = $year;
         
        return $calData;
    }
}