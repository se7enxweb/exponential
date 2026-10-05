#!/usr/bin/env php
<?php
/**
 * @description One-command installation, SQLite by default, no kickstart.ini needed
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
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
);
$databaseAliases = array( 'sqlite3' => 'sqlite', 'mysqli' => 'mysql', 'mariadb' => 'mysql',
                          'postgresql' => 'pgsql', 'postgres' => 'pgsql', 'mongo' => 'mongodb',
                          'oci8' => 'oracle', 'ezoracle' => 'oracle' );

$options = array(
    'db'             => 'sqlite',
    'db-host'        => 'localhost',
    'db-port'        => null,
    'db-name'        => null,
    'db-user'        => null,
    'db-password'    => '',
    'db-socket'      => '',
    'db-action'      => 'remove',
    'package'        => 'sevenx_multisite',
    'language'       => 'eng-US',
    'languages'      => '',
    'title'          => 'Exponential',
    'organisation-name'    => '',
    'organisation-address' => '',
    'url'            => '',
    'access'         => null,
    'site-access'    => 'site',
    'admin-access'   => 'admin',
    'host'           => '',
    'admin-host'     => '',
    'port'           => '8080',
    'admin-port'     => '8081',
    'email'          => 'nospam@exponential.earth',
    'password'       => null,
    'first-name'     => 'Administrator',
    'last-name'      => 'User',
);
$flags = array( 'force' => false, 'dry-run' => false, 'print' => false, 'help' => false, 'allow-root-user' => false,
                'random-password' => false );

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

// Database type and its defaults.
$db = strtolower( trim( $options['db'] ) );
if ( isset( $databaseAliases[$db] ) )
    $db = $databaseAliases[$db];
if ( !isset( $databaseTypes[$db] ) )
{
    fwrite( STDERR, "Unknown database type '{$options['db']}': use sqlite, mysql, pgsql, mongodb or oracle\n" );
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
if ( !filter_var( $options['email'], FILTER_VALIDATE_EMAIL ) )
{
    fwrite( STDERR, "--email is not an e-mail address: {$options['email']}\n" );
    exit( 1 );
}
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
}
else if ( $accessType === 'port' )
{
    $siteURL = "$scheme://$urlHost:{$options['port']}$urlPath/";
    $adminURL = "$scheme://$urlHost:{$options['admin-port']}$urlPath/";
}
else
{
    $siteURL = "$scheme://$urlHost$urlPort$urlPath/{$options['site-access']}/";
    $adminURL = "$scheme://$urlHost$urlPort$urlPath/{$options['admin-access']}/";
}

$languages = array_values( array_filter( array_map( 'trim', explode( ',', $options['languages'] ) ), 'strlen' ) );

// The configuration, section by section, as kickstart.ini-dist documents it.
$sections = array(
    'email_settings'   => array( 'Type' => 'mta', 'Server' => '', 'User' => '', 'Password' => '' ),
    'database_choice'  => array( 'Type' => $dbDefaults['type'] ),
    'database_init'    => array( 'Server' => $options['db-host'], 'Port' => $dbPort, 'Database' => $dbName,
                                 'User' => $dbUser, 'Password' => $options['db-password'], 'Socket' => $options['db-socket'] ),
    'language_options' => array( 'Primary' => $options['language'], 'Languages' => $languages ),
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
);

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
        . "exp:install would replace its database and settings. Re-run with --force to do that,\n"
        . "or with --dry-run to check the configuration only.\n" );
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
        @mkdir( $logDir, 0775, true );
    if ( !@rename( $kickstart, $aside ) )
    {
        fwrite( STDERR, "Could not move the existing kickstart.ini aside; nothing was changed.\n" );
        exit( 1 );
    }
}
$oldUmask = umask( 0077 );
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
    'Username'       => 'admin',
    'Password'       => $options['password'],
    'E-mail'         => $options['email'],
    'Database'       => $db . ' ' . $dbName . ( $db === 'sqlite' ? '' : ' at ' . ( $options['db-socket'] !== '' ? $options['db-socket'] : $options['db-host'] . ( $dbPort !== '' ? ':' . $dbPort : '' ) ) )
                        . ( $dbUser !== '' ? ', user ' . $dbUser : '' ),
    'Package'        => $options['package'] . ', ' . $options['language'] . ( $languages ? ' + ' . implode( ',', $languages ) : '' ),
    'Siteaccesses'   => $options['site-access'] . ', ' . $options['admin-access'] . ' (by ' . ( $accessType === 'hostname' ? 'host' : $accessType ) . ')',
    'Configuration'  => 'var/log/exp-install-' . $stamp . '.ini (passwords masked)',
);
register_shutdown_function( function () use ( $kickstart, $aside, $sections, $logDir, $stamp, $startedAt, $overrideFile, $flags, &$summary, &$passwordNote, $passwordGiven, $rootDir ) {
    if ( is_dir( $logDir ) || @mkdir( $logDir, 0775, true ) )
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

echo "exp:install: {$options['package']} on $db"
    . ( $db === 'sqlite' ? " ($dbName)" : " ({$dbName} at {$options['db-host']}" . ( $dbPort !== '' ? ":$dbPort" : '' ) . ')' )
    . ", $url, access by " . ( $accessType === 'hostname' ? 'host' : $accessType )
    . ", administrator admin / " . ( $passwordGiven ? '(as given)' : '(generated, shown at the end)' ) . "\n";

function installUsage()
{
    echo <<<TXT
Usage: ./console exp:install [options]

Installs Exponential in one command: no kickstart.ini to write first.
Everything has a default, so "./console exp:install" alone installs the
multisite package on SQLite with the administrator admin and a generated
password, shown once at the end.

Database (default: SQLite, no server needed)
  --db=<type>            sqlite (default), mysql, pgsql, mongodb or oracle
                         (oracle: needs oci8 and extension/ezoracle)
  --db-host=<host>       default localhost
  --db-port=<port>       default 3306 (mysql), 5432 (pgsql), 27017 (mongodb),
                         1521 (oracle)
  --db-name=<name>       default exponential (exponential.db for sqlite);
                         oracle: the service name, default FREEPDB1, used as
                         host:port/service; a full connect string (with / or
                         a descriptor) or @alias (a TNS alias) is used as it is
  --db-user=<user>       default root (mysql), postgres (pgsql), none otherwise
  --db-password=<pass>   default none; or set EXP_INSTALL_DB_PASSWORD, which
                         keeps it out of the process list and shell history
  --db-socket=<path>     MySQL socket, instead of host and port
  --db-action=<action>   remove (default: empty the database first), ignore, skip

Site
  --package=<name>       site package, default sevenx_multisite
  --language=<locale>    primary language, default eng-US
  --languages=<a,b>      more languages, e.g. ger-DE,fre-FR
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

Administrator (login: admin)
  --email=<address>      default nospam@exponential.earth
  --password=<pass>      default: a generated one (see --random-password). A
                         given one is kept when it has at least 10 characters
                         (site.ini MinPasswordLength) and is not a well-known
                         one such as publish or admin; otherwise a generated
                         one replaces it and the summary says so
  --random-password      generate a strong one (24 characters of the bcrypt
                         alphabet ./A-Za-z0-9), shown once at the end and
                         written to var/log/initial-admin-password
  --first-name=<name>    default Administrator
  --last-name=<name>     default User

Run
  --force                install over an existing installation (replaces its
                         database and settings)
  --dry-run              check the configuration and the packages, install nothing
  --print                show the configuration that would be used and stop
  --help                 this help

Examples
  ./console exp:install
  ./console exp:install --db=mysql --db-name=site --db-user=site --db-password=secret
  ./console exp:install --db=pgsql --db-host=db.local --db-user=exp --db-password=secret
  ./console exp:install --db=mongodb --db-name=exponential --url=https://www.example.com
  ./console exp:install --access=host --host=www.example.com --admin-host=admin.example.com

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
