<?php
/**
 * File containing the expClusterSetup class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The one-node DFS cluster of an installation, switched on and off by exp:cluster:enable and exp:cluster:disable
 * (doc/eng-us/features/6.0/cluster-commands.md): the share folder, the kernel's cluster tables in the site's own
 * database, a marked block of settings/override/file.ini.append.php, config.cluster.php for the cluster gateway, and
 * the stored files moved in (bin/php/clusterize.php) or back (clusterize.php -u). The web server's rewrite rule for
 * the gateway is printed, not written: the web server's configuration belongs to whoever runs it (Plesk, a vhost file).
 *
 * The pure parts (the plan, the texts) are static and need no database; enable() and disable() do the work.
 */
class expClusterSetup
{
    const MARK_INI = 'Exponential cluster';
    const MARK_PHP = '// BEGIN Exponential cluster';

    /**
     * The backends: engine => array( classes in order of preference, gateway, schema engine directory, default port,
     * optional schema file name in that directory, cluster_dfs_schema.sql when none is given ).
     */
    public static function backends()
    {
        return array(
            'mysqli' => array( 'classes' => array( 'eZDFSFileHandlerMySQLiBackend' ), 'gateway' => 'dfsmysqli', 'sql' => 'mysql', 'port' => 3306 ),
            'postgresql' => array( 'classes' => array( 'eZDFSFileHandlerPostgresqlBackend', 'expDFSFileHandlerPostgresqlBackend' ), 'gateway' => 'dfspostgresql',
                                   'sql' => 'postgresql', 'port' => 5432 ),
            // one file on one machine: the cluster of one node this command makes, never several web servers
            'sqlite' => array( 'classes' => array( 'expDFSFileHandlerSQLiteBackend' ), 'gateway' => 'dfssqlite', 'sql' => 'sqlite', 'port' => 0 ),
            // collections, not tables: the schema is a JSON file the backend creates (createSchema())
            'mongodb' => array( 'classes' => array( 'expDFSFileHandlerMongoDBBackend' ), 'gateway' => 'dfsmongodb', 'sql' => 'mongodb', 'port' => 27017,
                                'schema' => 'cluster_dfs_schema.json' ),
        );
    }

    /** The engine of a [DatabaseSettings] DatabaseImplementation (pure), '' when no cluster backend serves it. */
    public static function engineOf( $implementation )
    {
        $engines = array( 'ezmysqli' => 'mysqli', 'ezmysql' => 'mysqli', 'mysqli' => 'mysqli', 'mysql' => 'mysqli',
                          'ezpostgresql' => 'postgresql', 'postgresql' => 'postgresql', 'pgsql' => 'postgresql',
                          'ezsqlite3' => 'sqlite', 'sqlite3' => 'sqlite',
                          'mongodb' => 'mongodb', 'expmongodb' => 'mongodb', 'mongo' => 'mongodb' );
        $implementation = strtolower( (string)$implementation );
        return isset( $engines[$implementation] ) ? $engines[$implementation] : '';
    }

    /** The name of an engine for people (pure). */
    public static function engineName( $engine )
    {
        $names = array( 'mysqli' => 'MySQL/MariaDB', 'postgresql' => 'PostgreSQL', 'sqlite' => 'SQLite', 'mongodb' => 'MongoDB' );
        return isset( $names[$engine] ) ? $names[$engine] : (string)$engine;
    }

    /** The share by default (pure): the folder dfs next to the installation, /var/www/vhosts/<site>/dfs for <site>/httpdocs. */
    public static function defaultShare( $root )
    {
        return rtrim( dirname( rtrim( (string)$root, '/' ) ), '/' ) . '/dfs';
    }

    /**
     * The facts of the installation (reads the settings): per siteaccess its database, VarDir and file handler; the
     * default siteaccess; the backends this kernel has (a class, its schema and its gateway). @return array
     */
    public static function facts( $root )
    {
        $ini = eZINI::instance( 'site.ini' );
        $facts = array( 'default' => (string)$ini->variable( 'SiteSettings', 'DefaultAccess' ), 'siteaccesses' => array(), 'backends' => array() );
        foreach ( (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) as $sa )
        {
            $si = eZSiteAccess::getIni( $sa, 'site.ini' );
            $fi = eZSiteAccess::getIni( $sa, 'file.ini' );
            $db = array();
            foreach ( array( 'DatabaseImplementation', 'Server', 'Port', 'Socket', 'User', 'Password', 'Database' ) as $k )
                $db[$k] = $si->hasVariable( 'DatabaseSettings', $k ) ? (string)$si->variable( 'DatabaseSettings', $k ) : '';
            $facts['siteaccesses'][$sa] = array( 'db' => $db, 'var' => (string)$si->variable( 'FileSettings', 'VarDir' ),
                                                 'handler' => (string)$fi->variable( 'ClusteringSettings', 'FileHandler' ) );
        }
        foreach ( self::backends() as $engine => $b )
            foreach ( $b['classes'] as $class )
                if ( !isset( $facts['backends'][$engine] ) && class_exists( $class )
                     && is_file( $root . '/kernel/sql/' . $b['sql'] . '/' . self::schemaFile( $b ) ) && is_file( $root . '/kernel/clustering/' . $b['gateway'] . '.php' ) )
                    $facts['backends'][$engine] = $class;
        return $facts;
    }

    /**
     * The plan of a switch on (pure): every siteaccess on one database, its engine's backend in this kernel.
     * @return array|string engine, backend_class, gateway, db, vardirs (VarDir => siteaccess), or why not
     */
    public static function plan( array $facts, $wanted = 'auto' )
    {
        if ( empty( $facts['siteaccesses'] ) )
            return 'the installation has no siteaccesses';
        $default = isset( $facts['siteaccesses'][$facts['default']] ) ? $facts['default'] : key( $facts['siteaccesses'] );
        $db = $facts['siteaccesses'][$default]['db'];
        $key = function ( array $d ) { return implode( '|', array( $d['DatabaseImplementation'], $d['Server'], $d['Port'], $d['Socket'], $d['Database'], $d['User'] ) ); };
        $vardirs = array();
        foreach ( $facts['siteaccesses'] as $sa => $s )
        {
            if ( $key( $s['db'] ) !== $key( $db ) )
                return 'the siteaccesses use more than one database; a one-node cluster needs them on one';
            if ( !isset( $vardirs[$s['var']] ) )
                $vardirs[$s['var']] = (string)$sa;
        }
        $vardirs[$facts['siteaccesses'][$default]['var']] = (string)$default;
        $impl = (string)$db['DatabaseImplementation'];
        $engine = self::engineOf( $impl );
        if ( $engine === '' )
            return 'the database (' . ( $impl !== '' ? $impl : 'none' ) . ') has no cluster backend; a one-node cluster needs '
                   . implode( ', ', array_map( array( __CLASS__, 'engineName' ), array_slice( array_keys( self::backends() ), 0, -1 ) ) )
                   . ' or ' . self::engineName( array_slice( array_keys( self::backends() ), -1 )[0] );
        if ( $wanted !== 'auto' && $wanted !== $engine )
            return 'the installation runs on ' . self::engineName( $engine ) . ', not ' . $wanted;
        if ( !isset( $facts['backends'][$engine] ) )
            return 'this Exponential version has no ' . ( $engine === 'mysqli' ? 'MySQL' : self::engineName( $engine ) ) . ' cluster backend' . ( $engine === 'mysqli' ? '' : ' yet' );
        // a SQLite database is a file: no account; a MongoDB server may run without authentication
        if ( $db['Database'] === '' || ( $db['User'] === '' && !in_array( $engine, array( 'sqlite', 'mongodb' ), true ) ) )
            return 'the database settings are incomplete';
        $b = self::backends()[$engine];
        return array( 'engine' => $engine, 'backend_class' => (string)$facts['backends'][$engine], 'gateway' => $b['gateway'], 'sql' => $b['sql'],
                      'db' => $db, 'vardirs' => $vardirs, 'default' => (string)$default,
                      'host' => $b['port'] === 0 ? '' : ( $db['Server'] !== '' ? $db['Server'] : 'localhost' ),
                      'port' => $b['port'] === 0 ? 0 : ( $db['Port'] !== '' ? (int)$db['Port'] : $b['port'] ),
                      'socket' => $b['port'] !== 0 && $db['Socket'] !== '' && $db['Socket'] !== 'disabled' ? $db['Socket'] : '' );
    }

    /** The schema file name of a backend (pure), in kernel/sql/<sql>/. */
    public static function schemaFile( array $backend )
    {
        return isset( $backend['schema'] ) ? (string)$backend['schema'] : 'cluster_dfs_schema.sql';
    }

    /** Whether a value can stand in the INI block (pure): one line, never the end of the file's PHP comment. */
    public static function iniSafe( $value )
    {
        return is_string( $value ) && !preg_match( '/[\r\n\x00]/', $value ) && strpos( $value, '*/' ) === false && strpos( $value, '?>' ) === false;
    }

    /** The INI block (pure). */
    public static function iniBlock( array $plan, $share )
    {
        $lines = array( '# BEGIN ' . self::MARK_INI . ' (exp:cluster:enable; exp:cluster:disable takes it out)',
                        '# exp-cluster backend=' . $plan['engine'] . ' share=' . $share,
                        '[ClusteringSettings]', 'FileHandler=eZDFSFileHandler', '',
                        '[eZDFSClusteringSettings]', 'MountPointPath=' . $share, 'DBBackend=' . $plan['backend_class'], 'DFSBackend=eZDFSFileHandlerDFSBackend',
                        'DBHost=' . $plan['host'], 'DBPort=' . ( (int)$plan['port'] > 0 ? (int)$plan['port'] : '' ), 'DBSocket=' . $plan['socket'], 'DBName=' . $plan['db']['Database'],
                        'DBUser=' . $plan['db']['User'], 'DBPassword=' . $plan['db']['Password'], 'MetaDataTableNameCache=ezdfsfile_cache',
                        '# END ' . self::MARK_INI );
        return implode( "\n", $lines ) . "\n";
    }

    /** The regular expression of a cluster block (pure): ours, or Katalyst's (a hosting panel's). */
    protected static function blockRe( $mark )
    {
        return '/^# BEGIN ' . preg_quote( $mark, '/' ) . '\b.*?^# END ' . preg_quote( $mark, '/' ) . '\R?/ms';
    }

    /**
     * A file.ini.append.php with the block put in front of the closing comment, replaced, or taken out ($block '')
     * (pure); a new file gets the INI header; null when there is no closing comment to put it before.
     */
    public static function iniText( $text, $block )
    {
        $rest = $text === null ? '' : preg_replace( self::blockRe( self::MARK_INI ), '', (string)$text );
        if ( $block === '' )
            return $rest;
        if ( trim( $rest ) === '' )
            $rest = "<?php /* #?ini charset=\"utf-8\"?\n\n*/ ?>\n";
        $pos = strrpos( $rest, '*/' );
        if ( $pos === false )
            return null;
        $before = substr( $rest, 0, $pos );
        if ( $before !== '' && substr( $before, -1 ) !== "\n" )
            $before .= "\n";
        return $before . $block . substr( $rest, $pos );
    }

    /** Whether a file.ini override has our block (pure). */
    public static function iniOurs( $text )
    {
        return (bool)preg_match( self::blockRe( self::MARK_INI ), (string)$text );
    }

    /** Whether a file.ini override has cluster settings outside our block: its own, or a hosting panel's (pure). */
    public static function iniForeign( $text )
    {
        $rest = preg_replace( self::blockRe( self::MARK_INI ), '', (string)$text );
        return (bool)preg_match( '/^\s*\[(ClusteringSettings|eZDFSClusteringSettings|eZDBFileHandlerSettings)\]/mi', $rest );
    }

    /** The share of our block (pure), or ''. */
    public static function iniShare( $text )
    {
        return preg_match( '#^\# exp-cluster backend=\S+ share=(/\S+)$#m', (string)$text, $m ) ? $m[1] : '';
    }

    /** config.cluster.php (pure): every value var_export()ed. */
    public static function gatewayText( array $plan, $share )
    {
        $defs = array( 'CLUSTER_STORAGE_BACKEND' => $plan['gateway'], 'CLUSTER_STORAGE_HOST' => $plan['host'], 'CLUSTER_STORAGE_PORT' => (int)$plan['port'],
                       'CLUSTER_STORAGE_USER' => $plan['db']['User'], 'CLUSTER_STORAGE_PASS' => $plan['db']['Password'], 'CLUSTER_STORAGE_DB' => $plan['db']['Database'],
                       'CLUSTER_STORAGE_CHARSET' => 'utf8', 'CLUSTER_MOUNT_POINT_PATH' => $share, 'CLUSTER_METADATA_TABLE_CACHE' => 'ezdfsfile_cache' );
        $lines = array( '<?php', self::MARK_PHP . ': the one-node cluster (exp:cluster:enable; exp:cluster:disable removes this file).',
                        '// The cluster gateway (index_cluster.php) reads these; the kernel reads settings/override/file.ini.append.php.' );
        foreach ( $defs as $k => $v )
            $lines[] = "define( '" . $k . "', " . var_export( $v, true ) . ' );';
        $lines[] = '// END Exponential cluster';
        return implode( "\n", $lines ) . "\n";
    }

    /** Whether a config.cluster.php is ours (pure). */
    public static function gatewayOurs( $text )
    {
        return is_string( $text ) && strpos( $text, self::MARK_PHP ) !== false;
    }

    /** The web server rules of the gateway (pure), to be put in front of the site's own rules. */
    public static function webServerRules()
    {
        return array(
            'apache-vhost' => 'RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_cluster.php [PT,END]',
            'apache-htaccess' => "RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_cluster.php [L]\nRewriteRule ^index_cluster\\.php - [L]",
            'nginx' => 'location ~ ^/var/([^/]+/)?storage/images(-versioned)?/ { rewrite ^ /index_cluster.php last; }',
        );
    }

    /** The cluster tables' statements of a schema file (pure): CREATE TABLE / INDEX / SEQUENCE, with the name each creates. */
    public static function schemaStatements( $sql )
    {
        $out = array();
        foreach ( preg_split( '/;\s*(\n|$)/', (string)$sql ) as $statement )
        {
            $statement = trim( preg_replace( '/^\s*(--|#).*$/m', '', $statement ) );
            if ( $statement !== '' && preg_match( '/^CREATE\s+(TABLE|INDEX|UNIQUE\s+INDEX|SEQUENCE)\s+(IF\s+NOT\s+EXISTS\s+)?(\w+)/i', $statement, $m ) )
                $out[] = array( 'kind' => strtoupper( preg_replace( '/\s+/', ' ', $m[1] ) ), 'name' => $m[3], 'sql' => $statement );
        }
        return $out;
    }

    // ------------------------------------------------------------------------------------------------ the work

    /** Whether a table answers. */
    protected static function hasTable( eZDBInterface $db, $table )
    {
        try
        {
            $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            $r = $db->arrayQuery( 'SELECT 1 FROM ' . $table . ' WHERE 1 = 0' );
            return $r !== false;
        }
        catch ( Exception $e )
        {
            return false;
        }
        finally
        {
            $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        }
    }

    /** Writes a file through a temporary file and a rename, after a backup of the old one. @return string|null an error */
    protected static function writeFile( $path, $text, $backupDir )
    {
        if ( is_link( $path ) )
            return 'a link: ' . $path;
        if ( is_file( $path ) )
        {
            if ( !is_dir( $backupDir ) && !mkdir( $backupDir, eZDir::dirMode( 0700 ), true ) )
                return 'cannot make the backup folder ' . $backupDir;
            if ( !copy( $path, $backupDir . '/' . basename( $path ) ) )
                return 'cannot back up ' . $path;
        }
        if ( $text === null )
            return !is_file( $path ) || unlink( $path ) ? null : 'cannot remove ' . $path;
        if ( !is_dir( dirname( $path ) ) && !mkdir( dirname( $path ), eZDir::dirMode( 0755 ), true ) )
            return 'cannot make ' . dirname( $path );
        $tmp = dirname( $path ) . '/.' . basename( $path ) . '.exp-cluster.' . getmypid();
        if ( file_put_contents( $tmp, $text ) === false || !chmod( $tmp, eZFile::fileMode( 0640 ) ) || !rename( $tmp, $path ) )
        {
            @unlink( $tmp );
            return 'cannot write ' . $path;
        }
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $path, true );
        return null;
    }

    /** Runs bin/php/clusterize.php for one siteaccess in a process of its own (it reads the new settings). @return int exit code */
    protected static function clusterize( $root, $siteAccess, $back, $out )
    {
        $argv = array( PHP_BINARY, '-d', 'memory_limit=1024M', $root . '/bin/php/clusterize.php', '-n', '--siteaccess=' . $siteAccess );
        if ( $back )
            array_splice( $argv, 4, 0, array( '-u' ) );
        $p = proc_open( $argv, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $root );
        if ( !is_resource( $p ) )
            return 127;
        fclose( $pipes[0] );
        $text = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $p );
        if ( $code !== 0 && is_callable( $out ) )
            $out( substr( trim( $text ), -600 ) );
        return $code;
    }

    /** The INI caches cleared: a new override file is not in the cached file list. */
    protected static function clearIni()
    {
        if ( class_exists( 'eZCache' ) )
            eZCache::clearByTag( 'ini' );
    }

    /**
     * Switches the cluster on. $o: share, backend (auto or an engine of backends()), move (bool), apply (bool). $say(line)
     * prints. @return array ok, lines (what was or would be done), error
     */
    public static function enable( $root, array $o, $say )
    {
        $o += array( 'share' => '', 'backend' => 'auto', 'move' => true, 'apply' => false );
        $root = rtrim( (string)$root, '/' );
        $share = $o['share'] !== '' ? rtrim( (string)$o['share'], '/' ) : self::defaultShare( $root );
        if ( $share === '' || $share[0] !== '/' || preg_match( '#/\.\.?(/|$)#', $share ) || strpos( $share . '/', $root . '/' ) === 0 )
            return array( 'ok' => false, 'error' => 'the share must be an absolute path outside the installation' );
        $iniPath = $root . '/settings/override/file.ini.append.php';
        $gwPath = $root . '/config.cluster.php';
        $iniText = is_file( $iniPath ) ? (string)file_get_contents( $iniPath ) : null;
        $gwText = is_file( $gwPath ) ? (string)file_get_contents( $gwPath ) : null;
        if ( self::iniForeign( (string)$iniText ) || ( $gwText !== null && !self::gatewayOurs( $gwText ) ) )
            return array( 'ok' => false, 'error' => 'the installation has cluster settings of its own (file.ini or config.cluster.php); they are left alone' );
        $facts = self::facts( $root );
        if ( self::iniOurs( (string)$iniText ) )
        {
            $on = true;
            foreach ( $facts['siteaccesses'] as $s )
                $on = $on && $s['handler'] === 'eZDFSFileHandler';
            if ( $on )
                return array( 'ok' => true, 'unchanged' => true, 'error' => '', 'share' => self::iniShare( $iniText ) );
        }
        $plan = self::plan( $facts, (string)$o['backend'] );
        if ( is_string( $plan ) )
            return array( 'ok' => false, 'error' => $plan );
        foreach ( array( $plan['host'], $plan['socket'], $plan['db']['Database'], $plan['db']['User'], $plan['db']['Password'], $plan['backend_class'], $share ) as $v )
            if ( !self::iniSafe( (string)$v ) )
                return array( 'ok' => false, 'error' => 'a database setting cannot be written into file.ini safely (a line break, */ or ?>)' );
        $newIni = self::iniText( $iniText, self::iniBlock( $plan, $share ) );
        if ( $newIni === null )
            return array( 'ok' => false, 'error' => 'settings/override/file.ini.append.php has no closing comment to put the settings before' );
        $say( 'backend ' . $plan['engine'] . ' (' . $plan['backend_class'] . '), share ' . $share . ', database ' . $plan['db']['Database'] );
        $say( 'var directories to move: ' . implode( ', ', array_map( function ( $v, $sa ) { return $v . ' (' . $sa . ')'; }, array_keys( $plan['vardirs'] ), $plan['vardirs'] ) ) );
        if ( !$o['apply'] )
            return array( 'ok' => true, 'dry_run' => true, 'error' => '', 'share' => $share, 'plan' => $plan );

        // the share, with the marker that says it is ours
        $marker = $share . '/.exp-cluster.json';
        if ( is_link( $share ) || ( file_exists( $share ) && !is_dir( $share ) ) )
            return array( 'ok' => false, 'error' => 'a file or link is where the share goes: ' . $share );
        $owned = is_file( $marker ) && !is_link( $marker );
        if ( is_dir( $share ) && !$owned && count( (array)scandir( $share ) ) > 2 )
            return array( 'ok' => false, 'error' => 'the folder ' . $share . ' holds files that are not the cluster\'s' );
        if ( !is_dir( $share ) && !@mkdir( $share, eZDir::dirMode( 0750 ) ) )
            return array( 'ok' => false, 'error' => 'the share ' . $share . ' could not be made (does its parent folder exist?)' );
        @file_put_contents( $marker, json_encode( array( 'v' => 1, 'state' => 'preparing', 'by' => 'exp:cluster:enable' ) ) );
        $say( 'PASS share ' . $share );

        // the tables, in the site's own database (emptied when an earlier switch of ours left them)
        if ( $plan['engine'] === 'mongodb' )
        {
            $count = self::mongoCollections( $root, $plan, $owned );
            if ( is_string( $count ) )
                return array( 'ok' => false, 'error' => $count );
            $say( 'PASS collections ezdfsfile, ezdfsfile_cache' );
            return self::enableSettings( $root, $plan, $share, $o, $say, $iniPath, $gwPath, $iniText, $gwText, $newIni, $marker,
                                         function () use ( $root, $plan ) { return self::mongoCollections( $root, $plan, true, false ); } );
        }
        $db = eZDB::instance();
        $schema = (string)@file_get_contents( $root . '/kernel/sql/' . $plan['sql'] . '/cluster_dfs_schema.sql' );
        foreach ( self::schemaStatements( $schema ) as $st )
        {
            if ( $st['kind'] === 'TABLE' && self::hasTable( $db, $st['name'] ) )
                continue;
            if ( $st['kind'] !== 'TABLE' && self::hasTable( $db, 'ezdfsfile' ) && self::hasTable( $db, 'ezdfsfile_cache' ) )
                continue;
            if ( $db->query( $st['sql'] ) === false )
                return array( 'ok' => false, 'error' => 'creating ' . $st['name'] . ' failed: ' . $db->errorMessage() );
        }
        $rows = (array)$db->arrayQuery( 'SELECT COUNT(*) AS n FROM ezdfsfile' );
        if ( (int)( $rows[0]['n'] ?? 0 ) > 0 )
        {
            if ( !$owned )
                return array( 'ok' => false, 'error' => 'the cluster tables hold rows that are not from a switch of ours; nothing was changed' );
            $db->query( 'DELETE FROM ezdfsfile' );
            $db->query( 'DELETE FROM ezdfsfile_cache' );
        }
        $say( 'PASS tables ezdfsfile, ezdfsfile_cache' );
        return self::enableSettings( $root, $plan, $share, $o, $say, $iniPath, $gwPath, $iniText, $gwText, $newIni, $marker,
                                     function () use ( $db ) { $n = (array)$db->arrayQuery( 'SELECT COUNT(*) AS n FROM ezdfsfile' ); return (int)( $n[0]['n'] ?? 0 ); } );
    }

    /**
     * The MongoDB collections of the cluster in the site's database: created (with their indexes) when missing,
     * emptied when an earlier switch of ours ($owned) left documents. With $prepare false only the documents are
     * counted. @return int the documents of ezdfsfile, or why not (a string)
     */
    protected static function mongoCollections( $root, array $plan, $owned, $prepare = true )
    {
        if ( !extension_loaded( 'mongodb' ) || !class_exists( 'expDFSFileHandlerMongoDBBackend' ) )
            return 'the PHP extension mongodb is not loaded';
        $database = (string)$plan['db']['Database'];
        list( $uri, $options ) = expDFSFileHandlerMongoDBBackend::connectionUri( array( 'host' => $plan['host'], 'port' => $plan['port'], 'socket' => $plan['socket'],
                                                                                       'user' => $plan['db']['User'], 'password' => $plan['db']['Password'], 'dbname' => $database ) );
        try
        {
            $manager = new MongoDB\Driver\Manager( $uri, $options );
            if ( $prepare )
                expDFSFileHandlerMongoDBBackend::createSchema( $manager, $database, $root . '/kernel/sql/mongodb/cluster_dfs_schema.json' );
            $count = function ( $collection ) use ( $manager, $database )
            {
                $r = $manager->executeCommand( $database, new MongoDB\Driver\Command( array( 'count' => $collection ) ) )->toArray();
                return (int)( $r[0]->n ?? 0 );
            };
            $n = $count( 'ezdfsfile' );
            if ( $prepare && $n + $count( 'ezdfsfile_cache' ) > 0 )
            {
                if ( !$owned )
                    return 'the cluster collections hold documents that are not from a switch of ours; nothing was changed';
                foreach ( array( 'ezdfsfile', 'ezdfsfile_cache' ) as $collection )
                {
                    $bulk = new MongoDB\Driver\BulkWrite();
                    $bulk->delete( array(), array( 'limit' => 0 ) );
                    $manager->executeBulkWrite( $database . '.' . $collection, $bulk );
                }
                $n = 0;
            }
            return $n;
        }
        catch ( Exception $e )
        {
            return 'the MongoDB cluster collections: ' . $e->getMessage();
        }
    }

    /**
     * The second half of enable(): the settings (put back on a failure), the stored files moved in, the marker.
     * $count() counts the files of the cluster. @return array as enable()
     */
    protected static function enableSettings( $root, array $plan, $share, array $o, $say, $iniPath, $gwPath, $iniText, $gwText, $newIni, $marker, $count )
    {
        // the settings; from here on a failure puts them back
        $backup = $root . '/var/backup/cluster/' . date( 'Ymd-His' );
        $restore = function () use ( $iniPath, $gwPath, $iniText, $gwText, $backup ) {
            self::writeFile( $iniPath, $iniText, $backup . '-restore' );
            self::writeFile( $gwPath, $gwText, $backup . '-restore' );
            self::clearIni();
        };
        foreach ( array( array( $iniPath, $newIni ), array( $gwPath, self::gatewayText( $plan, $share ) ) ) as $w )
            if ( ( $e = self::writeFile( $w[0], $w[1], $backup ) ) !== null )
            {
                $restore();
                return array( 'ok' => false, 'error' => $e . '; nothing was changed' );
            }
        self::clearIni();
        $say( 'PASS settings/override/file.ini.append.php and config.cluster.php (backup: ' . $backup . ')' );
        if ( $o['move'] )
            foreach ( $plan['vardirs'] as $var => $sa )
            {
                $code = self::clusterize( $root, $sa, false, $say );
                if ( $code !== 0 )
                {
                    $restore();
                    return array( 'ok' => false, 'error' => 'clusterize.php stopped (exit ' . $code . ' for ' . $sa . '); the settings were put back' );
                }
                $say( 'PASS files of ' . $var . ' moved in (clusterize.php --siteaccess=' . $sa . ')' );
            }
        $files = $count();
        $files = is_int( $files ) ? $files : 0;
        @file_put_contents( $marker, json_encode( array( 'v' => 1, 'state' => 'cluster', 'since' => time(), 'backend' => $plan['engine'], 'files' => $files,
                                                         'by' => 'exp:cluster:enable' ) ) );
        return array( 'ok' => true, 'error' => '', 'share' => $share, 'files' => $files, 'plan' => $plan );
    }

    /** Switches the cluster off. $o: move (bool), apply (bool). @return array ok, error */
    public static function disable( $root, array $o, $say )
    {
        $o += array( 'move' => true, 'apply' => false );
        $root = rtrim( (string)$root, '/' );
        $iniPath = $root . '/settings/override/file.ini.append.php';
        $gwPath = $root . '/config.cluster.php';
        $iniText = is_file( $iniPath ) ? (string)file_get_contents( $iniPath ) : null;
        $gwText = is_file( $gwPath ) ? (string)file_get_contents( $gwPath ) : null;
        if ( !self::iniOurs( (string)$iniText ) )
            return self::iniForeign( (string)$iniText )
                ? array( 'ok' => false, 'error' => 'the cluster settings are not from exp:cluster:enable (the installation\'s own, or a hosting panel\'s); they are left alone' )
                : array( 'ok' => true, 'unchanged' => true, 'error' => '' );
        $facts = self::facts( $root );
        $vardirs = array();
        foreach ( $facts['siteaccesses'] as $sa => $s )
            if ( !isset( $vardirs[$s['var']] ) || $sa === $facts['default'] )
                $vardirs[$s['var']] = (string)$sa;
        $share = self::iniShare( $iniText );
        $say( 'share ' . $share . '; var directories to move back: ' . implode( ', ', array_keys( $vardirs ) ) );
        if ( !$o['apply'] )
            return array( 'ok' => true, 'dry_run' => true, 'error' => '' );
        if ( $o['move'] )
            foreach ( $vardirs as $var => $sa )
            {
                $code = self::clusterize( $root, $sa, true, $say );
                if ( $code !== 0 )
                    return array( 'ok' => false, 'error' => 'clusterize.php -u stopped (exit ' . $code . ' for ' . $sa . '); the cluster stays on' );
                $say( 'PASS files of ' . $var . ' moved back (clusterize.php -u --siteaccess=' . $sa . ')' );
            }
        $backup = $root . '/var/backup/cluster/' . date( 'Ymd-His' );
        if ( ( $e = self::writeFile( $iniPath, (string)self::iniText( $iniText, '' ), $backup ) ) !== null )
            return array( 'ok' => false, 'error' => $e );
        if ( $gwText !== null && self::gatewayOurs( $gwText ) && ( $e = self::writeFile( $gwPath, null, $backup ) ) !== null )
            return array( 'ok' => false, 'error' => $e );
        self::clearIni();
        if ( $share !== '' && is_dir( $share ) && !is_link( $share . '/.exp-cluster.json' ) )
            @file_put_contents( $share . '/.exp-cluster.json', json_encode( array( 'v' => 1, 'state' => 'single', 'until' => time(), 'by' => 'exp:cluster:disable' ) ) );
        $say( 'PASS the cluster settings and config.cluster.php are out (backup: ' . $backup . '); the share and the tables are kept' );
        return array( 'ok' => true, 'error' => '' );
    }
}
