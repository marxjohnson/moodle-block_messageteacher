<?php

class block_messageteacher extends block_base {
    function init() {
        $this->title = 'Message My Teacher';
        $this->version = 2009011900;
    }

    function get_content() {
        global $COURSE, $CFG, $USER;

        $this->content->text='';
        $this->content->footer='';

        $sql="SELECT userid
              FROM  {$CFG->prefix}role_assignments
                  JOIN {$CFG->prefix}context ON contextid = {$CFG->prefix}context.id AND contextlevel= 50
              WHERE {$CFG->prefix}context.instanceid=$COURSE->id AND userid <> $USER->id AND roleid in ($CFG->block_messageteacher_roles) AND hidden=0";
        if(!$teachers=get_records_sql($sql)){
            return '';
        }
        foreach ($teachers as $teacherid){
            $teacher=get_record('user','id',$teacherid->userid);
            $this->content->text .= "<a target='message_2'  href='$CFG->wwwroot/message/discussion.php?id=$teacher->id' onclick=\"this.target='message_2';return openpopup('/message/discussion.php?id=$teacher->id', 'message_2', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);\">$teacher->firstname $teacher->lastname</a><br>";
        }
        return $this->content;
    }
}

?>