<?php
class App_Cronjob_WorkshopSignupReminder implements Ot_Cron_JobInterface
{
    public function execute($lastRunDt = null)
    {
        $config = Zend_Registry::get('config');

        $event = new Event();
        
        $events = $event->getEvents(null, null, null, time(), null, 'open');
        
        $location   = new Location();
        $workshop   = new Workshop();
        $instructor = new Event_Instructor();
        $attendees  = new Event_Attendee();
        
        $lastRunDt = new Zend_Date($this->_lastRunDt);
        $currentDt = new Zend_Date();

        foreach ($events as $e) {
            
            $startDt = strtotime($e->date . ' ' . $e->startTime);
            $endDt   = strtotime($e->date . ' ' . $e->endTime);
            
            $firstDt = new Zend_Date($startDt);
            $firstDt->subHour($config->user->numHoursFirstReminder->val);
            
            $finalDt = new Zend_Date($startDt);
            $finalDt->subHour($config->user->numHoursFinalReminder->val);
            
            $notification = null;
            
            if ($firstDt->getTimestamp() > $lastRunDt->getTimestamp() && $firstDt->getTimestamp() < $currentDt->getTimestamp()) {
                $notification = 'first';    
            }
            
            if ($finalDt->getTimestamp() > $lastRunDt->getTimestamp() && $finalDt->getTimestamp() < $currentDt->getTimestamp()) {
                $notification = 'final';    
            }
            
            if (!is_null($notification)) {
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
                
                $attending = $attendees->getAttendeesForEvent($e->eventId, 'attending');
                
                foreach ($attending as $a) {
                    $trigger = new Ot_Trigger();
                    $trigger->setVariables($data);
                    $trigger->accountId = $a['accountId'];
                    $trigger->studentEmail = $a['emailAddress'];
                    $trigger->studentName = $a['firstName'] . ' ' . $a['lastName'];
                             
                    if ($notification == 'final') {
                        $trigger->dispatch('Event_Attendee_Final_Reminder');
                    } else {
                        $trigger->dispatch('Event_Attendee_First_Reminder');
                    }   
                }    
                
                $trigger = new Ot_Trigger();
                $trigger->setVariables($data);
                
                if ($notification == 'final') {
                    $trigger->dispatch('Event_Instructor_Final_Reminder');
                } else {
                    $trigger->dispatch('Event_Instructor_First_Reminder');
                }
            }
        }            
    }
}