var Trillium = new(function () {
    var self = this;
    this.settings = {
        // System settings
        system: {},
        // User settings
        user: {},
        // Returns user settings from cookies
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
            var url = self.urlGenerator.generate('settings.validate'),
                options = {async: false, data: {'settings': settings}, dataType: 'json', type: 'POST'};
            $.ajax(url, options).done(done).fail(fail);
        }
    };
    this.urlGenerator = {
        routes: {},
        basePath: '',
        generate: function (name, params) {
            if (this.routes[name]) {
                params = params == undefined ? {} : params;
                var route        = this.routes[name],
                    requirements = route['requirements'],
                    defaults     = route['defaults'],
                    variables    = route['variables'],
                    result       = route['path'],
                    val;
                for (var param in variables) {
                    param = variables[param];
                    val = params[param] ? params[param] : defaults[param];
                    if (val === undefined) {
                        throw 'Missing "' + param + '" parameter for route "'+name+'"!';
                    }
                    if (requirements.hasOwnProperty(param) && !new RegExp(requirements[param]).test(val)) {
                        throw 'Parameter "' + param + '" for route "' + name + '" must pass "' + requirements[param] + '" test!';
                    }
                    result = result.replace('{' + param + '}', val);
                }
                return (window.location.protocol + '//' + window.location.hostname + this.basePath + result).replace(/\/$/, '');
            } else {
                throw 'Undefined route "' + name + '"!';
            }
        },
        raw: function (path) {
            return window.location.protocol + '//' + window.location.hostname + this.basePath + '/' + path;
        }
    };
})();
$('document').ready(
    function () {
        // Load and validate settings
        var settings = Trillium.settings.load();
        Trillium.settings.validate(
            settings,
            function () { Trillium.settings.user = settings;                },
            function () { Trillium.settings.user = Trillium.settings.system;}
        );
        // Echoes the greeting
        var echoGreeting = function (term) {
            term.echo('<div id="trillium_greeting"></div>', {raw: true});
        };
        // Create the terminal
        $('body').terminal(
            function (command, term) { Trillium.terminal.commandHandler(command, term, 'main'); },
            {
                greetings: null,
                onInit: echoGreeting,
                onClear: echoGreeting,
                onBlur: function () { return false; },
                prompt: "[anonymous@" + Trillium.terminal.name + "] >>> "
            }
        );
    }
);