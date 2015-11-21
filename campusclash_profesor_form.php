<?php
require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/campusclash/lib.php');
 
class campusclash_profesor_form extends moodleform {
 
    function definition() {
 
    $mform =& $this->_form;
    // add group for text areas
	$mform->addElement('header','profesorheader', get_string('profesorheader', 'block_campusclash'));
 
	$mform->addElement('text', 'FULLNAME', get_string('nombrecompleto', 'block_campusclash'));
	$mform->addRule('FULLNAME', null, 'required', null, 'client');

	// add username element.
	$mform->addElement('text', 'USERNAME', get_string('nombredeusuraio', 'block_campusclash'));
	$mform->addRule('USERNAME', null, 'required', null, 'client');

	// add a email
	$mform->addElement('text', 'EMAIL', get_string('emaildeusuraio', 'block_campusclash'));
	$mform->addRule('EMAIL', null, 'required', null, 'client');
	
 	// add a password
	$mform->addElement('password', 'PASSWORD', get_string('passdeusuraio', 'block_campusclash'));
	$mform->addRule('PASSWORD', null, 'required', null, 'client');

	// add a password
	$mform->addElement('password', 'PASSWORD2', get_string('repite_passdeusuraio', 'block_campusclash'));
	$mform->addRule('PASSWORD', null, 'required', null, 'client');

 
	// add Form Buttons
	$this->add_action_buttons();

	// add hidden elements.
	$mform->addElement('hidden', 'blockid');
	$mform->addElement('hidden', 'courseid');
	$mform->addElement('hidden','id','0');	
    }
}
?>
