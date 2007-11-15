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
 * http://itdapps.ncsu.edu/billboard_bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    
 * @subpackage Ot_Auth_Adapter_InterfaceLocal
 * @category   Authenticaiton Interface
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/billboard_bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: InterfaceLocal.php 42 2007-05-22 12:28:29Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Interface to build an Authentication Adapter that the app must use its
 * local auth interfaces for.  This should be used for interacting with an auth source
 * that can be managed locally (IE a database or LDAP source with admin privs).
 *
 * @package    Cyclone
 * @subpackage Ot_Auth_Adapter_InterfaceLocal
 * @category   Authentication Interface
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
interface Ot_Auth_Adapter_InterfaceLocal
{
	/**
	 * Tells the application whenter the user has an account or not
	 *
	 * @param int $userId
	 * @return boolean
	 */
    public function hasAccount($userId);

	/**
	 * Gets a user by ID from the system
	 *
	 * @param int $userId
	 * @return array with user_id, password, and email
	 */
    public function getUser($userId);

	/**
	 * Adds an account to the system
	 *
	 * @param int    $userId
	 * @param string $password
	 * @param string $email
	 */
    public function addAccount($userId, $password, $email);

	/**
	 * Edits an account in the system
	 *
	 * @param int    $userId
	 * @param string $password
	 * @param string $email
	 */
    public function editAccount($userId, $password, $email);

	/**
	 * Deletes an account from the system
	 *
	 * @param int $userId
	 */
    public function deleteAccount($userId);

    /**
	 * Resets a users password and emails them the new pass
	 *
	 * @param int $userId
	 */
    public function resetPassword($userId);

	/**
	 * Encrypts the password
	 *
	 * @param string $password
	 * @return string encypted password
	 */
    public function encryptPassword($password);

}