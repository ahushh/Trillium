Trillium.terminal.commands.panel.adduser = function (term) {
    var userData = {username: '', password: '', roles: ''};
    term.push(
        function (confirm) {
            if (confirm == 'y') {
                term.echo('Sending request...');
                $.ajax(
                    Trillium.urlGenerator.generate('user.create'),
                    {async: false, data: userData, dataType: 'json', type: 'POST'}
                ).done(
                    function (data) {
                        if (data.hasOwnProperty('success')) {
                            term.echo(data.success);
                        } else {
                            for (var error in data) {
                                if (data.hasOwnProperty(error)) {
                                    term.error(data[error]);
                                }
                            }
                        }
                    }
                ).fail(
                    function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR, textStatus, errorThrown);
                        term.error('Unknown error');
                    }
                );
            }
            term.pop();
        },
        {prompt: 'Are you sure? [y/n]: '}
    ).push(
        function (roles) {
            userData.roles = roles;
            term.pop();
        },
        {prompt: 'Roles: '}
    ).push(
        function (password) {
            userData.password = password;
            term.pop();
        },
        {prompt: 'Password: '}
    ).push(
        function (username) {
            userData.username = username;
            term.pop()
        },
        {prompt: 'Username: '}
    );
};