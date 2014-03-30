Trillium.terminal.commands.panel.users = function (term) {
    $.ajax(Trillium.urlGenerator.generate('user.listing'), {async: false, dataType: 'json'})
    .done(
        function (data) {
            var listing = '';
            for (var user in data) {
                if (data.hasOwnProperty(user)) {
                    user = data[user];
                    listing += '\nUsername: ' + user['username']
                            + '\nLast activity: ' + user['last_activity']
                            + '\nRoles: ' + user['roles'] + "\n";
                }
            }
            term.echo(listing);
        }
    )
    .fail(
        function (jqXHR, textStatus, errorThrown) {
            term.error('Unknown error');
            console.log(jqXHR, textStatus, errorThrown);
        }
    );
};