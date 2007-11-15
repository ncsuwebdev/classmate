<?php

/**
 * Cyclone
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
 * @package    Cyclone (Default)
 * @subpackage BugController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: BugController.php 216 2007-08-07 12:59:59Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Bug reports
 *
 * @package    Cyclone (Default)
 * @subpackage BugController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class BugController extends Internal_Controller_Action 
{
    /**
     * shows all open bugs
     *
     */
    public function indexAction()
    {
        $bug = new Bug();

        $bugs = $bug->getBugs();

        $this->view->acl = array(
            'add'     => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'details' => $this->_acl->isAllowed($this->_role, $this->_resource, 'details'),
            );

        if ($bugs->count() != 0) {
            $this->view->javascript = 'sortable.js';
        }

        $this->view->bugs = $bugs->toArray();
        $this->view->title = 'Bug Reports';
    }

    /**
     * shows the details of the bug
     *
     */
    public function detailsAction()
    {
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');

        if (!isset($get['bugId'])) {
            throw new Internal_Exception_Input('bugId not set in query string');
        }

        $bugId = $filter->filter($get['bugId']);

        $bug = new Bug();

        $thisBug = $bug->find((int)$bugId);

        if ($thisBug->count() != 1) {
            throw new Internal_Exception_Input('Bug not found');
        }

        $this->view->acl = array(
            'changeStatus' => $this->_acl->isAllowed($this->_role, $this->_resource, 'changeStatus'),
            );

        $this->view->statusTypes = array(
            'new'    => 'New',
            'ignore' => 'Ignore',
            'fixed'  => 'Fixed',
            );

        $this->view->bug = $thisBug->current()->toArray();
        $this->view->title = 'Bug Details';
    }

    /**
     * adds a bug to the system
     *
     */
    public function addAction()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');

            $data = array(
                'submittedByUserId' => Zend_Auth::getInstance()->getIdentity(),
                'reproducibility'   => $filter->filter($post['reproducibility']),
                'severity'          => $filter->filter($post['severity']),
                'priority'          => $filter->filter($post['priority']),
                'description'       => $filter->filter($post['description']),
                );

            $bug = new Bug;

            $result = $bug->insert($data);

            $mail = new Zend_Mail();

            $config = Zend_Registry::get('config');
            Zend_Loader::loadClass($config->authorization);

            $authz = new $config->authorization(Zend_Auth::getInstance()->getIdentity());
            $users = $authz->getUsers($config->bugContactRole);

            if ($users !== false) {

                foreach ($users as $u) {
                    $mail->addTo($u['userId'] . '@ncsu.edu', $u['userId']);
                }

                $mail->setFrom(Zend_Auth::getInstance()->getIdentity() . '@ncsu.edu');
                $mail->setSubject('Bug report filed by ' . $data['submittedByUserId']);
                $mail->setBodyText($data['description']);

                $mail->send();
            }
            
            $this->_logger->setEventItem('attributeName', 'bugId');
            $this->_logger->setEventItem('attributeId', (int)$result);
            $this->_logger->info('Bug was added');


            $this->_redirect('/bug/details/?bugId=' . $result);

        } else {
            $reproducibilityTypes = array(
                'always'    => 'Always',
                'sometimes' => 'Sometimes',
                'never'     => 'Never',
                );

            $severityTypes = array(
                'minor' => 'Minor',
                'major' => 'Major',
                'crash' => 'Crash',
                );

            $priorityTypes = array(
                'low'      => 'Low',
                'medium'   => 'Medium',
                'high'     => 'High',
                'critical' => 'Critical',
                );

            $this->view->reproducibilityTypes = $reproducibilityTypes;
            $this->view->severityTypes        = $severityTypes;
            $this->view->priorityTypes        = $priorityTypes;

            $this->view->title = 'New Bug Report';
        }
    }

    /**
     * change status of the bugs
     *
     */
    public function changeStatusAction()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');
            $filter = Zend_Registry::get('inputFilter');

            if (!isset($post['bugId'])) {
                throw new Internal_Exception_Input('bugId not set in query string');
            }

            $data = array(
                'bugId'  => $filter->filter($post['bugId']),
                'status' => $filter->filter($post['status']),
                );

            $bug = new Bug;

            $bug->update($data, null);

            $this->_logger->setEventItem('attributeName', 'bugId');
            $this->_logger->setEventItem('attributeId', $data['bugId']);
            $this->_logger->info('Bug status was changed to ' . $data['status']);

            $this->_redirect('/bug/');
        }

        $this->_redirect('/bug/');
    }
}
