<?php
// This file is part of CampusClash block for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * CampusClash block configuration form definition
 *
 * @package    campusclash
 * @subpackage block_campusclash
 * @authors    Juan Pablo Navarro Castillo
 */

/**

Este archivo define los elementos dentro de la edición del bloque. Es decir, lo que veremos cuando
entremos en el modo edición del curso y clickemos en la tuerca del bloque.

*/

 
class block_campusclash_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
 
        // A sample string variable with a default value.
        $mform->addElement('text', 'config_text', get_string('blockstring', 'block_campusclash'));
        $mform->setDefault('config_text', 'default value');
        $mform->setType('config_text', PARAM_RAW); 
	
	// A sample string variable with a default value.
    	$mform->addElement('text', 'config_title', get_string('blocktitle', 'block_campusclash'));
    	$mform->setDefault('config_title', 'default value');
    	$mform->setType('config_title', PARAM_TEXT);       
 
    }
}
