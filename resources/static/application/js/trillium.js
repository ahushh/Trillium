var Trillium = {};
$(document).ready(function () {
    // Load and validate settings
    var settings = Trillium.settings.load();
    Trillium.settings.validate(
        settings,
        function () {
            Trillium.settings.user = settings
        },
        function () {
            Trillium.settings.user = Trillium.settings.system;
        }
    );
    // Create the terminal
    $('body').terminal(
        function (command, term) {
            Trillium.terminal.commandHandler(command, term, 'main');
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