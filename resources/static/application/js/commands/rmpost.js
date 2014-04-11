app.addCommand(
    'rmpost',
    'Remove a post<br />' +
    'Usage: rmpost &lt;post&gt;',
    'Remove a post',
    function (term, args) {
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
    },
    true,
    false
);