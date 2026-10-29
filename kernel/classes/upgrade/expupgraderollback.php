<?php
/**
 * File containing the expUpgradeRollback class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Puts an installation back to a backup that exp:upgrade made (var/backup/upgrade/<time>/), part by part:
 *
 *   database   SQLite: the copy written back through SQLite's backup API; MySQL/MariaDB: database.sql through mysql;
 *              PostgreSQL: database.sql through psql; MongoDB: mongorestore --drop. Tables made after the backup
 *              (not in its manifest) are dropped. Oracle: restore with impdp.
 *   settings   settings/override copied back over the installation's (files added since stay)
 *   libraries  composer.json and composer.lock copied back, then composer install
 *   code       every checkout of code.json moved back to its commit (git reset --keep: refused over local changes)
 *
 * Never automatic and never without apply: run() without it only lists what it would do. Afterwards the changes
 * are not live until exp:upgrade:finish --apply (or exp:velocity deploy --kernel).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeRollback
{
    /** The parts, in the order they are restored (permanent). */
    const PARTS = array( 'database', 'settings', 'libraries', 'code' );

    /**
     * Restores (with apply) or lists what would be restored from a backup.
     *
     * @param string $name the backup's directory name (as exp:upgrade:backup and the report give it)
     * @param array $options apply, parts (string[]), backup-dir, db, root, command
     * @return array a report as expUpgrade::report() with 'rollback' => array( backup, parts => part => lines )
     */
    public static function run( $name, array $options = array() )
    {
        $apply = !empty( $options['apply'] );
        $report = expUpgrade::report( $apply ? 'rollback' : 'rollback-dry-run', $options );
        $report['rollback'] = array( 'backup' => (string)$name, 'parts' => array() );
        $root = rtrim( isset( $options['root'] ) ? $options['root'] : eZSys::rootDir(), '/' );
        $base = rtrim( !empty( $options['backup-dir'] ) ? $options['backup-dir'] : expUpgradeBackup::baseDir(), '/' );
        if ( !preg_match( '/^[0-9]{8}-[0-9]{6}(-[0-9a-z]+)?$/', (string)$name ) || !is_dir( "$base/$name" ) )
        {
            $report['errors'][] = "no backup $name in $base (./console exp:upgrade:rollback lists them)";
            $report['exit'] = expUpgrade::EXIT_USAGE;
            return $report;
        }
        $dir = "$base/$name";
        $manifest = is_file( "$dir/manifest.json" ) ? json_decode( (string)file_get_contents( "$dir/manifest.json" ), true ) : array();
        $parts = !empty( $options['parts'] ) ? expUpgrade::listOption( $options['parts'] ) : self::PARTS;
        foreach ( $parts as $p )
            if ( !in_array( $p, self::PARTS, true ) )
            {
                $report['errors'][] = "unknown part $p; parts: " . implode( ', ', self::PARTS );
                $report['exit'] = expUpgrade::EXIT_USAGE;
                return $report;
            }
        try
        {
            $db = isset( $options['db'] ) ? $options['db'] : eZDB::instance();
            if ( in_array( 'database', $parts, true ) && ( !$db || !$db->isConnected() ) )
            {
                $report['errors'][] = 'the database is not reachable';
                $report['exit'] = expUpgrade::EXIT_UNREACHABLE;
                return $report;
            }
            if ( $apply && !expUpgradeLock::acquire( isset( $options['lock-file'] ) ? $options['lock-file'] : null ) )
            {
                $report['errors'][] = 'another upgrade is running';
                $report['exit'] = expUpgrade::EXIT_FAILED;
                return $report;
            }
            foreach ( self::PARTS as $part )
            {
                if ( !in_array( $part, $parts, true ) )
                    continue;
                $lines = array();
                try
                {
                    $method = $part;
                    $lines = self::$method( $dir, $manifest, $root, $db, $apply, $options );
                }
                catch ( Throwable $e )
                {
                    $lines[] = 'FAILED: ' . $e->getMessage();
                    $report['errors'][] = "$part: " . $e->getMessage();
                    $report['exit'] = expUpgrade::EXIT_FAILED;
                }
                $report['rollback']['parts'][$part] = $lines;
                if ( $report['exit'] !== expUpgrade::EXIT_OK )
                    break;
            }
            if ( $apply )
            {
                expUpgradeLock::release();
                eZLog::write( "rollback to $name: " . ( $report['exit'] === 0 ? 'done' : 'failed' ) . ' (' . implode( ', ', array_keys( $report['rollback']['parts'] ) ) . ')', 'upgrade.log' );
                expUpgrade::audit( 'system.upgrade.rollback', array( 'object' => array( 'type' => 'backup', 'id' => $name ),
                                                                      'result' => $report['exit'] === 0 ? 'success' : 'failure',
                                                                      'x' => array( 'parts' => array_keys( $report['rollback']['parts'] ) ) ) );
                if ( $report['exit'] === 0 )
                    $report['warnings'][] = 'not live yet: run ./console exp:upgrade:finish --apply (or exp:velocity deploy --kernel)';
            }
        }
        catch ( Throwable $e )
        {
            expUpgradeLock::release();
            $report['errors'][] = 'internal error: ' . $e->getMessage();
            $report['exit'] = expUpgrade::EXIT_INTERNAL;
        }
        return $report;
    }

    protected static function database( $dir, array $manifest, $root, $db, $apply, array $options )
    {
        $engine = expKernelTableSchema::type( $db );
        if ( isset( $manifest['engine'] ) && $manifest['engine'] !== $engine )
            throw new RuntimeException( "the backup is of a {$manifest['engine']} database, this one is $engine" );
        $extra = array();
        if ( isset( $manifest['tables'] ) && is_array( $manifest['tables'] ) && $engine !== 'sqlite' )
            $extra = array_values( array_diff( array_map( 'strtolower', (array)expKernelTableSchema::tables( $db ) ), array_map( 'strtolower', $manifest['tables'] ) ) );
        $lines = array();
        switch ( $engine )
        {
            case 'sqlite':
                $file = "$dir/database.sqlite";
                if ( !is_file( $file ) )
                    throw new RuntimeException( 'the backup has no database.sqlite' );
                $live = eZSQLite3DB::filePath( (string)$db->DB );
                if ( $live[0] !== '/' )
                    $live = "$root/$live";
                $lines[] = "write $file back into $live (SQLite backup API)";
                if ( $apply )
                {
                    $source = new SQLite3( $file, SQLITE3_OPEN_READONLY );
                    $target = new SQLite3( $live );
                    if ( !$source->backup( $target ) )
                        throw new RuntimeException( 'the SQLite backup API failed: ' . $target->lastErrorMsg() );
                    $source->close();
                    $target->close();
                }
                break;
            case 'mysql':
            case 'postgresql':
                $file = "$dir/database.sql";
                if ( !is_file( $file ) )
                    throw new RuntimeException( 'the backup has no database.sql' );
                $lines[] = "load $file into " . $db->DB . ( $engine === 'mysql' ? ' (mysql)' : ' (psql)' );
                foreach ( $extra as $t )
                    $lines[] = "drop the table $t (made after the backup)";
                if ( $apply )
                {
                    if ( $engine === 'mysql' )
                    {
                        $cnf = expUpgradeBackup::mysqlDefaultsFile( $db, "$dir/.restore.cnf" );
                        $err = '';
                        $code = expUpgradeBackup::runWithEnv( 'mysql --defaults-extra-file=' . escapeshellarg( $cnf ) . ' ' . escapeshellarg( (string)$db->DB )
                                                              . ' < ' . escapeshellarg( $file ), array(), $err );
                        @unlink( $cnf );
                    }
                    else
                    {
                        $err = '';
                        $code = expUpgradeBackup::runWithEnv( 'psql -X -q -v ON_ERROR_STOP=1 -h ' . escapeshellarg( $db->Server ?: 'localhost' )
                                                              . ( $db->Port ? ' -p ' . (int)$db->Port : '' ) . ' -U ' . escapeshellarg( (string)$db->User )
                                                              . ' -d ' . escapeshellarg( (string)$db->DB ) . ' -f ' . escapeshellarg( $file ),
                                                              array( 'PGPASSWORD' => (string)$db->Password ), $err );
                    }
                    if ( $code !== 0 )
                        throw new RuntimeException( 'loading the dump failed (exit ' . $code . '): ' . preg_replace( '/\s+/', ' ', mb_substr( trim( $err ), 0, 400 ) ) );
                    foreach ( $extra as $t )
                        if ( preg_match( '/^[a-z_][a-z0-9_]*$/', $t ) )
                            expUpgradeCatalog::execute( $db, "DROP TABLE $t;\n" );
                    expKernelTableSchema::reset();
                }
                break;
            case 'mongodb':
                $lines[] = "mongorestore --drop from $dir/mongodb";
                if ( $apply )
                    throw new RuntimeException( 'restore MongoDB by hand: mongorestore --drop --dir=' . "$dir/mongodb" );
                break;
            default:
                throw new RuntimeException( "no built-in restore for $engine: restore with the engine's own tool (Oracle: impdp)" );
        }
        return $lines;
    }

    protected static function settings( $dir, array $manifest, $root, $db, $apply, array $options )
    {
        $source = "$dir/settings/override";
        if ( !is_dir( $source ) )
            return array( 'no settings in the backup' );
        $lines = array( "copy $source back over $root/settings/override (files added since stay)" );
        if ( $apply )
        {
            $target = "$root/settings";
            eZDir::copy( $source, $target, true, true );
        }
        return $lines;
    }

    protected static function libraries( $dir, array $manifest, $root, $db, $apply, array $options )
    {
        if ( !is_file( "$dir/libraries/composer.lock" ) )
            return array( 'no composer.lock in the backup' );
        $lines = array( 'copy composer.json and composer.lock back, then composer install' );
        if ( !$apply )
            return $lines;
        $problem = expUpgradeComposer::ownerProblem( "$root/composer.lock" );
        if ( $problem !== '' )
            throw new RuntimeException( $problem );
        foreach ( array( 'composer.json', 'composer.lock' ) as $f )
            if ( is_file( "$dir/libraries/$f" ) && !copy( "$dir/libraries/$f", "$root/$f" ) )
                throw new RuntimeException( "cannot write $root/$f" );
        $context = new expUpgradeContext( $db, true, $options + array( 'root' => $root ) );
        $args = array( 'install', '--no-progress' );
        if ( is_file( "$dir/libraries/vendor/composer/installed.json" ) )
        {
            $installed = json_decode( (string)file_get_contents( "$dir/libraries/vendor/composer/installed.json" ), true );
            if ( is_array( $installed ) && isset( $installed['dev'] ) && !$installed['dev'] )
                $args[] = '--no-dev';
        }
        $r = expUpgradeComposer::run( $context, $args );
        if ( $r['exit'] !== 0 )
            throw new RuntimeException( 'composer install failed (exit ' . $r['exit'] . '): ' . preg_replace( '/\s+/', ' ', mb_substr( trim( $r['errors'] ), -400 ) ) );
        $lines[] = 'composer ' . implode( ' ', $args );
        return $lines;
    }

    protected static function code( $dir, array $manifest, $root, $db, $apply, array $options )
    {
        if ( !is_file( "$dir/code.json" ) )
            return array( 'the code did not move in this upgrade (no code.json)' );
        if ( !expUpgradeGit::available() )
            throw new RuntimeException( 'git_manager is not active: move the code back by hand from ' . "$dir/code.json" );
        $heads = json_decode( (string)file_get_contents( "$dir/code.json" ), true );
        $lines = array();
        foreach ( isset( $heads['repos'] ) ? (array)$heads['repos'] : array() as $label => $repo )
        {
            $path = (string)$repo['path'];
            $head = (string)$repo['head'];
            if ( !preg_match( '/^[0-9a-f]{40}$/', $head ) || !file_exists( "$path/.git" ) )
                throw new RuntimeException( "$label: no checkout or no commit in code.json" );
            $now = expUpgradeGit::value( $path, array( 'rev-parse', '--verify', 'HEAD' ) );
            if ( $now === $head )
            {
                $lines[] = "$label: at " . substr( $head, 0, 10 ) . ' already';
                continue;
            }
            $lines[] = "$label: " . substr( $now, 0, 10 ) . ' -> ' . substr( $head, 0, 10 ) . ' (git reset --keep)';
            if ( !$apply )
                continue;
            $problem = expUpgradeComposer::ownerProblem( "$path/.git" );
            if ( $problem !== '' )
                throw new RuntimeException( "$label: $problem" );
            $r = expUpgradeGit::git( $path, array( 'reset', '--keep', $head ), 300 );
            if ( !$r['ok'] )
                throw new RuntimeException( "$label: git reset --keep refused (local changes in the files it would move back)" );
            expUpgradeGit::updateSubmodules( $path );
        }
        return $lines;
    }
}
