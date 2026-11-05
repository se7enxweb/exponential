<?php
/**
 * The help system's shared helpers (doc/eng-us/features/6.0/help-system.md): its settings, whether it is on, the install
 * root it reads from, and the URLs of its views. Every other expHelp* class takes its settings from here.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelp
{
    /** The policy function every view needs */
    const POLICY_FUNCTION = 'read';

    /** The translation context of the module's strings */
    const I18N_CONTEXT = 'kernel/exphelp';

    /**
     * exphelp.ini
     *
     * @return eZINI
     */
    public static function ini()
    {
        return eZINI::instance( 'exphelp.ini' );
    }

    /**
     * A setting of exphelp.ini, or $default when it is not set.
     *
     * @param string $group
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $group, $name, $default = null )
    {
        $ini = self::ini();
        return $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : $default;
    }

    /**
     * Whether the help system answers at all ([HelpSettings] Enabled).
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return self::setting( 'HelpSettings', 'Enabled', 'true' ) === 'true';
    }

    /**
     * Whether the current user holds exphelp/read.
     *
     * @return bool
     */
    public static function canRead()
    {
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser )
            return false;
        $access = $user->hasAccessTo( 'exphelp', self::POLICY_FUNCTION );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * The installation root (realpath, no trailing slash); every served file must be below it and below a doc root.
     *
     * @return string
     */
    public static function installRoot()
    {
        $root = realpath( eZSys::rootDir() );
        return $root === false ? rtrim( (string)eZSys::rootDir(), '/' ) : rtrim( $root, '/' );
    }

    /**
     * The directory of the help caches: <cache dir>/exphelp ([HelpSettings] CacheDir).
     *
     * @return string
     */
    public static function cacheDirectory()
    {
        return eZSys::cacheDirectory() . '/' . trim( (string)self::setting( 'HelpSettings', 'CacheDir', 'exphelp' ), '/' );
    }

    /**
     * The module-relative URL of a view (not yet passed through ezurl): viewer/<id>, asset/<id>, search, ...
     *
     * @param string $view dashboard|viewer|search|asset|report
     * @param string $id a page or asset id (path below doc/, or ext/<extension>/<path> for extension docs)
     * @param string $anchor a heading anchor, without #
     * @param array $query view parameters, name => value, appended as /(name)/value (never a GET query string)
     * @return string e.g. "/exphelp/viewer/features/6.0/help-system.md#context-help", "/exphelp/viewer/x.md/(lang)/eng-us"
     */
    public static function url( $view, $id = '', $anchor = '', array $query = array() )
    {
        $url = '/exphelp/' . $view;
        if ( $id !== '' )
            $url .= '/' . implode( '/', array_map( 'rawurlencode', explode( '/', ltrim( (string)$id, '/' ) ) ) );
        foreach ( $query as $name => $value )
        {
            if ( is_scalar( $value ) && (string)$value !== '' && preg_match( '/^[a-z][a-z0-9_]*$/', (string)$name ) )
                $url .= '/(' . $name . ')/' . rawurlencode( (string)$value );
        }
        if ( $anchor !== '' )
            $url .= '#' . rawurlencode( (string)$anchor );
        return $url;
    }

    /** The heading of the page [ContextHelp] Map[exphelp/report] names that the security link opens, without "#" */
    const REPORT_SECURITY_ANCHOR = 'security-problems';

    /**
     * Where "Report an error" and its security link lead (doc/eng-us/features/6.0/help-system.md, "Reporting an
     * error"): the view exphelp/report and its section #security when this siteaccess allows the view
     * (site.ini [SiteAccessRules]; the view needs exphelp/read like every other view of the help, so a reader who
     * sees the link may open it), else the help page [ContextHelp] Map[exphelp/report] names (exphelp/viewer/<page>)
     * and its security section ([ContextHelp] ReportSecurityAnchor, by default #security-problems; on a translated
     * page the heading in the same position, #sicherheitsprobleme on the German page), else the help dashboard. A
     * site that switches the view off never links an address that answers 404.
     *
     * @param bool|null $viewAllowed whether exphelp/report is reachable here; null asks eZModule::accessAllowed()
     * @return array array( 'url' => module-relative address (not yet passed through ezurl), 'security' => the same
     *               with the security anchor, 'view' => bool, true when it is the view exphelp/report )
     */
    public static function reportLinks( $viewAllowed = null )
    {
        if ( $viewAllowed === null )
        {
            $access = eZModule::accessAllowed( new eZURI( 'exphelp/report' ) );
            $viewAllowed = !empty( $access['result'] );
        }
        if ( $viewAllowed )
            return array( 'url' => self::url( 'report' ), 'security' => self::url( 'report', '', 'security' ), 'view' => true );

        $map = (array)self::setting( 'ContextHelp', 'Map', array() );
        $target = isset( $map['exphelp/report'] ) && is_scalar( $map['exphelp/report'] )
            ? expHelpContextRegistry::parseTarget( trim( (string)$map['exphelp/report'] ) ) : null;
        if ( $target === null || $target['id'] === '' )
        {
            $dashboard = self::url( 'dashboard' );
            return array( 'url' => $dashboard, 'security' => $dashboard, 'view' => false );
        }
        $security = trim( (string)self::setting( 'ContextHelp', 'ReportSecurityAnchor', self::REPORT_SECURITY_ANCHOR ) );
        if ( !preg_match( '#^[\p{L}\p{N}_-]{1,200}$#u', $security ) )
            $security = self::REPORT_SECURITY_ANCHOR;
        // the reader may see the page in another language: its heading in the same position
        try
        {
            $security = expHelpDocumentIndex::instance()->anchorOf( $target['id'], $security );
        }
        catch ( Throwable $e )
        {
        }
        return array( 'url' => self::url( 'viewer', $target['id'], $target['anchor'] ),
                      'security' => self::url( 'viewer', $target['id'], $security ),
                      'view' => false );
    }

    /** The view parameters of exphelp/search, in the order of the canonical address. */
    const SEARCH_PARAMS = array( 'q', 'category', 'audience', 'type', 'extension', 'offset', 'limit' );

    /**
     * The canonical address of a search or browse state: view parameters, never GET, only the ones that are set,
     * always in the order q, category, audience, type, offset, limit
     * ("/exphelp/search/(q)/upload+image/(category)/layouts/(offset)/20"). Words are percent-encoded with "+" for
     * spaces; "/", "(" and ")" in them become spaces, since the address splits on them (and the search splits words
     * on them anyway). An offset of 0 and a limit of $defaultLimit are left out.
     *
     * @param array $state q (string), category, audience, type (string or list), offset, limit (int)
     * @param int $defaultLimit
     * @return string module-relative, for |ezurl or expHelpLinks::absolute()
     */
    public static function searchUrl( array $state, $defaultLimit = 20 )
    {
        $url = '/exphelp/search';
        foreach ( self::SEARCH_PARAMS as $name )
        {
            if ( !isset( $state[$name] ) )
                continue;
            $value = $state[$name];
            if ( $name === 'q' )
            {
                $value = self::searchWords( $value );
                if ( $value === '' )
                    continue;
                $value = str_replace( '%20', '+', rawurlencode( $value ) );
            }
            elseif ( $name === 'offset' || $name === 'limit' )
            {
                $value = (int)$value;
                if ( $value <= 0 || ( $name === 'limit' && $value === (int)$defaultLimit ) )
                    continue;
                $value = (string)$value;
            }
            else
            {
                $values = is_array( $value ) ? $value : explode( ',', (string)$value );
                $values = array_values( array_filter( array_map( 'strval', $values ), function ( $v ) {
                    return preg_match( '/^[a-z0-9][a-z0-9_.-]{0,63}$/', $v ) === 1;
                } ) );
                if ( !$values )
                    continue;
                $value = implode( ',', array_unique( $values ) );
            }
            $url .= '/(' . $name . ')/' . $value;
        }
        return $url;
    }

    /**
     * The words of a search as they go into an address: trimmed, white space collapsed, "/", "(", ")" and control
     * characters as spaces, at most 200 characters.
     *
     * @param mixed $q
     * @return string
     */
    public static function searchWords( $q )
    {
        if ( !is_string( $q ) )
            return '';
        if ( !mb_check_encoding( $q, 'UTF-8' ) )
            $q = mb_convert_encoding( $q, 'UTF-8', 'UTF-8' );
        $q = preg_replace( '/[\x00-\x1F\x7F\/()\\\\]+/u', ' ', $q );
        $q = trim( preg_replace( '/\s+/u', ' ', (string)$q ) );
        return mb_substr( $q, 0, 200, 'UTF-8' );
    }

    /**
     * Rows of the index or of exphelp.ini (audiences, categories, trackers) with their display names in the interface
     * language: each $keys field goes through the kernel/exphelp context. The index caches the names in English for
     * every language, so the views translate them when they hand them to a template.
     *
     * @param array $rows id => array( name ... )
     * @param string[] $keys the fields to translate
     * @return array
     */
    public static function translatedNames( array $rows, array $keys = array( 'name' ) )
    {
        if ( !class_exists( 'ezpI18n' ) )
            return $rows;
        foreach ( $rows as $id => $row )
        {
            foreach ( $keys as $key )
            {
                if ( is_array( $row ) && isset( $row[$key] ) && is_string( $row[$key] ) && $row[$key] !== '' )
                    $rows[$id][$key] = ezpI18n::tr( self::I18N_CONTEXT, $row[$key] );
            }
        }
        return $rows;
    }

    /**
     * The translated strings the client script (exphelp.js) shows, keyed as it reads them.
     *
     * @return array<string,string>
     */
    public static function clientStrings()
    {
        $c = self::I18N_CONTEXT;
        return array( 'copy' => ezpI18n::tr( $c, 'Copy' ),
                      'copyLabel' => ezpI18n::tr( $c, 'Copy code' ),
                      'copied' => ezpI18n::tr( $c, 'Copied' ),
                      'copyFailed' => ezpI18n::tr( $c, 'Copying failed' ),
                      'noResults' => ezpI18n::tr( $c, 'No results' ),
                      'resultsCount' => ezpI18n::tr( $c, '%count results' ),
                      'resultCount' => ezpI18n::tr( $c, '1 result' ),
                      'searching' => ezpI18n::tr( $c, 'Searching...' ),
                      'error' => ezpI18n::tr( $c, 'The search is not available right now.' ),
                      'collapse' => ezpI18n::tr( $c, 'Hide section: %title' ),
                      'expand' => ezpI18n::tr( $c, 'Show section: %title' ),
                      'relaxed' => ezpI18n::tr( $c, 'No page has every word; these have some of them.' ),
                      'clear' => ezpI18n::tr( $c, 'Clear' ),
                      'recordsFilter' => ezpI18n::tr( $c, 'Filter' ),
                      'recordsPlaceholder' => ezpI18n::tr( $c, 'Version, name, year or revision' ),
                      'recordsCount' => ezpI18n::tr( $c, '%count of %total entries' ),
                      'sortBy' => ezpI18n::tr( $c, 'Sort by %title' ),
                      'navCount' => ezpI18n::tr( $c, '%count pages match' ),
                      'navNone' => ezpI18n::tr( $c, 'No page title matches' ),
                      'allOpen' => ezpI18n::tr( $c, 'Every folder is open.' ),
                      'allClosed' => ezpI18n::tr( $c, 'Every folder is closed.' ) );
    }

    /**
     * clientStrings() as JSON for a data-exphelp-i18n attribute (wash it in the template).
     *
     * @return string
     */
    public static function clientStringsJson()
    {
        return (string)json_encode( self::clientStrings(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE );
    }

    /**
     * The page or asset id a request names: the rest of the address ($Params['Parameters']; view parameters such as
     * (lang) are not part of it). Never a GET parameter. Only syntax is checked here; whether the file may be served is
     * expHelpDocumentIndex::resolve()'s job.
     *
     * @param array $params the view's $Params
     * @return string '' when none was given
     */
    public static function requestedId( array $params )
    {
        $parts = isset( $params['Parameters'] ) && is_array( $params['Parameters'] ) ? $params['Parameters'] : array();
        $id = implode( '/', array_map( 'strval', $parts ) );
        $id = rawurldecode( $id );
        return trim( str_replace( '\\', '/', $id ), '/' );
    }

    /** The uiswitch provider whose value is the reader's language (doc/eng-us/features/6.0/ui-switch.md) */
    const READING_PROVIDER = 'language';

    /**
     * The language a help page is asked for with the view parameter /(lang)/<key> (eng-us, ger-de; the old keys
     * en-us and de of 6.0.14 are still accepted): a configured directory other than the reader's, else null. The
     * viewer then shows the page of that language for this one request (the English original of a translated page),
     * without changing the reader's interface language. A GET ?lang= is not read.
     *
     * @param array $viewParameters the view's user parameters ($Params['UserParameters']), name => value
     * @param expHelpLanguage $languages
     * @param string $reader the reader's directory
     * @return string|null
     */
    public static function requestedLanguage( array $viewParameters, expHelpLanguage $languages, $reader )
    {
        if ( !isset( $viewParameters['lang'] ) || !is_string( $viewParameters['lang'] ) || strlen( $viewParameters['lang'] ) > 16 )
            return null;
        $directory = method_exists( $languages, 'resolveDirectory' ) ? $languages->resolveDirectory( $viewParameters['lang'] ) : null;
        return $directory !== null && $directory !== $reader ? $directory : null;
    }

    /** The help views that keep a reading language in their links (/(lang)/<key>): every view of a page a reader
     *  reads or a form they send; not the files (asset, image) nor the JSON of the header's Help menu (context) */
    const READING_VIEWS = array( 'dashboard', 'viewer', 'contents', 'navigation', 'search', 'audience', 'extensions',
                                 'topic', 'manual', 'pdf', 'report', 'feedback', 'state' );

    /**
     * The language a reader who cannot use the interface language switch reads the help in for this request: the
     * view parameter /(lang)/<key> when it names a configured language other than the one of the interface locale,
     * else null. A reader who can switch (signed in, uiswitch/language, a switch siteaccess) gets null: for them
     * /(lang)/ keeps showing one page in another language (requestedLanguage()), and the switch changes the rest.
     *
     * @param array $viewParameters the view's user parameters, name => value
     * @param expHelpLanguage $languages
     * @param string $interface the language of the interface locale (expHelpLanguage::interfaceLanguage())
     * @param bool $canSwitch whether the reader can use the interface language switch (canSwitchReading())
     * @return string|null
     */
    public static function browsingLanguage( array $viewParameters, expHelpLanguage $languages, $interface, $canSwitch )
    {
        return $canSwitch ? null : self::requestedLanguage( $viewParameters, $languages, $interface );
    }

    /**
     * Whether the current user can choose the help's language with the interface language switch on this siteaccess.
     *
     * @return bool
     */
    public static function canSwitchReading()
    {
        return self::switchProvider() !== null;
    }

    /**
     * The interface language switch's provider when the current user may use it on this siteaccess, else null.
     *
     * @return expUISwitchProviderInterface|null
     */
    protected static function switchProvider()
    {
        try
        {
            if ( class_exists( 'expUISwitch' ) && expUISwitch::isEnabled() && expUISwitch::isSwitchSiteAccess()
                 && expUISwitch::canUse( self::READING_PROVIDER ) )
            {
                $provider = expUISwitch::provider( self::READING_PROVIDER );
                return $provider ? $provider : null;
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'The language switch of the help is not available: ' . $e->getMessage(), __METHOD__ );
        }
        return null;
    }

    /**
     * Called by every help view before it runs: a reader without the interface language switch who asked for
     * /(lang)/<key> reads the help in that language for this request (expHelpLanguage::setReading(), which
     * expHelpLanguage::current() and so expHelpDocumentIndex::instance() follow); anything else forgets an earlier
     * choice. endReading() then keeps the language in the view's links.
     *
     * @param array $params the view's $Params
     * @return string|null the language chosen for this request
     */
    public static function beginReading( array $params )
    {
        expHelpLanguage::setReading( null );
        $viewParameters = isset( $params['UserParameters'] ) && is_array( $params['UserParameters'] ) ? $params['UserParameters'] : array();
        if ( !isset( $viewParameters['lang'] ) || !self::isEnabled() )
            return null;
        $languages = expHelpLanguage::fromIni();
        $directory = self::browsingLanguage( $viewParameters, $languages, $languages->interfaceLanguage(), self::canSwitchReading() );
        expHelpLanguage::setReading( $directory );
        return $directory;
    }

    /**
     * The /(lang)/ value the help's links carry in this request: the key of the language beginReading() chose, or ''.
     *
     * @return string
     */
    public static function readingKey()
    {
        $directory = expHelpLanguage::reading();
        return $directory !== null ? expHelpLanguage::key( $directory ) : '';
    }

    /**
     * A help address with the view parameter /(lang)/<key> (before the #anchor), when it is the address of a help
     * view of READING_VIEWS and has no (lang) yet; any other address unchanged. Works for module-relative addresses
     * (/exphelp/..., exphelp/...), for those with the siteaccess path in front ($prefix: eZSys::indexDir()) and for
     * absolute ones (https://host/<prefix>/exphelp/...; callers pass only addresses of this site).
     *
     * @param string $url
     * @param string $key the language key ('' leaves the address as it is)
     * @param string $prefix the path in front of /exphelp/ ('' or e.g. /index.php/site)
     * @return string
     */
    public static function withLanguage( $url, $key, $prefix = '' )
    {
        $url = (string)$url;
        if ( $key === '' || !preg_match( '/^[a-z0-9-]{1,16}$/', (string)$key ) )
            return $url;
        $anchor = '';
        if ( ( $hash = strpos( $url, '#' ) ) !== false )
        {
            $anchor = substr( $url, $hash );
            $url = substr( $url, 0, $hash );
        }
        $prefix = rtrim( (string)$prefix, '/' );
        $views = implode( '|', self::READING_VIEWS );
        // an absolute address of this site (expHelpLinks::absolute() for the search's JSON) keeps its origin
        $pattern = '#^(?:https?://[^/?\#]+)?(?:' . preg_quote( $prefix, '#' ) . ')?/?exphelp/(?:' . $views . ')(?:/|$)#';
        if ( strpos( $url, '?' ) !== false || strpos( $url, '/(lang)/' ) !== false || !preg_match( $pattern, $url ) )
            return $url . $anchor;
        return rtrim( $url, '/' ) . '/(lang)/' . $key . $anchor;
    }

    /**
     * Adds /(lang)/<key> to every help address of an HTML fragment (withLanguage()) that sits in an attribute: the
     * links (href), the forms (action) and the addresses the client script reads (data-*: the search's endpoint and
     * canonical base, the page tree's folders). Addresses of other sites and of the files (asset, image) stay.
     *
     * @param string $html
     * @param string $key
     * @param string $prefix eZSys::indexDir()
     * @return string
     */
    public static function htmlWithLanguage( $html, $key, $prefix = '' )
    {
        $html = (string)$html;
        if ( $key === '' || strpos( $html, 'exphelp/' ) === false )
            return $html;
        $result = preg_replace_callback( '#(\s(?:href|action|data-[a-z0-9-]+)=)(["\'])(/[^"\'<>\s]*)\2#i', function ( $m ) use ( $key, $prefix ) {
            return $m[1] . $m[2] . self::withLanguage( $m[3], $key, $prefix ) . $m[2];
        }, $html );
        return $result !== null ? $result : $html;
    }

    /**
     * Called by every help view with its result: when beginReading() chose a language, its links, forms, path
     * (breadcrumbs) and a redirect keep /(lang)/<key>, so the reader stays in that language while browsing the help.
     * The rendered pages and their caches are not touched: the language is added to the output of each request.
     *
     * @param mixed $result the view's result
     * @param eZModule|null $module
     * @return mixed
     */
    public static function endReading( $result, $module = null )
    {
        $key = self::readingKey();
        if ( $key === '' )
            return $result;
        $prefix = class_exists( 'eZSys' ) ? (string)eZSys::indexDir() : '';
        if ( is_array( $result ) )
        {
            if ( isset( $result['content'] ) && is_string( $result['content'] ) )
                $result['content'] = self::htmlWithLanguage( $result['content'], $key, $prefix );
            if ( isset( $result['path'] ) && is_array( $result['path'] ) )
            {
                foreach ( $result['path'] as $i => $item )
                    if ( is_array( $item ) && isset( $item['url'] ) && is_string( $item['url'] ) && $item['url'] !== '' )
                        $result['path'][$i]['url'] = self::withLanguage( $item['url'], $key, $prefix );
            }
        }
        if ( $module instanceof eZModule && $module->exitStatus() == eZModule::STATUS_REDIRECT && is_string( $module->redirectURI() ) )
            $module->setRedirectURI( self::withLanguage( $module->redirectURI(), $key, $prefix ) );
        return $result;
    }

    /**
     * An address of this request's help with its reading language (withLanguage() with readingKey()), for the
     * addresses a view writes itself: a redirect it sends, the JSON of the search, the page tree's fragments.
     *
     * @param string $url
     * @return string
     */
    public static function carryReading( $url )
    {
        $key = self::readingKey();
        return $key === '' ? (string)$url : self::withLanguage( $url, $key, class_exists( 'eZSys' ) ? (string)eZSys::indexDir() : '' );
    }

    /**
     * What the "Reading in" control offers a reader who cannot use the interface language switch (pure): a link per
     * language that has pages (the default always has them), to the same help address with /(lang)/<key>, labelled
     * with the language's own name ("Deutsch", "English"; $labels, else its name in [DocLanguages] Names[]).
     *
     * @param expHelpLanguage $languages
     * @param string $reader the language shown
     * @param array $coverage expHelpDocumentIndex::languageCoverage()
     * @param array $labels directory => the language's own name
     * @param string $back the module-relative address of the page (/exphelp/dashboard, /exphelp/viewer/<id>)
     * @return array directory, key, label, lang, url, current
     */
    public static function readingLinks( expHelpLanguage $languages, $reader, array $coverage, array $labels, $back )
    {
        $default = $languages->defaultDirectory();
        $links = array();
        foreach ( $languages->directories() as $directory )
        {
            $pages = isset( $coverage[$directory]['pages'] ) ? (int)$coverage[$directory]['pages'] : 0;
            if ( $directory !== $default && $pages === 0 && $directory !== $reader )
                continue;
            $key = expHelpLanguage::key( $directory );
            $label = isset( $labels[$directory] ) ? trim( preg_replace( '/\s*\(.*$/u', '', (string)$labels[$directory] ) ) : '';
            $links[] = array( 'directory' => $directory, 'key' => $key,
                              'label' => $label !== '' ? $label : $languages->name( $directory ),
                              'lang' => expHelpLanguage::htmlLang( $directory ),
                              'url' => self::withLanguage( (string)$back, $key ),
                              'current' => $directory === $reader );
        }
        return count( $links ) >= 2 ? $links : array();
    }

    /**
     * What the "Reading in" control of the help shows (pure: every input is given; reading() gathers them).
     *
     * A language is offered when it has pages (the default always has them) and a locale of the interface language
     * switch maps to it ([DocLanguages] Map[]): the current locale when it maps there, else the siteaccess's own
     * locale, else the first locale of Map[] that is allowed, else the first allowed one. The reader's language is
     * always listed. Its label is the language's own name without the country ("Deutsch", "English").
     *
     * @param expHelpLanguage $languages
     * @param string $reader the reader's directory
     * @param array $coverage expHelpDocumentIndex::languageCoverage()
     * @param string[] $allowed the locales the user may switch to (empty: no switch)
     * @param array $labels locale => the language's own name (the switch's label)
     * @param string $currentLocale
     * @param string $defaultLocale the siteaccess's own locale
     * @return array reader, can_switch, provider, choices (directory, key, label, name, lang, locale, current, pages, total),
     *               coverage (directory, name, pages, total: the translations that have pages)
     */
    public static function readingChoices( expHelpLanguage $languages, $reader, array $coverage, array $allowed, array $labels, $currentLocale, $defaultLocale )
    {
        $default = $languages->defaultDirectory();
        $mapOrder = $languages->localeOrder();
        $choices = array();
        $translations = array();
        foreach ( $languages->directories() as $directory )
        {
            $pages = isset( $coverage[$directory]['pages'] ) ? (int)$coverage[$directory]['pages'] : 0;
            $total = isset( $coverage[$directory]['total'] ) ? (int)$coverage[$directory]['total'] : 0;
            $isReader = $directory === $reader;
            if ( $directory !== $default && $pages > 0 )
                $translations[] = array( 'directory' => $directory, 'name' => self::languageName( $languages, $directory ), 'pages' => $pages, 'total' => $total );
            if ( $directory !== $default && $pages === 0 && !$isReader )
                continue;
            $candidates = array();
            foreach ( $allowed as $locale )
                if ( is_string( $locale ) && $languages->forLocale( $locale ) === $directory )
                    $candidates[] = $locale;
            $locale = '';
            foreach ( array_merge( array( (string)$currentLocale, (string)$defaultLocale ), $mapOrder ) as $preferred )
                if ( $preferred !== '' && in_array( $preferred, $candidates, true ) )
                {
                    $locale = $preferred;
                    break;
                }
            if ( $locale === '' && $candidates )
                $locale = $candidates[0];
            if ( $locale === '' && !$isReader )
                continue;
            $label = $locale !== '' && isset( $labels[$locale] ) ? trim( preg_replace( '/\s*\(.*$/u', '', (string)$labels[$locale] ) ) : '';
            $name = self::languageName( $languages, $directory );
            $choices[] = array( 'directory' => $directory, 'key' => expHelpLanguage::key( $directory ),
                                'label' => $label !== '' ? $label : $name, 'name' => $name,
                                'lang' => expHelpLanguage::htmlLang( $directory ), 'locale' => $locale,
                                'current' => $isReader, 'pages' => $pages, 'total' => $total );
        }
        $switchable = 0;
        foreach ( $choices as $choice )
            if ( $choice['locale'] !== '' )
                $switchable++;
        return array( 'reader' => $reader, 'reader_name' => self::languageName( $languages, $reader ),
                      'can_switch' => $allowed && $switchable >= 2, 'provider' => self::READING_PROVIDER,
                      'choices' => $choices, 'coverage' => $translations );
    }

    /**
     * A language's name in the reader's language ([DocLanguages] Names[], translated in kernel/exphelp).
     *
     * @param expHelpLanguage $languages
     * @param string $directory
     * @return string
     */
    public static function languageName( expHelpLanguage $languages, $directory )
    {
        $name = $languages->name( $directory );
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( self::I18N_CONTEXT, $name ) : $name;
    }

    /**
     * A language's own name (share/locale/<locale>.ini LanguageName: "Deutsch (Deutschland)"), '' when unknown.
     *
     * @param string $directory
     * @return string
     */
    public static function nativeName( $directory )
    {
        if ( !class_exists( 'eZLocale' ) )
            return '';
        try
        {
            $locale = eZLocale::create( expHelpLanguage::localeOf( $directory ) );
            return $locale && $locale->isValid() ? trim( (string)$locale->languageName() ) : '';
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /**
     * The "Reading in" control of the help for the current user: readingChoices() with the interface language
     * switch's choices when the user may use it on this siteaccess, plus 'back' (where the switch returns to) and the
     * page counts written as numbers of the reader's locale ('pages_text', 'total_text').
     *
     * @param expHelpDocumentIndex $index
     * @param string $back the module-relative address to come back to (/exphelp/viewer/<id>)
     * @return array
     */
    public static function reading( expHelpDocumentIndex $index, $back )
    {
        $languages = $index->languages();
        $allowed = array();
        $labels = array();
        $defaultLocale = '';
        try
        {
            $provider = self::switchProvider();
            if ( $provider )
            {
                $allowed = $provider->allowedValues();
                foreach ( $allowed as $locale )
                    $labels[$locale] = $provider->label( $locale );
                $defaultLocale = (string)$provider->defaultValue();
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'The language switch of the help is not available: ' . $e->getMessage(), __METHOD__ );
            $allowed = array();
        }
        $coverage = $index->languageCoverage();
        $reading = self::readingChoices( $languages, $languages->current(), $coverage, $allowed, $labels,
                                         expHelpLanguage::currentLocale(), $defaultLocale );
        // without the switch: a link per language, which the help's links then keep (beginReading(), endReading())
        $reading['links'] = array();
        if ( !$reading['can_switch'] )
        {
            $native = array();
            foreach ( $languages->directories() as $directory )
                $native[$directory] = self::nativeName( $directory );
            $reading['links'] = self::readingLinks( $languages, $languages->current(), $coverage, $native, $back );
        }
        $separator = class_exists( 'eZLocale' ) ? (string)eZLocale::instance()->thousandsSeparator() : ',';
        foreach ( $reading['coverage'] as $i => $row )
        {
            $reading['coverage'][$i]['pages_text'] = number_format( $row['pages'], 0, '', $separator );
            $reading['coverage'][$i]['total_text'] = number_format( $row['total'], 0, '', $separator );
        }
        $reading['back'] = class_exists( 'expUISwitch' ) ? expUISwitch::safeRedirect( $back, '/exphelp/dashboard' ) : '/exphelp/dashboard';
        return $reading;
    }
}

?>
