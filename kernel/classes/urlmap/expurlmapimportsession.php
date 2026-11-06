<?php
/**
 * File containing the expUrlMapImportSession class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * An import of a URL map in the administration (content/urlimport) between its requests: the uploaded file, the
 * options chosen, the plan with the decisions of each row, the last dry run and the result of the import. Kept on the
 * server, never in the form: a request names it only by its token, and only the user who uploaded the file can open
 * it. Plans are re-read from here and the importer checks every entry again before it writes, so nothing a browser
 * posts can change what a row does beyond import or skip.
 *
 * Each import is a directory var/<site>/urlimport/<token>/ (meta.json, the source file, plan.json, result.json); a
 * token is 32 hexadecimal characters from random_bytes(). They expire after TTL seconds and are removed when the next
 * one is created.
 *
 *   $session = expUrlMapImportSession::create( $userId, $content, 'old-site.csv', 'csv', $options );
 *   $session = expUrlMapImportSession::load( $token, $userId );   // null: unknown, expired or someone else's
 *   $plan = $session->plan();
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapImportSession
{
    /** Seconds an import is kept */
    const TTL = 86400;

    /** The largest file accepted, in bytes */
    const MAX_SIZE = 10485760;

    /** @var string */
    protected $token;

    /** @var string */
    protected $dir;

    /** @var array */
    protected $meta;

    /**
     * @param string $token
     * @param string $dir
     * @param array $meta
     */
    final protected function __construct( $token, $dir, array $meta )
    {
        $this->token = $token;
        $this->dir = $dir;
        $this->meta = $meta;
    }

    /**
     * The directory of every import.
     *
     * @return string
     */
    public static function baseDirectory()
    {
        return eZSys::varDirectory() . '/urlimport';
    }

    /**
     * Whether a text is a token. Pure.
     *
     * @param mixed $token
     * @return bool
     */
    public static function isToken( $token )
    {
        return is_string( $token ) && preg_match( '/^[0-9a-f]{32}$/', $token ) === 1;
    }

    /**
     * Keeps an uploaded or pasted file for an import.
     *
     * @param int $userId who may open it
     * @param string $content the file
     * @param string $sourceName the file's name, as the user gave it
     * @param string $format the format (expUrlMapFormats)
     * @param array $options the planner's options: siteaccess, language, on_conflict, hosts
     * @return expUrlMapImportSession
     * @throws RuntimeException when it cannot be written
     */
    public static function create( $userId, $content, $sourceName, $format, array $options )
    {
        static::purgeExpired();
        $token = bin2hex( random_bytes( 16 ) );
        $dir = static::baseDirectory() . '/' . $token;
        if ( !eZDir::mkdir( $dir, false, true ) && !is_dir( $dir ) )
            throw new RuntimeException( 'The directory of the import cannot be created' );
        $definitions = expUrlMapFormats::definitions();
        $extension = isset( $definitions[$format] ) ? $definitions[$format]['extensions'][0] : 'txt';
        $meta = array( 'user_id' => (int)$userId, 'created' => time(), 'source_name' => mb_substr( (string)$sourceName, 0, 255 ),
                       'format' => (string)$format, 'file' => 'source.' . $extension, 'size' => strlen( (string)$content ),
                       'options' => self::cleanOptions( $options ) );
        $session = new static( $token, $dir, $meta );
        $session->write( $meta['file'], (string)$content );
        $session->write( 'meta.json', self::json( $meta ) );
        return $session;
    }

    /**
     * An import by its token, for the user who made it.
     *
     * @param mixed $token
     * @param int $userId
     * @return expUrlMapImportSession|null null when the token is unknown, expired or another user's
     */
    public static function load( $token, $userId )
    {
        if ( !self::isToken( $token ) )
            return null;
        $dir = static::baseDirectory() . '/' . $token;
        $meta = is_file( $dir . '/meta.json' ) ? json_decode( (string)file_get_contents( $dir . '/meta.json' ), true ) : null;
        if ( !is_array( $meta ) || (int)$meta['user_id'] !== (int)$userId || (int)$meta['created'] < time() - self::TTL )
            return null;
        return new static( $token, $dir, $meta );
    }

    /**
     * Removes the imports older than TTL.
     *
     * @return int how many were removed
     */
    public static function purgeExpired()
    {
        $base = static::baseDirectory();
        $removed = 0;
        foreach ( is_dir( $base ) ? (array)glob( $base . '/*', GLOB_ONLYDIR ) : array() as $dir )
        {
            if ( !self::isToken( basename( $dir ) ) )
                continue;
            $meta = is_file( $dir . '/meta.json' ) ? json_decode( (string)file_get_contents( $dir . '/meta.json' ), true ) : null;
            $created = is_array( $meta ) && isset( $meta['created'] ) ? (int)$meta['created'] : (int)@filemtime( $dir );
            if ( $created < time() - self::TTL && eZDir::recursiveDelete( $dir ) )
                $removed++;
        }
        return $removed;
    }

    /**
     * The planner's options of a form, checked. Pure.
     *
     * @param array $options
     * @return array siteaccess, language, on_conflict, hosts (host => siteaccess)
     */
    public static function cleanOptions( array $options )
    {
        $hosts = array();
        foreach ( isset( $options['hosts'] ) && is_array( $options['hosts'] ) ? $options['hosts'] : array() as $host => $siteaccess )
        {
            if ( !is_string( $host ) )
                continue;
            $host = strtolower( trim( $host ) );
            $siteaccess = trim( (string)$siteaccess );
            if ( preg_match( '/^[a-z0-9.\-]+(:\d+)?$/', $host ) && preg_match( '/^[A-Za-z0-9_\-]+$/', $siteaccess ) )
                $hosts[$host] = $siteaccess;
        }
        $onConflict = isset( $options['on_conflict'] ) && $options['on_conflict'] === expUrlMapPlan::ON_CONFLICT_OVERWRITE
                    ? expUrlMapPlan::ON_CONFLICT_OVERWRITE : expUrlMapPlan::ON_CONFLICT_SKIP;
        $siteaccess = isset( $options['siteaccess'] ) && preg_match( '/^[A-Za-z0-9_\-]+$/', (string)$options['siteaccess'] ) ? (string)$options['siteaccess'] : '';
        $language = isset( $options['language'] ) && preg_match( '/^[a-z]{3}-[A-Z]{2}(@[a-z]+)?$/', (string)$options['language'] ) ? (string)$options['language'] : '';
        return array( 'siteaccess' => $siteaccess, 'language' => $language, 'on_conflict' => $onConflict, 'hosts' => $hosts );
    }

    /**
     * The lines "old-host = siteaccess" of a text area as host => siteaccess; other lines are left out. Pure.
     *
     * @param string $text
     * @return array
     */
    public static function parseHosts( $text )
    {
        $hosts = array();
        foreach ( preg_split( '/[\r\n,]+/', (string)$text ) as $line )
        {
            if ( preg_match( '/^\s*(?:https?:\/\/)?([A-Za-z0-9.\-]+(?::\d+)?)\/?\s*=>?\s*([A-Za-z0-9_\-]+)\s*$/', $line, $m ) )
                $hosts[strtolower( $m[1] )] = $m[2];
        }
        return $hosts;
    }

    /** @return string */
    public function token()
    {
        return $this->token;
    }

    /** @return array user_id, created, source_name, format, file, size, options */
    public function meta()
    {
        return $this->meta;
    }

    /** @return array the planner's options */
    public function options()
    {
        return $this->meta['options'];
    }

    /** @return string the path of the kept file */
    public function sourcePath()
    {
        return $this->dir . '/' . $this->meta['file'];
    }

    /**
     * Plans the kept file (again) with these options and keeps the plan.
     *
     * @param array $options siteaccess, language, on_conflict, hosts (null: the kept ones)
     * @param expUrlMapPlanner|null $planner
     * @return expUrlMapPlan
     * @throws expUrlMapFormatException when the file cannot be read
     */
    public function makePlan( ?array $options = null, ?expUrlMapPlanner $planner = null )
    {
        if ( $options !== null )
        {
            $this->meta['options'] = self::cleanOptions( $options );
            $this->write( 'meta.json', self::json( $this->meta ) );
        }
        $rows = expUrlMapFormats::readFile( $this->sourcePath(), $this->meta['format'] );
        $planner = $planner ?: new expUrlMapPlanner();
        $plan = $planner->plan( $rows, $this->meta['options'] + array( 'source_name' => $this->meta['source_name'], 'format' => $this->meta['format'] ) );
        $this->savePlan( $plan );
        $this->forget( 'dryrun.json' );
        return $plan;
    }

    /**
     * Keeps a plan with its decisions; a dry run of an earlier plan no longer counts.
     *
     * @param expUrlMapPlan $plan
     */
    public function savePlan( expUrlMapPlan $plan )
    {
        $this->write( 'plan.json', self::json( array( 'items' => $plan->items(), 'options' => $plan->options() ) ) );
        $this->forget( 'dryrun.json' );
    }

    /**
     * The kept plan, with the decisions taken since.
     *
     * @return expUrlMapPlan|null
     */
    public function plan()
    {
        $data = $this->read( 'plan.json' );
        if ( !is_array( $data ) || !isset( $data['items'], $data['options'] ) || !is_array( $data['items'] ) )
            return null;
        $plan = new expUrlMapPlan( $data['items'], (array)$data['options'] );
        foreach ( $data['items'] as $index => $item )
        {
            if ( isset( $item['decision'] ) && $plan->item( $index ) && $plan->item( $index )['decision'] !== $item['decision'] )
                $plan->setDecision( $index, (string)$item['decision'] );
        }
        return $plan;
    }

    /**
     * Keeps a dry run's or an import's result.
     *
     * @param string $kind dryrun, result or test (exp:url:test's result after the import)
     * @param array $result expUrlMapImporter::apply()'s
     */
    public function saveResult( $kind, array $result )
    {
        $this->write( self::resultFile( $kind ), self::json( $result + array( 'time' => time() ) ) );
    }

    /**
     * @param string $kind dryrun, result or test (exp:url:test's result after the import)
     * @return array|null
     */
    public function result( $kind )
    {
        $data = $this->read( self::resultFile( $kind ) );
        return is_array( $data ) ? $data : null;
    }

    /**
     * The file of a result kind. Pure.
     *
     * @param string $kind
     * @return string
     */
    public static function resultFile( $kind )
    {
        return ( in_array( $kind, array( 'result', 'test' ), true ) ? $kind : 'dryrun' ) . '.json';
    }

    /**
     * The rows of the kept file, as its format reads them.
     *
     * @return expUrlMapRow[]
     * @throws expUrlMapFormatException
     */
    public function rows()
    {
        $meta = $this->meta();
        return expUrlMapFormats::readFile( $this->sourcePath(), isset( $meta['format'] ) && $meta['format'] !== '' ? (string)$meta['format'] : null );
    }

    /** Removes the import now (after it is done the plan is no longer needed). */
    public function remove()
    {
        eZDir::recursiveDelete( $this->dir );
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function read( $name )
    {
        $path = $this->dir . '/' . $name;
        return is_file( $path ) ? json_decode( (string)file_get_contents( $path ), true ) : null;
    }

    /**
     * @param string $name
     */
    protected function forget( $name )
    {
        if ( is_file( $this->dir . '/' . $name ) )
            @unlink( $this->dir . '/' . $name );
    }

    /**
     * Writes a file of the import (readable by the web server's user and Velocity's alike).
     *
     * @param string $name
     * @param string $content
     * @throws RuntimeException
     */
    protected function write( $name, $content )
    {
        $path = $this->dir . '/' . $name;
        if ( !eZFile::create( $name, $this->dir, $content, true ) )
            throw new RuntimeException( 'The import cannot be kept: ' . $name . ' cannot be written' );
        $permissions = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
        @chmod( $path, eZFile::fileMode( octdec( (string)$permissions ) ?: 0644 ) );
    }

    /**
     * @param array $data
     * @return string
     */
    protected static function json( array $data )
    {
        return (string)json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_PRETTY_PRINT );
    }
}
