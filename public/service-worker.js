// https://developers.google.com/web/fundamentals/primers/service-workers/

var CACHE_NAME = 'gdhh-cache-v3';
var urlsToCache = [
        '/login',
        '/bundles/sonatacore/vendor/bootstrap/dist/css/bootstrap.min.css',
        '/bundles/sonatacore/vendor/components-font-awesome/css/font-awesome.min.css',
        '/bundles/sonatacore/vendor/ionicons/css/ionicons.min.css',
        '/bundles/sonataadmin/vendor/admin-lte/dist/css/AdminLTE.min.css',
        '/bundles/sonataadmin/vendor/admin-lte/dist/css/skins/skin-black.min.css',
        '/bundles/sonataadmin/vendor/iCheck/skins/square/blue.css',
        '/bundles/sonatacore/vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
        '/bundles/sonataadmin/vendor/jqueryui/themes/base/jquery-ui.css',
        '/bundles/sonatacore/vendor/select2/select2.css',
        '/bundles/sonatacore/vendor/select2-bootstrap-css/select2-bootstrap.min.css',
        '/bundles/sonataadmin/vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css',
        '/bundles/sonataadmin/css/styles.css',
        '/bundles/sonataadmin/css/layout.css',
        '/bundles/sonataadmin/css/tree.css',
        '/bundles/sonatacore/vendor/jquery/dist/jquery.min.js',
        '/bundles/sonataadmin/vendor/jquery.scrollTo/jquery.scrollTo.min.js',
        '/bundles/sonataadmin/vendor/jqueryui/ui/minified/jquery-ui.min.js',
        '/bundles/sonataadmin/vendor/jqueryui/ui/minified/i18n/jquery-ui-i18n.min.js',
        '/bundles/sonatacore/vendor/moment/min/moment.min.js',
        '/bundles/sonatacore/vendor/bootstrap/dist/js/bootstrap.min.js',
        '/bundles/sonatacore/vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        '/bundles/sonataadmin/vendor/jquery-form/jquery.form.js',
        '/bundles/sonataadmin/jquery/jquery.confirmExit.js',
        '/bundles/sonataadmin/vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js',
        '/bundles/sonatacore/vendor/select2/select2.min.js',
        '/bundles/sonataadmin/vendor/admin-lte/dist/js/app.min.js',
        '/bundles/sonataadmin/vendor/iCheck/icheck.min.js',
        '/bundles/sonataadmin/vendor/slimScroll/jquery.slimscroll.min.js',
        '/bundles/sonataadmin/vendor/waypoints/lib/jquery.waypoints.min.js',
        '/bundles/sonataadmin/vendor/waypoints/lib/shortcuts/sticky.min.js',
        '/bundles/sonataadmin/vendor/readmore-js/readmore.min.js',
        '/bundles/sonataadmin/vendor/masonry/dist/masonry.pkgd.min.js',
        '/bundles/sonataadmin/Admin.js',
        '/bundles/sonataadmin/treeview.js',
        '/bundles/sonataadmin/sidebar.js',
        '/bundles/sonatacore/vendor/moment/locale/vi.js',
        '/bundles/sonatacore/vendor/select2/select2_locale_vi.js'
    ]
;

self.addEventListener('install', function (event) {
    // Perform install steps
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function (cache) {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', function (event) {
    console.log('The service worker is serving the asset.');
    event.respondWith(fromCache(event.request, 400).catch(function () {
        return fromNetwork(event.request);
    }));


    // if (event.request.url.indexOf('book',15) == 13) {
    // console.log('load from network before cache for all the pages');
    // event.respondWith(fromNetwork(event.request, 400).catch(function () {
    //     return fromCache(event.request);
    // }));

    // } else {
    //     event.respondWith(fromCache(event.request, 400).catch(function() {
    //         return fromNetwork(event.request);
    //     }));
    // }
});

function fromNetwork(request, timeout) {
    // IMPORTANT: Clone the request. A request is a stream and
    // can only be consumed once. Since we are consuming this
    // once by cache and once by the browser for fetch, we need
    // to clone the response.
    let fetchRequest = request.clone();

    return fetch(fetchRequest).then(
        function (response) {
            // Check if we received a valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
                return response;
            }

            // IMPORTANT: Clone the response. A response is a stream
            // and because we want the browser to consume the response
            // as well as the cache consuming the response, we need
            // to clone it so we have two streams.
            var responseToCache = response.clone();

            // caches.open(CACHE_NAME)
            //     .then(function (cache) {
            //         console.log('cache.put');
            //         cache.put(request, responseToCache);
            //     });

            return response;
        }
    );
}

function fromCache(request) {
    return caches.open(CACHE_NAME).then(function (cache) {
        return cache.match(request).then(function (matching) {
            return matching || Promise.reject('no-match');
        });
    });
}