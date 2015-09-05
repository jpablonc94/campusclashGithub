<?php
    defined('MOODLE_INTERNAL') || die();
 
    $capabilities = array(
 
    	'block/campusclash:viewpages' => array(
 
            'captype' => 'read',
            'contextlevel' => CONTEXT_COURSE,
            'legacy' => array(
                'guest' => CAP_PREVENT,
            	'student' => CAP_ALLOW,
            	'teacher' => CAP_ALLOW,
            	'editingteacher' => CAP_ALLOW,
            	'coursecreator' => CAP_ALLOW,
            	'manager' => CAP_ALLOW
            )
    	),
 
    	'block/campusclash:managepages' => array(
 
            'captype' => 'read',
            'contextlevel' => CONTEXT_COURSE,
            'legacy' => array(
            	'guest' => CAP_PREVENT,
            	'student' => CAP_PREVENT,
            	'teacher' => CAP_PREVENT,
            	'editingteacher' => CAP_ALLOW,
            	'coursecreator' => CAP_ALLOW,
            	'manager' => CAP_ALLOW
            )
    	)
    );
