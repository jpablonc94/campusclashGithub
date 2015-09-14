<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext('block_campusclash/campusclashsize', get_string('campusclashsize', 'block_campusclash'),
        get_string('campusclashsize_help', 'block_campusclash'), 10, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_campusclash/resourcepoints', get_string('resourcepoints', 'block_campusclash'),
        '', 2, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_campusclash/assignpoints', get_string('assignpoints', 'block_campusclash'),
        '', 2, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_campusclash/forumpoints', get_string('forumpoints', 'block_campusclash'),
        '', 2, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_campusclash/pagepoints', get_string('pagepoints', 'block_campusclash'),
        '', 2, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_campusclash/workshoppoints', get_string('workshoppoints', 'block_campusclash'),
        '', 2, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_campusclash/defaultpoints', get_string('defaultpoints', 'block_campusclash'),
        '', 2, PARAM_INT));

    $settings->add(new admin_setting_configselect('block_campusclash/enable_multiple_quizz_attempts', get_string('enable_multiple_quizz_attempts', 'block_campusclash'),
        get_string('enable_multiple_quizz_attempts_help', 'block_campusclash'), '1', array('1' => get_string('yes', 'block_campusclash'), '0' => get_string('no', 'block_campusclash'))));
}
