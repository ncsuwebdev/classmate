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
 * @subpackage Instructor
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the instructors of events
 *
 * @package    Classmate
 * @subpackage Instructor
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class SearchTerm extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_search_term';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = array('term');
    
    public function increment($term)
    {
    	$term = trim($term);
    	
    	$result = $this->find($term);
    	
    	if (is_null($result)) {
    		$data = array(
    		  'term' => $term,
    		  'count' => 1,
    		  'last'  => time(),
    		);
    		
    		$this->insert($data);
    	} else {
    		$data = array(
    		  'term' => $term,
    		  'count' => $result->count + 1,
    		  'last' => time(),
    		);
    		
    		$this->update($data, null);
    	}
    }
    
    public function getTopSearchTerms($limit = 10)
    {
    	return $this->fetchAll(null, array('count DESC', 'last DESC'), $limit);
    }
}
