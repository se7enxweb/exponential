<?php
/**
 * File containing the expUpgradeComposer class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Composer for the libraries stage of exp:upgrade: which binary, running it in the installation root without a
 * shell, what composer.lock asks for against what vendor/ holds, and the security audit.
 *
 * Writes (install, update) are refused when the process does not run as the owner of the installation: files that
 * root writes into vendor/ cannot be read or replaced by the site's own user afterwards.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeComposer
{
    /**
     * The command that starts Composer: the option composer (--composer=, a binary or a .phar), else composer on the
     * PATH, else composer.phar in the root. Empty when there is none.
     *
     * @param expUpgradeContext $context
     * @return string[]
     */
    public static function command( expUpgradeContext $context )
    {
        $given = (string)$context->option( 'composer', '' );
        $root = (string)$context->option( 'root' );
        $candidates = $given !== '' ? array( $given ) : array();
        if ( !$candidates )
        {
            foreach ( explode( PATH_SEPARATOR, (string)getenv( 'PATH' ) ) as $dir )
                if ( $dir !== '' && is_file( "$dir/composer" ) && is_executable( "$dir/composer" ) )
                {
                    $candidates[] = "$dir/composer";
                    break;
                }
            $candidates[] = "$root/composer.phar";
        }
        foreach ( $candidates as $c )
        {
            if ( !is_file( $c ) )
                continue;
            return substr( $c, -5 ) === '.phar' || !is_executable( $c ) ? array( PHP_BINARY, $c ) : array( $c );
        }
        return array();
    }

    /** The variables of this process Composer gets as they are, when set. */
    const PASSED_VARIABLES = array( 'HOME', 'COMPOSER_HOME', 'COMPOSER_CACHE_DIR', 'COMPOSER_AUTH', 'COMPOSER_ROOT_VERSION', 'COMPOSER_PROCESS_TIMEOUT', 'PHPRC', 'PHP_INI_SCAN_DIR',
                                    'TMPDIR', 'HTTP_PROXY', 'HTTPS_PROXY', 'NO_PROXY', 'SSL_CERT_FILE', 'SSL_CERT_DIR', 'OPENSSL_CONF' );

    /**
     * What Windows needs in the environment of a PHP process: without SystemRoot, PHP's curl extension does not start
     * ("Unable to start curl module") and Composer fails before it does anything; the temporary folder, the profile
     * folders and the program search follow the same variables.
     */
    const WINDOWS_VARIABLES = array( 'SystemRoot', 'SystemDrive', 'windir', 'TEMP', 'TMP', 'USERPROFILE', 'APPDATA', 'LOCALAPPDATA',
                                     'ProgramData', 'ProgramFiles', 'PATHEXT', 'ComSpec', 'COMPUTERNAME', 'USERNAME' );

    /**
     * The environment Composer runs in: no interaction, the PATH, and the variables of PASSED_VARIABLES (on Windows
     * also those of WINDOWS_VARIABLES) that this process has. Pure, with $getenv.
     *
     * @param callable $getenv name => value or false, as getenv()
     * @param bool $superuser whether this process runs as root (COMPOSER_ALLOW_SUPERUSER)
     * @param bool|null $windows whether this is Windows (null: PHP_OS_FAMILY)
     * @param string|null $rootVersion the version of the installation's own package (COMPOSER_ROOT_VERSION) where
     *                                 this process sets none: see rootVersion()
     * @return array
     */
    public static function environment( callable $getenv, $superuser = false, $windows = null, $rootVersion = null )
    {
        if ( $windows === null )
            $windows = PHP_OS_FAMILY === 'Windows';
        $path = $getenv( 'PATH' );
        $env = array( 'COMPOSER_NO_INTERACTION' => '1', 'PATH' => $path ? $path : ( $windows ? '' : '/usr/local/bin:/usr/bin:/bin' ) );
        foreach ( $windows ? array_merge( self::PASSED_VARIABLES, self::WINDOWS_VARIABLES ) : self::PASSED_VARIABLES as $name )
        {
            $value = $getenv( $name );
            if ( $value !== false && $value !== null && $value !== '' )
                $env[$name] = (string)$value;
        }
        if ( $superuser )
            $env['COMPOSER_ALLOW_SUPERUSER'] = '1';
        if ( !isset( $env['COMPOSER_ROOT_VERSION'] ) && $rootVersion !== null && $rootVersion !== '' )
            $env['COMPOSER_ROOT_VERSION'] = (string)$rootVersion;
        return $env;
    }

    /**
     * The version Composer gives the installation's own package (se7enxweb/exponential) in $root: the kernel's
     * version (6.0.15) where $root is no git checkout, null in one (Composer reads the branch and its alias there).
     * Composer cannot tell the version of a release install, which has neither a git checkout nor a "version" in
     * composer.json, and took 1.0.0: a package that requires se7enxweb/exponential ^6.0.14 (expai, through
     * exponential-php-floor 1.3 on PHP 8.4 and later) could then not be installed, and the libraries stage failed.
     *
     * @param string $root
     * @param string|null $version MAJOR.MINOR.RELEASE (null: eZPublishSDK's)
     * @return string|null
     */
    public static function rootVersion( $root, $version = null )
    {
        if ( file_exists( rtrim( (string)$root, '/\\' ) . '/.git' ) )
            return null;
        if ( $version === null )
        {
            if ( !class_exists( 'eZPublishSDK' ) )
                return null;
            $version = eZPublishSDK::VERSION_MAJOR . '.' . eZPublishSDK::VERSION_MINOR . '.' . eZPublishSDK::VERSION_RELEASE;
        }
        return preg_match( '/^[0-9]+\.[0-9]+\.[0-9]+$/', (string)$version ) ? (string)$version : null;
    }

    /**
     * Runs Composer in the root: array( 'exit' => int, 'output' => stdout, 'errors' => stderr ).
     *
     * @param expUpgradeContext $context
     * @param string[] $arguments
     * @return array
     */
    public static function run( expUpgradeContext $context, array $arguments )
    {
        $command = self::command( $context );
        if ( !$command )
            return array( 'exit' => 127, 'output' => '', 'errors' => 'no composer: give one with --composer=' );
        $root = (string)$context->option( 'root' );
        $command = array_merge( $command, $arguments, array( '--no-interaction', '--working-dir=' . $root ) );
        $env = self::environment( 'getenv', function_exists( 'posix_geteuid' ) && posix_geteuid() === 0, null, self::rootVersion( $root ) );
        $result = self::runCommand( $command, $root, $env );
        if ( $result === null )
            return array( 'exit' => 127, 'output' => '', 'errors' => 'cannot start composer' );
        return $result;
    }

    /**
     * Runs a command and collects what it writes: array( 'exit' => int, 'output' => stdout, 'errors' => stderr ),
     * null when it cannot be started. Both streams go to temporary files, never to pipes read one after the other:
     * Composer writes its progress to stderr, and with stdout read to the end first, a full stderr pipe (a few
     * kilobytes on Windows) left Composer and the upgrade waiting for each other until the job was killed.
     *
     * @param string[] $command
     * @param string $cwd
     * @param array $env
     * @return array|null
     */
    public static function runCommand( array $command, $cwd, array $env )
    {
        $dir = sys_get_temp_dir();
        $out = tempnam( $dir, 'expcomposer-out-' );
        $err = tempnam( $dir, 'expcomposer-err-' );
        if ( $out === false || $err === false )
        {
            foreach ( array( $out, $err ) as $file )
                if ( $file !== false )
                    @unlink( $file );
            return null;
        }
        $process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $out, 'w' ), 2 => array( 'file', $err, 'w' ) ),
                              $pipes, $cwd, $env );
        if ( !is_resource( $process ) )
        {
            @unlink( $out );
            @unlink( $err );
            return null;
        }
        fclose( $pipes[0] );
        $exit = proc_close( $process );
        $result = array( 'exit' => $exit, 'output' => (string)@file_get_contents( $out ), 'errors' => (string)@file_get_contents( $err ) );
        @unlink( $out );
        @unlink( $err );
        return $result;
    }

    /**
     * What composer.lock asks for and vendor/ does not hold: name => "installed -> locked".
     *
     * @param string $root
     * @return array|null null without a composer.lock
     */
    public static function lockDifferences( $root )
    {
        $lock = self::json( "$root/composer.lock" );
        if ( !$lock )
            return null;
        $installed = self::json( "$root/vendor/composer/installed.json" );
        $have = array();
        $dev = true;
        if ( $installed )
        {
            $packages = isset( $installed['packages'] ) ? $installed['packages'] : $installed;
            $dev = !isset( $installed['dev'] ) || $installed['dev'];
            foreach ( (array)$packages as $p )
                if ( isset( $p['name'] ) )
                    $have[strtolower( $p['name'] )] = self::identity( $p );
        }
        $want = array();
        foreach ( isset( $lock['packages'] ) ? $lock['packages'] : array() as $p )
            $want[strtolower( $p['name'] )] = self::identity( $p );
        if ( $dev )
            foreach ( isset( $lock['packages-dev'] ) ? $lock['packages-dev'] : array() as $p )
                $want[strtolower( $p['name'] )] = self::identity( $p );
        $diff = array();
        foreach ( $want as $name => $id )
            if ( !isset( $have[$name] ) )
                $diff[$name] = "not installed -> $id";
            else if ( $have[$name] !== $id )
                $diff[$name] = $have[$name] . " -> $id";
        ksort( $diff );
        return $diff;
    }

    /**
     * The packages Composer would change that live in a git checkout of their own (extension/<x>/ or
     * vendor/<a>/<b>/ with a .git and a composer.json naming the package): kept by hand or by the code stage,
     * and composer install would replace them. name => path.
     *
     * @param string $root
     * @param string[] $names the package names Composer would change
     * @return array
     */
    public static function gitCheckouts( $root, array $names )
    {
        if ( !$names )
            return array();
        $wanted = array_flip( array_map( 'strtolower', $names ) );
        $found = array();
        foreach ( array_merge( glob( "$root/extension/*/composer.json" ) ?: array(), glob( "$root/vendor/*/*/composer.json" ) ?: array() ) as $file )
        {
            $dir = dirname( $file );
            if ( !file_exists( "$dir/.git" ) )
                continue;
            $data = self::json( $file );
            $name = is_array( $data ) && isset( $data['name'] ) ? strtolower( (string)$data['name'] ) : '';
            if ( $name !== '' && isset( $wanted[$name] ) )
                $found[$name] = substr( $dir, strlen( $root ) + 1 );
        }
        ksort( $found );
        return $found;
    }

    /** Whether vendor/ was installed with the dev packages (installed.json "dev"); true when unknown. */
    public static function installedWithDev( $root )
    {
        $installed = self::json( "$root/vendor/composer/installed.json" );
        return !$installed || !isset( $installed['dev'] ) || (bool)$installed['dev'];
    }

    /**
     * The security advisories of the locked packages (composer audit --locked): lines "package: title (CVE)".
     *
     * @param expUpgradeContext $context
     * @return string[]|null null when the audit could not run
     */
    public static function audit( expUpgradeContext $context )
    {
        $r = self::run( $context, array( 'audit', '--locked', '--format=json' ) );
        $data = json_decode( $r['output'], true );
        if ( !is_array( $data ) )
            return null;
        $lines = array();
        foreach ( isset( $data['advisories'] ) ? (array)$data['advisories'] : array() as $package => $list )
            foreach ( (array)$list as $a )
                $lines[] = sprintf( '%s: %s%s', $package, isset( $a['title'] ) ? $a['title'] : 'advisory',
                                    !empty( $a['cve'] ) ? ' (' . $a['cve'] . ')' : '' );
        foreach ( isset( $data['abandoned'] ) ? (array)$data['abandoned'] : array() as $package => $replacement )
            $lines[] = sprintf( '%s: abandoned%s', $package, $replacement ? ", use $replacement" : '' );
        return $lines;
    }

    /**
     * Why the process may not write into the root, or '' when it may: it must run as the owner of the root.
     *
     * @param string $path
     * @return string
     */
    public static function ownerProblem( $path )
    {
        if ( !function_exists( 'posix_geteuid' ) || !file_exists( $path ) )
            return '';
        $owner = fileowner( $path );
        if ( $owner === false || $owner === posix_geteuid() )
            return '';
        $name = function_exists( 'posix_getpwuid' ) && ( $pw = posix_getpwuid( $owner ) ) ? $pw['name'] : (string)$owner;
        return "$path belongs to $name: run the upgrade as $name (sudo -u $name), files written by another user break the site";
    }

    /** @return string the version or, for a dev version, the reference of a package */
    protected static function identity( array $p )
    {
        $version = isset( $p['version'] ) ? (string)$p['version'] : '';
        $ref = isset( $p['source']['reference'] ) ? (string)$p['source']['reference'] : ( isset( $p['dist']['reference'] ) ? (string)$p['dist']['reference'] : '' );
        return $ref !== '' && ( strpos( $version, 'dev-' ) === 0 || substr( $version, -4 ) === '-dev' ) ? "$version@" . substr( $ref, 0, 10 ) : $version;
    }

    /** @return array|null */
    protected static function json( $file )
    {
        if ( !is_readable( $file ) )
            return null;
        $data = json_decode( (string)file_get_contents( $file ), true );
        return is_array( $data ) ? $data : null;
    }
}
