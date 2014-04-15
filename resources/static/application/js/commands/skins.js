app.addCommand(
    'skins',
    {
        summary: 'Shows available skins',
        help: 'Shows available skins',
        secured: false,
        isAvailable: true,
        run: function (term) {
            $.ajax(
                app.urlGenerator.generate('settings.skins'),
                {"dataType": "json"}
            ).done(
                function (data) {
                    var output = '';
                    if (data.length == 0) {
                        output = '\nList is empty';
                    } else {
                        for (var skin in data) {
                            output += '\n' + data[skin];
                        }
                    }
                    term.echo('Available skins:' + output);
                }
            ).fail(
                function (jqXhr, textStatus, errorThrown) {
                    app.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                }
            );
        }
    }
);