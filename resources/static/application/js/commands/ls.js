app.addCommand('ls', {
    summary: TermHelp.ls.summary,
    help: TermHelp.ls.help,
    secured: false,
    isAvailable: true,
    run: function (term, args) {
        if ((args.length == 0 && app.board.current == '~') || args[0] == '~') {
            // Boards list
            app.board.list(term);
        } else if (args.length == 0 && app.board.current != '~' && app.thread.current != '') {
            $.ajax(
                app.urlGenerator.generate('post.listing', {thread: app.thread.current}),
                {dataType: 'json'}
            ).done(
                function (posts) {
                    var output = '', i = 0, image;
                    for (var p in posts) {
                        image = posts[p]['image'];
                        output += '#' + posts[p]['id'] + ' >> At: ' + posts[p]['time'] + '<br />' + posts[p]['message'];
                        if (image) {
                            output += '<div><a href="'
                            + app.urlGenerator.raw('images/' + posts[p]['thread'] + '/' + posts[p]['id'] + '.' + image['ext'])
                            + '" target="_blank" title="Click, to get full image">'
                            + '<img src="'
                            + app.urlGenerator.raw('images/' + posts[p]['thread'] + '/' + posts[p]['id'] + '_preview.png')
                            + '" alt="thumb" />'
                            + '<a/><br />'
                            + image['width'] + 'x' + image['height']
                            + '/' + image['size'] + ' Kb,' + image['ext'] + '</div>';
                        }
                        if (i + 1 != posts.length) {
                            output += '<hr />';
                        }
                        i++;
                    }
                    term.echo(output, {raw: true});
                }
            ).fail(
                function (xhr, textStatus, errorThrown) {
                    app.responseHandler.fail(term, xhr, textStatus, errorThrown);
                }
            )
        } else {
            var board;
            if (args[0]) {
                args[0] = args[0].toString();
                board = args[0].replace(/^[\/]+/, '');
                if (/[^a-z0-9]+/.test(board)) {
                    term.error('Wrong path given');
                    return;
                }
            } else {
                board = app.board.current;
            }
            $.ajax(
                app.urlGenerator.generate('thread.listing', {board: board}),
                {dataType: 'json'}
            ).done(
                function (data) {
                    var output = '';
                    if (data.length == 0) {
                        output += 'List is empty';
                    } else {
                        var i = 0;
                        for (var t in data) {
                            t = data[t];
                            output += '/' + t['board'] + '/' + t['id'] + '/ - ' + t['title'];
                            if (i + 1 != data.length) {
                                output += '\n'
                            }
                            i++;
                        }
                    }
                    term.echo(output);
                }
            ).fail(
                function (xhr, textStatus, errorThrown) {
                    app.responseHandler.fail(term, xhr, textStatus, errorThrown);
                }
            )
        }
    }
});