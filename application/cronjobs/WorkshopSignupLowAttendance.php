<?php
class App_Cronjob_WorkshopSignupLowAttendance implements Ot_Cron_JobInterface
{
    public function execute($lastRunDt = null)
    {
        $vr = new Ot_Var_Register();
        
        $event = new Event();
        
        $events = $event->getEvents(null, null, null, time(), null, 'open');
        
        $location = new Location();
        $workshop = new Workshop();
        $instructor = new Event_Instructor();
        
        $checkDt = new Zend_Date($this->_lastRunDt);
        $checkDt->addHour($vr->getVar('numHoursLowAttendanceNotification')->getValue());
        
        foreach ($events as $e) {
            
            if ($e->roleSize < $e->minSize) {
                
                $startDt = strtotime($e->date . ' ' . $e->startTime);
                $endDt   = strtotime($e->date . ' ' . $e->endTime);
        
                if ($checkDt->getTimestamp() > $startDt && $this->_lastRunDt  < $startDt) {
                
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
            
                    $trigger = new Ot_Trigger();
                    $trigger->setVariables($data);
                    
                    $trigger->dispatch('Event_LowAttendance');
                }
            }
        }
    }
}