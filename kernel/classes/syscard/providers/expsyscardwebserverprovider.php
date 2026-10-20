<?php
/**
 * File containing the expSysCardWebServerProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The other web servers on the machine: Apache, nginx, Caddy, FrankenPHP, and PHP-FPM with its masters (one per PHP
 * version) and pool workers (exp:info: the pool names). Read from the process list in /proc (each process's name
 * and command line); nothing is run.
 */
class expSysCardWebServerProvider extends expSysCardBaseProvider
{
    protected $id = 'webserver';
    protected $section = 'engine';
    protected $priority = 40;
    protected $budget = 1.5;

    /** process name => label */
    const SERVERS = array( 'httpd' => 'Apache', 'apache2' => 'Apache', 'nginx' => 'nginx', 'caddy' => 'Caddy', 'frankenphp' => 'FrankenPHP' );

    public function collect( expSysCardContext $context )
    {
        $servers = array();
        $masters = array();
        $pools = array();
        $workers = 0;
        foreach ( $context->entries( '/proc' ) as $entry )
        {
            if ( !ctype_digit( (string)$entry ) )
                continue;
            if ( $context->remaining() < 0.05 )
                break;
            $name = trim( (string)$context->file( '/proc/' . $entry . '/comm', 256 ) );
            if ( isset( self::SERVERS[$name] ) )
            {
                $label = self::SERVERS[$name];
                $servers[$label] = isset( $servers[$label] ) ? $servers[$label] + 1 : 1;
                continue;
            }
            if ( strpos( $name, 'php-fpm' ) !== 0 )
                continue;
            $cmdline = str_replace( "\0", ' ', (string)$context->file( '/proc/' . $entry . '/cmdline', 4096 ) );
            if ( preg_match( '/php-fpm: master process \(([^)]*)\)/', $cmdline, $m ) )
            {
                $version = self::match( '#/php/?(\d+\.\d+)/#', $m[1] );
                if ( $version === '' && preg_match( '#php-?(\d)\.?(\d)#', $m[1], $v ) )
                    $version = $v[1] . '.' . $v[2];
                if ( $version === '' )
                    $version = 'system';
                $masters[] = $version;
            }
            elseif ( preg_match( '/php-fpm: pool (\S+)/', $cmdline, $m ) )
            {
                $workers++;
                $pools[$m[1]] = true;
            }
        }
        $facts = array();
        foreach ( $servers as $label => $count )
            $facts[] = $this->fact( strtolower( $label ), $label, 'running (' . $count . ' ' . ( $count === 1 ? 'process' : 'processes' ) . ')', expSysCardFact::OK );
        if ( !empty( $masters ) )
        {
            sort( $masters );
            $text = count( $masters ) . ' ' . ( count( $masters ) === 1 ? 'master' : 'masters' ) . ' (' . implode( ', ', array_unique( $masters ) ) . '), '
                  . $workers . ' pool ' . ( $workers === 1 ? 'worker' : 'workers' );
            $facts[] = $this->fact( 'fpm', 'PHP-FPM', $text, expSysCardFact::OK );
            if ( $context->mode === expSysCardContext::MODE_INFO && !empty( $pools ) )
            {
                $names = array_keys( $pools );
                sort( $names );
                $facts[] = $this->fact( 'pools', 'FPM pools', implode( ', ', $names ) );
            }
        }
        if ( empty( $facts ) )
            $facts[] = $this->fact( 'none', 'Web server', 'none found (only Velocity, or not visible to this user)' );
        return $facts;
    }
}
