app.addCommand(
    'cd',
    {
        summary: 'Go to board/thread',
        help: 'Go to board/thread<br />Usage: cd &lt;boardName&gt;[/threadID]',
        secured: false,
        isAvailable: true,
        run: function (term, args, rest) {
            if (args.length == 0) {
                return;
            }
            if (/[^/a-z0-9]+/.test(rest)) {
                term.error('Wrong path given');
                return;
            }
            args[0] = args[0].toString();
            if (args[0] == '/') {
                app.board.current = '~';
                app.thread.current = '';
            } else {
                var boardName = false;
                var threadID = false;
                if (/^\//.test(args[0])) {
                    boardName = args[0];
                    threadID = args.length > 1 && args[1] ? args[1] : threadID;
                } else if (app.board.current == '~') {
                    args[0] = args[0].split('/');
                    boardName = args[0][0];
                    threadID = args[0].length > 1 && args[0][1] ? args[0][1] : threadID;
                } else {
                    threadID = args[0];
                }
                if (boardName) {
                    boardName = boardName.replace(/^\/|\/$/g, '');
                    app.board.get(boardName, term, function (board) {
                        app.board.current = board['name'];
                    });
                }
                if (threadID) {
                    app.thread.get(threadID, term, function (thread) {
                        if (boardName && thread['board'] != boardName) {
                            // Of course, thread is exists, but not in this board
                            term.error('Thread does not exists');
                        } else {
                            app.thread.current = thread['id'];
                        }
                    });
                } else {
                    app.thread.current = '';
                }
            }
            app.prompt(term.set_prompt);
        }
    }
);