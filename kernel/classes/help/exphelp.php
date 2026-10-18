<?php
/**
 * The help system's shared helpers (doc/features/6.0/help-system.md): its settings, whether it is on, the install
 * root it reads from, and the URLs of its views. Every other expHelp* class takes its settings from here.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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
     * @param array $query extra GET parameters
     * @return string e.g. "/exphelp/viewer/features/6.0/help-system.md#context-help"
     */
    public static function url( $view, $id = '', $anchor = '', array $query = array() )
    {
        $url = '/exphelp/' . $view;
        if ( $id !== '' )
            $url .= '/' . implode( '/', array_map( 'rawurlencode', explode( '/', ltrim( (string)$id, '/' ) ) ) );
        if ( $query )
            $url .= '?' . http_build_query( $query, '', '&', PHP_QUERY_RFC3986 );
        if ( $anchor !== '' )
            $url .= '#' . rawurlencode( (string)$anchor );
        return $url;
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
                      'clear' => ezpI18n::tr( $c, 'Clear' ) );
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
     * The page or asset id a request names: the rest of the address ($Params['Parameters']) or GET p.
     * Only syntax is checked here; whether the file may be served is expHelpDocumentIndex::resolve()'s job.
     *
     * @param array $params the view's $Params
     * @param array $get usually $_GET
     * @return string '' when none was given
     */
    public static function requestedId( array $params, array $get )
    {
        $parts = isset( $params['Parameters'] ) && is_array( $params['Parameters'] ) ? $params['Parameters'] : array();
        $id = $parts ? implode( '/', array_map( 'strval', $parts ) ) : ( isset( $get['p'] ) && is_string( $get['p'] ) ? $get['p'] : '' );
        $id = rawurldecode( $id );
        return trim( str_replace( '\\', '/', $id ), '/' );
    }
}

?>
