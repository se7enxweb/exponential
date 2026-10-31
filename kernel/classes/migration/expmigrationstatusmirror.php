<?php
/**
 * File containing the expMigrationStatusMirror class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The migration record mirrored into the migrations repository, so the repository says what each environment has
 * applied without access to its database:
 *
 *   migrations/status/<environment>.json   the rows of exp_migration and the latest runs (schema exp-migration-status/1,
 *                                          share/migration/schema/exp-migration-status-1.json)
 *   migrations/runs/<run-id>.log           one line per event of a run: "<UTC time> <level> <text>"
 *
 * Nothing in either file is a secret or personal data: the status leaves out who ran a migration (run_by) and every
 * message; a log line is cleaned (clean()) of secret values, credentials in URLs, "password=..." pairs, bearer
 * tokens and e-mail addresses before it is written. The status file is written whole and atomically, sorted, with
 * times from the record only (no "now"), so writing it again without a change gives the same bytes and no diff.
 *
 *   $mirror = new expMigrationStatusMirror();                 // <root>/migrations
 *   $mirror->writeFromDatabase( eZDB::instance() );          // status/<environment>.json
 *   $mirror->log( $runId, 'info', 'applied 20261031120000_add_news_class in 412 ms' );
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStatusMirror
{
    const SCHEMA = 'exp-migration-status/1';

    const SCHEMA_FILE = 'share/migration/schema/exp-migration-status-1.json';

    /** The environment of an installation that names none. */
    const DEFAULT_ENVIRONMENT = 'default';

    /** The most runs a status file lists (the newest). */
    const MAX_RUNS = 50;

    /** The levels of a log line. */
    const LEVELS = array( 'debug', 'info', 'notice', 'warning', 'error' );

    /** @var string */
    protected $directory;

    /**
     * @param string|null $directory the migrations repository (<root>/migrations when null)
     */
    public function __construct( $directory = null )
    {
        if ( $directory === null )
            $directory = rtrim( (string)eZSys::rootDir(), '/' ) . '/migrations';
        $this->directory = rtrim( (string)$directory, '/' );
    }

    /** @return string */
    public function directory()
    {
        return $this->directory;
    }

    /**
     * The status file of an environment.
     *
     * @param string $environment
     * @return string
     * @throws InvalidArgumentException for a name that is no environment
     */
    public function statusFile( $environment )
    {
        return $this->directory . '/status/' . self::environmentName( $environment ) . '.json';
    }

    /**
     * The log file of a run.
     *
     * @param string $runId
     * @return string
     * @throws InvalidArgumentException for a run id that cannot name a file
     */
    public function logFile( $runId )
    {
        if ( !expMigrationRecord::validRunId( $runId ) )
            throw new InvalidArgumentException( 'not a run id: ' . self::printable( $runId ) );
        return $this->directory . '/runs/' . $runId . '.log';
    }

    /**
     * The log file of a run relative to the repository (what exp_migration_run.log_file holds).
     *
     * @param string $runId
     * @return string
     */
    public static function logPath( $runId )
    {
        if ( !expMigrationRecord::validRunId( $runId ) )
            throw new InvalidArgumentException( 'not a run id: ' . self::printable( $runId ) );
        return 'runs/' . $runId . '.log';
    }

    /**
     * Writes the status file of an environment from the record of a connection.
     *
     * @param eZDBInterface $db
     * @param string|null $environment the installation's (expMigrationRecord::environment()) when null
     * @return string the file written
     * @throws RuntimeException when the file cannot be written
     */
    public function writeFromDatabase( $db, $environment = null )
    {
        if ( $environment === null )
            $environment = expMigrationRecord::environment();
        return $this->write( $environment, expMigrationRecord::all( $db ), expMigrationRecord::runs( $db, self::MAX_RUNS ),
                             expKernelTableSchema::type( $db ) );
    }

    /**
     * Writes the status file of an environment.
     *
     * @param string $environment
     * @param array[] $rows rows of exp_migration
     * @param array[] $runs rows of exp_migration_run
     * @param string $engine
     * @return string the file written
     * @throws RuntimeException when the file cannot be written
     */
    public function write( $environment, array $rows, array $runs, $engine )
    {
        $file = $this->statusFile( $environment );
        $document = self::document( $environment, $rows, $runs, $engine );
        $errors = self::validate( $document );
        if ( $errors )
            throw new RuntimeException( 'the status would not match ' . self::SCHEMA . ': ' . implode( '; ', array_slice( $errors, 0, 5 ) ) );
        if ( !eZFile::create( basename( $file ), dirname( $file ), self::encode( $document ), true ) || !is_file( $file ) )
            throw new RuntimeException( 'cannot write ' . $file );
        return $file;
    }

    /**
     * The status file of an environment, decoded; null when there is none or it is not valid.
     *
     * @param string $environment
     * @return array|null
     */
    public function read( $environment )
    {
        $file = $this->statusFile( $environment );
        if ( !is_readable( $file ) )
            return null;
        $document = json_decode( (string)file_get_contents( $file ), true );
        return is_array( $document ) && !self::validate( $document ) ? $document : null;
    }

    /**
     * Adds a line to the log of a run (the file and its directory are made when missing).
     *
     * @param string $runId
     * @param string $level debug, info, notice, warning, error
     * @param string $text
     * @param string[] $secretNames names of the secret store whose values are masked (expSecrets::mask())
     * @param string[] $secretValues values masked as they are
     * @param int|null $time
     * @return string the line written, without its newline
     */
    public function log( $runId, $level, $text, array $secretNames = array(), array $secretValues = array(), $time = null )
    {
        $file = $this->logFile( $runId );
        $line = self::logLine( $level, $text, $secretNames, $secretValues, $time );
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        if ( file_put_contents( $file, $line . "\n", FILE_APPEND | LOCK_EX ) === false )
            throw new RuntimeException( 'cannot write ' . $file );
        return $line;
    }

    /**
     * One log line: "<UTC time> <level> <text>", the text cleaned and on one line.
     *
     * @param string $level
     * @param string $text
     * @param string[] $secretNames
     * @param string[] $secretValues
     * @param int|null $time
     * @return string
     */
    public static function logLine( $level, $text, array $secretNames = array(), array $secretValues = array(), $time = null )
    {
        $level = in_array( $level, self::LEVELS, true ) ? $level : 'info';
        $text = self::clean( (string)$text, $secretNames, $secretValues );
        $text = trim( preg_replace( '/[\x00-\x08\x0B-\x1F\x7F]/', ' ', str_replace( array( "\r\n", "\n", "\r" ), ' | ', $text ) ) );
        return self::time( $time === null ? time() : (int)$time ) . ' ' . $level . ' ' . $text;
    }

    /**
     * A text without secrets and personal data: the values of the named secrets and the given values, credentials in
     * URLs, "password=...", "token: ..." and the like, bearer tokens, and e-mail addresses.
     *
     * @param string $text
     * @param string[] $secretNames
     * @param string[] $secretValues
     * @return string
     */
    public static function clean( $text, array $secretNames = array(), array $secretValues = array() )
    {
        $text = (string)$text;
        if ( $secretNames && class_exists( 'expSecrets' ) )
        {
            try
            {
                $text = expSecrets::mask( $text, $secretNames );
            }
            catch ( Throwable $e )
            {
            }
        }
        foreach ( $secretValues as $value )
            if ( is_string( $value ) && strlen( $value ) >= 3 )
                $text = str_replace( array( $value, rawurlencode( $value ) ), '[secret]', $text );
        $text = preg_replace( '#\b([a-z][a-z0-9+.-]*://)[^/\s:@]+:[^/\s@]*@#i', '$1[credentials]@', $text );
        $text = preg_replace( '/\bBearer\s+[A-Za-z0-9._~+\/=-]+/i', 'Bearer [secret]', $text );
        $text = preg_replace( '/\b(password|passwd|pwd|secret|token|api[_-]?key|client[_-]?secret|authorization)(\s*[=:]\s*)(?!Bearer\b|\[secret)("[^"]*"|\'[^\']*\'|[^\s,;&"\']+)/i',
                              '$1$2[secret]', $text );
        $text = preg_replace( '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[e-mail]', $text );
        return $text;
    }

    /**
     * The status document (exp-migration-status/1). Pure: the same rows give the same document.
     *
     * @param string $environment
     * @param array[] $rows rows of exp_migration (caption rows of exp:upgrade are left out)
     * @param array[] $runs rows of exp_migration_run
     * @param string $engine
     * @return array
     */
    public static function document( $environment, array $rows, array $runs, $engine )
    {
        $migrations = array();
        $summary = array( 'total' => 0 );
        $latest = 0;
        foreach ( $rows as $row )
        {
            $row = expMigrationRecord::normalize( array_change_key_case( (array)$row, CASE_LOWER ), expMigrationRecord::COLUMNS );
            if ( $row['migration_id'] === '' || $row['source'] === '' || $row['stage'] === 'caption' )
                continue;
            $state = self::state( $row['state'] );
            $migrations[$row['source'] . "\n" . $row['migration_id']] = array(
                'source' => $row['source'],
                'id' => $row['migration_id'],
                'state' => $state,
                'file' => $row['file_path'],
                'checksum' => preg_match( '/^[0-9a-f]{64}$/', $row['checksum'] ) ? $row['checksum'] : '',
                'stage' => $row['stage'],
                'version' => $row['version'],
                'run_id' => $row['run_id'],
                'started_at' => self::time( $row['started_at'] ),
                'finished_at' => self::time( $row['finished_at'] ),
                'duration_ms' => $row['duration_ms'],
                'caption_id' => $row['caption_id'],
                'revision' => $row['revision'],
            );
            $summary['total']++;
            $summary[$state] = isset( $summary[$state] ) ? $summary[$state] + 1 : 1;
            $latest = max( $latest, $row['finished_at'] );
        }
        ksort( $migrations, SORT_STRING );
        ksort( $summary, SORT_STRING );

        $list = array();
        foreach ( $runs as $run )
        {
            $run = expMigrationRecord::normalize( array_change_key_case( (array)$run, CASE_LOWER ), expMigrationRecord::RUN_COLUMNS );
            if ( !expMigrationRecord::validRunId( $run['run_id'] ) )
                continue;
            $list[] = array(
                'run_id' => $run['run_id'],
                'state' => self::state( $run['state'] ),
                'started_at' => self::time( $run['started_at'] ),
                'finished_at' => self::time( $run['finished_at'] ),
                'caption_pre' => $run['caption_pre'],
                'caption_post' => $run['caption_post'],
                'commit' => $run['commit_id'],
                'tag' => $run['tag_name'],
                'push_state' => $run['push_state'],
                'log' => $run['log_file'],
            );
            $latest = max( $latest, $run['finished_at'], $run['started_at'] );
        }
        usort( $list, function ( $a, $b ) {
            return strcmp( (string)$b['started_at'], (string)$a['started_at'] ) ?: strcmp( $b['run_id'], $a['run_id'] );
        } );

        return array(
            'schema' => self::SCHEMA,
            'environment' => self::environmentName( $environment ),
            'engine' => preg_match( '/^[a-z0-9_]{0,30}$/', (string)$engine ) ? (string)$engine : '',
            'updated_at' => self::time( $latest ),
            'summary' => $summary,
            'migrations' => array_values( $migrations ),
            'runs' => array_slice( $list, 0, self::MAX_RUNS ),
        );
    }

    /**
     * The JSON text of a status document: pretty, slashes as they are, a newline at the end.
     *
     * @param array $document
     * @return string
     */
    public static function encode( array $document )
    {
        return json_encode( $document, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
    }

    /**
     * Checks a decoded status document against share/migration/schema/exp-migration-status-1.json.
     *
     * @param array $document
     * @return string[] the errors; empty when it is valid
     */
    public static function validate( array $document )
    {
        static $schema = null;
        if ( $schema === null )
        {
            $file = rtrim( (string)eZSys::rootDir(), '/' ) . '/' . self::SCHEMA_FILE;
            $schema = is_readable( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : false;
        }
        if ( !is_array( $schema ) )
            return array( 'the schema ' . self::SCHEMA_FILE . ' cannot be read' );
        return expJsonSchemaValidator::validate( $document, $schema );
    }

    /**
     * The name of an environment as files use it: lower case; empty is "default".
     *
     * @param string $environment
     * @return string
     * @throws InvalidArgumentException for a name that is no environment
     */
    public static function environmentName( $environment )
    {
        $name = strtolower( trim( (string)$environment ) );
        if ( $name === '' )
            return self::DEFAULT_ENVIRONMENT;
        if ( !preg_match( '/^[a-z0-9][a-z0-9_.-]{0,49}$/', $name ) )
            throw new InvalidArgumentException( 'not an environment name: ' . self::printable( $environment ) );
        return $name;
    }

    /**
     * A timestamp as UTC ISO 8601, null for 0.
     *
     * @param int $timestamp
     * @return string|null
     */
    public static function time( $timestamp )
    {
        $timestamp = (int)$timestamp;
        return $timestamp > 0 ? gmdate( 'Y-m-d\TH:i:s\Z', $timestamp ) : null;
    }

    /** @return string a state as the schema allows it ("unknown" otherwise) */
    protected static function state( $state )
    {
        $state = strtolower( (string)$state );
        return preg_match( '/^[a-z][a-z-]{0,19}$/', $state ) ? $state : 'unknown';
    }

    /** @return string a value for an error message: printable, short */
    protected static function printable( $value )
    {
        return mb_substr( preg_replace( '/[^\x20-\x7E]/', '?', is_scalar( $value ) ? (string)$value : gettype( $value ) ), 0, 80 );
    }
}
