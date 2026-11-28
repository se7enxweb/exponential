<?php
/**
 * @description Run a console command (or, with --shell, a command line) in this installation on one host of settings/cluster.ini, over ssh
 * @alias exp:host:run
 *
 * File containing the exp:host:run command (bin/php/exphostrun.php); guide: doc/eng-us/features/6.0/cluster-host-commands.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exphostrun extends Expclusterbase
{
    public function run()
    {
        $this->script( array( 'description' => "Run a command in this installation on one host, over ssh\n" .
                                               "The host is one of settings/cluster.ini [ClusterSettings] HostList[]. The words after --\n" .
                                               "are a console command run there as bin/php/console <words>, in the installation's root\n" .
                                               "on that host, with its PHP binary, as its User= (sudo -n), else as the ssh login.\n" .
                                               "--shell runs the words as a command line for sh instead. ssh runs with BatchMode=yes:\n" .
                                               "a host that would ask for a password fails. Nothing is interactive.\n" .
                                               "\n" .
                                               "./console exp:host:run web -- exp:cache status\n" .
                                               "./console exp:host:run web -- exp:ezcache --clear-tag=template\n" .
                                               "./console exp:host:run web --shell -- git log -1 --oneline\n" .
                                               "./console exp:host:run web --dry-run -- exp:velocity deploy\n" .
                                               "\n" .
                                               "Exit code: 0 PASS; 1 the command failed on the host (or ssh did); 2 wrong options or settings.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startupSplit( '[shell][as:]' . self::RUN_OPTIONS, '[host][words*]', array(
            'shell' => 'Run the words after -- as a command line for sh, not as a console command',
            'as' => 'Run as: user (the default: the host\'s User=, else the ssh login), deploy (DeployUser=) or login',
        ) + self::RUN_HELP );
        $name = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '';
        if ( $name === '' )
            $this->fail( 'Name the host: exp:host:run <host> -- <command>', self::EXIT_USAGE );
        $words = $this->hasSeparator ? $this->remoteWords : array_slice( $options['arguments'], 1 );
        $step = self::remoteStep( $words, !empty( $options['shell'] ), isset( $options['as'] ) && $options['as'] ? (string)$options['as'] : 'user', $error );
        if ( $step === null )
            $this->fail( (string)$error, self::EXIT_USAGE );
        $hosts = $this->loadHosts();
        $selected = $hosts->select( $name, $unknown );
        if ( $unknown || !$selected )
            $this->fail( "Not a host of [ClusterSettings] HostList[]: $name (exp:cluster:hosts lists them)", self::EXIT_USAGE );
        $this->execute( $hosts, $selected, array( $step ), $options, 'exp:host:run ' . $name . ' -- ' . \expClusterRemote::quoteAll( $words ) );
    }

    /**
     * The step for the words after --: a console command, or with $shell a command line for sh.
     *
     * @param string[] $words
     * @param bool $shell
     * @param string $as user, deploy or login
     * @param string|null $error gets why there is none
     * @return array|null the step
     */
    public static function remoteStep( array $words, $shell, $as, &$error = null )
    {
        $error = null;
        $words = array_values( array_map( 'strval', $words ) );
        if ( !$words || trim( implode( '', $words ) ) === '' )
        {
            $error = 'Give the command after --, e.g. -- exp:cache status';
            return null;
        }
        if ( !in_array( $as, array( 'user', 'deploy', 'login' ), true ) )
        {
            $error = "--as must be user, deploy or login, not $as";
            return null;
        }
        if ( $shell )
            return array( 'label' => implode( ' ', $words ), 'shell' => implode( ' ', $words ), 'as' => $as );
        if ( $words[0] === '' || $words[0][0] === '-' )
        {
            $error = 'The command after -- starts with its name (exp:...), not an option: ' . $words[0];
            return null;
        }
        return array( 'label' => implode( ' ', $words ), 'console' => $words, 'as' => $as );
    }
}

}
?>
