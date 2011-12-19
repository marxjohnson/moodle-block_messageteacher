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
 * Defines the class for the Message My Teacher block
 *
 * @package    block_messageteacher
 * @author     Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright  2010 onwards Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *  Class definition for the Message My Teacher block
 */
class block_messageteacher extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_messageteacher');
    }

    /**
     * Gets a list of "teachers" with the defined role, and displays a link to message each
     *
     * @access public
     * @return void
     */
    public function get_content() {
        global $COURSE, $CFG, $USER, $DB;

        $this->content->text = '';
        $this->content->footer = '';

        $roles = explode(',', $CFG->block_messageteacher_roles);
        list($usql, $params) = $DB->get_in_or_equal($roles);
        $params = array_merge(array($COURSE->id, $USER->id, CONTEXT_COURSE), $params);
        $select = 'SELECT u.id, u.firstname, u.lastname ';
        $from = 'FROM {role_assignments} ra
            JOIN {context} AS c ON ra.contextid = c.id
            JOIN {user} AS u ON u.id = ra.userid ';
        $where = 'WHERE c.instanceid = ? AND userid != ? AND c.contextlevel = ? AND roleid '.$usql;

        if ($teachers = $DB->get_records_sql($select.$from.$where, $params)) {
            foreach ($teachers as $teacher) {
                $url = new moodle_url('/message/discussion.php', array('id' => $teacher->id));
                $this->content->text .= html_writer::tag('a', fullname($teacher), array('href' => $url));
            }
        }
        return $this->content;
    }
}
