app.addCommand('user', {
    summary: TermHelp.user.summary,
    help: TermHelp.user.help,
    secured: true,
    isAvailable: false,
    run: function (term, args) {
        if (args.length == 0) {
            term.error('No such argument');
            return;
        }
        var username = args.length > 1 && args[1] ? args[1] : null;
        switch (args[0]) {
            case '-c': // Create user
                var userData = {username: '', password: '', roles: ''};
                app.termConfirm(term,
                    function () {
                        $.ajax(
                            app.urlGenerator.generate('user.create'),
                            {async: false, data: userData, dataType: 'json', type: 'POST'}
                        ).done(
                            function (data) {
                                app.responseHandler.success(term, data);
                            }
                        ).fail(
                            function (jqXHR, textStatus, errorThrown) {
                                app.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
                            }
                        );
                    }
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
                break;
            case '-r': // Update roles
                var roles = args.slice(2);
                if (username === null) {
                    term.error('No username given');
                } else if (roles.length == 0) {
                    term.error('No roles given');
                } else {
                    $.ajax(
                        app.urlGenerator.generate('user.edit.roles', {'username': username}),
                        {async: false, data: {'roles': roles}, dataType: 'json', type: 'POST'}
                    ).done(
                        function (data) {
                            app.responseHandler.success(term, data);
                        }
                    ).fail(
                        function (jqXHR, textStatus, errorThrown) {
                            app.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
                        }
                    );
                }
                break;
            case '-p': // Update password
                var passwords = {};
                term.push(
                    function (string) {
                        passwords['confirm'] = string;
                        $.ajax(
                            app.urlGenerator.generate('user.edit.password', username !== null ? {'username': username} : {}),
                            {
                                async: false,
                                data: {
                                    '_password_old': passwords['old'],
                                    '_password_new': passwords['new'],
                                    '_password_confirm': passwords['confirm']
                                },
                                dataType: 'json',
                                type: 'POST'
                            }
                        ).done(
                            function (data) {
                                app.responseHandler.success(term, data);
                            }
                        ).fail(
                            function (jqXHR, textStatus, errorThrown) {
                                app.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
                            }
                        ).always(
                            function () {
                                term.pop();
                                term.set_mask(false);
                            }
                        );
                    },
                    {prompt: 'Confirm password: '}
                ).set_mask(
                    true
                ).push(
                    function (string) {
                        passwords['new'] = string;
                        term.pop();
                    },
                    {prompt: 'New password: '}
                ).set_mask(
                    true
                ).push(
                    function (string) {
                        passwords['old'] = string;
                        term.pop();
                    },
                    {prompt: 'Old password: '}
                ).set_mask(
                    true
                );
                break;
            case '-d': // Delete user
                if (username == null) {
                    term.error('No username given');
                } else {
                    app.termConfirm(term, function () {
                        $.ajax(
                            app.urlGenerator.generate('user.remove', {'username': username}),
                            {dataType: 'json'}
                        ).done(
                            function (data) {
                                app.responseHandler.success(term, data);
                            }
                        ).fail(
                            function (jqXHR, textStatus, errorThrown) {
                                app.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
                            }
                        );
                    });
                }
                break;
            case '-l':
                $.ajax(
                    app.urlGenerator.generate('user.listing'),
                    {dataType: 'json'}
                ).done(
                    function (data) {
                        var listing = '';
                        for (var user in data) {
                            if (data.hasOwnProperty(user)) {
                                user = data[user];
                                listing += '\nUsername: ' + user['username']
                                + '\nLast activity: ' + user['last_activity']
                                + '\nRoles: ' + user['roles'] + "\n";
                            }
                        }
                        term.echo(listing);
                    }
                ).fail(
                    function (jqXHR, textStatus, errorThrown) {
                        app.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
                    }
                );
                break; // List of users
            default:
                term.error('No such argument');
        }
    }
});