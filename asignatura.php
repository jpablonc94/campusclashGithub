<?php
 
require_once('../../config.php');
require_once('campusclash_asignatura_form.php');
 
global $DB, $OUTPUT, $PAGE, $USER, $COURSE;

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
$PAGE->set_url('/blocks/campusclash/asignatura.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_campusclash'));

//Esto aparecerá dentro del bloque de administración
$settingsnode = $PAGE->settingsnav->add(get_string('campusclashsettings', 'block_campusclash'));
$editurl = new moodle_url('/blocks/campusclash/asignatura.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('editpage', 'block_campusclash'), $editurl);
$editnode->make_active();
 
$campusclash = new campusclash_asignatura_form();

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

    require_once 'connection.php';

    $profesorid = $USER->id;
    $timecreated = time();
    $universidad = $fromform->universidades;
    $grado = $fromform->grados;
    $nombre = $COURSE->fullname;
    $style="color:red;";

    $query =mysql_query("SELECT `username` FROM `profesores` WHERE `moodle_id`='".$profesorid."'");
 
    $numrows=mysql_num_rows($query);

    if($numrows!=0){
        $row=mysql_fetch_assoc($query);
        $username = $row['username'];
        mysql_query ("INSERT INTO `asignaturas`(`profesor_id`, `username`, `course_id`, `universidad`, `grado`, `nombre_asignatura`, `timecreated`) VALUES ('".$profesorid."', '".$username."', '".$courseid."', '".$universidad."', '".$grado."', '".$nombre."', '".$timecreated."')");
    }

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
