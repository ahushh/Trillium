app.addCommand(
    'reply',
    'Reply to the thread<br />' +
    'Usage: reply [thread]',
    'Reply to the thread',
    function (term, args) {
        var threadID = app.thread.current ? app.thread.current : (args.length > 0 && args[0] ? args[0] : false);
        if (!threadID) {
            term.error('No thread given');
            return;
        }
        app.thread.get(threadID, term, function (thread) {
            var data = {message: '', captcha: ''};
            term.echo('Reply to thread: /' + thread['board'] + '/' + thread['id'] + ' - ' + thread['title']);
            term.push(
                function (captcha) {
                    data.captcha = captcha;
                    $.ajax(
                        app.urlGenerator.generate('post.create', {thread: thread['id']}),
                        {dataType: 'json', type: 'POST', data: data}
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
                {prompt: 'Are you human? '}
            ).push(
                function (message) {
                    data.message = message;
                    app.captcha(term);
                    term.pop();
                },
                {prompt: 'Message: '}
            );
        });
    },
    false,
    true
);