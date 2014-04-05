Trillium.settings = {
    // System settings
    system: {},
    // User settings
    user: {},
    // Returns settings from cookies
    load: function () {
        var key, value, settings = {};
        for (key in Trillium.settings.system) {
            value = $.cookie(key);
            if (value) {
                settings[key] = value;
            } else {
                settings[key] = Trillium.settings.system[key];
            }
        }
        return settings;
    },
    // Validates settings
    validate: function (settings, done, fail) {
        $.ajax(
            Trillium.urlGenerator.generate('settings.validate'),
            {async: false, data: {'settings': settings}, dataType: 'json', type: 'POST'}
        ).done(done).fail(fail);
    }
};