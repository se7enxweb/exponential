/* jshint camelcase: false */
/* exported eZAsynchronousPublishingApp */
/**
 * The asynchronous publishing page (content/queued): checks the status of the version being published at regular
 * intervals and shows what happened, on Exponential UI (jQuery 4 and Exp.io: exp::core and exp::io must be on the
 * page; queued.tpl loads them).
 */
var eZAsynchronousPublishingApp = (function() {
    var ret = {cfg: {}},
        DEFAULT_CONF = {
            max_allowed_failures: 5,
            wait_time: 1000,
            redirect_uri: false,
        },
        checkedCount = 0,
        failureCount = 0;

    /**
     * Initializes the component to regularly checks the version status
     * This methods expects the component to be configured before this method is
     * called, see the example below.
     *
     * @example
     *   eZAsynchronousPublishingApp.cfg = {
     *      contentobject_id: 42, // required, content object id
     *      version: 2, // required, version number
     *      failure_message: "Ouch!" // required, fatal error message
     *      redirect_uri: false, // optional, where to redirect the user when
     *                           // the version is getting published
     *      wait_time: 1000, // optional, time in ms to wait between 2 checks
     *      max_allowed_failures: 5, // optional, max number of allowed failure
     *   };
     *   eZAsynchronousPublishingApp.init();
     *
     * @method init
     */
    ret.init = function() {
        if ( window.Exp && window.Exp.io && window.Exp.$ ) {
            initExp(window.Exp);
        } else if ( window.console ) {
            window.console.error("eZAsynchronousPublishingApp: Exponential UI (exp::core, exp::io) is not on the page");
        }
    };

    /**
     * The checks: the configuration above, the server call ezpublishingqueue::status (GET), the placeholders of
     * queued.tpl, the failure count and the waiting time.
     *
     * @method initExp
     * @private
     * @param {Object} Exp the Exponential UI namespace
     */
    function initExp(Exp) {
        var $ = Exp.$;
        ret.cfg = $.extend({}, DEFAULT_CONF, ret.cfg);
        var fn = 'ezpublishingqueue::status', args = [ret.cfg.contentobject_id, ret.cfg.version];
        var $error, $publishing, $finished, $deferred;

        function display($element, message) {
            $('.ezap-placeholder').css('display', 'none');
            $element.css('display', 'block');
            if ( typeof message === 'function' ) {
                message($element);
            } else {
                $element.html(message);
            }
        }
        function lastCheck($element) {
            var $last = $element.find('.last-check');
            if ( !$last.length ) {
                $last = $('<span class="last-check"></span>').appendTo($element);
            }
            $last.html(ret.cfg.last_checked_message.replace('%times%', checkedCount).replace('%ms%', ret.cfg.wait_time));
        }
        function retry() {
            window.setTimeout(update, ret.cfg.wait_time);
        }
        function failed(message) {
            failureCount++;
            if ( failureCount > ret.cfg.max_allowed_failures ) {
                display($error, message);
            } else {
                if ( checkedCount ) {
                    display($publishing, lastCheck);
                }
                checkedCount++;
                retry();
            }
        }
        function update() {
            Exp.io.call(fn, args, { method: 'GET' }).then(function (content) {
                if ( content && content.status == 'finished' ) {
                    if ( ret.cfg.redirect_uri !== false ) {
                        window.location = ret.cfg.redirect_uri;
                    } else {
                        display($finished, function ($element) {
                            $element.find('#ezap-contentview-uri').attr('href', content.node_uri);
                        });
                    }
                } else if ( content && content.status == 'deferred' ) {
                    display($deferred, function ($element) {
                        $element.find('#ezap-versionview-uri').attr('href', content.versionview_uri);
                    });
                } else {
                    if ( checkedCount ) {
                        display($publishing, lastCheck);
                    }
                    checkedCount++;
                    retry();
                }
            }, function (error) {
                // the server function's own error text; any other failure: the configured message
                var r = error && error.response;
                failed(r && typeof r === 'object' && r.error_text ? String(r.error_text) : ret.cfg.failure_message);
            });
        }

        $(function () {
            $error = $('#ezap-error');
            $publishing = $('#ezap-message-publishing');
            $finished = $('#ezap-message-finished');
            $deferred = $('#ezap-message-deferred');
            update();
        });
    }

    return ret;
})();
