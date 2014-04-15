app.addCommand(
    'reply',
    {
        summary: 'Reply to the thread',
        help: 'Reply to the thread<br />Usage: reply [thread] [-option]...<br />' +
        'Options:<br /><table><tr><td>-f</td><td>Attach a file</td></tr></table>',
        secured: false,
        isAvailable: true,
        run: function (term, args) {
            var threadID = app.thread.current ? app.thread.current : (args.length > 0 && args[0] != '-f' ? args[0] : false);
            var attachFile = args.length == 1 && args[0] == '-f' ? true : (args.length == 2 && args[1] == '-f');
            var data = new FormData();
            var showCaptcha = function () {
                if (app.username === false) {
                    app.captcha(term);
                    term.pop();
                } else {
                    sendMessage(threadID);
                }
            };
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
                    showCaptcha();
                });
            }
            if (!threadID) {
                term.error('No thread given');
                return;
            }
            var sendMessage = function (threadID) {
                $.ajax(
                    app.urlGenerator.generate('post.create', {thread: threadID}),
                    {dataType: 'json', type: 'POST', data: data, processData: false, contentType: false}
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
                            data.append('captcha', captcha);
                            sendMessage(threadID);
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
                            showCaptcha();
                        }
                    },
                    {prompt: 'Message: '}
                );
            });
        }
    }
);