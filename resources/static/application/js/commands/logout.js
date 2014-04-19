app.addCommand('logout', {
    summary: TermHelp.logout.summary,
    help: TermHelp.logout.help,
    secured: true,
    isAvailable: false,
    run: function (term) {
        $.ajax(app.urlGenerator.generate('user.sign.out'), {async: false}).done(function (data) {
            app.responseHandler.success(term, data);
        });
        app.logout(term);
    }
});