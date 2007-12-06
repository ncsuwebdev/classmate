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
                    $tmp['num'] = "&nbsp;";
                    $calData['rows'][$x]['days'][] = $tmp; 
                }
                
                $sd = $calData['startDay']; 
            }
            
            // put the numbers in the rows
            for ($z = $sd; $z < 7; $z++) {
                    $tmp = array();
                if ($dayCounter <= $calData['monthDays']) {
                    $tmp['num'] = $dayCounter;
                    $calData['rows'][$x]['days'][] = $tmp;
                } else {
                    $tmp['num'] = "&nbsp;";
                    $calData['rows'][$x]['days'][] = $tmp;
                }
                $dayCounter++;
            }
        }
        
        return $calData;
    }
}