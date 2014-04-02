var Trillium = {
    terminal: {
        name: 'trillium',
        commands: {
            main: {},
            panel: {}
        },
        help: {
            main: {},
            panel: {}
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
                if (hr.hasOwnProperty('responseJSON') && hr.responseJSON.hasOwnProperty('error')) {
                    var error = hr.responseJSON.error;
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
    },
    urlGenerator: {
        routes: {},
        basePath: '',
        generate: function (name, params) {
            if (this.routes[name]) {
                params = params == undefined ? {} : params;
                var route        = this.routes[name],
                    requirements = route.requirements,
                    defaults     = route.defaults,
                    variables    = route.variables,
                    result       = route.path,
                    val;
                for (var param in variables) {
                    param = variables[param];
                    val = params[param] ? params[param] : defaults[param];
                    if (val === undefined) {
                        throw 'Missing "' + param + '" parameter for route "'+name+'"!';
                    }
                    if (requirements.hasOwnProperty(param) && !new RegExp(requirements[param]).test(val)) {
                        throw 'Parameter "' + param + '" for route "' + name + '" must pass "' + requirements[param] + '" test!';
                    }
                    result = result.replace('{' + param + '}', val);
                }

                return (window.location.protocol + '//' + window.location.hostname + this.basePath + result).replace(/\/$/, '');
            } else {
                throw 'Undefined route "' + name + '"!';
            }
        }
    }
};
$(document).ready(function() {
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