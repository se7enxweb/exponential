/** Boot: chrome, catalogue of available services, who is logged in, basket count, then the router. */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal;
    $(function () {
        Exp.initChrome();
        $.when(Exp.api.loadCatalog()).always(function (n) {
            $('#service-status').text(Exp.api.catalogNames ? n + ' services listed.' : 'Service catalogue not available.');
            Exp.loadUser().always(Exp.refreshBasketCount);
            Exp.router.start(function (r) {
                var $main = Exp.ui.main(); Exp.drawNav(r.path);
                r.handler({ $view: $main, params: r.params, query: r.query }); Exp.afterRender();
            });
        });
    });
}(window, jQuery));
