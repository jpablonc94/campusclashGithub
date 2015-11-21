<?php

global $COURSE, $DB, $USER;

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/blocks/campusclash/lib.php');

define('DEFAULT_PAGE_SIZE', 100);

$courseid = required_param('courseid', PARAM_INT);
$profesorid = $USER->id;
$perpage = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$group = optional_param('group', null, PARAM_INT);
$action = optional_param('action', null, PARAM_ALPHA);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

require_login($courseid);
$context = context_course::instance($courseid);

// Some stuff.
$url = new moodle_url('/blocks/campusclash/report.php', array('courseid' => $courseid));
if ($action) {
    $url->param('action', $action);
}

// Page info.
$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_title($course->fullname.': CampusClash geral dos alunos');
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_url($url);


require_once 'connection.php';


$query = mysql_query("SELECT * FROM `compras_premios` WHERE `course_id` = '$courseid' AND `profesor_id`='$profesorid'"); 

$numrows=mysql_num_rows($query);

$resultado = "";

if($numrows!=0){
    $strcoursereport = get_string('report_head2', 'block_campusclash', count($students));

    $table = new html_table();
    $table->attributes = array("class" => "campusclashTable table table-striped generaltable");
    $table->head = array(
                        get_string('table_fecha', 'block_campusclash'),
                        get_string('table_name', 'block_campusclash'),
                        get_string('table_nombre_producto', 'block_campusclash'),
                        get_string('table_precio', 'block_campusclash'),
                        get_string('table_canjeado', 'block_campusclash'),
                    );
    while($row=mysql_fetch_assoc($query)){
        $id_canjeo = $row['id'];
        $nombre_premio=$row['nombre_premio'];
        $precio=$row['precio'];
        $timecreated= $row['timecreated'];
        $alumnoid= $row['user_id'];
        $iscanjeado= $row['canjeado'];

        if($iscanjeado){
            $style = "color:green;";
            $canjeado = "<div style='$style'><b>Canjeado</b></div>";
        } else {
            $href = "canjear.php?id=$id_canjeo";
            $canjeado = "<a href='$href'><button>Canjear</button></a>";
        }

        $student = array_values($DB->get_records('user', array('id'=>"$alumnoid")));

        $firstname = $student[0]->firstname;
        $lastname = $student[0]->lastname;

        $row = new html_table_row();

        $row->cells = array(
                        $timecreated,
                        $OUTPUT->user_picture($student[0], array('size' => 24, 'alttext' => false)) . ' '.$firstname. ' '.$lastname,
                        $nombre_premio,
                        $precio,
                        $canjeado
                    );
        $table->data[] = $row;
    }   
    $resultado =  html_writer::table($table);
} else {
    $strcoursereport = get_string('nopremios', 'block_campusclash');
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strcoursereport);
$PAGE->set_title($strcoursereport);

// Output group selector if there are groups in the course.
echo $OUTPUT->container_start('campusclash-report');

echo $resultado;

echo $OUTPUT->container_end();

echo $OUTPUT->footer();



