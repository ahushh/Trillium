app.addCommand(
    'rmthread',
    {
        summary: 'Removes a thread',
        help: 'Removes a thread<br />Usage: rmthread &lt;thread&gt;',
        secured: true,
        isAvailable: false,
        run: function (term, args) {
            if (args.length == 0) {
                term.error('No thread given');
                return;
            }
            app.termConfirm(term, function () {
                $.ajax(
                    app.urlGenerator.generate('thread.remove', {id: args[0]}),
                    {dataType: 'json'}
                ).done(
                    function (data) {
                        if (app.thread.current == args[0]) {
                            app.thread.current = '';
                            app.prompt(term.set_prompt);
                        }
                        app.responseHandler.success(term, data);
                    }
                ).fail(
                    function (xhr, textStatus, errorThrown) {
                        app.responseHandler.fail(term, xhr, textStatus, errorThrown);
                    }
                )
            });
        }
    }
);