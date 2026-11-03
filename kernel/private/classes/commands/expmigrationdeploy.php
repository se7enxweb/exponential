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
                             "./console exp:migration:deploy --remotes=beta --only=20261102090000_add_news_class --apply --format=json\n" .
                             "\n" .
                             "Staged: --staged puts the remotes in stages by their environment ([RemoteSettings] StageOrder[]); the\n" .
                             "first stage is switched, then the deploy waits at a gate until --continue=<deploy id> switches the\n" .
                             "next one. --rollback=<deploy id> takes back every remote that applied a deploy.\n" .
                             "\n" .
                             "./console exp:migration:deploy --remotes=all --staged --apply\n" .
                             "./console exp:migration:deploy --continue=20261102-120000-a1b2c3 --apply\n" .
                             "./console exp:migration:deploy --rollback=20261102-120000-a1b2c3 --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[remotes:*][apply][dry-run][transfer-only][only:*][format:][parallel:][timeout:][retries:][staged][continue:][rollback:][login:][run-id:][progress:]', '', array(
            'remotes' => 'The remotes: ids or group names of [RemoteSettings] RemoteList[] in migration.ini, or all (a comma list or repeated)',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'transfer-only' => 'Send and verify the migration files, but do not run them (with --apply; the only mode for an FTP remote without a trigger)',
            'only' => 'Only these migrations (migration ids, a comma list or repeated)',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'parallel' => 'How many remotes are worked on at the same time (default [RemoteSettings] Parallel)',
            'timeout' => 'Seconds each remote may take for one phase (default [RemoteSettings] Timeout)',
            'retries' => 'How often a connection failure is tried again (default [RemoteSettings] Retries)',
            'staged' => 'Deploy stage by stage ([RemoteSettings] StageOrder[] by environment): the first stage now, a gate before each next one (with --apply)',
            'continue' => 'Approve the gate of a staged deploy and deploy its next stage (the deploy id; with --apply)',
            'rollback' => 'Take back every remote that applied this deploy (the deploy id; with --apply)',
            'login' => 'Run as this user (default: [RunSettings] RunAsUser of migration.ini)',
            'run-id' => 'The id of the run to start (default a new one: <date>-<time>-<6 hex digits>); the administration names it before it starts the run',
            'progress' => 'Append the progress of the run to this file, one JSON object per line (what the administration shows while the run goes on)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $continue = $this->deployIdOption( $options, 'continue' );
        $rollback = $this->deployIdOption( $options, 'rollback' );
        if ( $continue !== '' && $rollback !== '' )
            $this->usage( '--continue and --rollback cannot be combined' );
        if ( ( $continue !== '' || $rollback !== '' ) && !empty( $options['remotes'] ) )
            $this->usage( '--continue and --rollback take the remotes of the deploy; leave --remotes out' );
        if ( ( $continue !== '' || $rollback !== '' ) && !$apply )
            $this->usage( ( $continue !== '' ? '--continue' : '--rollback' ) . ' changes remotes: add --apply' );
        if ( !empty( $options['transfer-only'] ) && !$apply )
            $this->usage( '--transfer-only sends files: add --apply (without it the dry run shows the plan)' );
        if ( !empty( $options['staged'] ) && ( !$apply || !empty( $options['transfer-only'] ) ) )
            $this->usage( '--staged switches stage by stage: add --apply (and leave --transfer-only out)' );
        $progress = $this->progress( $options );
        $settings = \expMigrationRemote::settings();
        $only = array();
        foreach ( isset( $options['only'] ) && $options['only'] ? (array)$options['only'] : array() as $value )
            foreach ( explode( ',', (string)$value ) as $id )
                if ( ( $id = trim( $id ) ) !== '' )
                    $only[] = $id;
        $user = \eZUser::currentUser();
        $login = $user && $user->isRegistered() ? (string)$user->attribute( 'login' ) : '';
        if ( isset( $options['login'] ) && $options['login'] !== false && $options['login'] !== '' )
        {
            if ( !\expMigrationContext::resolveUser( (string)$options['login'] ) )
                $this->usage( '--login names no user: ' . $options['login'] );
            $login = (string)$options['login'];
        }
        $stored = null;
        if ( $continue !== '' || $rollback !== '' )
        {
            $stored = \expMigrationRemoteDeploy::load( $continue !== '' ? $continue : $rollback );
            if ( $stored === null )
                $this->usage( 'no stored report of the deploy ' . ( $continue !== '' ? $continue : $rollback ) . ' (var/<site>/migration/deploy/reports/)' );
            $remotes = \expMigrationRemote::all();
        }
        else
            $remotes = $this->remotes( $options, true );
        $db = null;
        try
        {
            $db = \eZDB::instance();
            if ( !$db || !$db->isConnected() )
                $db = null;
        }
        catch ( \Throwable $e )
        {
            $db = null;
        }
        $notifier = new \expMigrationDeployNotifier();
        $deploy = new \expMigrationRemoteDeploy( $remotes, array(
            'apply' => $apply,
            'transfer_only' => !empty( $options['transfer-only'] ),
            'staged' => !empty( $options['staged'] ),
            'stage_order' => $settings['stage_order'],
            'only' => $stored !== null ? array() : $only,
            'parallel' => $this->number( $options, 'parallel', $settings['parallel'], 1 ),
            'timeout' => $this->number( $options, 'timeout', $settings['timeout'], 10 ),
            'retries' => $this->number( $options, 'retries', $settings['retries'], 0 ),
            'root' => \eZSys::rootDir(),
            'version' => class_exists( 'ExponentialSDK' ) ? (string)\ExponentialSDK::version() : '',
            'store' => $settings['store'],
            'heal' => $settings['heal'],
            'stale_lock_age' => $settings['stale_lock_age'],
            'disk_margin' => $settings['disk_margin'],
            'php_check' => $settings['php_check'],
            'extension_check' => $settings['extension_check'],
            'user' => $login,
            'deploy_id' => $stored !== null ? (string)$stored['deploy_id'] : ( isset( $progress['run-id'] ) ? $progress['run-id'] : null ),
            'progress' => isset( $progress['progress'] ) ? $progress['progress'] : null,
            'notify' => function ( array $report ) use ( $notifier ) { $notifier->notify( $report ); },
            'db' => $db,
            'say' => $format === 'text' ? function ( $line ) { $this->output( $line ); } : null,
        ) );
        if ( $continue !== '' )
            $report = $deploy->continueStaged( $stored );
        else if ( $rollback !== '' )
            $report = $deploy->rollbackDeploy( $stored );
        else
            $report = $deploy->run();
        $this->finish( array( 'mode' => 'deploy', 'deploy' => $report, 'errors' => $report['errors'], 'warnings' => $report['warnings'],
                              'exit' => $report['exit'] ), $format );
    }

    /** A deploy id option (--continue, --rollback), checked; '' when not given. */
    protected function deployIdOption( array $options, $name )
    {
        if ( !isset( $options[$name] ) || $options[$name] === false || $options[$name] === '' || $options[$name] === true )
            return '';
        $id = trim( (string)$options[$name] );
        if ( !\expMigrationRecord::validRunId( $id ) )
            $this->usage( "--$name must be a deploy id (<date>-<time>-<6 hex digits>), not $id" );
        return $id;
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
