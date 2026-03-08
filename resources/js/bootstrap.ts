window.fbAsyncInit = function () {
    FB.init({
        appId: import.meta.env.VITE_META_ID,
        cookie: true,
        xfbml: true,
        version: import.meta.env.VITE_META_VERSION,
    });

    FB.AppEvents.logPageView();
};

(function (d, s, id) {
    var js,
        fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = 'https://connect.facebook.net/en_US/sdk.js';
    fjs.parentNode.insertBefore(js, fjs);
})(document, 'script', 'facebook-jssdk');


console.log(import.meta.env.VITE_META_ID);
console.log(import.meta.env.VITE_META_VERSION);