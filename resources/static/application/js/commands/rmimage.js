app.addCommand('rmimage', {
    summary: TermHelp.rmimage.summary,
    help: TermHelp.rmimage.help,
    isAvailable: false,
    secured: true,
    run: function (term, args) {
        if (args.length == 0) {
            term.error('No post given');
            return;
        }
        app.termConfirm(term, function () {
            $.ajax(
                app.urlGenerator.generate('image.remove', {id: args[0]}),
                {dataType: 'json', async: false}
            ).done(
                function (data) {
                    app.responseHandler.success(term, data);
                }
            ).fail(
                function (xhr, textStatus, errorThrown) {
                    app.responseHandler.fail(term, xhr, textStatus, errorThrown);
                }
            )
        });
    }
});