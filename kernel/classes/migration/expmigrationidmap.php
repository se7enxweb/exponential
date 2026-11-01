<?php
/**
 * File containing the expMigrationIdMap class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The id map of an environment: names for the ids that differ between installations, so one migration works on the
 * development, staging and production installation of a site. A file per environment in the migrations repository:
 *
 *   migrations/idmap/<environment>.json        (default.json when the installation names no environment)
 *
 *   {
 *       "schema": "exp-migration-idmap/1",
 *       "ids": {
 *           "node":    { "news": 1234, "media": "remote:media-root" },
 *           "section": { "intranet": 6 }
 *       }
 *   }
 *
 * A YAML migration reads it as ${idmap.<kind>.<name>} (expMigrationReferences); a value may be a number or an
 * identifier text (remote:..., path:..., expMigrationIdentifier). Kinds and names are letters, digits, _ and -. A
 * missing file is an empty map; a file that is not one is an error of every YAML migration that runs with it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationIdMap
{
    /** The schema of the file. */
    const SCHEMA = 'exp-migration-idmap/1';

    /** A kind or a name. */
    const NAME_PATTERN = '/^[A-Za-z0-9_-]+$/';

    /** @var array path => array( 'ids' => ..., 'errors' => ... ), files read once per process */
    private static $cache = array();

    /**
     * The file of an environment.
     *
     * @param string $repository the migrations repository (a folder)
     * @param string $environment empty: default
     * @return string
     */
    public static function file( $repository, $environment )
    {
        return rtrim( (string)$repository, '/' ) . '/idmap/' . expMigrationStatusMirror::environmentName( $environment ) . '.json';
    }

    /**
     * The map of an environment; read once per process and file (forget() reads it again).
     *
     * @param string $repository
     * @param string $environment
     * @param string[] $errors problems are added here
     * @return array kind => array( name => value )
     */
    public static function load( $repository, $environment, array &$errors = array() )
    {
        try
        {
            $file = self::file( $repository, $environment );
        }
        catch ( InvalidArgumentException $e )
        {
            $errors[] = $e->getMessage();
            return array();
        }
        if ( !isset( self::$cache[$file] ) )
        {
            $fileErrors = array();
            $ids = array();
            if ( is_file( $file ) )
            {
                $content = @file_get_contents( $file );
                if ( $content === false )
                    $fileErrors[] = "$file cannot be read";
                else
                    $ids = self::parse( $content, $fileErrors );
                foreach ( $fileErrors as $i => $error )
                    $fileErrors[$i] = basename( dirname( $file ) ) . '/' . basename( $file ) . ": $error";
            }
            self::$cache[$file] = array( 'ids' => $ids, 'errors' => $fileErrors );
        }
        foreach ( self::$cache[$file]['errors'] as $error )
            $errors[] = $error;
        return self::$cache[$file]['ids'];
    }

    /** Forgets the files read. */
    public static function forget()
    {
        self::$cache = array();
    }

    /**
     * The ids of an id map file's content. Pure.
     *
     * @param string $json
     * @param string[] $errors
     * @return array kind => array( name => value ); empty with an error
     */
    public static function parse( $json, array &$errors = array() )
    {
        $data = json_decode( (string)$json, true );
        if ( !is_array( $data ) )
        {
            $errors[] = 'not JSON: ' . json_last_error_msg();
            return array();
        }
        if ( !isset( $data['schema'] ) || $data['schema'] !== self::SCHEMA )
        {
            $errors[] = 'the schema is not ' . self::SCHEMA;
            return array();
        }
        if ( !isset( $data['ids'] ) || !is_array( $data['ids'] ) )
        {
            $errors[] = 'no "ids" map';
            return array();
        }
        $ids = array();
        foreach ( $data['ids'] as $kind => $names )
        {
            if ( !preg_match( self::NAME_PATTERN, (string)$kind ) || !is_array( $names ) )
            {
                $errors[] = "ids.$kind: a kind (letters, digits, _ and -) with a map of names";
                continue;
            }
            foreach ( $names as $name => $value )
            {
                if ( !preg_match( self::NAME_PATTERN, (string)$name ) )
                    $errors[] = "ids.$kind.$name: a name is letters, digits, _ and -";
                else if ( !is_int( $value ) && !( is_string( $value ) && $value !== '' ) )
                    $errors[] = "ids.$kind.$name: a number or an identifier text";
                else
                    $ids[(string)$kind][(string)$name] = $value;
            }
        }
        return $errors ? array() : $ids;
    }

    /**
     * The content of an id map file. Pure.
     *
     * @param array $ids kind => array( name => value )
     * @return string
     */
    public static function encode( array $ids )
    {
        ksort( $ids );
        foreach ( $ids as $kind => $names )
            ksort( $ids[$kind] );
        return json_encode( array( 'schema' => self::SCHEMA, 'ids' => (object)$ids ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
    }
}
