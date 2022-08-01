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
 * Defines the custom messaging form
 *
 * @package    block_messageteacher
 * @author      Mark Johnson <mark@barrenfrozenwasteland.com>
 * @copyright   2013 Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_messageteacher;

use context;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Custom messaging form.
 *
 * @copyright  2013 Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_form extends \core_form\dynamic_form {

    protected function get_context_for_dynamic_submission(): context {
        if ($contextid = $this->optional_param('contextid', null, PARAM_INT)) {
            return \context::instance_by_id($contextid, MUST_EXIST);
        } else {
            $courseid = $this->optional_param('courseid', null, PARAM_INT);
            return \context_course::instance($courseid);
        }
    }

    public function set_data_for_dynamic_submission(): void {
        global $DB;

        $this->set_data([
            'recipientid' => $this->optional_param('recipientid', null, PARAM_INT),
            'referurl' => $this->optional_param('referurl', null, PARAM_URL),
            'courseid' => $this->optional_param('courseid', null, PARAM_INT)
        ]);
    }

    protected function check_access_for_dynamic_submission(): void {
        require_capability('moodle/site:sendmessage', $this->get_context_for_dynamic_submission());
    }

    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url($this->optional_param('referurl', null, PARAM_URL));
    }

    public function process_dynamic_submission() {
        return $this->process($this->get_data());
    }

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->disable_form_change_checker();
        $strrequired = get_string('required');

        $mform->addElement('textarea',
                            'message',
                            get_string('messagetext', 'block_messageteacher'),
                            array('rows' => 6, 'cols' => 60));
        $mform->setType('message', PARAM_TEXT);

        $mform->addRule('message', $strrequired, 'required', null, 'client');

        $mform->addElement('hidden', 'referurl');
        $mform->setType('referurl', PARAM_URL);

        $mform->addElement('hidden', 'recipientid');
        $mform->setType('recipientid', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        if (empty($this->_ajaxformdata)) {
            $mform->addElement('submit', 'send', get_string('send', 'block_messageteacher'));
        }

    }

    public function definition_after_data() {
        global $DB;
        $mform = $this->_form;
        $recipientid = $mform->getElementValue('recipientid');
        $recipient = $DB->get_record('user', ['id' => $recipientid], '*', MUST_EXIST);
        $header = $mform->createElement('header', 'messsageheader',
                get_string('messageheader', 'block_messageteacher', fullname($recipient)));
        $mform->insertElementBefore($header, 'message');

    }

    /**
     * Validate and send the message.
     *
     * @param \stdClass $data Form data
     * @return true
     */
    public function process($data) {
        global $DB, $USER, $COURSE;
        if (!$recipient = $DB->get_record('user', array('id' => $data->recipientid))) {
            throw new no_recipient_exception($data->recipientid);
        }

        $appendurl = get_config('block_messageteacher', 'appendurl');
        if ($appendurl) {
            $data->message .= "\n\n".get_string('sentfrom', 'block_messageteacher', $data->referurl);
        }

        $eventdata = new \core\message\message();
        $eventdata->component = 'moodle';
        $eventdata->name = 'instantmessage';
        $eventdata->userfrom = $USER;
        $eventdata->userto = $recipient;
        $eventdata->subject = get_string('messagefrom', 'block_messageteacher', fullname($USER));
        $eventdata->fullmessage = $data->message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml = '';
        $eventdata->smallmessage = '';
        $eventdata->notification = 0;
        $eventdata->courseid = $COURSE->id;

        if (!$id = message_send($eventdata)) {
            throw new message_failed_exception();
        }
        return $id;
    }
}
