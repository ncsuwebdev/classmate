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
 * @subpackage Evaluation
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the evaluations
 *
 * @package    Classmate
 * @subpackage Evaluation
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Evaluation extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_evaluation';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'evaluationId';
    
    public function saveEvaluation($eventId, $userId, $customAttributes)
    {
        
        $dba = $this->getAdapter();
        
        $inTransaction = false;
        
        try {
            $dba->beginTransaction();
        } catch (Exception $e) {
            $inTransaction = true;
        }
        
        $data = array('eventId' => $eventId,
                      'timestamp' => time()
                     );

        try {
            $evaluationId = $this->insert($data);
        } catch(Exception $e) {
            if (!$inTransaction) {
                $dba->rollBack();
            }
            throw $e;
        }
        
        $evalUser = new EvaluationUser();
        
        $data = array('eventId' => $eventId,
                      'timestamp' => time()
                     );
        
        $data = array('eventId' => $eventId,
                      'userId'  => $userId
                     );
                     
        try {
            $result = $evalUser->insert($data);
        } catch (Exception $e) {
            if (!$inTransaction) {
                $dba->rollBack();
            }
            throw $e;
        }
        
        $ca = new CustomAttribute();
        
        try {
            $ca->saveData('evaluations', $evaluationId, $customAttributes);
        } catch (Exception $e) {
            if (!$inTransaction) {
                $dba->rollBack();
            }
            throw $e;    
        }
        
        if (!$inTransaction) {
            $dba->commit();
        }
    }
}