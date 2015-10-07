<?php

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/campusclash/lib.php');

class block_campusclash extends block_base {
    
    public function init() {
        $this->title = get_string('campusclash', 'block_campusclash');
    }
    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.
    public function specialization() {
    	if (isset($this->config)) {
            if (empty($this->config->title)) {
            	$this->title = get_string('defaulttitle', 'block_campusclash');            
            } else {
            	$this->title = $this->config->title;
            }
 
            if (empty($this->config->text)) {
            	$this->config->text = get_string('defaulttext', 'block_campusclash');
            }    
        }
    }

    public function get_content() {
        global $COURSE, $DB, $PAGE, $USER;

	if ($this->content !== null) {
            return $this->content;
        }
	if (! empty($this->config->text)) {
    	    $this->content->text = $this->config->text;
	}

	//Texto que aparece en el bloque
        $this->content         =  new stdClass;
        $this->content->text   = '<h5>Primera versión de pruebas</h5>';


	$context = context_course::instance($COURSE->id);
	
	if (has_capability('block/campusclash:managepages', $context)) {
	    $footerurl = new moodle_url('/blocks/campusclash/report.php', array('courseid' => $this->page->course->id));
                                            
    	    $this->content->footer .= html_writer::tag('p', html_writer::link($footerurl, get_string('Ver lista de usuarios', 'block_ranking'), array('class' => 'btn btn-default')));

	} else {
            $this->content->footer = '';
	}	

	//Check to see if we are in editing mode and that we can manage pages.
	$canmanage = has_capability('block/campusclash:managepages', $context);// && $PAGE->user_is_editing($this->instance->id);
	$canview = has_capability('block/campusclash:viewpages', $context);

	$studentpoints = block_campusclash_get_student_points($USER->id);

	if ($studentpoints!= null) {
            $this->content->text .= block_campusclash_print_student_points($studentpoints);
	    $this->content->text .= '<a href="/campusclash/sections/inicio.html" style="text-align: center;">Canjea tus puntos aquí</a>';
	} else {
	    if ($canmanage) {
		    $this->content->text .= '<p> Gracias por utilizar campusclash, si quiere ver la repartición de puntos entre los alumnos, pulse el siguiente boton';
            	    $acepto = '';
		    $accepted = '';
	    } else {
		    $this->content->text .= '<p>Si aceptas entrarás a formar parte del mundo CampusCLASH! podrás ser beneficiario de grandes premios!</p>';
            	    $pageparam = array('blockid' => $this->instance->id, 
                    	'courseid' => $COURSE->id, 
                    	'id' => $campusclashpage->id);
                    $acceptedurl = new moodle_url('/blocks/campusclash/accepted.php', $pageparam);
             	    $checkpicurl = new moodle_url('/blocks/campusclash/pix/check_opt.png');
            	    $accepted = html_writer::link($acceptedurl, html_writer::tag('img', '', array('src' => $checkpicurl, 'alt' => get_string('edit'))));
		    $acepto = 'ACEPTO';
	    }		
    	    
	    	    
	    $this->content->text .= '<div class="class1" style="text-align: center;">';
	    if ($canview) {
		
		$this->content->text .= $accepted;
		$this->content->text .= ' ';
		$this->content->text .= $acepto;
	    } else {
		$this->content->text .= 'Lamentablemente, eres sólo un invitado. Si quieres participar deberás estar matriculado en algún curso que utilice CAMPUSCLASH!';
	    }
	    $this->content->text .= '</div>';       	
	}
	
        return $this->content;
    }

    public function instance_config_save($data) {
       if(get_config('campusclash', 'Allow_HTML') == '1') {
          $data->text = strip_tags($data->text);
       }
 
       // And now forward to the default implementation defined in the parent class
       return parent::instance_config_save($data);
    } 

    public function instance_delete() {
    	global $DB;
    	$DB->delete_records('block_campusclash', array('blockid' => $this->instance->id));
    }
    
    public function instance_allow_multiple() {
        return false;
    }
    
    function has_config() {return true;}

    public function hide_header() {
        return false;
    }

    public function html_attributes() {
    	$attributes = parent::html_attributes(); // Get default values
    	$attributes['class'] .= ' block_'. $this->name(); // Append our class to class attribute
    	return $attributes;
    }

    public function applicable_formats() {
        return array(
            'course-view'    => true,
            'site'           => false,
            'mod'            => false,
            'my'             => false
        );
    }

    public function cron() {  

	/*global $DB, $USER, $COURSE; // Global database object
 	
	$server="localhost";
        $database = "moodle18";
        $db_pass = 'T7tmn892AB3';
        $db_user = 'root';

        mysql_connect($server, $db_user, $db_pass) or die ("error1".mysql_error());

        mysql_select_db($database) or die ("error2".mysql_error());
        $userid = $USER->id;
        $points = 0;
        $timecreated = time();
        mysql_query ("INSERT INTO `prueba`(`USERID`, `USERNAME`, `POINTS`, `TIMECREATED`) VALUES ($userid, 'Hola', $points ,$timecreated)");

	*/
	    
	         	
	
    }



}   // Here's the closing bracket for the class definition
