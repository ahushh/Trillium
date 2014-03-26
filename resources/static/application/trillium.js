var Trillium = {
    terminal: {
        name: 'Trillium',
        selector: 'body',
        echo_greeting: function (term) {
            term.echo('<div id="trillium_greeting"></div>', {raw: true});
        },
        create: function () {
            $(this.selector).terminal(
                function(command, term) {
                    switch (command) {
                        default:
                            term.echo(Trillium.terminal.name + ': ' + command + ': command not found');
                            break;
                    }
                },
                {
                    greetings: null,
                    onInit: function (term) {
                        Trillium.terminal.echo_greeting(term)
                    },
                    onClear: function (term) {
                        Trillium.terminal.echo_greeting(term)
                    },
                    prompt: "[guest@" + Trillium.terminal.name + "] >>> "
                }
            );
        }
    },
    run: function () {
        this.terminal.create()
    }
};
$(document).ready(function() {
    Trillium.run()
});