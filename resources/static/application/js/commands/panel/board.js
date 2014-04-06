Trillium.terminal.commands.panel.board = function (term, args) {
    var boardName = args.length > 1 && args[1] ? args[1] : false;
    var manageBoard = function (url) {
        var boardData = {name: '', summary: ''};
        term.push(
            function (summary) {
                boardData.summary = summary;
                $.ajax(url, {async: false, data: boardData, type: 'POST', dataType: 'json'})
                .done(
                    function (data) {
                        Trillium.terminal.responseHandler.success(term, data);
                    }
                ).fail(
                    function (jqXhr, textStatus, errorThrown) {
                        Trillium.terminal.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
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
    } else if (!boardName && (args[0] == '-u' || args[0] == '-r')) {
        term.error('No board name given');
    } else {
        switch (args[0]) {
            case '-l': // Show list
                term.echo('Boards: ');
                $.ajax(
                    Trillium.urlGenerator.generate('board.listing'),
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
                        Trillium.terminal.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                    }
                );
                break;
            case '-c': // Create
                manageBoard(Trillium.urlGenerator.generate('board.create'));
                break;
            case '-u': // Update
                manageBoard(Trillium.urlGenerator.generate('board.update', {'board': boardName}));
                break;
            case '-r': // Delete
                term.push(
                    function (confirm) {
                        if (confirm == 'y') {
                            $.ajax(
                                Trillium.urlGenerator.generate('board.delete', {name: boardName}),
                                {async: false, dataType: 'json'}
                            ).done(
                                function (data) {
                                    Trillium.terminal.responseHandler.success(term, data);
                                }
                            ).fail(
                                function (jqXhr, textStatus, errorThrown) {
                                    Trillium.terminal.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                                }
                            ).always(
                                function () {
                                    term.pop();
                                }
                            );
                        } else {
                            term.pop();
                        }
                    },
                    {prompt: 'Are you sure? [y/n]'}
                );
                break;
            default: // Show board
                $.ajax(
                    Trillium.urlGenerator.generate('board.get', {'name': args[0]}),
                    {async: false, dataType: 'json'}
                ).done(
                    function (data) {
                        term.echo('/' + data['name'] + '/ - ' + data['summary']);
                    }
                ).fail(
                    function (jqXhr, textStatus, errorThrown) {
                        Trillium.terminal.responseHandler.fail(term, jqXhr, textStatus, errorThrown);
                    }
                );
        }
    }
};
Trillium.terminal.help.panel.board = 'Boards management\n' +
    'Usage: board [-option] [name]\n' +
    'Options:\n' +
    '-c Creates a board\n' +
    '-u <name> Update a board\n' +
    '-r <name> Remove a board\n' +
    '-l Displays boards list';
Trillium.terminal.description.panel.board = 'Boards management';