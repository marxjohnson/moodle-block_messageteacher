<?php
// This file is part of Moodle - http://moodle.org/
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
 * Displays and processes the messaging form
 *
 * @package    block_messageteacher
 * @author      Mark Johnson <mark@barrenfrozenwasteland.com>
 * @copyright   2013 Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$recipientid = required_param('recipientid', PARAM_INT);
$referurl = required_param('referurl', PARAM_URL);

$coursecontext = context_course::instance($courseid);
$PAGE->set_context($coursecontext);

require_login();
require_capability('moodle/site:sendmessage', $coursecontext);

$url = '/blocks/messageteacher/message.php';
$PAGE->set_url($url);

$recipient = $DB->get_record('user', array('id' => $recipientid));

$customdata = array(
    'recipient' => $recipient,
    'referurl' => $referurl,
    'courseid' => $courseid
);
$mform = new block_messageteacher\message_form(null, $customdata);

if ($mform->is_cancelled()) {
    // Form cancelled, redirect.
    redirect($referurl);
    exit();
} else if (($data = $mform->get_data())) {
    $mform->process($data);
    redirect($data->referurl);
    exit();
} else {
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
