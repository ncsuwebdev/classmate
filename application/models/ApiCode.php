<?php
/**
 * Website
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
 * @package    Website
 * @subpackage Image
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Semester.php 188 2007-07-31 17:59:10Z jfaustin@EOS.NCSU.EDU $
 */

/**
 *
 * @package    Website
 * @subpackage PortalLink
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class ApiCode extends Zend_Db_Table implements Itdcs_Model_Interface
{
    /**
     * Database table name
     *
     * @var string
     */
    public $_name = 'tbl_api_access';

    /**
     * Primary key for the database
     *
     * @var string
     */
    protected $_primary = 'userId';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_messages = array();
    
    protected $_apiCodeUser = '';
    

    /**
     * Checks validity of the data object
     *
     * !! Since no data object is checked, we always return true !!
     *
     * @param string $data
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
     * Checks validity of an ID
     *
     * @param int $semesterId
     * @return boolean
     */
    public function isValidId($id) {

        if (count($this->_messages) == 0) {
            return true;
        }

        return false;
    }

    /**
     * Gets any error messages generated
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

        $data['code'] = md5(microtime());
        
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

        if (is_null($where)) {
            $where = $this->getAdapter()->quoteInto($this->_primary[1] . ' = ?', $data[$this->_primary[1]]);
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
     * Deletes a row in the database
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
     * Find overrides the Zend_Db_Table find method and adds business logic to
     * check the validity of the id passed to the function
     *
     * @param int $id The ID to look up
     * @return The DbRow for the ID to be looked up
     */
    public function find($key)
    {

        $args = func_get_args();

        $id = $args[0];
        if ($this->isValidId($id) === false) {
            return false;
        }

        try {
            $result = parent::find($id);
        } catch (Exception $e) {
            $this->_messages[] = $e->getMessage();
            return false;
        }

        if (count($result) == 1) {
            return $result->current();
        }

        return null;
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
    
    public function verify($accessCode)
    {
    	$where = $this->getAdapter()->quoteInto('code = ?', $accessCode);
    	$this->_messages[] = $where;
    	$result = $this->fetchAll($where, null, 1);
    	
    	if ($result === false) {
    		return false;
    	}
    	
    	if ($result->count() != 1) {
    		return false;
    	}
    	
    	$this->_apiCodeUser = $result->current()->userId;
    	
        $config = Zend_Registry::get('config');
        Zend_Loader::loadClass($config->authorization);

        $authz = new $config->authorization;   
        
        try {
        	$result = $authz->getUser($this->_apiCodeUser);
        	$this->_messages[] = $result;
        } catch (Exception $e) {
        	return false;
        }
        
        return true;
    }
    
    public function getApiUserId()
    {
    	return $this->_apiCodeUser;
    }
}
