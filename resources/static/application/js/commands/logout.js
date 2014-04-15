app.addCommand(
    'logout',
    {
        summary: 'Logout',
        help: 'Logout',
        secured: true,
        isAvailable: false,
        run: function (term) {
            $.ajax(app.urlGenerator.generate('user.sign.out'), {async: false}).done(function (data) {
                app.responseHandler.success(term, data);
            });
            app.logout(term);
        }
    }
);