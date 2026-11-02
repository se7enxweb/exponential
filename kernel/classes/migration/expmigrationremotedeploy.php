<?php
/**
 * File containing the expMigrationRemoteDeploy class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One deploy of this installation's migrations to a selection of remotes (expMigrationRemote), all at once and in
 * sync (exp:migration:deploy):
 *
 *   1. prepare     every remote's settings and credential are checked; one problem stops the deploy
 *   2. preflight   in parallel on every remote: first the fixes of [RemoteSettings] SelfHeal[] (exp:migration:heal:
 *                  a stale run lock, missing migration folders, file modes; each reported in the remote's fixes), then
 *                  reachable, its migration status read (exp:migration:status --format=json), the run lock free, no
 *                  failed migration, the same kernel version, the PHP version ([RemoteSettings] PhpCheck) and the
 *                  versions of the extensions ([RemoteSettings] ExtensionCheck) as here, enough free disk space for the
 *                  transfer plus [RemoteSettings] DiskMargin, no migration changed after it was applied there; the files
 *                  to send are planned (a migration whose file the remote has with the same checksum is not sent). Any
 *                  problem on any remote stops the deploy before anything changes. A dry run (the default) ends here.
 *   3. transfer    the planned files to every remote in parallel (ssh/scp, sftp, rsync, ftp/ftps, local); a dropped
 *                  connection is retried ([RemoteSettings] Retries, rsync resumes a broken transfer)
 *   4. verify      in parallel on every remote: a dry run of exp:migration:migrate must see every sent migration with
 *                  the source's checksum, and captions must be on (the rollback needs the caption taken before the
 *                  run); FTP remotes: every file is read back and compared. A failure stops the deploy before any
 *                  remote is switched; --transfer-only ends here.
 *   5. switch      exp:migration:migrate --apply --run-id=<deploy id> on every remote in parallel, one shared id;
 *                  with SelfHeal[]=caches the caches are cleared there afterwards (exp:migration:heal --fix=caches)
 *   6. all or nothing: when one remote fails, every remote that applied is taken back to the caption taken before
 *                  the run (exp:migration:rollback <deploy id> --apply); the failed remote restored itself. A remote
 *                  that cannot be taken back is reported and the deploy is marked out-of-sync with every remote's state.
 *
 * FTP remotes run nothing over FTP. With Trigger (and TriggerSecret) they are run through their signed HTTP trigger
 * (expMigrationTrigger, the view migration/trigger there): status, dry run, run, rollback and fixes like any other
 * remote. Without it they only take files (--transfer-only).
 *
 * Staged (the option staged, exp:migration:deploy --staged, the administration's "Staged"): the remotes are put in
 * stages by their environment ([RemoteSettings] StageOrder[]: dev before stage before beta before production ...).
 * The preflight still runs on every remote first; then the first stage is transferred, verified and switched (all
 * or nothing within the stage) and the deploy stops at a gate (state gated). Someone approves the next stage
 * (continueStaged(), --continue=<deploy id>), which is preflighted again and switched the same way, until the last
 * stage is deployed. A stage that fails is taken back; the stages before it stay deployed (rollbackDeploy() takes
 * them back too).
 *
 * The report (exp-migration-deploy/1) is kept in var/<site>/migration/deploy/reports/<deploy id>.json, and in the
 * tables exp_migration_deploy and exp_migration_deploy_host when the option db names a connection that has them
 * (expMigrationDeployRecord). It is audited (system.migration.deploy; system.migration.deploy.rollback when remotes
 * were taken back), and the option notify is called with it at the end (expMigrationDeployNotifier: a notice by
 * e-mail on a failure or a gate). The option progress is called with the events of the administration's live page
 * (expMigrationProgress: run, phase, host, step-done, end). Only one deploy runs at a time on the source (a lock
 * file). Secrets never appear in a report: every output is redacted.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRemoteDeploy
{
    const REPORT = 'exp-migration-deploy/1';

    /** The console scripts run on a remote */
    const STATUS = 'bin/php/expmigrationstatus.php';
    const MIGRATE = 'bin/php/expmigrationmigrate.php';
    const ROLLBACK = 'bin/php/expmigrationrollback.php';
    const HEAL = 'bin/php/expmigrationheal.php';

    /** The free disk space a remote must have beyond the transfer when [RemoteSettings] DiskMargin is not set: 50 MB */
    const DISK_MARGIN = 52428800;

    /** @var expMigrationRemote[] */
    protected $remotes;

    /** @var array */
    protected $options;

    /** @var expMigrationRemoteCredential[] */
    protected $credentials = array();

    /** @var expMigrationRemoteTransport[] */
    protected $transports = array();

    /** The order of the stages of a staged deploy when [RemoteSettings] StageOrder[] is empty */
    const STAGE_ORDER = array( 'alpha', 'dev', 'test', 'stage', 'beta', 'production', 'post', 'arch' );

    /** The states a deploy ends in that are failures (a notice by e-mail) */
    const FAILED_STATES = array( 'refused', 'failed', 'rolled-back', 'out-of-sync' );

    /** @var array the report being built */
    protected $report;

    /** @var string the stage the phases run for ('' when the deploy is not staged), as the report's phase keys show it */
    protected $stageLabel = '';

    /** @var int how many phases have ended (the progress) */
    protected $phasesDone = 0;

    /** @var bool the deploy is being taken back on request (rollbackDeploy()): rolled-back is its success */
    protected $takingBack = false;

    /**
     * @param expMigrationRemote[] $remotes id => remote (the selection)
     * @param array $options apply (bool), transfer_only (bool), only (migration ids), parallel, timeout, retries, root
     *        (the source's root), work_dir (var/<site>/migration/deploy), local (the source's migrations, as
     *        localDefinitions() returns them), version (the source's kernel version), deploy_id, lookup (credential
     *        lookup, tests), store (bool), audit (callable( name, data ), default expUpgrade::audit), say (callable(
     *        line ) for progress), user (who deploys, for the audit), staged (bool), stage_order (environments),
     *        progress (callable( event, data ), expMigrationProgress::writer()), notify (callable( report ) at the
     *        end), db (a connection for expMigrationDeployRecord, null for none), heal (the fixes of SelfHeal[]),
     *        stale_lock_age (seconds), disk_margin (bytes), php_check (major, minor, warn, off), extension_check
     *        (migrations, all, warn, off), host (this installation's hostInfo(), default read from root)
     */
    public function __construct( array $remotes, array $options = array() )
    {
        $this->remotes = $remotes;
        $this->options = $options + array( 'apply' => false, 'transfer_only' => false, 'only' => array(), 'parallel' => 4, 'timeout' => 900,
                                           'retries' => 2, 'root' => getcwd(), 'work_dir' => null, 'local' => null, 'version' => '',
                                           'deploy_id' => null, 'lookup' => null, 'store' => true, 'audit' => null, 'say' => null, 'user' => '',
                                           'staged' => false, 'stage_order' => self::STAGE_ORDER, 'progress' => null, 'notify' => null, 'db' => null,
                                           'heal' => array(), 'stale_lock_age' => expMigrationHeal::STALE_LOCK_AGE, 'disk_margin' => self::DISK_MARGIN,
                                           'php_check' => 'major', 'extension_check' => 'migrations', 'host' => null );
        $this->options['root'] = rtrim( str_replace( '\\', '/', (string)$this->options['root'] ), '/' );
        if ( $this->options['work_dir'] === null )
            $this->options['work_dir'] = self::workDirectory();
        if ( $this->options['deploy_id'] === null )
            $this->options['deploy_id'] = expMigrationContext::newRunId();
    }

    /** @return string var/<site>/migration/deploy */
    public static function workDirectory()
    {
        return rtrim( (string)eZSys::varDirectory(), '/' ) . '/migration/deploy';
    }

    /**
     * The source's migrations that a deploy sends: the site's and the active extensions' (package and kernel
     * migrations reach a remote with their package or the kernel), each file relative to the root.
     *
     * @param string $root
     * @param expMigrationLocator|null $locator
     * @param string[] $warnings
     * @return array[] id, source, files (engine => relative path), paths (relative => local file), checksums (engine => sha256)
     */
    public static function localDefinitions( $root, $locator = null, array &$warnings = array() )
    {
        $root = rtrim( (string)$root, '/' );
        $locator = $locator ?: expMigrationLocator::fromSettings();
        $definitions = array();
        foreach ( $locator->find() as $definition )
        {
            $source = (string)$definition->source();
            if ( $source !== 'site' && strpos( $source, 'extension:' ) !== 0 )
                continue;
            $files = array();
            $paths = array();
            foreach ( $definition->files() as $engine => $file )
            {
                $file = str_replace( '\\', '/', (string)$file );
                $rel = strpos( $file, $root . '/' ) === 0 ? substr( $file, strlen( $root ) + 1 ) : '';
                if ( !expMigrationRemoteTransport::safePath( $rel ) )
                {
                    $warnings[] = $definition->id() . ": $file is outside the installation or has characters a deploy does not send; it is left out";
                    continue 2;
                }
                $files[$engine] = $rel;
                $paths[$rel] = $file;
            }
            $definitions[] = array( 'id' => (string)$definition->id(), 'source' => $source, 'files' => $files, 'paths' => $paths,
                                    'checksums' => $definition->checksums() );
        }
        foreach ( $locator->errors() as $error )
            $warnings[] = $error;
        return $definitions;
    }

    /** @return string */
    public function deployId()
    {
        return (string)$this->options['deploy_id'];
    }

    /** A progress line. */
    protected function say( $line )
    {
        if ( is_callable( $this->options['say'] ) )
            call_user_func( $this->options['say'], $line );
    }

    /** An event of the live progress (expMigrationProgress). */
    protected function emit( $event, array $data = array() )
    {
        if ( is_callable( $this->options['progress'] ) )
            call_user_func( $this->options['progress'], $event, $data );
    }

    /** The live progress of every remote of the selection: its state, the phase it is in, how many problems it has. */
    protected function emitHosts( $phase )
    {
        foreach ( array_keys( $this->remotes ) as $id )
            $this->emit( 'host', array( 'remote' => (string)$id, 'state' => $this->report['remotes'][$id]['state'], 'phase' => (string)$phase,
                                        'problems' => count( $this->report['remotes'][$id]['problems'] ) ) );
    }

    /**
     * The stages of a staged deploy: the remotes grouped by environment, in the order of $order (an environment that
     * is not in it comes after, in the order of the selection). Pure.
     *
     * @param expMigrationRemote[] $remotes id => remote
     * @param string[] $order environments
     * @return array[] each a list of remote ids
     */
    public static function stagesOf( array $remotes, array $order )
    {
        $rank = array();
        foreach ( array_values( $order ) as $i => $environment )
            if ( ( $environment = strtolower( trim( (string)$environment ) ) ) !== '' && !isset( $rank[$environment] ) )
                $rank[$environment] = $i;
        $groups = array();
        $first = array();
        foreach ( $remotes as $id => $remote )
        {
            $environment = strtolower( $remote->environment() );
            if ( !isset( $groups[$environment] ) )
                $first[$environment] = count( $first );
            $groups[$environment][] = (string)$id;
        }
        $keys = array_keys( $groups );
        usort( $keys, function ( $a, $b ) use ( $rank, $first ) {
            $ra = isset( $rank[$a] ) ? $rank[$a] : PHP_INT_MAX;
            $rb = isset( $rank[$b] ) ? $rank[$b] : PHP_INT_MAX;
            return $ra <=> $rb ?: $first[$a] <=> $first[$b];
        } );
        $stages = array();
        foreach ( $keys as $key )
            $stages[] = $groups[$key];
        return $stages;
    }

    /**
     * A stored report.
     *
     * @param string $deployId
     * @param string|null $workDir
     * @return array|null
     */
    public static function load( $deployId, $workDir = null )
    {
        if ( !expMigrationRecord::validRunId( (string)$deployId ) )
            return null;
        $file = ( $workDir !== null ? rtrim( (string)$workDir, '/' ) : self::workDirectory() ) . '/reports/' . $deployId . '.json';
        $data = is_file( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : null;
        return is_array( $data ) && isset( $data['schema'], $data['deploy_id'] ) && $data['schema'] === self::REPORT && $data['deploy_id'] === $deployId ? $data : null;
    }

    /**
     * What a stored deploy allows now. Pure.
     *
     * @param array $report
     * @return array continue (bool: a gate waits for the next stage), rollback (bool: remotes applied it), next_stage
     *               (index, null), next (the remote ids of the next stage), applied (the remote ids that applied it)
     */
    public static function actions( array $report )
    {
        $applied = array();
        foreach ( isset( $report['remotes'] ) ? (array)$report['remotes'] : array() as $id => $remote )
            if ( isset( $remote['state'] ) && $remote['state'] === 'applied' && !empty( $remote['applied'] ) )
                $applied[] = (string)$id;
        $next = isset( $report['next_stage'] ) && $report['next_stage'] !== null ? (int)$report['next_stage'] : null;
        $gated = isset( $report['state'] ) && $report['state'] === 'gated' && $next !== null && isset( $report['stages'][$next] );
        return array( 'continue' => $gated, 'rollback' => (bool)$applied, 'next_stage' => $gated ? $next : null,
                      'next' => $gated ? array_values( (array)$report['stages'][$next] ) : array(), 'applied' => $applied );
    }

    /** @return array an empty report */
    protected function emptyReport( $mode )
    {
        $remotes = array();
        foreach ( $this->remotes as $id => $remote )
            $remotes[$id] = array( 'remote' => $remote->toArray(), 'state' => 'selected', 'problems' => array(), 'version' => '', 'pending' => array(),
                                   'send' => array(), 'applied' => array(), 'caption' => '', 'restore' => '', 'attempts' => array(), 'fixes' => array(),
                                   'migrations' => array( 'done' => 0, 'pending' => 0, 'failed' => 0 ), 'lock' => null, 'last_run' => null,
                                   'host' => null, 'send_bytes' => 0 );
        return array( 'schema' => self::REPORT, 'deploy_id' => $this->deployId(), 'mode' => $mode, 'state' => '', 'in_sync' => null,
                      'started_at' => date( 'c' ), 'finished_at' => null, 'user' => (string)$this->options['user'], 'version' => (string)$this->options['version'],
                      'remotes' => $remotes, 'phases' => array(), 'errors' => array(), 'warnings' => array(), 'exit' => expUpgrade::EXIT_OK,
                      'stages' => array(), 'next_stage' => null, 'gates' => array() );
    }

    /**
     * The deploy: a dry run unless the option apply (or transfer_only).
     *
     * @return array the report (exp-migration-deploy/1)
     */
    public function run()
    {
        $mode = $this->options['transfer_only'] ? 'transfer-only' : ( $this->options['apply'] ? 'apply' : 'dry-run' );
        $this->report = $this->emptyReport( $mode );
        $stages = $this->options['staged'] && $mode === 'apply' ? self::stagesOf( $this->remotes, (array)$this->options['stage_order'] ) : array();
        if ( count( $stages ) > 1 )
            $this->report['stages'] = $stages;
        $this->emit( 'run', array( 'mode' => 'deploy', 'total' => $mode === 'dry-run' ? 2 : ( $mode === 'transfer-only' ? 4 : 5 ),
                                   'remotes' => array_map( 'strval', array_keys( $this->remotes ) ), 'stages' => count( $this->report['stages'] ) ) );
        if ( !$this->remotes )
            return $this->end( 'refused', 'no remote selected' );
        $lock = $this->lock();
        if ( $lock === null )
            return $this->end( 'refused', 'another deploy runs on this installation (' . $this->options['work_dir'] . '/deploy.lock)' );
        try
        {
            if ( !$this->phase( 'prepare', function () { return $this->prepare( !$this->options['transfer_only'] ); } ) )
                return $this->end( 'refused', 'a remote cannot be used; nothing was changed' );
            $local = $this->local();
            if ( !$this->phase( 'preflight', function () use ( $local ) { return $this->preflight( $local ); } ) )
                return $this->end( 'refused', 'the preflight failed on a remote; nothing was changed' );
            if ( $mode === 'dry-run' )
                return $this->end( 'planned' );
            if ( !$this->report['stages'] )
                return $this->stage( null, $local );
            // staged: the first stage now, the others wait at the gate
            foreach ( $this->report['stages'] as $index => $ids )
                if ( $index > 0 )
                    foreach ( $ids as $id )
                        $this->report['remotes'][$id]['state'] = 'waiting';
            $this->emitHosts( '' );
            return $this->stage( 0, $local );
        }
        finally
        {
            $this->cleanup( $lock );
        }
    }

    /** Removes the key files and releases the lock. */
    protected function cleanup( $lock )
    {
        foreach ( $this->credentials as $credential )
            $credential->cleanup();
        $this->credentials = array();
        if ( $lock )
        {
            flock( $lock, LOCK_UN );
            fclose( $lock );
        }
    }

    /**
     * Transfer, verify and switch the remotes of a stage (all of the selection when $index is null), all or nothing
     * within them.
     *
     * @param int|null $index the stage
     * @param array[] $local
     * @return array the report
     */
    protected function stage( $index, array $local )
    {
        $all = $this->remotes;
        if ( $index !== null )
        {
            $this->remotes = array_intersect_key( $all, array_flip( $this->report['stages'][$index] ) );
            $this->stageLabel = 'stage' . ( $index + 1 );
            $this->report['next_stage'] = null;
            $this->say( '-- stage ' . ( $index + 1 ) . ' of ' . count( $this->report['stages'] ) . ': ' . implode( ', ', array_keys( $this->remotes ) ) );
        }
        try
        {
            $mode = $this->report['mode'];
            if ( !$this->phase( 'transfer', function () use ( $local ) { return $this->transfer( $local ); } ) )
                return $this->end( 'failed', 'the transfer failed; no remote was switched (the files sent stay pending there)' );
            if ( !$this->phase( 'verify', function () use ( $local ) { return $this->verify( $local ); } ) )
                return $this->end( 'failed', 'the verification failed; no remote was switched (the files sent stay pending there)' );
            if ( $mode === 'transfer-only' )
                return $this->end( 'transferred' );
            if ( $this->phase( 'switch', function () { return $this->switchAll(); } ) )
            {
                if ( $index !== null && isset( $this->report['stages'][$index + 1] ) )
                {
                    $this->report['next_stage'] = $index + 1;
                    return $this->end( 'gated' );
                }
                return $this->end( 'deployed' );
            }
            $back = $this->phase( 'rollback', function () { return $this->rollbackAll(); } );
            return $this->end( $back ? 'rolled-back' : 'out-of-sync', $back ? 'a remote failed; every remote is back where it was before the deploy'
                                                                             : 'a remote failed and not every remote could be taken back: the remotes are OUT OF SYNC' );
        }
        finally
        {
            $this->remotes = $all;
            $this->stageLabel = '';
        }
    }

    /**
     * The next stage of a staged deploy that waits at its gate: its remotes are prepared and preflighted again (time
     * has passed), then transferred, verified and switched like the first stage. The remotes the deploy was made
     * with are given to the constructor (the stage's are taken from them).
     *
     * @param array $report the stored report (load()), state gated
     * @return array the report; not stored when the gate cannot be passed (errors say why)
     */
    public function continueStaged( array $report )
    {
        $actions = self::actions( $report );
        $refuse = function ( $why ) use ( $report ) {
            $report['errors'] = array( $why );
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        };
        if ( !$actions['continue'] )
            return $refuse( 'the deploy ' . ( isset( $report['deploy_id'] ) ? $report['deploy_id'] : '' ) . ' does not wait at a gate' );
        if ( (string)$report['deploy_id'] !== $this->deployId() )
            return $refuse( 'the deploy id is not the report\'s' );
        $missing = array_diff( $actions['next'], array_map( 'strval', array_keys( $this->remotes ) ) );
        if ( $missing )
            return $refuse( 'the remotes ' . implode( ', ', $missing ) . ' of the next stage are no longer in [RemoteSettings] RemoteList[]' );
        $index = $actions['next_stage'];
        $this->remotes = array_intersect_key( $this->remotes, array_flip( $actions['next'] ) );
        $this->report = $report;
        $this->report['errors'] = array();
        $this->report['warnings'] = array();
        $this->report['finished_at'] = null;
        $this->report['gates'][] = array( 'stage' => $index + 1, 'by' => (string)$this->options['user'], 'at' => date( 'c' ) );
        foreach ( $this->remotes as $id => $remote )
        {
            $this->report['remotes'][$id] = array( 'remote' => $remote->toArray(), 'state' => 'selected', 'problems' => array(), 'version' => '', 'pending' => array(),
                                                   'send' => array(), 'applied' => array(), 'caption' => '', 'restore' => '', 'attempts' => array(), 'fixes' => array(),
                                                   'migrations' => array( 'done' => 0, 'pending' => 0, 'failed' => 0 ), 'lock' => null, 'last_run' => null,
                                   'host' => null, 'send_bytes' => 0 );
        }
        $this->emit( 'run', array( 'mode' => 'deploy', 'total' => 5, 'remotes' => array_map( 'strval', array_keys( $this->remotes ) ), 'stage' => $index + 1 ) );
        $lock = $this->lock();
        if ( $lock === null )
            return $refuse( 'another deploy runs on this installation (' . $this->options['work_dir'] . '/deploy.lock)' );
        try
        {
            $this->stageLabel = 'stage' . ( $index + 1 );
            if ( !$this->phase( 'prepare', function () { return $this->prepare( true ); } )
                 || !$this->phase( 'preflight', function () { return $this->preflight( $this->local() ); } ) )
            {
                // nothing changed: the gate still waits
                $this->report['next_stage'] = $index;
                foreach ( $this->remotes as $id => $remote )
                    if ( in_array( $this->report['remotes'][$id]['state'], array( 'prepared', 'ready', 'selected' ), true ) )
                        $this->report['remotes'][$id]['state'] = 'waiting';
                return $this->end( 'gated', 'the preflight of stage ' . ( $index + 1 ) . ' failed; nothing was changed, the gate still waits' );
            }
            $this->stageLabel = '';
            return $this->stage( $index, $this->local() );
        }
        finally
        {
            $this->stageLabel = '';
            $this->cleanup( $lock );
        }
    }

    /**
     * Takes a stored deploy back: every remote that applied it (state applied) runs exp:migration:rollback <deploy id>
     * --apply and so returns to the caption taken before the run. The remotes the deploy was made with are given to
     * the constructor.
     *
     * @param array $report the stored report (load())
     * @return array the report (state rolled-back or out-of-sync); not stored when nothing could be taken back
     */
    public function rollbackDeploy( array $report )
    {
        $actions = self::actions( $report );
        $refuse = function ( $why ) use ( $report ) {
            $report['errors'] = array( $why );
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        };
        if ( !$actions['rollback'] )
            return $refuse( 'no remote applied the deploy ' . ( isset( $report['deploy_id'] ) ? $report['deploy_id'] : '' ) . ': there is nothing to take back' );
        if ( (string)$report['deploy_id'] !== $this->deployId() )
            return $refuse( 'the deploy id is not the report\'s' );
        $missing = array_diff( $actions['applied'], array_map( 'strval', array_keys( $this->remotes ) ) );
        if ( $missing )
            return $refuse( 'the remotes ' . implode( ', ', $missing ) . ' are no longer in [RemoteSettings] RemoteList[]; take them back on the remote (exp:migration:rollback ' . $report['deploy_id'] . ' --apply)' );
        $this->remotes = array_intersect_key( $this->remotes, array_flip( $actions['applied'] ) );
        $this->report = $report;
        $this->report['errors'] = array();
        $this->report['warnings'] = array();
        $this->report['next_stage'] = null;
        $this->report['rollback'] = array( 'by' => (string)$this->options['user'], 'at' => date( 'c' ) );
        $this->takingBack = true;
        $this->emit( 'run', array( 'mode' => 'deploy-rollback', 'total' => 2, 'remotes' => $actions['applied'] ) );
        $lock = $this->lock();
        if ( $lock === null )
            return $refuse( 'another deploy runs on this installation (' . $this->options['work_dir'] . '/deploy.lock)' );
        try
        {
            foreach ( $this->remotes as $id => $remote )
            {
                $this->report['remotes'][$id]['state'] = 'selected';
                $this->report['remotes'][$id]['problems'] = array();
            }
            $prepared = $this->phase( 'prepare-rollback', function () { return $this->prepare( true ); } );
            foreach ( $this->remotes as $id => $remote )
                $this->report['remotes'][$id]['state'] = 'applied';
            if ( !$prepared )
            {
                $this->report['errors'] = array( 'a remote cannot be used; nothing was taken back' );
                foreach ( $this->remotes as $id => $remote )
                    foreach ( $this->report['remotes'][$id]['problems'] as $problem )
                        $this->report['errors'][] = "$id: $problem";
                $this->report['exit'] = expUpgrade::EXIT_FAILED;
                return $this->report;
            }
            $back = $this->phase( 'rollback', function () { return $this->rollbackAll(); } );
            return $this->end( $back ? 'rolled-back' : 'out-of-sync', $back ? '' : 'not every remote could be taken back: the remotes are OUT OF SYNC' );
        }
        finally
        {
            $this->cleanup( $lock );
        }
    }

    /**
     * The status of the selected remotes, read-only: phases prepare and preflight, then whether they are in sync.
     *
     * @return array the report (mode status)
     */
    public function status()
    {
        $this->report = $this->emptyReport( 'status' );
        if ( !$this->remotes )
            return $this->end( 'refused', 'no remote selected' );
        try
        {
            $this->phase( 'prepare', function () { return $this->prepare( false ); } );
            $this->phase( 'preflight', function () { return $this->preflight( null ); } );
            $this->report['in_sync'] = $this->inSync();
            return $this->end( 'status' );
        }
        finally
        {
            foreach ( $this->credentials as $credential )
                $credential->cleanup();
        }
    }

    /** @return resource|null the source's deploy lock, null when another deploy holds it */
    protected function lock()
    {
        $dir = $this->options['work_dir'];
        if ( !is_dir( $dir ) )
            @mkdir( $dir, eZDir::dirMode( 0775 ), true );
        $handle = @fopen( $dir . '/deploy.lock', 'c' );
        if ( !$handle )
            return null;
        if ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            fclose( $handle );
            return null;
        }
        return $handle;
    }

    /** Runs a phase, records its result and time. */
    protected function phase( $name, callable $work )
    {
        $this->say( "-- $name" );
        $this->emit( 'phase', array( 'name' => $name, 'stage' => $this->stageLabel ) );
        $this->emitHosts( $name );
        $started = microtime( true );
        $ok = (bool)$work();
        $key = $this->stageLabel !== '' ? $this->stageLabel . '-' . $name : $name;
        $this->report['phases'][$key] = array( 'ok' => $ok, 'duration_ms' => (int)round( ( microtime( true ) - $started ) * 1000 ) );
        $this->emitHosts( '' );
        $this->emit( 'step-done', array( 'index' => ++$this->phasesDone, 'id' => $key, 'action' => $ok ? 'done' : 'failed' ) );
        return $ok;
    }

    /** Records a problem of a remote. */
    protected function problem( $id, $message, $state = 'failed' )
    {
        $credential = isset( $this->credentials[$id] ) ? $this->credentials[$id] : null;
        $this->report['remotes'][$id]['problems'][] = $credential ? $credential->redact( $message ) : $message;
        $this->report['remotes'][$id]['state'] = $state;
        $this->say( "   $id: " . end( $this->report['remotes'][$id]['problems'] ) );
    }

    /** @return array the local definitions (option local, else the installation's), only filtered */
    protected function local()
    {
        $local = $this->options['local'];
        if ( $local === null )
            $local = self::localDefinitions( $this->options['root'], null, $this->report['warnings'] );
        $only = (array)$this->options['only'];
        if ( $only )
            $local = array_values( array_filter( $local, function ( $d ) use ( $only ) { return in_array( $d['id'], $only, true ); } ) );
        return $local;
    }

    /**
     * Phase prepare: settings, credentials, transports, host keys.
     *
     * @param bool $mustRun every remote must be able to run commands (not FTP)
     * @return bool
     */
    protected function prepare( $mustRun )
    {
        $ok = true;
        $keyDir = $this->options['work_dir'] . '/known_hosts';
        foreach ( $this->remotes as $id => $remote )
        {
            foreach ( $remote->validate() as $problem )
            {
                $this->problem( $id, $problem, 'refused' );
                $ok = false;
            }
            $credential = new expMigrationRemoteCredential( $remote->credential(), array( 'work_dir' => $this->options['work_dir'], 'root' => $this->options['root'],
                                                                                         'lookup' => $this->options['lookup'] ) );
            $this->credentials[$id] = $credential;
            if ( ( $why = $credential->problem() ) !== '' )
            {
                $this->problem( $id, $why, 'refused' );
                $ok = false;
            }
            if ( $mustRun && !$remote->canRun() )
            {
                $this->problem( $id, 'an FTP remote cannot run the migrations (no commands over FTP); use --transfer-only, give it the HTTP trigger (Trigger, TriggerSecret), or use ssh, sftp or rsync', 'refused' );
                $ok = false;
            }
            if ( !is_dir( $keyDir ) )
                @mkdir( $keyDir, eZDir::dirMode( 0700 ), true );
            $this->transports[$id] = new expMigrationRemoteTransport( $remote, $credential, $keyDir . '/' . $id );
            if ( $remote->trigger() !== '' )
            {
                // the HTTP trigger signs with a secret of its own, read like a password
                $key = new expMigrationRemoteCredential( 'password:' . $remote->triggerSecret(), array( 'work_dir' => $this->options['work_dir'],
                                                                                                      'root' => $this->options['root'], 'lookup' => $this->options['lookup'] ) );
                $why = $key->problem();
                if ( $why === '' && strlen( (string)$key->secret() ) < 16 )
                    $why = 'the secret ' . $remote->triggerSecret() . ' of the trigger is shorter than 16 characters';
                if ( $why !== '' )
                {
                    $this->problem( $id, 'TriggerSecret: ' . $why, 'refused' );
                    $ok = false;
                }
                else
                    $this->transports[$id]->setTriggerKey( $key->secret() );
            }
        }
        if ( !$ok )
            return false;

        // a remote with HostKeyFingerprint: its key is read and compared before the first login; once it is in the
        // remote's known_hosts it is not read again (a scan that does not log in counts against the source in the
        // penalties of a recent sshd)
        $scan = array();
        foreach ( $this->remotes as $id => $remote )
        {
            if ( $remote->hostKeyFingerprint() === '' || !in_array( $remote->transport(), array( 'ssh', 'sftp', 'rsync' ), true ) )
                continue;
            $known = is_file( $keyDir . '/' . $id ) ? (string)file_get_contents( $keyDir . '/' . $id ) : '';
            if ( $known !== '' && expMigrationRemoteTransport::matchingKeys( $known, $remote->hostKeyFingerprint() ) )
                $this->transports[$id]->setStrict( true );
            else
                $scan[$id] = array( $this->transports[$id]->keyscan() );
        }
        if ( $scan )
        {
            $results = $this->runRetried( $scan, 'hostkey' );
            foreach ( $scan as $id => $ops )
            {
                $lines = expMigrationRemoteTransport::matchingKeys( $results[$id]['stdout'], $this->remotes[$id]->hostKeyFingerprint() );
                if ( !$lines )
                {
                    $this->problem( $id, 'the host key of ' . $this->remotes[$id]->host() . ' does not have the fingerprint HostKeyFingerprint names (or the host was not reachable)', 'refused' );
                    $ok = false;
                    continue;
                }
                file_put_contents( $keyDir . '/' . $id, implode( "\n", $lines ) . "\n" );
                $this->transports[$id]->setStrict( true );
            }
        }
        foreach ( $this->remotes as $id => $remote )
            if ( $this->report['remotes'][$id]['state'] === 'selected' )
                $this->report['remotes'][$id]['state'] = 'prepared';
        return $ok;
    }

    /**
     * Runs jobs, again for the keys whose failure looks like the connection (bounded by the option retries).
     *
     * @param array $jobs key => operations
     * @param string $what for the attempts and fixes of the report
     * @return array key => result
     */
    protected function runRetried( array $jobs, $what )
    {
        $results = expMigrationRemoteProcess::runAll( $jobs, $this->options['parallel'], $this->options['timeout'] );
        foreach ( array_keys( $jobs ) as $id )
            $this->report['remotes'][$id]['attempts'][$what] = 1;
        for ( $try = 1; $try <= (int)$this->options['retries']; $try++ )
        {
            $again = array();
            foreach ( $jobs as $id => $ops )
                if ( isset( $results[$id] ) && self::connectionFailure( $results[$id] ) )
                    $again[$id] = $ops;
            if ( !$again )
                break;
            usleep( min( 5000000, 500000 * $try ) );
            foreach ( expMigrationRemoteProcess::runAll( $again, $this->options['parallel'], $this->options['timeout'] ) as $id => $result )
            {
                $this->report['remotes'][$id]['attempts'][$what] = $try + 1;
                $this->report['remotes'][$id]['fixes'][] = "$what: tried again after a connection failure (exit {$results[$id]['exit']})";
                $results[$id] = $result;
            }
        }
        return $results;
    }

    /**
     * Whether a failed result looks like the connection rather than the work: ssh's 255, a timeout, curl's connect
     * and transfer errors, rsync's connection codes. Pure.
     *
     * @param array $result
     * @return bool
     */
    public static function connectionFailure( array $result )
    {
        return in_array( (int)$result['exit'], array( -1, 255, 7, 28, 35, 52, 55, 56, 10, 12, 30 ), true )
            && stripos( (string)$result['stderr'], 'Permission denied' ) === false
            && stripos( (string)$result['stderr'], 'Host key verification failed' ) === false
            && stripos( (string)$result['stderr'], 'REMOTE HOST IDENTIFICATION HAS CHANGED' ) === false;
    }

    /**
     * The exp-migration/1 report in a command's output.
     *
     * @param string $stdout
     * @return array|null
     */
    public static function parseReport( $stdout )
    {
        $stdout = (string)$stdout;
        $start = strpos( $stdout, '{' );
        if ( $start === false )
            return null;
        $data = json_decode( substr( $stdout, $start ), true );
        return is_array( $data ) && isset( $data['schema'] ) && $data['schema'] === expMigrationRunner::REPORT ? $data : null;
    }

    /** @return string the end of a failed command's output, redacted, for a problem */
    protected function tail( $id, array $result )
    {
        $text = trim( (string)$result['stderr'] ) !== '' ? (string)$result['stderr'] : (string)$result['stdout'];
        $lines = array_slice( array_filter( array_map( 'trim', preg_split( '/\r?\n/', $text ) ) ), -3 );
        return $this->credentials[$id]->redact( implode( ' | ', $lines ) );
    }

    /**
     * Phase preflight: every remote's status, the plan of the files to send.
     *
     * @param array[]|null $local the source's migrations; null: status only (no plan)
     * @return bool
     */
    protected function preflight( $local )
    {
        $this->heal( array_values( array_diff( (array)$this->options['heal'], array( 'caches' ) ) ), 'prepared' );
        $jobs = array();
        foreach ( $this->remotes as $id => $remote )
        {
            if ( $this->report['remotes'][$id]['state'] !== 'prepared' )
                continue;
            if ( !$remote->canRun() )
            {
                // FTP: the remote's state cannot be read; every selected migration is sent and read back
                if ( $local !== null )
                    foreach ( $local as $definition )
                        foreach ( $definition['paths'] as $rel => $file )
                            $this->report['remotes'][$id]['send'][] = $rel;
                $this->report['remotes'][$id]['state'] = 'ready';
                $this->report['warnings'][] = "$id: an FTP remote's migration status cannot be read" . ( $local !== null ? '; every selected migration is sent' : '' );
                continue;
            }
            $jobs[$id] = array( $this->transports[$id]->run( array( self::STATUS, '--format=json', '--runs=1' ) ) );
        }
        $ok = true;
        $results = $jobs ? $this->runRetried( $jobs, 'preflight' ) : array();
        foreach ( $jobs as $id => $ops )
        {
            $result = $results[$id];
            $status = self::parseReport( $result['stdout'] );
            if ( $status === null )
            {
                $this->problem( $id, 'not reachable or no migration status (exit ' . $result['exit'] . '): ' . $this->tail( $id, $result ), 'unreachable' );
                $ok = false;
                continue;
            }
            $this->absorbStatus( $id, $status );
            $problems = self::statusProblems( $status, (string)$this->options['version'] );
            $bytes = null;
            if ( $local !== null )
            {
                $plan = self::plan( $local, $status );
                $this->report['remotes'][$id]['send'] = $plan['send'];
                $this->report['remotes'][$id]['pending'] = $plan['pending'];
                $problems = array_merge( $problems, $plan['problems'] );
                foreach ( $plan['ahead'] as $ahead )
                    $this->report['warnings'][] = "$id has the migration $ahead, which this installation does not have";
                $bytes = self::sendBytes( $local, $plan['send'] );
                $this->report['remotes'][$id]['send_bytes'] = $bytes;
            }
            $checks = self::hostProblems( isset( $status['host'] ) && is_array( $status['host'] ) ? $status['host'] : null, $this->sourceHost(), $bytes,
                                          self::extensionsOf( $local ), $this->options );
            $problems = array_merge( $problems, $checks['problems'] );
            foreach ( $checks['warnings'] as $warning )
                $this->report['warnings'][] = "$id: $warning";
            foreach ( $problems as $problem )
            {
                $this->problem( $id, $problem, 'refused' );
                $ok = false;
            }
            if ( !$problems )
                $this->report['remotes'][$id]['state'] = 'ready';
        }
        return $ok;
    }

    /** Keeps what the status of a remote says in the report. */
    protected function absorbStatus( $id, array $status )
    {
        $remote =& $this->report['remotes'][$id];
        $remote['version'] = isset( $status['version'] ) ? (string)$status['version'] : '';
        $remote['lock'] = isset( $status['lock'] ) ? $status['lock'] : null;
        $remote['last_run'] = isset( $status['runs'][0] ) ? $status['runs'][0] : null;
        if ( isset( $status['host'] ) && is_array( $status['host'] ) )
            $remote['host'] = array( 'php' => isset( $status['host']['php'] ) ? (string)$status['host']['php'] : '',
                                     'disk_free' => isset( $status['host']['disk_free'] ) ? $status['host']['disk_free'] : null,
                                     'extensions' => isset( $status['host']['extensions'] ) ? count( (array)$status['host']['extensions'] ) : 0 );
        $remote['states'] = array();
        foreach ( isset( $status['migrations'] ) ? (array)$status['migrations'] : array() as $entry )
            $remote['states'][$entry['source'] . ' ' . $entry['id']] = (string)$entry['state'];
        $counts = array( 'done' => 0, 'pending' => 0, 'failed' => 0 );
        foreach ( $remote['states'] as $state )
            if ( isset( $counts[$state] ) )
                $counts[$state]++;
        $remote['migrations'] = $counts;
        if ( !$remote['pending'] )
            foreach ( $remote['states'] as $key => $state )
                if ( $state === 'pending' )
                    $remote['pending'][] = substr( $key, strpos( $key, ' ' ) + 1 );
    }

    /**
     * What stops a deploy in a remote's status. Pure.
     *
     * @param array $status the remote's exp:migration:status report
     * @param string $version the source's kernel version ('' : not compared)
     * @return string[]
     */
    public static function statusProblems( array $status, $version )
    {
        $problems = array();
        if ( isset( $status['table'] ) && !$status['table'] )
            $problems[] = 'the migration tables do not exist there yet (./console exp:upgrade --apply on the remote)';
        if ( !empty( $status['lock'] ) )
            $problems[] = 'a migration run holds the lock there (' . ( is_array( $status['lock'] ) ? json_encode( $status['lock'] ) : (string)$status['lock'] ) . ')';
        foreach ( isset( $status['migrations'] ) ? (array)$status['migrations'] : array() as $entry )
            if ( isset( $entry['state'] ) && $entry['state'] === 'failed' )
                $problems[] = "the migration {$entry['id']} failed there; resolve it first (exp:migration:status on the remote)";
        if ( $version !== '' && isset( $status['version'] ) && $status['version'] !== '' && (string)$status['version'] !== $version )
            $problems[] = "the kernel version there is {$status['version']}, here $version: upgrade first, so the migrations run on the same code";
        if ( isset( $status['exit'] ) && (int)$status['exit'] !== expUpgrade::EXIT_OK && empty( $status['migrations'] ) )
            foreach ( isset( $status['errors'] ) ? (array)$status['errors'] : array() as $error )
                $problems[] = 'status: ' . $error;
        return $problems;
    }

    /**
     * The files to send to a remote and what stays pending there afterwards. Pure.
     *
     * A migration the remote has (same source and id, its file at the same path with one of the source's checksums)
     * is not sent. A migration applied there whose file differs here stops the deploy: the checksum guard would
     * refuse it, and the remote would not end in the source's state.
     *
     * @param array[] $local localDefinitions()
     * @param array $status the remote's exp:migration:status report
     * @return array send (relative paths), pending (migration ids pending after the transfer), problems, ahead
     */
    public static function plan( array $local, array $status )
    {
        $remote = array();
        foreach ( isset( $status['migrations'] ) ? (array)$status['migrations'] : array() as $entry )
            $remote[$entry['source'] . ' ' . $entry['id']] = $entry;
        $send = array();
        $pending = array();
        $problems = array();
        $known = array();
        foreach ( $local as $definition )
        {
            $key = $definition['source'] . ' ' . $definition['id'];
            $known[$key] = true;
            $there = isset( $remote[$key] ) ? $remote[$key] : null;
            $same = $there !== null && in_array( (string)$there['checksum'], array_map( 'strval', (array)$definition['checksums'] ), true )
                 && in_array( (string)$there['file'], array_values( (array)$definition['files'] ), true );
            if ( $there !== null && in_array( $there['state'], array( 'done' ), true ) )
            {
                if ( !$same && (string)$there['file'] !== '' )
                    $problems[] = "the migration {$definition['id']} was applied there from a file that differs from this installation's; "
                                . 'make the files equal (or accept it there with exp:migration:migrate --force --reason) before deploying';
                continue;
            }
            if ( $there !== null && $there['state'] === 'not-needed' && $same )
                continue;
            if ( !$same )
                foreach ( array_keys( (array)$definition['paths'] ) as $rel )
                    $send[] = $rel;
            $pending[] = $definition['id'];
        }
        $ahead = array();
        foreach ( $remote as $key => $entry )
            if ( !isset( $known[$key] ) && $entry['source'] !== expMigrationRecord::SOURCE_UPGRADE && strpos( $entry['source'], 'package:' ) !== 0 )
                $ahead[] = $entry['id'];
        sort( $send );
        return array( 'send' => array_values( array_unique( $send ) ), 'pending' => $pending, 'problems' => $problems, 'ahead' => $ahead );
    }

    /**
     * Phase transfer.
     *
     * @param array[] $local
     * @return bool
     */
    protected function transfer( array $local )
    {
        $paths = array();
        foreach ( $local as $definition )
            $paths += $definition['paths'];
        $jobs = array();
        foreach ( $this->remotes as $id => $remote )
        {
            $files = array();
            foreach ( $this->report['remotes'][$id]['send'] as $rel )
                $files[$rel] = $paths[$rel];
            if ( $files )
                $jobs[$id] = $this->transports[$id]->send( $files, $this->options['root'] );
            else
                $this->report['remotes'][$id]['state'] = 'sent';
        }
        $ok = true;
        foreach ( $jobs ? $this->runRetried( $jobs, 'transfer' ) : array() as $id => $result )
        {
            if ( $result['exit'] !== 0 )
            {
                $this->problem( $id, 'the transfer failed (exit ' . $result['exit'] . '): ' . $this->tail( $id, $result ) );
                $ok = false;
            }
            else
            {
                $this->report['remotes'][$id]['state'] = 'sent';
                $this->say( "   $id: " . count( $this->report['remotes'][$id]['send'] ) . ' file(s) sent' );
            }
        }
        return $ok;
    }

    /**
     * Phase verify: every remote sees what was sent, unchanged, and can be taken back.
     *
     * @param array[] $local
     * @return bool
     */
    protected function verify( array $local )
    {
        $checksums = array();
        foreach ( $local as $definition )
            foreach ( $definition['files'] as $engine => $rel )
                $checksums[$rel] = (string)$definition['checksums'][$engine];
        $jobs = array();
        $fetch = array();
        foreach ( $this->remotes as $id => $remote )
        {
            if ( $remote->canRun() )
                $jobs[$id] = array( $this->transports[$id]->run( array_merge( array( self::MIGRATE, '--format=json' ), $this->onlyArgument() ) ) );
            else
                foreach ( $this->report['remotes'][$id]['send'] as $rel )
                    $fetch[$id . "\n" . $rel] = array( $this->transports[$id]->fetch( $rel ) );
        }
        $ok = true;
        foreach ( $fetch ? expMigrationRemoteProcess::runAll( $fetch, $this->options['parallel'], $this->options['timeout'] ) : array() as $key => $result )
        {
            list( $id, $rel ) = explode( "\n", $key, 2 );
            if ( $result['exit'] !== 0 || expMigrationDefinition::checksumOf( $result['stdout'] ) !== $checksums[$rel] )
            {
                $this->problem( $id, "$rel did not arrive unchanged" );
                $ok = false;
            }
        }
        foreach ( $fetch as $key => $ops )
        {
            $id = substr( $key, 0, strpos( $key, "\n" ) );
            if ( $this->report['remotes'][$id]['state'] === 'sent' )
                $this->report['remotes'][$id]['state'] = 'verified';
        }
        foreach ( $this->remotes as $id => $remote )
            if ( !$remote->canRun() && !$this->report['remotes'][$id]['send'] )
                $this->report['remotes'][$id]['state'] = 'verified';
        $results = $jobs ? $this->runRetried( $jobs, 'verify' ) : array();
        foreach ( $jobs as $id => $ops )
        {
            $dry = self::parseReport( $results[$id]['stdout'] );
            $problems = $dry === null ? array( 'no dry run there (exit ' . $results[$id]['exit'] . '): ' . $this->tail( $id, $results[$id] ) )
                                      : self::verifyProblems( $dry, $this->report['remotes'][$id]['send'], $checksums );
            foreach ( $problems as $problem )
            {
                $this->problem( $id, $problem );
                $ok = false;
            }
            if ( !$problems )
            {
                $this->report['remotes'][$id]['state'] = 'verified';
                $this->report['remotes'][$id]['pending'] = array();
                foreach ( (array)$dry['migrations'] as $entry )
                    if ( $entry['action'] === 'would-apply' )
                        $this->report['remotes'][$id]['pending'][] = $entry['id'];
            }
        }
        return $ok;
    }

    /**
     * What a remote's dry run says against switching it. Pure.
     *
     * @param array $dry the remote's exp:migration:migrate dry run report
     * @param string[] $sent the relative paths sent to it
     * @param array $checksums relative path => the source's checksum
     * @return string[]
     */
    public static function verifyProblems( array $dry, array $sent, array $checksums )
    {
        $problems = array();
        if ( isset( $dry['exit'] ) && (int)$dry['exit'] !== expUpgrade::EXIT_OK )
            foreach ( (array)( isset( $dry['errors'] ) && $dry['errors'] ? $dry['errors'] : array( 'the dry run failed (exit ' . (int)$dry['exit'] . ')' ) ) as $error )
                $problems[] = 'dry run: ' . $error;
        // a remote with nothing to apply takes no caption (its dry run says off) and needs none: nothing to take back
        $applies = false;
        foreach ( isset( $dry['migrations'] ) ? (array)$dry['migrations'] : array() as $entry )
            if ( isset( $entry['action'] ) && $entry['action'] === 'would-apply' )
                $applies = true;
        if ( $applies && ( !isset( $dry['captions']['pre'] ) || $dry['captions']['pre'] === 'off' ) )
            $problems[] = 'captions are off there ([CaptionSettings]): without the caption before the run it could not be taken back, so it is not switched';
        $seen = array();
        foreach ( isset( $dry['migrations'] ) ? (array)$dry['migrations'] : array() as $entry )
            if ( isset( $entry['file'] ) && $entry['file'] !== '' )
                $seen[(string)$entry['file']] = (string)$entry['checksum'];
        foreach ( $sent as $rel )
            if ( isset( $seen[$rel] ) && $seen[$rel] !== $checksums[$rel] )
                $problems[] = "$rel arrived changed (checksum " . substr( $seen[$rel], 0, 12 ) . ', expected ' . substr( $checksums[$rel], 0, 12 ) . ')';
        return $problems;
    }

    /** @return string[] --only=... when the deploy is limited to migrations */
    protected function onlyArgument()
    {
        return $this->options['only'] ? array( '--only=' . implode( ',', (array)$this->options['only'] ) ) : array();
    }

    /**
     * Phase switch: every remote applies, with the deploy's id as its run id.
     *
     * @return bool true when every remote applied without a failure
     */
    protected function switchAll()
    {
        $jobs = array();
        foreach ( $this->remotes as $id => $remote )
            $jobs[$id] = array( $this->transports[$id]->run( array_merge( array( self::MIGRATE, '--apply', '--format=json', '--run-id=' . $this->deployId() ),
                                                                          $this->onlyArgument() ) ) );
        // never retried: a run that may have started is not started again
        $results = expMigrationRemoteProcess::runAll( $jobs, count( $jobs ), $this->options['timeout'] );
        $ok = true;
        foreach ( $jobs as $id => $ops )
        {
            $run = self::parseReport( $results[$id]['stdout'] );
            $remote =& $this->report['remotes'][$id];
            if ( $run === null )
            {
                $remote['state'] = 'unknown';
                $remote['problems'][] = 'no report of the run (exit ' . $results[$id]['exit'] . '): ' . $this->tail( $id, $results[$id] );
                $ok = false;
                unset( $remote );
                continue;
            }
            $remote['applied'] = array();
            foreach ( (array)$run['migrations'] as $entry )
                if ( $entry['action'] === 'applied' )
                    $remote['applied'][] = $entry['id'];
            $remote['caption'] = isset( $run['captions']['pre']['id'] ) ? (string)$run['captions']['pre']['id'] : '';
            $remote['restore'] = isset( $run['restore']['state'] ) ? (string)$run['restore']['state'] : '';
            $failed = isset( $run['summary']['failed'] ) ? (int)$run['summary']['failed'] : 0;
            if ( (int)$run['exit'] !== expUpgrade::EXIT_OK || $failed > 0 )
            {
                $remote['state'] = $remote['restore'] === 'restored' ? 'restored' : 'failed';
                foreach ( (array)$run['errors'] as $error )
                    $remote['problems'][] = $this->credentials[$id]->redact( (string)$error );
                $ok = false;
            }
            else
                $remote['state'] = 'applied';
            $this->say( "   $id: " . $remote['state'] . ' (' . count( $remote['applied'] ) . ' applied)' );
            unset( $remote );
        }
        if ( $ok && in_array( 'caches', (array)$this->options['heal'], true ) )
            $this->heal( array( 'caches' ), 'applied' );
        return $ok;
    }

    /**
     * The fixes of SelfHeal[] on the remotes in a state (exp:migration:heal --apply): what was fixed goes in each
     * remote's fixes, what was found and left in the warnings. A fix never stops the deploy; the checks after it do.
     *
     * @param string[] $fixes
     * @param string $state only the remotes in this state
     */
    protected function heal( array $fixes, $state )
    {
        $fixes = expMigrationHeal::normalize( $fixes );
        if ( !$fixes )
            return;
        $jobs = array();
        foreach ( $this->remotes as $id => $remote )
        {
            if ( $this->report['remotes'][$id]['state'] !== $state || !$remote->canRun() )
                continue;
            $args = array( self::HEAL, '--apply', '--format=json', '--fix=' . implode( ',', $fixes ) );
            if ( in_array( 'stale-lock', $fixes, true ) )
                $args[] = '--stale-lock-age=' . max( 60, (int)$this->options['stale_lock_age'] );
            $op = $this->transports[$id]->run( $args );
            if ( $op !== null )
                $jobs[$id] = array( $op );
        }
        $results = $jobs ? $this->runRetried( $jobs, 'heal-' . implode( '-', $fixes ) ) : array();
        foreach ( $jobs as $id => $ops )
        {
            $report = self::parseReport( $results[$id]['stdout'] );
            if ( $report === null || !isset( $report['fixes'] ) )
            {
                $this->report['warnings'][] = "$id: the self-healing (" . implode( ', ', $fixes ) . ') gave no report (exit ' . $results[$id]['exit'] . '): ' . $this->tail( $id, $results[$id] );
                continue;
            }
            foreach ( (array)$report['fixes'] as $fix )
            {
                $this->report['remotes'][$id]['fixes'][] = 'heal ' . $this->credentials[$id]->redact( (string)$fix );
                $this->say( "   $id: fixed " . $fix );
            }
            foreach ( array_merge( (array)$report['problems'], isset( $report['warnings'] ) ? (array)$report['warnings'] : array() ) as $problem )
                $this->report['warnings'][] = "$id: " . $this->credentials[$id]->redact( (string)$problem );
        }
    }

    /** @return array this installation's hostInfo() (option host, else read once) */
    protected function sourceHost()
    {
        if ( $this->options['host'] === null )
            $this->options['host'] = self::hostInfo( $this->options['root'] );
        return (array)$this->options['host'];
    }

    /**
     * What a status report says about its host, for a deploy's preflight: the PHP version, the free disk space of the
     * installation's file system, and the active extensions with their versions (ezinfo.php or extension.xml).
     *
     * @param string $root
     * @param string[]|null $extensions the active extensions (default eZExtension::activeExtensions())
     * @return array php, disk_free (bytes, null when unknown), extensions (name => version, '' when it states none)
     */
    public static function hostInfo( $root, $extensions = null )
    {
        $free = @disk_free_space( (string)$root );
        $versions = array();
        if ( $extensions === null )
            $extensions = class_exists( 'eZExtension' ) ? eZExtension::activeExtensions() : array();
        foreach ( (array)$extensions as $extension )
        {
            $version = '';
            try
            {
                $info = class_exists( 'ezpExtension' ) ? ezpExtension::getInstance( (string)$extension )->getInfo() : null;
                foreach ( is_array( $info ) ? $info : array() as $key => $value )
                    if ( is_string( $value ) && strtolower( (string)$key ) === 'version' )
                        $version = trim( $value );
            }
            catch ( Throwable $e )
            {
                $version = '';
            }
            $versions[(string)$extension] = $version;
        }
        ksort( $versions );
        return array( 'php' => PHP_VERSION, 'disk_free' => $free === false ? null : (int)$free, 'extensions' => $versions );
    }

    /**
     * The extensions whose migrations a deploy sends (source extension:<name>). Pure.
     *
     * @param array[]|null $local
     * @return string[]
     */
    public static function extensionsOf( $local )
    {
        $names = array();
        foreach ( (array)$local as $definition )
            if ( strpos( (string)$definition['source'], 'extension:' ) === 0 )
                $names[substr( (string)$definition['source'], 10 )] = true;
        return array_keys( $names );
    }

    /**
     * The size of the files a remote is sent, in bytes.
     *
     * @param array[] $local
     * @param string[] $send relative paths
     * @return int
     */
    public static function sendBytes( array $local, array $send )
    {
        $paths = array();
        foreach ( $local as $definition )
            $paths += (array)$definition['paths'];
        $bytes = 0;
        foreach ( $send as $rel )
            if ( isset( $paths[$rel] ) )
                $bytes += (int)@filesize( $paths[$rel] );
        return $bytes;
    }

    /**
     * What stops a deploy, or is only worth a warning, in the host of a remote's status: free disk space against the
     * transfer and the margin, the PHP version, the versions of the extensions. Pure.
     *
     * @param array|null $remote the remote's status host (hostInfo()); null: an older kernel that does not say
     * @param array $source this installation's hostInfo()
     * @param int|null $bytes the size of the transfer; null: no transfer (status), not checked
     * @param string[] $sent the extensions whose migrations are sent
     * @param array $settings disk_margin, php_check (major, minor, warn, off), extension_check (migrations, all, warn, off)
     * @return array problems, warnings
     */
    public static function hostProblems( $remote, array $source, $bytes, array $sent, array $settings )
    {
        $settings += array( 'disk_margin' => self::DISK_MARGIN, 'php_check' => 'major', 'extension_check' => 'migrations' );
        $problems = array();
        $warnings = array();
        if ( !is_array( $remote ) )
            return array( 'problems' => array(), 'warnings' => array( 'its status does not say its PHP version, extensions and free disk space (an older kernel): not checked' ) );
        $mb = function ( $value ) { return number_format( $value / 1048576, 1, '.', '' ) . ' MB'; };
        if ( $bytes !== null && isset( $remote['disk_free'] ) && $remote['disk_free'] !== null )
        {
            $need = (int)$bytes + max( 0, (int)$settings['disk_margin'] );
            if ( (int)$remote['disk_free'] < $need )
                $problems[] = 'only ' . $mb( (int)$remote['disk_free'] ) . ' free there; the transfer needs ' . $mb( (int)$bytes ) . ' and the margin '
                            . $mb( (int)$settings['disk_margin'] ) . ' ([RemoteSettings] DiskMargin)';
        }
        $php = isset( $remote['php'] ) ? (string)$remote['php'] : '';
        $here = isset( $source['php'] ) ? (string)$source['php'] : '';
        $check = (string)$settings['php_check'];
        if ( $check !== 'off' && $php !== '' && $here !== '' )
        {
            $parts = $check === 'minor' ? 2 : 1;
            $cut = function ( $version ) use ( $parts ) { return implode( '.', array_slice( explode( '.', $version ), 0, $parts ) ); };
            if ( $cut( $php ) !== $cut( $here ) )
            {
                $text = "PHP there is $php, here $here ([RemoteSettings] PhpCheck=$check)";
                if ( $check === 'warn' )
                    $warnings[] = $text;
                else
                    $problems[] = $text . ': a migration may behave differently';
            }
        }
        $check = (string)$settings['extension_check'];
        if ( $check !== 'off' )
        {
            $there = isset( $remote['extensions'] ) && is_array( $remote['extensions'] ) ? $remote['extensions'] : array();
            $mine = isset( $source['extensions'] ) && is_array( $source['extensions'] ) ? $source['extensions'] : array();
            foreach ( $mine as $name => $version )
            {
                $needed = $check === 'all' || in_array( (string)$name, $sent, true );
                if ( !array_key_exists( $name, $there ) )
                    $text = "the extension $name is not active there";
                else if ( (string)$there[$name] !== (string)$version )
                    $text = "the extension $name is " . ( (string)$there[$name] !== '' ? $there[$name] : 'without a version' ) . ' there, here '
                          . ( (string)$version !== '' ? $version : 'without a version' );
                else
                    continue;
                if ( $needed && $check !== 'warn' )
                    $problems[] = $text . ' ([RemoteSettings] ExtensionCheck=' . $check . ')';
                else if ( $needed || $check === 'warn' )
                    $warnings[] = $text;
            }
        }
        return array( 'problems' => $problems, 'warnings' => $warnings );
    }

    /**
     * Phase rollback: every remote that applied is taken back to the caption taken before the run.
     *
     * @return bool true when every remote is back where it was
     */
    protected function rollbackAll()
    {
        $jobs = array();
        foreach ( $this->remotes as $id => $remote )
            if ( $this->report['remotes'][$id]['state'] === 'applied' && $this->report['remotes'][$id]['applied'] )
                $jobs[$id] = array( $this->transports[$id]->run( array( self::ROLLBACK, $this->deployId(), '--apply', '--format=json' ) ) );
            else if ( $this->report['remotes'][$id]['state'] === 'applied' )
                $this->report['remotes'][$id]['state'] = 'unchanged';
        $results = $jobs ? expMigrationRemoteProcess::runAll( $jobs, count( $jobs ), $this->options['timeout'] ) : array();
        $back = true;
        foreach ( $jobs as $id => $ops )
        {
            $report = self::parseReport( $results[$id]['stdout'] );
            if ( $report !== null && (int)$report['exit'] === expUpgrade::EXIT_OK )
                $this->report['remotes'][$id]['state'] = 'rolled-back';
            else
            {
                $this->problem( $id, 'the rollback failed (exit ' . $results[$id]['exit'] . '): '
                                     . ( $report !== null && $report['errors'] ? implode( '; ', (array)$report['errors'] ) : $this->tail( $id, $results[$id] ) ), 'out-of-sync' );
                $back = false;
            }
        }
        foreach ( $this->remotes as $id => $remote )
        {
            $state = $this->report['remotes'][$id]['state'];
            if ( $state === 'failed' && !$this->report['remotes'][$id]['applied'] )
                $this->report['remotes'][$id]['state'] = 'unchanged';
            else if ( $state === 'failed' || $state === 'unknown' )
            {
                $this->report['remotes'][$id]['state'] = 'out-of-sync';
                $back = false;
            }
        }
        $this->report['state'] = $back ? 'rolled-back' : 'out-of-sync';
        $this->audit( 'system.migration.deploy.rollback', array( 'verb' => 'rollback' ) );
        return $back;
    }

    /**
     * Whether the remotes of a status are in sync: each reachable, every migration known anywhere done (or not
     * needed) on all of them or on none, nothing failed.
     *
     * @return bool|null null when a remote could not be read
     */
    protected function inSync()
    {
        $sets = array();
        foreach ( $this->report['remotes'] as $id => $remote )
        {
            if ( !isset( $remote['states'] ) )
                return null;
            $sets[$id] = $remote['states'];
        }
        return self::statesInSync( $sets );
    }

    /**
     * Pure: remote => (source id => state).
     *
     * @param array $sets
     * @return bool
     */
    public static function statesInSync( array $sets )
    {
        $keys = array();
        foreach ( $sets as $states )
            $keys += array_fill_keys( array_keys( $states ), true );
        foreach ( array_keys( $keys ) as $key )
        {
            $settled = null;
            foreach ( $sets as $states )
            {
                $state = isset( $states[$key] ) ? $states[$key] : 'missing';
                if ( $state === 'failed' )
                    return false;
                $now = in_array( $state, array( 'done', 'not-needed' ), true );
                if ( $settled !== null && $settled !== $now )
                    return false;
                $settled = $now;
            }
        }
        return true;
    }

    /**
     * Ends the deploy: state, exit, stored report, audit.
     *
     * @param string $state
     * @param string $error
     * @return array
     */
    protected function end( $state, $error = '' )
    {
        $report =& $this->report;
        $report['state'] = $state;
        $report['finished_at'] = date( 'c' );
        if ( $error !== '' )
            $report['errors'][] = $error;
        if ( $state !== 'status' )
        {
            // the remotes of earlier stages that stay deployed while a later stage was taken back or waits
            $applied = 0;
            foreach ( $report['remotes'] as &$remote )
            {
                if ( $remote['state'] === 'applied' )
                    $applied++;
                // a later stage that will not be deployed now
                if ( $remote['state'] === 'waiting' && $state !== 'gated' )
                    $remote['state'] = 'skipped';
            }
            unset( $remote );
            if ( $state === 'gated' )
                $report['in_sync'] = null;
            else if ( $state === 'rolled-back' && $applied > 0 )
            {
                $report['in_sync'] = false;
                $report['warnings'][] = 'the remotes of the earlier stages stay deployed; roll the deploy back to take them back too';
            }
            else
                $report['in_sync'] = in_array( $state, array( 'deployed', 'rolled-back' ), true ) ? true : ( $state === 'out-of-sync' ? false : null );
        }
        foreach ( $report['remotes'] as $id => &$remote )
        {
            unset( $remote['states'] );
            foreach ( $remote['problems'] as $problem )
                $report['errors'][] = "$id: $problem";
        }
        unset( $remote );
        $report['errors'] = array_values( array_unique( $report['errors'] ) );
        $report['warnings'] = array_values( array_unique( $report['warnings'] ) );
        $report['exit'] = in_array( $state, array( 'planned', 'transferred', 'deployed' ), true ) || ( in_array( $state, array( 'status', 'gated' ), true ) && !$report['errors'] )
                        || ( $state === 'rolled-back' && $this->takingBack && !$report['errors'] )
                        ? expUpgrade::EXIT_OK : expUpgrade::EXIT_FAILED;
        $this->emit( 'end', array( 'exit' => $report['exit'], 'state' => $state ) );
        if ( $state !== 'status' )
        {
            if ( $report['mode'] !== 'dry-run' && $this->options['store'] )
                $this->store( $report );
            $this->audit( 'system.migration.deploy', array( 'verb' => $report['mode'] ) );
            if ( $report['mode'] !== 'dry-run' && is_callable( $this->options['notify'] ) )
            {
                try
                {
                    call_user_func( $this->options['notify'], $report );
                }
                catch ( Throwable $e )
                {
                    // a notice that cannot be sent is never an error of the deploy
                    $report['warnings'][] = 'the notice could not be sent: ' . $e->getMessage();
                }
            }
        }
        return $report;
    }

    /**
     * Writes the report to var/<site>/migration/deploy/reports/<deploy id>.json, and its rows to the tables when the
     * option db names a connection that has them.
     */
    protected function store( array $report )
    {
        $dir = $this->options['work_dir'] . '/reports';
        if ( !is_dir( $dir ) )
            @mkdir( $dir, eZDir::dirMode( 0775 ), true );
        @file_put_contents( $dir . '/' . $this->deployId() . '.json', json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" );
        if ( $this->options['db'] )
        {
            try
            {
                expMigrationDeployRecord::write( $this->options['db'], $report );
            }
            catch ( Throwable $e )
            {
                if ( class_exists( 'eZDebug' ) )
                    eZDebug::writeWarning( 'The deploy record could not be written: ' . $e->getMessage(), __METHOD__ );
            }
        }
    }

    /**
     * The stored reports, the newest first.
     *
     * @param int $count
     * @param string|null $workDir
     * @return array[] deploy_id, mode, state, in_sync, started_at, remotes (id => state)
     */
    public static function history( $count = 10, $workDir = null )
    {
        $dir = ( $workDir !== null ? rtrim( (string)$workDir, '/' ) : self::workDirectory() ) . '/reports';
        $files = is_dir( $dir ) ? glob( $dir . '/*.json' ) : array();
        rsort( $files );
        $list = array();
        foreach ( array_slice( $files ?: array(), 0, max( 0, (int)$count ) ) as $file )
        {
            $data = json_decode( (string)file_get_contents( $file ), true );
            if ( !is_array( $data ) || !isset( $data['deploy_id'] ) )
                continue;
            $remotes = array();
            foreach ( (array)$data['remotes'] as $id => $remote )
                $remotes[$id] = $remote['state'];
            $list[] = array( 'deploy_id' => $data['deploy_id'], 'mode' => $data['mode'], 'state' => $data['state'], 'in_sync' => $data['in_sync'],
                             'started_at' => $data['started_at'], 'remotes' => $remotes,
                             'finished_at' => isset( $data['finished_at'] ) ? $data['finished_at'] : null, 'user' => isset( $data['user'] ) ? (string)$data['user'] : '',
                             'stages' => isset( $data['stages'] ) ? count( (array)$data['stages'] ) : 0,
                             'next_stage' => isset( $data['next_stage'] ) ? $data['next_stage'] : null );
        }
        return $list;
    }

    /** The audit event of the deploy: the remotes and their states, never an address detail beyond the host. */
    protected function audit( $name, array $data )
    {
        $states = array();
        foreach ( $this->report['remotes'] as $id => $remote )
            $states[$id] = $remote['state'];
        $ok = $name === 'system.migration.deploy.rollback' ? $this->report['state'] === 'rolled-back'
                                                          : in_array( $this->report['state'], array( 'planned', 'transferred', 'deployed', 'gated' ), true );
        $data += array( 'object' => array( 'type' => 'migration_deploy', 'id' => $this->deployId() ), 'result' => $ok ? 'success' : 'failure',
                        'x' => array( 'state' => $this->report['state'], 'remotes' => $states ) );
        if ( is_callable( $this->options['audit'] ) )
            call_user_func( $this->options['audit'], $name, $data );
        else
            expUpgrade::audit( $name, $data );
    }
}
