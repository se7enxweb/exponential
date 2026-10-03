<?php
/**
 * [AuditSettings] OnWriteFailure (doc/bc/6.0/audit.md, "When the audit cannot write"): what happens to an action
 * whose audit record cannot be written.
 *
 *   continue  (the shipped default) the action goes on; the record goes to error.log as AUDIT-UNWRITTEN
 *   refuse    the actions of the "written at once" kind ([AuditBufferSettings] ImmediateEvents[]: access.*,
 *             system.audit.*, system.setting.write) are refused while their channel cannot be written, so nothing
 *             security-relevant happens unrecorded
 *
 * The guarded actions, and only these:
 *
 *   - a POST to a view of a sensitive module ([AuditReadSettings] AlwaysModules[]: setup, role, user, audit,
 *     settings) by a signed-in user: ezpKernelWeb asks webAllows() before the view runs and answers 503 with
 *     the page audit/refused.tpl. GET requests are never refused, nor are the views in [AuditSettings]
 *     RefuseExemptViews[] (user/login, user/logout by default: people can still sign in and out, and the login
 *     records go to error.log), nor anything an anonymous visitor does;
 *   - an INI write through expIniEditor (exp:ini, the debug bar, the settings forms): system.setting.write;
 *   - the audit's own manage actions (exp:audit archive, restore, purge, rotate, reindex, pseudonymise, import,
 *     export, key rotate, checkpoint, and the console's "Verify now"): system.audit.*.
 *
 * "Cannot be written" is decided by a probe of the channel (the directory exists or can be created, the channel
 * lock opens, the newest file can be appended to, more than 1 MB is free) and by the request's own last
 * write of that channel (expAudit::writeFailed()). The probe writes nothing. Pages, content editing and every
 * other action are never touched, and with OnWriteFailure=continue nothing here does anything.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditGuard
{
    /** The error.log prefix of a refusal */
    const REFUSED = 'AUDIT-REFUSED';

    /** Free bytes below which a channel counts as unwritable (a record is at most MaxLineBytes) */
    const MIN_FREE_BYTES = 1048576;

    /** @var array|null the last refusal: name, channel, problem (for the error page and the command) */
    protected static $last = null;

    /**
     * Whether OnWriteFailure=refuse is in effect (and the audit is on).
     *
     * @param array|null $config expAuditConfig::get()
     * @return bool
     */
    public static function refusing( ?array $config = null )
    {
        try
        {
            $config = $config ?: expAuditConfig::get();
            return !empty( $config['enabled'] ) && isset( $config['onWriteFailure'] ) && $config['onWriteFailure'] === 'refuse';
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Whether an action that records $name may go on. Always true with OnWriteFailure=continue, with the audit
     * off, and for a name that is not recorded or not written at once (not in ImmediateEvents[]).
     *
     * @param string $name the event the action records, e.g. system.setting.write
     * @param array|null $config
     * @return bool
     */
    public static function allows( $name, ?array $config = null )
    {
        self::$last = null;
        try
        {
            $config = $config ?: expAuditConfig::get();
            if ( !self::refusing( $config ) )
                return true;
            $decision = expAuditTaxonomy::decide( $name, $config );
            if ( !$decision['valid'] || !$decision['on'] || !$decision['immediate'] )
                return true;
            $channel = $decision['channel'];
            $problem = expAudit::writeFailed( $channel ) ? "the last write of the channel $channel in this request failed"
                                                          : self::problem( $channel, $config );
        }
        catch ( Throwable $e )
        {
            // refusing was asked for: when even the check fails, the action does not go on unrecorded
            $channel = '?';
            $problem = 'the check failed: ' . $e->getMessage();
        }
        if ( $problem === null )
            return true;
        self::$last = array( 'name' => (string)$name, 'channel' => $channel, 'problem' => $problem );
        $line = self::REFUSED . ' ' . $name . ' (OnWriteFailure=refuse): the audit channel ' . $channel . ' cannot be written: ' . $problem;
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeError( $line, __CLASS__ );
        else
            error_log( $line );
        return false;
    }

    /** @return array|null name, channel, problem of the last refusal of allows() */
    public static function lastRefusal()
    {
        return self::$last;
    }

    /** @return string The last refusal as one sentence (for the command and the error page) */
    public static function message()
    {
        $l = self::$last;
        if ( !$l )
            return '';
        return "Refused: the audit cannot write the channel {$l['channel']} ({$l['problem']}), and [AuditSettings] " .
               "OnWriteFailure=refuse does not let {$l['name']} happen unrecorded.";
    }

    /**
     * Why a channel cannot be written now, or null. Writes nothing: it creates the log directory (as the writer
     * would) and opens the channel's lock file, which the writer opens on every append anyway.
     *
     * @param string $channel
     * @param array $config
     * @return string|null
     */
    public static function problem( $channel, array $config )
    {
        if ( !preg_match( '/^[a-z][a-z0-9_]{0,31}$/', (string)$channel ) )
            return "malformed channel '$channel'";
        $dir = rtrim( (string)$config['logDir'], '/' );
        if ( $dir === '' )
            return 'no LogDir';
        if ( !expAuditWriter::ensureDirectory( $dir ) )
            return 'the directory ' . expAudit::relativePath( $dir ) . ' does not exist and cannot be created';
        if ( !is_writable( $dir ) )
            return 'the directory ' . expAudit::relativePath( $dir ) . ' is not writable';
        $lock = $dir . '/.' . $channel . '.lock';
        $h = @fopen( $lock, 'c' );
        if ( !$h )
            return 'the lock ' . expAudit::relativePath( $lock ) . ' cannot be opened';
        fclose( $h );
        $files = expAuditWriter::channelFiles( $dir, $channel );
        $newest = $files ? $dir . '/' . end( $files ) : null;
        if ( $newest !== null && !is_writable( $newest ) )
            return 'the file ' . expAudit::relativePath( $newest ) . ' is not writable';
        $free = @disk_free_space( $dir );
        if ( $free !== false && $free < self::MIN_FREE_BYTES )
            return 'only ' . (int)$free . ' bytes are free';
        return null;
    }

    /**
     * ezpKernelWeb, before a module view runs: whether a POST to a sensitive module may run. True for everything
     * that is not guarded (see the class comment).
     *
     * @param string $moduleName
     * @param string $functionName
     * @return bool
     */
    public static function webAllows( $moduleName, $functionName )
    {
        try
        {
            if ( !isset( $_SERVER['REQUEST_METHOD'] ) || strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) !== 'POST' )
                return true;
            $config = expAuditConfig::get();
            if ( !self::refusing( $config ) )
                return true;
            $ini = eZINI::instance( 'audit.ini' );
            $modules = $ini->hasVariable( 'AuditReadSettings', 'AlwaysModules' ) ? (array)$ini->variable( 'AuditReadSettings', 'AlwaysModules' ) : array();
            if ( !in_array( (string)$moduleName, $modules, true ) )
                return true;
            $exempt = $ini->hasVariable( 'AuditSettings', 'RefuseExemptViews' ) ? (array)$ini->variable( 'AuditSettings', 'RefuseExemptViews' )
                                                                                : array( 'user/login', 'user/logout' );
            if ( in_array( $moduleName . '/' . $functionName, $exempt, true ) )
                return true;
            $user = eZUser::currentUser();
            if ( !$user instanceof eZUser || $user->isAnonymous() )
                return true;
        }
        catch ( Throwable $e )
        {
            return true;
        }
        // the access channel (access.view.sensitive was just written at once; role and user changes go there) and,
        // for the settings and the audit, the system channel
        if ( !self::allows( 'access.view.sensitive', $config ) )
            return false;
        if ( in_array( (string)$moduleName, array( 'setup', 'settings' ), true ) )
            return self::allows( 'system.setting.write', $config );
        if ( (string)$moduleName === 'audit' )
            return self::allows( 'system.audit.read', $config );
        return true;
    }

    /**
     * The module result of a refused POST: 503 with audit/refused.tpl.
     *
     * @param string $moduleName
     * @param string $functionName
     * @return array
     */
    public static function refusedResult( $moduleName, $functionName )
    {
        if ( !headers_sent() )
        {
            $protocol = class_exists( 'eZSys' ) ? (string)eZSys::serverVariable( 'SERVER_PROTOCOL', true ) : '';
            header( ( $protocol !== '' ? $protocol : 'HTTP/1.1' ) . ' 503 Service Unavailable' );
            header( 'Status: 503 Service Unavailable' );
            header( 'Retry-After: 300' );
        }
        $l = self::$last ?: array( 'name' => '', 'channel' => '', 'problem' => '' );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'view', $moduleName . '/' . $functionName );
        $tpl->setVariable( 'channel', $l['channel'] );
        $tpl->setVariable( 'event_name', $l['name'] );
        return array( 'content' => $tpl->fetch( 'design:audit/refused.tpl' ),
                      'path' => array( array( 'text' => ezpI18n::tr( 'design/standard/audit', 'Not recorded, so refused' ), 'url' => false ) ) );
    }
}
