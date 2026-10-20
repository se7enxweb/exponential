<?php
/**
 * File containing the expSysCardHostProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The machine: operating system, kernel, host name, uptime, load, CPU, memory, the disk of the installation (exp:info:
 * every local disk), the shell and the terminal. Read from /etc/os-release, /proc and PHP; nothing is run.
 */
class expSysCardHostProvider extends expSysCardBaseProvider
{
    protected $id = 'host';
    protected $section = 'system';
    protected $modes = array( expSysCardContext::MODE_CARD );
    protected $priority = 90;
    protected $budget = 1.0;
    protected $watched = true;

    /** File systems exp:info lists as disks */
    const DISK_TYPES = array( 'ext2', 'ext3', 'ext4', 'xfs', 'btrfs', 'zfs', 'f2fs', 'jfs', 'reiserfs', 'apfs', 'hfs', 'ntfs', 'vfat', 'exfat' );

    public function collect( expSysCardContext $context )
    {
        $facts = array();
        $release = (string)$context->file( '/etc/os-release' );
        $os = self::match( '/^PRETTY_NAME="?([^"\n]+)"?$/m', $release );
        if ( $os === '' )
            $os = trim( self::match( '/^NAME="?([^"\n]+)"?$/m', $release ) . ' ' . self::match( '/^VERSION_ID="?([^"\n]+)"?$/m', $release ) );
        if ( $os === '' )
            $os = (string)$context->get( 'host.os', function () { return php_uname( 's' ) . ' ' . php_uname( 'r' ); }, PHP_OS_FAMILY );
        $facts[] = $this->fact( 'os', 'OS', $os );
        $kernel = (string)$context->get( 'host.kernel', function () { return php_uname( 's' ) . ' ' . php_uname( 'r' ) . ' ' . php_uname( 'm' ); }, '' );
        if ( $kernel !== '' )
            $facts[] = $this->fact( 'kernel', 'Kernel', $kernel );
        $hostname = (string)$context->get( 'host.name', function () { return php_uname( 'n' ); }, '' );
        if ( $hostname !== '' )
            $facts[] = $this->fact( 'name', 'Host', $hostname );

        $uptime = $context->file( '/proc/uptime' );
        if ( $uptime !== null && is_numeric( strtok( trim( $uptime ), ' ' ) ) )
            $facts[] = $this->fact( 'uptime', 'Uptime', self::duration( (float)strtok( trim( $uptime ), ' ' ) ) );

        $cpuinfo = (string)$context->file( '/proc/cpuinfo' );
        $cores = $cpuinfo !== '' ? preg_match_all( '/^processor\s*:/m', $cpuinfo ) : 0;
        $model = self::match( '/^model name\s*:\s*(.+)$/m', $cpuinfo );
        if ( $model === '' )
            $model = self::match( '/^Hardware\s*:\s*(.+)$/m', $cpuinfo );
        $model = (string)preg_replace( '/\s+/', ' ', str_replace( array( '(R)', '(TM)', '(tm)', ' CPU', ' Processor' ), '', $model ) );

        $load = $context->get( 'host.load', function () { return function_exists( 'sys_getloadavg' ) ? sys_getloadavg() : null; } );
        if ( is_array( $load ) && count( $load ) >= 3 )
        {
            $text = implode( ' ', array_map( function ( $v ) { return number_format( (float)$v, 2, '.', '' ); }, array_slice( $load, 0, 3 ) ) );
            $severity = expSysCardFact::INFO;
            $hint = '';
            if ( $cores > 0 && (float)$load[0] > 2 * $cores )
            {
                $severity = expSysCardFact::WARN;
                $hint = sprintf( 'The load (%s) is more than twice the %d cores: the machine is busy; look at what runs (top) before a deploy or a reindex.', number_format( (float)$load[0], 1 ), $cores );
            }
            $facts[] = $this->fact( 'load', 'Load', $text, $severity, $hint );
        }
        if ( $model !== '' || $cores > 0 )
            $facts[] = $this->fact( 'cpu', 'CPU', trim( $model . ( $cores > 0 ? ' (' . $cores . ' ' . ( $cores === 1 ? 'core' : 'cores' ) . ')' : '' ) ) );

        $meminfo = (string)$context->file( '/proc/meminfo' );
        $total = (int)self::match( '/^MemTotal:\s*(\d+)\s*kB/m', $meminfo ) * 1024;
        $available = self::match( '/^MemAvailable:\s*(\d+)\s*kB/m', $meminfo );
        if ( $total > 0 )
        {
            $free = $available !== '' ? (int)$available * 1024 : null;
            if ( $free === null )
                $facts[] = $this->fact( 'memory', 'Memory', self::size( $total ) );
            else
            {
                $used = max( 0, $total - $free );
                $percent = (int)round( 100 * $used / $total );
                $severity = $percent >= 95 ? expSysCardFact::ERROR : ( $percent >= 90 ? expSysCardFact::WARN : expSysCardFact::INFO );
                $hint = $severity === expSysCardFact::INFO ? '' : sprintf( 'Memory is %d%% used: the web server and Velocity workers may be swapped out or killed; lower the number of workers or add memory.', $percent );
                $facts[] = $this->fact( 'memory', 'Memory', self::size( $used ) . ' / ' . self::size( $total ) . ' (' . $percent . '%)', $severity, $hint );
            }
        }

        $disks = array( $context->root => 'Disk' );
        if ( $context->mode === expSysCardContext::MODE_INFO )
            $disks += $this->mounts( $context );
        $seen = array();
        foreach ( $disks as $path => $label )
        {
            $space = $context->get( 'host.disk:' . $path, function () use ( $path ) {
                $total = @disk_total_space( $path );
                $free = @disk_free_space( $path );
                return $total ? array( (float)$total, (float)$free ) : null;
            } );
            if ( !is_array( $space ) || $space[0] <= 0 )
                continue;
            $signature = $space[0] . ':' . round( $space[1] / 1048576 );
            if ( isset( $seen[$signature] ) )
                continue;
            $seen[$signature] = true;
            $used = $space[0] - $space[1];
            $percent = (int)round( 100 * $used / $space[0] );
            $severity = $percent >= 97 ? expSysCardFact::ERROR : ( $percent >= 90 ? expSysCardFact::WARN : expSysCardFact::INFO );
            $where = $label === 'Disk' ? 'the disk of the installation' : 'the disk ' . $path;
            $hint = $severity === expSysCardFact::INFO ? '' : sprintf( '%s is %d%% full (%s free): clear old caches and logs (./console exp:cache), or add space before uploads and cache writes fail.', ucfirst( $where ), $percent, self::size( $space[1] ) );
            $facts[] = $this->fact( 'disk' . ( $label === 'Disk' ? '' : ':' . $path ), $label === 'Disk' ? 'Disk' : 'Disk ' . $path,
                                    self::size( $used ) . ' / ' . self::size( $space[0] ) . ' (' . $percent . '%)', $severity, $hint );
        }

        $shell = (string)$context->env( 'SHELL' );
        if ( $shell !== '' )
            $facts[] = $this->fact( 'shell', 'Shell', basename( $shell ) );
        $terminal = (string)$context->env( 'TERM_PROGRAM' );
        if ( $terminal !== '' && (string)$context->env( 'TERM_PROGRAM_VERSION' ) !== '' )
            $terminal .= ' ' . $context->env( 'TERM_PROGRAM_VERSION' );
        if ( $terminal === '' )
            $terminal = (string)$context->env( 'TERM' );
        $size = $context->get( 'host.terminal_size' );
        if ( $terminal !== '' )
            $facts[] = $this->fact( 'terminal', 'Terminal', $terminal . ( is_string( $size ) && $size !== '' ? ' (' . $size . ')' : '' ) );
        return $facts;
    }

    /**
     * The mount points of local disks (exp:info).
     *
     * @param expSysCardContext $context
     * @return array path => label
     */
    protected function mounts( expSysCardContext $context )
    {
        $list = array();
        foreach ( explode( "\n", (string)$context->file( '/proc/mounts' ) ) as $line )
        {
            $parts = preg_split( '/\s+/', trim( $line ) );
            if ( !is_array( $parts ) || count( $parts ) < 3 || !in_array( $parts[2], self::DISK_TYPES, true ) )
                continue;
            $path = str_replace( '\\040', ' ', $parts[1] );
            if ( strpos( $path, '/snap/' ) === 0 || strpos( $path, '/boot/efi' ) === 0 )
                continue;
            $list[$path] = 'Disk ' . $path;
            if ( count( $list ) >= 8 )
                break;
        }
        return $list;
    }
}
