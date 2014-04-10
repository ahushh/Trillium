app.addCommand(
    'mvthread',
    'Renames a thread<br />' +
    'Usage: mvthread &lt;thread&gt;',
    'Renames a thread',
    function (term, args) {
        if (args.length == 0) {
            term.error('No thread given');
            return ;
        }
        $.ajax(
            app.urlGenerator.generate('thread.get', {id: args[0]}),
            {async: false, dataType: 'json'}
        ).done(
            function (thread) {
                term.echo('Rename thread: [' + thread['board'] + '/' + thread['id'] + '] ' + thread['title']);
                term.push(
                    function (title) {
                        $.ajax(
                            app.urlGenerator.generate('thread.rename', {id: args[0]}),
                            {async: false, dataType: 'json', type: 'POST', data: {title: title}}
                        ).done(
                            function (data) {
                                app.responseHandler.success(term, data);
                            }
                        ).fail(
                            function (xhr, textStatus, errorThrown) {
                                app.responseHandler.fail(term, xhr, textStatus, errorThrown);
                            }
                        ).always(
                            function () {
                                term.pop();
                            }
                        );
                    },
                    {prompt: 'New title: '}
                );
            }
        ).fail(
            function (xhr, textStatus, errorThrown) {
                app.responseHandler.fail(term, xhr, textStatus, errorThrown);
            }
        );

    },
    true,
    false
);