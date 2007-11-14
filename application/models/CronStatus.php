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
 * @package    Cyclone
 * @subpackage Bug
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Bug.php 156 2007-07-20 12:57:10Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Grab the model interface from the library
 */
require_once 'Itdcs/Model/Interface.php';

/**
 * Model to allow admins to enable and disable cron jobs
 *
 * @package    Cyclone
 * @subpackage CronStatus
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class CronStatus extends Zend_Db_Table implements Itdcs_Model_Interface {


    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_cron_status';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'path';


    /**
     * Error messages
     *
     * @var array
     */
    protected $_messages = array();


    /**
     * Validates data
     *
     * @param array $data
     * @return boolean
     */
    public function isValid(&$data)
    {

        if (count($this->_messages) == 0) {
            return true;
        }

        return false;
    }


    /**
     * Validates an id.  This id is the one that is a primary key used mainly when
     * using find()
     *
     * @param int $id
     * @return boolean
     */
    public function isValidId($id)
    {
        if ($id == '') {
            $this->_messages[] = "The id must be non-empty.";
        }

        if ($id == 0) {
            $this->_messages[] = "The id must not be 0.";
        }

        if (!is_int($id)) {
            $this->_messages[] = "The id must be an integer.";
        }

        if ($id < 0) {
            $this->_messages[] = "The id must be greater than 0.";
        }

        if (count($this->_messages) == 0) {
            return true;
        }

        return false;
    }


    /**
     * Gets any error messages generated from isValid();
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }


    /**
     * Inserts a new row into the table
     *
     * @param array $data
     * @return Result from Zend_Db_Table::insert()
     */
    public function insert(array $data)
    {
        if ($this->isValid($data) === false) {
            return false;
        }

        try {
            $result = parent::insert($data);
        } catch (Exception $e) {
            $this->_messages[] = $e->getMessage();
            return false;
        }

        return $result;
    }

    /**
     * Updates the table
     *
     * @param array $data The column=>value paired array of data
     * @param string $where The sql where clause to use
     * @return Result from Zend_Db_Table::update()
     */
    public function update(array $data, $where)
    {
        if ($this->isValid($data) === false) {
            return false;
        }

        try {
            $result = parent::update($data, $where);
        } catch (Exception $e) {
            $this->_messages[] = $e->getMessage();
            return false;
        }

        return $result;
    }

    /**
     * Deletes a row from the database
     *
     * @param string $where
     * @return boolean
     */
    public function delete($where)
    {
        try {
            $result = parent::delete($where);
        } catch (Exception $e) {
            $this->_messages[] = $e->getMessage();
            return false;
        }

        return $result;
    }

    /**
     * Returns a row from the database
     *
     * @param int $id
     * @return boolean or array
     */
    public function find($key)
    {
        $args = func_get_args();
        $id = $args[0];

        try {
            $result = parent::find($id);
        } catch (Exception $e) {
            $this->_messages[] = $e->getMessage();
            return false;
        }

        return $result;
    }


    /**
     * Fetch all attributes matching $where
     *
     * @param string|array $where  OPTIONAL An SQL WHERE clause.
     * @param string|array $order  OPTIONAL An SQL ORDER clause.
     * @param int          $count  OPTIONAL An SQL LIMIT count.
     * @param int          $offset OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Table_Rowset The row results per the Zend_Db_Adapter_Abstract fetch mode.
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        try {
            $result = parent::fetchAll($where, $order, $count, $offset);
        } catch (Exception $e) {
            $this->_messages[] = $e->getMessage();
            return false;
        }

        return $result;
    }

    /**
     * Fetches one row.
     *
     * Honors the Zend_Db_Adapter_Abstract fetch mode.
     *
     * @param string|array $where OPTIONAL An SQL WHERE clause.
     * @param string|array $order OPTIONAL An SQL ORDER clause.
     * @return Zend_Db_Table_Row The row results per the Zend_Db_Adapter_Abstract fetch mode.
     */
    public function fetchRow($where = null, $order = null)
    {
        try {
            $result = parent::fetchRow($where, $order);
        } catch (Exception $e) {
            $this->_messages[] = $e->getMessage();
            return false;
        }

        return $result;
    }

    /**
     * Checks to see if a certain cron job is enabled
     *
     * @param string $path
     * @return boolean
     */
    public function isEnabled($path)
    {
        $result = $this->find($path);

        if ($result === false) {
            return false;
        }

        if ($result->count() != 1) {
            return false;
        }

        return ($result->current()->status == 'enabled');
    }

    public function setCronStatus($path, $status)
    {
        $dba = $this->getAdapter();

        if ($path == 'all') {

            $jobs = $this->getAvailableCronJobs();

            if ($jobs === false) {
                return false;
            }

            foreach ($jobs as $j) {
                $data = array('status' => $status);
                $job = $this->find($j['path']);
                if ($job === false) {
                    return false;
                }

                if ($job->count() == 1) {
                    $where = $dba->quoteInto('path = ?', $j['path']);

                    $result = $this->update($data, $where);
                    if ($result === false) {
                        return false;
                    }
                } else {

                    $data['path'] = $j['path'];

                    $result = $this->insert($data);
                    if ($result === false) {
                        return false;
                    }
                }
            }
        } else {

            $data = array('status' => $status);
            $job = $this->find($path);
            if ($job === false) {
                return false;
            }

            if ($job->count() == 1) {
                $where = $dba->quoteInto('path = ?', $path);

                $result = $this->update($data, $where);
                if ($result === false) {
                    return false;
                }
            } else {
                $data['path'] = $path;

                $result = $this->insert($data);
                if ($result === false) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getAvailableCronJobs()
    {
        $config = Zend_Registry::get('config');

        if (!is_dir($config->cronDirectory)) {
            $this->_messages = 'Cron directory not set correctly in config file';
            return false;
        }

        $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($config->cronDirectory));
        $crons = array();
        foreach ($dir as $file) {
            $f = str_replace($config->cronDirectory . '/', '', $file);
            if (preg_match('/\.php$/i', $f)) {
                $temp = array();
                $temp['path'] = str_replace(DIRECTORY_SEPARATOR, '_', preg_replace('/\.php$/i', '', $f));

                $data = $this->find($temp['path']);
                if ($data === false) {
                    return false;
                }

                if ($data->count() == 1) {
                    $temp = $data->current()->toArray();
                } else {
                    $temp['status'] = 'disabled';
                }

                $crons[] = $temp;
            }
        }

        return $crons;
    }
}
?>
