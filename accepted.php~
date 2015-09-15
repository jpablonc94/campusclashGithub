<?php
 
require_once('../../config.php');
require_once('campusclash_formulario.php');
 
global $DB, $OUTPUT, $PAGE, $USER;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT); 
$blockid = required_param('blockid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$viewpage = optional_param('viewpage', false, PARAM_BOOL);
 
// Next look for optional variables.
$id = optional_param('id', 0, PARAM_INT); 

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_campusclash', $courseid);
}

//Checks that the user is logged in 
require_login($course);
//ensure that users have access to only those portions of the application that they should
require_capability('block/campusclash:viewpages', context_course::instance($courseid));
$PAGE->set_url('/blocks/campusclash/accepted.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_campusclash'));

//Esto aparecerá dentro del bloque de administración
$settingsnode = $PAGE->settingsnav->add(get_string('campusclashsettings', 'block_campusclash'));
$editurl = new moodle_url('/blocks/campusclash/accepted.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('editpage', 'block_campusclash'), $editurl);
$editnode->make_active();
 
$campusclash = new campusclash_formulario();

$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$toform['id'] = $id;
$campusclash->set_data($toform);

//Comprobaciones en caso de que se cancele/acepte la información introducida en los campos
if($campusclash->is_cancelled()) {

    // Cancelled forms redirect to the course main page.
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);

} else if ($fromform = $campusclash->get_data()) {

    // We need to add code to appropriately act on and store the submitted data
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    // We need to add code to appropriately act on and store the submitted data

    $server="localhost";
    $database = "moodle18";
    $db_pass = 'T7tmn892AB3';
    $db_user = 'root';

    mysql_connect($server, $db_user, $db_pass) or die ("error1".mysql_error());

    mysql_select_db($database) or die ("error2".mysql_error());
    $userid = $USER->id;
    $points = 0;
    $timecreated = time();
    mysql_query ("INSERT INTO `prueba`(`USERID`, `USERNAME`, `POINTS`, `TIMECREATED`) VALUES ($userid, '".$fromform->USERNAME."', $points ,$timecreated)");
    redirect($courseurl);
    
} else {
    
   // form didn't validate or this is the first display
   $site = get_site();
   echo $OUTPUT->header();
   if ($id) {
    	$campusclashpage = $DB->get_record('block_campusclash', array('id' => $id));
    	if($viewpage) {
            block_campusclash_print_page($campusclashpage);
    	} else {
            $campusclash->set_data($campusclashpage);
            $campusclash->display();
    	}
    } else {
    	$campusclash->display();
    }
    echo $OUTPUT->footer();

}

 
?>
