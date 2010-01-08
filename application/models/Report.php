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
 * @package    Report
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to generate reports
 *
 * @package    Report
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Report
{
    
    public function getReportCsv($fromDate, $toDate)
    {        
        $event = new Event();
        $workshop = new Workshop();
        $location = new Location();
        $attendee = new Event_Attendee();
        
        // go ahead and get all the workshops so we don't have to do loads of DB queries
        $workshopList = $workshop->fetchAll();
        $workshops = array();
        
        foreach ($workshopList as $w) {
            $workshops[$w->workshopId] = $w->toArray();
        }
        
        // go ahead and get all the locations so we don't have to do loads of DB queries
        $locationList = $location->fetchAll();
        $locations = array();
        
        foreach ($locationList as $l) {
            $locations[$l->locationId] = $l->toArray();
        }        
        
        $events = $event->getEvents(null, null, null, $fromDate, $toDate)->toArray();
        
        $fileName = 'report-' . date('Ymd-B') . '.csv';
        $tmpName = tempnam('/tmp', $fileName);
        $fp = fopen($tmpName, 'w');

        $columnNames = array(
                          'eventId'       => 'eventId',
                          'workshopId'    => 'workshopId',
                          'workshopTitle' => 'workshopTitle',
                          'locationId'    => 'locationId',
                          'locationName'  => 'locationName',
                          'eventDate'     => 'eventDate',
                          'startTime'     => 'startTime',
                          'endTime'       => 'endTime',
                          'accountId'     => 'accountId',
                          'username'      => 'username',
                          'firstName'     => 'firstName',
                          'lastName'      => 'lastName',
                          'status'        => 'status',
                          'attended'      => 'attended'
                       );
        
        $ret = fputcsv($fp, $columnNames, ',', '"');
        
        if ($ret === false) {
            throw new Ot_Exception_Data('Error writing backup CSV file');
        }
        
        foreach ($events as &$e) {
            $e['workshop'] = $workshops[$e['workshopId']];
            $e['location'] = $locations[$e['locationId']];
            $e['attendees'] = $attendee->getAttendeesForEvent($e['eventId']);

            foreach ($e['attendees'] as $a) {
                $data = array();
                $data = array(
                            'eventId'       => $e['eventId'],
                            'workshopId'    => $e['workshopId'],
                            'workshopTitle' => $e['workshop']['title'], 
                            'locationId'    => $e['locationId'],
                            'locationName'  => $e['location']['name'],
                            'eventDate'     => $e['date'],
                            'startTime'     => $e['startTime'],
                            'endTime'       => $e['endTime'],
                            'accountId'     => $a['accountId'],
                            'username'      => $a['username'],
                            'firstName'     => $a['firstName'],
                            'lastName'      => $a['lastName'],
                            'status'        => $a['status'],
                            'attended'      => $a['attended']
                        );

                $ret = fputcsv($fp, $data, ',', '"');
            
                if ($ret === false) {
                    throw new Ot_Exception_Data('Error writing backup CSV file');
                }
            }
        }
        
        fclose($fp);
        
        file_get_contents($tmpName);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($tmpName));
        header("Content-Disposition: attachment; filename=$fileName");
        readfile($tmpName);
        unlink($tmpName);
    }
    
    public function form($values = array())
    {        
        $form = new Zend_Form();
        $form->setAttrib('id', 'reportingForm')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));
             
        $fromDate = $form->createElement('text', 'fromDate', array('label' => 'reporting-index-index:fromDate'));
        $fromDate->setRequired(true)
                  ->addFilter('StringTrim')
                  ->addFilter('StripTags')
                  ->setValue((isset($values['fromDate']) ? $values['fromDate'] : ''));

        $toDate = $form->createElement('text', 'toDate', array('label' => 'reporting-index-index:toDate'));
        $toDate->setRequired(true)
               ->addFilter('StringTrim')
               ->addFilter('StripTags')
               ->setValue((isset($values['toDate']) ? $values['toDate'] : strftime('%B %e, %Y', time())));
                  
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'reporting-index-index:getReport'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));
        
        $form->addElements(array($fromDate, $toDate));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit));

        return $form;
    }
}
