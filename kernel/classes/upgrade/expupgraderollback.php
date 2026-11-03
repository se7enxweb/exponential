<?php
/**
 * File containing the expUpgradeRollback class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Puts an installation back to a caption (expUpgradeCaption: git_manager's captions and exp:upgrade's own), part by
 * part:
 *
 *   database   SQLite: the copy written back through SQLite's backup API; MySQL/MariaDB: the dump through mysql;
 *              PostgreSQL: the dump through psql; MongoDB: the mongodump folder through mongorestore --drop. Tables
 *              (collections) made after the caption (not in its list) are dropped. Oracle has no built-in dump or
 *              restore: the part stops with the exact steps to restore by hand (manualSteps()).
 *   storage    var/ from git_manager's archive (var_<caption>.tar.gz), unpacked over the installation's (files added
 *              since stay)
 *   settings   settings/override copied back (files added since stay)
 *   libraries  composer.json and composer.lock copied back, then composer install (refused over packages kept as git
 *              checkouts, as the libraries stage refuses them)
 *   code       every checkout of code.json moved back to its commit (git reset --keep: refused over local changes)
 *
 * Never automatic, never without apply. With apply it first takes a safety caption of the current state (so the
 * rollback can be undone; --no-caption, refused on production without --force-no-caption), then restores, then runs
 * the verification of exp:upgrade:verify --after (unless no-verify).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeRollback
{
    /** The parts, in the order they are restored. */
    const PARTS = array( 'database', 'storage', 'settings', 'libraries', 'code' );

    /** The engines whose database part is restored here; the others stop with manualSteps(). */
    const RESTORE_ENGINES = array( 'sqlite', 'mysql', 'postgresql', 'mongodb' );

    /**
     * Restores (with apply) or lists what would be restored from a caption.
     *
     * @param string $name the caption id (exp:upgrade:captions lists them)
     * @param array $options apply, parts, backup-dir, db, root, no-caption, force-no-caption, no-verify, url, no-tests,
     *                       allow-vendor-changes, command
     * @return array a report as expUpgrade::report() with 'rollback' => array( caption, parts, safety_caption ) and
     *               'verify' after an applied rollback
     */
    public static function run( $name, array $options = array() )
    {
        $apply = !empty( $options['apply'] );
        $report = expUpgrade::report( $apply ? 'rollback' : 'rollback-dry-run', $options );
        $report['rollback'] = array( 'backup' => (string)$name, 'caption' => (string)$name, 'parts' => array(), 'safety_caption' => null );
        $report['verify'] = null;
        $root = rtrim( isset( $options['root'] ) ? $options['root'] : eZSys::rootDir(), '/' );
        $dir = expUpgradeCaption::find( (string)$name, !empty( $options['backup-dir'] ) ? $options['backup-dir'] : null );
        if ( $dir === null )
        {
            $report['errors'][] = "no caption $name (./console exp:upgrade:captions lists them)";
            $report['exit'] = expUpgrade::EXIT_USAGE;
            return $report;
        }
        $meta = self::meta( $dir );
        $parts = !empty( $options['parts'] ) ? expUpgrade::listOption( $options['parts'] ) : self::PARTS;
        foreach ( $parts as $p )
            if ( !in_array( $p, self::PARTS, true ) )
            {
                $report['errors'][] = "unknown part $p; parts: " . implode( ', ', self::PARTS );
                $report['exit'] = expUpgrade::EXIT_USAGE;
                return $report;
            }
        if ( $apply && !empty( $options['no-caption'] ) && empty( $options['force-no-caption'] ) && expUpgrade::isProduction() )
        {
            $report['errors'][] = 'this installation is production: --no-caption is refused, the safety caption is the way back (or add --force-no-caption)';
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
            if ( $apply && empty( $options['no-caption'] ) )
            {
                $context = new expUpgradeContext( $db, true, $options + array( 'root' => $root, 'mode' => 'apply' ) );
                $safety = expUpgradeCaption::take( 'safety', 'rollback to ' . $name . ' ' . date( 'Ymd-His' ) . ' safety', $context );
                $report['rollback']['safety_caption'] = array( 'id' => $safety['id'], 'ok' => $safety['ok'], 'source' => $safety['source'], 'messages' => $safety['messages'] );
                if ( !$safety['ok'] )
                {
                    expUpgradeLock::release();
                    $report['errors'][] = 'the safety caption failed, nothing was restored: ' . implode( '; ', $safety['messages'] );
                    $report['exit'] = expUpgrade::EXIT_FAILED;
                    return $report;
                }
            }
            foreach ( self::PARTS as $part )
            {
                if ( !in_array( $part, $parts, true ) )
                    continue;
                $lines = array();
                try
                {
                    $lines = self::$part( $dir, $meta, $root, $db, $apply, $options );
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
                expUpgrade::audit( 'system.upgrade.rollback', array( 'object' => array( 'type' => 'caption', 'id' => $name ),
                                                                      'result' => $report['exit'] === 0 ? 'success' : 'failure',
                                                                      'x' => array( 'parts' => array_keys( $report['rollback']['parts'] ),
                                                                                    'safety_caption' => $report['rollback']['safety_caption'] ? $report['rollback']['safety_caption']['id'] : null ) ) );
                if ( $report['exit'] === 0 && empty( $options['no-verify'] ) )
                {
                    expKernelTableSchema::reset();
                    $report['verify'] = expUpgradeVerifyAfter::run( array( 'db' => $db, 'url' => isset( $options['url'] ) ? $options['url'] : array(),
                                                                           'no-tests' => !empty( $options['no-tests'] ), 'since' => time() - 60 ) );
                    if ( !$report['verify']['ok'] )
                    {
                        $report['errors'][] = 'restored, but the verification after the rollback failed: '
                                            . implode( ', ', array_keys( array_filter( $report['verify']['checks'], function ( $c ) { return $c['ok'] === false; } ) ) );
                        $report['exit'] = expUpgrade::EXIT_FAILED;
                    }
                }
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

    /**
     * The steps to restore a database by hand, for an engine without a built-in restore (Oracle): the exact commands
     * with the connection's account and service, never its password (the tool asks for it).
     *
     * @param string $engine
     * @param eZDBInterface|object|null $db the connection (its User, Server, Port, DB)
     * @param string|null $dir the caption, when there is one
     * @param string[] $tablesBefore the tables at the time of the export (caption.json), to name the ones to drop
     * @return string[] numbered lines
     */
    public static function manualSteps( $engine, $db = null, $dir = null, array $tablesBefore = array() )
    {
        $user = $db && isset( $db->User ) && (string)$db->User !== '' ? (string)$db->User : '<user>';
        $service = $db && isset( $db->DB ) && (string)$db->DB !== '' ? (string)$db->DB : '<service>';
        $server = $db && isset( $db->Server ) ? (string)$db->Server : '';
        $port = $db && !empty( $db->Port ) ? (int)$db->Port : 0;
        $extra = array();
        if ( $tablesBefore && $db instanceof eZDBInterface )
        {
            try
            {
                $extra = array_values( array_diff( array_map( 'strtolower', (array)expKernelTableSchema::tables( $db ) ), array_map( 'strtolower', $tablesBefore ) ) );
            }
            catch ( Throwable $e )
            {
            }
        }
        $steps = array( '1. Switch the site to maintenance mode and stop its cronjobs, so nothing writes while the database is restored: '
                        . './console exp:maintenance on' );
        if ( $engine === 'oracle' )
        {
            // a connection whose database is already a connect string (//host:port/service, a descriptor) keeps it
            $connect = $server !== '' && strpbrk( $service, '/(' ) === false ? '//' . $server . ':' . ( $port ?: 1521 ) . '/' . $service : $service;
            $schema = strtoupper( $user );
            $steps[] = '2. Restore the schema ' . $schema . ' from the Data Pump export (expdp) taken before the change; impdp asks for the password: '
                     . 'impdp ' . $user . '@' . $connect . ' SCHEMAS=' . $schema . ' DIRECTORY=DATA_PUMP_DIR DUMPFILE=<the export>.dmp '
                     . 'TABLE_EXISTS_ACTION=REPLACE LOGFILE=restore-' . date( 'Ymd-His' ) . '.log';
            $steps[] = '3. Drop the tables made after the export (impdp leaves them): '
                     . ( $extra ? implode( ' ', array_map( function ( $t ) { return 'DROP TABLE ' . strtoupper( $t ) . ' PURGE;'; }, $extra ) )
                                : 'DROP TABLE <name> PURGE; for each table the export does not have' );
        }
        else
        {
            $steps[] = "2. Restore the database $service with $engine's own tool from a dump taken before the change";
            $steps[] = '3. Drop the tables made after the dump' . ( $extra ? ': ' . implode( ', ', $extra ) : '' );
        }
        if ( $dir !== null && $dir !== '' )
            $steps[] = '4. Put back what else the caption holds (settings/override, var/ storage): ./console exp:upgrade:rollback '
                     . basename( $dir ) . ' --parts=settings,storage --apply';
        else
            $steps[] = '4. Put back settings/override and var/ storage from your own copy if the change wrote them';
        $steps[] = '5. Clear the caches and reload the site: ./console exp:velocity deploy';
        $steps[] = '6. Check the result: ./console exp:upgrade:verify --after';
        $steps[] = '7. Switch maintenance mode off: ./console exp:maintenance off';
        return $steps;
    }

    /**
     * The database a mongodump folder holds (its one subfolder; mongodump --out writes <out>/<database>/).
     *
     * @param string $dir
     * @return string
     * @throws RuntimeException when it holds none or more than one
     */
    public static function mongoDumpDatabase( $dir )
    {
        $names = array();
        foreach ( glob( rtrim( $dir, '/' ) . '/*', GLOB_ONLYDIR ) ?: array() as $sub )
            if ( basename( $sub ) !== 'admin' && preg_match( '/^[A-Za-z0-9_-]{1,64}$/', basename( $sub ) ) )
                $names[] = basename( $sub );
        if ( count( $names ) !== 1 )
            throw new RuntimeException( 'the MongoDB dump holds ' . ( $names ? 'more than one database: ' . implode( ', ', $names ) : 'no database' ) );
        return $names[0];
    }

    /** @return array caption.json, or manifest.json of an older backup, or an empty array */
    protected static function meta( $dir )
    {
        foreach ( array( "$dir/upgrade/caption.json", "$dir/manifest.json" ) as $file )
            if ( is_file( $file ) )
            {
                $data = json_decode( (string)file_get_contents( $file ), true );
                if ( is_array( $data ) )
                    return $data;
            }
        return array();
    }

    /** A file of the caption: in upgrade/ (captions), else at the top (backups of exp:upgrade before captions). */
    protected static function path( $dir, $relative )
    {
        if ( file_exists( "$dir/upgrade/$relative" ) )
            return "$dir/upgrade/$relative";
        return file_exists( "$dir/$relative" ) ? "$dir/$relative" : null;
    }

    protected static function database( $dir, array $meta, $root, $db, $apply, array $options )
    {
        $engine = expKernelTableSchema::type( $db );
        if ( !empty( $meta['engine'] ) && $meta['engine'] !== $engine )
            throw new RuntimeException( "the caption is of a {$meta['engine']} database, this one is $engine" );
        if ( !in_array( $engine, self::RESTORE_ENGINES, true ) )
            throw new RuntimeException( "no built-in restore for $engine; restore it by hand:\n"
                                        . implode( "\n", self::manualSteps( $engine, $db, $dir, isset( $meta['tables'] ) ? (array)$meta['tables'] : array() ) ) );
        $tmp = "$root/var/tmp/rollback-" . getmypid();
        try
        {
            $dump = expUpgradeCaption::extractDump( $dir, $tmp );
            // the folder for the client's option file: extractDump() makes it only for a dump in an archive
            if ( !is_dir( $tmp ) )
                eZDir::mkdir( $tmp, 0700, true );
            $extra = array();
            if ( isset( $meta['tables'] ) && is_array( $meta['tables'] ) && $engine !== 'sqlite' )
                $extra = array_values( array_diff( array_map( 'strtolower', (array)expKernelTableSchema::tables( $db ) ), array_map( 'strtolower', $meta['tables'] ) ) );
            $lines = array();
            switch ( $engine )
            {
                case 'sqlite':
                    $live = eZSQLite3DB::filePath( (string)$db->DB );
                    if ( $live[0] !== '/' )
                        $live = "$root/$live";
                    $lines[] = 'write ' . basename( $dump['path'] ) . " back into $live (SQLite backup API)";
                    if ( $apply )
                    {
                        $source = new SQLite3( $dump['path'], SQLITE3_OPEN_READONLY );
                        $target = new SQLite3( $live );
                        if ( !$source->backup( $target ) )
                            throw new RuntimeException( 'the SQLite backup API failed: ' . $target->lastErrorMsg() );
                        $source->close();
                        $target->close();
                    }
                    break;
                case 'mysql':
                case 'postgresql':
                    $lines[] = 'load ' . basename( $dump['path'] ) . ' into ' . $db->DB . ( $engine === 'mysql' ? ' (mysql)' : ' (psql)' );
                    foreach ( $extra as $t )
                        $lines[] = "drop the table $t (made after the caption)";
                    if ( $apply )
                    {
                        $err = '';
                        if ( $engine === 'mysql' )
                        {
                            $cnf = expUpgradeBackup::mysqlDefaultsFile( $db, "$tmp/.restore.cnf" );
                            $code = expUpgradeBackup::runWithEnv( 'mysql --defaults-extra-file=' . escapeshellarg( $cnf ) . ' ' . escapeshellarg( (string)$db->DB )
                                                                  . ' < ' . escapeshellarg( $dump['path'] ), array(), $err );
                            @unlink( $cnf );
                        }
                        else
                            $code = expUpgradeBackup::runWithEnv( 'psql -X -q -v ON_ERROR_STOP=1 -h ' . escapeshellarg( $db->Server ?: 'localhost' )
                                                                  . ( $db->Port ? ' -p ' . (int)$db->Port : '' ) . ' -U ' . escapeshellarg( (string)$db->User )
                                                                  . ' -d ' . escapeshellarg( (string)$db->DB ) . ' -f ' . escapeshellarg( $dump['path'] ),
                                                                  array( 'PGPASSWORD' => (string)$db->Password ), $err );
                        if ( $code !== 0 )
                            throw new RuntimeException( 'loading the dump failed (exit ' . $code . '): ' . preg_replace( '/\s+/', ' ', mb_substr( trim( $err ), 0, 400 ) ) );
                        foreach ( $extra as $t )
                            if ( preg_match( '/^[a-z_][a-z0-9_]*$/', $t ) )
                                expUpgradeCatalog::execute( $db, "DROP TABLE $t;\n" );
                        expKernelTableSchema::reset();
                    }
                    break;
                case 'mongodb':
                    $from = self::mongoDumpDatabase( $dump['path'] );
                    $lines[] = 'mongorestore --drop ' . $from . ' from ' . basename( $dump['path'] ) . ' into ' . $db->DB;
                    foreach ( $extra as $t )
                        $lines[] = "drop the collection $t (made after the caption)";
                    if ( $apply )
                    {
                        $cnf = expUpgradeBackup::mongoConfigFile( $db, "$tmp/.restore.yaml" );
                        // the connection string names the database, so mongorestore takes the dump's database folder
                        // (given the whole --out folder it skips the subfolder and restores nothing, with exit 0)
                        $err = '';
                        $code = expUpgradeBackup::runWithEnv( 'mongorestore --config=' . escapeshellarg( $cnf ) . ' --drop --dir='
                                                              . escapeshellarg( rtrim( $dump['path'], '/' ) . '/' . $from ), array(), $err );
                        @unlink( $cnf );
                        if ( $code !== 0 )
                            throw new RuntimeException( 'mongorestore failed (exit ' . $code . '): ' . preg_replace( '/\s+/', ' ', mb_substr( trim( $err ), -400 ) ) );
                        foreach ( $extra as $t )
                            if ( preg_match( '/^[a-z_][a-z0-9_]*$/', $t ) )
                                expUpgradeCatalog::execute( $db, "DROP TABLE $t;\n" );
                        expKernelTableSchema::reset();
                    }
                    break;
            }
            return $lines;
        }
        finally
        {
            if ( is_dir( $tmp ) )
                eZDir::recursiveDelete( $tmp, false );
        }
    }

    protected static function storage( $dir, array $meta, $root, $db, $apply, array $options )
    {
        $archives = glob( "$dir/var_*.tar.gz" ) ?: array();
        if ( !$archives )
            return array( glob( "$dir/var_*.gpg" ) ? 'the storage archive is encrypted (GPG): decrypt it with git_manager first' : 'no storage in the caption (var/ is in git_manager captions only)' );
        $archive = $archives[0];
        $lines = array( 'unpack ' . basename( $archive ) . " over $root/var (files added since stay)" );
        if ( !$apply )
            return $lines;
        $problem = expUpgradeComposer::ownerProblem( "$root/var" );
        if ( $problem !== '' )
            throw new RuntimeException( $problem );
        exec( 'tar -xzf ' . escapeshellarg( $archive ) . ' -C ' . escapeshellarg( $root ) . ' var 2>&1', $out, $code );
        if ( $code !== 0 )
            throw new RuntimeException( 'unpacking the storage failed: ' . implode( ' ', array_slice( $out, -3 ) ) );
        return $lines;
    }

    protected static function settings( $dir, array $meta, $root, $db, $apply, array $options )
    {
        $source = self::path( $dir, 'settings/override' );
        if ( $source === null )
            return array( 'no settings in the caption' );
        $lines = array( "copy the caption's settings/override back over $root/settings/override (files added since stay)" );
        if ( $apply )
        {
            $target = "$root/settings";
            eZDir::copy( $source, $target, true, true );
        }
        return $lines;
    }

    protected static function libraries( $dir, array $meta, $root, $db, $apply, array $options )
    {
        $lock = self::path( $dir, 'libraries/composer.lock' );
        if ( $lock === null )
            return array( 'no composer.lock in the caption' );
        $lines = array( 'copy composer.json and composer.lock back, then composer install' );
        if ( !$apply )
            return $lines;
        $problem = expUpgradeComposer::ownerProblem( "$root/composer.lock" );
        if ( $problem !== '' )
            throw new RuntimeException( $problem );
        $current = is_file( "$root/composer.lock" ) ? file_get_contents( "$root/composer.lock" ) : null;
        if ( !copy( $lock, "$root/composer.lock" ) )
            throw new RuntimeException( "cannot write $root/composer.lock" );
        $checkouts = !empty( $options['allow-vendor-changes'] ) ? array()
                   : expUpgradeComposer::gitCheckouts( $root, array_keys( (array)expUpgradeComposer::lockDifferences( $root ) ) );
        if ( $checkouts )
        {
            if ( $current !== null )
                file_put_contents( "$root/composer.lock", $current );
            throw new RuntimeException( 'composer install would replace ' . count( $checkouts ) . ' git checkout(s): ' . implode( ', ', array_keys( $checkouts ) )
                                        . '; composer.lock left as it was (--allow-vendor-changes installs over them)' );
        }
        $json = self::path( $dir, 'libraries/composer.json' );
        if ( $json !== null && !copy( $json, "$root/composer.json" ) )
            throw new RuntimeException( "cannot write $root/composer.json" );
        $context = new expUpgradeContext( $db, true, $options + array( 'root' => $root ) );
        $args = array( 'install', '--no-progress' );
        $installed = self::path( $dir, 'libraries/vendor/composer/installed.json' );
        if ( $installed !== null )
        {
            $data = json_decode( (string)file_get_contents( $installed ), true );
            if ( is_array( $data ) && isset( $data['dev'] ) && !$data['dev'] )
                $args[] = '--no-dev';
        }
        $r = expUpgradeComposer::run( $context, $args );
        if ( $r['exit'] !== 0 )
            throw new RuntimeException( 'composer install failed (exit ' . $r['exit'] . '): ' . preg_replace( '/\s+/', ' ', mb_substr( trim( $r['errors'] ), -400 ) ) );
        $lines[] = 'composer ' . implode( ' ', $args );
        return $lines;
    }

    protected static function code( $dir, array $meta, $root, $db, $apply, array $options )
    {
        $file = self::path( $dir, 'code.json' );
        if ( $file === null )
            return array( 'no code state in the caption (no code.json)' );
        if ( !expUpgradeGit::available() )
            throw new RuntimeException( "git_manager is not active: move the code back by hand from $file" );
        $heads = json_decode( (string)file_get_contents( $file ), true );
        $lines = array();
        foreach ( isset( $heads['repos'] ) ? (array)$heads['repos'] : array() as $label => $repo )
        {
            $path = (string)$repo['path'];
            $head = (string)$repo['head'];
            if ( !preg_match( '/^[0-9a-f]{40}$/', $head ) || !file_exists( "$path/.git" ) )
                throw new RuntimeException( "$label: no checkout or no commit in code.json" );
            $problem = expUpgradeComposer::ownerProblem( "$path/.git" );
            if ( $problem !== '' )
                throw new RuntimeException( "$label: $problem" );
            $now = expUpgradeGit::value( $path, array( 'rev-parse', '--verify', 'HEAD' ) );
            if ( $now === $head )
            {
                $lines[] = "$label: at " . substr( $head, 0, 10 ) . ' already';
                continue;
            }
            $lines[] = "$label: " . substr( $now, 0, 10 ) . ' -> ' . substr( $head, 0, 10 ) . ' (git reset --keep)';
            if ( !$apply )
                continue;
            $r = expUpgradeGit::git( $path, array( 'reset', '--keep', $head ), 300 );
            if ( !$r['ok'] )
                throw new RuntimeException( "$label: git reset --keep refused (local changes in the files it would move back)" );
            expUpgradeGit::updateSubmodules( $path );
        }
        return $lines;
    }
}
