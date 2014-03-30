Trillium.terminal.commands.main.panel = function (term) {
    var credentials = {_username: null, _password: null};
    var isAuthorized = false;
    var controlPanel = function () {
        term.push(
            function (command, term) {
                if (Trillium.terminal.commands.panel[command]) {
                    Trillium.terminal.commands.panel[command](term)
                } else {
                    term.echo(Trillium.terminal.name + ': ' + command + ': command not found');
                }
            },
            {
                name: 'panel',
                prompt: function (callback) {
                    callback('[' + credentials._username + '@' + Trillium.terminal.name + '] >>> ')
                }
            }
        );
    };
    $.ajax(
        Trillium.urlGenerator.generate('user.is_authorized'),
        {dataType: 'json', async: false}
    ).done(
        function (data) {
            if (data.hasOwnProperty('isAuthorized') && data.hasOwnProperty('username')) {
                isAuthorized = data['isAuthorized'];
                credentials._username = data['username'];
                controlPanel();
            } else {
                console.log(data);
                term.error('Unknown error');
            }
        }
    );
    if (!isAuthorized) {
        term
            .push(function (password) {
                credentials._password = password;
                $.ajax(Trillium.urlGenerator.generate('user.sign.in.check'),
                    {async: false, data: credentials, dataType: 'json', type: 'POST'}
                ).done(
                    function (data) {
                        if (data.hasOwnProperty('username') && data.hasOwnProperty('error')) {
                            term.error('Unable to login with username "' + data['username'] + '".');
                            term.error('The following error has occurred: "' + data['error'] + '".');
                            term.pop();
                        } else if (data.hasOwnProperty('success')) {
                            term.echo(data.success);
                            term.pop();
                            controlPanel();
                            credentials._password = null;
                        } else {
                            console.log(data);
                            term.error('Unknown error');
                            term.pop();
                        }
                    }
                ).fail(
                    function (jqXHR, textStatus, errorThrown) {
                        term.error('Unknown error');
                        console.log(jqXHR, textStatus, errorThrown);
                        term.pop();
                    }
                );
            }, {prompt: 'Password: '})
            .set_mask(true)
            .push(function (username) {
                credentials._username = username;
                term.pop();
            }, {prompt: 'Username: '})
            .set_mask(false);
    }
};