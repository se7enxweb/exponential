<?php
/**
 * File containing the expMigrationRecorder class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The tap of the recording of changes: the kernel's audit hands every event of an operation to observe() (content
 * published, a node moved, a class stored, a role changed, a setting written ...; expMigrationRecordingEvent::STEPS
 * lists them), whether the audit itself records that event or not. While a recording runs that covers the user who
 * made the change, the event is turned into a migration step (expMigrationRecordingEvent::normalise()) and stored
 * (expMigrationRecording::addEvent()).
 *
 * It costs nothing while nothing records: wants() reads the small file of running recordings once per request
 * (expMigrationRecording::active(); a request of a persistent Velocity worker is told apart by REQUEST_TIME_FLOAT, as
 * the audit does) and answers from memory afterwards. Nothing here throws into the caller's work: a failure is a
 * notice in the debug output.
 *
 * Changes made by a migration run are not recorded (they are already a migration): the runner's process is paused
 * with pause().
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRecorder
{
    /** @var string|null the request the state below belongs to */
    protected static $requestKey = null;

    /** @var array[]|null the running recordings of this request (null: not read yet) */
    protected static $active = null;

    /** @var array|null [RecordingSettings] of migration.ini */
    protected static $settings = null;

    /** @var int depth of observe() in progress (an event recorded while recording another) */
    protected static $inside = 0;

    /** @var int depth of pause() */
    protected static $paused = 0;

    /** @var expMigrationRecordingSource|null for tests */
    protected static $source = null;

    /** @var int|null a fixed user for tests */
    protected static $user = null;

    /**
     * Whether a change of this audit name is recorded now: a recording runs that covers the current user, and the
     * name is one a recording keeps.
     *
     * @param string $name
     * @return bool
     */
    public static function wants( $name )
    {
        try
        {
            if ( self::$paused > 0 || self::$inside > 0 )
                return false;
            self::forRequest();
            if ( self::$active === null )
                self::$active = self::readActive();
            if ( !self::$active )
                return false;
            $settings = self::settings();
            if ( !$settings['enabled'] || !expMigrationRecordingEvent::recorded( $name, $settings['events'] ) )
                return false;
            return (bool)expMigrationRecording::matching( self::$active, self::currentUserId() );
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Whether any recording runs now (for the descriptions that keep remote ids before a removal).
     *
     * @return bool
     */
    public static function isActive()
    {
        try
        {
            if ( self::$paused > 0 )
                return false;
            self::forRequest();
            if ( self::$active === null )
                self::$active = self::readActive();
            return (bool)self::$active;
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Records an audit event in the recordings that cover the current user. Never throws.
     *
     * @param string $name
     * @param array $data the data of the audit event
     * @return int how many recordings kept it
     */
    public static function observe( $name, array $data )
    {
        if ( !self::wants( $name ) )
            return 0;
        self::$inside++;
        try
        {
            $settings = self::settings();
            $event = expMigrationRecordingEvent::normalise( $name, $data, self::source(), array( 'keepUserAttributes' => $settings['keepUserAttributes'] ) );
            if ( $event === null )
                return 0;
            $userId = self::currentUserId();
            $db = eZDB::instance();
            $kept = 0;
            foreach ( expMigrationRecording::matching( self::$active, $userId ) as $recording )
                if ( expMigrationRecording::addEvent( $db, $recording, $event, $userId, $settings['maxEvents'] ) )
                    $kept++;
            return $kept;
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeNotice( 'The recording of changes could not keep ' . (string)$name . ': ' . $e->getMessage(), __METHOD__ );
            return 0;
        }
        finally
        {
            self::$inside--;
        }
    }

    /**
     * Runs code without recording what it changes (a migration run: its changes are a migration already).
     *
     * @param callable $code
     * @return mixed what $code returns
     */
    public static function paused( $code )
    {
        self::$paused++;
        try
        {
            return call_user_func( $code );
        }
        finally
        {
            self::$paused--;
        }
    }

    /**
     * Pauses or resumes recording in this process (the migration runner, for the whole run).
     *
     * @param bool $pause
     */
    public static function pause( $pause = true )
    {
        self::$paused = $pause ? self::$paused + 1 : max( 0, self::$paused - 1 );
    }

    /** Forgets what this process read (the file changed; a new request; tests). */
    public static function forget()
    {
        self::$active = null;
        self::$settings = null;
        if ( class_exists( 'expMigrationRecordingResolver' ) )
            expMigrationRecordingResolver::forget();
    }

    /**
     * Tests: a source of the installation and a user instead of the real ones (null resets).
     *
     * @param expMigrationRecordingSource|null $source
     * @param int|null $userId
     */
    public static function setUp( ?expMigrationRecordingSource $source = null, $userId = null )
    {
        self::$source = $source;
        self::$user = $userId === null ? null : (int)$userId;
        self::forget();
    }

    /**
     * The settings of the recording: [RecordingSettings] of migration.ini.
     *
     * @return array enabled, events (null: every name of STEPS), keepUserAttributes, maxEvents
     */
    public static function settings()
    {
        if ( self::$settings !== null )
            return self::$settings;
        $settings = array( 'enabled' => true, 'events' => null, 'keepUserAttributes' => array(), 'maxEvents' => 10000 );
        $ini = eZINI::instance( 'migration.ini' );
        if ( $ini->hasGroup( 'RecordingSettings' ) )
        {
            if ( $ini->hasVariable( 'RecordingSettings', 'Recording' ) )
                $settings['enabled'] = strtolower( trim( (string)$ini->variable( 'RecordingSettings', 'Recording' ) ) ) !== 'disabled';
            if ( $ini->hasVariable( 'RecordingSettings', 'EventList' ) )
            {
                $events = array_values( array_filter( (array)$ini->variable( 'RecordingSettings', 'EventList' ), 'strlen' ) );
                $settings['events'] = $events ?: null;
            }
            if ( $ini->hasVariable( 'RecordingSettings', 'UserKeepAttributeList' ) )
                $settings['keepUserAttributes'] = array_values( array_filter( (array)$ini->variable( 'RecordingSettings', 'UserKeepAttributeList' ), 'strlen' ) );
            if ( $ini->hasVariable( 'RecordingSettings', 'MaxEvents' ) )
                $settings['maxEvents'] = max( 0, (int)$ini->variable( 'RecordingSettings', 'MaxEvents' ) );
        }
        return self::$settings = $settings;
    }

    /** @return expMigrationRecordingSource */
    protected static function source()
    {
        return self::$source ?: new expMigrationRecordingResolver();
    }

    /**
     * The running recordings, from the file. A window that has ended is stopped first (one query, only then).
     *
     * @return array[]
     */
    protected static function readActive()
    {
        $active = expMigrationRecording::active();
        $now = time();
        foreach ( $active as $r )
        {
            if ( !expMigrationRecording::ended( $r, $now ) )
                continue;
            try
            {
                self::$inside++;
                expMigrationRecording::closeEnded( eZDB::instance(), $now );
            }
            catch ( Throwable $e )
            {
                if ( class_exists( 'eZDebug' ) )
                    eZDebug::writeNotice( 'A recording whose window ended could not be stopped: ' . $e->getMessage(), __METHOD__ );
            }
            finally
            {
                self::$inside--;
            }
            return array_values( array_filter( expMigrationRecording::active(), function ( $one ) use ( $now ) {
                return !expMigrationRecording::ended( $one, $now );
            } ) );
        }
        return $active;
    }

    /** @return int the user making the change */
    protected static function currentUserId()
    {
        if ( self::$user !== null )
            return self::$user;
        return class_exists( 'eZUser' ) ? (int)eZUser::currentUserID() : 0;
    }

    /** A new request (a persistent worker serves many): read the file again. */
    protected static function forRequest()
    {
        $key = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        if ( $key !== self::$requestKey )
        {
            self::$requestKey = $key;
            self::$active = null;
            self::$settings = null;
            if ( class_exists( 'expMigrationRecordingResolver' ) )
                expMigrationRecordingResolver::forget();
        }
    }
}
