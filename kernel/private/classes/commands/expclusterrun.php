<?php
/**
 * @description Run a console command (or, with --shell, a command line) in this installation on every host of settings/cluster.ini, over ssh
 * @alias exp:cluster:run
 *
 * File containing the exp:cluster:run command (bin/php/expclusterrun.php); guide: doc/eng-us/features/6.0/cluster-host-commands.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expclusterrun extends Expclusterbase
{
    public function run()
    {
        $this->script( array( 'description' => "Run a command in this installation on every host, over ssh\n" .
                                               "The hosts are settings/cluster.ini [ClusterSettings] HostList[] (or --hosts). The words\n" .
                                               "after -- are a console command each host runs as bin/php/console <words> in its root, with\n" .
                                               "its PHP binary, as its User= (else the ssh login); --shell runs them as a command line for\n" .
                                               "sh. One host after the other by default, --parallel all at once. PASS/FAIL per host.\n" .
                                               "\n" .
                                               "./console exp:cluster:run -- exp:cache status\n" .
                                               "./console exp:cluster:run --hosts=web,cluster -- exp:maintenance status\n" .
                                               "./console exp:cluster:run --parallel --shell -- uptime\n" .
                                               "./console exp:cluster:run --dry-run -- exp:ezcache --clear-tag=ini\n" .
                                               "\n" .
                                               "Exit code: 0 every host passed; 1 a host failed; 2 wrong options or settings.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startupSplit( '[shell][as:]' . self::HOST_OPTIONS . self::RUN_OPTIONS, '[words*]', array(
            'shell' => 'Run the words after -- as a command line for sh, not as a console command',
            'as' => 'Run as: user (the default: each host\'s User=, else the ssh login), deploy (DeployUser=) or login',
        ) + self::HOST_HELP + self::RUN_HELP );
        $words = $this->hasSeparator ? $this->remoteWords : $options['arguments'];
        $step = Exphostrun::remoteStep( $words, !empty( $options['shell'] ), isset( $options['as'] ) && $options['as'] ? (string)$options['as'] : 'user', $error );
        if ( $step === null )
            $this->fail( (string)$error, self::EXIT_USAGE );
        $hosts = $this->loadHosts();
        $selected = $this->selectHosts( $hosts, $options );
        $this->execute( $hosts, $selected, array( $step ), $options, 'exp:cluster:run -- ' . \expClusterRemote::quoteAll( $words ) );
    }
}

}
?>
