import ModalFactory from 'core/modal_factory';
import ModalForm from 'core_form/modalform';
import * as Str from 'core/str';

let modalForm = null;
let responseModal = null;
let contextid = null;
let appendurl = false;

/**
 * Display the form modal and load the form.
 *
 * @param {Event} e
 */
async function showForm(e) {
    e.preventDefault();
    M.util.js_pending('block_messageteacher_show');

    const link = e.currentTarget;
    modalForm = new ModalForm({
        formClass: 'block_messageteacher\\message_form',
        args: {
            contextid: contextid,
            appendurl: appendurl,
            referurl: link.dataset.referurl,
            courseid: link.dataset.courseid,
            recipientid: link.dataset.recipientid
        },
        modalConfig: {
            title: await Str.get_string('pluginname', 'block_messageteacher')
        },
        saveButtonText: await Str.get_string('send', 'block_messageteacher'),
        returnFocus: link
    });
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, submitForm);
    await modalForm.show();
    M.util.js_complete('block_messageteacher_show');
}

/**
 * Send the message entered into the form using a core webservice.
 *
 * @param {Event} e
 */
async function submitForm(e) {
    M.util.js_pending('block_messageteacher_send');
    if (e.detail < 1) {
        responseModal.setBody(e.errormessage);
    } else {
        const sent = await Str.get_string('messagesent', 'block_messageteacher');
        responseModal.setBody(sent);
    }
    responseModal.show();
    M.util.js_complete('block_messageteacher_send');
}

/**
 * Create modals.
 *
 * Create a Save/Cancel modal to display the form, and a Cancel modal for confirmation/error messages.
 */
export const init = async function() {

    const title = await Str.get_string('pluginname', 'block_messageteacher');

    responseModal = await ModalFactory.create({
        type: ModalFactory.types.DEFAULT,
        title: title
    });

    var links = document.querySelectorAll('.messageteacher_link');
    for (var i = 0; i < links.length; i++) {
        if (contextid === null) {
            contextid = links[i].parentElement.parentElement.dataset.contextid;
            appendurl = links[i].parentElement.parentElement.dataset.appendurl;
        }
        links[i].addEventListener('click', showForm);
    }
};
