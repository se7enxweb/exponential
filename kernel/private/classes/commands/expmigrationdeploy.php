<?php
/**
 * @description Deploy this installation's migrations to one or many remotes at once, in sync: preflight and verify on all, then switch all, or take all back
 * @alias exp:migration:deploy
 *
 * File containing the exp:migration:deploy command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationdeploy extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:deploy';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Deploy migrations to remotes, all at once and in sync\n" .
                             "Sends this installation's migrations (the site's and the active extensions') to the selected remotes of\n" .
                             "migration.ini ([RemoteSettings] RemoteList[], one [Remote_<id>] each) and runs them there. A preflight\n" .
                             "on every remote in parallel (reachable, run lock free, nothing failed, the same kernel version, no file\n" .
                             "changed after it was applied) must pass before anything changes; the files go to every remote and are\n" .
                             "verified there; then every remote applies with one shared run id. When one remote fails, every remote\n" .
                             "that applied is taken back to its caption, so all of them stay in the same state. Without --apply it is\n" .
                             "a dry run: the preflight and the plan, nothing is sent or changed.\n" .
                             "\n" .
                             "./console exp:migration:deploy --remotes=stage,production\n" .
                             "./console exp:migration:deploy --remotes=production --apply\n" .
                             "./console exp:migration:deploy --remotes=all --transfer-only --apply\n" .
                             "./console exp:migration:deploy --remotes=beta --only=20261102090000_add_news_class --apply --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[remotes:*][apply][dry-run][transfer-only][only:*][format:][parallel:][timeout:][retries:]', '', array(
            'remotes' => 'The remotes: ids or group names of [RemoteSettings] RemoteList[] in migration.ini, or all (a comma list or repeated)',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'transfer-only' => 'Send and verify the migration files, but do not run them (with --apply; the only mode for FTP remotes)',
            'only' => 'Only these migrations (migration ids, a comma list or repeated)',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'parallel' => 'How many remotes are worked on at the same time (default [RemoteSettings] Parallel)',
            'timeout' => 'Seconds each remote may take for one phase (default [RemoteSettings] Timeout)',
            'retries' => 'How often a connection failure is tried again (default [RemoteSettings] Retries)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        if ( !empty( $options['transfer-only'] ) && !$apply )
            $this->usage( '--transfer-only sends files: add --apply (without it the dry run shows the plan)' );
        $settings = \expMigrationRemote::settings();
        $remotes = $this->remotes( $options, true );
        $only = array();
        foreach ( isset( $options['only'] ) && $options['only'] ? (array)$options['only'] : array() as $value )
            foreach ( explode( ',', (string)$value ) as $id )
                if ( ( $id = trim( $id ) ) !== '' )
                    $only[] = $id;
        $user = \eZUser::currentUser();
        $deploy = new \expMigrationRemoteDeploy( $remotes, array(
            'apply' => $apply,
            'transfer_only' => !empty( $options['transfer-only'] ),
            'only' => $only,
            'parallel' => $this->number( $options, 'parallel', $settings['parallel'], 1 ),
            'timeout' => $this->number( $options, 'timeout', $settings['timeout'], 10 ),
            'retries' => $this->number( $options, 'retries', $settings['retries'], 0 ),
            'root' => \eZSys::rootDir(),
            'version' => class_exists( 'ExponentialSDK' ) ? (string)\ExponentialSDK::version() : '',
            'store' => $settings['store'],
            'user' => $user ? (string)$user->attribute( 'login' ) : '',
            'say' => $format === 'text' ? function ( $line ) { $this->output( $line ); } : null,
        ) );
        $report = $deploy->run();
        $this->finish( array( 'mode' => 'deploy', 'deploy' => $report, 'errors' => $report['errors'], 'warnings' => $report['warnings'],
                              'exit' => $report['exit'] ), $format );
    }

    /**
     * The selected remotes; a usage error when none or an unknown name is given.
     *
     * @param array $options
     * @param bool $required
     * @return \expMigrationRemote[]
     */
    protected function remotes( array $options, $required )
    {
        $all = \expMigrationRemote::all();
        $names = isset( $options['remotes'] ) && $options['remotes'] ? (array)$options['remotes'] : array();
        if ( !$names )
        {
            if ( $required )
                $this->usage( 'name the remotes: --remotes=<id or group>[,...] or --remotes=all (./console exp:migration:remote:list lists them)' );
            return $all;
        }
        $errors = array();
        $selected = \expMigrationRemote::select( $names, $all, $errors );
        if ( $errors )
            $this->usage( implode( '; ', $errors ) );
        return $selected;
    }

    /** A whole-number option, or its default. */
    protected function number( array $options, $name, $default, $min )
    {
        if ( !isset( $options[$name] ) || $options[$name] === false || $options[$name] === '' )
            return $default;
        if ( !ctype_digit( (string)$options[$name] ) || (int)$options[$name] < $min )
            $this->usage( "--$name must be a number of at least $min" );
        return (int)$options[$name];
    }

    protected function lines( array $report )
    {
        return isset( $report['deploy'] ) ? self::deployLines( $report['deploy'] ) : array();
    }

    /**
     * The lines of a deploy or remote status report. Pure.
     *
     * @param array $deploy exp-migration-deploy/1
     * @return string[]
     */
    public static function deployLines( array $deploy )
    {
        $titles = array( 'dry-run' => 'deploy, dry run (--apply sends and runs)', 'apply' => 'deploy', 'transfer-only' => 'deploy, transfer only',
                         'status' => 'remote status' );
        $lines = array( 'Exponential migrations: ' . ( isset( $titles[$deploy['mode']] ) ? $titles[$deploy['mode']] : $deploy['mode'] ) . '  ' . $deploy['deploy_id'] );
        foreach ( $deploy['remotes'] as $id => $remote )
        {
            $lines[] = sprintf( '  %-14s %-12s %-12s %s', $id, $remote['remote']['environment'], $remote['state'], $remote['remote']['address'] );
            if ( $remote['version'] !== '' )
                $lines[] = '      version ' . $remote['version'] . ', done ' . $remote['migrations']['done'] . ', pending ' . $remote['migrations']['pending']
                         . ', failed ' . $remote['migrations']['failed'] . ( $remote['lock'] ? ', LOCKED' : '' );
            if ( $remote['send'] )
                $lines[] = '      send ' . count( $remote['send'] ) . ' file(s): ' . implode( ', ', array_slice( $remote['send'], 0, 5 ) ) . ( count( $remote['send'] ) > 5 ? ' ...' : '' );
            if ( $remote['pending'] && $deploy['mode'] !== 'status' )
                $lines[] = '      to apply: ' . implode( ', ', $remote['pending'] );
            if ( $remote['applied'] )
                $lines[] = '      applied: ' . implode( ', ', $remote['applied'] );
            foreach ( $remote['fixes'] as $fix )
                $lines[] = '      fixed: ' . $fix;
        }
        if ( $deploy['mode'] !== 'status' )
            $lines[] = 'State: ' . $deploy['state'] . ( $deploy['in_sync'] === false ? '  (OUT OF SYNC: every remote\'s state is listed above)' : '' );
        else
            $lines[] = 'In sync: ' . ( $deploy['in_sync'] === null ? 'unknown (a remote could not be read)' : ( $deploy['in_sync'] ? 'yes' : 'no' ) );
        if ( !empty( $deploy['history'] ) )
        {
            $lines[] = 'Latest deploys:';
            foreach ( $deploy['history'] as $h )
            {
                $states = array();
                foreach ( $h['remotes'] as $id => $state )
                    $states[] = "$id=$state";
                $lines[] = sprintf( '  %-24s %-13s %-12s %s', $h['deploy_id'], $h['mode'], $h['state'], implode( ' ', $states ) );
            }
        }
        return $lines;
    }
}

}
