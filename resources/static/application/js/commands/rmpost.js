app.addCommand(
    'rmpost',
    {
        summary: 'Remove a post',
        help: 'Remove a post<br />Usage: rmpost &lt;post&gt;',
        secured: true,
        isAvailable: false,
        run: function (term, args) {
            if (args.length == 0 || !args[0]) {
                term.error('No post given');
                return ;
            }
            app.termConfirm(term, function () {
                $.ajax(
                    app.urlGenerator.generate('post.remove', {id: args[0]}),
                    {async: false, dataType: 'json'}
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
    }
);