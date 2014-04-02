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
            {async: false, data: {'roles': roles}, dataType: 'json', type: 'POST'}
        ).done(
            function (data) {
                Trillium.terminal.responseHandler.success(term, data);
            }
        ).fail(
            function (jqXHR, textStatus, errorThrown) {
                Trillium.terminal.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
            }
        );
    }
};
Trillium.terminal.help.panel.roles = 'Update roles for an user.\nUsage: roles <username> <role>...';
Trillium.terminal.description.panel.roles = 'Update roles for an user';