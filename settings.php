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

Este archivo define los parámetros de confguración del bloque. Es decir, lo que veremos cuando nos metamos en el bloque de administración y queramos configurar el bloque.

*/

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
$settings->add(new admin_setting_heading(
            'headerconfig',
            get_string('headerconfig', 'block_campusclash'),
            get_string('descconfig', 'block_campusclash')
        ));
 
$settings->add(new admin_setting_configcheckbox(
            'campusclash/Allow_HTML',
            get_string('labelallowhtml', 'block_campusclash'),
            get_string('descallowhtml', 'block_campusclash'),
            '0'
        ));
}
