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
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    
 * @subpackage WorkshopLink
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Bug.php 156 2007-07-20 12:57:10Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Model to do deal with workshop links
 *
 * @package    
 * @subpackage WorkshopLink
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 *
 */
class WorkshopLink extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_workshop_link';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'workshopLinkId';
    
    public function getLinksForWorkshop($workshopId)
    {
    	$where = $this->getAdapter()->quoteInto('workshopId = ?', $workshopId);
    	return $this->fetchAll($where, 'name');
    }
    
    public function deleteWorkshopLink($workshopLinkId)
    {
        $where = $this->getAdapter()->quoteInto('workshopLinkId = ?', $workshopLinkId);

        $this->delete($where);
    }    
}
?>
