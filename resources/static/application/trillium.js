var Trillium = {
    terminal: {
        name: 'trillium',
        selector: 'body',
        echo_greeting: function (term) {
            term.echo('<div id="trillium_greeting"></div>', {raw: true});
        },
        create: function () {
            $(this.selector).terminal(
                function(command, term) {
                    if (Trillium.terminal.commands.hasOwnProperty(command)) {
                        Trillium.terminal.commands[command](term)
                    } else {
                        term.echo(Trillium.terminal.name + ': ' + command + ': command not found');
                    }
                },
                {
                    greetings: null,
                    onInit: function (term) {
                        Trillium.terminal.echo_greeting(term)
                    },
                    onClear: function (term) {
                        Trillium.terminal.echo_greeting(term)
                    },
                    onBlur: function () {
                        return false
                    },
                    prompt: "[anonymous@" + Trillium.terminal.name + "] >>> "
                }
            );
        },
        commands: {
            'login': function (term) {
                var panel_username = 'anonymous';
                term.push(
                    function(cmd, term) {
                        if (cmd == 'help') {
                            term.echo('type "ping" it will display "pong"');
                        } else if (cmd == 'ping') {
                            term.echo('pong');
                        } else {
                            term.echo('unknown command "' + cmd + '"');
                        }
                    },
                    {
                        greetings: null,
                        name: 'panel',
                        prompt: function (callback) {
                            callback('['+ panel_username + '@' + Trillium.terminal.name + '] >>> ')
                        },
                        onBlur: function () {
                            return false
                        },
                        login: function(username, password, callback) {
                            $.ajax(
                                TrilliumUrlGenerator.generate('user.sign.in.check'),
                                {
                                    async: false,
                                    cache: false,
                                    data: {'_username': username, '_password': password},
                                    dataType: 'json',
                                    type: 'POST'
                                }
                            ).done(
                                function (data) {
                                    if (data.hasOwnProperty('username') && data.hasOwnProperty('error')) {
                                        term.error('Unable to login with username "' + data['username'] + '".');
                                        term.error('The following error has occurred: "' + data['error'] + '".');
                                        callback(null);
                                    } else if (data.hasOwnProperty('token')) {
                                        panel_username = username;
                                        callback(data.token);
                                    } else {
                                        console.log(data);
                                        term.error('Unknown error');
                                        callback(null);
                                    }
                                }
                            ).fail(
                                function (jqXHR, textStatus, errorThrown) {
                                    console.log(jqXHR, textStatus, errorThrown);
                                    callback(null);
                                }
                            );
                        }
                    }
                );
            }
        }
    },
    run: function () {
        this.terminal.create()
    }
};
$(document).ready(function() {
    Trillium.run()
});