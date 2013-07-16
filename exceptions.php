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
 * Defines the exceptions thrown by messageteacher
 *
 * @package    block_messageteacher
 * @author      Mark Johnson <mark@barrenfrozenwasteland.com>
 * @copyright   2013 Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class messageteacher_message_failed_exception extends moodle_exception {

    public function __construct() {
        parent::__construct('messagefailed', 'block_messageteacher');
    }
}

class messageteacher_no_recipient_exception extends moodle_exception {

    public function __construct($recipientid) {
        parent::__construct('norecipient', 'block_messageteacher', $recipientid);
    }
}
