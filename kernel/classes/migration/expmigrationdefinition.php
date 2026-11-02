<?php
/**
 * File containing the expMigrationDefinition class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A migration file as found on disk: its name, its header and its checksum. Nothing is run or loaded here.
 *
 * File names: YYYYMMDDHHMMSS_name.<type>, where the type is php, yml, sql, ini, <engine>.sql (mysql, postgresql or
 * its short name pgsql, sqlite, oracle) or mongodb.json (the MongoDB file of an SQL migration). The id is the file
 * name without the type. Files of one id that differ only in the engine (20261031120000_index.mysql.sql,
 * 20261031120000_index.pgsql.sql, 20261031120000_index.mongodb.json and a portable 20261031120000_index.sql) are one
 * migration of the type sql with a file per engine. A portable .sql file is for the SQL engines: without a MongoDB
 * file (or "engines" in the header) the migration does not run on MongoDB.
 *
 * The header is the comment block at the top of the file, one "key: value" per line, in the comment syntax of the
 * file (# in YAML and INI, -- in SQL, //, # or a /** ... *\/ block in PHP):
 *
 *   -- description: An index for the news list
 *   -- requires: 20261030090000_news_class, extension:expnews
 *   -- environments: staging, production
 *   -- engines: mysql, postgresql
 *   -- maintenance: no
 *   -- author: Jane Doe
 *
 * Keys: id (when given it must equal the file name's id), description, environments, requires, engines (lists,
 * comma-separated, may repeat), maintenance (yes/no), author, transactional (yes/no), events (each, summary, none).
 * Lines without a lowercase "key:" are prose and ignored; an unknown key is an error, so a misspelled key does not
 * pass silently. The header ends at the first line that is not a comment (a PHP file's "<?php", an INI file's
 * "#?ini ...?" line and a YAML "---" come before it).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationDefinition
{
    /** The engines a migration file can name. */
    const ENGINES = array( 'mysql', 'postgresql', 'sqlite', 'oracle', 'mongodb' );

    /** The file types. */
    const TYPES = array( 'php', 'yml', 'sql', 'ini' );

    /** The engines with SQL: what a portable .sql file runs on. */
    const SQL_ENGINES = array( 'mysql', 'postgresql', 'sqlite', 'oracle' );

    /** Short names of engines in file names. */
    const ENGINE_ALIASES = array( 'pgsql' => 'postgresql' );

    /** The header keys; the lists may repeat. */
    const KEYS = array( 'id', 'description', 'environments', 'requires', 'engines', 'maintenance', 'author', 'transactional', 'events' );

    /** The values of the header key events: what the content changes of the migration put into the event journal. */
    const EVENTS = array( 'each', 'summary', 'none' );
    const LIST_KEYS = array( 'environments', 'requires', 'engines' );

    /** How much of a file is read for the header. */
    const HEADER_BYTES = 16384;

    /** @var string */
    protected $id;
    /** @var string */
    protected $timestamp;
    /** @var string */
    protected $name;
    /** @var string */
    protected $type;
    /** @var string */
    protected $source;
    /** @var array engine or '*' => path */
    protected $files = array();
    /** @var array engine or '*' => SHA-256 */
    protected $checksums = array();
    /** @var array key => value of the header that counts (the portable file's, else the first engine file's) */
    protected $header = array();
    /** @var string[] */
    protected $errors = array();

    /**
     * @param string $id
     * @param string $source kernel, site, extension:<name>, package:<name>
     * @param string $type php, yml, sql, ini
     */
    public function __construct( $id, $source, $type )
    {
        $this->id = (string)$id;
        $this->source = (string)$source;
        $this->type = (string)$type;
        $this->timestamp = substr( $this->id, 0, 14 );
        $this->name = (string)substr( $this->id, 15 );
    }

    /**
     * The parts of a migration file name, or null when the name is not one (a README, a .gitkeep). A name that starts
     * like a migration (14 digits and "_") but is not valid gives an array with 'error'.
     *
     * @param string $basename
     * @return array|null array( id, timestamp, name, type, engine ('*' for every engine) ) or array( error )
     */
    public static function parseName( $basename )
    {
        $basename = (string)$basename;
        if ( !preg_match( '/^\d{14}_/', $basename ) )
            return null;
        if ( !preg_match( '/^((\d{14})_([A-Za-z0-9][A-Za-z0-9_-]*))\.(?:([a-z0-9]+)\.)?([a-z]+)$/', $basename, $m ) )
            return array( 'error' => "$basename is not a migration file name (YYYYMMDDHHMMSS_name.type, the name of letters, digits, _ and -)" );
        list( , $id, $timestamp, $name, $engine, $type ) = $m;
        if ( isset( self::ENGINE_ALIASES[$engine] ) )
            $engine = self::ENGINE_ALIASES[$engine];
        // the MongoDB file of an SQL migration: commands as JSON
        if ( $type === 'json' && $engine === 'mongodb' )
            $type = 'sql';
        else if ( $type === 'sql' && $engine === 'mongodb' )
            return array( 'error' => "$basename: MongoDB takes no SQL; its file is $id.mongodb.json" );
        else if ( !in_array( $type, self::TYPES, true ) )
            return array( 'error' => "$basename has the unknown type .$type (" . implode( ', ', self::TYPES ) . ', mongodb.json)' );
        if ( $engine !== '' && ( $type !== 'sql' || !in_array( $engine, self::ENGINES, true ) ) )
            return array( 'error' => "$basename: only SQL files name an engine: " . implode( ', ', self::SQL_ENGINES ) . ' (.sql) or mongodb (.json)' );
        if ( !self::validTimestamp( $timestamp ) )
            return array( 'error' => "$basename does not start with a valid date and time (YYYYMMDDHHMMSS)" );
        return array( 'id' => $id, 'timestamp' => $timestamp, 'name' => $name, 'type' => $type, 'engine' => $engine === '' ? '*' : $engine );
    }

    /**
     * @param string $timestamp YYYYMMDDHHMMSS
     * @return bool
     */
    public static function validTimestamp( $timestamp )
    {
        if ( !preg_match( '/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', (string)$timestamp, $m ) )
            return false;
        return (int)$m[1] >= 1970 && checkdate( (int)$m[2], (int)$m[3], (int)$m[1] )
            && (int)$m[4] < 24 && (int)$m[5] < 60 && (int)$m[6] < 60;
    }

    /**
     * Adds a file of the migration (the only one, or one engine's) and reads its header and checksum.
     *
     * @param string $path
     * @param string $engine '*' for the portable file
     * @return bool false when the file cannot be read (an error is recorded)
     */
    public function addFile( $path, $engine = '*' )
    {
        if ( isset( $this->files[$engine] ) )
        {
            $this->errors[] = "$path: the migration {$this->id} already has a file" . ( $engine === '*' ? '' : " for $engine" ) . ' (' . $this->files[$engine] . ')';
            return false;
        }
        $content = @file_get_contents( $path );
        if ( $content === false )
        {
            $this->errors[] = "$path cannot be read";
            return false;
        }
        $this->files[$engine] = $path;
        $this->checksums[$engine] = self::checksumOf( $content );
        ksort( $this->files );
        ksort( $this->checksums );

        $errors = array();
        $header = self::parseHeader( substr( $content, 0, self::HEADER_BYTES ), $errors );
        foreach ( $errors as $error )
            $this->errors[] = $path . ": $error";
        if ( isset( $header['id'] ) && $header['id'] !== $this->id )
            $this->errors[] = $path . ": the header names the id {$header['id']}, the file name {$this->id}";
        foreach ( isset( $header['engines'] ) ? $header['engines'] : array() as $name )
            if ( !in_array( $name, self::ENGINES, true ) )
                $this->errors[] = $path . ": the header names the unknown engine $name";
        // the portable file's header counts; else the first engine file's (ksort: '*' sorts first)
        if ( array_key_first( $this->files ) === $engine )
            $this->header = $header;
        return true;
    }

    /**
     * The header of a file's start. Pure.
     *
     * @param string $text the start of the file
     * @param string[] $errors problems are added here
     * @return array key => string, or string[] for the list keys; maintenance as bool
     */
    public static function parseHeader( $text, array &$errors = array() )
    {
        $header = array();
        $inBlock = false;
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$text ) as $raw )
        {
            $line = trim( $raw );
            if ( $inBlock )
            {
                $end = strpos( $line, '*/' );
                if ( $end !== false )
                {
                    $line = substr( $line, 0, $end );
                    $inBlock = false;
                }
                $line = ltrim( $line, '* ' );
            }
            else if ( $line === '' || $line === '<?php' || $line === '---' || strncmp( $line, '#?ini', 5 ) === 0 )
            {
                continue;
            }
            else if ( strncmp( $line, '/*', 2 ) === 0 )
            {
                $line = ltrim( substr( $line, 2 ), '*' );
                $end = strpos( $line, '*/' );
                if ( $end !== false )
                    $line = substr( $line, 0, $end );
                else
                    $inBlock = true;
            }
            else if ( preg_match( '/^(\/\/|#|--|;)(.*)$/', $line, $m ) )
            {
                $line = $m[2];
            }
            else
            {
                break;
            }
            self::headerLine( trim( $line ), $header, $errors );
        }
        foreach ( self::LIST_KEYS as $key )
            if ( isset( $header[$key] ) )
                $header[$key] = array_values( array_unique( $header[$key] ) );
        return $header;
    }

    /**
     * @param string $line
     * @param array $header
     * @param string[] $errors
     */
    protected static function headerLine( $line, array &$header, array &$errors )
    {
        if ( !preg_match( '/^([a-z][a-z-]*)\s*:\s*(.*)$/', $line, $m ) )
            return;
        $key = $m[1];
        $value = trim( $m[2] );
        if ( !in_array( $key, self::KEYS, true ) )
        {
            $errors[] = "unknown header key $key (" . implode( ', ', self::KEYS ) . ')';
            return;
        }
        if ( in_array( $key, self::LIST_KEYS, true ) )
        {
            if ( !isset( $header[$key] ) )
                $header[$key] = array();
            foreach ( explode( ',', trim( $value, '[] ' ) ) as $item )
                if ( ( $item = trim( $item, " \t'\"" ) ) !== '' )
                    $header[$key][] = $item;
            return;
        }
        if ( array_key_exists( $key, $header ) )
        {
            $errors[] = "the header key $key is given twice";
            return;
        }
        if ( $key === 'maintenance' || $key === 'transactional' )
        {
            $flag = strtolower( $value );
            if ( in_array( $flag, array( 'yes', 'true', 'on', '1', 'enabled' ), true ) )
                $header[$key] = true;
            else if ( in_array( $flag, array( 'no', 'false', 'off', '0', 'disabled', '' ), true ) )
                $header[$key] = false;
            else
                $errors[] = "$key: $value is not yes or no";
            return;
        }
        if ( $key === 'events' )
        {
            $mode = strtolower( $value );
            if ( !in_array( $mode, self::EVENTS, true ) )
                $errors[] = "events: $value is not " . implode( ', ', self::EVENTS );
            else
                $header[$key] = $mode;
            return;
        }
        $header[$key] = $value;
    }

    /**
     * The checksum of a migration file: SHA-256, hex, of its content with line endings normalized.
     *
     * Line endings are normalized to LF (CRLF and a lone CR become LF) before hashing, so the same migration checked
     * out with git's core.autocrlf on Windows and without it on Linux has the same checksum and an applied migration
     * is not reported as changed only because a checkout converted its line endings. Every other byte counts: a
     * changed space, a byte order mark or a different character encoding is a change.
     *
     * @param string $path
     * @return string|null null when the file cannot be read
     */
    public static function checksum( $path )
    {
        $content = @file_get_contents( $path );
        return $content === false ? null : self::checksumOf( $content );
    }

    /**
     * @param string $content
     * @return string
     */
    public static function checksumOf( $content )
    {
        return hash( 'sha256', strpos( $content, "\r" ) === false ? $content : str_replace( array( "\r\n", "\r" ), "\n", $content ) );
    }

    /** @return string */
    public function id()
    {
        return $this->id;
    }

    /** @return string YYYYMMDDHHMMSS */
    public function timestamp()
    {
        return $this->timestamp;
    }

    /** @return string the name part of the id */
    public function name()
    {
        return $this->name;
    }

    /** @return string php, yml, sql, ini */
    public function type()
    {
        return $this->type;
    }

    /** @return string kernel, site, extension:<name>, package:<name> */
    public function source()
    {
        return $this->source;
    }

    /** @return string */
    public function description()
    {
        return isset( $this->header['description'] ) ? $this->header['description'] : '';
    }

    /** @return string */
    public function author()
    {
        return isset( $this->header['author'] ) ? $this->header['author'] : '';
    }

    /** @return string[] empty for every environment */
    public function environments()
    {
        return isset( $this->header['environments'] ) ? $this->header['environments'] : array();
    }

    /** @return string[] */
    public function requires()
    {
        return isset( $this->header['requires'] ) ? $this->header['requires'] : array();
    }

    /** @return string[] the migration ids among requires() */
    public function requiredMigrations()
    {
        $out = array();
        foreach ( $this->requires() as $item )
            if ( strncmp( $item, 'extension:', 10 ) !== 0 )
                $out[] = $item;
        return $out;
    }

    /** @return string[] the extension names among requires() */
    public function requiredExtensions()
    {
        $out = array();
        foreach ( $this->requires() as $item )
            if ( strncmp( $item, 'extension:', 10 ) === 0 && trim( substr( $item, 10 ) ) !== '' )
                $out[] = trim( substr( $item, 10 ) );
        return $out;
    }

    /** @return bool */
    public function maintenance()
    {
        return !empty( $this->header['maintenance'] );
    }

    /** @return bool false when the header says "transactional: no" (no transaction around the migration) */
    public function transactional()
    {
        return !isset( $this->header['transactional'] ) || $this->header['transactional'];
    }

    /**
     * What the content changes of the migration put into the event journal of the GraphQL API (webhooks and
     * subscriptions): each (the default) every change as an event of its own, tagged with the migration; summary one
     * event at the end (a mutation event "migration" with the objects and nodes it changed); none nothing.
     *
     * @return string each, summary or none
     */
    public function events()
    {
        return isset( $this->header['events'] ) ? (string)$this->header['events'] : 'each';
    }

    /**
     * The engines the migration is for: the header's engines, narrowed to the engine files when there is no portable
     * file; array( '*' ) for every engine. A portable .sql file runs on the SQL engines, and on MongoDB only with a
     * .mongodb.json file next to it.
     *
     * @return string[]
     */
    public function engines()
    {
        $named = isset( $this->header['engines'] ) ? $this->header['engines'] : array();
        if ( isset( $this->files['*'] ) && $this->type === 'sql' && !$named )
            return isset( $this->files['mongodb'] ) ? array( '*' ) : self::SQL_ENGINES;
        if ( isset( $this->files['*'] ) || !$this->files )
            return $named ? $named : array( '*' );
        $variants = array_keys( $this->files );
        return $named ? array_values( array_intersect( $named, $variants ) ) : $variants;
    }

    /**
     * Whether the migration runs on an engine.
     *
     * @param string $engine
     * @return bool
     */
    public function supportsEngine( $engine )
    {
        $engines = $this->engines();
        return $engines === array( '*' ) || in_array( $engine, $engines, true );
    }

    /**
     * Whether the migration runs in an environment: no environments named, or the environment named and not
     * excluded with !name. An environment that is only excluded elsewhere counts as named.
     *
     * @param string $environment
     * @return bool
     */
    public function appliesTo( $environment )
    {
        $environment = (string)$environment;
        $include = array();
        foreach ( $this->environments() as $item )
        {
            if ( $item[0] === '!' )
            {
                if ( substr( $item, 1 ) === $environment )
                    return false;
            }
            else
            {
                $include[] = $item;
            }
        }
        return !$include || in_array( $environment, $include, true );
    }

    /** @return array engine ('*' for the portable file) => path */
    public function files()
    {
        return $this->files;
    }

    /**
     * The file that runs on an engine: its own, else the portable one; with no engine the portable one, else the first.
     *
     * @param string|null $engine
     * @return string|null
     */
    public function file( $engine = null )
    {
        if ( $engine !== null && isset( $this->files[$engine] ) )
            return $this->files[$engine];
        if ( isset( $this->files['*'] ) )
            return $this->files['*'];
        if ( $engine !== null )
            return null;
        return $this->files ? reset( $this->files ) : null;
    }

    /**
     * The checksum of the file that runs on an engine (see file()).
     *
     * @param string|null $engine
     * @return string|null
     */
    public function checksumFor( $engine = null )
    {
        $file = $this->file( $engine );
        if ( $file === null )
            return null;
        $key = array_search( $file, $this->files, true );
        return $this->checksums[$key];
    }

    /** @return array engine => SHA-256 of every file */
    public function checksums()
    {
        return $this->checksums;
    }

    /** @return array the parsed header */
    public function header()
    {
        return $this->header;
    }

    /** @return string[] problems in the files of the migration */
    public function errors()
    {
        return $this->errors;
    }
}
