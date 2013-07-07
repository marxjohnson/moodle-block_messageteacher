@block @block_messageteacher @block_messageteacher_ajaxform @javascript
Feature:
    In order to message my teacher easily
    As a student
    I need to access a messaging form without leaving the current page

Scenario: User accesses form
        Given the following "users" exists:
            | username     | email                    | firstname | lastname |
            | teststudent  | teststudent@example.com  | Test      | Student  |
            | testteacher1 | testteacher1@example.com | Test      | Teacher1 |
        And the following "categories" exists:
            | name       | category | idnumber |
            | Category 1 | 0        | CAT1     |
        And the following "courses" exists:
            | fullname | shortname | category | format |
            | Course 1 | course1   | CAT1     | topics |
        And the following "course enrolments" exists:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course1 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
        And I log in as "teststudent"
        And I follow "Course 1"
        When I follow "Test Teacher1"
        Then "Enter your message for Test Teacher1" "fieldset" should exists
        And "Message text" "field" should exists
        And "Send" "button" should exists

Scenario: Student sends a message and returns to the page
        Given the following "users" exists:
            | username     | email                    | firstname | lastname |
            | teststudent  | teststudent@example.com  | Test      | Student  |
            | testteacher1 | testteacher1@example.com | Test      | Teacher1 |
        And the following "categories" exists:
            | name       | category | idnumber |
            | Category 1 | 0        | CAT1     |
        And the following "courses" exists:
            | fullname | shortname | category | format |
            | Course 1 | course1   | CAT1     | topics |
        And the following "course enrolments" exists:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course1 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
        And I log in as "teststudent"
        And I follow "Course 1"
        And I follow "Test Teacher1"
        And I fill the moodle form with:
            | Message text | Test Message |
        When I press "Send"
        Then I wait "5" seconds
        And I should see "Message Sent!" in the ".messageteacher_confirm" "css_element"
        And I should see "Course 1" in the "h1" "css_element"
        And "Enter your message for Test Teacher1" "fieldset" should not exists

Scenario: Teacher recieves a message sent from the custom form
        Given the following "users" exists:
            | username     | email                    | firstname | lastname |
            | teststudent  | teststudent@example.com  | Test      | Student  |
            | testteacher1 | testteacher1@example.com | Test      | Teacher1 |
        And the following "categories" exists:
            | name       | category | idnumber |
            | Category 1 | 0        | CAT1     |
        And the following "courses" exists:
            | fullname | shortname | category | format |
            | Course 1 | course1   | CAT1     | topics |
        And the following "course enrolments" exists:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course1 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
        And I log in as "teststudent"
        And I follow "Course 1"
        And I follow "Test Teacher1"
        And I fill the moodle form with:
            | Message text | Test Message |
        And I press "Send"
        And I log out
        And I log in as "testteacher1"
        And I expand "My profile" node
        When I follow "Messages"
        And I follow "Test Student (1)"
        Then I should see "Test Message"

Scenario: Teacher recieves a message sent from the custom form and appendurl is enabled
        Given the following "users" exists:
            | username     | email                    | firstname | lastname |
            | teststudent  | teststudent@example.com  | Test      | Student  |
            | testteacher1 | testteacher1@example.com | Test      | Teacher1 |
        And the following "categories" exists:
            | name       | category | idnumber |
            | Category 1 | 0        | CAT1     |
        And the following "courses" exists:
            | fullname | shortname | category | format |
            | Course 1 | course1   | CAT1     | topics |
        And the following "course enrolments" exists:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course1 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles     | 3 |
            | appendurl | 1 |
        And I log in as "teststudent"
        And I follow "Course 1"
        And I follow "Test Teacher1"
        And I fill the moodle form with:
            | Message text | Test Message |
        And I press "Send"
        And I log out
        And I log in as "testteacher1"
        And I expand "My profile" node
        When I follow "Messages"
        And I follow "Test Student (1)"
        Then I should see "Test Message"
        And I should see "/course/view.php?id=2"
