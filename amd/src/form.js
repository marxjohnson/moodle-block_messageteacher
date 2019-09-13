
define(['core/fragment', 'core/ajax', 'core/modal_factory', 'core/modal_events'],
        function(Fragment, Ajax, ModalFactory, ModalEvents) {

    var t = {
        modal: null,

        responseModal: null,

        contextid: null,

        appendurl: false,

        /**
         * Create modals.
         *
         * Create a Save/Cancel modal to display the form, and a Cancel modal for confirmation/error messages.
         */
        init: function() {

            ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: M.util.get_string('pluginname', 'block_messageteacher')
            }).then(function(modal) {
                t.responseModal = modal;
            });

            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: M.util.get_string('pluginname', 'block_messageteacher')
            }).then(function(modal) {
                t.modal = modal;
                t.modal.setLarge();
                t.modal.setSaveButtonText(M.util.get_string('send', 'block_messageteacher'));

                t.modal.getRoot().on(ModalEvents.hidden, function() {
                    t.modal.setBody('');
                }.bind(this));

                // We catch the modal save event, and use it to submit the form inside the modal.
                // Triggering a form submission will give JS validation scripts a chance to check for errors.
                t.modal.getRoot().on(ModalEvents.save, function(e) {
                    e.preventDefault();
                    t.modal.getRoot().find('form').submit();
                });
                // We also catch the form submit event and use it to submit the form with ajax.
                t.modal.getRoot().on('submit', 'form', t.submitForm);

                var links = document.querySelectorAll('.messageteacher_link');
                for (var i = 0; i < links.length; i++) {
                    if (t.contextid === null) {
                        t.contextid = links[i].parentElement.parentElement.dataset.contextid;
                        t.appendurl = links[i].parentElement.parentElement.dataset.appendurl;
                    }
                    links[i].addEventListener('click', t.showForm);
                }
            });
        },

        /**
         * Get the page fragment for the form.
         *
         * @param {array} params
         * @returns {*|Deferred}
         */
        getForm: function(params) {
            return Fragment.loadFragment('block_messageteacher', 'message_form', t.contextid, params);
        },

        /**
         * Display the form modal and load the form.
         *
         * @param {Event} e
         */
        showForm: function(e) {
            e.preventDefault();
            t.modal.show();
            t.modal.setBody(t.getForm(e.currentTarget.dataset));
        },

        /**
         * Send the message entered into the form using a core webservice.
         *
         * @param {Event} e
         */
        submitForm: function(e) {
            e.preventDefault();
            var recipientid = t.modal.getRoot().find('[name=recipientid]').val();
            var message = t.modal.getRoot().find('[name=message]').val();
            if (t.appendurl) {
                message += "\n\n" + t.modal.getRoot().find('[name=referurl]').val();
            }

            M.util.js_pending('block_messageteacher_send');

            Ajax.call([{
                methodname: 'core_message_send_instant_messages',
                args: {
                    'messages': [
                        {
                            'touserid': recipientid,
                            'text': message
                        }
                    ]
                },
                done: function(response) {
                    t.modal.hide();
                    if (response.msgid === -1) {
                        t.responseModal.setBody(response.errormessage);
                    } else {
                        t.responseModal.setBody(M.util.get_string('messagesent', 'block_messageteacher'));
                    }
                    t.responseModal.show();
                    M.util.js_complete('block_messageteacher_send');
                },
                fail: function(ex) {
                    t.modal.hide();
                    t.responseModal.setBody(ex.message);
                    t.responseModal.show();
                    M.util.js_complete('block_messageteacher_send');
                }
            }]);
        }
    };

    return t;
});