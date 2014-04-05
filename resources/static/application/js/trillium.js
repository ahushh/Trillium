var Trillium = {};
$(document).ready(function() {
    Trillium.settings.load();
    Trillium.settings.validate(
        Trillium.settings.user,
        function () {},
        function () {Trillium.settings.user = Trillium.settings.system;}
    );
    $('body').terminal(
        function(command, term) {
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