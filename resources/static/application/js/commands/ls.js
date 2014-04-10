app.addCommand(
    'ls',
    'List contents',
    'List contents',
    function (term, args) {
        if ((args.length == 0 && app.board.current == '~') || args[0] == '~') {
            // Boards list
            app.board.list(term);
        } else {
            var board = args[0] ? args[0] : app.board.current;
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
                            output += '/' + t['board'] + '/' +  t['id'] + '/ - ' + t['title'];
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
    },
    false,
    true
);