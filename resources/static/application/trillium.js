var Trillium = {
    terminal: {
        name: 'trillium',
        commands: {
            main: {},
            panel: {}
        }
    }
};
$(document).ready(function() {
    $('body').terminal(
        function(command, term) {
            if (Trillium.terminal.commands.main.hasOwnProperty(command)) {
                Trillium.terminal.commands.main[command](term)
            } else {
                term.echo(Trillium.terminal.name + ': ' + command + ': command not found');
            }
        },
        {
            greetings: null,
            onInit: function (term) {
                term.echo('<div id="trillium_greeting"></div>', {raw: true});
            },
            onClear: function (term) {
                term.echo('<div id="trillium_greeting"></div>', {raw: true});
            },
            onBlur: function () {
                return false
            },
            prompt: "[anonymous@" + Trillium.terminal.name + "] >>> "
        }
    );
});