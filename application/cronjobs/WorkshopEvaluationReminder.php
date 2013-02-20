<?php
class App_Cronjob_WorkshopEvaluationReminder implements Ot_Cron_JobInterface
{
    public function execute($lastRunDt = null)
    {
        $vr = new Ot_Var_Register();
        
        $checkDtStart = new Zend_Date($this->_lastRunDt);
        $checkDtStart->subHour($vr->getVar('numHoursEvaluationReminder')->getValue());
        
        $checkDtEnd = new Zend_Date();
        $checkDtEnd->subHour($vr->getVar('numHoursEvaluationReminder')->getValue());
        
        $event = new App_Model_DbTable_Event();
        
        $events = $event->getEvents(null, null, null, $checkDtStart->getTimestamp(), $checkDtEnd->getTimestamp(), 'open');
        
        $location   = new App_Model_DbTable_Location();
        $workshop   = new App_Model_DbTable_Workshop();
        $instructor = new App_Model_DbTable_EventInstructor();
        $attendee   = new App_Model_DbTable_EventAttendee();
        $eu         = new App_Model_DbTable_EvaluationUser();
        
        foreach ($events as $e) {
            
            $startDt = strtotime($e->date . ' ' . $e->startTime);
            $endDt   = strtotime($e->date . ' ' . $e->endTime);
            
            if ($checkDtStart->getTimestamp() < $endDt && $checkDtEnd->getTimestamp() >= $endDt) {
                
                
            
                $evalAvailableDt = new Zend_Date($endDt);
                $evalAvailableDt->addHour($vr->getVar('numHoursEvaluationAvailability')->getValue());
                
                if ($evalAvailableDt->getTimestamp() > time()) {
                    
                    $taken = $eu->getCompleted($e->eventId);    
                    
                    $thisLocation = $location->find($e->locationId);
                    if (is_null($thisLocation)) {
                        throw new Ot_Exception_Data('msg-error-noLocation');
                    }
                    
                    $thisWorkshop = $workshop->find($e->workshopId);        
                    if (is_null($thisWorkshop)) {
                        throw new Ot_Exception_Data('msg-error-noWorkshop');
                    }
                                    
                    $instructors = $instructor->getInstructorsForEvent($e->eventId);
                                    
                    $instructorNames = array();
                    $instructorEmails = array();
                            
                    foreach ($instructors as $i) {
                        $instructorNames[] = $i['firstName'] . ' ' . $i['lastName'];
                        $instructorEmails[] = $i['emailAddress'];
                    }       
                            
                    $data = array(
                        'workshopName'              => $thisWorkshop->title,
                        'workshopDate'              => date('m/d/Y', $startDt),
                        'workshopStartTime'         => date('g:i a', $startDt),
                        'workshopEndTime'           => date('g:i a', $endDt),
                        'workshopMinimumEnrollment' => $e->minSize,
                        'workshopCurrentEnrollment' => $e->roleSize,
                        'locationName'              => $thisLocation->name,
                        'locationAddress'           => $thisLocation->address,
                        'instructorNames'           => implode(', ', $instructorNames),
                        'instructorEmails'          => implode(', ', $instructorEmails),
                    );      
                        
                    $attending = $attendee->getAttendeesForEvent($e->eventId, 'attending');
    
                    foreach ($attending as $a) {
                        if ($a['attended'] == 1 && !in_array($a['accountId'], $taken)) {
                            
                            $trigger = new Ot_Trigger();
                            $trigger->setVariables($data);
                            
                            $trigger->accountId   = $a['accountId'];
                            $trigger->studentEmail = $a['emailAddress'];
                            $trigger->studentName  = $a['firstName'] . ' ' . $a['lastName'];
                            $trigger->studentUsername = $a['username'];
                            
                            $trigger->dispatch('Event_Evaluation_Reminder');
                            
                        }    
                    }
                }
            }
        }        
    }
}