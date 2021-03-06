<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/blocks/campusclash/lib.php');

define('DEFAULT_PAGE_SIZE', 100);

$courseid = required_param('courseid', PARAM_INT);
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

$userfields = user_picture::fields('u', array('username'));
$from = "FROM {user} u
        INNER JOIN {role_assignments} a ON a.userid = u.id
        LEFT JOIN {campusclash_points} r ON r.userid = u.id AND r.courseid = :r_courseid
        INNER JOIN {context} c ON c.id = a.contextid";

$where = "WHERE a.contextid = :contextid
        AND a.userid = u.id
        AND a.roleid = :roleid
        AND c.instanceid = :courseid";

$params['contextid'] = $context->id;
$params['roleid'] = 5;
$params['courseid'] = $COURSE->id;
$params['r_courseid'] = $params['courseid'];

$order = "ORDER BY r.points DESC, u.firstname ASC
        LIMIT " . $perpage;

if ($group) {
    $from .= " INNER JOIN {groups_members} gm ON gm.userid = u.id AND gm.groupid = :groupid";
    $params['groupid'] = $group;
}

$sql = "SELECT $userfields, r.points $from $where $order";

$students = array_values($DB->get_records_sql($sql, $params));

$strcoursereport = get_string('nostudents', 'block_campusclash');
if (count($students)) {
    $strcoursereport = get_string('report_head', 'block_campusclash', count($students));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strcoursereport);
$PAGE->set_title($strcoursereport);

// Output group selector if there are groups in the course.
echo $OUTPUT->container_start('campusclash-report');

if (has_capability('moodle/site:accessallgroups', $context)) {
    $groups = groups_get_all_groups($course->id);
    if (!empty($groups)) {
        groups_print_course_menu($course, $PAGE->url);
    }
}

echo block_campusclash_generate_table($students);

echo $OUTPUT->container_end();

echo $OUTPUT->footer();
