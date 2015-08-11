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

defined('MOODLE_INTERNAL') || die();

class block_campusclash extends block_base {

    function init() {
        $this->title = get_string('campusclash', 'block_campusclash');
    }

    function get_content() {
   	if ($this->content !== NULL) {
            return $this->content;
    	}

    	$this->content = new stdClass;
    	$this->content->text = '¡Primeros pasos para crear el bloque!';
    	$this->content->footer = 'Proximamente... ¡¡MÁS!!';

    	return $this->content;
    }

    function instance_allow_config() {
    	return true;
    }
}

