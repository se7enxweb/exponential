<?php
/**
 * File containing the expRenderSwitch class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The full-render switch of the debug bar: the view parameter "(exp_render)/full" anywhere in the path of an address
 * (/fitness/(exp_render)/full, /(exp_render)/full for the front page, content/view/full/89/(exp_render)/full).
 *
 * A visitor the debug bar is shown to (DebugIPList, DebugByUser) is answered from the role-aware page cache when it
 * has the page, with the bar of the render that stored it (ezpHttpCacheContract); with the switch the page is
 * rendered by the kernel instead, as before. For anyone else the switch means nothing.
 *
 * The switch is taken out of the request's address before anything reads it (apply(): REQUEST_URI and PATH_INFO),
 * so it never reaches the URL alias lookup, the view parameters, the content view cache key, the cache-block keys,
 * the templates, the links a page makes or the HTTP cache key. Only asked() remembers it for the request.
 *
 * Loaded without the autoloader as well (the early exit of the HTTP cache, before the kernel): no dependencies.
 *
 * @package kernel
 */
class expRenderSwitch
{
    /** The view parameter, as it is written in an address */
    const NAME = 'exp_render';

    /** Its one value */
    const VALUE = 'full';

    /** @var bool|null whether this request asked for a full render (apply()) */
    private static $asked = null;

    /** @var string|null the request this state belongs to (REQUEST_TIME_FLOAT, set per request also by Velocity) */
    private static $request = null;

    /**
     * $uri without the switch, and whether it had it. The query string is left alone; an address that was only the
     * switch becomes "/".
     *
     * @param string $uri a request URI (path and query)
     * @return array array( string $uri, bool $found )
     */
    public static function strip( $uri )
    {
        $uri = (string)$uri;
        $q = strpos( $uri, '?' );
        $path = $q === false ? $uri : substr( $uri, 0, $q );
        $query = $q === false ? '' : substr( $uri, $q );
        // "(exp_render)/full", also percent-encoded, as a whole path segment pair
        $pattern = '#/(?:\(|%28)' . self::NAME . '(?:\)|%29)/' . self::VALUE . '(?=/|$)#i';
        $stripped = preg_replace( $pattern, '', $path, -1, $count );
        if ( !$count )
            return array( $uri, false );
        if ( $stripped === '' )
            $stripped = '/';
        return array( $stripped . $query, true );
    }

    /**
     * $uri with the switch: after the path, before the query string. The front page of a siteaccess ("/twig/")
     * becomes "/twig/(exp_render)/full".
     *
     * @param string $uri
     * @return string
     */
    public static function add( $uri )
    {
        list( $uri ) = self::strip( $uri );
        $q = strpos( $uri, '?' );
        $path = $q === false ? $uri : substr( $uri, 0, $q );
        $query = $q === false ? '' : substr( $uri, $q );
        return rtrim( $path, '/' ) . '/(' . self::NAME . ')/' . self::VALUE . $query;
    }

    /**
     * Takes the switch out of this request's address ($_SERVER REQUEST_URI and PATH_INFO) and remembers whether it
     * was there. Called by the early exit of the HTTP cache and by the web kernel before it reads the address;
     * calling it again in the same request changes nothing.
     *
     * @return bool whether the request asked for a full render
     */
    public static function apply()
    {
        $request = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        if ( self::$asked !== null && self::$request === $request )
        {
            // a later apply() of the same request: the address may only have been set again by something else
            list( $_SERVER['REQUEST_URI'] ) = self::strip( $_SERVER['REQUEST_URI'] ?? '/' );
            return self::$asked;
        }
        self::$request = $request;
        list( $uri, $found ) = self::strip( $_SERVER['REQUEST_URI'] ?? '/' );
        $_SERVER['REQUEST_URI'] = $uri;
        if ( isset( $_SERVER['PATH_INFO'] ) && $_SERVER['PATH_INFO'] !== '' )
        {
            list( $pathInfo, $inPathInfo ) = self::strip( $_SERVER['PATH_INFO'] );
            if ( $inPathInfo )
            {
                $_SERVER['PATH_INFO'] = $pathInfo === '/' && substr( (string)$_SERVER['PATH_INFO'], 0, 1 ) !== '/' ? '' : $pathInfo;
                $found = true;
            }
        }
        return self::$asked = $found;
    }

    /**
     * Whether this request asked for a full render (apply() saw the switch). Only the debug bar's visitors are
     * answered differently for it.
     *
     * @return bool
     */
    public static function asked()
    {
        $request = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        return self::$asked === true && self::$request === $request;
    }

    /**
     * Forgets the request (tests).
     */
    public static function reset()
    {
        self::$asked = null;
        self::$request = null;
    }
}
