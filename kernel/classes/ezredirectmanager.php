<?php
/**
 * File containing the eZRedirectManager class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZRedirectManager ezredirectmanager.php
  \brief Handles generation of redirection URIs and redirection

  It also holds the one set of rules for a redirect target that came from outside the code (a form field, the
  session, a URL parameter): safeURI() and unsafeReason(). eZModule::redirectTo() applies them to every redirect,
  redirectURI() to the preferred and the last viewed page, and returnURI() is "back to where you came from" for a
  Cancel button. The rules are described in doc/features/6.0/safe-redirects.md.
*/

class eZRedirectManager
{
    /**
     * The module views a "back to where you came from" redirect never returns to, when site.ini
     * [SiteSettings] DisallowedReturnViews is not available: views that act on a POST, end the session or start a
     * download, where a GET shows nothing or does something.
     */
    const DEFAULT_DISALLOWED_RETURN_VIEWS = array( 'content/action', 'content/removeobject', 'content/removenode',
                                                   'content/removeassignment', 'content/removeeditversion',
                                                   'content/download', 'user/logout', 'user/login', 'user/activate',
                                                   'user/success', 'user/forgotpassword', 'user/register',
                                                   'layout/set', 'shop/add' );

    /*!
     Generates a URI which can be used to redirect with, the uri is based on:
     - The last accessed view/non-view page if any (see \a $view parameter)
     - The uri is not the currently running module, if so use default
     - The default uri \a $default

     \return The new URI string or \c false if no uri could be made.

     \param $module The current module object
     \param $default The default URI to redirect to if all else fails.
                     If set to \c false then it will return false.
     \param $view If true it will try to redirect to last accessed view URI.
     \param $disallowed An array with urls not allowed to redirect to or \c false to allow all
     \param $preferredURI An URI that is preferred for the caller. If that URI is safe (safeURI()), it's returned.

     \note All URLs must start with a slash \c /
     \note The preferred URI and the page from the session must pass safeURI(); the default is the caller's own.

     \sa redirectTo()
    */
    static function redirectURI( $module, $default, $view = true, $disallowed = false, $preferredURI = false )
    {
        $uri = false;
        $http = eZHTTPTool::instance();

        if ( $preferredURI ) // check if $preferredURI is a valid URI
        {
            $preferred = self::safeURI( $preferredURI );
            if ( $preferred !== false )
                return $preferred;
        }

        if ( $view )
        {
            if ( $http->hasSessionVariable( "LastAccessesURI", false ) )
            {
                $uri = $http->sessionVariable( "LastAccessesURI" );
            }
        }
        else
        {
            if ( $http->hasSessionVariable( "LastAccessedModifyingURI", false ) )
            {
                $uri = $http->sessionVariable( "LastAccessedModifyingURI" );
            }
        }

        if ( $uri !== false )
            $uri = self::safeURI( $uri );

        if ( $uri !== false && is_object( $module ) )
        {
            $moduleURI = $module->functionURI( $module->currentView() );
            // Check for correct module/view
            if ( substr( $uri, 0, strlen( $moduleURI ) ) == $moduleURI )
            {
                // Check parameters
                $moduleURI = $module->currentRedirectionURI();
                if ( $moduleURI == $uri )
                    $uri = false;
            }
        }

        // Check for disallowed urls
        if ( $uri !== false and
             is_array( $disallowed ) )
        {
            if ( in_array( $uri, $disallowed ) )
                $uri = false;
        }

        if ( $uri === false )
        {
            // If no default is set we should return false.
            if ( $default === false )
                return false;
            $uri = $default;
        }

        return $uri;
    }

    /*!
     Generates a URI which can be used to redirect with, the uri is based on:
     - The last accessed view/non-view page if any (see \a $view parameter)
     - The uri is not the currently running module, if so use default
     - The default uri \a $default

     \param $module The current module object
     \param $default The default URI to redirect to if all else fails.
                     If set to \c false then it will not redirect if there is no url found
                     but instead it will return false.
     \param $view If true it will try to redirect to last accessed view URI.
     \param $disallowed An array with urls not allowed to redirect to or \c false to allow all
     \param $preferredURI An URI that is preferred for the caller.
            We redirect to that URI if it's specified and is valid.

     \return \c true if the module was redirected or \c false if not.

     \note All URLs must start with a slash \c /
     \sa redirectURI()
    */
    static function redirectTo( $module, $default, $view = true, $disallowed = false, $preferredURI = false )
    {
        $uri = eZRedirectManager::redirectURI( $module, $default, $view, $disallowed, $preferredURI );
        if ( $uri === false )
            return false;
        $module->redirectTo( $uri );
        return true;
    }

    /**
     * Where "back to where you came from" goes, for a Cancel or Discard button: the first safe page of
     * $preferredURI (the page the form names, usually RedirectIfDiscarded), else the page this session viewed last
     * (LastAccessesURI), else $default.
     *
     * A page is taken only when it passes safeURI(), is not the running view itself (Cancel would land on the form
     * again) and is not one of the views of disallowedReturnViews(). The page viewed last must also still be one
     * the current user may view (isViewable()): it was, when it was recorded, but a role may have changed since.
     *
     * @param eZModule|null $module the running module; null when there is none (a test)
     * @param string|false $default the caller's own page; false returns false when nothing else is found
     * @param string|array|false $preferredURI one page or a list, the first that passes is taken
     * @param array $options 'session' => bool, whether to read LastAccessesURI (default true);
     *                       'viewable' => callable( string $uri ) returning bool, instead of isViewable();
     *                       'disallowed_views' => list of "module/view", instead of disallowedReturnViews();
     *                       'allowed_hosts', 'current_host', 'prefix' as for safeURI() and moduleView()
     * @return string|false
     */
    static function returnURI( $module, $default, $preferredURI = false, $options = array() )
    {
        $options = array_merge( array( 'session' => true,
                                       'viewable' => null,
                                       'disallowed_views' => null,
                                       'allowed_hosts' => null,
                                       'current_host' => null,
                                       'prefix' => null ),
                                $options );

        $candidates = is_array( $preferredURI ) ? array_values( $preferredURI ) : array( $preferredURI );
        foreach ( $candidates as $candidate )
        {
            $uri = self::returnCandidate( $module, $candidate, $options );
            if ( $uri !== false )
                return $uri;
        }

        if ( $options['session'] )
        {
            $http = eZHTTPTool::instance();
            if ( $http->hasSessionVariable( 'LastAccessesURI', false ) )
            {
                $uri = self::returnCandidate( $module, $http->sessionVariable( 'LastAccessesURI' ), $options );
                if ( $uri !== false )
                {
                    $viewable = is_callable( $options['viewable'] ) ? $options['viewable'] : array( __CLASS__, 'isViewable' );
                    if ( call_user_func( $viewable, $uri ) )
                        return $uri;
                }
            }
        }

        return $default;
    }

    /**
     * The pages a form names for "back to where you came from", for returnURI(): the string values of the POST
     * variables $names, in that order. RedirectIfDiscarded is the name content/edit, user/register and every
     * edit form with a Cancel button use.
     *
     * @param array $names
     * @return array
     */
    static function formReturnURIs( $names = array( 'RedirectIfDiscarded' ) )
    {
        $http = eZHTTPTool::instance();
        $uris = array();
        foreach ( (array)$names as $name )
        {
            $value = $http->hasPostVariable( $name ) ? $http->postVariable( $name ) : null;
            if ( is_string( $value ) && trim( $value ) !== '' )
                $uris[] = trim( $value );
        }
        return $uris;
    }

    /**
     * The page an edit form carries in RedirectIfDiscarded, for its template (the variable redirect_if_discarded):
     * the page the form named when it was posted, else the page viewed before the form; '' when there is none.
     * Frozen into the form, Cancel goes back to it even when other pages were viewed in another tab meanwhile.
     *
     * @param eZModule|null $module
     * @param array $names see formReturnURIs()
     * @return string
     */
    static function formReturnURI( $module, $names = array( 'RedirectIfDiscarded' ) )
    {
        return (string)self::returnURI( $module, '', self::formReturnURIs( $names ) );
    }

    /**
     * One page for returnURI(): the safe form of $uri, or false when it is not safe, is the running view or a
     * view a return never goes to.
     *
     * @param eZModule|null $module
     * @param mixed $uri
     * @param array $options see returnURI()
     * @return string|false
     */
    protected static function returnCandidate( $module, $uri, $options )
    {
        $uri = self::safeURI( $uri, $options['allowed_hosts'], $options['current_host'] );
        if ( $uri === false )
            return false;

        $target = self::moduleView( $uri, $options['prefix'] );
        if ( $target === false )
            return $uri;

        $views = is_array( $options['disallowed_views'] ) ? $options['disallowed_views'] : self::disallowedReturnViews();
        if ( in_array( $target, array_map( 'strtolower', $views ), true ) )
            return false;

        if ( is_object( $module ) )
        {
            $current = strtolower( $module->currentModule() . '/' . $module->currentView() );
            if ( $target === $current )
                return false;
        }

        return $uri;
    }

    /**
     * The "module/view" (lower case) a URI of this site points at, or false when it is not a module path that can
     * be told without the database (a page alias such as /Company/About, an absolute URL). The siteaccess prefix
     * of the current request (eZSys::indexDir(), "/admin" with URI matching) is skipped.
     *
     * @param string $uri
     * @param string|null $prefix the siteaccess prefix; null reads eZSys::indexDir()
     * @return string|false
     */
    static function moduleView( $uri, $prefix = null )
    {
        if ( !is_string( $uri ) || $uri === '' || preg_match( '#^(?:[a-z][a-z0-9+.\-]*:|//)#i', $uri ) )
            return false;

        $path = '/' . ltrim( preg_replace( '/[?#].*$/s', '', $uri ), '/' );
        if ( $prefix === null )
            $prefix = class_exists( 'eZSys', false ) ? (string)eZSys::indexDir() : '';
        $prefix = rtrim( (string)$prefix, '/' );
        if ( $prefix !== '' && ( $path === $prefix || strpos( $path, $prefix . '/' ) === 0 ) )
            $path = '/' . ltrim( substr( $path, strlen( $prefix ) ), '/' );

        $parts = explode( '/', trim( $path, '/' ) );
        if ( count( $parts ) < 2 || $parts[0] === '' || $parts[1] === '' )
            return false;
        if ( !preg_match( '/^[a-z0-9_]+$/i', $parts[0] ) || !preg_match( '/^[a-z0-9_]+$/i', $parts[1] ) )
            return false;

        return strtolower( $parts[0] . '/' . $parts[1] );
    }

    /**
     * The views of site.ini [SiteSettings] DisallowedReturnViews, else DEFAULT_DISALLOWED_RETURN_VIEWS.
     *
     * @return array list of "module/view"
     */
    static function disallowedReturnViews()
    {
        if ( class_exists( 'eZINI', false ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'SiteSettings', 'DisallowedReturnViews' ) )
            {
                $views = $ini->variable( 'SiteSettings', 'DisallowedReturnViews' );
                if ( is_array( $views ) )
                    return array_values( array_filter( $views, 'strlen' ) );
            }
        }
        return self::DEFAULT_DISALLOWED_RETURN_VIEWS;
    }

    /**
     * Whether the current user may view the page $uri: a page alias is translated first; content/view checks
     * that the node can be read, a module view the siteaccess rules and the user's policies. A page that cannot
     * be told (an absolute URL of an allowed host, a lookup that fails) counts as viewable: the page itself still
     * checks access when it is opened.
     *
     * @param string $uri
     * @return bool
     */
    static function isViewable( $uri )
    {
        try
        {
            if ( !is_string( $uri ) || preg_match( '#^(?:[a-z][a-z0-9+.\-]*:|//)#i', $uri ) )
                return true;

            $path = trim( preg_replace( '/[?#].*$/s', '', $uri ), '/' );
            $prefix = trim( (string)eZSys::indexDir(), '/' );
            if ( $prefix !== '' && ( $path === $prefix || strpos( $path, $prefix . '/' ) === 0 ) )
                $path = trim( substr( $path, strlen( $prefix ) ), '/' );
            if ( $path === '' )
                return true;

            $parts = explode( '/', $path );
            if ( !eZModule::exists( $parts[0] ) && eZINI::instance()->variable( 'URLTranslator', 'Translation' ) === 'enabled' )
            {
                $translated = $path;
                if ( eZURLAliasML::translate( $translated ) && is_string( $translated ) )
                    $parts = explode( '/', trim( $translated, '/' ) );
            }

            if ( count( $parts ) >= 4 && $parts[0] === 'content' && $parts[1] === 'view' && is_numeric( $parts[3] ) )
            {
                $node = eZContentObjectTreeNode::fetch( (int)$parts[3] );
                return $node instanceof eZContentObjectTreeNode && $node->canRead();
            }

            if ( count( $parts ) < 2 )
                return true;
            $module = eZModule::exists( $parts[0] );
            if ( !$module instanceof eZModule )
                return true;
            $check = eZModule::accessAllowed( new eZURI( implode( '/', $parts ) ) );
            if ( !$check['result'] )
                return false;
            $params = array();
            return (bool)eZUser::currentUser()->hasAccessToView( $module, $parts[1], $params );
        }
        catch ( Exception $e )
        {
            eZDebug::writeWarning( 'Could not tell whether ' . $uri . ' is viewable: ' . $e->getMessage(), __METHOD__ );
            return true;
        }
    }

    /**
     * $uri as a safe redirect target, or false. See unsafeReason() for the rules; a safe URI is returned with the
     * spaces around it removed.
     *
     * @param mixed $uri
     * @param array|null $allowedHosts hosts an absolute URL may name; null reads eZModule::allowedRedirectHosts()
     * @param string|null $currentHost the host of the request; null reads eZSys::hostname()
     * @return string|false
     */
    static function safeURI( $uri, $allowedHosts = null, $currentHost = null )
    {
        if ( self::unsafeReason( $uri, $allowedHosts, $currentHost ) !== false )
            return false;
        return trim( $uri, ' ' );
    }

    /**
     * Why $uri is not a safe redirect target, or false when it is. The reasons:
     *
     *  - 'type'      not a string
     *  - 'empty'     nothing but spaces
     *  - 'control'   a control character anywhere, CR and LF among them (a split header; browsers also drop tab, CR
     *                and LF inside a URL, so "/<TAB>/evil.example" is "//evil.example")
     *  - 'backslash' a backslash before the query: browsers read "\" as "/", so "/\evil.example" is another host
     *  - 'encoded'   the path, percent-decoded (up to three times), starts with "//", a scheme, a backslash or a
     *                control character: "%2F%2Fevil.example" and the like
     *  - 'scheme'    a scheme other than http and https (javascript:, data:, vbscript:, ...) or "http:" without "//"
     *  - 'userinfo'  an absolute URL with a user or password ("https://own.example@evil.example" names evil.example)
     *  - 'host'      an absolute or protocol-relative URL whose host is neither the current one nor one of
     *                site.ini [SiteSettings] AllowedRedirectHosts and the hosts of the siteaccess host matching
     *
     * A path of this site, with or without the leading slash and with or without the siteaccess prefix, is safe.
     *
     * @param mixed $uri
     * @param array|null $allowedHosts see safeURI()
     * @param string|null $currentHost see safeURI()
     * @return string|false
     */
    static function unsafeReason( $uri, $allowedHosts = null, $currentHost = null )
    {
        if ( !is_string( $uri ) )
            return 'type';
        if ( preg_match( '/[\x00-\x1F\x7F]/', $uri ) )
            return 'control';
        $uri = trim( $uri, ' ' );
        if ( $uri === '' )
            return 'empty';

        // where a browser goes is decided before the query and the fragment
        $target = preg_replace( '/[?#].*$/s', '', $uri );
        if ( strpos( $target, '\\' ) !== false )
            return 'backslash';

        $decoded = $target;
        for ( $i = 0; $i < 3; ++$i )
        {
            $next = rawurldecode( $decoded );
            if ( $next === $decoded )
                break;
            $decoded = $next;
            if ( preg_match( '/[\x00-\x1F\x7F\\\\]/', $decoded ) )
                return 'encoded';
            $start = ltrim( $decoded, ' ' );
            if ( strncmp( $start, '//', 2 ) === 0 || preg_match( '#^[a-z][a-z0-9+.\-]*:#i', $start ) )
            {
                if ( strncmp( $target, '//', 2 ) !== 0 && !preg_match( '#^[a-z][a-z0-9+.\-]*:#i', $target ) )
                    return 'encoded';
            }
        }

        if ( preg_match( '#^([a-z][a-z0-9+.\-]*):#i', $target, $matches ) )
        {
            $scheme = strtolower( $matches[1] );
            if ( ( $scheme !== 'http' && $scheme !== 'https' ) || substr( $target, strlen( $matches[0] ), 2 ) !== '//' )
                return 'scheme';
            return self::absoluteReason( $uri, $allowedHosts, $currentHost );
        }

        if ( strncmp( $target, '//', 2 ) === 0 )
            return self::absoluteReason( 'http:' . $uri, $allowedHosts, $currentHost );

        return false;
    }

    /**
     * unsafeReason() of an absolute URL: false, 'userinfo' or 'host'.
     *
     * @param string $url
     * @param array|null $allowedHosts
     * @param string|null $currentHost
     * @return string|false
     */
    protected static function absoluteReason( $url, $allowedHosts, $currentHost )
    {
        $parts = parse_url( $url );
        if ( !is_array( $parts ) || !isset( $parts['host'] ) || $parts['host'] === '' )
            return 'host';
        if ( isset( $parts['user'] ) || isset( $parts['pass'] ) )
            return 'userinfo';
        return self::isOwnHost( $parts['host'], $allowedHosts, $currentHost ) ? false : 'host';
    }

    /**
     * Whether $host is the current host or one a redirect may go to (see unsafeReason(), 'host').
     *
     * @param string $host
     * @param array|null $allowedHosts
     * @param string|null $currentHost
     * @return bool
     */
    static function isOwnHost( $host, $allowedHosts = null, $currentHost = null )
    {
        $host = self::normaliseHost( $host );
        if ( $host === '' )
            return false;

        if ( $currentHost === null )
            $currentHost = (string)eZSys::hostname();
        if ( $allowedHosts === null )
            $allowedHosts = array_keys( eZModule::allowedRedirectHosts() );

        foreach ( array_merge( array( $currentHost ), (array)$allowedHosts ) as $allowed )
        {
            if ( self::normaliseHost( (string)$allowed ) === $host )
                return true;
        }
        return false;
    }

    /**
     * Lower case host without port and trailing dot.
     *
     * @param string $host
     * @return string
     */
    protected static function normaliseHost( $host )
    {
        $host = strtolower( trim( (string)$host ) );
        if ( $host === '' )
            return '';
        if ( strpos( $host, '//' ) !== false )
            $host = (string)parse_url( $host, PHP_URL_HOST );
        if ( $host !== '' && $host[0] === '[' )
        {
            $end = strpos( $host, ']' );
            return $end === false ? $host : substr( $host, 0, $end + 1 );
        }
        return rtrim( (string)preg_replace( '/:\d*$/', '', $host ), '.' );
    }
}

?>
