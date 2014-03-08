Trillium.urlGenerator = {
    routes: {"login":{"path":"\/login","requirements":[],"defaults":[],"variables":[]},"hello":{"path":"\/{name}","requirements":[],"defaults":{"name":"Kilte"},"variables":["name"]},"login.check":{"path":"\/login\/check","requirements":[],"defaults":[],"variables":[]},"logout":{"path":"\/logout","requirements":[],"defaults":[],"variables":[]}},
    basePath: '',
    generate: function (name, params) {
        if (this.routes[name]) {
            params = params == undefined ? {} : params;
            var route        = this.routes[name],
                requirements = route.requirements,
                defaults     = route.defaults,
                variables    = route.variables,
                result       = route.path,
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
    }
};