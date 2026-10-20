<?php
/**
 * The help system's shared helpers (doc/en/us/features/6.0/help-system.md): its settings, whether it is on, the install
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

    /** The view parameters of exphelp/search, in the order of the canonical address. */
    const SEARCH_PARAMS = array( 'q', 'category', 'audience', 'type', 'offset', 'limit' );

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
