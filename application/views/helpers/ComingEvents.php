<?php

/**
 * This view helper returns a value, or a default value if the value is empty
 *
 */
class Zend_View_Helper_ComingEvents extends Zend_View_Helper_Abstract
{   
    /**
     * Checks if the passed $val is empty or not.  If it is not,
     * it returns the $val.  If it is, it returns the translation
     * of $alt;
     *
     * @param string $val
     * @param string $alt
     * @return string
     */
    public function comingEvents($events, $limit = null)
    {
        if (!is_null($limit)) {
            $events = array_slice($events, 0, $limit);
        }
        
        $this->view->events = $events;
        
        return $this->view->render('coming-events.phtml');
    }
}