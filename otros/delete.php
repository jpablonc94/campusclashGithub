<?php
require_once('../../config.php');


$courseid = required_param('courseid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

//Checks the courseid corresponds to a valid course 
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_campusclash', $courseid);
}

//Checks that the user is logged in 
require_login($course);
//ensure that users have access to only those portions of the application that they should
require_capability('block/campusclash:managepages', context_course::instance($courseid));

//Checks that the campusClash page id corresponds to an existing page id 
if(! $campusclashpage = $DB->get_record('block_campusclash', array('id' => $id))) {
    print_error('nopage', 'block_campusclash', '', $id);
}

//Prints out the header and footer for the page  
$site = get_site();
$PAGE->set_url('/blocks/campusclash/view.php', array('id' => $id, 'courseid' => $courseid));
$heading = $site->fullname . ' :: ' . $course->shortname . ' :: ' . $campusclashpage->pagetitle;
$PAGE->set_heading($heading);

if (!$confirm) {
    $optionsno = new moodle_url('/course/view.php', array('id' => $courseid));
    $optionsyes = new moodle_url('/blocks/campusclash/delete.php', array('id' => $id, 'courseid' => $courseid, 'confirm' => 1, 'sesskey' => sesskey()));
    // YES/NO Option
    echo $OUTPUT->confirm(get_string('deletepage', 'block_campusclash', $campusclashpage->pagetitle), $optionsyes, $optionsno);
} else {
    if (confirm_sesskey()) {
	//Lo que borra el dato
        if (!$DB->delete_records('block_campusclash', array('id' => $id))) {
            print_error('deleteerror', 'block_campusclash');
        }
    } else {
        print_error('sessionerror', 'block_campusclash');
    }
    $url = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($url);
}

echo $OUTPUT->header();
echo $OUTPUT->footer();
