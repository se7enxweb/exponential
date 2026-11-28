<?php
/**
 * @description List the hosts of settings/cluster.ini and check their settings; --check also logs in on each
 * @alias exp:cluster:hosts
 *
 * File containing the exp:cluster:hosts command (bin/php/expclusterhosts.php); guide: doc/eng-us/features/6.0/cluster-host-commands.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expclusterhosts extends Expclusterbase
{
    public function run()
    {
        $this->script( array( 'description' => "List the hosts of settings/cluster.ini\n" .
                                               "Each host with its ssh target, port, root, PHP binary, users and engine, and what is wrong\n" .
                                               "with the settings. --check also logs in on each host and checks that its root holds\n" .
                                               "bin/php/console and that its PHP binary runs.\n" .
                                               "\n" .
                                               "./console exp:cluster:hosts\n" .
                                               "./console exp:cluster:hosts --check\n" .
                                               "./console exp:cluster:hosts --json\n" .
                                               "\n" .
                                               "Exit code: 0 the settings are right (and with --check every host answered); 1 a host failed\n" .
                                               "the check; 2 the settings are wrong.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startup( '[check]' . self::HOST_OPTIONS . self::RUN_OPTIONS, '', array(
            'check' => 'Log in on each host: its root holds bin/php/console, its PHP binary runs',
        ) + self::HOST_HELP + self::RUN_HELP );
        $hosts = \expClusterHosts::fromINI();
        $out = $this->cli();
        if ( !empty( $options['json'] ) && empty( $options['check'] ) )
        {
            $out->output( self::json( array( 'schema' => 'exp-cluster-hosts/1', 'settings' => $hosts->settings(), 'hosts' => $hosts->hosts(),
                                             'errors' => $hosts->errors() ) ) );
            $this->shutdown( $hosts->errors() ? self::EXIT_USAGE : self::EXIT_OK );
            return;
        }
        if ( empty( $options['json'] ) )
        {
            foreach ( self::describe( $hosts ) as $line )
                $out->output( $line );
        }
        if ( $hosts->errors() )
        {
            foreach ( $hosts->errors() as $error )
                $out->error( "FAIL cluster.ini: $error" );
            $this->shutdown( self::EXIT_USAGE );
            return;
        }
        if ( empty( $options['check'] ) )
        {
            $count = count( $hosts->hosts() );
            $out->output( $count ? "PASS the settings of $count host(s) are complete" : 'No hosts in [ClusterSettings] HostList[]' );
            $this->shutdown( self::EXIT_OK );
            return;
        }
        $selected = $this->selectHosts( $hosts, $options );
        $this->execute( $hosts, $selected, array( self::checkStep() ), $options, 'exp:cluster:hosts --check' );
    }

    /**
     * The step of --check: the root holds the console and the PHP binary runs, as the user commands run as.
     *
     * @return array
     */
    public static function checkStep()
    {
        return array( 'label' => 'check (root, console, PHP)',
                      'shell' => 'test -f bin/php/console || { echo "no bin/php/console in $(pwd)" >&2; exit 1; }' . "\n"
                               . '"$1" -v | head -n 1' . "\n"
                               . 'echo "user $(id -un) in $(pwd)"' . "\n" );
    }

    /**
     * The hosts as text lines.
     *
     * @param \expClusterHosts $hosts
     * @return string[]
     */
    public static function describe( \expClusterHosts $hosts )
    {
        $settings = $hosts->settings();
        $lines = array( sprintf( 'settings/cluster.ini: %d host(s), %s, timeout %d s, connect timeout %d s', count( $hosts->hosts() ), $settings['mode'],
                                 $settings['timeout'], $settings['connect_timeout'] ) );
        foreach ( $hosts->hosts() as $name => $host )
        {
            $lines[] = '';
            $lines[] = "[$name]";
            $lines[] = '  ssh         ' . $host['ssh'] . ( $host['port'] ? ' port ' . $host['port'] : '' );
            $lines[] = '  root        ' . $host['root'];
            $lines[] = '  php         ' . $host['php'];
            $lines[] = '  user        ' . ( $host['user'] !== '' ? $host['user'] : '(the ssh login)' );
            $lines[] = '  deploy as   ' . ( $host['deploy_user'] !== '' ? $host['deploy_user'] : '(the ssh login)' );
            $lines[] = '  engine      ' . $host['engine'];
            if ( $host['deploy_options'] )
                $lines[] = '  deploy      ' . implode( ' ', $host['deploy_options'] );
        }
        return $lines;
    }
}

}
?>
