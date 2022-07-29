import Fragment from 'core/fragment';
import Ajax from 'core/ajax';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import * as Str from 'core/str';

let modal = null;
let responseModal = null;
let contextid = null;
let appendurl = false;

/**
 * Get the page fragment for the form.
 *
 * @param {array} params
 * @returns {*|Deferred}
 */
function getForm(params) {
    return Fragment.loadFragment('block_messageteacher', 'message_form', contextid, params);
}

/**
 * Display the form modal and load the form.
 *
 * @param {Event} e
 */
function showForm(e) {
    e.preventDefault();
    modal.show();
    modal.setBody(getForm(e.currentTarget.dataset));
}

/**
 * Send the message entered into the form using a core webservice.
 *
 * @param {Event} e
 */
async function submitForm(e) {
    e.preventDefault();
    var recipientid = modal.getRoot().find('[name=recipientid]').val();
    var message = modal.getRoot().find('[name=message]').val();
    if (appendurl) {
        message += "\n\n" + modal.getRoot().find('[name=referurl]').val();
    }

    M.util.js_pending('block_messageteacher_send');

    try {
        const response = await Ajax.call([{
            methodname: 'core_message_send_instant_messages',
            args: {
                'messages': [
                    {
                        'touserid': recipientid,
                        'text': message
                    }
                ]
            }
        }])[0];

        modal.hide();
        if (response.msgid === -1) {
            responseModal.setBody(response.errormessage);
        } else {
            const sent = await Str.get_string('messagesent', 'block_messageteacher');
            responseModal.setBody(sent);
        }
        responseModal.show();
        M.util.js_complete('block_messageteacher_send');
    } catch (ex) {
        modal.hide();
        responseModal.setBody(ex.message);
        responseModal.show();
        M.util.js_complete('block_messageteacher_send');
    }
}

/**
 * Create modals.
 *
 * Create a Save/Cancel modal to display the form, and a Cancel modal for confirmation/error messages.
 */
export const init = async function() {

    const title = await Str.get_string('pluginname', 'block_messageteacher');
    const send = await Str.get_string('send', 'block_messageteacher');

    responseModal = await ModalFactory.create({
        type: ModalFactory.types.DEFAULT,
        title: title
    });

    modal = await ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: title
    });
    modal.setLarge();
    modal.setSaveButtonText(send);

    modal.getRoot().on(ModalEvents.hidden, function() {
        modal.setBody('');
    }.bind(this));

    // We catch the modal save event, and use it to submit the form inside the modal.
    // Triggering a form submission will give JS validation scripts a chance to check for errors.
    modal.getRoot().on(ModalEvents.save, function(e) {
        e.preventDefault();
        modal.getRoot().find('form').submit();
    });
    // We also catch the form submit event and use it to submit the form with ajax.
    modal.getRoot().on('submit', 'form', submitForm);

    var links = document.querySelectorAll('.messageteacher_link');
    for (var i = 0; i < links.length; i++) {
        if (contextid === null) {
            contextid = links[i].parentElement.parentElement.dataset.contextid;
            appendurl = links[i].parentElement.parentElement.dataset.appendurl;
        }
        links[i].addEventListener('click', showForm);
    }
};
