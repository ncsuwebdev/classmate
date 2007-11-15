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
 * @subpackage Admin_EmailqueueController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: EmailqueueController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Controller to manage the email queue in the admin section
 *
 * @package    Aerial (Admin)
 * @subpackage Admin_EmailqueueController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Admin_EmailqueueController extends Internal_Controller_Action 
{
    /**
     * displays emails based on the search criteria
     */
    public function indexAction()
    {
        $eq = new EmailQueue();

        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');

        $where = '';
        $dba = $eq->getAdapter();

        $attr = array('status', 'attributeName', 'attributeId', 'callId');

        foreach ($attr as $a) {
            if (isset($get[$a]) && $get[$a] != '') {
                if ($where != '') {
                    $where .= ' AND ';
                }

                $where .= $dba->quoteInto($a . ' = ?', $filter->filter($get[$a]));

                $this->view->$a = $filter->filter($get[$a]);
            }
        }

        if (isset($get['queueBeginDt']) && isset($get['queueEndDt']) && strtotime($get['queueEndDt']) != 0 && strtotime($get['queueBeginDt']) != 0) {
            if ($where != '') {
                $where .= ' AND ';
            }

            $where .= '(' .
                $dba->quoteInto('queueDt >= ?', strtotime($filter->filter($get['queueBeginDt']))) .
                ' AND ' .
                $dba->quoteInto('queueDt <= ?', strtotime($filter->filter($get['queueEndDt']))) .
                ')';

            $this->view->queueBeginDt = $filter->filter($get['queueBeginDt']);
            $this->view->queueEndDt = $filter->filter($get['queueEndDt']);
        }

        if (isset($get['sentBeginDt']) && isset($get['sentEndDt']) && strtotime($get['sentEndDt']) != 0 && strtotime($get['sentBeginDt']) != 0) {
            if ($where != '') {
                $where .= ' AND ';
            }

            $where .= '(' .
                $dba->quoteInto('sentDt >= ?', strtotime($filter->filter($get['sentBeginDt']))) .
                ' AND ' .
                $dba->quoteInto('sentDt <= ?', strtotime($filter->filter($get['sentEndDt']))) .
                ')';

            $this->view->sentBeginDt = $filter->filter($get['sentBeginDt']);
            $this->view->sentEndDt = $filter->filter($get['sentEndDt']);
        }


        $result = array();
        if ($where != '') {
            $result = $eq->fetchAll($where, 'queueDt DESC');
        }

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['msg'] = array(
                'to' => implode(', ', $result[$i]['zendMailObject']->getRecipients()),
                'from' => $result[$i]['zendMailObject']->getFrom(),
                'subject' => $result[$i]['zendMailObject']->getSubject(),
                );
        }

        if (count($result) != 0) {
            $this->view->javascript = 'sortable.js';
        }

        $this->view->emails = $result;

        $this->view->statusTypes = array(
            ''        => '',
            'waiting' => 'Waiting',
            'sent'    => 'Sent',
            'error'   => 'Error',
            );

        $this->view->title          = "Email Queue";
    }

    /**
     * Shows the details of an email that is in the queue
     *
     */
    public function detailsAction()
    {
        $eq = new EmailQueue();

        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');

        if (!isset($get['queueId'])) {
            throw new Internal_Exception_Input('Queue ID not set');
        }

        $email = $eq->find((int)$filter->filter($get['queueId']));

        if (is_null($email)) {
            throw new Internal_Exception_Data('Queued email could not be found');
        }

        $email['msg'] = array(
            'to'      => implode(', ', $email['zendMailObject']->getRecipients()),
            'from'    => $email['zendMailObject']->getFrom(),
            'subject' => $email['zendMailObject']->getSubject(),
            'body'    => nl2br(quoted_printable_decode($email['zendMailObject']->getBodyText(true))),
            'header'  => $email['zendMailObject']->getHeaders(),
            );

        $this->view->email          = $email;
        $this->view->title          = "Queued Email Details";
    }
}