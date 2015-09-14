<?php

function block_campusclash_images() {
    return array(html_writer::tag('img', '', array('alt' => get_string('red', 'block_campusclash'), 'src' => "pix/picture0.gif")),
                html_writer::tag('img', '', array('alt' => get_string('blue', 'block_campusclash'), 'src' => "pix/picture1.gif")),
                html_writer::tag('img', '', array('alt' => get_string('green', 'block_campusclash'), 'src' => "pix/picture2.gif")));
}

function block_campusclash_print_page($campusclash, $return = false) {
    //Add Page Title
    global $OUTPUT, $COURSE;
    $display = $OUTPUT->heading($campusclash->pagetitle);

    //Open a box
    $display .= $OUTPUT->box_start(); 
    
    //Display the Date
    if($campusclash->displaydate) {
    	$display .= html_writer::start_tag('div', array('class' => 'displaydate'));
	$display .= userdate($campusclash->displaydate);
	$display .= html_writer::end_tag('div');
    }
    
    //Display Text
    $display .= clean_text($campusclash->displaytext);
 
    //close the box
    $display .= $OUTPUT->box_end();

    //Display the Picture
    if ($campusclash->displaypicture) {
    	$display .= $OUTPUT->box_start();
    	$images = block_campusclash_images();
    	$display .= $images[$campusclash->picture];
    	$display .= html_writer::start_tag('p');
    	$display .= clean_text($campusclash->description);
    	$display .= html_writer::end_tag('p');
    	$display .= $OUTPUT->box_end();
    }

    //Check to ensure that it was set before trying to output it
    if($return) {
    	return $display;
    } else {
    	echo $display;
    }
}

function block_campusclash_print_student_points($studentpoints) {
    global $USER, $COURSE;

    $table = new html_table();
    $table->attributes = array("class" => "rankingTable table table-striped generaltable");
    
    $row = new html_table_row();
    $row->cells = array($studentpoints);
    $table->data[] = $row;

    $individualpoints = html_writer::table($table);

    return "<h4>".get_string('your_score', 'block_ranking').":</h4>". $individualpoints;
}

/*function block_campusclash_hola(){
	return "hola";
}*/
function block_campusclash_get_student_points($userid) {
    global $COURSE, $DB;
    $server="localhost";
    $database = "moodle18";
    $db_pass = 'T7tmn892AB3';
    $db_user = 'root';
	
    mysql_connect($server, $db_user, $db_pass) or die ("error1".mysql_error());

    mysql_select_db($database) or die ("error2".mysql_error());
    $result = mysql_query("SELECT `POINTS` FROM `prueba` WHERE `USERID` = $userid"); 
    $row = mysql_fetch_row($result);
    return "$row[0]"; 
}
