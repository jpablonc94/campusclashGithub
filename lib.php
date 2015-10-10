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
    $database = "campusclash";
    $db_pass = 'T7tmn892AB3';
    $db_user = 'root';
	
    mysql_connect($server, $db_user, $db_pass) or die ("error1".mysql_error());

    mysql_select_db($database) or die ("error2".mysql_error());
    $result = mysql_query("SELECT `points` FROM `usertbl` WHERE `moodle_id` = $userid"); 
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

function block_ranking_print_intro() {
    global $PAGE;

    $PAGE->requires->js_init_call('M.block_ranking.init_tabview');
    $ccweb = "http://localhost/campusclashapp/public/index.php";
    $left = new moodle_url('/blocks/campusclash/pix/moveleft.gif');
    $right = new moodle_url('/blocks/campusclash/pix/removeright.gif');
    $bg1 = new moodle_url('/blocks/campusclash/pix/header-bg.jpg');
    $bg2 = new moodle_url('/blocks/campusclash/pix/intro-bg2.jpg');
    $bg3 = new moodle_url('/blocks/campusclash/pix/home-bg.jpg');
    $margin = "margin:0px 30px";
    $nextA = "nextA()";
    $previousA = "previousA()";
    $nextB = "nextB()";
    $previousB = "previousB()";
    $azulclaro = "color:#0099FF;";
    return '<div id="ranking-tabs">
                <ul>
                    <li><a href="#quees" onclick="fondo1()">'.get_string('definition', 'block_campusclash').'</a></li>
                    <li><a href="#puntos" onclick="fondo2()">'.get_string('points', 'block_campusclash').'</a></li>
                    <li><a href="#contacto" onclick="fondo3()">'.get_string('contact', 'block_campusclash').'</a></li>
                </ul>
                <div id="fondo" style="width: 238px;height: 220px;border: 3px double gray; background: url('.$bg1.');">
                    <div id="quees">
                        <h5 style="color:white">¿Qué es CampusClash?</h5>
                        <h6 id="quees-texto" style="color:white;text-align:justify;font-size:13px">Antes de nada, darte la bienvenida y animarte a leer estas líneas, merecerá la pena.<br>El objetivo final de <a href='.$ccweb.' style='.$azulclaro.'>CampusClash</a> es hacer tu paso por el aula virtual, más entretenido. ¿Quieres saber más?</h6>                        
                        <div style="color:white;margin:10px 0px 20px 45px">
                            <button onclick="previousA()">Volver</button>
                            <button onclick="nextA()">Seguir</button>                            
                        </div>
                    </div>
                    <div id="puntos">
                        <h5 style="color:white">Acerca de los puntos</h5>
                        <h6 id="puntos-texto" style="color:white;text-align:justify;font-size:13px">¿Quieres saber cómo obtener puntos?<br>La respuesta es simple: Participando en el aula mediante la entrega de tareas, la realización de test o la interacción en foros.</h6>
                        <div style="color:white;margin:10px 0px 20px 45px">
                            <button onclick="previousB()">Volver</button>
                            <button onclick="nextB()">Seguir</button>
                        </div>
                    </div>
                    <div id="contacto">
                        <h5 style="color:white">¿Alguna otra duda?</h5>
                        <h6 style="color:white;text-align:justify;font-size:13px">Si alguno de los apartados no ha quedado claro o tienes cualquier otra pregunta acerca de este bloque, puedes enviar un correo a la siguiente dirección:</h6>
                        <h6 style="color:white;text-align:center;font-size:13px">jpablonc94@gmail.com</h6>
                        <h6 style="color:white;text-align:justify;font-size:13px">Espero que te animes a probarlo y que te sea de utilidad!</h6>
                    </div>
                </div>
                <script>
                    var pagA = 1;
                    function nextA(){
                        pagA = pagA+1;
                        if(pagA>4){
                            pagA=4;
                        } 
                        switch (pagA){
                            case 1:
                                var x = document.getElementById("quees-texto").innerHTML = "Antes de nada, darte la bienvenida y animarte a leer estas líneas, merecerá la pena.<br>El objetivo final de <a href='.$ccweb.' style='.$azulclaro.'>CampusClash</a> es hacer tu paso por el aula virtual, más entretenido. ¿Quieres saber más?";
                                break
                            case 2:
                                var x = document.getElementById("quees-texto").innerHTML = "<u>Puntuación:</u><br>Como podréis observar debajo, si os habéis registrado, os ha sido asignada una puntuación. Dichos puntos irán incrementando conforme vayáis participando dentro del aula.";
                                break
                            case 3:
                                var x = document.getElementById("quees-texto").innerHTML = "<u>Puntuación:</u><br>¿Quieres saber qué hacer con los puntos? Te recomiendo visitar la web haciendo click en el botón de más abajo o leyendo el aparatado Puntos de este bloque.";
                                break
                            case 4:
                                var x = document.getElementById("quees-texto").innerHTML = "<u>Página web:</u><br>Este bloque es solo la puerta a la verdadera esencia de esta idea. Si queréis ver las posibilidades que brinda CampusClash, os recomiendo visitar la <a href='.$ccweb.' style='.$azulclaro.'>página web</a>.";
                                break
                            default:                                 
                        }           
                    }

                    function previousA(){
                        pagA = pagA-1;
                        if(pagA<1){
                            pagA = 1
                        } 
                        switch (pagA){
                            case 1:
                                var x = document.getElementById("quees-texto").innerHTML = "Antes de nada, darte la bienvenida y animarte a leer estas líneas, merecerá la pena.<br>El objetivo final de <a href='.$ccweb.' style='.$azulclaro.'>CampusClash</a> es hacer tu paso por el aula virtual, más entretenido. ¿Quieres saber más?";
                                break
                            case 2:
                                var x = document.getElementById("quees-texto").innerHTML = "<u>Puntuación:</u><br>Como podréis observar debajo, si os habéis registrado, os ha sido asignada una puntuación. Dichos puntos irán incrementando conforme vayáis participando dentro del aula.";
                                break
                            case 3:
                                var x = document.getElementById("quees-texto").innerHTML = "<u>Puntuación:</u><br>¿Quieres saber qué hacer con los puntos? Te recomiendo visitar la web haciendo click en el botón de más abajo o leyendo el aparatado Puntos de este bloque.";
                                break
                            case 4:
                                var x = document.getElementById("quees-texto").innerHTML = "<u>Página web:</u><br>Este bloque es solo la puerta a la verdadera esencia de esta idea. Si queréis ver las posibilidades que brinda CampusClash, os recomiendo visitar la <a href='.$ccweb.' style='.$azulclaro.'>página web</a>.";
                                break
                            default:                                 
                        }         
                    }

                    var pagB = 1;
                    function nextB(){
                        pagB = pagB+1;
                        if(pagB>4){
                            pagB=4;
                        } 
                        switch (pagB){
                            case 1:
                                var x = document.getElementById("puntos-texto").innerHTML = "¿Quieres saber cómo obtener puntos?<br>La respuesta es simple: Participando en el aula mediante la entrega de tareas, la realización de test o la interacción en foros";
                                break
                            case 2:
                                var x = document.getElementById("puntos-texto").innerHTML = "<u>Tareas y tests:</u><br>Conforme vayas realizando test (dentro de los cursos) y entregando prácticas, trabajos y entregables, verás como tu puntuación comienza a incrementarse!";
                                break
                            case 3:
                                var x = document.getElementById("puntos-texto").innerHTML = "<u>Foros:</u><br>Dentro de los distintos cursos, los profesores pueden abrir foros de debate. Si participáis en ellos, también recibiréis puntos!<br>Hablad con vuestros profesores y acordad temas que os interesen.";
                                break
                            case 4:
                                var x = document.getElementById("puntos-texto").innerHTML = "<u>Utilidad de los puntos:</u><br>La puntuación servirá para dos fines: Poder canjearlos en una tienda online por premios y tener una posición en un ranking general. Ambas cosas estarán situadas dentro de la <a href='.$ccweb.' style='.$azulclaro.'>página web</a>.";
                                break
                            default:                                 
                        }           
                    }

                    function previousB(){
                        pagB = pagB-1;
                        if(pagB<1){
                            pagB = 1
                        } 
                        switch (pagB){
                            case 1:
                                var x = document.getElementById("puntos-texto").innerHTML = "¿Quieres saber qué te da puntos?<br>La respuesta es simple: Participando en el aula mediante la entrega de tareas, la realización de test o la interacción en foros";
                                break
                            case 2:
                                var x = document.getElementById("puntos-texto").innerHTML = "<u>Tareas y tests:</u><br>Conforme vayas realizando test (dentro de los cursos) y entregando prácticas, trabajos y entregables, verás como tu puntuación comienza a incrementarse!";
                                break
                            case 3:
                                var x = document.getElementById("puntos-texto").innerHTML = "<u>Foros:</u><br>Dentro de los distintos cursos, los profesores pueden abrir foros de debate. Si participáis en ellos, también recibiréis puntos!<br>Hablad con vuestros profesores y acordad temas que os interesen.";
                                break
                            case 4:
                                var x = document.getElementById("puntos-texto").innerHTML = "<u>Utilidad de los puntos:</u><br>La puntuación servirá para dos fines: Poder canjearlos en una tienda online por premios y tener una posición en un ranking general. Ambas cosas estarán situadas dentro de la <a href='.$ccweb.' style='.$azulclaro.'>la página web</a>.";
                                break
                            default:                                 
                        }         
                    }

                    function fondo1(){
                        var x = document.getElementById("fondo");
                        x.style.background = "url('.$bg1.')";          
                    }
                    function fondo2(){
                        var x = document.getElementById("fondo");
                        x.style.background = "url('.$bg2.')";          
                    }
                    function fondo3(){
                        var x = document.getElementById("fondo");
                        x.style.background = "url('.$bg3.')";         
                    }
                </script>

            </div>';
}

function block_campusclash_generate_table($data) {
    global $USER, $OUTPUT;

    if (empty($data)) {
        return get_string('nostudents', 'block_campusclash');
    }

    $table = new html_table();
    $table->attributes = array("class" => "campusclashTable table table-striped generaltable");
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
