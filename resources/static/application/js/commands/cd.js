app.addCommand(
    'cd',
    'Go to board<br />' +
    'Usage: cd &lt;board&gt;',
    'Go to board',
    function (term, args) {
        if (args.length == 0) {
            term.error('No board given');
            return ;
        }
        app.thread.current = '';
        if (args[0] == '~') {
           app.board.current = '~';
        } else {
            app.board.get(args[0], term, function (data) {
                app.board.current = data['name'];
            });
        }
        app.prompt(term.set_prompt);
    },
    false,
    true
);