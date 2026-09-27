<?php
/**
 * File containing the expMaintenance class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Maintenance mode: while var/maintenance.json exists, every request to the
 * front controllers is answered with a maintenance page (503, Retry-After,
 * never cached) instead of the site.
 *
 * It exists for two cases. An installation (the kickstarter) rebuilds the
 * database while the web server keeps answering: requests then met half-built
 * tables ("no such table", "no row was chosen for action eznode:2"), logged
 * errors that were not the installation's, and could leave broken pages in the
 * caches. And a site taken offline on purpose for a maintenance window.
 *
 * check() runs before autoload.php, the settings and the database, so this
 * class uses plain PHP only. The page is a self-contained HTML file with
 * {placeholders}: the marker names it (the theme's errors/maintenance.html when
 * an active extension has one, found by enable()), else share/maintenance.html.
 *
 * Switch it with bin/php/maintenance.php on|off|status (./console
 * exp:maintenance), or from code with enable() / disable().
 */
class expMaintenance
{
    const MARKER = 'var/maintenance.json';
    const DEFAULT_PAGE = 'share/maintenance.html';
    /** The cookie that lets the web setup wizard's own browser through */
    const WIZARD_COOKIE = 'exp_setup_wizard';
    /** Seconds a web wizard holds maintenance after its last request */
    const WIZARD_LEASE = 1800;

    /**
     * Answers the request with the maintenance page when maintenance is on and
     * the request is not let through. Called first thing by the front
     * controllers.
     *
     * @param string $root the document root (the front controller's directory)
     * @return bool true when the maintenance page was sent: the caller stops
     */
    static function check( $root )
    {
        $state = self::state( $root );
        if ( $state === false || self::letThrough( $state ) )
            return false;
        self::send( $root, $state );
        return true;
    }

    /**
     * @param string $root
     * @return array|false the marker's content, false when maintenance is off
     */
    static function state( $root )
    {
        $file = rtrim( $root, '/' ) . '/' . self::MARKER;
        if ( !is_file( $file ) )
            return false;
        $state = json_decode( (string)@file_get_contents( $file ), true );
        // A marker that cannot be read still means maintenance: better a
        // maintenance page than a site that is half way through a change
        if ( !is_array( $state ) )
            return array( 'reason' => 'unknown' );
        // A web wizard's maintenance ends on its own when the wizard was left:
        // nobody else could reach the site, or start the wizard again
        if ( !empty( $state['lease'] ) && (int)$state['lease'] < time() )
            return false;
        return $state;
    }

    /**
     * The web setup wizard's first request: maintenance on for everyone but
     * this browser, which gets a cookie the marker knows (as a hash) only.
     * Every other visitor gets the maintenance page instead of a second
     * wizard that would abandon this one or install over it.
     *
     * @param string $root
     * @param string $runId the setup log's run
     * @return bool
     */
    static function beginWizard( $root, $runId )
    {
        $token = bin2hex( random_bytes( 16 ) );
        if ( !self::enable( $root, array( 'reason' => 'setup', 'run' => (string)$runId,
                                          'allow_token' => hash( 'sha256', $token ),
                                          'lease' => time() + self::WIZARD_LEASE ) ) )
            return false;
        self::wizardCookie( $token, time() + self::WIZARD_LEASE );
        return true;
    }

    /** Each further wizard request: the wizard is still there, hold maintenance a while longer. */
    static function renewWizard( $root, $runId )
    {
        $state = self::state( $root );
        if ( $state === false || empty( $state['lease'] ) || ( $state['run'] ?? null ) !== (string)$runId )
            return false;
        $state['lease'] = time() + self::WIZARD_LEASE;
        $file = rtrim( $root, '/' ) . '/' . self::MARKER;
        $tmp = $file . '.tmp' . getmypid();
        if ( @file_put_contents( $tmp, json_encode( $state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" ) === false )
            return false;
        @chmod( $tmp, 0666 );
        return @rename( $tmp, $file );
    }

    /** The wizard's last page: the site is installed, maintenance off. */
    static function endWizard( $root, $runId )
    {
        self::wizardCookie( '', time() - 3600 );
        return self::disable( $root, (string)$runId );
    }

    protected static function wizardCookie( $value, $expires )
    {
        // Not PHP_SAPI: Velocity's workers answer web requests from the CLI SAPI
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || headers_sent() )
            return;
        $secure = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off' );
        setcookie( self::WIZARD_COOKIE, $value, array( 'expires' => $expires, 'path' => '/',
                   'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax' ) );
    }

    /**
     * Switches maintenance on (or updates it).
     *
     * @param string $root
     * @param array $options reason (setup|manual), message, until (unix time),
     *        allow_ips (list), allow_paths (list of path prefixes, e.g. /admin),
     *        page (a self-contained HTML file, relative to $root), run (setup run id)
     * @return bool
     */
    static function enable( $root, array $options = array() )
    {
        $state = $options + array(
            'reason' => 'manual',
            'message' => '',
            'until' => 0,
            'allow_ips' => array(),
            'allow_paths' => array(),
            'page' => self::themePage( $root ),
            'since' => time(),
        );
        $file = rtrim( $root, '/' ) . '/' . self::MARKER;
        $tmp = $file . '.tmp' . getmypid();
        if ( @file_put_contents( $tmp, json_encode( $state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" ) === false )
            return false;
        @chmod( $tmp, 0666 );
        // Renamed into place, so a request never reads half a marker
        if ( !@rename( $tmp, $file ) )
            return false;
        self::clearResponseCaches( $root );
        return true;
    }

    /**
     * Switches maintenance off. With $onlyRun, only when the marker was set by
     * that setup run: a run does not end a maintenance window someone opened.
     *
     * @param string $root
     * @param string|null $onlyRun
     * @return bool true when it was switched off
     */
    static function disable( $root, $onlyRun = null )
    {
        $state = self::state( $root );
        if ( $state === false )
            return false;
        if ( $onlyRun !== null && ( !isset( $state['run'] ) || $state['run'] !== $onlyRun ) )
            return false;
        return @unlink( rtrim( $root, '/' ) . '/' . self::MARKER );
    }

    /**
     * Whether this request passes: an allowed client address, or a path below
     * an allowed prefix (a window opened with --allow-admin keeps /admin).
     */
    static function letThrough( array $state )
    {
        // The web wizard's own browser
        if ( !empty( $state['allow_token'] ) && isset( $_COOKIE[self::WIZARD_COOKIE] ) && is_string( $_COOKIE[self::WIZARD_COOKIE] )
             && hash_equals( (string)$state['allow_token'], hash( 'sha256', $_COOKIE[self::WIZARD_COOKIE] ) ) )
            return true;
        $ip =isset( $_SERVER['REMOTE_ADDR'] ) ? (string)$_SERVER['REMOTE_ADDR'] : '';
        if ( $ip !== '' && !empty( $state['allow_ips'] ) && in_array( $ip, (array)$state['allow_ips'], true ) )
            return true;
        $path = isset( $_SERVER['REQUEST_URI'] ) ? (string)parse_url( (string)$_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
        foreach ( (array)( $state['allow_paths'] ?? array() ) as $prefix )
        {
            $prefix = '/' . trim( (string)$prefix, '/' );
            if ( $prefix !== '/' && ( $path === $prefix || strpos( $path, $prefix . '/' ) === 0 ) )
                return true;
        }
        return false;
    }

    /** Sends the maintenance page: 503, Retry-After, not to be kept by any cache. */
    static function send( $root, array $state )
    {
        $until = isset( $state['until'] ) ? (int)$state['until'] : 0;
        $retry = $until > time() ? $until - time() : 120;
        if ( !headers_sent() )
        {
            header( 'HTTP/1.1 503 Service Unavailable', true, 503 );
            header( 'Retry-After: ' . $retry );
            header( 'Cache-Control: no-store, no-cache, must-revalidate, private' );
            header( 'Pragma: no-cache' );
            header( 'Expires: 0' );
            header( 'Content-Type: text/html; charset=utf-8' );
            header( 'X-Robots-Tag: noindex' );
        }
        echo self::page( $root, $state );
    }

    /** The maintenance page's HTML, placeholders filled. */
    static function page( $root, array $state )
    {
        $root = rtrim( $root, '/' );
        $html = '';
        foreach ( array( $state['page'] ?? '', self::DEFAULT_PAGE ) as $candidate )
        {
            // Only a file inside the installation
            if ( is_string( $candidate ) && $candidate !== '' && strpos( $candidate, '..' ) === false
                 && is_file( $root . '/' . ltrim( $candidate, '/' ) ) )
            {
                $html = (string)file_get_contents( $root . '/' . ltrim( $candidate, '/' ) );
                break;
            }
        }
        if ( $html === '' )
            $html = '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>{title}</title></head>'
                  . '<body><h1>{title}</h1><p>{message}</p><p>{until}</p></body></html>';

        $setup = ( $state['reason'] ?? '' ) === 'setup';
        $title = $setup ? 'The site is being set up' : 'Down for maintenance';
        $message = trim( (string)( $state['message'] ?? '' ) );
        if ( $message === '' )
            $message = $setup
                ? 'It is being installed right now and will be here in a few minutes.'
                : 'We are working on the site and will be back shortly.';
        $until = isset( $state['until'] ) ? (int)$state['until'] : 0;
        $untilText = $until > time() ? 'Expected back at ' . gmdate( 'H:i', $until ) . ' UTC.' : 'Please try again in a few minutes.';
        $e = function ( $s ) { return htmlspecialchars( (string)$s, ENT_QUOTES, 'UTF-8' ); };
        return strtr( $html, array(
            '{status}' => '503',
            '{title}' => $e( $title ),
            '{message}' => $e( $message ),
            '{until}' => $e( $untilText ),
            '{home}' => '/',
            '{reference}' => '',
            '{detail}' => '',
        ) );
    }

    /**
     * The first active extension's errors/maintenance.html, when the settings
     * can be read (they cannot during check(), which is why enable() records it).
     *
     * @return string a path relative to $root, '' for the default page
     */
    static function themePage( $root )
    {
        $root = rtrim( $root, '/' );
        $extensions = array();
        if ( class_exists( 'eZINI', false ) )
        {
            $ini = eZINI::instance();
            foreach ( array( 'ActiveExtensions', 'ActiveAccessExtensions' ) as $list )
                if ( $ini->hasVariable( 'ExtensionSettings', $list ) )
                    $extensions = array_merge( $extensions, (array)$ini->variable( 'ExtensionSettings', $list ) );
        }
        foreach ( array_unique( $extensions ) as $extension )
        {
            $page = 'extension/' . $extension . '/errors/maintenance.html';
            if ( is_file( $root . '/' . $page ) )
                return $page;
        }
        return '';
    }

    /**
     * Empties the caches that answer ahead of the front controller, so no page
     * stored before maintenance is served during it: the HTTP cache's files
     * are moved aside (not deleted), Velocity is told to clear its own.
     */
    static function clearResponseCaches( $root )
    {
        $root = rtrim( $root, '/' );
        foreach ( glob( $root . '/var/*/cache/exphttpcache', GLOB_ONLYDIR ) ?: array() as $dir )
            @rename( $dir, $root . '/var/tmp/' . basename( dirname( dirname( $dir ) ) ) . '-exphttpcache-before-maintenance-' . date( 'YmdHis' ) . '-' . getmypid() );
        if ( PHP_SAPI === 'cli' && is_file( $root . '/console' ) && function_exists( 'exec' ) )
            @exec( 'cd ' . escapeshellarg( $root ) . ' && php console exp:velocity cache clear --allow-root-user >/dev/null 2>&1' );
    }
}
?>
