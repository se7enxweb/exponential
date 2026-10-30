<?php
/**
 * File containing the expSetupWizardGuard class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Decides what a request to the web setup wizard (setup/init) may do, before
 * the wizard shows a page or touches maintenance mode.
 *
 * - On an installed site the wizard is refused: the page explains that the
 *   site is installed and that a reinstall is done with the kickstarter or a
 *   fresh installation. Nothing is written: no maintenance marker, no setup
 *   log run. [SetupSettings] AllowWizardOnInstalledSite=enabled (development
 *   only, disabled by default) lets it run, and then only after a confirm page
 *   whose form is posted back with a one-time token.
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
    /** Installed site, wizard allowed: ask for a confirmation first */
    const CONFIRM = 'confirm';
    /** Start a run and maintenance mode (wizard lease) */
    const BEGIN = 'begin';
    /** A further step of a run that holds the lease */
    const CONTINUE_RUN = 'continue';

    /** The posted fields of the confirm page */
    const CONFIRM_BUTTON = 'ExpSetupConfirmReinstallButton';
    const CONFIRM_TOKEN = 'ExpSetupConfirmToken';
    /** Session variable that holds the confirm page's token */
    const CONFIRM_SESSION_KEY = 'expSetupWizardConfirmToken';

    /**
     * @param bool $installed the site is installed (see isInstalled())
     * @param bool $allowedOnInstalled [SetupSettings] AllowWizardOnInstalledSite is enabled
     * @param bool $isStepPost the request posts a wizard step (eZSetup_current_step)
     * @param bool $confirmed the request is the confirm page's POST with a valid token
     * @param bool $leaseRenewed this run's maintenance lease was renewed
     * @return string one of the constants SHOW, REFUSE, CONFIRM, BEGIN, CONTINUE_RUN
     */
    static function decide( $installed, $allowedOnInstalled, $isStepPost, $confirmed, $leaseRenewed )
    {
        if ( $installed )
        {
            if ( !$allowedOnInstalled )
                return self::REFUSE;
            if ( $confirmed )
                return self::BEGIN;
            // A step of a confirmed run whose lease still holds; anything else
            // (a GET, a step after the lease ran out) asks again
            return ( $isStepPost && $leaseRenewed ) ? self::CONTINUE_RUN : self::CONFIRM;
        }
        if ( !$isStepPost )
            return self::SHOW;
        return $leaseRenewed ? self::CONTINUE_RUN : self::BEGIN;
    }

    /**
     * Is the site installed? True when the marker exists, when the request did
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
        if ( is_file( rtrim( $root, '/' ) . '/' . self::INSTALLED_MARKER ) )
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
     * @param eZINI $ini site.ini
     * @return bool [SetupSettings] AllowWizardOnInstalledSite is enabled (development only)
     */
    static function allowedOnInstalledSite( $ini )
    {
        return $ini->hasVariable( 'SetupSettings', 'AllowWizardOnInstalledSite' )
            && $ini->variable( 'SetupSettings', 'AllowWizardOnInstalledSite' ) === 'enabled';
    }

    /**
     * A one-time token for the confirm page, kept in the session.
     *
     * @param eZHTTPTool $http
     * @return string
     */
    static function issueConfirmToken( $http )
    {
        $token = bin2hex( random_bytes( 16 ) );
        $http->setSessionVariable( self::CONFIRM_SESSION_KEY, $token );
        return $token;
    }

    /**
     * Is this the confirm page's POST, with the token the session holds? The
     * token is used up either way.
     *
     * @param eZHTTPTool $http
     * @return bool
     */
    static function confirmedByPost( $http )
    {
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) !== 'POST' )
            return false;
        if ( !$http->hasPostVariable( self::CONFIRM_BUTTON ) || !$http->hasPostVariable( self::CONFIRM_TOKEN ) )
            return false;
        if ( !$http->hasSessionVariable( self::CONFIRM_SESSION_KEY ) )
            return false;
        $expected = (string)$http->sessionVariable( self::CONFIRM_SESSION_KEY );
        $http->removeSessionVariable( self::CONFIRM_SESSION_KEY );
        $posted = $http->postVariable( self::CONFIRM_TOKEN );
        return $expected !== '' && is_string( $posted ) && hash_equals( $expected, $posted );
    }

    /**
     * The wizard's last page: the site is installed from now on.
     *
     * @param string $root
     * @param string $runId the setup log's run
     * @return bool
     */
    static function markInstalled( $root, $runId )
    {
        $file = rtrim( $root, '/' ) . '/' . self::INSTALLED_MARKER;
        $data = json_encode( array( 'installed' => gmdate( 'c' ), 'by' => 'setup wizard', 'run' => (string)$runId ),
                             JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
        return @file_put_contents( $file, $data ) !== false;
    }
}
