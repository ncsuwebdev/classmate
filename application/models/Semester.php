<?php
/**
 * 
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
 * @package    
 * @subpackage Semester
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Semester.php 188 2007-07-31 17:59:10Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Model to do all searching with regards to semesters.  Semester dates are determined
 * by Registration and Records up to three years in the future.  Dates must be entered
 * manually, so we have no reason to insert, update, or delete data, only search it.
 *
 * @package    Cyclone
 * @subpackage Semester
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class Semester extends Ot_Db_Table
{
    /**
     * Database table name
     *
     * @var string
     */
    public $_name = 'tbl_semester';

    /**
     * Primary key for the database
     *
     * @var string
     */
    protected $_primary = 'semesterId';

    /**
     * Gets all available semesters that are open for signup
     *
     * @param int $timestamp
     * @return associative array as follows
     *
     * Array (
     *    [0] => Array (
     *       [status]   => (current or open),
     *       [semester] => Zend_Db_Table_Row
     *       )
     *    [...]
     *    [n] => Array (
     *       [status]   => (current or open),
     *       [semester] => Zend_Db_Table_Row
     *       )
     * )
     */
    public function getFutureSemesters($timestamp = 0)
    {
        require_once 'Zend/Date.php';

        if ($timestamp == 0) {
            $timestamp = time();
        }

        $semesters = $this->fetchAll(null, 'startDate ASC');

        $after = array();

        foreach ($semesters as $s) {

            $preActivateDate  = new Zend_Date($s->startDate);

            $preActivateDate->subDay($s->preSemesterActivateDays);

            if ($timestamp < $preActivateDate->getTimestamp()) {
                $after[] = $s->toArray();
            }
        }

        return $after;
    }

    /**
     * Gets the current semester based on the timestamp.  if the timestamp is 0, the
     * current time is used.
     *
     * @param int $timestamp
     * @return Zend_Db_Table_Row
     */
    public function getCurrentSemester($timestamp = 0)
    {
        require_once 'Zend/Date.php';

        if ($timestamp == 0) {
            $timestamp = time();
        }

        $semesters = $this->fetchAll(null, 'startDate ASC');

        foreach ($semesters as $s) {

            $preActivateDate  = new Zend_Date($s->startDate);

            $preActivateDate->subDay($s->preSemesterActivateDays);

            if ($timestamp >= $preActivateDate->getTimestamp()) {
                $before = $s;
            } else {
                return $before;
            }
        }

        $this->_messages[] = 'Current semester not found';
        return false;
    }

    /**
     * Gets the next semester based on the timestamp.  if the timestamp is 0, the
     * current time is used.
     *
     * @param int $timestamp
     * @return Zend_Db_Table_Row
     */
    public function getNextSemester($timestamp = 0)
    {
        require_once 'Zend/Date.php';

        if ($timestamp == 0) {
            $timestamp = time();
        }

        $semesters = $this->fetchAll(null, 'startDate ASC');

        if ($semesters === false) {
            return false;
        }

        foreach ($semesters as $s) {

            $preActivateDate  = new Zend_Date($s->startDate);

            $preActivateDate->subDay($s->preSemesterActivateDays);

            if ($timestamp < $preActivateDate->getTimestamp()) {
                return $s;
            }
        }

        throw new Exception('Next semester data not found');
    }

    /**
     * Gets the end of the current semester based on the timestamp.  if the timestamp
     * is 0, the current time is used.  The end date of a semester is determined
     * by the activate date of the following semester.
     *
     * @param int $timestamp
     * @return Zend_Db_Table_Row
     */
    public function getSemesterEndDt($timestamp = 0)
    {
        $next = $this->getNextSemester($timestamp);

        if ($next === false) {
            return false;
        }

        $dt = new Zend_Date($next->startDate);
        $dt->subDay($next->preSemesterActivateDays);

        return $dt->getTimestamp();
    }
}
