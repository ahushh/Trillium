Trillium.terminal.commands.panel.roles = function (term, args) {
    var username = args.length > 0 && args[0] ? args[0] : null;
    var roles = args.slice(1);
    if (username === null) {
        term.error('No username given');
    } else if (roles.length == 0) {
        term.error('No roles given');
    } else {
        $.ajax(
            Trillium.urlGenerator.generate('user.edit.roles', {'username': username}),
            {
                async: false,
                data: {'roles': roles},
                dataType: 'json',
                type: 'POST'
            }
        ).done(
            function (data) {
                if (data.hasOwnProperty('success')) {
                    term.echo(data.success);
                } else if (data.hasOwnProperty('error')) {
                    term.error(data.error);
                } else {
                    console.log(data);
                    term.error('Unknown response given');
                }
            }
        ).fail(
            function () {
                // TODO
            }
        );
    }
};