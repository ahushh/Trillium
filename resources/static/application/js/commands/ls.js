app.addCommand(
    'ls',
    'List contents',
    'List contents',
    function (term) {
        app.board.list(term);
    },
    false,
    true
);