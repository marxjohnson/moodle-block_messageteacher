<?php

require_once(__DIR__.'/../../config.php');

require_once($CFG->dirroot . '/blocks/messageteacher/exceptions.php');
require_once($CFG->dirroot . '/blocks/messageteacher/message_form.php');

$courseid = required_param('courseid', PARAM_INT);
$recipientid = required_param('recipientid', PARAM_INT);
$referurl = required_param('referurl', PARAM_URL);

require_login();
    
@$ajax = $_SERVER['HTTP_X_REQUESTED_WITH'];   

$url = '/blocks/messageteacher/message.php';
$PAGE->set_url($url);
$PAGE->set_context(context_course::instance($courseid));

$recipient = $DB->get_record('user', array('id' => $recipientid));

$customdata = array(
    'recipient' => $recipient, 
    'referurl' => $referurl,
    'courseid' => $courseid
);
$mform = new block_messageteacher_message_form(null, $customdata);

if ($mform->is_cancelled()) {
  // form cancelled, redirect
    redirect($referurl);
    exit();
} else if (($data = $mform->get_data())) {
    try { 
        $mform->process($data);
    } catch (messageteacher_no_recipient_exception $e) {
        if ($ajax) {
            header('HTTP/1.1 400 Bad Request');
            die($e->getMessage());
        } else {
            throw $e;
        }
    } catch (messageteacher_message_failed_exception $e) {
        if ($ajax) {
            header('HTTP/1.1 500 Internal Server Error');
            die($e->getMessage());
        } else {
            throw $e;
        }
    }
    if ($ajax) {
        echo json_encode(array('state' => 1, 'output' => get_string('messagesent', 'block_messageteacher'));
    } else {
        redirect($data->referurl);
    }
    exit();
} else {
        
    // form has not been submitted, just display it
    if ($ajax) {
        ob_start();
        $mform->display();
        $form = ob_get_clean();
        if (strpos($form, '</script>') !== false) {
            $outputparts = explode('</script>', $form);
            $output = $outputparts[1];
            $script = str_replace('<script type="text/javascript">', '', $outputparts[0]);
        } else {
            $output = $form;
        }

        // Now it gets a bit tricky, we need to get the libraries and init calls for any Javascript used
        // by the form element plugins.
        $headcode = $PAGE->requires->get_head_code($PAGE, $OUTPUT);
        $loadpos = strpos($headcode, 'M.yui.loader');
        $cfgpos = strpos($headcode, 'M.cfg');
        $script .= substr($headcode, $loadpos, $cfgpos-$loadpos);
        $endcode = $PAGE->requires->get_end_code();
        $script .= preg_replace('/<\/?(script|link)[^>]*>/', '', $endcode);

        echo json_encode(array('state' => 0, 'output' => $output, 'script' => $script));

    } else {
        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }
}
