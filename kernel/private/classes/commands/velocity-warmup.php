<?php
/**
 * The code of bin/php/velocity-warmup.php, moved into a class (#207 stage 1). The file bin/php/velocity-warmup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/velocity-warmup.php:
 *
 *
 * Exponential Velocity parent warm-up.
 *
 * Run once, in the pool's parent process, before it forks its workers
 * (wired through Q.webserver.warmup). It loads the Exponential kernel and
 * renders a few representative pages, so the class tables, parsed
 * configuration, compiled templates and the arena they live in are built HERE,
 * in the parent -- and then handed to every worker copy-on-write by fork().
 *
 * Without this each worker builds that state privately on its first request:
 * measured at ~39 MB for a cold front-page render, held per worker. With it a
 * worker inherits the warmed arena shared and its first render was measured at
 * ~5 MB private. Across hundreds of workers that is the difference between
 * tens of gigabytes and a few.
 *
 * Two rules make it safe to run before a fork:
 *   1. It opens no resource a child must not share. The kernel connects to the
 *      database during a render, so the connection is closed and the global
 *      instance nulled before returning; each worker reconnects with its own.
 *   2. It renders only anonymous, side-effect-light GETs, and never exits: a
 *      throw here is caught by the pool, which then lets workers warm lazily
 *      the old way. The warm-up is an optimisation, never a dependency.
 *
 * It prints nothing on the happy path; the pool reports how much it warmed.
 *
 */

namespace Exponential\Command\Kernel
{

class VelocityWarmup extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'clearPrefixes', 'e', 'kernel', 'result', 'root', 'uri', 'urls', 'warmUrls' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        // eZ would otherwise end the process itself at the close of a request.
        if (class_exists('eZExecution')) {
            \eZExecution::setCleanExit();
        }

        // The pages to warm. The front page first, because it exercises the pagelayout,
        // the design and the layout engine that most other pages share. More can be
        // added, but each costs startup time, so this stays short and representative.
        $urls = array('/');
        $warmUrls = getenv('VELOCITY_WARMUP_URLS');
        if (is_string($warmUrls) && $warmUrls !== '') {
            $urls = preg_split('/\s*,\s*/', $warmUrls, -1, PREG_SPLIT_NO_EMPTY);
        }

        // A render that throws stops halfway through its request: output buffers
        // open, the route, the visitor and the siteaccess all its own. The clean-up
        // below must run all the same -- the pool snapshots what this script leaves,
        // and every worker restores that snapshot before each request, so a render
        // left standing answered every later request with its page (2026-09-28: an
        // admin asking for the dashboard got the public front page). So the renders
        // run inside a try, the clean-up always runs, and only then is the failure
        // passed on for the pool to log.
        //
        // VELOCITY_WARMUP_TEST_FAILURE=1 makes the last render throw after it has run,
        // with its output buffer still open, to test exactly that path.
        $__warmupFailure = null;
        $__warmupObLevel = ob_get_level();
        // Maintenance (var/maintenance.json, as index.php checks it): nothing to warm. A
        // render would be the setup wizard's first page, which takes the wizard's lease
        // from the browser that is on it. $GLOBALS['expVelocityWarmup'] tells the
        // wizard a render is the warm-up (it starts no run and no lease then).
        if (is_file($root . '/var/maintenance.json')) {
            require_once $root . '/kernel/classes/expmaintenance.php';
            if (\expMaintenance::state($root) !== false) {
                $urls = array();
            }
        }
        $GLOBALS['expVelocityWarmup'] = true;
        try {
        foreach ($urls as $uri) {
            $_SERVER['REQUEST_URI'] = $uri;
            $_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
            $_SERVER['SCRIPT_NAME'] = '/index.php';
            $_SERVER['PHP_SELF'] = '/index.php';
            $_GET = array();

            // A render can start a database transaction; make sure the next iteration
            // and, more importantly, the fork do not inherit an open one.
            if (!class_exists('ezpKernel') || !class_exists('ezpKernelWeb')) {
                break;
            }
            $kernel = new \ezpKernel(new \ezpKernelWeb());
            $result = $kernel->run();
            if (is_object($result) && method_exists($result, 'getContent')) {
                // Read and discard: the point is the work done to produce it.
                $result->getContent();
            }
            // ezpKernel keeps a singleton; drop it so the next URL builds a clean one
            // and so nothing holds a reference to the connection we are about to close.
            if (method_exists('ezpKernel', 'reset')) {
                @\ezpKernel::reset();
            }
        }
        if (getenv('VELOCITY_WARMUP_TEST_FAILURE') === '1') {
            ob_start();
            echo 'half a page';
            throw new \RuntimeException('VELOCITY_WARMUP_TEST_FAILURE: a render failed halfway (test)');
        }
        } catch (\Throwable $__e) {
            $__warmupFailure = $__e;
        }
        while (ob_get_level() > $__warmupObLevel && @ob_end_clean()) {
        }
        unset($GLOBALS['expVelocityWarmup']);

        // ── Undo the render's request footprint, keep everything else ─────────
        //
        // The pool takes its statics snapshot AFTER this script, so any eZ state the
        // render leaves becomes every worker's per-request baseline. The first attempt
        // cleared $GLOBALS wholesale except a tiny keep-list -- that fixed the
        // contamination but also stripped eZ's design and template caches
        // (eZOverrideTemplateCacheMap, eZTemplateDesignResource*), so pages rendered
        // with no stylesheets.
        //
        // A render's $GLOBALS were dumped and
        // the contaminating ones are specifically the request-routing state: the parsed
        // request, the requested module and uri, the view stack, the siteaccess. Those
        // are what froze "/" for every worker. So ONLY those are cleared, by name, and
        // the design, template, datatype and locale caches -- which every request needs
        // and which are identical across requests -- are left in place. The memory the
        // cleared objects held returns to the Zend arena, not the OS, so the arena
        // stays warm.
        // Close the database connection BEFORE the globals below are cleared -- the
        // list includes eZDBGlobalInstance, and once it is unset there is nothing left
        // to close. See the note at the end of this script for what an unclosed
        // connection did to every worker.
        if (isset($GLOBALS['eZDBGlobalInstance']) and is_object($GLOBALS['eZDBGlobalInstance'])
            and method_exists($GLOBALS['eZDBGlobalInstance'], 'close')) {
            try { $GLOBALS['eZDBGlobalInstance']->close(); } catch (\Throwable $e) {}
        }

        $clearPrefixes = array(
            // The request and where it routed -- the actual contamination.
            'eZRequestedModule', 'eZRequestedModuleParams', 'eZRequestedURI',
            'eZURIRequestInstance', 'eZGlobalRequestURI', 'eZModuleViewStack',
            'eZSysServerPort', 'eZCurrentAccess', 'eZGlobalRequestURI',
            // Per-visitor identity.
            'eZUserGlobalInstance', 'eZUserBuiltins',
            // Connection and request tool -- reopened/rebuilt per request anyway.
            'eZDBGlobalInstance', 'eZHTTPToolInstance', 'eZExpiryHandlerInstance',
            // The template override cache map is built for the pages the warm-up
            // rendered and no others; kept partial it starved other page types (the
            // search results template) of their CSS, because the override that loads
            // it was not in the map. Cleared, each request rebuilds a complete map for
            // its own templates.
            'eZOverrideTemplateCacheMap',
            // The design names, read from the rendered siteaccess's site.ini and
            // cached here for the rest of the process, and that siteaccess's basics
            // (site-design-override among them). Kept, a worker's first request under
            // any other siteaccess resolved templates through the public site's
            // design: every admin view in a freshly forked worker compiled the media
            // theme's header template and failed with array_unique() on an empty
            // ezini() value -- a 500 for every signed-in request in fork-per-request
            // mode, and for any worker forked after start. Each request reads its own.
            'eZTemplateDesignSetting', 'eZSiteBasics',
            // The rest of the design resolver's state, all of it the rendered
            // siteaccess's: its design bases and start path, design keys and the
            // resolver instance holding them. Kept, a fresh worker's first admin
            // request built the admin template override map from the public site's
            // design and wrote it to var/site/cache/override -- a file Apache reads
            // too -- so admin broke on both servers (2026-09-24). Rebuilt per request,
            // from that request's siteaccess, as a fresh Apache request does.
            'eZTemplateDesignResource', 'eZDesignKeys', 'eZDesignOverrides',
            // The rendered siteaccess's language, and everything built from it: the
            // locale eZLocale::instance() hands out when asked for none
            // (eZLocaleStringDefault, eng-US here), the translator manager with that
            // locale's translation file registered as its first handler, the loaded
            // translation tables and contexts, and the content languages in the
            // siteaccess's order. Kept, a request under a translated siteaccess
            // (bold_ger, ger-DE) registered its own translation behind the warm-up's
            // and the manager asked the English one first: html lang="de-DE" with
            // every interface string in English, "We couldn't find that page" where
            // Apache said "Diese Seite haben wir nicht gefunden" (2026-09-29). Each
            // request builds them for its own siteaccess, as a fresh process does.
            'eZLocale', 'eZTSTranslationTables', 'eZTranslatorManagerInstance',
            'eZTranslationCache', 'eZContentLanguage',
        );
        foreach (array_keys($GLOBALS) as $__g) {
            foreach ($clearPrefixes as $__p) {
                if (strncmp($__g, $__p, strlen($__p)) === 0) {
                    unset($GLOBALS[$__g]);
                    break;
                }
            }
        }

        // ezjscore is kept whole above, but its "assets already emitted" flag and its
        // persistent variable ARE request state -- frozen, the search page skipped its
        // CSS. Reset just those two, by name.
        foreach (array('ezjscPackerTemplateFunctions', 'ezjscPacker') as $__pk) {
            if (!class_exists($__pk)) continue;
            foreach (array('loaded' => array(), 'persistentVariable' => null) as $__pn => $__pv) {
                try {
                    $__rp = new \ReflectionProperty($__pk, $__pn);
                    $__rp->setValue(null, $__pv);
                } catch (\Throwable $e) {}
            }
        }

        // Singletons kept outside $GLOBALS: the request parser and the kernel. Drop
        // them so the next real request parses itself from scratch.
        // Whether text is translated at all is the siteaccess's TextTranslation, read
        // once and kept in a static; the pool restores statics to what they are here,
        // so the warm-up's answer would hold for every siteaccess. Forget it.
        if (class_exists('ezpI18n', false) && method_exists('ezpI18n', 'reset')) {
            \ezpI18n::reset();
        }
        if (class_exists('eZSys') && method_exists('eZSys', 'setInstance')) {
            @\eZSys::setInstance(null);
        }
        if (class_exists('ezpKernel')) {
            try {
                $__k = new \ReflectionProperty('ezpKernel', 'instance');
                $__k->setValue(null, null);
            } catch (\Throwable $e) {}
        }
        // The session layer's state is the warm-up request's, and it is all static.
        // Kept, every worker inherited a registered handler: registerFunctions() then
        // returned at once, so it neither named the cookie (SessionNamePrefix, eZSESSID
        // here) nor noted whether the visitor sent one. The session went out as
        // PHPSESSID, the next request looked for eZSESSID, found nothing and served
        // the page anonymously -- a sign-in on the public site under Velocity was
        // accepted and forgotten on the next page (2026-09-24). Reset to what a fresh
        // request starts with.
        if (class_exists('eZSession', false)) {
            foreach (array('userID' => 0, 'hasStarted' => false, 'hasSessionCookie' => null,
                           'callbackFunctions' => array(), 'handlerInstance' => null, 'namespace' => null) as $__sn => $__sv) {
                try {
                    $__sp = new \ReflectionProperty('eZSession', $__sn);
                    $__sp->setValue(null, $__sv);
                } catch (\Throwable $e) {}
            }
        }

        // The content object cache holds the rendered page's objects; drop it through
        // eZ's own accessor rather than by guessing global names.
        if (class_exists('eZContentObject') && method_exists('eZContentObject', 'clearCache')) {
            try { \eZContentObject::clearCache(); } catch (\Throwable $e) {}
        }

        // The breadcrumb read a frozen "current node", and bisection
        // pinned it to ONE class: eZTemplate.
        // A render leaves the last-rendered node in eZTemplate's static state, and the
        // home page's breadcrumb read it. Resetting that one class's statics to their
        // declared defaults fixes it -- and, unlike the broad reset this replaces, it
        // touches nothing else, so it does not crash the ezjscore AJAX path (the
        // load-more buttons) the way resetting thousands of statics did. Targeted, not
        // comprehensive: name the one class the render dirties in a way that leaks.
        //
        // eZTemplateDesignResource is the second: it keeps the whole template location
        // map in a static (overrideArrayCache), built here for the public siteaccess.
        // Kept, every worker's first admin request that compiled a template resolved
        // its includes through that map, and wrote the result to the compiled template
        // cache -- the admin pagelayout came out including the media theme's
        // page_header.tpl, and every signed-in admin page answered 500 with
        // array_unique() on an empty ezini() value (2026-09-24). The globals above were
        // cleared; the static was not.
        //
        // The rest hold the rendered siteaccess's settings, the anonymous visitor's
        // view of things, or data read from the database, each cached in a static the
        // pool puts back before every request (2026-09-30):
        //   eZContentObjectTreeNode  ShowHiddenNodes: false for the public site, so
        //                            admin (true) answered "access denied" for a
        //                            hidden node and counted one sub item fewer --
        //                            and the view cache kept that page for Apache too
        //   eZURLAliasML             the node paths the front page looked up, in the
        //                            public site's languages; a renamed node kept its
        //                            old URL in every worker until a restart
        //   ezjscPacker              the public site's index dir (/site/) and cache
        //                            dirs, used in packed file names and generated code
        //   ezjscServerFunctionsJs   the same for the ezjscore JavaScript helpers
        //   eZImageManager           image.ini of the public site: aliases, handlers
        //   eZContentObjectStateGroup  the anonymous visitor's state limitations
        //   eZContentClass           the class identifier map as the warm-up read it
        //   eZUser                   the anonymous user id of the public site
        // Each is reset to its declared default, as a fresh process has it.
        foreach (array('eZTemplate', 'eZTemplateDesignResource', 'eZContentObjectTreeNode',
                       'eZURLAliasML', 'ezjscPacker', 'ezjscServerFunctionsJs', 'eZImageManager',
                       'eZContentObjectStateGroup', 'eZContentClass',
                       'eZUser') as $__cls) {
            if (!class_exists($__cls, false)) continue;
            try {
                $__rc = new \ReflectionClass($__cls);
                foreach ($__rc->getProperties(\ReflectionProperty::IS_STATIC) as $__prop) {
                    if (!$__prop->hasDefaultValue()) continue;
                    if ($__prop->getDeclaringClass()->getName() !== $__cls) continue;
                    $__prop->setValue(null, $__prop->getDefaultValue());
                }
            } catch (\Throwable $e) {}
        }

        // Configuration, last. eZINI keeps its parsed files and its list of override
        // directories -- including the siteaccess directory -- in static properties,
        // and the pool restores statics to what they are at the end of this script
        // before every request. The render above went through the public siteaccess,
        // so every worker began every request with the public site's site.ini
        // already loaded: an admin request then switched siteaccess on top of it and
        // still read PathPrefix=fit-healthy, so /admin/bold-agency was looked up as
        // fit-healthy/bold-agency and answered 404. Reset to what a fresh process has:
        // no instances, the default override directories. Each request then loads
        // the configuration of its own siteaccess.
        if (class_exists('eZINI', false)) {
            \eZINI::resetAllInstances(true);
        }

        // The connection is CLOSED, not only forgotten, so no worker shares the
        // parent's socket. Setting the global to null left the connection open
        // wherever else it was still referenced, and every forked worker inherited
        // that one socket: concurrent workers answered "Commands out of sync", and
        // each worker that exited sent QUIT down it, so the others got "MySQL server
        // has gone away". With a fresh worker per request that was every request, and
        // the failed queries surfaced as "Call to a member function attribute() on
        // array" all over the front page. Each worker opens its own connection.
        if (isset($GLOBALS['eZDBGlobalInstance']) and is_object($GLOBALS['eZDBGlobalInstance'])
            and method_exists($GLOBALS['eZDBGlobalInstance'], 'close')) {
            try { $GLOBALS['eZDBGlobalInstance']->close(); } catch (\Throwable $e) {}
        }
        $GLOBALS['eZDBGlobalInstance'] = null;

        // Every other global the render left. A pool worker removes the globals a
        // request added before its next request, keeping only the server's own and
        // the ones named in Q.webserver.keepGlobals (the datatype and workflow
        // registries). A worker forked for each request never gets there: it starts
        // from the parent as this script leaves it, so every global the render set was
        // every request's -- the custom HTTP header switch worked out for an anonymous
        // visitor (eZHTTPHeaderCustom), the SSL zone switch, the current module view,
        // the module repositories, the navigation parts, the XML schema and image
        // analyzer of the public siteaccess, the class attribute cache as the
        // database had it at start, the warm-up's debug messages. The first request of
        // a pool worker saw them too. Removed here, both kinds of worker start the way
        // a pool worker's second request does. The named list above stays: it is what
        // makes the intent reviewable, and it runs before the connection is closed.
        $__warmupKeep = array_flip(array('GLOBALS', '_GET', '_POST', '_COOKIE', '_SERVER', '_REQUEST',
            '_FILES', '_ENV', '_SESSION', 'argv', 'argc', '_Q_RAW_INPUT'));
        if (class_exists('Q_Config', false)) {
            $__warmupExtra = \Q_Config::get('Q', 'webserver', 'keepGlobals', array());
            if (is_string($__warmupExtra)) {
                $__warmupExtra = preg_split('/\s*,\s*/', $__warmupExtra, -1, PREG_SPLIT_NO_EMPTY);
            }
            foreach ((array)$__warmupExtra as $__g) {
                $__warmupKeep[(string)$__g] = true;
            }
        }
        foreach (array_keys($GLOBALS) as $__g) {
            if (!isset($__warmupGlobalsBefore[$__g]) && !isset($__warmupKeep[$__g])) {
                unset($GLOBALS[$__g]);
            }
        }

        // Clean-up done; now the pool may hear that the warm-up failed.
        if ($__warmupFailure !== null) {
            throw $__warmupFailure;
        }
    }
}

}
