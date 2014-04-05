Trillium.terminal.commands.main.skins = function (term) {
    $.ajax(
        Trillium.urlGenerator.generate('settings.skins'),
        {"async": false, "dataType": "json"}
    ).done(
        function (data) {
            term.echo('Available skins:');
            if (data.length == 0) {
                term.echo('List is empty');
            } else {
                for (var skin in data) {
                    if (data.hasOwnProperty(skin)) {
                        term.echo(data[skin]);
                    }
                }
            }
        }
    ).fail(
        function () {
            term.error('Unable to get skins')
        }
    );
};
Trillium.terminal.help.main.skins = Trillium.terminal.description.main.skins = 'Shows available skins';