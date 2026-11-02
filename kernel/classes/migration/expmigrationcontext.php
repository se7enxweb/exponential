<?php
/**
 * File containing the expMigrationContext class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a migration run works on: everything of expUpgradeContext (the connection, its engine, the options, whether it
 * writes) plus the run id, the user the run runs as, the environment and the references migrations pass on to the
 * ones after them.
 *
 * The runner (expMigrationRunner) reaches the installation only through the methods of this class: the record
 * (record(), writeRecord(), writeRun()), the lock, maintenance mode, transactions, events, the audit log, the run log
 * and the switch to the run's user. A test gives the runner a subclass that keeps all of it in memory.
 *
 * Options besides those of expUpgradeContext:
 *   run-id       the run's id (a new one when not given: YYYYMMDD-HHMMSS-<6 hex>)
 *   environment  the environment (expMigrationRecord::environment() when not given)
 *   user         the eZUser the run runs as (resolveUser() when not given)
 *   login        a login to run as instead of the signed-in user or [RunSettings] RunAsUser
 *   repository   the migrations repository the run log and status mirror are written to (<root>/migrations)
 *   lock-file    another lock file (a test's)
 *   no-mirror    true: no status mirror and no run log are written
 *   backup-dir   the folder of captions made without git_manager (expUpgradeCaption)
 *   storage-dir  the var/ storage the caption reuse check compares (eZSys::storageDirectory() when not given)
 *   no-cache-clear  true: the caches the migrations touched are not cleared after the run ([RunSettings] ClearCaches)
 *   reindex      queue, now or disabled: what happens to the objects a run touched ([RunSettings] Reindex)
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationContext extends expUpgradeContext
{
    /** A transaction around each migration that also undoes schema changes (PostgreSQL, SQLite). */
    const TRANSACTION_FULL = 'full';
    /** A transaction around each migration; the engine commits schema changes at once (MySQL/MariaDB, Oracle). */
    const TRANSACTION_DATA = 'data';
    /** No transactions (MongoDB: begin, commit and rollback do nothing). */
    const TRANSACTION_NONE = 'none';

    /** @var string */
    protected $runId;

    /** @var string */
    protected $environment;

    /** @var eZUser|null|false false: not resolved yet */
    protected $user = false;

    /** @var array name => value */
    protected $references = array();

    /** @var array|null source\nid => row of exp_migration */
    protected $records;

    /** @var array|null the user and session before becomeUser() */
    protected $previousUser;

    /** @var array object id => true: the content objects the steps of the run created or changed */
    protected $touchedObjects = array();

    /** @var string[] the caches steps asked to be cleared after the run (the step type cache, when: end) */
    protected $deferredCaches = array();

    /** @var array[] the code and design files the steps of the migration that runs now wrote (code_file) */
    protected $fileChanges = array();

    /** @var expMigrationLock|null */
    protected $lock;

    /** @var expMigrationStatusMirror|null */
    protected $mirror;

    /** @var int|null the error handling of the connection before a migration */
    protected $previousErrorHandling;

    /** @var array column => value of exp_migration_run that the caption flow sets (caption_pre ...) */
    protected $runFields = array();

    /** @var string|null the file of the YAML migration whose steps run (or are planned) now */
    protected $migrationFile;

    /**
     * @param eZDBInterface|null $db the connection (eZDB::instance() when null)
     * @param bool $apply whether the run writes
     * @param array $options see the class
     * @param string|null $schemaFile the .dba (share/db_schema.dba when null)
     */
    public function __construct( $db = null, $apply = false, array $options = array(), $schemaFile = null )
    {
        parent::__construct( $db, $apply, $options, $schemaFile );
        $runId = isset( $options['run-id'] ) ? (string)$options['run-id'] : '';
        if ( $runId === '' )
            $runId = self::newRunId();
        if ( !expMigrationRecord::validRunId( $runId ) )
            throw new InvalidArgumentException( 'not a run id: ' . $runId );
        $this->runId = $runId;
        $this->environment = isset( $options['environment'] ) ? (string)$options['environment'] : expMigrationRecord::environment();
        if ( array_key_exists( 'user', $options ) )
            $this->user = $options['user'];
    }

    /**
     * A new run id: the time of day and six random hex digits, e.g. 20261031-120000-a1b2c3.
     *
     * @param int|null $time
     * @return string
     */
    public static function newRunId( $time = null )
    {
        return date( 'Ymd-His', $time === null ? time() : (int)$time ) . '-' . bin2hex( random_bytes( 3 ) );
    }

    /** @return string */
    public function runId()
    {
        return $this->runId;
    }

    /**
     * The file of the YAML migration whose steps run now: steps that read files of the migration (file, image) find
     * them next to it. expMigrationYaml sets it before its steps run, are planned or undone.
     *
     * @param string|null $file
     */
    public function setMigrationFile( $file )
    {
        $this->migrationFile = $file === null || $file === '' ? null : (string)$file;
    }

    /** @return string|null the file of the YAML migration whose steps run now, null outside one */
    public function migrationFile()
    {
        return $this->migrationFile;
    }

    /** @return string the environment ([AdminEnvironment] Level), empty when the installation names none */
    public function environment()
    {
        return $this->environment;
    }

    /**
     * The user the run runs as (resolved once, see resolveUser()).
     *
     * @return eZUser|null null when no such user exists
     */
    public function user()
    {
        if ( $this->user === false )
            $this->user = self::resolveUser( $this->option( 'login' ) );
        return $this->user;
    }

    /** @return string the login of the run's user, empty without one */
    public function userLogin()
    {
        $user = $this->user();
        return $user ? (string)$user->attribute( 'login' ) : '';
    }

    /**
     * Who ran the migration, as the record keeps it (run_by): "web <login>" or "cli <login>".
     *
     * @return string
     */
    public function runBy()
    {
        $login = $this->userLogin();
        return ( self::isWebRequest() ? 'web ' : 'cli ' ) . ( $login !== '' ? $login : '-' );
    }

    /**
     * The user a run runs as: the login given; in the administration the signed-in user; otherwise (console,
     * unattended runs, nobody signed in) migration.ini [RunSettings] RunAsUser. Always by login, never by id.
     *
     * @param string|null $login
     * @param eZINI|null $ini migration.ini
     * @return eZUser|null null when the user does not exist
     */
    public static function resolveUser( $login = null, $ini = null )
    {
        if ( $login !== null && (string)$login !== '' )
            return eZUser::fetchByName( (string)$login ) ?: null;
        if ( self::isWebRequest() )
        {
            $current = eZUser::currentUser();
            if ( $current instanceof eZUser && $current->isRegistered() )
                return $current;
        }
        $login = self::runAsLogin( $ini );
        return $login !== '' ? ( eZUser::fetchByName( $login ) ?: null ) : null;
    }

    /**
     * @param eZINI|null $ini migration.ini
     * @return string [RunSettings] RunAsUser
     */
    public static function runAsLogin( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        return $ini->hasVariable( 'RunSettings', 'RunAsUser' ) ? trim( (string)$ini->variable( 'RunSettings', 'RunAsUser' ) ) : '';
    }

    /**
     * A reference: a value one migration leaves for the ones after it in the same run (an object id, a node id).
     *
     * @param string $name
     * @param mixed $value
     */
    public function setReference( $name, $value )
    {
        $this->references[(string)$name] = $value;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function reference( $name, $default = null )
    {
        return array_key_exists( (string)$name, $this->references ) ? $this->references[(string)$name] : $default;
    }

    /** @return bool */
    public function hasReference( $name )
    {
        return array_key_exists( (string)$name, $this->references );
    }

    /** @return array name => value */
    public function references()
    {
        return $this->references;
    }

    // ---- what the run touched -----------------------------------------------------------------------------------

    /**
     * Notes a content object a step created or changed: after the run it is reindexed (expMigrationAfterRun), and
     * the step type search reindexes the objects noted so far when it names none.
     *
     * @param int|int[] $objectIds
     */
    public function touchObject( $objectIds )
    {
        foreach ( (array)$objectIds as $id )
            if ( is_numeric( $id ) && (int)$id > 0 )
                $this->touchedObjects[(int)$id] = true;
    }

    /** @return int[] the objects the steps of the run created or changed, in the order they were first touched */
    public function touchedObjects()
    {
        return array_keys( $this->touchedObjects );
    }

    /**
     * Notes caches to clear after the run, with those the steps of the migrations declare.
     *
     * @param string[] $caches ids or tags of ezcache (content, template-block, ini ...)
     */
    public function deferCaches( array $caches )
    {
        foreach ( $caches as $cache )
            if ( is_scalar( $cache ) && trim( (string)$cache ) !== '' && !in_array( trim( (string)$cache ), $this->deferredCaches, true ) )
                $this->deferredCaches[] = trim( (string)$cache );
    }

    /** @return string[] the caches steps asked to be cleared after the run */
    public function deferredCaches()
    {
        return $this->deferredCaches;
    }

    /**
     * Notes a code or design file a step of the migration that runs now wrote (expMigrationFiles::write()): when the
     * migration fails, the runner puts it back.
     *
     * @param array $change expMigrationFiles::write()
     */
    public function addFileChange( array $change )
    {
        $this->fileChanges[] = $change;
    }

    /**
     * The files the migration that ran last wrote, the last first, and forgets them (the runner, before and after
     * each migration).
     *
     * @return array[]
     */
    public function takeFileChanges()
    {
        $changes = array_reverse( $this->fileChanges );
        $this->fileChanges = array();
        return $changes;
    }

    // ---- the record --------------------------------------------------------------------------------------------

    /**
     * The row of exp_migration of a migration, or null.
     *
     * @param string $source
     * @param string $id
     * @return array|null
     */
    public function record( $source, $id )
    {
        if ( $this->records === null )
        {
            $this->records = array();
            foreach ( expMigrationRecord::all( $this->db() ) as $row )
                $this->records[$row['source'] . "\n" . $row['migration_id']] = $row;
        }
        $key = $source . "\n" . $id;
        return isset( $this->records[$key] ) ? $this->records[$key] : null;
    }

    /**
     * Whether the record can be written: both tables exist (upgrade step 6.0.15-migration-tables has run).
     *
     * @return bool
     */
    public function recordReady()
    {
        return expMigrationRecord::exists( $this->db() ) === true && expMigrationRecord::runTableExists( $this->db() ) === true;
    }

    /**
     * Writes the row of a migration (expMigrationRecord::write()).
     *
     * @param array $row
     * @return bool false when the table does not exist
     */
    public function writeRecord( array $row )
    {
        $written = expMigrationRecord::write( $this->db(), $row );
        $this->records = null;
        return $written;
    }

    /**
     * Removes the row of a migration (after its down()).
     *
     * @param string $source
     * @param string $id
     * @return bool
     */
    public function removeRecord( $source, $id )
    {
        $removed = expMigrationRecord::remove( $this->db(), $source, $id );
        $this->records = null;
        return $removed;
    }

    /**
     * Writes the row of the run (expMigrationRecord::writeRun()).
     *
     * @param array $run
     * @return bool false when the table does not exist
     */
    public function writeRun( array $run )
    {
        return expMigrationRecord::writeRun( $this->db(), $run );
    }

    /**
     * The row of this run in exp_migration_run, or null before it is written.
     *
     * @return array|null
     */
    public function runRecord()
    {
        return expMigrationRecord::run( $this->db(), $this->runId );
    }

    /**
     * Sets a column of this run's row that the runner does not know itself (caption_pre, caption_post, commit_id,
     * tag_name, push_state): every row of the run written after this carries it.
     *
     * @param string $name a column of expMigrationRecord::RUN_COLUMNS
     * @param string $value
     * @throws InvalidArgumentException for another name
     */
    public function setRunField( $name, $value )
    {
        if ( !array_key_exists( (string)$name, expMigrationRecord::RUN_COLUMNS ) || in_array( $name, array( 'run_id', 'state', 'started_at', 'finished_at' ), true ) )
            throw new InvalidArgumentException( 'not a column the caption flow sets: ' . $name );
        $this->runFields[$name] = (string)$value;
    }

    /**
     * @param string $name
     * @return string the value set with setRunField(), '' when none
     */
    public function runField( $name )
    {
        return isset( $this->runFields[$name] ) ? $this->runFields[$name] : '';
    }

    /** @return array column => value set with setRunField() */
    public function runFields()
    {
        return $this->runFields;
    }

    /**
     * The status mirror of the run (migrations/status/<environment>.json), written after a run that wrote.
     *
     * @return string|null the file written, null when there is none to write
     */
    public function writeStatus()
    {
        if ( $this->option( 'no-mirror' ) || expMigrationRecord::exists( $this->db() ) !== true )
            return null;
        return $this->mirror()->writeFromDatabase( $this->db(), $this->environment !== '' ? $this->environment : null );
    }

    /**
     * The run log, relative to the migrations repository (exp_migration_run.log_file), empty without one.
     *
     * @return string
     */
    public function logFile()
    {
        return $this->option( 'no-mirror' ) ? '' : expMigrationStatusMirror::logPath( $this->runId );
    }

    /**
     * One line in the run log (migrations/runs/<run-id>.log, cleaned of secrets) and in var/log/migration.log.
     * Never throws.
     *
     * @param string $level debug, info, notice, warning, error
     * @param string $text
     */
    public function log( $level, $text )
    {
        try
        {
            eZLog::write( $this->runId . ' ' . $level . ' ' . expMigrationStatusMirror::clean( (string)$text ), 'migration.log' );
            if ( !$this->option( 'no-mirror' ) )
                $this->mirror()->log( $this->runId, $level, $text );
        }
        catch ( Throwable $e )
        {
        }
    }

    /** @return expMigrationStatusMirror */
    protected function mirror()
    {
        if ( !$this->mirror )
            $this->mirror = new expMigrationStatusMirror( $this->option( 'repository' ) );
        return $this->mirror;
    }

    // ---- lock, maintenance, transactions --------------------------------------------------------------------

    /**
     * The installation lock, shared with exp:upgrade.
     *
     * @return expMigrationLock
     */
    public function lock()
    {
        if ( !$this->lock )
            $this->lock = expMigrationLock::installation( $this->option( 'lock-file' ) );
        return $this->lock;
    }

    /**
     * Switches maintenance mode on for a migration that asks for it (expMaintenance::beginRun()).
     *
     * @return string|false 'enabled' (this run switched it on), 'adopted', 'existing' (it was on and stays), false
     */
    public function maintenanceBegin()
    {
        return expMaintenance::beginRun( $this->root(), $this->runId, array( 'reason' => 'migration', 'message' => '' ) );
    }

    /**
     * Switches maintenance mode off again, only when this run switched it on.
     *
     * @return bool
     */
    public function maintenanceEnd()
    {
        return expMaintenance::disable( $this->root(), $this->runId );
    }

    /** @return string the installation's root */
    public function root()
    {
        $root = $this->option( 'root' );
        return $root !== null && $root !== '' ? (string)$root : rtrim( (string)eZSys::rootDir(), '/' );
    }

    /**
     * How much a transaction protects on this engine.
     *
     * @return string TRANSACTION_FULL, TRANSACTION_DATA or TRANSACTION_NONE
     */
    public function transactionMode()
    {
        return self::transactionModeOf( $this->engine() );
    }

    /**
     * @param string $engine
     * @return string
     */
    public static function transactionModeOf( $engine )
    {
        switch ( $engine )
        {
            case 'postgresql':
            case 'sqlite':
                return self::TRANSACTION_FULL;
            case 'mysql':
            case 'oracle':
                return self::TRANSACTION_DATA;
            default:
                return self::TRANSACTION_NONE;
        }
    }

    /**
     * Starts the transaction of a migration; a failing statement throws (eZDB::ERROR_HANDLING_EXCEPTIONS) instead of
     * ending the process, until commit() or rollback().
     */
    public function begin()
    {
        $this->exceptions( true );
        $this->db()->begin();
    }

    /** Commits the transaction of a migration. */
    public function commit()
    {
        try
        {
            $this->db()->commit();
        }
        finally
        {
            $this->exceptions( false );
        }
    }

    /** Rolls the transaction of a migration back (what the engine can roll back). */
    public function rollback()
    {
        try
        {
            if ( $this->db()->transactionCounter() > 0 )
                $this->db()->rollback();
        }
        finally
        {
            $this->exceptions( false );
            $this->records = null;
            expKernelTableSchema::reset();
        }
    }

    /**
     * Statement errors as exceptions (on), or the connection's handling as it was (off).
     *
     * @param bool $on
     */
    protected function exceptions( $on )
    {
        $db = $this->db();
        if ( !method_exists( $db, 'setErrorHandling' ) )
            return;
        if ( $on )
        {
            if ( $this->previousErrorHandling === null )
            {
                $read = function () {
                    return isset( $this->errorHandling ) ? $this->errorHandling : eZDB::ERROR_HANDLING_STANDARD;
                };
                $this->previousErrorHandling = (int)Closure::bind( $read, $db, get_class( $db ) )();
            }
            $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        }
        else if ( $this->previousErrorHandling !== null )
        {
            $db->setErrorHandling( $this->previousErrorHandling );
            $this->previousErrorHandling = null;
        }
    }

    // ---- events, audit, user -------------------------------------------------------------------------------

    /**
     * Notifies the listeners of an ezpEvent (migration/run/before ...). A listener that throws does not stop the run.
     *
     * @param string $name
     * @param array $params
     */
    public function notify( $name, array $params )
    {
        try
        {
            ezpEvent::getInstance()->notify( $name, $params );
        }
        catch ( Throwable $e )
        {
            $this->log( 'warning', "a listener of $name failed: " . $e->getMessage() );
        }
    }

    /**
     * An audit event (system.migration.*); never stops the run.
     *
     * @param string $name
     * @param array $data
     */
    public function audit( $name, array $data )
    {
        expUpgrade::audit( $name, $data );
    }

    /**
     * Makes the run's user the current user of the process for the run (content created belongs to it, policies are
     * its), without a new session.
     */
    public function becomeUser()
    {
        $user = $this->user();
        if ( !$user || $this->previousUser !== null )
            return;
        $current = eZUser::currentUser();
        $this->previousUser = array( 'user' => $current, 'id' => $current ? (int)$current->attribute( 'contentobject_id' ) : 0 );
        if ( $current && (int)$current->attribute( 'contentobject_id' ) === (int)$user->attribute( 'contentobject_id' ) )
            return;
        eZUser::setCurrentlyLoggedInUser( $user, (int)$user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    /** The current user as it was before becomeUser(). */
    public function restoreUser()
    {
        if ( $this->previousUser === null )
            return;
        $previous = $this->previousUser;
        $this->previousUser = null;
        $user = $this->user();
        if ( $previous['user'] && $user && $previous['id'] !== (int)$user->attribute( 'contentobject_id' ) )
            eZUser::setCurrentlyLoggedInUser( $previous['user'], $previous['id'], eZUser::NO_SESSION_REGENERATE );
    }

    /** @return bool */
    protected static function isWebRequest()
    {
        return class_exists( 'eZExecution' ) && method_exists( 'eZExecution', 'isWebRequest' ) ? eZExecution::isWebRequest() : PHP_SAPI !== 'cli';
    }
}
