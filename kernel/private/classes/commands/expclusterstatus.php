<?php
/**
 * @description The state of this installation on every host of settings/cluster.ini: version, PHP, git head, maintenance and Velocity
 * @alias exp:cluster:status
 *
 * File containing the exp:cluster:status command (bin/php/expclusterstatus.php); guide: doc/eng-us/features/6.0/cluster-host-commands.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expclusterstatus extends Expclusterbase
{
    public function run()
    {
        $this->script( array( 'description' => "The state of every host\n" .
                                               "For each host of settings/cluster.ini: the version (console --version), the PHP version,\n" .
                                               "the git head, branch and commit date of the installation, whether maintenance mode is on,\n" .
                                               "and on a host with Engine=velocity whether Velocity answers. Reads only. A table, and\n" .
                                               "a warning when the hosts are not on the same git head.\n" .
                                               "\n" .
                                               "./console exp:cluster:status\n" .
                                               "./console exp:cluster:status --parallel --json\n" .
                                               "\n" .
                                               "Exit code: 0 every host answered; 1 a host did not; 2 wrong options or settings.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startup( self::HOST_OPTIONS . self::RUN_OPTIONS, '', self::HOST_HELP + self::RUN_HELP );
        $hosts = $this->loadHosts();
        $selected = $this->selectHosts( $hosts, $options );
        $steps = array();
        foreach ( $selected as $name => $host )
            $steps[$name] = \expClusterOperations::statusSteps( $host );
        if ( !empty( $options['dry-run'] ) || !empty( $options['json'] ) )
        {
            $this->execute( $hosts, $selected, $steps, $options, 'exp:cluster:status' );
            return;
        }
        $settings = $hosts->settings();
        if ( !empty( $options['timeout'] ) && ctype_digit( (string)$options['timeout'] ) )
            $settings['timeout'] = (int)$options['timeout'];
        $parallel = !empty( $options['parallel'] ) || ( empty( $options['sequential'] ) && $settings['mode'] === 'parallel' );
        $remote = new \expClusterRemote( $settings );
        $results = $remote->run( $selected, $steps, $parallel );
        $this->record( 'exp:cluster:status', $results, $settings['log_file'] );
        foreach ( self::table( $results ) as $line )
            $this->cli()->output( $line );
        $summary = \expClusterRemote::summary( $results );
        foreach ( $summary['lines'] as $line )
            if ( strpos( $line, 'FAIL ' ) === 0 && $line !== end( $summary['lines'] ) )
                $this->cli()->output( $line );
        $this->cli()->output( (string)end( $summary['lines'] ) );
        $this->shutdown( $summary['exit'] );
    }

    /**
     * The status of the hosts as a table, with a warning when their git heads differ.
     *
     * @param array $results of expClusterRemote::run()
     * @return string[]
     */
    public static function table( array $results )
    {
        $rows = array( array( 'HOST', 'VERSION', 'PHP', 'GIT', 'BRANCH', 'MAINTENANCE', 'VELOCITY' ) );
        $heads = array();
        foreach ( $results as $name => $result )
        {
            $stdout = $result['steps'] ? $result['steps'][0]['stdout'] : '';
            $s = \expClusterOperations::parseStatus( $stdout );
            if ( !$s )
            {
                $rows[] = array( (string)$name, 'no answer', '-', '-', '-', '-', '-' );
                continue;
            }
            $get = function ( $key ) use ( $s ) { return isset( $s[$key] ) && $s[$key] !== '' ? $s[$key] : '-'; };
            $rows[] = array( (string)$name, $get( 'version' ), $get( 'php' ), $get( 'git_head' ), $get( 'git_branch' ), $get( 'maintenance' ), $get( 'velocity' ) );
            if ( isset( $s['git_head'] ) && $s['git_head'] !== '' )
                $heads[$s['git_head']][] = $name;
        }
        $widths = array();
        foreach ( $rows as $row )
            foreach ( $row as $i => $cell )
                $widths[$i] = max( isset( $widths[$i] ) ? $widths[$i] : 0, strlen( $cell ) );
        $lines = array();
        foreach ( $rows as $row )
        {
            $cells = array();
            foreach ( $row as $i => $cell )
                $cells[] = str_pad( $cell, $widths[$i] );
            $lines[] = rtrim( implode( '  ', $cells ) );
        }
        if ( count( $heads ) > 1 )
        {
            $parts = array();
            foreach ( $heads as $head => $names )
                $parts[] = $head . ' (' . implode( ', ', $names ) . ')';
            $lines[] = 'WARNING the hosts are on different git heads: ' . implode( ', ', $parts );
        }
        return $lines;
    }
}

}
?>
