<?php
/**
 * @description Deploy this installation on every host of settings/cluster.ini: exp:velocity deploy on each, with the host's own options
 * @alias exp:cluster:deploy
 *
 * File containing the exp:cluster:deploy command (bin/php/expclusterdeploy.php); guide: doc/eng-us/features/6.0/cluster-host-commands.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expclusterdeploy extends Expclusterbase
{
    /** The options of exp:velocity deploy this command hands on. */
    const DEPLOY_FLAGS = array( 'kernel', 'packer', 'no-autoload', 'no-fpm', 'fpm-reload', 'no-velocity', 'rebuild-phar', 'clear-content',
                                'all-caches', 'migrate', 'no-migrate' );

    public function run()
    {
        $this->script( array( 'description' => "Deploy on every host\n" .
                                               "Runs exp:velocity deploy on each host of settings/cluster.ini, one after the other by\n" .
                                               "default: autoloads, caches, PHP-FPM, Velocity and the caches of rendered output, in that\n" .
                                               "order, PASS/FAIL per host. Each host adds its DeployOptions[]; a host with Engine=fpm\n" .
                                               "gets --no-velocity. It runs as the host's DeployUser=, else the ssh login, because it\n" .
                                               "reloads PHP-FPM. The code must already be on the hosts (git pull, rsync): this command\n" .
                                               "does not copy files.\n" .
                                               "\n" .
                                               "./console exp:cluster:deploy\n" .
                                               "./console exp:cluster:deploy --kernel --packer\n" .
                                               "./console exp:cluster:deploy --hosts=web --remote-dry-run\n" .
                                               "./console exp:cluster:deploy --dry-run\n" .
                                               "\n" .
                                               "Exit code: 0 every host passed; 1 a host failed; 2 wrong options or settings.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $flags = '';
        $help = array();
        foreach ( self::DEPLOY_FLAGS as $flag )
        {
            $flags .= "[$flag]";
            $help[$flag] = "exp:velocity deploy --$flag on every host";
        }
        $options = $this->startup( $flags . '[remote-dry-run]' . self::HOST_OPTIONS . self::RUN_OPTIONS, '', $help + array(
            'remote-dry-run' => 'Each host prints its deploy steps (exp:velocity deploy --dry-run) and does nothing',
        ) + self::HOST_HELP + self::RUN_HELP );
        $hosts = $this->loadHosts();
        $selected = $this->selectHosts( $hosts, $options );
        $deploy = self::deployOptions( $options );
        $steps = array();
        foreach ( $selected as $name => $host )
            $steps[$name] = \expClusterOperations::deploySteps( $host, $deploy );
        $what = str_replace( '--dry-run', '--remote-dry-run', implode( ' ', $deploy ) );
        $this->execute( $hosts, $selected, $steps, $options, trim( 'exp:cluster:deploy ' . $what ) );
    }

    /**
     * The options for exp:velocity deploy on the hosts.
     *
     * @param array $options
     * @return string[]
     */
    public static function deployOptions( array $options )
    {
        $deploy = array();
        foreach ( self::DEPLOY_FLAGS as $flag )
            if ( !empty( $options[$flag] ) )
                $deploy[] = '--' . $flag;
        if ( !empty( $options['remote-dry-run'] ) )
            $deploy[] = '--dry-run';
        return $deploy;
    }
}

}
?>
