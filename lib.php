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

function block_campusclash_print_points() {
    global $USER, $COURSE;

    if (!is_student($USER->id)) {
        return '';
    }

    $totalpoints = block_ranking_get_student_points($USER->id);
    $totalpoints = $totalpoints->points != null ? $totalpoints->points : '0';
    $totalpoints = $totalpoints . " " . strtolower(get_string('table_points', 'block_ranking'));
    
    $table = new html_table();
    $table->attributes = array("class" => "rankingTable table table-striped generaltable");
    
    $row = new html_table_row();
    $row->cells = array($totalpoints);
    $table->data[] = $row;

    $individualranking = html_writer::table($table);

    return "<h4>".get_string('your_score', 'block_ranking').":</h4>" . $individualranking;
}

function block_campusclash_get_student_points($userid) {
    global $COURSE, $DB;

    $sql = "SELECT
                sum(rl.points) as points
            FROM
                {user} u
            INNER JOIN {campusclash_points} r ON r.userid = u.id AND r.courseid = :courseid
            INNER JOIN {campusclash_logs} rl ON rl.campusclashid = r.id
            WHERE u.id = :userid
            AND r.courseid = :crsid";

    $params['userid'] = $userid;
    $params['courseid'] = $COURSE->id;
    $params['crsid'] = $COURSE->id;

    return $DB->get_record_sql($sql, $params);
}


