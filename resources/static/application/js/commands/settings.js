Trillium.terminal.commands.main.settings = function (term, args) {
    if (args.length == 0) {
        // Show settings
        for (var key in Trillium.settings.user) {
            if (Trillium.settings.user.hasOwnProperty(key)) {
                term.echo(key + ': ' + Trillium.settings.user[key]);
            }
        }
    } else if (args[0] == 'set') {
        // Update settings
        if (args.length > 2) {
            args = args.slice(1);
            var settings = {};
            for (var k = 0, v = 1; v <= args.length; k += 2, v += 2) {
                settings[args[k]] = args[v];
            }
            $.ajax(
                Trillium.urlGenerator.generate('settings.validate'),
                {async: false, data: {'settings': settings}, dataType: 'json', type: 'POST'}
            ).done(
                function (data) {
                    Trillium.terminal.responseHandler.success(term, data);
                    for (var key in settings) {
                        if (settings.hasOwnProperty(key)) {
                            $.cookie(key, settings[key], {expires: 365});
                        }
                    }
                    // Reload settings
                    Trillium.settings.load();
                    // Reload skin
                    var css_skin = Trillium.urlGenerator.raw('static/' + Trillium.settings.user['skin'] + '.css');
                    css_skin = $('<link id="css_skin" rel="stylesheet" type="text/css" href="' + css_skin + '" />');
                    $('#css_skin').replaceWith(css_skin);
                }
            ).fail(
                function (jqXHR, textStatus, errorThrown) {
                    Trillium.terminal.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
                }
            );
        } else {
            term.error('No key or value given');
        }
    } else {
        term.error('Unknown argument "' + args[0] + '"');
    }
};
Trillium.terminal.help.main.settings = 'User settings.\n' +
'Usage: settings [command] [[key] [value]]...\n' +
'Available commands:\n' +
'set - Sets a new value for given key\n' +
'Example: settings set locale ru timeshift 4';
Trillium.terminal.description.main.settings = 'User settings';