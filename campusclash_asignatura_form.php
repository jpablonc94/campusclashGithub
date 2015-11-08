<?php
require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/campusclash/lib.php');
 
class campusclash_asignatura_form extends moodleform {
 
    function definition() {
 
    $mform =& $this->_form;
    // add group for text areas
	$mform->addElement('header','asignaturaheader', get_string('asignaturaheader', 'block_campusclash'));
 
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

	$mform->addElement('text', 'nombreasignatura', get_string('nombreasignatura', 'block_campusclash'));
	$mform->addRule('nombreasignatura', null, 'required', null, 'client');

 
	// add Form Buttons
	$this->add_action_buttons();

	// add hidden elements.
	$mform->addElement('hidden', 'blockid');
	$mform->addElement('hidden', 'courseid');
	$mform->addElement('hidden','id','0');	
    }
}
?>
