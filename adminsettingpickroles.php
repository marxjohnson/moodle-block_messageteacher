<?php

/**
 * Admin setting that allows a user to pick appropriate roles for something.
 */
class admin_setting_pickroles extends admin_setting_configmulticheckbox {
 private $types;

 /**
 * @param string $name Name of config variable
 * @param string $visiblename Display name
 * @param string $description Description
 * @param array $types Array of capabilities (usually moodle/legacy:something)
 * which identify roles that will be enabled by default. Default is the
 * student role
 */
 public function __construct($name, $visiblename, $description, $types) {
 parent::__construct($name, $visiblename, $description, NULL, NULL);
 $this->types = $types;
 }

 public function load_choices() {
 global $CFG, $DB;
 if (empty($CFG->rolesactive)) {
 return false;
 }
 if (is_array($this->choices)) {
 return true;
 }
 if ($roles = get_all_roles()) {
 $this->choices = array();
 foreach($roles as $role) {
 $this->choices[$role->id] = format_string($role->name);
 }
 return true;
 } else {
 return false;
 }
 }

 public function get_defaultsetting() {
 global $CFG;

 if (empty($CFG->rolesactive)) {
 return null;
 }
 $result = array();
 foreach($this->types as $capability) {
 if ($caproles = get_roles_with_capability($capability, CAP_ALLOW)) {
 foreach ($caproles as $caprole) {
 $result[$caprole->id] = 1;
 }
 }
 }
 return $result;
 }
}

?>