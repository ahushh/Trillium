Trillium.settings = {
    system: {},
    user: {},
    load: function () {
        var option_key, value;
        for (option_key in Trillium.settings.system) {
            if (Trillium.settings.system.hasOwnProperty(option_key)) {
                value = $.cookie(option_key);
                if (value) {
                    Trillium.settings.user[option_key] = value;
                } else {
                    Trillium.settings.user[option_key] = Trillium.settings.system[option_key];
                }
            }
        }
    },
    validate: function (settings, done, fail) {
        $.ajax(
            Trillium.urlGenerator.generate('settings.validate'),
            {async: false, data: {'settings': settings}, dataType: 'json', type: 'POST'}
        ).done(done).fail(fail);
    }
};