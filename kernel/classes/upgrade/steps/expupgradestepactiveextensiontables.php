<?php
/**
 * File containing the expUpgradeStepActiveExtensionTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The tables of every active extension: an extension switched on globally (site.ini ActiveExtensions[]) or in a
 * siteaccess (ActiveAccessExtensions[]) that ships a share/db_schema.dba gets the tables of that file the database
 * lacks. Nothing is dropped or changed and no data is loaded (a share/db_data.dba holds a site's seed, e.g. the
 * layouts of a demo, and stays with the site package that knows it).
 *
 * This is the same as a new installation does after the site package (eZStepCreateSites, through
 * expInstallDefaultExtensions::ensureExtensionTables()), for installations made before it, or by site packages that
 * copied an extension's files from an extension package and created its tables from that package's copy: ezstarrating
 * (ezstarrating, ezstarrating_data) and ezgmaplocation (ezgmaplocation) come from Composer since 6.0.15 and no package
 * carries them any more. The SQL is written by each engine's schema handler (MySQL/MariaDB, PostgreSQL, SQLite,
 * Oracle through ezoracle, MongoDB collections).
 *
 * Options of the context, for tests: extensions (the list to look at, in place of the active ones) and
 * extension_root (the extension directory, in place of eZExtension::baseDirectory()).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepActiveExtensionTables extends expUpgradeBaseStep
{
    public function id()
    {
        return '6.0.15-active-extension-tables';
    }

    public function stage()
    {
        return 'database';
    }

    public function description()
    {
        return 'The tables of every active extension that ships a share/db_schema.dba (ezstarrating, ezgmaplocation and others), where missing';
    }

    /**
     * The active extensions: global ones, then the access extensions of every siteaccess, each once.
     *
     * @return string[]
     */
    public static function activeExtensions()
    {
        $list = (array)eZExtension::activeExtensions();
        foreach ( (array)glob( 'settings/siteaccess/*', GLOB_ONLYDIR ) as $dir )
        {
            foreach ( array( 'site.ini.append.php', 'site.ini.append' ) as $file )
            {
                if ( !is_file( "$dir/$file" ) )
                    continue;
                $ini = new eZINI( $file, $dir, null, null, null, true, true );
                if ( $ini->hasVariable( 'ExtensionSettings', 'ActiveAccessExtensions' ) )
                    $list = array_merge( $list, (array)$ini->variable( 'ExtensionSettings', 'ActiveAccessExtensions' ) );
            }
        }
        $out = array();
        foreach ( $list as $extension )
        {
            if ( is_string( $extension ) && preg_match( '/^[A-Za-z0-9_.-]+$/', $extension ) && !in_array( $extension, $out, true ) )
                $out[] = $extension;
        }
        return $out;
    }

    /**
     * The extensions with a share/db_schema.dba and the tables each declares: extension => name => definition.
     *
     * @param expUpgradeContext $context
     * @return array
     */
    public function schemas( expUpgradeContext $context )
    {
        $root = rtrim( (string)$context->option( 'extension_root', eZExtension::baseDirectory() ), '/' );
        $extensions = $context->option( 'extensions', null );
        $extensions = is_array( $extensions ) ? $extensions : self::activeExtensions();
        $out = array();
        foreach ( $extensions as $extension )
        {
            $file = "$root/$extension/share/db_schema.dba";
            if ( !is_file( $file ) )
                continue;
            if ( $file[0] !== '/' )
                $file = rtrim( eZSys::rootDir(), '/' ) . '/' . $file;
            $schema = expKernelTableSchema::read( $file );
            if ( $schema )
                $out[$extension] = $schema;
        }
        return $out;
    }

    /**
     * What is missing: array( 'tables' => extension => [table], 'unknown' => [table], 'extensions' => count ).
     *
     * @param expUpgradeContext $context
     * @return array
     */
    public function missing( expUpgradeContext $context )
    {
        $db = $context->db();
        $schemas = $this->schemas( $context );
        $result = array( 'tables' => array(), 'unknown' => array(), 'extensions' => count( $schemas ), 'schemas' => $schemas );
        $seen = array();
        foreach ( $schemas as $extension => $schema )
        {
            foreach ( array_keys( $schema ) as $table )
            {
                // a table two extensions declare (one bundles the other's schema) is created once, by the first
                if ( isset( $seen[$table] ) )
                    continue;
                $seen[$table] = true;
                $exists = expKernelTableSchema::exists( $table, $db, true );
                if ( $exists === null )
                    $result['unknown'][] = $table;
                else if ( !$exists )
                    $result['tables'][$extension][] = $table;
            }
        }
        return $result;
    }

    public function detect( expUpgradeContext $context )
    {
        $missing = $this->missing( $context );
        if ( !$missing['extensions'] )
            return self::effect( self::NOT_APPLICABLE, 'no active extension ships a share/db_schema.dba' );
        if ( $missing['tables'] )
        {
            $parts = array();
            foreach ( $missing['tables'] as $extension => $tables )
                $parts[] = "$extension (" . implode( ', ', $tables ) . ')';
            return self::effect( self::MISSING, 'missing: ' . implode( ', ', $parts ) );
        }
        if ( $missing['unknown'] )
            return self::effect( self::UNKNOWN, 'cannot tell: ' . implode( ', ', $missing['unknown'] ) );
        return self::effect( self::PRESENT, 'every table of the ' . $missing['extensions'] . ' active extensions with a share/db_schema.dba is there' );
    }

    public function plan( expUpgradeContext $context )
    {
        $missing = $this->missing( $context );
        $engine = $context->engine();
        $lines = array();
        foreach ( $missing['tables'] as $extension => $tables )
        {
            $schema = array_intersect_key( $missing['schemas'][$extension], array_flip( $tables ) );
            $sql = $engine === 'mongodb' ? '' : expKernelTableSchema::sql( $schema, $engine );
            $lines[] = "create table " . implode( ', ', $tables ) . " ($extension)" . ( $sql !== '' ? ': ' . preg_replace( '/\s+/', ' ', trim( $sql ) ) : '' );
        }
        return $lines;
    }

    public function apply( expUpgradeContext $context )
    {
        $db = $context->db();
        $missing = $this->missing( $context );
        if ( $missing['unknown'] )
            throw new RuntimeException( 'cannot tell whether these tables exist: ' . implode( ', ', $missing['unknown'] ) );
        $done = array();
        foreach ( $missing['tables'] as $extension => $tables )
        {
            $messages = array();
            $schema = array_intersect_key( $missing['schemas'][$extension], array_flip( $tables ) );
            if ( !expKernelTableSchema::install( $tables, $db, $schema, $messages ) )
                throw new RuntimeException( "$extension: " . implode( '; ', $messages ) );
            foreach ( $tables as $table )
                $done[] = "created table $table ($extension)";
        }
        expKernelTableSchema::reset();
        return $done;
    }
}
