Trillium.terminal.commands.panel.password = function (term) {
    var passwords = {};
    term.set_mask(true)
    .push(
        function (string) {
            passwords['confirm'] = string;
            $.ajax(
                TrilliumUrlGenerator.generate('user.edit.password'),
                {
                    async: false,
                    cache: false,
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
                    var key, size = 0;
                    for (key in data) {
                        if (data.hasOwnProperty(key)) {
                            size++;
                            term.error(data[key]);
                        }
                    }
                    if (size == 0) {
                        term.echo('Password updated');
                    }
                }
            ).fail(
                function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                    term.error('Unknown error');
                }
            ).always(
                function () {
                    term.pop();
                    term.set_mask(false);
                }
            );
        },
        {prompt: 'Confirm password: '}
    )
    .push(
        function (string) {
            passwords['new'] = string;
            term.pop();
        },
        {prompt: 'New password: '}
    )
    .push(
        function (string) {
            passwords['old'] = string;
            term.pop();
        },
        {prompt: 'Old password: '}
    );
};