M.block_messageteacher = M.block_messageteacher || {};

M.block_messageteacher.form = {
    overlay: '',

    init: function() {
        // Create an overlay (like the ones that Help buttons display) for
        // showing output from asynchronous call.
        html = '<a id="block_messageteacher_output_header" href="#block_messageteacher">'
            +'<img src="'+M.util.image_url('t/delete', 'moodle')+'" /></a>';
        this.overlay_close = Y.Node.create(html);
        this.overlay = new Y.Overlay({
            headerContent: this.overlay_close,
            bodyContent: '',
            id: 'block_messageteacher_output',
            width:'400px',
            visible : false,
            centered: true,
            contrain: true
        });

        this.overlay.render(Y.one(document.body));
        this.overlay_close.on('click', this.overlay.hide, this.overlay);
        Y.on("key", this.hide_response, this.overlay_close, "down:13", this);
        this.overlay_close.on('click', this.hide_response, this);

        Y.all('.messageteacher_link').on('click', this.show_form, this);
    },

    /**
     * Displays the specifies response in the overlay
     *
     * @param data The text to display
     */
    show_response: function(data) {
        // Create a node from the data
        this.overlay.set('bodyContent', Y.Node.create(data));
        this.overlay.set("align", {
            node: Y.one('.block_messageteacher'),
            points: [
                Y.WidgetPositionAlign.TL,
                Y.WidgetPositionAlign.RC
            ]
        }); // Align the overlay with the submit button
        this.overlay.show(); // Show the overlay
    },

    hide_response: function() {
        this.overlay.hide();
    },

    /**
     *
     */
    show_form: function(e) {
        e.preventDefault();
        this.xhr = Y.io(e.currentTarget.get('href'), {
            method: 'GET',
            context: this,
            on: {
                success: function(id, o) {
                    response = Y.JSON.parse(o.responseText);
                    this.show_response(response.output);
                    if (response.script.length > 0) {
                        script = Y.one('#messageteacher_dynamic_script');
                        if (script) {
                            script.remove();
                        }
                        el = document.createElement('script');
                        el.id = 'ilp_dynamic_script';
                        el.textContent = response.script;
                        document.body.appendChild(el);
                    }
                    Y.one('#mform1').on('submit', M.block_messageteacher.form.submit_form);
                    Y.one('#id_message').focus(); // Focus the message box
                },
                failure: function(id, o) {
                    this.show_response(o.responseText);
                }
            }               
        });
    },

    submit_form: function(e) {
        e.preventDefault();
        var mform = document.getElementById('mform1');
        
        this.xhr = Y.io(e.target.get('action'), {
            method: 'POST',
            context: this,
            form: mform,
            on: {
                success: function(id, o) {
                    response = Y.JSON.parse(o.responseText);
                    M.block_messageteacher.form.show_response(response.output);
                },
                failure: function(id, o) {
                    M.block_messageteacher.form.show_response(o.responseText);
                }
            }               
        });
    }
}
