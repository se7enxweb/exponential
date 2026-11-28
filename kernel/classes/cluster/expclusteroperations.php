<?php
/**
 * File containing the expClusterOperations class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The ready-made operations of the cluster commands, as steps for expClusterRemote: what each host runs for
 * exp:cluster:cache, exp:cluster:deploy and exp:cluster:status, and the reading of a status report.
 *
 * Each operation uses the installation's own commands on the host (exp:ezcache, exp:velocity), so a host does
 * exactly what an operator logged in there would do, in the same order.
 *
 * @since 6.0.15
 * @package kernel
 */
class expClusterOperations
{
    /**
     * The steps that clear the caches of one host.
     *
     * Without full: exp:ezcache --clear-all (or the tags and ids given) clears every cache of the cache list, and with
     * it, through the cache clear listeners of site.ini [Cache] ClearListeners[], the caches extensions keep elsewhere
     * (the Valkey INI and view keys); the template-block cache is one of them. Then, on a Velocity host, Velocity's
     * response cache (exp:velocity cache clear --apply), last, so no page rendered before the clear stays stored.
     *
     * With full: exp:velocity deploy --all-caches --no-autoload, which also reloads PHP-FPM and restarts Velocity in
     * the right order (as the deploy user).
     *
     * @param array $host
     * @param array $options tags (string[]), ids (string[]), purge (bool), full (bool)
     * @return array list of steps
     */
    public static function cacheSteps( array $host, array $options = array() )
    {
        if ( !empty( $options['full'] ) )
        {
            $argv = array( 'exp:velocity', 'deploy', '--all-caches', '--no-autoload' );
            return array( array( 'label' => 'every cache, PHP-FPM and Velocity (exp:velocity deploy --all-caches)', 'as' => 'deploy',
                                 'console' => self::withHostDeployOptions( $host, $argv ) ) );
        }
        $tags = isset( $options['tags'] ) ? array_values( array_filter( (array)$options['tags'], 'strlen' ) ) : array();
        $ids = isset( $options['ids'] ) ? array_values( array_filter( (array)$options['ids'], 'strlen' ) ) : array();
        $argv = array( 'exp:ezcache' );
        if ( !$tags && !$ids )
            $argv[] = '--clear-all';
        if ( $tags )
            $argv[] = '--clear-tag=' . implode( ',', $tags );
        if ( $ids )
            $argv[] = '--clear-id=' . implode( ',', $ids );
        if ( !empty( $options['purge'] ) )
            $argv[] = '--purge';
        $steps = array( array( 'label' => 'caches (exp:ezcache ' . implode( ' ', array_slice( $argv, 1 ) ) . ')', 'console' => $argv ) );
        if ( $host['engine'] === 'velocity' )
            $steps[] = array( 'label' => 'Velocity response cache', 'console' => array( 'exp:velocity', 'cache', 'clear', '--apply' ) );
        return $steps;
    }

    /**
     * The step that deploys one host: exp:velocity deploy with the host's DeployOptions[] and the options given, as
     * the deploy user. A host with Engine=fpm gets --no-velocity.
     *
     * @param array $host
     * @param string[] $options deploy options, e.g. --kernel, --packer, --dry-run
     * @return array list of steps
     */
    public static function deploySteps( array $host, array $options = array() )
    {
        $argv = self::withHostDeployOptions( $host, array_merge( array( 'exp:velocity', 'deploy' ), array_values( $options ) ) );
        return array( array( 'label' => 'deploy (exp:velocity ' . implode( ' ', array_slice( $argv, 1 ) ) . ')', 'as' => 'deploy', 'console' => $argv ) );
    }

    /**
     * The step that reports a host's state as key=value lines: version, php, git_head, git_branch, git_date,
     * maintenance, and on a Velocity host velocity (running or down). It changes nothing.
     *
     * @param array $host
     * @return array list of steps
     */
    public static function statusSteps( array $host )
    {
        $script = 'php=$1' . "\n"
                . 'printf "version=%s\n" "$("$php" bin/php/console --version 2>/dev/null | head -n 1)"' . "\n"
                . 'printf "php=%s\n" "$("$php" -v 2>/dev/null | head -n 1)"' . "\n"
                . 'git_() { git -c safe.directory="*" "$@" 2>/dev/null; }' . "\n"
                . 'printf "git_head=%s\n" "$(git_ rev-parse --short HEAD)"' . "\n"
                . 'printf "git_branch=%s\n" "$(git_ rev-parse --abbrev-ref HEAD)"' . "\n"
                . 'printf "git_date=%s\n" "$(git_ log -1 --format=%cI)"' . "\n"
                . 'if [ -f var/maintenance.json ]; then echo maintenance=on; else echo maintenance=off; fi' . "\n";
        if ( $host['engine'] === 'velocity' )
            $script .= 'if "$php" bin/php/console exp:velocity status $2 >/dev/null 2>&1; then echo velocity=running; else echo velocity=down; fi' . "\n";
        return array( array( 'label' => 'status', 'shell' => $script ) );
    }

    /**
     * Reads the key=value lines of a status step.
     *
     * @param string $stdout
     * @return array key => value: version, php, git_head, git_branch, git_date, maintenance, velocity (when reported)
     */
    public static function parseStatus( $stdout )
    {
        $status = array();
        foreach ( preg_split( '/\r?\n/', (string)$stdout ) as $line )
        {
            if ( !preg_match( '/^([a-z_]+)=(.*)$/', $line, $m ) )
                continue;
            $value = trim( $m[2] );
            if ( $m[1] === 'version' )
                $value = preg_replace( '/^console \(Exponential\)\s*/', '', $value );
            if ( $m[1] === 'php' )
                $value = preg_replace( '/^PHP\s+(\S+).*$/', '$1', $value );
            $status[$m[1]] = $value;
        }
        return $status;
    }

    /** A deploy command line with the host's DeployOptions[], and --no-velocity on an fpm host, each option once. */
    protected static function withHostDeployOptions( array $host, array $argv )
    {
        $extra = $host['deploy_options'];
        if ( $host['engine'] === 'fpm' )
            $extra[] = '--no-velocity';
        foreach ( $extra as $option )
            if ( !in_array( $option, $argv, true ) )
                $argv[] = $option;
        return $argv;
    }
}
