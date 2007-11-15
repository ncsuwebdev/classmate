<?php
/**
 * Aerial
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
 * @package    Aerial (Admin)
 * @subpackage Admin_LogController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: LogController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Controller to show logs gathered from the application.
 *
 * @package    Aerial (Admin)
 * @subpackage Admin_LogController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Admin_LogController extends Internal_Controller_Action 
{
    /**
     * displays logs based on search criteria
     */
    public function indexAction()
    {
    	$al = new ActionLog();

        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');

        $where = '';
        $dba = $al->getAdapter();

        $attr = array('userId', 'role', 'attributeName', 'attributeId', 'request', 'sid', 'priority');

        foreach ($attr as $a) {
            if (isset($get[$a]) && $get[$a] != '') {
                if ($where != '') {
                    $where .= ' AND ';
                }

                $where .= $dba->quoteInto($a . ' = ?', $filter->filter($get[$a]));

                $this->view->$a = $filter->filter($get[$a]);
            }
        }

        if (isset($get['beginDt']) && isset($get['endDt']) && strtotime($get['endDt']) != 0 && strtotime($get['beginDt']) != 0) {
            if ($where != '') {
                $where .= ' AND ';
            }

            $where .= '(' .
                $dba->quoteInto('timestamp >= ?', strtotime($filter->filter($get['beginDt']))) .
                ' AND ' .
                $dba->quoteInto('timestamp <= ?', strtotime($filter->filter($get['endDt']))) .
                ')';

            $this->view->beginDt = $filter->filter($get['beginDt']);
            $this->view->endDt = $filter->filter($get['endDt']);
        }


        $result = array();
        if ($where != '') {
            $result = $al->fetchAll($where, 'timestamp DESC');

            $result = $result->toArray();
        }

        if (count($result) != 0) {
            $this->view->javascript = 'sortable.js';
        }

        $this->view->logs           = $result;

        $this->view->priorityTypes = array(
            '' => '',
            'EMERG',
            'ALERT',
            'CRIT',
            'ERR',
            'WARN',
            'NOTICE',
            'INFO',
            'DEBUG',
            );

        $this->view->title          = "Action Logs";
    }

    /**
     * shows the details of the log message
     *
     */
    public function detailsAction()
    {
        $al = new ActionLog();

        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');

        if (!isset($get['logId'])) {
            throw new Internal_Exception_Input('Log ID not set');
        }

        $log = $al->find((int)$filter->filter($get['logId']));

        if (is_null($log)) {
            throw new Internal_Exception_Data('Log message could not be found');
        }

        $this->view->log            = $log->toArray();
        $this->view->title          = "Action Log Details";
    }
}