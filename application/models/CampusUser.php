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
 * @subpackage CampusUser
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: CampusUser.php 155 2007-07-19 19:44:26Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Grab the model interface from the library
 */
require_once 'Itdcs/Model/Interface.php';

/**
 * Gets the Key Manager
 */
require_once $_SERVER['KEY_MANAGER_PATH'];

/**
 * Gets the NCSU LDAP reader
 */
require_once 'Itdcs/Ldap/Ncsu.php';

/**
 * Handles all interaction dealing with User Accounts
 *
 * @package    Cyclone
 * @subpackage CampusUser
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class CampusUser implements Itdcs_Model_Interface
{

    /**
     * Error messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * LDAP object
     *
     * @var unknown_type
     */
    protected $_ldap = null;

    /**
     * creates a new instance of CampusUser
     *
     */
    public function __construct()
    {
        $km = new KeyManager();

        $key = $km->getKeyObject('ldapreader');

        $this->_ldap = new Itdcs_Ldap_Ncsu();

        try {
            $result = $this->_ldap->connect($key->host, $key->bindDn, $key->password);
        } catch (Exception $e) {
            die('LDAP Error: ' . $e->getMessage());
        }
    }
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
        return true;
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
     * Looks up a users information by their ID
     *
     * @param string $userId
     * @return unknown
     */
    public function getUserByUserId($userId)
    {
        try {
            $result = $this->_ldap->lookupByUserId($userId);
        } catch (Exception $e) {
            $this->_messages[] = $e->getMessage();
            return false;
        }

        if (!isset($result[0])) {
            $this->_messages[] = 'User ID not found';
            return false;
        }

        $data = array(
            'userId'          => $result[0]['uid'][0],
            'name'            => $result[0]['givenname'][0] . ' ' . ((isset($result[0]['ncsumiddlename'][0])) ? $result[0]['ncsumiddlename'][0] : '') . ' ' . $result[0]['sn'][0],
            'firstName'       => $result[0]['givenname'][0],
            'lastName'        => $result[0]['sn'][0],
            'middleName'      => (isset($result[0]['ncsumiddlename'][0])) ? $result[0]['ncsumiddlename'][0] : '',
            'cid'             => $result[0]['ncsucampusid'][0],
            'emailAddress'    => $result[0]['ncsuprimaryemail'][0],
            'telephoneNumber' => (isset($result[0]['telephonenumber'][0])) ? $result[0]['telephonenumber'][0] : '',
            );

        if ($data['name'] == '') {
            $data['name'] = $data['userId'];
        }

        return $data;
    }
}