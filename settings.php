<?php

//this will be standard in moodle 2
if (!class_exists('admin_setting_pickroles')) { require_once('adminsettingpickroles.php'); }


$settings->add(new admin_setting_pickroles('block_messageteacher_roles', get_string('teachersinclude', 'block_messageteacher'),
                   get_string('rolesdesc', 'block_messageteacher'), array('moodle/legacy:teacher'), PARAM_TEXT));


?>