<?php
/**
 * The code of bin/php/install.php, moved into a class (#207 stage 1). The file bin/php/install.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/install.php:
 *
 *
 * @description One-command installation, SQLite by default, no kickstart.ini needed
 * @package   kernel
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   GNU General Public License v2.0 (or any later version)
 *
 * ./console exp:install [options]
 *
 * The kickstarter installs from a kickstart.ini that has to be written first,
 * by hand or with "exp:kickstarter ini". This command builds that
 * configuration from its options, with a default for every one of them, and
 * runs the same installation steps in the same process. Any kickstart.ini
 * already there is put back afterwards, untouched; the configuration that was
 * used is kept in var/log with its passwords masked.
 *
 */

namespace Exponential\Command\Kernel
{

class Install extends \Exponential\Runnable\Command
{
    /**
     * Why an administrator password cannot be installed, null when it can: shorter than
     * site.ini [UserSettings] MinPasswordLength (10), or one the setup refuses as well-known.
     *
     * @param string $password
     * @return string|null
     */
    public static function passwordProblem( $password )
    {
        if ( \eZStepSiteAdmin::isWeakDefaultPassword( $password ) )
            return 'well-known';
        $min = 10;
        if ( class_exists( 'eZINI', false ) )
        {
            $configured = (int)\eZINI::instance( 'site.ini' )->variable( 'UserSettings', 'MinPasswordLength' );
            if ( $configured > 0 )
                $min = $configured;
        }
        if ( strlen( $password ) < $min )
            return "shorter than $min characters";
        return null;
    }

    /** A random password: 24 characters of the bcrypt alphabet from the secure random source. */
    public static function generatePassword()
    {
        $alphabet = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $generated = '';
        for ( $i = 0; $i < 24; $i++ )
            $generated .= $alphabet[random_int( 0, strlen( $alphabet ) - 1 )];
        return $generated;
    }

    /**
     * The password the setup recorded in var/log/initial-admin-password, when that file was
     * written at or after $since (this run); null otherwise.
     *
     * @param string $file
     * @param int $since
     * @return string|null
     */
    public static function readRecordedPassword( $file, $since )
    {
        clearstatcache( true, $file );
        if ( !is_file( $file ) || filemtime( $file ) < $since )
            return null;
        if ( preg_match( '/^Password: (.+)$/m', (string)file_get_contents( $file ), $m ) )
            return rtrim( $m[1], "\r\n" );
        return null;
    }

    /** Records a generated password in the format of the setup (owner-only file). */
    public static function writeRecordedPassword( $file, $password, $why )
    {
        if ( !is_dir( dirname( $file ) ) )
            @mkdir( dirname( $file ), 0770, true );
        $old = umask( 0077 );
        $written = @file_put_contents( $file,
            "Exponential administrator login: admin\n" .
            "Password: $password\n" .
            "Generated " . date( 'c' ) . " by exp:install: $why.\n" .
            "Log in, change it, then delete this file.\n" );
        umask( $old );
        if ( $written !== false )
            @chmod( $file, 0600 );
        return $written !== false;
    }

    /** $array with $key => $value inserted right after $after (appended when $after is missing). */
    public static function insertAfter( array $array, $after, $key, $value )
    {
        $out = array();
        $done = false;
        foreach ( $array as $k => $v )
        {
            $out[$k] = $v;
            if ( $k === $after )
            {
                $out[$key] = $value;
                $done = true;
            }
        }
        if ( !$done )
            $out[$key] = $value;
        return $out;
    }

    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'exitCode', 'flags', 'forward', 'rootDir', 'runner' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $forward = array( '--force' );
        if ( $flags['dry-run'] )
            $forward = array( '--dry-run' );
        if ( $flags['allow-root-user'] )
            $forward[] = '--allow-root-user';

        $runner = new \expKickstarter( $rootDir, $forward );
        $exitCode = $runner->run();

        exit( $exitCode );
    }
}

}
