app.addCommand(
    'logout',
    'Logout',
    'Logout',
    function (term) {
        $.ajax(app.urlGenerator.generate('user.sign.out'));
        app.logout(term);
    },
    true,
    false
);