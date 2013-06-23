<?php

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given;
use Behat\Behat\Context\Step\When as When;
use Behat\Gherkin\Node\TableNode as TableNode;

class behat_messageteacher extends behat_base {
    /**
     * Adds an instance of block_messageteacher to a given course.
     *
     * @Given /^there is an instance of messageteacher on "(?P<coursename_string>(?:[^"]|\\")*)"$/
     */
    public function there_is_an_instance_of_messageteacher_on($coursename) {

        return array(new Given('I log in as "admin"'),
            new Given('I follow "'.$coursename.'"'),
            new Given('I turn editing mode on'),
            new Given('I add the "Message My Teacher" block'),
            new Given('I turn editing mode off'),
            new Given('I log out'));

    }

    /**
     * Sets configuration for the block_messageteacher plugin. A table with | Setting name | value | is expected.
     *
     * @Given /^messageteacher has the following settings:$/
     */
    public function messageteacher_has_the_following_settings(TableNode $table) {
        
        if (!$data = $table->getRowsHash()) {
            return;
        }

        foreach ($data as $setting => $value) {
            set_config($setting, $value, 'block_messageteacher');
        }
    }

    /**
     * Creates course category enrolments. A table with | user | category | role | is expected.
     * User is found by username, category by idnumber and role by shortname.
     *
     * @Given /^the following category enrolments exists:$/
     */
    public function the_following_category_enrolments_exists(TableNode $table) {
        global $DB;

        if (!$data = $table->getRowsHash()) {
            return;
        }
        foreach ($data as $first=>$rest) {
            if ($first == 'user') {
                continue;
            }
            $userid = $DB->get_field('user', 'id', array('username' => $first));
            $catid = $DB->get_field('course_categories', 'id', array('idnumber' => $rest[0]));
            $contextid = context_coursecat::instance($catid)->id;
            $roleid = $DB->get_field('role', 'id', array('shortname' => $rest[1]));
            if (!$userid || !$contextid || !$roleid) {
                throw new Exception('Invalid category enrolment data provided. Expected table of | user | category | role |.');
            }
            role_assign($roleid, $userid, $contextid);
        }
        return true;
    }
}
