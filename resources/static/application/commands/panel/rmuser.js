Trillium.terminal.commands.panel.rmuser = function (term, args) {
    var username = args.length > 0 ? args[0] : null;
    if (username == null) {
        term.error('No username given');
    } else {
        term.push(
            function (answer) {
                if (answer == 'y') {
                    $.ajax(
                        Trillium.urlGenerator.generate('user.remove', {'username': username}),
                        {dataType: 'json'}
                    ).done(
                        function (data) {
                            if (data.hasOwnProperty('message')) {
                                term.echo(data.message);
                            } else {
                                console.log(data);
                                term.error('Unknown response');
                            }
                        }
                    ).fail(
                        function (jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR, textStatus, errorThrown);
                            term.error('Unknown error');
                        }
                    );
                }
                term.pop();
            },
            {prompt: "Are you sure? [y/n]: "}
        )
    }
};