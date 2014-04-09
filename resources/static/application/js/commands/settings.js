app.addCommand(
    'settings',
    'User settings<br />' +
    'Usage: settings [command] [[key] [value]]...<br />' +
    'Available commands:<br />' +
    'set - Sets a new value for given key<br />' +
    'Example: settings set skin default',
    'User settings',
    function (term, args) {
        var command = args.length == 0 ? 'list' : (args[0] == 'set' ? 'set' : false);
        switch (command) {
            case 'list':
                var output = 'System settings: ';
                for (var key in app.settings.user) {
                    output += "\n" + key + ': ' + app.settings.user[key];
                }
                term.echo(output);
                break;
            case 'set':
                args = args.slice(1);
                if (args.length % 2 != 0) {
                    term.error('Each key must have a value!');
                    return ;
                }
                var settings = {};
                for (var k = 0, v = 1; v <= args.length; k += 2, v += 2) {
                    settings[args[k]] = args[v];
                }
                app.settings.validate(
                    settings,
                    function () {
                        for (var key in settings) {
                            $.cookie(key, settings[key], {expires: 365});
                            app.settings.user[key] = settings[key];
                        }
                        // Reload skin
                        var css_skin = app.urlGenerator.raw('static/' + app.settings.user.skin + '.css');
                        css_skin = $('<link id="css_skin" rel="stylesheet" type="text/css" href="' + css_skin + '" />');
                        $('#css_skin').replaceWith(css_skin);
                        term.echo('Settings updated');
                    },
                    function (jqXHR, textStatus, errorThrown) {
                        app.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
                    }
                );
                break;
            default:
                term.error('Wrong command');
        }
    },
    false,
    true
);