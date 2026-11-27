#!/usr/bin/env php
<?php
/**
 * @description One-command installation, SQLite by default, no kickstart.ini needed (a dry run unless --apply)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$rootDir = dirname( dirname( __DIR__ ) );
chdir( $rootDir );

$argvIn = array_slice( $GLOBALS['argv'], 1 );

/** Defaults per database type: kickstart Type, port and database name. */
$databaseTypes = array(
    'sqlite'  => array( 'type' => 'sqlite3', 'port' => '',      'name' => 'exponential.db', 'user' => '' ),
    'mysql'   => array( 'type' => 'mysqli',  'port' => '3306',  'name' => 'exponential',    'user' => 'root' ),
    'pgsql'   => array( 'type' => 'pgsql',   'port' => '5432',  'name' => 'exponential',    'user' => 'postgres' ),
    'mongodb' => array( 'type' => 'mongodb', 'port' => '27017', 'name' => 'exponential',    'user' => '' ),
    // the name is the service; the connect string becomes host:port/service
    'oracle'  => array( 'type' => 'oci8',    'port' => '1521',  'name' => 'FREEPDB1',       'user' => '' ),
    // Microsoft SQL Server: the database must exist (CREATE DATABASE, a UTF-8 collation recommended)
    'sqlserver' => array( 'type' => 'sqlserver', 'port' => '1433', 'name' => 'exponential', 'user' => 'sa' ),
    // databases that speak the MySQL protocol: the MySQL driver with the database's profile (expMySQLWireDB)
    'tidb'        => array( 'type' => 'tidb',        'port' => '4000', 'name' => 'exponential', 'user' => 'root' ),
    'vitess'      => array( 'type' => 'vitess',      'port' => '3306', 'name' => 'exponential', 'user' => 'root' ),
    'singlestore' => array( 'type' => 'singlestore', 'port' => '3306', 'name' => 'exponential', 'user' => 'root' ),
    'xpand'       => array( 'type' => 'xpand',       'port' => '3306', 'name' => 'exponential', 'user' => 'root' ),
    'aurora'      => array( 'type' => 'aurora',      'port' => '3306', 'name' => 'exponential', 'user' => 'admin' ),
    // Firebird 4/5: the database must exist; the name is an alias of databases.conf or the file's path on the server
    'firebird' => array( 'type' => 'firebird', 'port' => '3050', 'name' => 'exponential', 'user' => 'SYSDBA' ),
    // Db2 for LUW 11.5/12.1 over pdo_ibm: the database must exist (PAGESIZE 32 K, UTF-8, a user temporary tablespace)
    'db2'      => array( 'type' => 'db2',      'port' => '50000', 'name' => 'EXP',         'user' => 'db2inst1' ),
    // Informix 14.10/15 over pdo_ibm through the DRDA listener: the database must exist, logged, UTF-8
    'informix' => array( 'type' => 'informix', 'port' => '9089',  'name' => 'exponential', 'user' => 'informix' ),
    // DuckDB: one file (var/storage/duckdb/<name>), no server; needs PHP's FFI extension and libduckdb
    'duckdb'  => array( 'type' => 'duckdb',  'port' => '',      'name' => 'exponential.duckdb', 'user' => '' ),
    // the databases of the PostgreSQL wire protocol (expPostgreSQLWireDB): PHP's pgsql extension
    'cockroachdb' => array( 'type' => 'cockroachdb', 'port' => '26257', 'name' => 'exponential', 'user' => 'root' ),
    'yugabytedb'  => array( 'type' => 'yugabytedb',  'port' => '5433',  'name' => 'exponential', 'user' => 'yugabyte' ),
    'neon'        => array( 'type' => 'neon',        'port' => '5432',  'name' => 'neondb',      'user' => '' ),
    'supabase'    => array( 'type' => 'supabase',    'port' => '5432',  'name' => 'postgres',    'user' => 'postgres' ),
    'aurorapostgresql' => array( 'type' => 'aurorapostgresql', 'port' => '5432', 'name' => 'exponential', 'user' => 'postgres' ),
    // libSQL over HTTP (expLibSQLDB, the SQLite driver): a libSQL server (sqld) or Turso's cloud; the password is the auth token
    'libsql'   => array( 'type' => 'libsql',   'port' => '8080',  'name' => 'exponential', 'user' => '' ),
    'turso'    => array( 'type' => 'turso',    'port' => '443',   'name' => 'exponential', 'user' => '' ),
);
$databaseAliases = array( 'sqlite3' => 'sqlite', 'mysqli' => 'mysql', 'mariadb' => 'mysql',
                          'postgresql' => 'pgsql', 'postgres' => 'pgsql', 'mongo' => 'mongodb',
                          'oci8' => 'oracle', 'ezoracle' => 'oracle',
                          'mssql' => 'sqlserver', 'sqlsrv' => 'sqlserver',
                          'planetscale' => 'vitess', 'memsql' => 'singlestore', 'aurora-mysql' => 'aurora', 'auroramysql' => 'aurora',
                          'fb' => 'firebird', 'pdo_firebird' => 'firebird',
                          'cockroach' => 'cockroachdb', 'crdb' => 'cockroachdb', 'yugabyte' => 'yugabytedb', 'ysql' => 'yugabytedb',
                          'aurora-postgresql' => 'aurorapostgresql',
                          'ibm_db2' => 'db2', 'pdo_ibm' => 'db2', 'ifx' => 'informix', 'sqld' => 'libsql' );
$pgWireTypes = array( 'cockroachdb', 'yugabytedb', 'neon', 'supabase', 'aurorapostgresql' );

$options = array(
    'db'             => 'sqlite',
    'db-host'        => 'localhost',
    'db-port'        => null,
    'db-name'        => null,
    'db-user'        => null,
    'db-password'    => '',
    'db-socket'      => '',
    'db-encrypt'     => '',
    'db-ssl'         => '',
    'db-ssl-ca'      => '',
    'db-sslmode'     => '',
    'db-sslrootcert' => '',
    'db-pooler'      => '',
    'db-endpoint'    => '',
    'db-url'         => '',
    'db-tls'         => '',
    'db-action'      => 'remove',
    'package'        => 'sevenx_multisite',
    'language'       => 'eng-US',
    'languages'      => '',
    'timezone'       => '',
    'title'          => 'Exponential',
    'organisation-name'    => '',
    'organisation-address' => '',
    'url'            => '',
    'access'         => null,
    'site-access'    => 'site',
    'admin-access'   => 'admin',
    'editor-access'  => '',
    'host'           => '',
    'admin-host'     => '',
    'editor-host'    => '',
    'port'           => '8080',
    'admin-port'     => '8081',
    'editor-port'    => '',
    'email'          => 'nospam@exponential.earth',
    'password'       => null,
    'first-name'     => 'Administrator',
    'last-name'      => 'User',
    'cluster'        => 'single',
    'cluster-share'  => '',
);
$flags = array( 'db-trust-server-certificate' => false, 'apply' => false, 'force' => false, 'dry-run' => false, 'print' => false, 'help' => false, 'allow-root-user' => false,
                'random-password' => false, 'no-crontab' => false );

foreach ( $argvIn as $arg )
{
    if ( !preg_match( '/^--([a-z][a-z-]*)(?:=(.*))?$/s', $arg, $m ) )
    {
        fwrite( STDERR, "Unknown argument: $arg (see --help)\n" );
        exit( 1 );
    }
    $key = $m[1];
    $value = isset( $m[2] ) ? $m[2] : null;
    if ( array_key_exists( $key, $flags ) && $value === null )
        $flags[$key] = true;
    else if ( array_key_exists( $key, $options ) && $value !== null )
        $options[$key] = $value;
    else
    {
        fwrite( STDERR, "Unknown option or missing value: $arg (see --help)\n" );
        exit( 1 );
    }
}

if ( $flags['help'] )
{
    installUsage();
    exit( 0 );
}

// Without --apply the run is a dry run: it shows the plan, checks the database and the packages and installs
// nothing. --dry-run means the same; with --apply it is a usage error, as --force without --apply is (--force
// alone installed before 6.0.15: a script that still passes it must not get a dry run that looks like a success).
// Checked here, before anything is written or a database is touched.
require_once 'autoload.php';
$apply = \Exponential\Runnable\Command::applyRequested( $flags );
if ( $apply === null || ( !$apply && $flags['force'] && !$flags['print'] ) )
{
    fwrite( STDERR, 'FAIL: ' . ( $apply === null
        ? '--apply and --dry-run exclude each other; without --apply the script changes nothing'
        : '--force no longer installs on its own: add --apply (exp:install --apply --force); without --apply the run is a dry run' ) . "\n" );
    exit( 2 );
}
$flags['dry-run'] = !$apply;

// Database type and its defaults.
$db = strtolower( trim( $options['db'] ) );
if ( isset( $databaseAliases[$db] ) )
    $db = $databaseAliases[$db];
if ( !isset( $databaseTypes[$db] ) )
{
    fwrite( STDERR, "Unknown database type '{$options['db']}': use sqlite, mysql, pgsql, mongodb, oracle, sqlserver, tidb, vitess, singlestore, xpand, aurora, firebird,"
                  . " duckdb, cockroachdb, yugabytedb, neon, supabase, aurorapostgresql, db2, informix, libsql or turso\n" );
    exit( 1 );
}
$dbDefaults = $databaseTypes[$db];
$dbPort = $options['db-port'] !== null ? $options['db-port'] : $dbDefaults['port'];
$dbName = $options['db-name'] !== null ? $options['db-name'] : $dbDefaults['name'];
$dbUser = $options['db-user'] !== null ? $options['db-user'] : $dbDefaults['user'];
// Oracle: the driver takes one connect string. A service name becomes the Easy
// Connect host:port/service; a connect string of its own (with / or a
// descriptor) stays as it is, and @alias names a TNS alias
if ( $db === 'oracle' )
{
    if ( strpos( $dbName, '@' ) === 0 )
        $dbName = substr( $dbName, 1 );
    else if ( strpos( $dbName, '/' ) === false && strpos( $dbName, '(' ) === false )
        $dbName = $options['db-host'] . ':' . $dbPort . '/' . $dbName;
}
if ( $options['db-password'] === '' && getenv( 'EXP_INSTALL_DB_PASSWORD' ) !== false )
    $options['db-password'] = (string)getenv( 'EXP_INSTALL_DB_PASSWORD' );
if ( !in_array( $options['db-action'], array( 'remove', 'ignore', 'skip' ), true ) )
{
    fwrite( STDERR, "--db-action must be remove, ignore or skip\n" );
    exit( 1 );
}

// Access type: named, or implied by the host or port options.
$access = $options['access'];
if ( $access === null )
    $access = ( $options['host'] !== '' || $options['admin-host'] !== '' ) ? 'host' : 'url';
$accessMap = array( 'url' => 'url', 'uri' => 'url', 'host' => 'hostname', 'hostname' => 'hostname', 'port' => 'port' );
if ( !isset( $accessMap[$access] ) )
{
    fwrite( STDERR, "--access must be url, host or port\n" );
    exit( 1 );
}
$accessType = $accessMap[$access];
if ( $accessType === 'hostname' && ( $options['host'] === '' || $options['admin-host'] === '' ) )
{
    fwrite( STDERR, "--access=host needs both --host and --admin-host\n" );
    exit( 1 );
}
foreach ( array( 'site-access', 'admin-access' ) as $key )
{
    if ( !preg_match( '/^[a-z0-9_]+$/', $options[$key] ) )
    {
        fwrite( STDERR, "--$key must be lower-case letters, digits and underscores\n" );
        exit( 1 );
    }
}
if ( $options['site-access'] === $options['admin-access'] )
{
    fwrite( STDERR, "--site-access and --admin-access must differ\n" );
    exit( 1 );
}
// The editor siteaccess (the admin for content editing only) is always
// installed. Its value never equals the public or admin one: on the same port
// or host one of the two would be unreachable. Without an --editor-* option it
// is port + 2, edit.<host without www.> or /editor.
require_once 'autoload.php';
if ( $options['editor-access'] !== '' && !preg_match( '/^[a-z0-9_]+$/', $options['editor-access'] ) )
{
    fwrite( STDERR, "--editor-access must be lower-case letters, digits and underscores\n" );
    exit( 1 );
}
if ( $options['editor-port'] !== '' && !ctype_digit( $options['editor-port'] ) )
{
    fwrite( STDERR, "--editor-port must be a port number\n" );
    exit( 1 );
}
$editorKey = $accessType === 'port' ? 'editor-port' : ( $accessType === 'hostname' ? 'editor-host' : 'editor-access' );
$siteKey = $accessType === 'port' ? 'port' : ( $accessType === 'hostname' ? 'host' : 'site-access' );
$adminKey = $accessType === 'port' ? 'admin-port' : ( $accessType === 'hostname' ? 'admin-host' : 'admin-access' );
$editorValue = $options[$editorKey] !== ''
    ? $options[$editorKey]
    : (string)\eZStepSiteAccess::distinctEditorAccessValue( $accessType, $options[$siteKey], $options[$adminKey] );
if ( in_array( (string)$editorValue, array( (string)$options[$siteKey], (string)$options[$adminKey] ), true ) )
{
    fwrite( STDERR, "--$editorKey must differ from --$siteKey and --$adminKey\n" );
    exit( 1 );
}
if ( $accessType === 'port' && (string)$options['port'] === (string)$options['admin-port'] )
{
    fwrite( STDERR, "--port and --admin-port must differ\n" );
    exit( 1 );
}
if ( $accessType === 'hostname' && $options['host'] === $options['admin-host'] )
{
    fwrite( STDERR, "--host and --admin-host must differ\n" );
    exit( 1 );
}

if ( !filter_var( $options['email'], FILTER_VALIDATE_EMAIL ) )
{
    fwrite( STDERR, "--email is not an e-mail address: {$options['email']}\n" );
    exit( 1 );
}
// The site's time zone (site.ini [TimeZoneSettings] TimeZone): as given, else the zone of the primary language's
// country, else php.ini's date.timezone, else UTC (expInstallTimeZone::choose(), which the installation applies)
if ( !in_array( $options['cluster'], array( 'single', 'one-node' ), true ) || ( $options['cluster-share'] !== '' && $options['cluster-share'][0] !== '/' ) )
{
    fwrite( STDERR, "--cluster is single or one-node, and --cluster-share an absolute path\n" );
    exit( 1 );
}
if ( $options['cluster'] === 'one-node' && !in_array( $db, array( 'mysql', 'mysqli', 'pgsql', 'postgresql', 'tidb', 'vitess', 'singlestore', 'xpand', 'aurora' ), true ) )
{
    fwrite( STDERR, "--cluster=one-node needs --db=mysql (or pgsql once the kernel has its PostgreSQL cluster backend)\n" );
    exit( 1 );
}
if ( $options['timezone'] !== '' && !expInstallTimeZone::isValid( $options['timezone'] ) )
{
    fwrite( STDERR, "--timezone is not a time zone this PHP knows: {$options['timezone']} (e.g. Europe/Berlin, America/New_York, UTC)\n" );
    exit( 1 );
}
$timeZone = expInstallTimeZone::choose( $options['timezone'], $options['language'], (string)ini_get( 'date.timezone' ) );
$timeZoneReasons = array( 'requested' => 'as given', 'language' => 'from ' . $options['language'],
                          'php' => "php.ini's date.timezone; " . $options['language'] . ' spans several zones, set --timezone',
                          'default' => 'no zone known for ' . $options['language'] . ' and none in php.ini; set --timezone' );
// The administrator password. Without --password, or with --random-password,
// the installer generates one (24 characters of the bcrypt alphabet, from the
// operating system's secure random source, about 143 bits). A given one is kept
// as it is when the installation accepts it: not empty, at least
// MinPasswordLength characters and not a well-known one; otherwise a generated
// one replaces it and the summary says so.
require_once 'autoload.php';
$passwordNote = '';
if ( $options['password'] !== null && trim( $options['password'] ) === '' )
{
    fwrite( STDERR, "--password cannot be empty (leave it out to have one generated)\n" );
    exit( 1 );
}
if ( $flags['random-password'] || $options['password'] === null )
{
    $options['password'] = \Exponential\Command\Kernel\Install::generatePassword();
    $passwordNote = $flags['random-password'] ? 'requested with --random-password' : 'no --password given';
}
else if ( ( $problem = \Exponential\Command\Kernel\Install::passwordProblem( $options['password'] ) ) !== null )
{
    $options['password'] = \Exponential\Command\Kernel\Install::generatePassword();
    $passwordNote = 'the password given is ' . $problem . ' and cannot be installed';
}
$passwordGiven = $passwordNote === '';

// Where the site is. The installer builds every siteaccess's address from it;
// without it that would be the host of this command line: localhost.
$url = trim( $options['url'] );
if ( $url === '' )
    $url = $accessType === 'hostname' ? 'http://' . $options['host'] : 'http://localhost';
if ( !preg_match( '#^[a-z][a-z0-9+.-]*://#i', $url ) )
    $url = 'http://' . $url;

// The addresses the installation will have, for the summary.
$urlParts = parse_url( $url );
$scheme = isset( $urlParts['scheme'] ) ? $urlParts['scheme'] : 'http';
$urlHost = isset( $urlParts['host'] ) ? $urlParts['host'] : 'localhost';
$urlPort = isset( $urlParts['port'] ) ? ':' . $urlParts['port'] : '';
$urlPath = isset( $urlParts['path'] ) ? rtrim( $urlParts['path'], '/' ) : '';
if ( $accessType === 'hostname' )
{
    $siteURL = "$scheme://{$options['host']}$urlPort$urlPath/";
    $adminURL = "$scheme://{$options['admin-host']}$urlPort$urlPath/";
    $editorURL = "$scheme://$editorValue$urlPort$urlPath/";
}
else if ( $accessType === 'port' )
{
    $siteURL = "$scheme://$urlHost:{$options['port']}$urlPath/";
    $adminURL = "$scheme://$urlHost:{$options['admin-port']}$urlPath/";
    $editorURL = "$scheme://$urlHost:$editorValue$urlPath/";
}
else
{
    $siteURL = "$scheme://$urlHost$urlPort$urlPath/{$options['site-access']}/";
    $adminURL = "$scheme://$urlHost$urlPort$urlPath/{$options['admin-access']}/";
    $editorURL = "$scheme://$urlHost$urlPort$urlPath/$editorValue/";
}

$languages = array_values( array_filter( array_map( 'trim', explode( ',', $options['languages'] ) ), 'strlen' ) );

// The configuration, section by section, as kickstart.ini-dist documents it.
$sections = array(
    'email_settings'   => array( 'Type' => 'mta', 'Server' => '', 'User' => '', 'Password' => '' ),
    'database_choice'  => array( 'Type' => $dbDefaults['type'] ),
    'database_init'    => array( 'Server' => $options['db-host'], 'Port' => $dbPort, 'Database' => $dbName,
                                 'User' => $dbUser, 'Password' => $options['db-password'], 'Socket' => $options['db-socket'] )
                          + ( $db === 'sqlserver' ? array( 'Encrypt' => $options['db-encrypt'] !== '' ? $options['db-encrypt'] : 'yes',
                                                           'TrustServerCertificate' => $flags['db-trust-server-certificate'] ? 'enabled' : 'disabled' ) : array() )
                          + ( in_array( $db, array( 'tidb', 'vitess', 'singlestore', 'xpand', 'aurora' ), true ) && $options['db-ssl'] !== ''
                              ? array_filter( array( 'SSL' => $options['db-ssl'], 'SSLCA' => $options['db-ssl-ca'] ), 'strlen' ) : array() )
                          + ( in_array( $db, $pgWireTypes, true ) ? array_filter( array( 'SSLMode' => $options['db-sslmode'], 'SSLRootCert' => $options['db-sslrootcert'],
                                                                                         'Pooler' => $options['db-pooler'], 'Endpoint' => $options['db-endpoint'] ), 'strlen' ) : array() )
                          + ( in_array( $db, array( 'libsql', 'turso' ), true ) ? array_filter( array( 'URL' => $options['db-url'], 'TLS' => $options['db-tls'] ), 'strlen' ) : array() ),
    'language_options' => array( 'Primary' => $options['language'], 'Languages' => $languages, 'TimeZone' => $timeZone['zone'] ),
    'site_types'       => array( 'Site_package' => $options['package'] ),
    'site_access'      => array( 'Access' => $accessType ),
    'site_details'     => array( 'Title' => $options['title'], 'URL' => $url,
                                 'Access' => $options['site-access'], 'AdminAccess' => $options['admin-access'],
                                 'AccessPort' => $options['port'], 'AdminAccessPort' => $options['admin-port'],
                                 'AccessHostname' => $options['host'], 'AdminAccessHostname' => $options['admin-host'],
                                 'Database' => $dbName, 'DatabaseAction' => $options['db-action'],
                                 'OrganisationName' => $options['organisation-name'], 'OrganisationAddress' => $options['organisation-address'] ),
    'site_admin'       => array( 'FirstName' => $options['first-name'], 'LastName' => $options['last-name'],
                                 'Email' => $options['email'], 'Password' => $options['password'] ),
    'security'         => array(),
    'registration'     => array( 'Send' => 'false' ),
    // a one-node DFS cluster after the install (expKickstarter::enableCluster(), exp:cluster:enable)
    'cluster'          => array( 'Mode' => $options['cluster'], 'Share' => $options['cluster-share'] ),
);
// the editor's value, under the key of the access type ([site_details] EditorAccess, EditorAccessPort or EditorAccessHostname)
$sections['site_details'][$accessType === 'port' ? 'EditorAccessPort' : ( $accessType === 'hostname' ? 'EditorAccessHostname' : 'EditorAccess' )] = (string)$editorValue;

if ( $flags['print'] )
{
    echo installIniText( $sections, true );
    exit( 0 );
}

// An installation is there when the override settings name a database.
$overrideFile = $rootDir . '/settings/override/site.ini.append.php';
$installed = is_file( $overrideFile ) && preg_match( '/^\s*\[DatabaseSettings\]/m', (string)file_get_contents( $overrideFile ) );
if ( $installed && !$flags['force'] && !$flags['dry-run'] )
{
    fwrite( STDERR, "This directory already holds an installation (settings/override/site.ini.append.php).\n"
        . "exp:install would replace its database and settings. Re-run with --apply --force to do that,\n"
        . "or without --apply to check the configuration only.\n" );
    exit( 1 );
}

// Put the generated configuration where the steps read it, and whatever was
// there back afterwards, however the run ends.
$stamp = date( 'Ymd-His' );
$kickstart = $rootDir . '/kickstart.ini';
$logDir = $rootDir . '/var/log';
$aside = null;
if ( file_exists( $kickstart ) )
{
    $aside = $rootDir . '/var/log/kickstart.ini.before-exp-install-' . $stamp;
    if ( !is_dir( $logDir ) )
        @mkdir( $logDir, eZDir::dirMode( 0775 ), true );
    if ( !@rename( $kickstart, $aside ) )
    {
        fwrite( STDERR, "Could not move the existing kickstart.ini aside; nothing was changed.\n" );
        exit( 1 );
    }
}
$oldUmask = umask( eZFile::creationUmask( 0077 ) );
$written = file_put_contents( $kickstart, installIniText( $sections, false ) );
umask( $oldUmask );
if ( $written === false )
{
    if ( $aside !== null )
        @rename( $aside, $kickstart );
    fwrite( STDERR, "Could not write kickstart.ini; nothing was changed.\n" );
    exit( 1 );
}
$startedAt = time();
// The final step writes the cron job unless this run says not to (expCrontabSetup::enabled())
if ( $flags['no-crontab'] )
    putenv( 'EXP_INSTALL_CRONTAB=0' );
// A password generated here is recorded where the setup records its own, so the
// two cases look alike and the file is the one place to read it back
if ( !$passwordGiven && !$flags['dry-run'] )
    \Exponential\Command\Kernel\Install::writeRecordedPassword( $rootDir . '/var/log/initial-admin-password', $options['password'], $passwordNote );
// The installer ends the process itself, so the clean-up and the summary run
// at shutdown. The run succeeded when it wrote the override settings afresh,
// with a database in them.
$summary = array(
    'Installed'      => '',
    'Site'           => $siteURL,
    'Admin login'    => $adminURL . 'user/login',
    'Editor'         => $editorURL,
    'Username'       => 'admin',
    'Password'       => $options['password'],
    'E-mail'         => $options['email'],
    'Database'       => $db . ' ' . $dbName . ( $db === 'sqlite' || $db === 'duckdb' ? '' : ' at ' . ( $options['db-socket'] !== '' ? $options['db-socket'] : $options['db-host'] . ( $dbPort !== '' ? ':' . $dbPort : '' ) ) )
                        . ( $dbUser !== '' ? ', user ' . $dbUser : '' ),
    'Package'        => $options['package'] . ', ' . $options['language'] . ( $languages ? ' + ' . implode( ',', $languages ) : '' ),
    'Time zone'      => $timeZone['zone'] . ' (' . $timeZoneReasons[$timeZone['reason']] . ')',
    'Siteaccesses'   => $options['site-access'] . ', ' . $options['admin-access'] . ', editor (by ' . ( $accessType === 'hostname' ? 'host' : $accessType ) . ')'
                        // the Admin UI siteaccess, made when exp_adminui is in the installation, always by URI
                        . ( class_exists( 'eZStepCreateSites' ) && method_exists( 'eZStepCreateSites', 'adminUIAvailable' )
                            && eZStepCreateSites::adminUIAvailable( $rootDir . '/extension' ) ? ', adminui (by uri)' : '' ),
    'Configuration'  => 'var/log/exp-install-' . $stamp . '.ini (passwords masked)',
);
register_shutdown_function( function () use ( $kickstart, $aside, $sections, $logDir, $stamp, $startedAt, $overrideFile, $flags, &$summary, &$passwordNote, $passwordGiven, $rootDir ) {
    if ( is_dir( $logDir ) || @mkdir( $logDir, eZDir::dirMode( 0775 ), true ) )
        @file_put_contents( $logDir . '/exp-install-' . $stamp . '.ini', installIniText( $sections, true ) );
    @unlink( $kickstart );
    if ( $aside !== null )
        @rename( $aside, $kickstart );

    clearstatcache();
    $ok = !$flags['dry-run'] && is_file( $overrideFile ) && filemtime( $overrideFile ) >= $startedAt
        && preg_match( '/^\s*\[DatabaseSettings\]/m', (string)file_get_contents( $overrideFile ) );
    if ( !$ok )
        return;
    // What the installation really set: the kickstarter replaces a password it
    // refuses and records the one it made, so that file is the truth when this run wrote it
    $recorded = \Exponential\Command\Kernel\Install::readRecordedPassword( $rootDir . '/var/log/initial-admin-password', $startedAt );
    if ( $recorded !== null && $recorded !== $summary['Password'] )
    {
        $summary['Password'] = $recorded;
        $passwordNote = 'the password given is well-known and was refused by the installation';
        $passwordGiven = false;
    }
    else if ( $recorded !== null && $passwordNote === '' )
        $passwordNote = 'made by the installation itself';
    if ( $passwordNote !== '' )
        $summary = \Exponential\Command\Kernel\Install::insertAfter( $summary, 'Password',
            'Password note', 'generated: ' . $passwordNote . '; also in var/log/initial-admin-password (owner only), change it and delete that file' );
    $summary['Installed'] = date( 'Y-m-d H:i:s T' ) . ' (' . ( time() - $startedAt ) . 's)';
    $width = max( array_map( 'strlen', array_keys( $summary ) ) );
    $rule = str_repeat( '=', 64 );
    echo "\n$rule\n  Exponential is installed\n$rule\n";
    foreach ( $summary as $label => $value )
        echo '  ' . str_pad( $label . ':', $width + 2 ) . $value . "\n";
    echo "$rule\n";
    echo "  Copy and paste:\n\n";
    echo "    Admin:    {$summary['Admin login']}\n";
    echo "    Username: {$summary['Username']}\n";
    echo "    Password: {$summary['Password']}\n";
    echo "    Site:     {$summary['Site']}\n";
    echo "$rule\n";
    if ( $passwordGiven )
        echo "  The password is shown here once and stored nowhere else in clear text.\n";
    else
        echo "  The password was generated: it is also in var/log/initial-admin-password (owner only).\n"
           . "  Change it after the first login and delete that file.\n";
} );

echo "exp:install" . ( $flags['dry-run'] ? ' (dry run, --apply installs)' : '' ) . ": {$options['package']} on $db"
    . ( $db === 'sqlite' || $db === 'duckdb' ? " ($dbName)" : " ({$dbName} at {$options['db-host']}" . ( $dbPort !== '' ? ":$dbPort" : '' ) . ')' )
    . ", $url, access by " . ( $accessType === 'hostname' ? 'host' : $accessType )
    . ", time zone {$timeZone['zone']} (" . $timeZoneReasons[$timeZone['reason']] . ')'
    . ", administrator admin / " . ( $passwordGiven ? '(as given)' : '(generated, shown at the end)' ) . "\n";

function installUsage()
{
    echo <<<TXT
Usage: ./console exp:install [options]

Installs Exponential in one command: no kickstart.ini to write first.
Everything has a default, so "./console exp:install --apply" alone installs
the multisite package on SQLite with the administrator admin and a generated
password, shown once at the end.

Without --apply nothing is installed: the run shows what it would install
(the database, and that it would be emptied first with --db-action=remove,
the package, the siteaccesses, the files), checks the database and the remote
packages, and ends with "PASS: ... (dry run, nothing changed; --apply writes)".

Database (default: SQLite, no server needed)
  --db=<type>            sqlite (default), mysql, pgsql, mongodb, oracle, sqlserver, firebird or duckdb;
                         cockroachdb, yugabytedb, neon, supabase or aurorapostgresql (PostgreSQL wire protocol)
                         db2 or informix (IBM, over pdo_ibm or ibm_db2)
                         libsql (sqld) or turso: SQLite over HTTP, with curl or allow_url_fopen;
                         the password is the server's auth token (turso: required)
                         (sqlserver: needs sqlsrv, pdo_sqlsrv or PDO_ODBC, and an existing database)
                         (neon, supabase, aurorapostgresql: the database must exist; TLS required by default)
                         (firebird: needs pdo_firebird of PHP 8.4 or later, Firebird 4 or 5, and an
                         existing database, PAGE_SIZE 16384 and UTF8)
                         (oracle: needs oci8 and extension/ezoracle)
                         MySQL protocol, with mysqli: tidb, vitess (planetscale),
                         singlestore (memsql), xpand, aurora (aurora-mysql)
                         (vitess: the keyspace must exist)
                         (duckdb: needs PHP's FFI extension and libduckdb, see [DuckDBSettings])
                         (db2, informix: need pdo_ibm and an existing database, see the
                         Db2 and Informix driver pages)
  --db-host=<host>       default localhost
  --db-port=<port>       default 3306 (mysql), 5432 (pgsql), 27017 (mongodb),
                         1521 (oracle), 1433 (sqlserver), 4000 (tidb), 3050 (firebird),
                         26257 (cockroachdb), 5433 (yugabytedb),
                         50000 (db2), 9089 (informix, DRDA), 8080 (libsql), 443 (turso)
  --db-name=<name>       default exponential (exponential.db for sqlite,
                         exponential.duckdb for duckdb, EXP for db2);
                         oracle: the service name, default FREEPDB1, used as
                         host:port/service; a full connect string (with / or
                         a descriptor) or @alias (a TNS alias) is used as it is;
                         firebird: an alias of the server's databases.conf or the
                         path of the database file on the server
  --db-user=<user>       default root (mysql, cockroachdb), postgres (pgsql, supabase, aurorapostgresql),
                         sa (sqlserver), SYSDBA (firebird), yugabyte (yugabytedb), db2inst1 (db2),
                         informix (informix), none otherwise
  --db-password=<pass>   default none; or set EXP_INSTALL_DB_PASSWORD, which
                         keeps it out of the process list and shell history
  --db-socket=<path>     MySQL socket, instead of host and port
  --db-encrypt=<mode>    sqlserver: yes (default), no or strict (TDS 8.0)
  --db-trust-server-certificate
                         sqlserver: accept the server's certificate without
                         checking it (a self-signed one, on a trusted network)
  --db-ssl=<mode>        tidb, vitess, singlestore, xpand, aurora: enabled (TLS),
                         verify (TLS, certificate checked) or disabled (default)
  --db-ssl-ca=<file>     the CA file for --db-ssl=verify (default: the system's)
  --db-sslmode=<mode>    cockroachdb, yugabytedb, neon, supabase, aurorapostgresql: disable, require, verify-ca
                         or verify-full (default: the database's; require for neon, supabase, aurorapostgresql)
  --db-sslrootcert=<file>  the CA file for verify-ca and verify-full
  --db-pooler=<mode>     auto (default: from the host and port), none, session or transaction
  --db-endpoint=<id>     neon: the endpoint id, for a libpq without SNI
  --db-url=<url>         libsql, turso: the server's URL instead of host and port (http://host:8080,
                         https://host, libsql://<db>-<org>.turso.io)
  --db-tls=<mode>        libsql, turso: enabled (https), disabled (http) or auto (default: turso,
                         or port 443)
  --db-action=<action>   remove (default: empty the database first), ignore, skip

Site
  --package=<name>       site package, default sevenx_multisite
  --language=<locale>    primary language, default eng-US
  --languages=<a,b>      more languages, e.g. ger-DE,fre-FR
  --timezone=<zone>      the site's time zone (site.ini [TimeZoneSettings]),
                         e.g. Europe/Berlin; default: the zone of --language's
                         country (ger-DE: Europe/Berlin), else php.ini's
                         date.timezone, else UTC. Dates, timed publishing and
                         cache_until( 'tomorrow' ) follow it
  --title=<text>         site name, default Exponential
  --organisation-name=<text>     who sends the optional e-mail (footer of every
                         newsletter and notification); default: the site name
  --organisation-address=<text>  the sender's postal address, lines separated
                         by \\n; empty is allowed (the e-mail preferences
                         status page warns until it is entered there)
  --url=<url>            where the site is, e.g. https://www.example.com
                         (default: http://localhost)
  --access=<type>        how siteaccesses are told apart: url (default,
                         /site and /admin), host or port
  --site-access=<name>   public siteaccess, default site
  --admin-access=<name>  admin siteaccess, default admin
  --host=<host>          public host (implies --access=host)
  --admin-host=<host>    admin host (implies --access=host)
  --port=<port>          public port, default 8080 (with --access=port)
  --admin-port=<port>    admin port, default 8081 (with --access=port)
  --editor-access=<name> URL path of the editor siteaccess (the admin for
                         content editing only), default editor
  --editor-host=<host>   editor host, default edit.<--host without www.>
                         (with --access=host)
  --editor-port=<port>   editor port, default --port + 2 (with --access=port)
                         The editor never shares the public or admin value.

Cluster
  --cluster=<mode>       single (default) or one-node: after the install the
                         site becomes a one-node DFS cluster (exp:cluster:enable:
                         share, cluster tables, settings, files moved in); needs
                         --db=mysql (pgsql once the kernel has its PostgreSQL
                         cluster backend). The gateway rule for the web server
                         is printed
  --cluster-share=<path> the cluster's share, an absolute path; default: the
                         folder dfs next to the installation

Administrator (login: admin)
  --email=<address>      default nospam@exponential.earth
  --password=<pass>      default: a generated one (see --random-password). A
                         given one is kept when it has at least 10 characters
                         (site.ini MinPasswordLength) and is not a well-known
                         one such as publish or admin; otherwise a generated
                         one replaces it and the summary says so. With
                         EXP_DEMO_MODE=1 in the environment (a public demo,
                         doc/eng-us/bc/6.0/demo-mode.md) a well-known or short one is kept
  --random-password      generate a strong one (24 characters of the bcrypt
                         alphabet ./A-Za-z0-9), shown once at the end and
                         written to var/log/initial-admin-password
  --first-name=<name>    default Administrator
  --last-name=<name>     default User

Run
  --apply                install (the default is a dry run that changes nothing)
  --force                with --apply: install over an existing installation
                         (replaces its database and settings); --force
                         without --apply is a usage error (exit 2)
  --dry-run              the default: check the configuration and the
                         packages, install nothing; with --apply a usage
                         error (exit 2)
  --print                show the configuration that would be used and stop
  --no-crontab           do not write the cron job; print its lines instead
                         (the default writes them to the crontab of the user
                         that owns the installation: doc/eng-us/bc/6.0/installer-crontab.md)
  --help                 this help

Examples
  ./console exp:install
  ./console exp:install --apply
  ./console exp:install --apply --db=mysql --db-name=site --db-user=site --db-password=secret
  ./console exp:install --apply --db=pgsql --db-host=db.local --db-user=exp --db-password=secret
  ./console exp:install --apply --db=mongodb --db-name=exponential --url=https://www.example.com
  ./console exp:install --apply --access=host --host=www.example.com --admin-host=admin.example.com

TXT;
}

function installIniText( array $sections, $mask )
{
    $lines = array( '; Written by exp:install for one installation run; see kickstart.ini-dist.', '' );
    foreach ( $sections as $section => $values )
    {
        $lines[] = "[$section]";
        $lines[] = 'Continue=true';
        foreach ( $values as $key => $value )
        {
            if ( is_array( $value ) )
            {
                foreach ( $value as $item )
                    $lines[] = $key . '[]=' . $item;
                continue;
            }
            if ( $mask && $key === 'Password' && $value !== '' )
                $value = '***';
            if ( preg_match( '/[\r\n]/', (string)$value ) )
            {
                fwrite( STDERR, "A value for $section/$key contains a line break\n" );
                exit( 1 );
            }
            $lines[] = $key . '=' . $value;
        }
        $lines[] = '';
    }
    return implode( "\n", $lines );
}

// The code is in kernel/private/classes/commands/install.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Install::main( __FILE__ );
