<?php
require_once('../../config.php');
 
global $DB, $OUTPUT, $PAGE, $USER, $COURSE;

if(isset($_GET['id'])){
    if ($_GET['id'] > 0){
    	$id = $_GET['id'];

        require_once 'connection.php';

		$query = mysql_query("SELECT * FROM `compras_premios` WHERE `id`= '$id'"); 

		$row=mysql_fetch_assoc($query);
		$courseid = $row['course_id'];

		$courseurl = new moodle_url('/blocks/campusclash/compras.php', array('courseid' => $courseid));

		mysql_query("UPDATE `compras_premios` SET `canjeado`= '1' WHERE `id`= '$id'"); 

		redirect($courseurl); 
    }
    echo "Error con id del producto, vuelva atrás";
}
echo "Error con id del producto, vuelva atrás";
