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
 * @subpackage Stats
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the log table and generate interesting statistics
 *
 * @package    Classmate
 * @subpackage Log
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Stats extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_log';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'logId';
    
    
    public function getLoginCount($startDate = null, $endDate = null)
    {
        $where = $this->getAdapter()->quoteInto('priorityName = ?', 'LOGIN');
        return $this->fetchAll($where)->count();
    }
    
    public function getUpcomingEventsCount()
    {
        $event = new Event();
        
        $events['open'] = count($event->getEvents(null, null, time(), null, 'open'));
        $events['closed'] = count($event->getEvents(null, null, time(), null, 'closed'));
        $events['canceled'] = count($event->getEvents(null, null, time(), null, 'canceled'));
        $events['totalNumber'] = $events['open'] + $events['closed'] + $events['canceled'];
        
        return $events;
    }
    
    public function getPastEventsCount()
    {
        $event = new Event();
        
        $events['open'] = count($event->getEvents(null, null, 0, time(), 'open'));
        $events['closed'] = count($event->getEvents(null, null, 0, time(), 'closed'));
        $events['canceled'] = count($event->getEvents(null, null, 0, time(), 'canceled'));
        $events['totalNumber'] = $events['open'] + $events['closed'] + $events['canceled'];
        
        return $events;
    }
}