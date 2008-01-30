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
 * @subpackage Profile
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with user profiles
 *
 * @package    Classmate
 * @subpackage Profile
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Profile extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_profile';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'userId';
    
    public function addProfile($userId)
    {
    	$profile = null;
    	
    	$uc = Zend_Registry::get('userConfig');
    	
    	while (is_null($profile)) {
    	    $profile = parent::find($userId);
    	    
    	    if (!is_null($profile)) {
    	    	return true;
    	    }
    	    
            $data = array(
                'userId'         => $userId,
                'availableVotes' => $uc['profileInitialVotes']['value'],
            );

            parent::insert($data);
    	}
    	
    	return null;
    }
}