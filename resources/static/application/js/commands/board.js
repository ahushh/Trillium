app.addCommand(
    'board',
    'Boards management<br />' +
    'Usage: board -option [name]<br />' +
    '<table>' +
    '<tr><td>-l</td><td>Show list</td></tr>' +
    '<tr><td>-c</td><td>Create a board</td></tr>' +
    '<tr><td>-u &lt;board&gt;</td><td>Update a board</td></tr>' +
    '<tr><td>-r &lt;board&gt;</td><td>Remove a board</td></tr>' +
    '<tr><td>-i &lt;board&gt;</td><td>Show information about board</td></tr>' +
    '</table>',
    'Boards management',
    function (term, args) {
        var boardName = args.length > 1 && args[1] ? args[1] : false;
        var manageBoard = function (url) {
            var boardData = {name: '', summary: ''};
            term.push(
                function (summary) {
                    boardData.summary = summary;
                    $.ajax(url, {async: false, data: boardData, type: 'POST', dataType: 'json'})
                        .done(
                        function (data) {
                            app.responseHandler.success(term, data);
                        }
                    ).fail(
                        function (jqXhr, textStatus, errorThrown) {
                            app.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                        }
                    ).always(
                        function () {
                            term.pop();
                        }
                    );
                },
                {prompt: 'Summary: '}
            ).push(
                function (name) {
                    boardData.name = name;
                    term.pop();
                },
                {prompt: 'Name: '}
            );
        };
        if (args.length == 0) {
            term.error('No args given');
        } else if (!boardName && (args[0] == '-u' || args[0] == '-r' || args[0] == '-i')) {
            term.error('No board name given');
        } else {
            switch (args[0]) {
                case '-l': // Show list
                    term.echo('Boards: ');
                    $.ajax(
                        app.urlGenerator.generate('board.listing'),
                        {async: false, dataType: 'json'}
                    ).done(
                        function (data) {
                            if (data.length == 0) {
                                term.echo('List is empty');
                            } else {
                                for (var b in data) {
                                    term.echo('/' + data[b]['name'] + '/ - ' + data[b]['summary']);
                                }
                            }
                        }
                    ).fail(
                        function (jqXhr, textStatus, errorThrown) {
                            app.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                        }
                    );
                    break;
                case '-c': // Create
                    manageBoard(app.urlGenerator.generate('board.create'));
                    break;
                case '-u': // Update
                    manageBoard(app.urlGenerator.generate('board.update', {'board': boardName}));
                    break;
                case '-r': // Delete
                    app.termConfirm(term, function () {
                        $.ajax(
                            app.urlGenerator.generate('board.delete', {name: boardName}),
                            {dataType: 'json'}
                        ).done(
                            function (data) {
                                app.responseHandler.success(term, data);
                            }
                        ).fail(
                            function (jqXhr, textStatus, errorThrown) {
                                app.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                            }
                        );
                    });
                    break;
                case '-i': // Show board
                    $.ajax(
                        app.urlGenerator.generate('board.get', {'name': boardName}),
                        {async: false, dataType: 'json'}
                    ).done(
                        function (data) {
                            term.echo('/' + data['name'] + '/ - ' + data['summary']);
                        }
                    ).fail(
                        function (jqXhr, textStatus, errorThrown) {
                            app.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                        }
                    );
                    break;
                default:
                    term.error('No such argument');
            }
        }
    },
    true,
    false
);