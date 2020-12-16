function Facebook(appId) {
    var me = this;
    me.appId = appId;
    me.scope = ["public_profile", "email"];
    me.userId = '';
    me.userToken = '';
    me.checkPermit = false;
    me.loadScript = function () {
        var js, fjs = document.getElementsByTagName('script')[0];
        if (document.getElementById('facebook-jssdk')) return;
        js = document.createElement('script');
        js.id = 'facebook-jssdk';
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
        window.fbAsyncInit = function () {
            me.fb = window.FB;
            me.fb.init({
                appId: me.appId,
                cookie: true,
                version: 'v7.0'
            });
        };
    };
    me.emit = function () {
        if (arguments.length === 0) return;
        var callback = arguments[0], args = Array.from(arguments);
        args.shift();
        if (typeof (callback) === 'function')
            callback.apply(this, args);
    };
    me.loadScript();
}

Facebook.prototype.setScope = function (scope) {
    this.scope = scope;
};

Facebook.prototype.checkLoginState = function (callback) {
    var me = this;
    me.fb.getLoginStatus(function (response) {
        if (response.status === 'connected') {
            me.userId = response.authResponse.userID;
            me.userToken = response.authResponse.accessToken;
            me.emit(callback, response);
        } else {
            me.emit(callback, false);
        }
    });
};

Facebook.prototype.loginUser = function (callback) {
    var me = this;
    me.fb.login(function (response) {
        if (response.status === 'connected') {
            me.userId = response.authResponse.userID;
            me.userToken = response.authResponse.accessToken;
            me.emit(callback, response);
        } else {
            me.emit(callback, false);
        }
    }, {scope: me.scope.join(',')});
};

Facebook.prototype.requierdPermit = function (callback) {
    var me = this, requiredScope = [], sumFnc = function (obj) {
        return Object.keys(obj).reduce((sum, key) => sum + parseFloat(obj[key] || 0), 0);
    };
    if (me.checkPermit)
        me.emit(callback, true);
    for (let index in me.scope)
        if (me.scope.hasOwnProperty(index))
            requiredScope[me.scope[index]] = 0;
    me.fb.api('/me/permissions', function (response) {
        if (response.data && Array.isArray(response.data)) {
            for (let pIndex in response.data) {
                if (response.data.hasOwnProperty(pIndex)) {
                    let scope = response.data[pIndex];
                    if (scope.status === 'granted' && requiredScope.hasOwnProperty(scope.permission))
                        requiredScope[scope.permission] = 1;
                }
            }
            if (sumFnc(requiredScope) >= me.scope.length) {
                me.checkPermit = true;
                me.emit(callback, true);
            } else
                me.emit(callback, false);
        } else
            me.emit(callback, false);
    });
};