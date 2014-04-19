function Trillium(systemSettings, routes, basePath) {
    var self = this;
    var commands = {
        help: {
            run: function (term) {
                var output = '', cmdName;
                for (var cmd in commands) {
                    cmdName = cmd;
                    cmd = commands[cmd];
                    if (cmd.isAvailable && cmd.summary) {
                        output += '<tr><td>' + cmdName + '</td><td>-</td><td>' + cmd.summary + '</td></tr>';
                    }
                }
                if (output.length != 0) {
                    output = 'Available commands: <table>' + output + '</table><br />';
                }
                output += 'Key bindings: ' +
                '<ul>' +
                '<li>TAB — tab completion</li>' +
                '<li>Shift+Enter — insert new line</li>' +
                '<li>Up Arrow/CTRL+P — show previous command from history</li>' +
                '<li>Down Arrow/CTRL+N — show next command from history</li>' +
                '<li>CTRL+R — Reverse Search through command line history</li>' +
                '<li>CTRL+G — Cancel Reverse Search</li>' +
                '<li>CTRL+L — Clear terminal</li>' +
                '<li>CTRL+K — remove the text after the cursor and save it in kill area</li>' +
                '<li>CTRL+U — remove the text before the cursor and save it in kill area</li>' +
                '<li>CTRL+H — remove text to the end of the line</li>' +
                '<li>ALT+D — remove one word after the cursor</li>' +
                '<li>CTRL+D — run previous interpreter from the stack or call logout.<br />' +
                'If terminal is using authentication and current interpreter is the first one.<br />' +
                'It also cancel all ajax call, if terminal is paused, and resume it.</li>' +
                '</ul>';
                term.echo(output, {raw: true});
            },
            isAvailable: true
        }
    };
    // Show/Hide secured commands
    var toggleSecuredCommands = function (flag) {
        for (var c in commands) {
            if (commands[c].secured) {
                commands[c].isAvailable = flag;
            }
        }
    };
    var commandNotFound = function (term, commandName) {
        term.error(self.host + ': ' + commandName + ': command not found');
    };
    var requiredCommandProperties = ['run', 'help', 'summary', 'secured', 'isAvailable'];
    this.host = 'trillium';
    this.username = false;
    this.greeting = function (term) {
        term.echo('<div id="trillium_greeting"></div>', {raw: true});
    };
    this.addCommand = function (name, command) {
        if (!$.isPlainObject(command)) {
            console.log(command);
            throw 'Command "' + name + '" is not object';
        }
        for (var property in requiredCommandProperties) {
            property = requiredCommandProperties[property];
            if (!command.hasOwnProperty(property)) {
                throw 'Command must have "' + property + '" property';
            }
        }
        commands[name] = command;
    };
    // Shows/hides a command
    this.showCommand = function (name, flag) {
        if (commands.hasOwnProperty(name)) {
            commands[name].isAvailable = flag;
        }
    };
    // Perform on logout
    this.logout = function (term) {
        self.username = false;
        self.prompt(term.set_prompt);
        self.showCommand('login', true);
        toggleSecuredCommands(false);
    };
    // Perform on login
    this.login = function (username, term) {
        self.username = username;
        self.prompt(term.set_prompt);
        self.showCommand('login', false);
        toggleSecuredCommands(true);
    };
    // Terminal prompt
    this.prompt = function (callback) {
        var username = self.username === false ? 'anonymous' : self.username;
        var path = self.board.current + (self.thread.current ? '/' + self.thread.current : '');
        callback("[" + username + "@" + self.host + "] -> [" + path + "] >>> ");
    };
    // Creates a confirm terminal
    this.termConfirm = function (term, onConfirm, onCancel) {
        term.push(
            function (answer) {
                switch (answer) {
                    case 'y':
                        if ($.isFunction(onConfirm)) {
                            onConfirm();
                        }
                        term.pop();
                        break;
                    case 'n':
                        if ($.isFunction(onCancel)) {
                            onCancel();
                        }
                        term.pop();
                        break;
                    default:
                        term.error('You can say only "yes" or "no"!');
                }
            },
            {prompt: 'Are you sure? [y/n]'}
        );
        return term;
    };
    // Settings
    this.settings = {
        // System settings
        system: systemSettings,
        // User settings
        user: {},
        // Returns user settings from cookies
        load: function () {
            var key, value, settings = {};
            for (key in this.system) {
                value = $.cookie(key);
                if (value) {
                    settings[key] = value;
                } else {
                    settings[key] = this.system[key];
                }
            }
            return settings;
        },
        // Validates settings
        validate: function (settings, done, fail) {
            var url = self.urlGenerator.generate('settings.validate');
            var options = {async: false, data: {'settings': settings}, dataType: 'json', type: 'POST'};
            $.ajax(url, options).done(done).fail(fail);
        }
    };
    // URL Generator
    this.urlGenerator = {
        routes: routes,
        basePath: basePath,
        generate: function (name, params) {
            if (this.routes[name]) {
                params = !$.isPlainObject(params) ? {} : params;
                var route = this.routes[name],
                    requirements = route['requirements'],
                    defaults = route['defaults'],
                    variables = route['variables'],
                    result = route['path'],
                    val;
                for (var param in variables) {
                    param = variables[param];
                    val = params[param] ? params[param] : defaults[param];
                    if (val === undefined) {
                        throw 'Missing "' + param + '" parameter for route "' + name + '"!';
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
        },
        raw: function (path) {
            return window.location.protocol + '//' + window.location.hostname + this.basePath + '/' + path;
        }
    };
    // jQueryXHR response handlers
    this.responseHandler = {
        success: function (term, data) {
            if (data.hasOwnProperty('success')) {
                term.echo(data.success);
            } else {
                console.log(data);
                term.error('Unknown response type');
            }
        },
        fail: function (term, hr, textStatus, errorThrown) {
            term.error('Whoops, looks like something went wrong.');
            var response = hr.hasOwnProperty('responseJSON') ? hr['responseJSON'] : false;
            if (response && response.hasOwnProperty('error')) {
                var error = response.error;
                if ($.isArray(error) || $.isPlainObject(error)) {
                    for (var e in error) {
                        if (error.hasOwnProperty(e)) {
                            term.error(error[e]);
                        }
                    }
                } else {
                    term.error(error);
                }
                // Show trace, if exists
                if (response.hasOwnProperty('trace')) {
                    var trace = response['trace'];
                    var output = '';
                    for (var t in trace) {
                        t = trace[t];
                        if (t['class']) {
                            output += t['class'];
                        }
                        if (t['function']) {
                            if (t['class']) {
                                output += '\n';
                            }
                            output += 'at ' + t['class'] + t['type'] + t['function'] + '(' + t['args'].join(', ') + ')';
                        }
                        if (t['file'] && t['line']) {
                            output += '\nin ' + t['file'] + ':' + t['line'];
                        }
                        output += '\n\n';
                    }
                    term.echo(output);
                }
            } else {
                console.log(hr, textStatus, errorThrown);
                term.error('Unknown error');
            }
        }
    };
    // Boards
    this.board = {
        current: '~',
        list: function (term) {
            $.ajax(
                self.urlGenerator.generate('board.listing'),
                {dataType: 'json'}
            ).done(
                function (data) {
                    var output = '';
                    if (data.length == 0) {
                        output += 'List is empty';
                    } else {
                        var board, i;
                        for (i = 0; i < data.length; i++) {
                            board = data[i];
                            output += '/' + board['name'] + '/ - ' + board['summary'];
                            if (i + 1 != data.length) {
                                output += '\n';
                            }
                        }
                    }
                    term.echo(output);
                }
            ).fail(
                function (jqXhr, textStatus, errorThrown) {
                    self.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                }
            );
        },
        get: function (boardName, term, onSuccess) {
            $.ajax(
                self.urlGenerator.generate('board.get', {'name': boardName}),
                {async: false, dataType: 'json'}
            ).done(
                function (data) {
                    if ($.isFunction(onSuccess)) {
                        onSuccess(data);
                    }
                }
            ).fail(
                function (jqXhr, textStatus, errorThrown) {
                    self.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                }
            );
        }
    };
    // Threads
    this.thread = {
        current: '',
        get: function (id, term, onSuccess) {
            $.ajax(
                self.urlGenerator.generate('thread.get', {'id': id}),
                {async: false, dataType: 'json'}
            ).done(
                function (data) {
                    if ($.isFunction(onSuccess)) {
                        onSuccess(data);
                    }
                }
            ).fail(
                function (jqXhr, textStatus, errorThrown) {
                    self.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                }
            );
        }
    };
    // Echoes captcha
    this.captcha = function (term) {
        term.echo(
            '<img src="' + self.urlGenerator.generate('captcha') + '?' + Math.random() + '" alt="Captcha" />',
            {raw: true}
        );
    };
    // Fileupload
    this.fileupload = function (data, term, after) {
        // MKTHREAD
        var fileupload = $('<input style="display: none" id="fileupload" type="file" name="image" />');
        fileupload.on('change', function () {
            var files = $(this).prop('files');
            if (files && files.length) {
                data.append('file', files[0]);
            } else {
                term.error('No files given');
            }
            if ($.isFunction(after)) {
                after();
            }
        });
        return fileupload;
    };
    // Creates a terminal
    this.run = function (selector) {
        // Load and validate settings
        var settings = self.settings.load();
        self.settings.validate(
            settings,
            function () {
                self.settings.user = settings;
            },
            function () {
                self.settings.user = self.settings.system;
            }
        );
        $(selector).terminal(
            function (command, term) {
                if (!command) {
                    return;
                }
                command = $.terminal.parseCommand(command);
                if (!commands.hasOwnProperty(command.name)) {
                    commandNotFound(term, command.name);
                    return;
                }
                var cmd = commands[command.name];
                if (!cmd.isAvailable) {
                    commandNotFound(term, command.name);
                    return;
                }
                if (command.args.length > 0 && (command.args[0] == '--help' || command.args[0] == '-h')) {
                    // Try to get help for given command
                    if (cmd.help) {
                        term.echo(cmd.help, {raw: true});
                    } else {
                        term.error('Help is not available');
                    }
                    return;
                }
                cmd.run(term, command.args, command.rest);
            },
            {
                greetings: null,
                onInit: function (term) {
                    self.greeting(term);
                    // Autologin
                    $.ajax(
                        self.urlGenerator.generate('user.is_authorized'),
                        {dataType: 'json', async: false}
                    ).done(
                        function (data) {
                            if (data.hasOwnProperty('isAuthorized') && data.hasOwnProperty('username')) {
                                if (data['isAuthorized']) {
                                    self.login(data['username'], term);
                                } else {
                                    self.logout(term);
                                }
                            } else {
                                console.log(data);
                                term.error('Autologin failed: Unknown error');
                            }
                        }
                    );
                },
                onClear: self.greeting,
                onBlur: function () {
                    return false;
                },
                prompt: self.prompt,
                completion: function (term, string, callback) {
                    var commandsNames = [];
                    var command = $.terminal.parseCommand(term.get_command());
                    if (command.args.length == 0 && !commands.hasOwnProperty(command.name)) {
                        for (var c in commands) {
                            if (commands[c].isAvailable) {
                                commandsNames.push(c);
                            }
                        }
                    }
                    callback(commandsNames)
                }
            }
        )
    };
}
var app = new Trillium(generated.settings, generated.routes, generated.basePath);
$('document').ready(
    function () {
        app.run('body');
    }
);