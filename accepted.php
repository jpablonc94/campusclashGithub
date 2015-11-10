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
    $database = "campusclash";
    $db_pass = 'T7tmn892AB3';
    $db_user = 'root';

    mysql_connect($server, $db_user, $db_pass) or die ("error1".mysql_error());

    mysql_select_db($database) or die ("error2".mysql_error());
    $userid = $USER->id;
    $points = 0;
    $experiencia = 0;
    $monedas = 5;
    $timecreated = time();
    $fullname = $fromform->FULLNAME;
    $username = $fromform->USERNAME;
    $email = $fromform->EMAIL;
    $password = $fromform->PASSWORD;
    $isstudent = true;
    $style="color:red;";

    $nuevo_usuario = mysql_query("SELECT `username` FROM `usertbl` WHERE `username`='$username'");
    $nuevo_profesor = mysql_query("SELECT `username` FROM `profesores` WHERE `username`='$username'");
    $nuevo_vendedor = mysql_query("SELECT `username` FROM `vendedores` WHERE `username`='$username'");
    
    if(mysql_num_rows($nuevo_usuario)>0 || mysql_num_rows($nuevo_profesor)>0 || mysql_num_rows($nuevo_vendedor)>0)
    {
        $site = get_site();
        echo $OUTPUT->header();
        echo "<p class='avisos' style=$style>El nombre de usuario ya existe, prueba con otro.</p>";
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
    // ------------ Si no esta registrado el usuario continua el script
    else
    {
        // ==============================================
        // Comprobamos si el email esta registrado
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){

            $nuevo_email=mysql_query("SELECT `email` FROM `usertbl` WHERE `email`='$email'");
            $nuevo_email2=mysql_query("SELECT `email` FROM `profesores` WHERE `email`='$email'");
            $nuevo_email3=mysql_query("SELECT `email` FROM `vendedores` WHERE `email`='$email'");

            if(mysql_num_rows($nuevo_email)>0 || mysql_num_rows($nuevo_email2)>0 || mysql_num_rows($nuevo_email3)>0) {
                $site = get_site();
                echo $OUTPUT->header();
                echo "<p class='avisos' style=$style>El email ya existe, prueba con otro.</p>";
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
            } else { // ------------ Si no esta registrado el e-mail continua el script

                mysql_query ("INSERT INTO `usertbl`(`moodle_id`, `full_name`, `email`, `username`, `password`,`points`,`monedas`,`experiencia`, `timecreated`) 
                            VALUES ('".$userid."', '".$fullname."', '".$email."', '".$username."', '".$password."', '".$points."', '".$monedas."', '".$experiencia."', '".$timecreated."')");
                            redirect($courseurl);
            }
        } else {
            $site = get_site();
            echo $OUTPUT->header();
            echo "<p class='avisos' style=$style>El formato del email no es correcto, debe seguir la siguiente estructura: example@example.example</p>";
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
    }
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
