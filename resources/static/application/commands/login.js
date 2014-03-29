Trillium.terminal.commands.main.login = function (term) {
    var panel_username = 'anonymous';
    term.push(
        function (command, term) {
            if (Trillium.terminal.commands.panel.hasOwnProperty(command)) {
                Trillium.terminal.commands.panel[command](term)
            } else {
                term.echo(Trillium.terminal.name + ': ' + command + ': command not found');
            }
        },
        {
            greetings: null,
            name: 'panel',
            prompt: function (callback) {
                callback('[' + panel_username + '@' + Trillium.terminal.name + '] >>> ')
            },
            onBlur: function () {
                return false
            },
            onExit: function () {
                $.ajax(Trillium.urlGenerator.generate('user.sign.out'), {async: false});
            },
            login: function (username, password, callback) {
                $.ajax(
                    Trillium.urlGenerator.generate('user.sign.in.check'),
                    {
                        async: false,
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
};