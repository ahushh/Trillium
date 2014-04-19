app.addCommand('mkthread', {
    summary: TermHelp.mkthread.summary,
    help: TermHelp.mkthread.help,
    secured: false,
    isAvailable: true,
    run: function (term, args) {
        var boardName = args.length > 0 && args[0] !='-f' ? args[0] : (app.board.current != '~' ? app.board.current : false);
        if (!boardName) {
            term.error('No board given');
            return;
        }
        var data = new FormData();
        data.append('board', boardName);
        var attachFile = args.length == 1 && args[0] == '-f' ? true : (args.length == 2 && args[1] == '-f');
        if (attachFile) {
            var fileupload = $('<input style="display: none" id="fileupload" type="file" name="image" />');
            fileupload.on('change', function () {
                var files = $(this).prop('files');
                if (files) {
                    if (files.length) {
                        data.append('file', files[0]);
                    }
                } else {
                    term.error('Not supported');
                }
                if (app.username === false) {
                    app.captcha(term);
                    term.pop();
                } else {
                    mkThread();
                }
            });
        }
        var mkThread = function () {
            $.ajax(
                app.urlGenerator.generate('thread.create'),
                {async: false, dataType: 'json', type: 'POST', data: data, processData: false, contentType: false}
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
        };
        if (app.username === false) {
            term.push(
                function (captcha) {
                    data.append('captcha', captcha);
                    mkThread();
                },
                {prompt: 'Are you human? '}
            );
        }
        term.push(
            function (message) {
                data.append('message', message);
                if (attachFile) {
                    fileupload.trigger('click');
                    term.set_prompt('');
                } else {
                    if (app.username === false) {
                        app.captcha(term);
                        term.pop();
                    } else {
                        mkThread();
                    }
                }
            },
            {prompt: 'Message: '}
        ).push(
            function (title) {
                data.append('title', title);
                term.pop();
            },
            {prompt: 'Title: '}
        );
    }
});