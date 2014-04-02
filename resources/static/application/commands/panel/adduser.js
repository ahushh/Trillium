Trillium.terminal.commands.panel.adduser = function (term) {
    var userData = {username: '', password: '', roles: ''};
    term.push(
        function (confirm) {
            if (confirm == 'y') {
                $.ajax(
                    Trillium.urlGenerator.generate('user.create'),
                    {async: false, data: userData, dataType: 'json', type: 'POST'}
                ).done(
                    function (data) {
                        Trillium.terminal.responseHandler.success(term, data);
                    }
                ).fail(
                    function (jqXHR, textStatus, errorThrown) {
                        Trillium.terminal.responseHandler.fail(term, jqXHR, textStatus, errorThrown)
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
Trillium.terminal.help.panel.adduser = 'Adds a user interactively';