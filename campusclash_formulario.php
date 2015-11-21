<?php
require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/campusclash/lib.php');
 
class campusclash_formulario extends moodleform {
 
    function definition() {
 
    $mform =& $this->_form;
    // add group for text areas
	$mform->addElement('header','usernameheader', get_string('usernameheader', 'block_campusclash'));
 
	$options = array(
    	'Universidad Politécnica de Cartagena' => 'Universidad Politécnica de Cartagena'
	);

	$mform->addElement('select', 'universidades', get_string('universidades'), $options);
	$mform->addRule('universidades', null, 'required', null, 'client');


	$options = array(
    	'Grado en Ingeniería de Sistemas de Telecomunicaciones' => 'Grado en Ingeniería de Sistemas de Telecomunicaciones',
    	'Grado en Ingeniería Telemática' => 'Grado en Ingeniería Telemática'
	);

	$mform->addElement('select', 'grados', get_string('grados'), $options);
	$mform->addRule('grados', null, 'required', null, 'client');

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
