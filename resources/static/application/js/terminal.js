Trillium.terminal = {
    name: 'trillium',
        // Commands
        commands: {
        main: {},
        panel: {}
    },
    // Help messages for each command
    help: {
        main: {},
        panel: {}
    },
    // Summary for each command
    description: {
        main: {},
        panel: {}
    },
    baseHelpCommand: function (namespace, term) {
        var container = Trillium.terminal.description[namespace];
        if (container.length == 0) {
            term.error('Help is not available');
        } else {
            term.echo('Available commands: ');
            for (var command in container) {
                if (container.hasOwnProperty(command)) {
                    term.echo(command + ' - ' + container[command]);
                }
            }
        }
    },
    responseHandler: {
        success: function (term, data) {
            if (data.hasOwnProperty('success')) {
                term.echo(data.success);
            } else {
                console.log(data);
                term.error('Unknown response type');
            }
        },
        fail: function (term, hr, textStatus, errorThrown) {
            if (hr.hasOwnProperty('responseJSON') && hr['responseJSON'].hasOwnProperty('error')) {
                var error = hr['responseJSON'].error;
                if (error instanceof Array || error instanceof Object) {
                    for (var e in error) {
                        if (error.hasOwnProperty(e)) {
                            term.error(error[e]);
                        }
                    }
                } else {
                    term.error(error);
                }
            } else {
                console.log(hr, textStatus, errorThrown);
                term.error('Unknown error');
            }
        }
    },
    commandHandler: function (command, term, namespace) {
        if (!command) {
            return ;
        }
        var container = Trillium.terminal.commands;
        if (namespace) {
            if (container.hasOwnProperty(namespace)) {
                container = container[namespace];
            } else {
                term.error('Undefined namespace "' + namespace + '"');
            }
        }
        command = $.terminal.parseCommand(command);
        if (container.hasOwnProperty(command.name)) {
            if (command.args.length > 0 && (command.args[0] == '--help' || command.args[0] == '-h')) {
                // Show help message, if exists
                if (Trillium.terminal.help[namespace][command.name]) {
                    term.echo(Trillium.terminal.help[namespace][command.name]);
                } else {
                    term.error('Help message for this command is not exists');
                }
            } else {
                // Run command
                container[command.name](term, command.args);
            }
        } else {
            term.error(Trillium.terminal.name + ': ' + command.name + ': command not found');
        }
    }
};