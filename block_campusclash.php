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
        $this->content->text   = '<h4>Versión 1.0</h4>';
        $this->content->text .= block_ranking_print_intro();


	$context = context_course::instance($COURSE->id);
	
	if (has_capability('block/campusclash:managepages', $context)) {
	    $footerurl = new moodle_url('/blocks/campusclash/report.php', array('courseid' => $this->page->course->id));
        $this->content->footer .= '<div style="margin:20px 40px 0px 40px">'.html_writer::tag('p', html_writer::link($footerurl, get_string('Verlistadeusuarios', 'block_campusclash'), array('class' => 'btn btn-default'))).'</div>';

	} else {
            $this->content->footer = '';
	}	

	//Check to see if we are in editing mode and that we can manage pages.
	$canmanage = has_capability('block/campusclash:managepages', $context);// && $PAGE->user_is_editing($this->instance->id);
	$canview = has_capability('block/campusclash:viewpages', $context);

	$studentpoints = block_campusclash_get_student_points($USER->id);
    $studentcoins = block_campusclash_get_student_coins($USER->id); 
    $studentexp = block_campusclash_get_student_experience($USER->id); 
    $studentlvl = block_campusclash_get_student_lvl($USER->id);
    $studentname = block_campusclash_get_student_username($USER->id);


	if ($studentpoints!= null) {
        $ccweb = "http://localhost/campusclashapp/public/index.php";
        $this->content->text .= block_campusclash_print_student_points($studentpoints, $studentcoins, $studentexp, $studentname, $studentlvl);
	    $this->content->text .= '<a href='.$ccweb.' style="margin: 0px 35px;"><button>Canjea tus monedas aquí!</button></a>';
	} else {
	    if ($canmanage) {
		    $accepted = '';
	    } else {
            $pageparam = array('blockid' => $this->instance->id, 'courseid' => $COURSE->id, 'id' => $campusclashpage->id);
            $acceptedurl = new moodle_url('/blocks/campusclash/accepted.php', $pageparam);
            $accepted = '<div style="margin:30px 80px 15px 80px">'.html_writer::tag('p', html_writer::link($acceptedurl, "Regístrate", array('class' => 'btn btn-default'))).'</div>';
         }		
    	    
	    if ($canview) {		
		    $this->content->text .= $accepted;
	    } else {
		    $this->content->text .= '<p style="text-align:justify;margin:20px;">Lamentablemente, no estás matriculado en este curso. Si quieres registrarte, debes hacerlo desde un curso donde este bloque esté activo y además, estés matriculado.</p>';
	    }       	
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
