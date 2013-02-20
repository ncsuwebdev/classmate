<?php
class Bootstrap extends Ot_Application_Bootstrap_Bootstrap
{
    public function _initVars()
    {
        $config = array();
           
        $config[] = new Ot_Var_Type_Text('numHoursEvaluationAvailability', 'Evaluation Availability', 'The numsber of hours an evaluation is available after an event has finished.', '1000');
        $config[] = new Ot_Var_Type_Text('numHoursEventCancel', 'Cancel Reservation', 'The number of hours before an event that a user can cancel their reservation.', '1');
        $config[] = new Ot_Var_Type_Text('fileUploadPathWorkshop', 'Workshop Handout Path', 'Upload directory for workshop handouts.', '/usr/local/zend/apache2/htdocs/classmate/classmate-workshop-downloads');
        $config[] = new Ot_Var_Type_Multiselect('fileUploadAllowableExtensions', 'File Upload Extensions', 'The allowable extensions for files.', array('jpg','png', 'gif', 'pdf', 'doc', 'ppt', 'xls', 'html','txt'), array('jpg', 'pdf', 'doc', 'ppt', 'html', 'txt'));
        $config[] = new Ot_Var_Type_Text('defaultMinWorkshopSize', 'Default Minimum Workshop Size', 'Default minimum number of attendees for a workshop', '5');
        $config[] = new Ot_Var_Type_Text('defaultWorkshopWaitlistSize', 'Default Workshop Waitlist Size', 'Default waitlist size for a workshop', '10');
        $config[] = new Ot_Var_Type_Text('numHoursLowAttendanceNotification', 'Low Attendance Notification', 'The number of hours before a workshop is supposed to start to send a notification if the class size is less than the minimum requirement.', '24');
        $config[] = new Ot_Var_Type_Text('numHoursFirstReminder', 'First Reminder Notification', 'The number of hours before a workshop is supposed to start to send a first notification of class attendance.', '24');
        $config[] = new Ot_Var_Type_Text('numHoursFinalReminder', 'Final Reminder Notification', 'The number of hours before a workshop is supposed to start to send a final notification of class attendance.', '12');
        $config[] = new Ot_Var_Type_Text('numHoursEvaluationReminder', 'Evaluation Reminder Notification', 'The number of hours after a workshop is supposed to finish to send a reminder to users to fill out the class evaluation.  This only applies to users who have not filled out the evaluation already, but were marked as attended.', '24');
        
        $vr = new Ot_Var_Register();
        $vr->registerVars($config, 'Classmate Settings');
    }    
    
    public function _initCron()
    {        
        $register = new Ot_Cron_Register();
        
        $workshopEvaluationNotification = new Ot_Cron('WorkshopEvaluationNotification', 'Sends notification that the workshop evaluations are available', '0 2 * * *');
        $workshopEvaluationNotification->setMethod(new App_Cronjob_WorkshopEvaluationNotification());
        $register->registerCronjob($workshopEvaluationNotification);
        
        $workshopEvaluationReminder = new Ot_Cron('WorkshopEvaluationReminder', 'Sends reminders to the people who have not filled out an evaluation yet', '0 2 * * *');
        $workshopEvaluationReminder->setMethod(new App_Cronjob_WorkshopEvaluationReminder());
        $register->registerCronjob($workshopEvaluationReminder);
        
        $workshopSignupLowAttendance = new Ot_Cron('WorkshopSignupLowAttendance', 'Sends notification to the instructor that the attendance for an event is low', '0 2 * * *');
        $workshopSignupLowAttendance->setMethod(new App_Cronjob_WorkshopSignupLowAttendance());
        $register->registerCronjob($workshopSignupLowAttendance);
        
        $workshopSignupReminder = new Ot_Cron('WorkshopSignupReminder', 'Sends reminder that a person has signed up for a workshop', '0 2 * * *');
        $workshopSignupReminder->setMethod(new App_Cronjob_WorkshopSignupReminder());
        $register->registerCronjob($workshopSignupReminder);
    }
    
    public function _initTriggers()
    {
        $triggers = array();
        
        $trigger = new Ot_Trigger('Login_Index_Forgot', 'When a user has forgotten their password, they ask for a reset email to be sent to their registered email address.');
        $trigger->addOption("firstName", "First name of the user")
                ->addOption("lastName", "Last name of the user.")
                ->addOption("emailAddress", "Email address of the user.")
                ->addOption("username", "Username of user.")
                ->addOption("loginMethod", "Name of login method which they use to log into the system with.")
                ->addOption("resetUrl", "URL the user will need to go to to reset their password.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Instructor_Promote_User_Waitlist_To_Attending', 'When an instructor promotes a waitlisted user to the attendee roster.');        
        $trigger->addOption("studentFirstName", "First name of the user being promoted")
                ->addOption("studentLastName", "Last name of the user being promoted")
                ->addOption("studentEmail", "Email address of the user being promoted")
                ->addOption("studentUsername", "Username of user being promoted")
                ->addOption("studentAccountId", "The account ID of the user being promoted")
                ->addOption("instructorNames", "A comma separated list of the names of the instructors of the event")
                ->addOption("instructorEmails", "A comma separated list of the instructor email addresses")
                ->addOption("locationName", "The name of the location of the event")
                ->addOption("locationAddress", "The address of the location of the event")
                ->addOption("workshopMinimumEnrollment", "The minimum number of people that have to sign up for the event")
                ->addOption("workshopName", "The title of the workshop")
                ->addOption("workshopDate", "The date of the event")
                ->addOption("workshopStartTime", "The start time of the event")
                ->addOption("workshopEndTime", "The end time of the event")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Instructor_Registered_User', 'When an instructor adds a user to the attendee roster.');      
        $trigger->addOption("studentFirstName", "First name of the user")
                ->addOption("studentLastName", "Last name of the user")
                ->addOption("studentEmail", "Email address of the user")
                ->addOption("studentUsername", "Username of user")
                ->addOption("studentAccountId", "The account ID of the user")
                ->addOption("instructorNames", "A comma separated list of the names of the instructors of the event")
                ->addOption("instructorEmails", "A comma separated list of the instructor email addresses")
                ->addOption("locationName", "The name of the location of the event")
                ->addOption("locationAddress", "The address of the location of the event")
                ->addOption("workshopMinimumEnrollment", "The minimum number of people that have to sign up for the event")
                ->addOption("workshopName", "The title of the workshop")
                ->addOption("workshopDate", "The date of the event")
                ->addOption("workshopStartTime", "The start time of the event")
                ->addOption("workshopEndTime", "The end time of the event")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Instructor_Registered_User_For_Waitlist', 'When an instructor adds a user to the waitlist.');
        $trigger->addOption("studentFirstName", "First name of the user")
                ->addOption("studentLastName", "Last name of the user")
                ->addOption("studentEmail", "Email address of the user")
                ->addOption("studentUsername", "Username of user")
                ->addOption("studentAccountId", "The account ID of the user")
                ->addOption("instructorNames", "A comma separated list of the names of the instructors of the event")
                ->addOption("instructorEmails", "A comma separated list of the instructor email addresses")
                ->addOption("locationName", "The name of the location of the event")
                ->addOption("locationAddress", "The address of the location of the event")
                ->addOption("workshopMinimumEnrollment", "The minimum number of people that have to sign up for the event")
                ->addOption("workshopName", "The title of the workshop")
                ->addOption("workshopDate", "The date of the event")
                ->addOption("workshopStartTime", "The start time of the event")
                ->addOption("workshopEndTime", "The end time of the event")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Instructor_Cancels_Users_Reservation', 'When an instructor cancels a users reservation.');
        $trigger->addOption("studentFirstName", "First name of the user")
                ->addOption("studentLastName", "Last name of the user")
                ->addOption("studentEmail", "Email address of the user")
                ->addOption("studentUsername", "Username of user")
                ->addOption("studentAccountId", "The account ID of the user")
                ->addOption("instructorNames", "A comma separated list of the names of the instructors of the event")
                ->addOption("instructorEmails", "A comma separated list of the instructor email addresses")
                ->addOption("locationName", "The name of the location of the event")
                ->addOption("locationAddress", "The address of the location of the event")
                ->addOption("workshopMinimumEnrollment", "The minimum number of people that have to sign up for the event")
                ->addOption("workshopName", "The title of the workshop")
                ->addOption("workshopDate", "The date of the event")
                ->addOption("workshopStartTime", "The start time of the event")
                ->addOption("workshopEndTime", "The end time of the event")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('User_Automatically_Moved_From_Waitlist_To_Attending', 'When a user is automatically moved from the waitlist to the attendee roster.');       
        $trigger->addOption("studentFirstName", "First name of the user")
                ->addOption("studentLastName", "Last name of the user")
                ->addOption("studentEmail", "Email address of the user")
                ->addOption("studentUsername", "Username of user")
                ->addOption("studentAccountId", "The account ID of the user")
                ->addOption("instructorNames", "A comma separated list of the names of the instructors of the event")
                ->addOption("instructorEmails", "A comma separated list of the instructor email addresses")
                ->addOption("locationName", "The name of the location of the event")
                ->addOption("locationAddress", "The address of the location of the event")
                ->addOption("workshopMinimumEnrollment", "The minimum number of people that have to sign up for the event")
                ->addOption("workshopName", "The title of the workshop")
                ->addOption("workshopDate", "The date of the event")
                ->addOption("workshopStartTime", "The start time of the event")
                ->addOption("workshopEndTime", "The end time of the event")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Attendee_Final_Reminder', 'Triggered (via cron) to send final reminder to attendees on the class role');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("userId", "user ID (userID@loginType) of the user who signed up for the workshop.")
                ->addOption("workshopCurrentEnrollment", "Current enrollment for the workshop")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Attendee_First_Reminder', 'Triggered (via cron) to send first reminder to attendees on the class role');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("userId", "user ID (userID@loginType) of the user who signed up for the workshop.")
                ->addOption("workshopCurrentEnrollment", "Current enrollment for the workshop")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Cancel_Reservation', 'Fires when someone cancels their reservation');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("studentUsername", "Username of user")
                ->addOption("userId", "user ID (userID@loginType) of the user who signed up for the workshop.")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Evaluation_Notification', 'When an event is over, sends an email to attendees to evaluate the event.');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("studentUsername", "Username of user")
                ->addOption("workshopCurrentEnrollment", "Current enrollment for the workshop")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Evaluation_Reminder', 'Triggered (via cron) to remind attendees who have not filled out evaluation forms to do so.');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("studentUsername", "Username of user")
                ->addOption("workshopCurrentEnrollment", "Current enrollment for the workshop")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Instructor_Final_Reminder', 'Triggered (via cron) to send final reminder to instructors of a workshop');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("workshopCurrentEnrollment", "Current enrollment for the workshop")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Instructor_First_Reminder', 'Triggered (via cron) to send first reminder to instructors of a workshop');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("workshopCurrentEnrollment", "Current enrollment for the workshop")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_LowAttendance', 'Triggered (via cron) when an event has low attendance.');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("workshopCurrentEnrollment", "Current enrollment for the workshop")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Signup', 'Fires when someone signs up for an event');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("studentUsername", "Username of the user who signed up for the workshop.")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Signup_Full', 'Fires when an event has been filled');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("studentUsername", "Username of the user who signed up for the workshop.")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;

        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Signup_Waitlist', 'Fired when a user signs up for a class but are put on the waitlist.');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("studentUsername", "Username of the user who signed up for the workshop.")
                ->addOption("waitlistPosition", "Position on the waitlist that the user is in.")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMiminumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
        $triggers[] = $trigger;
        
        $trigger = new Ot_Trigger('Event_Waitlist_To_Attending', 'Fired when a user advances from the waitlist to the class attendance');
	$trigger->addOption("instructorEmails", "Comma separated list of instructor email addresses")
                ->addOption("instructorNames", "Comma separated list of instructor names")
                ->addOption("locationAddress", "Address of the location where the workshop will be taught")
                ->addOption("locationName", "Name of the location where the workshop will be taught.")
                ->addOption("studentEmail", "Email address of the user who signed up for the workshop.")
                ->addOption("studentName", "Name of the user who signed up for the workshop.")
                ->addOption("studentUsername", "Username of the user who signed up for the workshop.")
                ->addOption("workshopDate", "Date (mm/dd/yyyy) when the workshop occurs.")
                ->addOption("workshopEndTime", "End time (hh:mm am/pm) of the workshop.")
                ->addOption("workshopMinimumEnrollment", "Minimum enrollment for the workshop")
                ->addOption("workshopName", "Name of the workshop the user signed up for")
                ->addOption("workshopStartTime", "Start time (hh:mm am/pm) the workshop occurs.")
                ;
                
                
        $register = new Ot_Trigger_Register();        
        $register->registerTriggers($triggers);
    }    
}