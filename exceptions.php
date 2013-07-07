<?php

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
