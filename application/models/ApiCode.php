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
class ApiCode extends Ot_Db_Table 
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

    protected $_apiCodeUser = '';
 
    /**
     * Inserts a new row into the table
     *
     * @param array $data
     * @return Result from Zend_Db_Table::insert()
     */
    public function insert(array $data)
    {
        $data['code'] = md5(microtime());
        
        return parent::insert($data);
    }

    public function verify($accessCode)
    {
    	$where = $this->getAdapter()->quoteInto('code = ?', $accessCode);
    	$this->_messages[] = $where;
    	$result = $this->fetchAll($where, null, 1);
    	
    	if ($result->count() != 1) {
    		throw new Exception('Code not found');
    	}
    	
    	$this->_apiCodeUser = $result->current()->userId;
    	
        $config = Zend_Registry::get('config');
        Zend_Loader::loadClass($config->authorization);

        $authz = new $config->authorization;   
        
        $result = $authz->getUser($this->_apiCodeUser);
    }
    
    public function getApiUserId()
    {
    	return $this->_apiCodeUser;
    }
}
