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
 * Standard functions for block_messageteacher
 *
 * @package    block_messageteacher
 * @author      Mark Johnson <mark@barrenfrozenwasteland.com>
 * @copyright   2019 Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Output fragment for modal message form.
 *
 * @param array $args
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 * @throws require_login_exception
 * @throws required_capability_exception
 */
function block_messageteacher_output_fragment_message_form($args) : string {
    global $DB;
    $coursecontext = context_course::instance($args['courseid']);

    require_login();
    require_capability('moodle/site:sendmessage', $coursecontext);

    $recipient = $DB->get_record('user', array('id' => $args['recipientid']));

    $customdata = array(
        'recipient' => $recipient,
        'referurl' => $args['referurl'],
        'courseid' => $args['courseid'],
        'modal' => true,
    );
    $mform = new block_messageteacher\message_form(null, $customdata);
    ob_start();
    $mform->display();
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}