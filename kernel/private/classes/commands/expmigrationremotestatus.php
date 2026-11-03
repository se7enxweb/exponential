<?php
/**
 * @description The migration state of remotes, read in parallel (reachable, version, lock, done, pending, failed), whether they are in sync, and the latest deploys
 * @alias exp:migration:remote:status
 *
 * File containing the exp:migration:remote:status command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationremotestatus extends Expmigrationdeploy
{
    protected function name()
    {
        return 'exp:migration:remote:status';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The migration state of remotes\n" .
                             "Reads exp:migration:status of every selected remote in parallel and lists, per remote, whether it\n" .
                             "was reached, its kernel version, the run lock, how many migrations are done, pending and failed, and\n" .
                             "its latest run; then whether the remotes are in sync (every migration done on all of them or on\n" .
                             "none) and the latest deploys of this installation. Changes nothing.\n" .
                             "\n" .
                             "./console exp:migration:remote:status\n" .
                             "./console exp:migration:remote:status --remotes=production --format=json\n" .
                             "./console exp:migration:remote:status --history=0",
        ) + self::SCRIPT );
        return (array)$this->startup( '[remotes:*][format:][parallel:][timeout:][history:]', '', array(
            'remotes' => 'The remotes: ids or group names of [RemoteSettings] RemoteList[] in migration.ini, or all (a comma list or repeated)',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'parallel' => 'How many remotes are worked on at the same time (default [RemoteSettings] Parallel)',
            'timeout' => 'Seconds each remote may take for one phase (default [RemoteSettings] Timeout)',
            'history' => 'How many of the latest deploys to list (default 10, 0 for none)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $settings = \expMigrationRemote::settings();
        $deploy = new \expMigrationRemoteDeploy( $this->remotes( $options, false ), array(
            'parallel' => $this->number( $options, 'parallel', $settings['parallel'], 1 ),
            'timeout' => $this->number( $options, 'timeout', $settings['timeout'], 10 ),
            'retries' => $settings['retries'],
            'php_check' => $settings['php_check'],
            'extension_check' => $settings['extension_check'],
            'root' => \eZSys::rootDir(),
            'version' => class_exists( 'ExponentialSDK' ) ? (string)\ExponentialSDK::version() : '',
        ) );
        $report = $deploy->status();
        $report['history'] = \expMigrationRemoteDeploy::history( $this->number( $options, 'history', 10, 0 ) );
        $this->finish( array( 'mode' => 'remote-status', 'deploy' => $report, 'errors' => $report['errors'], 'warnings' => $report['warnings'],
                              'exit' => $report['exit'] ), $format );
    }
}

}
