<?php
/**
 * File containing the expMigrationRecording class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Recordings of changes: what was changed in the administration (or anywhere in the kernel) while a recording ran,
 * kept as the steps of a YAML migration (expMigrationRecordingEvent) for exp:migration:generate --from-recording.
 *
 * Two kinds:
 *
 *   session  one user's own changes, from start until stop: the user who started it (in the administration, the
 *            website sidebar, or a console command run as that user); one per user at a time
 *   site     every change of every user and of the console while the window is open; one at a time
 *
 * Either kind can be started for a window of time (start() with $until): it stops by itself when the window ends.
 * While a recording runs, its stopped_at is the end of its window (0: none, it runs until it is stopped); the
 * recorder keeps nothing after it, and closeEnded() stops it, its stopped_at staying the end of the window.
 *
 * Tables exp_migration_recording (one row per recording) and exp_migration_recording_event (one row per recorded
 * change, its payload the event as JSON), plain SQL like expMigrationRecord, on every engine. Until the tables exist
 * (upgrade step 6.0.15-migration-recording-tables) nothing can be started.
 *
 * Which recordings are running is also kept in a small file, var/<site>/migration/recording/active.json, written by
 * start, stop and discard, so the recorder (expMigrationRecorder) knows without a query on every request whether
 * anything is recorded. The database is the truth: sync() writes the file again from it.
 *
 * Personal data (open question 16, answer a): the account of a user (login, e-mail, password) and the fields of
 * user objects are never recorded; recorded events name who made them by user id only, and a generated migration
 * leaves even that out.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRecording
{
    const TABLE = 'exp_migration_recording';

    const EVENT_TABLE = 'exp_migration_recording_event';

    /** The kinds of recording. */
    const MODE_SESSION = 'session';
    const MODE_SITE = 'site';
    const MODES = array( self::MODE_SESSION, self::MODE_SITE );

    /** The states of a recording (a discarded one is deleted). */
    const RECORDING = 'recording';
    const STOPPED = 'stopped';

    /** The columns of exp_migration_recording: name => array( type, length ). */
    const COLUMNS = array(
        'recording_id' => array( 'string', 64 ),
        'kind' => array( 'string', 20 ),
        'label' => array( 'string', 255 ),
        'state' => array( 'string', 20 ),
        'user_id' => array( 'int', 0 ),
        'started_at' => array( 'int', 0 ),
        'stopped_at' => array( 'int', 0 ),
        'environment' => array( 'string', 50 ),
        'event_count' => array( 'int', 0 ),
        'message' => array( 'string', 1000 ),
    );

    /** The columns of exp_migration_recording_event (payload is a text of any length). */
    const EVENT_COLUMNS = array(
        'recording_id' => array( 'string', 64 ),
        'event_id' => array( 'string', 32 ),
        'recorded_at' => array( 'int', 0 ),
        'audit_name' => array( 'string', 100 ),
        'step_type' => array( 'string', 50 ),
        'step_mode' => array( 'string', 20 ),
        'subject' => array( 'string', 255 ),
        'actor_id' => array( 'int', 0 ),
        'payload' => array( 'text', 0 ),
    );

    /** The longest window, in seconds (31 days). */
    const MAX_WINDOW = 2678400;

    /** The file that says which recordings run (relative to the var directory). */
    const ACTIVE_FILE = 'migration/recording/active.json';

    /** @var int the sequence of event ids of this process */
    protected static $sequence = 0;

    /** @var int|null the millisecond of the last event id */
    protected static $lastMs = null;

    // ------------------------------------------------------------------ tables

    /**
     * Whether both tables exist (true, false, null when it cannot be told).
     *
     * @param eZDBInterface $db
     * @return bool|null
     */
    public static function tablesExist( $db )
    {
        $one = expKernelTableSchema::exists( self::TABLE, $db, true );
        $two = expKernelTableSchema::exists( self::EVENT_TABLE, $db, true );
        if ( $one === null || $two === null )
            return null;
        return $one && $two;
    }

    // ------------------------------------------------------------------ the life cycle

    /**
     * Checks and starts a recording. One session recording per user, one site-wide window.
     *
     * @param eZDBInterface $db
     * @param string $mode session or site
     * @param int $userId the user who starts it (session: whose changes are recorded)
     * @param string $label
     * @param string[] $errors set to the reasons it was not started
     * @param int $until the end of the window (a time), 0: until it is stopped
     * @return array|null the recording's row
     */
    public static function start( $db, $mode, $userId, $label = '', &$errors = array(), $until = 0 )
    {
        $errors = self::startProblems( $mode, $userId, $label, $until );
        if ( $errors )
            return null;
        if ( self::tablesExist( $db ) !== true )
        {
            $errors[] = 'the recording tables do not exist yet: ./console exp:upgrade --apply makes them (upgrade step 6.0.15-migration-recording-tables)';
            return null;
        }
        self::closeEnded( $db );
        foreach ( self::running( $db ) as $row )
        {
            if ( $mode === self::MODE_SITE && $row['kind'] === self::MODE_SITE )
                $errors[] = 'a site-wide recording is already running: ' . $row['recording_id'];
            if ( $mode === self::MODE_SESSION && $row['kind'] === self::MODE_SESSION && (int)$row['user_id'] === (int)$userId )
                $errors[] = 'this user is already recording: ' . $row['recording_id'];
        }
        if ( $errors )
            return null;
        $row = self::normalize( array( 'recording_id' => self::newId(), 'kind' => $mode, 'label' => trim( (string)$label ), 'state' => self::RECORDING,
                                       'user_id' => (int)$userId, 'started_at' => time(), 'stopped_at' => (int)$until,
                                       'environment' => expMigrationRecord::environment() ),
                                self::COLUMNS );
        $db->query( self::insertSql( $db, self::TABLE, $row ) );
        self::sync( $db );
        self::audit( 'start', $row );
        return $row;
    }

    /**
     * The problems of a start request, before the database is asked. Pure.
     *
     * @param string $mode
     * @param int $userId
     * @param string $label
     * @param int $until the end of the window, 0: none
     * @param int|null $now
     * @return string[]
     */
    public static function startProblems( $mode, $userId, $label, $until = 0, $now = null )
    {
        $errors = array();
        $now = $now === null ? time() : (int)$now;
        if ( (int)$until !== 0 && (int)$until <= $now )
            $errors[] = 'the end of the window is not in the future';
        else if ( (int)$until > $now + self::MAX_WINDOW )
            $errors[] = 'a window is at most ' . ( self::MAX_WINDOW / 86400 ) . ' days long';
        if ( !in_array( $mode, self::MODES, true ) )
            $errors[] = "the kind of recording is session or site, not $mode";
        if ( $mode === self::MODE_SESSION && (int)$userId <= 0 )
            $errors[] = 'a session recording records one user: name the user (--login)';
        if ( mb_strlen( (string)$label ) > 255 )
            $errors[] = 'the label is longer than 255 characters';
        return $errors;
    }

    /**
     * Stops a recording; its events are kept until it is discarded.
     *
     * @param eZDBInterface $db
     * @param string $recordingId
     * @param string[] $errors
     * @return array|null the row as it is now
     */
    public static function stop( $db, $recordingId, &$errors = array() )
    {
        $errors = array();
        $row = self::get( $db, $recordingId );
        if ( $row === null )
        {
            $errors[] = "no recording $recordingId";
            return null;
        }
        if ( $row['state'] !== self::RECORDING )
        {
            $errors[] = "the recording $recordingId is not running (it is {$row['state']})";
            return $row;
        }
        $row['state'] = self::STOPPED;
        $row['stopped_at'] = time();
        $row['event_count'] = self::countEvents( $db, $recordingId );
        $db->query( 'UPDATE ' . self::TABLE . " SET state = '" . self::STOPPED . "', stopped_at = " . (int)$row['stopped_at']
                    . ', event_count = ' . (int)$row['event_count'] . ' WHERE ' . self::idWhere( $db, $recordingId ) );
        self::sync( $db );
        self::audit( 'stop', $row );
        return $row;
    }

    /**
     * Stops the recordings whose window has ended: their stopped_at stays the end of the window. start() and the
     * recorder call it; exp:migration:record:status too.
     *
     * @param eZDBInterface $db
     * @param int|null $now
     * @return array[] the rows stopped
     */
    public static function closeEnded( $db, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $closed = array();
        foreach ( self::running( $db ) as $row )
        {
            if ( !self::ended( $row, $now ) )
                continue;
            $row['state'] = self::STOPPED;
            $row['event_count'] = self::countEvents( $db, $row['recording_id'] );
            $db->query( 'UPDATE ' . self::TABLE . " SET state = '" . self::STOPPED . "', event_count = " . (int)$row['event_count']
                        . ' WHERE ' . self::idWhere( $db, $row['recording_id'] ) . " AND state = '" . self::RECORDING . "'" );
            self::audit( 'stop', $row );
            $closed[] = $row;
        }
        if ( $closed )
            self::sync( $db );
        return $closed;
    }

    /**
     * Whether the window of a running recording has ended (a row, or an entry of active()). Pure.
     *
     * @param array $recording
     * @param int $now
     * @return bool
     */
    public static function ended( array $recording, $now )
    {
        $until = isset( $recording['until'] ) ? (int)$recording['until'] : ( isset( $recording['stopped_at'] ) ? (int)$recording['stopped_at'] : 0 );
        return $until > 0 && (int)$now >= $until;
    }

    /**
     * Removes a recording and its events (running or stopped).
     *
     * @param eZDBInterface $db
     * @param string $recordingId
     * @param string[] $errors
     * @return bool
     */
    public static function discard( $db, $recordingId, &$errors = array() )
    {
        $errors = array();
        $row = self::get( $db, $recordingId );
        if ( $row === null )
        {
            $errors[] = "no recording $recordingId";
            return false;
        }
        $db->begin();
        try
        {
            $db->query( 'DELETE FROM ' . self::EVENT_TABLE . ' WHERE ' . self::idWhere( $db, $recordingId ) );
            $db->query( 'DELETE FROM ' . self::TABLE . ' WHERE ' . self::idWhere( $db, $recordingId ) );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        self::sync( $db );
        self::audit( 'discard', $row );
        return true;
    }

    // ------------------------------------------------------------------ reading

    /**
     * One recording, or null.
     *
     * @param eZDBInterface $db
     * @param string $recordingId
     * @return array|null
     */
    public static function get( $db, $recordingId )
    {
        if ( !self::validId( $recordingId ) || self::tablesExist( $db ) !== true )
            return null;
        $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::TABLE . ' WHERE ' . self::idWhere( $db, $recordingId ) );
        return $rows ? self::normalize( array_change_key_case( (array)$rows[0], CASE_LOWER ), self::COLUMNS ) : null;
    }

    /**
     * Every recording, the newest first.
     *
     * @param eZDBInterface $db
     * @param int $limit
     * @return array[]
     */
    public static function all( $db, $limit = 100 )
    {
        if ( self::tablesExist( $db ) !== true )
            return array();
        $rows = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::TABLE ) as $row )
            $rows[] = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::COLUMNS );
        usort( $rows, function ( $a, $b ) {
            return $b['started_at'] <=> $a['started_at'] ?: strcmp( $b['recording_id'], $a['recording_id'] );
        } );
        return array_slice( $rows, 0, max( 1, (int)$limit ) );
    }

    /**
     * The recordings that run.
     *
     * @param eZDBInterface $db
     * @return array[]
     */
    public static function running( $db )
    {
        if ( self::tablesExist( $db ) !== true )
            return array();
        $rows = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::TABLE . " WHERE state = '" . self::RECORDING . "'" ) as $row )
            $rows[] = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::COLUMNS );
        return $rows;
    }

    /**
     * The session recording of a user that runs, or null.
     *
     * @param eZDBInterface $db
     * @param int $userId
     * @return array|null
     */
    public static function runningFor( $db, $userId )
    {
        foreach ( self::running( $db ) as $row )
            if ( $row['kind'] === self::MODE_SESSION && (int)$row['user_id'] === (int)$userId )
                return $row;
        return null;
    }

    /**
     * The site-wide recording that runs, or null.
     *
     * @param eZDBInterface $db
     * @return array|null
     */
    public static function runningSite( $db )
    {
        foreach ( self::running( $db ) as $row )
            if ( $row['kind'] === self::MODE_SITE )
                return $row;
        return null;
    }

    /**
     * The events of a recording in the order they were recorded, each with its payload decoded under "event".
     *
     * @param eZDBInterface $db
     * @param string $recordingId
     * @param int $limit 0 for all
     * @return array[]
     */
    public static function events( $db, $recordingId, $limit = 0 )
    {
        if ( !self::validId( $recordingId ) || self::tablesExist( $db ) !== true )
            return array();
        $rows = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::EVENT_TABLE . ' WHERE ' . self::idWhere( $db, $recordingId ) ) as $row )
        {
            $row = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::EVENT_COLUMNS );
            $row['event'] = expMigrationRecordingEvent::decode( $row['payload'] );
            unset( $row['payload'] );
            $rows[] = $row;
        }
        usort( $rows, function ( $a, $b ) {
            return strcmp( $a['event_id'], $b['event_id'] );
        } );
        return $limit > 0 ? array_slice( $rows, 0, (int)$limit ) : $rows;
    }

    /**
     * How many events a recording has.
     *
     * @param eZDBInterface $db
     * @param string $recordingId
     * @return int
     */
    public static function countEvents( $db, $recordingId )
    {
        if ( self::tablesExist( $db ) !== true )
            return 0;
        $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT event_id FROM ' . self::EVENT_TABLE . ' WHERE ' . self::idWhere( $db, $recordingId ) );
        return $rows ? count( $rows ) : 0;
    }

    // ------------------------------------------------------------------ writing events

    /**
     * Adds an event to a recording. Never throws.
     *
     * @param eZDBInterface $db
     * @param array $recording the running recording (a row of active())
     * @param array $event a normalised event
     * @param int $actorId the user who made the change
     * @param int $maxEvents the most a recording keeps (0: no limit)
     * @return bool
     */
    public static function addEvent( $db, array $recording, array $event, $actorId, $maxEvents = 0 )
    {
        $row = self::normalize( array(
            'recording_id' => $recording['recording_id'],
            'event_id' => self::eventId(),
            'recorded_at' => time(),
            'audit_name' => $event['audit'],
            'step_type' => isset( $event['step']['type'] ) ? $event['step']['type'] : '',
            'step_mode' => isset( $event['step']['mode'] ) ? $event['step']['mode'] : '',
            'subject' => $event['subject'],
            'actor_id' => (int)$actorId,
            'payload' => expMigrationRecordingEvent::encode( $event ),
        ), self::EVENT_COLUMNS );
        $current = self::get( $db, $recording['recording_id'] );
        if ( $current === null || $current['state'] !== self::RECORDING )
            return false;
        if ( $maxEvents > 0 && $current['event_count'] >= $maxEvents )
        {
            if ( $current['message'] === '' )
                $db->query( 'UPDATE ' . self::TABLE . " SET message = '" . $db->escapeString( "stopped recording at $maxEvents events ([RecordingSettings] MaxEvents)" )
                            . "' WHERE " . self::idWhere( $db, $recording['recording_id'] ) );
            return false;
        }
        $db->query( self::insertSql( $db, self::EVENT_TABLE, $row ) );
        $db->query( 'UPDATE ' . self::TABLE . ' SET event_count = ' . ( (int)$current['event_count'] + 1 ) . ' WHERE ' . self::idWhere( $db, $recording['recording_id'] ) );
        return true;
    }

    /**
     * A new event id: the millisecond and a sequence of this process, so ids sort in the order the changes were
     * made; a few random digits keep two processes apart.
     *
     * @return string 26 characters
     */
    public static function eventId()
    {
        $ms = (int)floor( microtime( true ) * 1000 );
        if ( self::$lastMs !== null && $ms <= self::$lastMs )
            $ms = self::$lastMs;
        else
            self::$sequence = 0;
        self::$lastMs = $ms;
        self::$sequence++;
        return sprintf( '%013d-%06d-%s', $ms, self::$sequence % 1000000, substr( bin2hex( random_bytes( 3 ) ), 0, 5 ) );
    }

    // ------------------------------------------------------------------ the file of the running recordings

    /**
     * The file that lists the running recordings.
     *
     * @return string
     */
    public static function activeFile()
    {
        return rtrim( (string)eZSys::varDirectory(), '/' ) . '/' . self::ACTIVE_FILE;
    }

    /**
     * Writes the file of the running recordings from the database (start, stop, discard; exp:migration:record:status
     * repairs it).
     *
     * @param eZDBInterface $db
     * @return bool
     */
    public static function sync( $db )
    {
        $list = array();
        foreach ( self::running( $db ) as $row )
            $list[] = array( 'recording_id' => $row['recording_id'], 'kind' => $row['kind'], 'user_id' => (int)$row['user_id'],
                             'started_at' => (int)$row['started_at'], 'until' => (int)$row['stopped_at'] );
        $written = self::writeActive( self::activeFile(), $list );
        if ( class_exists( 'expMigrationRecorder' ) )
            expMigrationRecorder::forget();
        return $written;
    }

    /**
     * Writes the list of running recordings to a file (atomically); an empty list removes the file.
     *
     * @param string $file
     * @param array[] $list
     * @return bool
     */
    public static function writeActive( $file, array $list )
    {
        if ( !$list )
        {
            if ( is_file( $file ) )
                return @unlink( $file );
            return true;
        }
        $dir = dirname( $file );
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) && !is_dir( $dir ) )
            return false;
        $temp = $file . '.' . getmypid() . '.tmp';
        $json = json_encode( array( 'schema' => 'exp-migration-recording-active/1', 'recordings' => array_values( $list ) ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        if ( @file_put_contents( $temp, $json . "\n" ) === false )
            return false;
        @chmod( $temp, eZFile::fileMode( 0664 ) );
        return @rename( $temp, $file );
    }

    /**
     * The running recordings as the file lists them (no database), [] when none run.
     *
     * @param string|null $file
     * @return array[] recording_id, mode, user_id, started_at
     */
    public static function active( $file = null )
    {
        $file = $file === null ? self::activeFile() : $file;
        clearstatcache( true, $file );
        if ( !is_file( $file ) )
            return array();
        $data = json_decode( (string)@file_get_contents( $file ), true );
        $out = array();
        foreach ( is_array( $data ) && isset( $data['recordings'] ) ? (array)$data['recordings'] : array() as $r )
            if ( is_array( $r ) && isset( $r['recording_id'], $r['kind'] ) && self::validId( $r['recording_id'] ) && in_array( $r['kind'], self::MODES, true ) )
                $out[] = array( 'recording_id' => (string)$r['recording_id'], 'kind' => (string)$r['kind'],
                                'user_id' => isset( $r['user_id'] ) ? (int)$r['user_id'] : 0, 'started_at' => isset( $r['started_at'] ) ? (int)$r['started_at'] : 0,
                                'until' => isset( $r['until'] ) ? (int)$r['until'] : 0 );
        return $out;
    }

    /**
     * The running recordings that record a change made by a user: the user's session recording and the site-wide
     * one. Pure.
     *
     * @param array[] $active active()
     * @param int $userId
     * @param int|null $now a window that has ended records nothing more
     * @return array[]
     */
    public static function matching( array $active, $userId, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $out = array();
        foreach ( $active as $r )
            if ( self::ended( $r, $now ) )
                continue;
            else if ( $r['kind'] === self::MODE_SITE || ( $r['kind'] === self::MODE_SESSION && (int)$r['user_id'] === (int)$userId && (int)$userId > 0 ) )
                $out[] = $r;
        return $out;
    }

    /**
     * What the administration bar and the website sidebar show to a user: their own recording and the site-wide
     * window. No database: from the file.
     *
     * @param int $userId
     * @param string|null $file
     * @return array mine (the user's session recording or null), site (the site-wide one or null)
     */
    public static function forUser( $userId, $file = null )
    {
        $out = array( 'mine' => null, 'site' => null );
        $now = time();
        foreach ( self::active( $file ) as $r )
        {
            if ( self::ended( $r, $now ) )
                continue;
            if ( $r['kind'] === self::MODE_SITE )
                $out['site'] = $r;
            else if ( (int)$r['user_id'] === (int)$userId && (int)$userId > 0 )
                $out['mine'] = $r;
        }
        return $out;
    }

    // ------------------------------------------------------------------ helpers

    /**
     * A new recording id: <date>-<time>-<6 hex digits> (UTC), like a run id.
     *
     * @return string
     */
    public static function newId()
    {
        return gmdate( 'Ymd-His' ) . '-' . bin2hex( random_bytes( 3 ) );
    }

    /**
     * Whether a text is a recording id. Pure.
     *
     * @param string $id
     * @return bool
     */
    public static function validId( $id )
    {
        return is_string( $id ) && preg_match( '/^\d{8}-\d{6}-[0-9a-f]{6}$/', $id ) === 1;
    }

    /**
     * A row with exactly the given columns. Pure.
     *
     * @param array $row
     * @param array $columns COLUMNS or EVENT_COLUMNS
     * @return array
     */
    public static function normalize( array $row, array $columns )
    {
        $clean = array();
        foreach ( $columns as $name => $type )
        {
            $value = isset( $row[$name] ) ? $row[$name] : null;
            if ( $type[0] === 'int' )
                $clean[$name] = (int)$value;
            else if ( $type[0] === 'text' )
                $clean[$name] = is_scalar( $value ) ? (string)$value : '';
            else
                $clean[$name] = mb_substr( is_scalar( $value ) ? (string)$value : '', 0, $type[1] );
        }
        return $clean;
    }

    /**
     * A summary of a recording for the administration and the commands: the row with its events counted by step.
     *
     * @param array $row
     * @param array[] $events events() of it
     * @return array
     */
    public static function summary( array $row, array $events )
    {
        $byStep = array();
        $omitted = 0;
        foreach ( $events as $e )
        {
            $key = $e['step_type'] . '/' . $e['step_mode'];
            $byStep[$key] = ( isset( $byStep[$key] ) ? $byStep[$key] : 0 ) + 1;
            if ( !empty( $e['event']['omitted'] ) )
                $omitted++;
        }
        ksort( $byStep );
        return $row + array( 'events' => count( $events ), 'steps' => $byStep, 'with_omitted' => $omitted,
                             'step_types' => expMigrationRecordingEvent::stepTypes( array_filter( array_column( $events, 'event' ) ) ) );
    }

    /**
     * @param eZDBInterface $db
     * @param string $recordingId
     * @return string
     */
    protected static function idWhere( $db, $recordingId )
    {
        return "recording_id = '" . $db->escapeString( (string)$recordingId ) . "'";
    }

    /**
     * @param eZDBInterface $db
     * @param string $table
     * @param array $row normalized
     * @return string
     */
    protected static function insertSql( $db, $table, array $row )
    {
        $values = array();
        foreach ( $row as $value )
            $values[] = is_int( $value ) ? (string)$value : "'" . $db->escapeString( (string)$value ) . "'";
        return 'INSERT INTO ' . $table . ' ( ' . implode( ', ', array_keys( $row ) ) . ' ) VALUES ( ' . implode( ', ', $values ) . ' )';
    }

    /**
     * The audit of a recording started, stopped or discarded (system.migration.record, the verb says which).
     *
     * @param string $verb start, stop, discard
     * @param array $row
     */
    protected static function audit( $verb, array $row )
    {
        if ( !class_exists( 'expAudit' ) )
            return;
        expAudit::event( 'system.migration.record', array( 'verb' => $verb,
            'object' => array( 'type' => 'recording', 'id' => $row['recording_id'] ),
            'after' => array( 'kind' => $row['kind'], 'state' => $verb === 'discard' ? 'discarded' : $row['state'],
                              'events' => (int)$row['event_count'] ) ) );
    }
}
