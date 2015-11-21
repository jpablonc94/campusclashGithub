<?php
// This file is part of Campusclash block for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * campusclash block manager
 *
 * @package    contrib
 * @subpackage block_campusclash
 * @copyright  2015 Willian Mano http://willianmano.net
 * @authors    Willian Mano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Block campusclash manager class.
 *
 * @package    block_campusclash
 * @copyright  2015 Willian Mano http://willianmano.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_campusclash_manager {

    const DEFAULT_POINTS = 2;

    protected static $config = null;

    /**
     *
     * @param int $cmcid
     * @param float $grade
     *
     * @return void
     */
    public static function add_user_points($cmcid, $grade = null) {

        $usercompletion = self::get_module_completion($cmcid);

        switch ($usercompletion->modulename) {
            case 'assign':
                self::add_default_points($usercompletion, self::get_config('assignpoints'), $grade);
            break;

            case 'resource':
                self::add_default_points($usercompletion, self::get_config('resourcepoints'), $grade);
            break;

            case 'forum':
                self::add_default_points($usercompletion, self::get_config('forumpoints'), $grade);
            break;

            case 'workshop':
                self::add_default_points($usercompletion, self::get_config('workshoppoints'), $grade);
            break;

            case 'page':
                self::add_default_points($usercompletion, self::get_config('pagepoints'), $grade);
            break;

            default:
                self::add_default_points($usercompletion, self::get_config('defaultpoints'), $grade);
            break;
        }
    }

    /**
     * Returns the modules completions
     *
     * @param int
     * @return mixed
     */
    protected static function get_module_completion($cmcid) {
        global $DB;

        $sql = "SELECT
                    cmc.*,
                    cmc.id as cmcid,
                    cm.course,
                    cm.module as moduleid,
                    cm.instance,
                    cm.score,
                    cm.indent,
                    cm.completion,
                    cm.completiongradeitemnumber,
                    cm.completionview,
                    cm.completionexpected,
                    m.name as modulename
                FROM
                    {course_modules_completion} cmc
                INNER JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                INNER JOIN {modules} m ON m.id = cm.module
                WHERE cmc.id = :cmcid";

        $params['cmcid'] = $cmcid;

        return $DB->get_record_sql($sql, $params);
    }

    /**
     * Default method to add points to students
     *
     * @return void
     */
    protected static function add_default_points($completion, $points = null, $grade = null) {

        if (!isset($points) || trim($points) != '') {
            $points = self::DEFAULT_POINTS;
        }

        if (!empty($grade)) {
            if ($grade > 10) {
                $grade = $grade / 10;
            }

            $points += $grade;
        } else {
            $activitygrade = self::get_activity_finalgrade($completion->modulename, $completion->instance, $completion->userid);

            if ($activitygrade) {
                $points += $activitygrade;
            }
        }

        $campusclashid = self::add_or_update_user_points($completion->userid, $completion->course, $points);

        self::add_campusclash_log($campusclashid, $completion->course, $completion->coursemoduleid, $points);
    }

    /**
     * Returns activity grade
     *
     * @param int
     * @param int
     * @param int
     * @return float
     */
    protected static function get_activity_finalgrade($activity, $activityid, $userid) {
        global $DB;

        $sql = "SELECT
                    gg.itemid, gg.userid, gg.rawscaleid, gg.finalgrade, gi.scaleid
                FROM
                    {grade_grades} gg
                INNER JOIN {grade_items} gi ON gi.id = gg.itemid
                WHERE gi.itemmodule = :activity AND gi.iteminstance = :iteminstance AND gg.userid = :userid";
        $params['activity'] = $activity;
        $params['iteminstance'] = $activityid;
        $params['userid'] = $userid;

        $gradeitem = $DB->get_records_sql($sql, $params);

        $finalgrade = 0;
        if (!empty($gradeitem)) {
            $gradeitem = current($gradeitem);

            // Grade without scale -- grademax 100.
            if (empty($gradeitem->scaleid)) {
                $finalgrade = $gradeitem->finalgrade;

                if ($finalgrade > 10) {
                    $finalgrade = $finalgrade / 10;
                }
            } else {
                $finalgrade = self::get_finalgrade_by_scale($gradeitem->finalgrade, $gradeitem->scaleid);
            }
        }

        return $finalgrade;
    }

    /**
     * Returns activity grade by scale
     *
     * @param int
     * @param int
     * @return int
     */
    protected static function get_finalgrade_by_scale($finalgrade, $scaleid) {
        global $DB;

        $sql = "SELECT scale FROM {scale} WHERE id = :scaleid";
        $params['scaleid'] = $scaleid;

        $scale = $DB->get_records_sql($sql, $params);

        if (!empty($scale)) {
            $scale = current($scale);
            $scale = explode(',', $scale->scale);

            $finalgrade = $scale[$finalgrade - 1];
        }

        return $finalgrade;
    }

    /**
     * Save students points
     *
     * @param int
     * @param int
     * @param int
     * @return int
     */
    protected static function add_or_update_user_points($userid, $courseid, $points) {
        global $DB;

        $sql = "SELECT * FROM {campusclash_points}
                WHERE userid = :userid AND courseid = :courseid";
        $params['userid'] = $userid;
        $params['courseid'] = $courseid;

        $userpoints = $DB->get_record_sql($sql, $params);

        // User dont have points yet.
        if (empty($userpoints)) {
            // Basic block configuration.
            $userpoints = new stdClass();
            $userpoints->userid = $userid;
            $userpoints->courseid = $courseid;
            $userpoints->points = $points;
            $userpoints->timecreated = time();
            $userpoints->timemodified = time();

            $campusclashid = $DB->insert_record('campusclash_points', $userpoints, true);
        } else {
            $userpoints->points = $userpoints->points + $points;
            $userpoints->timemodified = time();
            $DB->update_record('campusclash_points', $userpoints);
            $campusclashid = $userpoints->id;
        }

	    $server="localhost";
        $database = "campusclash";
        $db_pass = 'T7tmn892AB3';
        $db_user = 'root';

        mysql_connect($server, $db_user, $db_pass) or die ("error1".mysql_error());
        mysql_select_db($database) or die ("error2".mysql_error());
        
    	$result = mysql_query("SELECT * FROM `usertbl` WHERE `moodle_id` = $userid"); 

        $numrows=mysql_num_rows($result);
    
        if($numrows!=0){
    	   $row = mysql_fetch_assoc($result);
            $userpoints = $row['points'];
            $money = $row['monedas'];
            $exp = $row['experiencia']; 
            $username = $row['username'];  
            $email = $row['email'];   
            
            $experiencia = $exp + $points;
            $nivel = self::obtener_nivel($experiencia);
            $nextlvl = self::obtener_nextlvl($nivel);
            $monedas = self::obtener_monedas($nivel, $money, $points);
            $puntos = self::obtener_puntos($nivel, $userpoints, $points);
            
            mysql_query("UPDATE `usertbl` SET `points`= $puntos, `monedas`= $monedas, `experiencia`= $experiencia, `nivel`= '$nivel', `next_lvl`=$nextlvl WHERE `moodle_id`= $userid"); 
        
            $consulta = mysql_query("SELECT * FROM `puntos_curso` WHERE `course_id` = $courseid AND `moodle_id` = $userid");

            if (mysql_num_rows($consulta)>0) {
                $row2 = mysql_fetch_assoc($consulta);
                $money2 = $row['monedas'];
                $monedas2 = self::obtener_monedas($nivel, $money2, $points);
                mysql_query("UPDATE `puntos_curso` SET `monedas`= $monedas2 WHERE `course_id` = $courseid AND `moodle_id` = $userid"); 
            } else {
                $timecreated = time();
                $monedas2 = 5 + $points/2;
                mysql_query ("INSERT INTO `puntos_curso`(`moodle_id`, `course_id`, `email`, `username`, `monedas`, `timecreated`) 
                            VALUES ('".$userid."', '".$courseid."', '".$email."', '".$username."', '".$monedas2."', '".$timecreated."')");
            }
        }
        return $campusclashid;
    }

    protected static function obtener_nivel($experiencia) {
        $nivel = 1;
        if($experiencia>20){
            $nivel = 2;
            if($experiencia>100){
                $nivel = 3;
                if($experiencia>250){
                    $nivel = 4;
                    if($experiencia>500){
                        $nivel = 5;
                    }
                }
            }
        }
        return $nivel; 
    }

     protected static function obtener_nextlvl($nivel){
        switch ($nivel) {
            case 1:
                $nextlvl= 20;
                break;
            case 2:
                $nextlvl= 100;
                break;
            case 3:
                $nextlvl= 250;
                break;
            case 4:
                $nextlvl= 500;
                break;
            default:
                $nextlvl= null;
        } 
        return $nextlvl;
    }

    protected static function obtener_monedas($nivel, $money, $points){
        switch ($nivel) {
            case 1:
                $monedas= $money + $points/2;
                break;
            case 2:
                $monedas= $money + $points;
                break;
            case 3:
                $monedas= $money + 2*$points;
                break;
            case 4:
                $monedas= $money + 4*$points;
                break;
            case 5:
                $monedas= $money + 8*$points;
                break;
            default:
                $monedas= $money + $points/2;
        } 
        return $monedas;
    }

    protected static function obtener_puntos($nivel, $userpoints, $points){
        switch ($nivel) {
            case 1:
                $puntos= $userpoints + 2*$points;
                break;
            case 2:
                $puntos= $userpoints + 4*$points;
                break;
            case 3:
                $puntos= $userpoints + 9*$points;
                break;
            case 4:
                $puntos= $userpoints + 12*$points;
                break;
            case 5:
                $puntos= $userpoints + 16*$points;
                break;
            default:
                $puntos= $userpoints + $points;
        } 
        return $puntos;
    }

    /**
     * Add points movement to log
     *
     * @param int
     * @param int
     * @param int
     * @param int
     * @return int
     */
    protected static function add_campusclash_log($campusclashid, $courseid, $cmc, $points) {
        global $DB;

        $campusclashlog = new stdClass();
        $campusclashlog->campusclashid = $campusclashid;
        $campusclashlog->courseid = $courseid;
        $campusclashlog->course_modules_completion = $cmc;
        $campusclashlog->points = $points;
        $campusclashlog->timecreated = time();

        $logid = $DB->insert_record('campusclash_logs', $campusclashlog, true);

        return $logid;
    }
    
    /**
     * Get the configuration.
     *
     * @param string $name The config to get.
     * @return string
     */
    protected static function get_config($name) {
        global $DB;

        if (empty(self::$config)) {
            $records = $DB->get_records('config_plugins', array('plugin' => 'block_campusclash'));

            foreach ($records as $key => $value) {
                self::$config[$value->name] = $value->value;
            }
        }

        if (array_key_exists($name, self::$config)) {
            return self::$config[$name];
        }

        return '';
    }
}
