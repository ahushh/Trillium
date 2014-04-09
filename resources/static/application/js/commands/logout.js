app.addCommand(
    'logout',
    'Logout',
    'Logout',
    function (term) {
        $.ajax(app.urlGenerator.generate('user.sign.out'), {async: false}).done(function (data) {
            app.responseHandler.success(term, data);
        });
        app.logout(term);
    },
    true,
    false
);