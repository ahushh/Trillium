Trillium.terminal.commands.panel.rmuser = function (term, args) {
    var username = args.length > 0 ? args[0] : null;
    if (username == null) {
        term.error('No username given');
    } else {
        term.push(
            function (answer) {
                if (answer == 'y') {
                    $.ajax(
                        Trillium.urlGenerator.generate('user.remove', {'username': username}),
                        {dataType: 'json'}
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
                term.pop();
            },
            {prompt: "Are you sure? [y/n]: "}
        )
    }
};
Trillium.terminal.help.panel.rmuser = 'Remove an user.\nUsage: rmuser <username>';