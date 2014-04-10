app.addCommand(
    'cd',
    'Go to board/thread<br />' +
    'Usage: cd &lt;boardName&gt;[/threadID]',
    'Go to board/thread',
    function (term, args) {
        if (args.length == 0) {
            term.error('No board given');
            return ;
        }
        if (args[0] == '~') {
            app.board.current = '~';
            app.thread.current = '';
        } else {
            args[0] = args[0].split('/');
            var boardName = args[0][0];
            var threadID = args[0].length > 1 ? args[0][1] : false;
            app.board.get(boardName, term, function (board) {
                if (threadID) {
                    app.thread.get(threadID, term, function (thread) {
                        app.thread.current = thread['id'];
                    });
                } else {
                    app.thread.current = '';
                }
                app.board.current = board['name'];
            });
        }
        app.prompt(term.set_prompt);
    },
    false,
    true
);