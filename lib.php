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

    return "<h4>".get_string('your_score', 'block_campusclash').":</h4>". $individualpoints;
}

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

function block_campusclash_get_students($limit = null) {
    global $COURSE, $DB, $PAGE;

    // Get block ranking configuration.
    $cfgcampusclash = get_config('block_campusclash');

    // Get limit from default configuration or instance configuration.
    if (!$limit) {
        if (isset($cfgcampusclash->campusclashsize) && trim($cfgcampusclash->campusclashsize) != '') {
            $limit = $cfgcampusclash->campusclashsize;
        } else {
            $limit = 10;
        }
    }

    $context = $PAGE->context;

    $userfields = user_picture::fields('u', array('username'));
    $sql = "SELECT
                DISTINCT $userfields, r.points
            FROM
                {user} u
            INNER JOIN {role_assignments} a ON a.userid = u.id
            INNER JOIN {campusclash_points} r ON r.userid = u.id AND r.courseid = :r_courseid
            INNER JOIN {context} c ON c.id = a.contextid
            WHERE a.contextid = :contextid
            AND a.userid = u.id
            AND a.roleid = :roleid
            AND c.instanceid = :courseid
            AND r.courseid = :crsid
            ORDER BY r.points DESC, u.firstname ASC
            LIMIT " . $limit;
    $params['contextid'] = $context->id;
    $params['roleid'] = 5;
    $params['courseid'] = $COURSE->id;
    $params['crsid'] = $COURSE->id;
    $params['r_courseid'] = $COURSE->id;

    $users = array_values($DB->get_records_sql($sql, $params));

    return $users;
}

function block_campusclash_print_students($rankinggeral) {
    global $PAGE;

    $tablegeral = generate_table($rankinggeral);

    $PAGE->requires->js_init_call('M.block_ranking.init_tabview');

    return '<div id="ranking-tabs">
                <ul>
                    <li><a href="#geral">'.get_string('general', 'block_campusclash').'</a></li>
                </ul>
                <div>
                    <div id="geral">'.$tablegeral.'</div>
                </div>
            </div>';
}

function block_campusclash_generate_table($data) {
    global $USER, $OUTPUT;

    if (empty($data)) {
        return get_string('nostudents', 'block_campusclash');
    }

    $table = new html_table();
    $table->attributes = array("class" => "rankingTable table table-striped generaltable");
    $table->head = array(
                        get_string('table_position', 'block_campusclash'),
                        get_string('table_name', 'block_campusclash'),
                        get_string('table_points', 'block_campusclash')
                    );
    $lastpos = 1;
    $lastpoints = current($data)->points;
    for ($i = 0; $i < count($data); $i++) {
        $row = new html_table_row();

        // Verify if the logged user is one user in ranking.
        if ($data[$i]->id == $USER->id) {
            $row->attributes = array('class' => 'itsme');
        }

        if ($lastpoints > $data[$i]->points) {
            $lastpos++;
            $lastpoints = $data[$i]->points;
        }

        $row->cells = array(
                        $lastpos,
                        $OUTPUT->user_picture($data[$i], array('size' => 24, 'alttext' => false)) . ' '.$data[$i]->firstname,
                        $data[$i]->points ?: '-'
                    );
        $table->data[] = $row;
    }

    return html_writer::table($table);
}
