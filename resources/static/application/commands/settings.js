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
            if (Trillium.settings.user.hasOwnProperty(args[1])) {
                // TODO: validation
                $.cookie(args[1], args[2], {expires: 365});
                // Reload settings
                Trillium.settings.load();
            } else {
                term.error('Option "' + args[1] + '" is not available');
            }
        } else {
            term.error('No key or value given');
        }
    } else {
        term.error('Unknown argument "' + args[0] + '"');
    }
};
Trillium.terminal.help.main.settings = 'User settings.\n' +
'Usage: settings [command] [key] [value]\n' +
'Available commands:\n' +
'set - Sets a new value for given key\n' +
'Example: settings set locale ru';
Trillium.terminal.description.main.settings = 'User settings';