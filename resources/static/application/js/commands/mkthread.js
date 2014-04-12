app.addCommand(
    'mkthread',
    'Creates a thread<br />' +
    'Usage: mkthread [board]',
    'Creates a thread',
    function (term, args) {
        var boardName = args.length > 0 && args[0] ? args[0] : (app.board.current != '~' ? app.board.current : false);
        if (!boardName) {
            term.error('No board given');
            return;
        }
        var threadData = {title: '', message: '', captcha: '', board: boardName};
        term.push(
            function (captcha) {
                threadData.captcha = captcha;
                $.ajax(
                    app.urlGenerator.generate('thread.create'),
                    {async: false, dataType: 'json', type: 'POST', data: threadData}
                ).done(
                    function (data) {
                        term.pop();
                        if (data.hasOwnProperty('success')) {
                            app.board.current = boardName;
                            app.thread.current = data.success.toString();
                            app.prompt(term.set_prompt);
                        } else {
                            console.log(data);
                            term.error('Unknown response type');
                        }
                    }
                ).fail(
                    function (xhr, textStatus, errorThrown) {
                        term.pop();
                        app.responseHandler.fail(term, xhr, textStatus, errorThrown);
                    }
                );
            },
            {prompt: 'Are you human? '}
        ).push(
            function (message) {
                threadData.message = message;
                app.captcha(term);
                term.pop();
            },
            {prompt: 'Message: '}
        ).push(
            function (title) {
                threadData.title = title;
                term.pop();
            },
            {prompt: 'Title: '}
        )
    },
    false,
    true
);