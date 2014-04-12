app.addCommand(
    'reply',
    'Reply to the thread<br />' +
    'Usage: reply [thread]',
    'Reply to the thread',
    function (term, args) {
        var threadID = app.thread.current ? app.thread.current : (args.length > 0 && args[0] ? args[0] : false);
        var data = {message: '', captcha: ''};
        if (!threadID) {
            term.error('No thread given');
            return;
        }
        var sendMessage = function (thread) {
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
        };
        app.thread.get(threadID, term, function (thread) {
            term.echo('Reply to thread: /' + thread['board'] + '/' + thread['id'] + ' - ' + thread['title']);
            if (app.username === false) {
                term.push(
                    function (captcha) {
                        data.captcha = captcha;
                        sendMessage(thread);
                    },
                    {prompt: 'Are you human? '}
                );
            }
            term.push(
                function (message) {
                    data.message = message;
                    if (app.username === false) {
                        app.captcha(term);
                        term.pop();
                    } else {
                        sendMessage(thread);
                    }
                },
                {prompt: 'Message: '}
            );
        });
    },
    false,
    true
);