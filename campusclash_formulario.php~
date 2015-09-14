<?php
require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/campusclash/lib.php');
 
class campusclash_formulario extends moodleform {
 
    function definition() {
 
        $mform =& $this->_form;
        // add group for text areas
	$mform->addElement('header','usernameheader', get_string('usernameheader', 'block_campusclash'));
 
	// add page title element.
	$mform->addElement('text', 'USERNAME', get_string('nombredeusuraio', 'block_campusclash'));
	$mform->addRule('USERNAME', null, 'required', null, 'client');
 
	// add Form Buttons
	$this->add_action_buttons();

	// add hidden elements.
	$mform->addElement('hidden', 'blockid');
	$mform->addElement('hidden', 'courseid');
	$mform->addElement('hidden','id','0');	
    }
}
?>
