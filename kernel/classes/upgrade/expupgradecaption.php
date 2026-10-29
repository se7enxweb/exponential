<?php
/**
 * File containing the expUpgradeCaption class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Captions: the safe points of exp:upgrade. A caption is git_manager's full backup (BackupManager::createFullCaption():
 * the database dump, var/ storage, its licenses) in git_manager's backup folder, with what exp:upgrade adds in its
 * upgrade/ folder:
 *
 *   upgrade/caption.json        id, label, kind (pre, post, safety, manual), upgrade from/to, versions, engine, source
 *   upgrade/settings/override   the settings overrides
 *   upgrade/libraries/          composer.json, composer.lock, vendor/composer/installed.json
 *   upgrade/code.json           the commit of the root checkout and of every checkout of the run (--repos)
 *   upgrade/checksums.json      SHA-256 of every file of the caption (integrity)
 *
 * Without git_manager, or with a git_manager that cannot dump the engine (an older git_manager dumps MySQL
 * only), the caption is made by exp:upgrade itself in var/backup/upgrade/<time>/ (expUpgradeBackup: the database dump,
 * no var/ storage) with the same upgrade/ folder, and says why.
 *
 * Every caption is listed (git_manager's own captions too), checked (checksums, and a restore of the dump into a
 * throwaway database), pruned by [UpgradeSettings] CaptionKeep / CaptionKeepDays / CaptionKeepUpgrades (only captions
 * of exp:upgrade, never git_manager's own) and rolled back to (expUpgradeRollback).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeCaption
{
    /** The kinds of a caption (permanent). */
    const KINDS = array( 'pre', 'post', 'safety', 'manual' );

    /** @return BackupManager|null git_manager's backup manager, when git_manager is there */
    public static function manager()
    {
        if ( !class_exists( 'BackupManager' ) )
            return null;
        try
        {
            return new BackupManager();
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /**
     * Whether git_manager can dump this engine (its supportedEngines(); before that, MySQL only).
     *
     * @param string $engine
     * @return bool
     */
    public static function managerDumps( $engine )
    {
        if ( !class_exists( 'BackupManager' ) )
            return false;
        // supportedEngines() arrived in git_manager 2.0.25; older copies dump MySQL only
        $engines = is_callable( array( 'BackupManager', 'supportedEngines' ) )
            ? (array)call_user_func( array( 'BackupManager', 'supportedEngines' ) )
            : array( 'mysql' );
        return in_array( $engine, $engines, true );
    }

    /**
     * Takes a caption.
     *
     * @param string $kind pre, post, safety, manual
     * @param string $label e.g. "upgrade 6.0.14→6.0.15 20261029-101500 pre"
     * @param expUpgradeContext $context the connection, root, options (repos, to, backup-dir)
     * @return array ok, id, dir, source (git_manager or exp:upgrade), label, kind, messages
     */
    public static function take( $kind, $label, expUpgradeContext $context )
    {
        $db = $context->db();
        $root = rtrim( (string)$context->option( 'root' ), '/' );
        $engine = $context->engine();
        $result = array( 'ok' => false, 'id' => '', 'dir' => '', 'source' => '', 'label' => $label, 'kind' => $kind, 'messages' => array() );
        $manager = self::managerDumps( $engine ) ? self::manager() : null;
        if ( $manager )
        {
            $made = $manager->createFullCaption( $label );
            if ( empty( $made['success'] ) )
            {
                $result['messages'][] = 'git_manager caption failed: ' . ( isset( $made['message'] ) ? $made['message'] : '' );
                return $result;
            }
            $result['dir'] = rtrim( $manager->backupPath(), '/' ) . '/' . $made['timestamp'];
            $result['source'] = 'git_manager';
            $result['messages'][] = 'git_manager caption ' . $made['timestamp'];
        }
        else
        {
            $why = class_exists( 'BackupManager' ) ? "git_manager does not dump $engine (update git_manager)" : 'git_manager is not active';
            $backup = expUpgradeBackup::create( $db, $context->option( 'backup-dir' ), $root );
            $result['dir'] = $backup['dir'];
            $result['source'] = 'exp:upgrade';
            $result['messages'] = array_merge( $result['messages'], array( "caption made by exp:upgrade: $why" ), $backup['messages'] );
            if ( !$backup['ok'] )
                return $result;
        }
        $result['id'] = basename( $result['dir'] );
        try
        {
            self::addUpgradePart( $result, $context );
        }
        catch ( Throwable $e )
        {
            $result['messages'][] = 'the upgrade part of the caption failed: ' . $e->getMessage();
            return $result;
        }
        $result['ok'] = true;
        expUpgradeRecord::writeCaption( $db, $result['id'], $kind, $label, $result['source'] );
        expUpgrade::audit( 'system.upgrade.caption', array( 'object' => array( 'type' => 'caption', 'id' => $result['id'] ), 'verb' => 'take',
                                                             'result' => 'success', 'x' => array( 'kind' => $kind, 'source' => $result['source'], 'label' => $label ) ) );
        eZLog::write( "caption {$result['id']} ($kind, {$result['source']}): $label", 'upgrade.log' );
        return $result;
    }

    /**
     * The upgrade/ folder of a caption: settings, libraries, code, caption.json and checksums.json.
     */
    protected static function addUpgradePart( array &$caption, expUpgradeContext $context )
    {
        $dir = $caption['dir'];
        $root = rtrim( (string)$context->option( 'root' ), '/' );
        $up = "$dir/upgrade";
        $old = umask( eZFile::creationUmask( 0077 ) );
        try
        {
            eZDir::mkdir( $up, 0700, true );
            if ( is_dir( "$root/settings/override" ) )
            {
                eZDir::mkdir( "$up/settings", 0700, true );
                $target = "$up/settings";
                eZDir::copy( "$root/settings/override", $target, true, true );
            }
            foreach ( array( 'composer.json', 'composer.lock', 'vendor/composer/installed.json' ) as $file )
            {
                if ( !is_file( "$root/$file" ) )
                    continue;
                eZDir::mkdir( "$up/libraries/" . dirname( $file ), 0700, true );
                copy( "$root/$file", "$up/libraries/$file" );
            }
            $code = self::codeState( $context );
            if ( $code )
                file_put_contents( "$up/code.json", json_encode( array( 'format' => 'exp-upgrade-code/1', 'created' => date( 'c' ), 'repos' => $code ),
                                                                 JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
            $db = $context->db();
            file_put_contents( "$up/caption.json", json_encode( array(
                'format' => 'exp-upgrade-caption/1',
                'id' => $caption['id'],
                'label' => $caption['label'],
                'kind' => $caption['kind'],
                'source' => $caption['source'],
                'created' => date( 'c' ),
                'engine' => $context->engine(),
                'database_version' => $context->engine() === 'mongodb' ? null : expUpgradeStepSiteVersion::stored( $db ),
                'kernel_version' => expUpgrade::kernelVersion(),
                'upgrade' => array( 'from' => (string)$context->option( 'caption-from', '' ), 'to' => (string)$context->option( 'caption-to', '' ) ),
                'extensions' => self::extensionVersions(),
                'tables' => (array)expKernelTableSchema::tables( $db ),
                'encrypted' => (bool)glob( "$dir/*.gpg" ),
            ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" );
            file_put_contents( "$up/checksums.json", json_encode( self::checksums( $dir ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
        }
        finally
        {
            umask( $old );
        }
    }

    /** @return array label => array( path, head, branch ) of the checkouts, when git_manager reads them */
    protected static function codeState( expUpgradeContext $context )
    {
        $root = rtrim( (string)$context->option( 'root' ), '/' );
        if ( !file_exists( "$root/.git" ) || !expUpgradeGit::available() )
            return array();
        $out = array();
        foreach ( expUpgradeStepCodeUpdate::repos( $context ) as $path => $label )
        {
            if ( !file_exists( "$path/.git" ) || expUpgradeComposer::ownerProblem( "$path/.git" ) !== '' )
                continue;
            $head = expUpgradeGit::value( $path, array( 'rev-parse', '--verify', 'HEAD' ) );
            if ( $head !== '' )
                $out[$label] = array( 'path' => $path, 'head' => $head, 'branch' => expUpgradeGit::value( $path, array( 'rev-parse', '--abbrev-ref', 'HEAD' ) ) );
        }
        return $out;
    }

    /** @return array extension => version of the active extensions with an ezinfo.php or extension.xml version */
    protected static function extensionVersions()
    {
        $out = array();
        foreach ( (array)eZExtension::activeExtensions() as $ext )
        {
            $xml = eZExtension::baseDirectory() . "/$ext/extension.xml";
            if ( is_readable( $xml ) && preg_match( '#<version>([^<]+)</version>#', (string)file_get_contents( $xml ), $m ) )
                $out[$ext] = trim( $m[1] );
        }
        ksort( $out );
        return $out;
    }

    /**
     * SHA-256 of every file of a caption, relative path => hash (checksums.json itself left out).
     *
     * @param string $dir
     * @return array
     */
    public static function checksums( $dir )
    {
        $out = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( !$file->isFile() || $file->isLink() )
                continue;
            $rel = substr( $file->getPathname(), strlen( $dir ) + 1 );
            if ( $rel === 'upgrade/checksums.json' )
                continue;
            $out[$rel] = hash_file( 'sha256', $file->getPathname() );
        }
        ksort( $out );
        return $out;
    }

    /**
     * The folders captions live in: git_manager's backup folder, and exp:upgrade's own.
     *
     * @param string|null $backupDir exp:upgrade's folder (expUpgradeBackup::baseDir() when null)
     * @return array source => folder
     */
    public static function folders( $backupDir = null )
    {
        $out = array();
        $manager = self::manager();
        if ( $manager )
            $out['git_manager'] = rtrim( $manager->backupPath(), '/' );
        $out['exp:upgrade'] = rtrim( $backupDir ?: expUpgradeBackup::baseDir(), '/' );
        return $out;
    }

    /**
     * The folder of a caption id, or null.
     *
     * @param string $id
     * @param string|null $backupDir
     * @return string|null
     */
    public static function find( $id, $backupDir = null )
    {
        if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.-]{0,80}$/', (string)$id ) || strpos( (string)$id, '..' ) !== false )
            return null;
        foreach ( self::folders( $backupDir ) as $folder )
            if ( is_dir( "$folder/$id" ) && !is_link( "$folder/$id" ) )
                return "$folder/$id";
        return null;
    }

    /**
     * Every caption, newest first.
     *
     * @param string|null $backupDir
     * @return array[] id, dir, source, label, kind, created (timestamp), age_days, size, engine, upgrade, kernel_version,
     *                 database_version, code, encrypted, upgrade_part (bool), checksums (bool), rollback (the command)
     */
    public static function all( $backupDir = null )
    {
        $out = array();
        foreach ( self::folders( $backupDir ) as $source => $folder )
        {
            foreach ( glob( "$folder/*", GLOB_ONLYDIR ) ?: array() as $dir )
            {
                $id = basename( $dir );
                if ( is_link( $dir ) || !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.-]*$/', $id ) )
                    continue;
                $out[] = self::describe( $dir, $source );
            }
        }
        usort( $out, function ( $a, $b ) { return $b['created'] <=> $a['created'] ?: strcmp( $b['id'], $a['id'] ); } );
        return $out;
    }

    /**
     * What a caption folder holds.
     *
     * @param string $dir
     * @param string $source
     * @return array
     */
    public static function describe( $dir, $source )
    {
        $id = basename( $dir );
        $meta = is_file( "$dir/upgrade/caption.json" ) ? json_decode( (string)file_get_contents( "$dir/upgrade/caption.json" ), true ) : null;
        $manifest = is_file( "$dir/manifest.json" ) ? json_decode( (string)file_get_contents( "$dir/manifest.json" ), true ) : null;
        $code = is_file( "$dir/upgrade/code.json" ) ? json_decode( (string)file_get_contents( "$dir/upgrade/code.json" ), true ) : null;
        $created = is_array( $meta ) && !empty( $meta['created'] ) ? strtotime( $meta['created'] )
                 : ( is_array( $manifest ) && !empty( $manifest['created'] ) ? strtotime( $manifest['created'] ) : filemtime( $dir ) );
        $size = 0;
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) ) as $f )
            if ( $f->isFile() )
                $size += $f->getSize();
        $label = is_array( $meta ) ? (string)$meta['label'] : ( is_file( "$dir/description.txt" ) ? trim( (string)file_get_contents( "$dir/description.txt" ) ) : '' );
        $commits = array();
        foreach ( is_array( $code ) && isset( $code['repos'] ) ? $code['repos'] : array() as $label2 => $repo )
            $commits[$label2] = substr( (string)$repo['head'], 0, 10 );
        return array(
            'id' => $id,
            'dir' => $dir,
            'source' => is_array( $meta ) && !empty( $meta['source'] ) ? $meta['source'] : $source,
            'label' => $label,
            'kind' => is_array( $meta ) ? (string)$meta['kind'] : ( $source === 'git_manager' ? 'git_manager' : 'backup' ),
            'created' => (int)$created,
            'age_days' => (int)floor( ( time() - (int)$created ) / 86400 ),
            'size' => $size,
            'engine' => is_array( $meta ) ? (string)$meta['engine'] : ( is_array( $manifest ) && isset( $manifest['engine'] ) ? $manifest['engine'] : '' ),
            'upgrade' => is_array( $meta ) && isset( $meta['upgrade'] ) ? $meta['upgrade'] : null,
            'kernel_version' => is_array( $meta ) ? (string)$meta['kernel_version'] : '',
            'database_version' => is_array( $meta ) ? $meta['database_version'] : ( is_array( $manifest ) && isset( $manifest['database_version'] ) ? $manifest['database_version'] : null ),
            'code' => $commits,
            'encrypted' => (bool)glob( "$dir/*.gpg" ),
            'upgrade_part' => is_array( $meta ),
            'checksums' => is_file( "$dir/upgrade/checksums.json" ),
            'rollback' => "./console exp:upgrade:rollback $id --apply",
        );
    }

    /**
     * Checks a caption: every checksum, and the database dump restored into a throwaway database (SQLite: a copy and
     * PRAGMA integrity_check; MySQL/MariaDB and PostgreSQL: a database <name>_capverify_<pid> made with the
     * installation's account, loaded, counted and dropped; refused as "unknown" when the account may not create one).
     *
     * @param string $id
     * @param eZDBInterface $db
     * @param string|null $backupDir
     * @return array ok (bool|null), checks => array( name => array( ok, detail ) )
     */
    public static function verify( $id, $db, $backupDir = null )
    {
        $dir = self::find( $id, $backupDir );
        $result = array( 'ok' => false, 'id' => (string)$id, 'checks' => array() );
        if ( $dir === null )
        {
            $result['checks']['found'] = array( 'ok' => false, 'detail' => "no caption $id" );
            return $result;
        }
        if ( is_file( "$dir/upgrade/checksums.json" ) )
        {
            $want = (array)json_decode( (string)file_get_contents( "$dir/upgrade/checksums.json" ), true );
            $have = self::checksums( $dir );
            $bad = array();
            foreach ( $want as $file => $hash )
                if ( !isset( $have[$file] ) )
                    $bad[] = "$file missing";
                else if ( $have[$file] !== $hash )
                    $bad[] = "$file changed";
            $result['checks']['checksums'] = array( 'ok' => !$bad, 'detail' => $bad ? implode( ', ', array_slice( $bad, 0, 10 ) ) : count( $want ) . ' file(s) as written' );
        }
        else
            $result['checks']['checksums'] = array( 'ok' => null, 'detail' => 'no checksums.json (a caption not made by exp:upgrade)' );
        $tmp = rtrim( eZSys::rootDir(), '/' ) . '/var/tmp/capverify-' . getmypid();
        try
        {
            $dump = self::extractDump( $dir, $tmp );
            $result['checks']['restore'] = self::restoreTest( $dump, $db );
        }
        catch ( Throwable $e )
        {
            $result['checks']['restore'] = array( 'ok' => false, 'detail' => $e->getMessage() );
        }
        if ( is_dir( $tmp ) )
            eZDir::recursiveDelete( $tmp, false );
        $oks = array_column( $result['checks'], 'ok' );
        $result['ok'] = in_array( false, $oks, true ) ? false : ( in_array( null, $oks, true ) ? null : true );
        expUpgrade::audit( 'system.upgrade.caption', array( 'object' => array( 'type' => 'caption', 'id' => (string)$id ), 'verb' => 'verify',
                                                             'result' => $result['ok'] === false ? 'failure' : 'success' ) );
        return $result;
    }

    /**
     * The database dump of a caption, unpacked into $tmp when it is in an archive.
     *
     * @param string $dir the caption
     * @param string $tmp a folder to unpack into (made here)
     * @return array engine, path (a .sql file, a .sqlite file or a mongodump folder)
     */
    public static function extractDump( $dir, $tmp )
    {
        $describe = is_file( "$dir/dump.json" ) ? json_decode( (string)file_get_contents( "$dir/dump.json" ), true ) : null;
        if ( is_array( $describe ) )
        {
            if ( !empty( $describe['encrypted'] ) )
                throw new RuntimeException( 'the dump is encrypted (GPG): decrypt it with git_manager first' );
            return array( 'engine' => $describe['engine'], 'path' => self::untar( "$dir/" . basename( $describe['archive'] ), (string)$describe['file'], $tmp ) );
        }
        $archives = glob( "$dir/sql_*.tar.gz" ) ?: array();
        if ( $archives )
            return array( 'engine' => 'mysql', 'path' => self::untar( $archives[0], 'sql/complete.sql', $tmp ) );
        if ( glob( "$dir/sql_*.gpg" ) )
            throw new RuntimeException( 'the dump is encrypted (GPG): decrypt it with git_manager first' );
        $manifest = is_file( "$dir/manifest.json" ) ? json_decode( (string)file_get_contents( "$dir/manifest.json" ), true ) : array();
        $engine = isset( $manifest['engine'] ) ? $manifest['engine'] : '';
        foreach ( array( 'database.sqlite', 'database.sql', 'mongodb' ) as $name )
            if ( file_exists( "$dir/$name" ) )
                return array( 'engine' => $engine ?: ( $name === 'database.sqlite' ? 'sqlite' : '' ), 'path' => "$dir/$name" );
        throw new RuntimeException( 'the caption holds no database dump' );
    }

    /** Unpacks one path of a .tar.gz into $tmp and returns its full path. */
    protected static function untar( $archive, $inner, $tmp )
    {
        if ( !is_file( $archive ) )
            throw new RuntimeException( 'no archive ' . basename( $archive ) );
        if ( !preg_match( '#^[A-Za-z0-9_./-]+$#', $inner ) || strpos( $inner, '..' ) !== false )
            throw new RuntimeException( "not a path inside the archive: $inner" );
        @mkdir( $tmp, eZDir::dirMode( 0700 ), true );
        exec( 'tar -xzf ' . escapeshellarg( $archive ) . ' -C ' . escapeshellarg( $tmp ) . ' ' . escapeshellarg( $inner ) . ' 2>&1', $out, $code );
        if ( $code !== 0 || !file_exists( "$tmp/$inner" ) )
            throw new RuntimeException( 'cannot unpack ' . basename( $archive ) . ': ' . implode( ' ', $out ) );
        return "$tmp/$inner";
    }

    /** A restore of the dump into a throwaway database: array( ok, detail ). */
    protected static function restoreTest( array $dump, $db )
    {
        switch ( $dump['engine'] )
        {
            case 'sqlite':
                $sqlite = new SQLite3( $dump['path'], SQLITE3_OPEN_READONLY );
                $check = (string)$sqlite->querySingle( 'PRAGMA integrity_check' );
                $tables = (int)$sqlite->querySingle( "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table'" );
                $sqlite->close();
                return array( 'ok' => $check === 'ok' && $tables > 0, 'detail' => "integrity_check $check, $tables table(s)" );
            case 'mysql':
            case 'postgresql':
                if ( expKernelTableSchema::type( $db ) !== $dump['engine'] )
                    return array( 'ok' => null, 'detail' => "a {$dump['engine']} dump, checked only on a {$dump['engine']} installation" );
                $name = preg_replace( '/[^a-z0-9_]/', '', strtolower( (string)$db->DB ) ) . '_capverify_' . getmypid();
                try
                {
                    $db->query( "CREATE DATABASE $name" );
                }
                catch ( Throwable $e )
                {
                    return array( 'ok' => null, 'detail' => 'the account may not create a throwaway database: ' . $e->getMessage() );
                }
                $err = '';
                if ( $dump['engine'] === 'mysql' )
                {
                    // a dump made with --databases names its database (CREATE DATABASE, USE): loaded as it is, it would
                    // write into the live database, not the throwaway one
                    $clean = self::withoutDatabaseStatements( $dump['path'] );
                    $cnf = expUpgradeBackup::mysqlDefaultsFile( $db, dirname( $dump['path'] ) . '/.verify.cnf' );
                    $code = expUpgradeBackup::runWithEnv( 'mysql --defaults-extra-file=' . escapeshellarg( $cnf ) . ' ' . escapeshellarg( $name ) . ' < ' . escapeshellarg( $clean ), array(), $err );
                    $count = $code === 0 ? self::countTables( $db, "SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = '$name'" ) : 0;
                    @unlink( $cnf );
                }
                else
                {
                    $code = expUpgradeBackup::runWithEnv( 'psql -X -q -v ON_ERROR_STOP=0 -h ' . escapeshellarg( $db->Server ?: 'localhost' ) . ( $db->Port ? ' -p ' . (int)$db->Port : '' )
                                                          . ' -U ' . escapeshellarg( (string)$db->User ) . ' -d ' . escapeshellarg( $name ) . ' -f ' . escapeshellarg( $dump['path'] ),
                                                          array( 'PGPASSWORD' => (string)$db->Password ), $err );
                    $count = $code === 0 ? self::countPostgresTables( $db, $name ) : 0;
                }
                try
                {
                    $db->query( "DROP DATABASE $name" );
                }
                catch ( Throwable $e )
                {
                }
                return array( 'ok' => $code === 0 && $count > 0, 'detail' => $code === 0 ? "restored into a throwaway database: $count table(s)" : 'the restore failed: ' . mb_substr( trim( $err ), 0, 300 ) );
            case 'mongodb':
                return array( 'ok' => null, 'detail' => 'MongoDB dump present; restore it with mongorestore to check' );
        }
        return array( 'ok' => null, 'detail' => 'no restore test for ' . $dump['engine'] );
    }

    /**
     * A copy of a MySQL dump without the statements that choose or create a database (CREATE DATABASE, USE), so it
     * loads into the database the client names. Line by line: a dump can be large.
     *
     * @param string $file
     * @return string the copy (next to the file)
     */
    public static function withoutDatabaseStatements( $file )
    {
        $out = $file . '.nodb.sql';
        $in = fopen( $file, 'r' );
        $to = fopen( $out, 'w' );
        if ( !$in || !$to )
            throw new RuntimeException( 'cannot prepare the dump for the restore test' );
        while ( ( $line = fgets( $in ) ) !== false )
        {
            if ( preg_match( '/^\s*(CREATE DATABASE|USE\s+`?[^\s;]+`?\s*;)/i', $line ) || preg_match( '#^/\*!\d+ CREATE DATABASE#i', $line ) )
                continue;
            fwrite( $to, $line );
        }
        fclose( $in );
        fclose( $to );
        return $out;
    }

    protected static function countTables( $db, $sql )
    {
        $rows = expKernelTableSchema::tryArrayQuery( $db, $sql );
        return $rows ? (int)array_change_key_case( (array)$rows[0] )['n'] : 0;
    }

    /** The tables of another PostgreSQL database: asked through psql (a connection is bound to its database). */
    protected static function countPostgresTables( $db, $name )
    {
        $p = proc_open( array( 'psql', '-X', '-A', '-t', '-h', $db->Server ?: 'localhost', '-U', (string)$db->User, '-d', $name, '-c',
                               "SELECT count(*) FROM pg_tables WHERE schemaname = 'public'" ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ),
                        $pipes, null, array( 'PGPASSWORD' => (string)$db->Password ) + getenv() );
        if ( !is_resource( $p ) )
            return 0;
        $out = stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $p );
        return (int)trim( (string)$out );
    }

    /**
     * Which captions of exp:upgrade a prune removes: beyond the newest CaptionKeep, older than CaptionKeepDays, and not
     * a pre or post caption of the last CaptionKeepUpgrades upgrades. git_manager's own captions are never pruned
     * here (git_manager's MaxBackups does that). Pure.
     *
     * @param array[] $captions as all() returns them, newest first
     * @param int $keep
     * @param int $keepDays
     * @param int $keepUpgrades
     * @param int|null $now
     * @return array[] the captions to remove
     */
    public static function prunable( array $captions, $keep, $keepDays, $keepUpgrades, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $ours = array_values( array_filter( $captions, function ( $c ) { return !empty( $c['upgrade_part'] ); } ) );
        $protected = array();
        $upgrades = array();
        foreach ( $ours as $c )
        {
            if ( !in_array( $c['kind'], array( 'pre', 'post' ), true ) )
                continue;
            $key = isset( $c['upgrade']['from'] ) ? $c['upgrade']['from'] . '>' . $c['upgrade']['to'] . '@' . date( 'Ymd', $c['created'] ) : $c['id'];
            if ( !isset( $upgrades[$key] ) && count( $upgrades ) >= (int)$keepUpgrades )
                continue;
            $upgrades[$key] = true;
            $protected[$c['id']] = true;
        }
        $out = array();
        foreach ( $ours as $i => $c )
        {
            if ( $i < (int)$keep || isset( $protected[$c['id']] ) )
                continue;
            if ( ( $now - $c['created'] ) > (int)$keepDays * 86400 )
                $out[] = $c;
        }
        return $out;
    }

    /**
     * Removes (with apply) or lists the captions a prune removes, by the settings of upgrade.ini [UpgradeSettings].
     *
     * @param bool $apply
     * @param string|null $backupDir
     * @param array $settings keep, keep-days, keep-upgrades (the INI's when not given)
     * @return array removed => ids, failed => messages
     */
    public static function prune( $apply, $backupDir = null, array $settings = array() )
    {
        $ini = eZINI::instance( 'upgrade.ini' );
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'UpgradeSettings', $name ) ? (int)$ini->variable( 'UpgradeSettings', $name ) : $default;
        };
        $settings += array( 'keep' => $get( 'CaptionKeep', 10 ), 'keep-days' => $get( 'CaptionKeepDays', 90 ), 'keep-upgrades' => $get( 'CaptionKeepUpgrades', 3 ) );
        $result = array( 'removed' => array(), 'failed' => array(), 'settings' => $settings );
        foreach ( self::prunable( self::all( $backupDir ), $settings['keep'], $settings['keep-days'], $settings['keep-upgrades'] ) as $c )
        {
            $result['removed'][] = $c['id'];
            if ( !$apply )
                continue;
            $manager = $c['source'] === 'git_manager' ? self::manager() : null;
            $ok = $manager ? !empty( $manager->deleteCaption( $c['id'] )['success'] ) : eZDir::recursiveDelete( $c['dir'], false );
            if ( !$ok )
                $result['failed'][] = $c['id'];
            else
                expUpgrade::audit( 'system.upgrade.caption', array( 'object' => array( 'type' => 'caption', 'id' => $c['id'] ), 'verb' => 'prune', 'result' => 'success' ) );
        }
        return $result;
    }
}
