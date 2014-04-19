app.addCommand('login', {
    summary: TermHelp.login.summary,
    help: TermHelp.login.help,
    secured: false,
    isAvailable: false,
    run: function (term) {
        var credentials = {_username: null, _password: null};
        term.push(
            function (password) {
                credentials._password = password;
                $.ajax(app.urlGenerator.generate('user.sign.in.check'),
                    {async: false, data: credentials, dataType: 'json', type: 'POST'}
                ).done(
                    function (data) {
                        if (data.hasOwnProperty('username') && data.hasOwnProperty('error')) {
                            term.error(data['error']);
                            term.pop();
                        } else if (data.hasOwnProperty('success')) {
                            term.echo(data.success);
                            term.pop();
                            credentials._password = null;
                            app.login(credentials._username, term);
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
            },
            {prompt: 'Password: '}
        ).set_mask(
            true
        ).push(
            function (username) {
                credentials._username = username;
                term.pop();
            },
            {prompt: 'Username: '}
        ).set_mask(
            false
        );
    }
});