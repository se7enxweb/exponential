<?php
/**
 * File containing the expSysCardVelocityProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Exponential Velocity, the application server of 6.0.15: its version, whether it runs, the live and configured
 * workers, the ports and TLS, the environment file (the number of variables; exp:info their names, never their
 * values) and demo mode. Read from velocity.ini, the pid file and its .json, /proc and the envvars file; nothing is
 * run and nothing is asked over the network.
 */
class expSysCardVelocityProvider extends expSysCardBaseProvider
{
    protected $id = 'velocity';
    protected $section = 'engine';
    protected $priority = 70;
    protected $budget = 1.0;
    protected $watched = true;

    const PACKAGES = array( 'se7enxweb/exponential-velocity', 'se7enxweb/qbix-webserver' );

    public function collect( expSysCardContext $context )
    {
        $facts = array();
        $version = (string)$context->get( 'velocity.version', function () {
            if ( class_exists( '\Composer\InstalledVersions' ) )
                foreach ( self::PACKAGES as $package )
                    if ( \Composer\InstalledVersions::isInstalled( $package ) )
                        return (string)\Composer\InstalledVersions::getPrettyVersion( $package );
            return '';
        }, '' );
        if ( $version === '' )
        {
            // a git checkout of the engine: the newest release heading of its changelog
            $changelog = $context->file( 'vendor/se7enxweb/exponential-velocity/CHANGELOG.md', 65536 );
            $version = self::match( '/^##\s+(v\d+(?:\.\d+)+)\b/m', $changelog );
        }
        $pidFile = (string)$context->ini( 'velocity.ini', 'ServerSettings', 'PidFile', 'var/vc/qbix/run/server.pid' );
        $pid = (int)trim( (string)$context->file( $pidFile, 64 ) );
        $info = json_decode( (string)$context->file( $pidFile . '.json', 262144 ), true );
        $args = is_array( $info ) && isset( $info['args'] ) && is_array( $info['args'] ) ? self::arguments( $info['args'] ) : array();
        $installed = $version !== '' || $context->isFile( 'vendor/se7enxweb/exponential-velocity/qbixserver.php' ) || $pid > 0;
        if ( !$installed )
        {
            $facts[] = $this->fact( 'server', 'Velocity', 'not installed' );
            return $facts;
        }
        $running = $pid > 0 && $context->isFile( '/proc/' . $pid . '/status' );
        $started = is_array( $info ) && isset( $info['started'] ) && is_numeric( $info['started'] ) ? (int)$info['started'] : 0;
        $state = $running ? 'running' . ( $started > 0 && $started <= $context->now ? ', up ' . self::duration( $context->now - $started ) : '' ) : 'stopped';
        $facts[] = $this->fact( 'server', 'Velocity', trim( ( $version !== '' ? $version . ', ' : '' ) . $state ),
                                $running ? expSysCardFact::OK : expSysCardFact::INFO );

        $configured = isset( $args['workers'] ) ? (int)$args['workers'] : (int)$context->ini( 'velocity.ini', 'ServerSettings', 'Workers', 0 );
        $live = $running ? $this->descendants( $context, $pid ) : 0;
        $instances = array();
        foreach ( $context->glob( preg_replace( '/\.pid$/', '', $context->path( $pidFile ) ) . '.*.pid' ) as $file )
        {
            if ( !preg_match( '/\.(\d+)\.pid$/', $file, $m ) )
                continue;
            $instancePid = (int)trim( (string)$context->file( $file, 64 ) );
            if ( $instancePid > 0 && $instancePid !== $pid && $context->isFile( '/proc/' . $instancePid . '/status' ) )
                $instances[(int)$m[1]] = array( 'pid' => $instancePid, 'workers' => $this->descendants( $context, $instancePid ) );
        }
        if ( $running || $configured > 0 )
        {
            $text = $running ? $live . ' live' . ( $configured > 0 ? ' / ' . $configured . ' configured' : '' ) : $configured . ' configured';
            if ( !empty( $instances ) )
                $text .= ', ' . ( count( $instances ) + 1 ) . ' instances';
            $facts[] = $this->fact( 'workers', 'Workers', $text );
        }
        $port = isset( $args['port'] ) ? (int)$args['port'] : (int)$context->ini( 'velocity.ini', 'ServerSettings', 'Port', 0 );
        $https = isset( $args['https-port'] ) ? (int)$args['https-port'] : (int)$context->ini( 'velocity.ini', 'ServerSettings', 'HTTPSPort', 0 );
        $ports = array();
        if ( $port > 0 )
            $ports[] = $port . ' (HTTP)';
        if ( $https > 0 )
            $ports[] = $https . ' (HTTPS, TLS)';
        if ( !empty( $ports ) )
            $facts[] = $this->fact( 'ports', 'Ports', implode( ', ', $ports ) );

        $confDir = isset( $args['conf-dir'] ) ? $args['conf-dir'] : '';
        if ( $confDir === '' )
            foreach ( array( '/etc/vc', '/etc/qbix' ) as $dir )
                if ( $context->isFile( $dir . '/envvars' ) )
                {
                    $confDir = $dir;
                    break;
                }
        $names = array();
        $demo = null;
        if ( $confDir !== '' )
        {
            $envvars = $context->file( rtrim( $confDir, '/' ) . '/envvars', 65536 );
            if ( $envvars !== null )
            {
                foreach ( explode( "\n", $envvars ) as $line )
                {
                    if ( !preg_match( '/^\s*(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)=(.*)$/', $line, $m ) )
                        continue;
                    $names[] = $m[1];
                    if ( $m[1] === 'EXP_DEMO_MODE' )
                        $demo = trim( $m[2], " \t\"'" ) === '1';
                }
                $names = array_values( array_unique( $names ) );
                $facts[] = $this->fact( 'envvars', 'Envvars', $context->mode === expSysCardContext::MODE_INFO && !empty( $names )
                                                    ? implode( ', ', $names ) : count( $names ) . ' ' . ( count( $names ) === 1 ? 'variable' : 'variables' ) );
            }
        }
        if ( $demo !== null )
            $facts[] = $this->fact( 'demo', 'Demo mode', $demo ? 'on (Velocity)' : 'off (Velocity)', $demo ? expSysCardFact::WARN : expSysCardFact::INFO,
                                    $demo ? 'Velocity runs in demo mode (EXP_DEMO_MODE=1 in its envvars): administrator passwords cannot be changed. Right for a public demo, wrong for a production site.' : '' );
        if ( $context->mode === expSysCardContext::MODE_INFO )
        {
            if ( $running )
                $facts[] = $this->fact( 'instance0', 'Instance 1', 'pid ' . $pid . ', ' . $live . ' workers' );
            foreach ( $instances as $n => $instance )
                $facts[] = $this->fact( 'instance' . $n, 'Instance ' . ( $n + 1 ), 'pid ' . $instance['pid'] . ', ' . $instance['workers'] . ' workers' );
            $fork = $context->iniEnabled( 'velocity.ini', 'ServerSettings', 'ForkPerRequest', 'disabled' );
            $facts[] = $this->fact( 'model', 'Worker model', $fork ? 'fork per request' : 'persistent workers' );
        }
        return $facts;
    }

    /**
     * The --name=value arguments of the server's command line.
     *
     * @param array $args
     * @return array name => value
     */
    public static function arguments( array $args )
    {
        $parsed = array();
        foreach ( $args as $arg )
            if ( is_string( $arg ) && preg_match( '/^--([a-z][a-z0-9-]*)=(.*)$/', $arg, $m ) )
                $parsed[$m[1]] = $m[2];
        return $parsed;
    }

    /**
     * The number of processes below a process (its workers and their children), two levels down.
     *
     * @param expSysCardContext $context
     * @param int $pid
     * @param int $depth
     * @return int
     */
    protected function descendants( expSysCardContext $context, $pid, $depth = 2 )
    {
        $children = preg_split( '/\s+/', trim( (string)$context->file( '/proc/' . $pid . '/task/' . $pid . '/children', 65536 ) ), -1, PREG_SPLIT_NO_EMPTY );
        $count = 0;
        foreach ( is_array( $children ) ? $children : array() as $child )
        {
            if ( !ctype_digit( $child ) )
                continue;
            $count++;
            if ( $depth > 1 && $context->remaining() > 0.05 )
                $count += $this->descendants( $context, (int)$child, $depth - 1 );
        }
        return $count;
    }
}
