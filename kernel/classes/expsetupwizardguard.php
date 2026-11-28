<?php
/**
 * File containing the expSetupWizardGuard class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Decides what a request to the web setup wizard (setup/init) may do, before
 * the wizard shows a page or touches maintenance mode.
 *
 * - On an installed site the wizard first shows a page that says so and
 *   explains reinstalling (the wizard, the kickstarter or a fresh
 *   installation). Nothing is written: no maintenance marker, no setup log
 *   run. [SetupSettings] AllowWizardOnInstalledSite decides whether that page
 *   offers "Run the setup wizard anyway": auto (the default) when the admin set
 *   [SiteAccessSettings] CheckValidity=true (the request came through the
 *   validity check), enabled always, disabled never. The button leads to a
 *   confirm page whose form is posted back with a one-time token; only that
 *   POST starts the run.
 * - Maintenance mode (expMaintenance::beginWizard()) is never started by a GET
 *   or by a page being shown. It starts with the first posted wizard step on a
 *   fresh installation, or with the confirmed POST on an installed one.
 *
 * decide() is pure, so the rules are tested without a database.
 */
class expSetupWizardGuard
{
    /** Written by the wizard's last page; its presence alone means "installed" */
    const INSTALLED_MARKER = 'var/site_installed.json';

    /** Show the wizard's page; no maintenance */
    const SHOW = 'show';
    /** Installed site, wizard not allowed: show the explanation page */
    const REFUSE = 'refuse';
    /** Installed site, wizard allowed: the explanation page with "Run the setup wizard anyway" */
    const OFFER = 'offer';
    /** Installed site, wizard allowed: ask for a confirmation first */
    const CONFIRM = 'confirm';
    /** Start a run and maintenance mode (wizard lease) */
    const BEGIN = 'begin';
    /** A further step of a run that holds the lease */
    const CONTINUE_RUN = 'continue';

    /** The posted field of the "already installed" page's "Run the setup wizard anyway" button */
    const RUN_ANYWAY_BUTTON = 'ExpSetupRunAnywayButton';
    /** The posted fields of the confirm page */
    const CONFIRM_BUTTON = 'ExpSetupConfirmReinstallButton';
    const CONFIRM_TOKEN = 'ExpSetupConfirmToken';
    /**
     * The cookie that pairs with the confirm page's token. The wizard runs without a
     * session (CheckValidity routes it before the session and the database start), so
     * the token is carried by the browser: the form holds "<expiry>.<nonce>", the cookie
     * the nonce. A page of another site can neither read nor set this site's cookie.
     */
    const CONFIRM_COOKIE = 'exp_setup_confirm';
    /** How long the confirm page's token is good for, in seconds */
    const CONFIRM_LIFETIME = 900;

    /** [SetupSettings] AllowWizardOnInstalledSite values */
    const MODE_AUTO = 'auto';
    const MODE_ENABLED = 'enabled';
    const MODE_DISABLED = 'disabled';

    /**
     * @param bool $installed the site is installed (see isInstalled())
     * @param bool $allowedOnInstalled the wizard may run on an installed site (see allowedOnInstalledSite())
     * @param bool $isStepPost the request posts a wizard step (eZSetup_current_step)
     * @param bool $confirmed the request is the confirm page's POST with a valid token
     * @param bool $leaseRenewed this run's maintenance lease was renewed
     * @param bool $runAnyway the request posts the "Run the setup wizard anyway" button
     * @return string one of the constants SHOW, REFUSE, OFFER, CONFIRM, BEGIN, CONTINUE_RUN
     */
    static function decide( $installed, $allowedOnInstalled, $isStepPost, $confirmed, $leaseRenewed, $runAnyway = false )
    {
        if ( $installed )
        {
            if ( !$allowedOnInstalled )
                return self::REFUSE;
            if ( $confirmed )
                return self::BEGIN;
            // A step of a confirmed run whose lease still holds
            if ( $isStepPost && $leaseRenewed )
                return self::CONTINUE_RUN;
            // The button of the "already installed" page, or a step after the
            // lease ran out: the confirm page. A first visit: the explanation
            // page with the button. Neither writes anything.
            return ( $runAnyway || $isStepPost ) ? self::CONFIRM : self::OFFER;
        }
        if ( !$isStepPost )
            return self::SHOW;
        return $leaseRenewed ? self::CONTINUE_RUN : self::BEGIN;
    }

    /**
     * Is the site installed? True when the marker exists (in var/, or in the
     * shared state directory of a site served by several nodes, where the node
     * that served the wizard's last page wrote it), when the request did
     * not come through the validity check (the site is served normally, so it
     * was set up), or when the configured database has content.
     *
     * @param string $root the installation root
     * @param bool $validityCheck the request was routed to the wizard by CheckValidity
     * @param callable|null $databaseHasContent returns bool; null probes the configured database
     * @return bool
     */
    static function isInstalled( $root, $validityCheck, $databaseHasContent = null )
    {
        foreach ( self::installedMarkers( $root ) as $marker )
            if ( is_file( $marker ) )
                return true;
        if ( !$validityCheck )
            return true;
        $probe = $databaseHasContent ?: array( __CLASS__, 'databaseHasContent' );
        try
        {
            return (bool)call_user_func( $probe );
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Does the database in the settings hold content objects? Run during the
     * validity check, when the kernel has no connection: a connection of its
     * own, closed again, and the global instance left as it was.
     *
     * @return bool
     */
    static function databaseHasContent()
    {
        $hadGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $saved = $hadGlobal ? $GLOBALS['eZDBGlobalInstance'] : null;
        $db = null;
        $count = 0;
        try
        {
            eZDB::setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            $db = @eZDB::instance( false, false, true );
            if ( $db instanceof eZDBInterface && $db->isConnected() )
            {
                $rows = @$db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' );
                $count = is_array( $rows ) && isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
            }
        }
        catch ( Throwable $e )
        {
            $count = 0;
        }
        if ( $db instanceof eZDBInterface )
            @$db->close();
        eZDB::setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        if ( $hadGlobal )
            $GLOBALS['eZDBGlobalInstance'] = $saved;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
        return $count > 0;
    }

    /**
     * [SetupSettings] AllowWizardOnInstalledSite as one of MODE_AUTO,
     * MODE_ENABLED, MODE_DISABLED. Missing or not one of the three: auto.
     *
     * @param eZINI $ini site.ini
     * @return string
     */
    static function mode( $ini )
    {
        $value = $ini->hasVariable( 'SetupSettings', 'AllowWizardOnInstalledSite' )
            ? strtolower( trim( (string)$ini->variable( 'SetupSettings', 'AllowWizardOnInstalledSite' ) ) )
            : '';
        if ( $value === self::MODE_ENABLED || $value === self::MODE_DISABLED )
            return $value;
        return self::MODE_AUTO;
    }

    /**
     * May the wizard run on an installed site (after the confirm page)?
     * enabled: yes; disabled: no; auto: when the request came through the
     * validity check, that is, the admin set [SiteAccessSettings]
     * CheckValidity=true to reach the wizard.
     *
     * @param eZINI|string $ini site.ini, or a mode (MODE_*)
     * @param bool $validityCheck the request was routed to the wizard by CheckValidity
     * @return bool
     */
    static function allowedOnInstalledSite( $ini, $validityCheck = false )
    {
        $mode = is_string( $ini ) ? $ini : self::mode( $ini );
        if ( $mode === self::MODE_ENABLED )
            return true;
        if ( $mode === self::MODE_DISABLED )
            return false;
        return (bool)$validityCheck;
    }

    /**
     * Did the "already installed" page's "Run the setup wizard anyway" button
     * post this request? It only leads to the confirm page.
     *
     * @param eZHTTPTool $http
     * @return bool
     */
    static function runAnywayPosted( $http )
    {
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) !== 'POST' )
            return false;
        return $http->hasPostVariable( self::RUN_ANYWAY_BUTTON );
    }

    /**
     * The address every wizard form posts to. Through the validity check
     * (CheckValidity=true) every address is the wizard, so the forms post
     * back to the site root: a /setup/... address is an admin module that a
     * frontend firewall (request-shield's EXP-MODULES) rightly refuses.
     * Otherwise the wizard is the setup/init view and the forms name it.
     *
     * @param bool $validityCheck the request was routed to the wizard by CheckValidity
     * @param string $wwwDir eZSys::wwwDir()
     * @param string $indexFileName eZSys::indexFileName()
     * @param string $indexFile eZSys::indexFile()
     * @param string $uriPrefix '/ezsetup' when served by that entry point, else ''
     * @return string
     */
    static function formAction( $validityCheck, $wwwDir, $indexFileName, $indexFile, $uriPrefix = '' )
    {
        if ( !$validityCheck )
            return $indexFile . $uriPrefix . '/setup/init';
        // The old ezsetup entry point keeps its own address
        if ( $uriPrefix !== '' )
            return $wwwDir . $indexFileName . $uriPrefix;
        return rtrim( (string)$wwwDir, '/' ) . '/';
    }

    /**
     * A one-time token for the confirm page: "<expiry>.<nonce>" for the form, the
     * nonce in a cookie (see CONFIRM_COOKIE). No session, nothing written on the server.
     *
     * @param int|null $now
     * @return string
     */
    static function issueConfirmToken( $now = null )
    {
        $nonce = bin2hex( random_bytes( 16 ) );
        $expires = ( $now === null ? time() : (int)$now ) + self::CONFIRM_LIFETIME;
        $_COOKIE[self::CONFIRM_COOKIE] = $nonce;
        self::confirmCookie( $nonce, $expires );
        return $expires . '.' . $nonce;
    }

    /**
     * Is this the confirm page's POST, with a token that is not expired and
     * matches this browser's cookie? The cookie is used up either way.
     *
     * @param eZHTTPTool $http
     * @param int|null $now
     * @return bool
     */
    static function confirmedByPost( $http, $now = null )
    {
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) !== 'POST' )
            return false;
        if ( !$http->hasPostVariable( self::CONFIRM_BUTTON ) || !$http->hasPostVariable( self::CONFIRM_TOKEN ) )
            return false;
        $cookie = isset( $_COOKIE[self::CONFIRM_COOKIE] ) && is_string( $_COOKIE[self::CONFIRM_COOKIE] ) ? $_COOKIE[self::CONFIRM_COOKIE] : '';
        unset( $_COOKIE[self::CONFIRM_COOKIE] );
        self::confirmCookie( '', time() - 3600 );
        $posted = $http->postVariable( self::CONFIRM_TOKEN );
        if ( $cookie === '' || !is_string( $posted ) || !preg_match( '/^([0-9]{1,12})\.([0-9a-f]{32})$/', $posted, $m ) )
            return false;
        if ( (int)$m[1] < ( $now === null ? time() : (int)$now ) )
            return false;
        return hash_equals( $cookie, $m[2] );
    }

    /**
     * Every page of the wizard (the "already installed" page, the confirm page
     * with its token, each step) is one visitor's: no cache keeps it. Replaces
     * a Cache-Control the site's [HTTPHeaderSettings] set for the address.
     */
    static function sendNoStoreHeaders()
    {
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || headers_sent() )
            return;
        foreach ( self::noStoreHeaders() as $name => $value )
            header( $name . ': ' . $value, true );
    }

    /**
     * @return array name => value
     */
    static function noStoreHeaders()
    {
        return array( 'Cache-Control' => 'no-store, private',
                      'Pragma' => 'no-cache',
                      'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
                      'X-Exp-Cache' => 'BYPASS (setup wizard)' );
    }

    protected static function confirmCookie( $value, $expires )
    {
        // Not PHP_SAPI: Velocity's workers answer web requests from the CLI SAPI
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || headers_sent() )
            return;
        $secure = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off' );
        setcookie( self::CONFIRM_COOKIE, $value, array( 'expires' => $expires, 'path' => '/',
                   'secure' => $secure, 'httponly' => true, 'samesite' => 'Strict' ) );
    }

    /**
     * The installed markers of the installation at $root: var/site_installed.json,
     * and on a site served by several nodes site_installed.json in the shared state
     * directory (expSetupSharedState), which every node sees.
     *
     * @param string $root
     * @return string[]
     */
    static function installedMarkers( $root )
    {
        $markers = array( rtrim( $root, '/' ) . '/' . self::INSTALLED_MARKER );
        $dir = class_exists( 'expSetupSharedState' ) ? expSetupSharedState::dir( $root ) : null;
        if ( $dir !== null )
            $markers[] = $dir . '/site_installed.json';
        return $markers;
    }

    /**
     * The setup run that installed the site, as the shared installed marker
     * records it: the run whose files (settings, packages) a node that did not
     * serve the wizard's last steps still has to fetch.
     *
     * @param string $root
     * @return string|null null on a single node, or when no wizard installed the site
     */
    static function sharedInstalledRun( $root )
    {
        $dir = class_exists( 'expSetupSharedState' ) ? expSetupSharedState::dir( $root ) : null;
        if ( $dir === null )
            return null;
        $data = expSetupSharedState::readJson( $dir . '/site_installed.json' );
        return is_array( $data ) && isset( $data['run'] ) && (string)$data['run'] !== '' ? (string)$data['run'] : null;
    }

    /**
     * The wizard's last page: the site is installed from now on, for every node.
     *
     * @param string $root
     * @param string $runId the setup log's run
     * @return bool true when every marker was written
     */
    static function markInstalled( $root, $runId )
    {
        $data = json_encode( array( 'installed' => gmdate( 'c' ), 'by' => 'setup wizard', 'run' => (string)$runId,
                                    'node' => class_exists( 'expSetupSharedState' ) ? expSetupSharedState::nodeName() : php_uname( 'n' ) ),
                             JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
        $written = true;
        foreach ( self::installedMarkers( $root ) as $i => $file )
        {
            if ( $i === 0 )
                $written = @file_put_contents( $file, $data ) !== false && $written;
            else
                $written = expSetupSharedState::writeAtomic( $file, $data ) && $written;
        }
        return $written;
    }
}
