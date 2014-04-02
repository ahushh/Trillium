Trillium.terminal.commands.panel.password = function (term, args) {
    var passwords = {};
    var username = args.length > 0 && args[0] ? args[0] : null;
    term.push(
        function (string) {
            passwords['confirm'] = string;
            $.ajax(
                Trillium.urlGenerator.generate('user.edit.password', username !== null ? {'username': username} : {}),
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
                    Trillium.terminal.responseHandler.success(term, data);
                }
            ).fail(
                function (jqXHR, textStatus, errorThrown) {
                    Trillium.terminal.responseHandler.fail(term, jqXHR, textStatus, errorThrown);
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
};