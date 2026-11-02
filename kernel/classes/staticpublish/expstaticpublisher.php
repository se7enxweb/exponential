<?php
/**
 * File containing the expStaticPublisher class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Publishing a target (static publishing): what exp:static:publish, exp:static:rollback, exp:static:push and the
 * cronjob part staticpublish do, and what the administration calls.
 *
 *   publish()   under the target's lock: optionally a build first (expStaticBuild; nothing is committed unless it
 *               applied), then the commit of the build in the target's checkout, its tag and the push
 *               (expStaticRepository); one row in exp_static_run, the pages in exp_static_page, the audit event
 *               system.staticpublish.publish
 *   rollback()  under the lock: a new commit with the files of an earlier tag, tagged and pushed; recorded as a run
 *               of kind rollback, audited as system.staticpublish.rollback
 *   retry()     pushes the targets whose runs wait for a push (push_state pending) again; a push sends the branch with
 *               every commit, so all pending runs of that target are pushed at once
 *
 * Every report is exp-static/1: mode, target, run_id, commit_id, tag_name, push_state, counts, messages, errors,
 * warnings, exit. No report, row or audit event holds a credential's value; only its kind and name.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticPublisher
{
    /** @var expStaticTarget */
    protected $target;

    /** @var array */
    protected $options;

    /**
     * @param expStaticTarget $target
     * @param array $options root, var_dir, work_dir, db (eZDBInterface|null), by (who runs it), emit (callable( $type,
     *        $message )), push (bool|null: null takes [StaticPublishSettings] Push), credentials (expStaticCredentials,
     *        default from the target's Credential), build (array: options of expStaticBuild), commit_name,
     *        commit_email, git_manager, lookup (secret lookup for the credentials, tests), audit (callable( $name, $data ) instead of
     *        expAudit, tests), run_id (the id of the run instead of a new one: the administration's job id), notify (true:
     *        the e-mail and webhook of the target's Notify[] and Webhook after a publish or rollback, expStaticNotifier;
     *        an expStaticNotifier to use; false, the default, sends nothing)
     */
    public function __construct( expStaticTarget $target, array $options = array() )
    {
        $this->target = $target;
        $this->options = $options + array( 'root' => (string)getcwd(), 'var_dir' => null, 'work_dir' => null, 'db' => null, 'by' => '',
                                           'emit' => null, 'push' => null, 'credentials' => null, 'build' => array(), 'commit_name' => null,
                                           'commit_email' => null, 'git_manager' => true, 'lookup' => null, 'audit' => null, 'run_id' => '',
                                           'notify' => false );
    }

    /** @return string a [StaticPublishSettings] value, or the default */
    protected static function setting( $name, $default )
    {
        $ini = eZINI::instance( 'staticcache.ini' );
        return $ini->hasVariable( 'StaticPublishSettings', $name ) ? (string)$ini->variable( 'StaticPublishSettings', $name ) : $default;
    }

    /** @return string the folder of all targets */
    public function workDirectory()
    {
        $varDir = $this->options['var_dir'] !== null ? (string)$this->options['var_dir'] : eZSys::varDirectory();
        $workDir = $this->options['work_dir'] !== null ? (string)$this->options['work_dir'] : self::setting( 'WorkDir', 'static-publish' );
        $dir = expStaticTarget::workDirectory( $varDir, $workDir );
        if ( $dir === '' || ( $dir[0] !== '/' && !preg_match( '#^[a-zA-Z]:/#', $dir ) ) )
            $dir = rtrim( str_replace( '\\', '/', (string)$this->options['root'] ), '/' ) . '/' . $dir;
        return $dir;
    }

    /** @return string the target's checkout */
    public function checkoutDirectory()
    {
        return $this->workDirectory() . '/' . $this->target->id();
    }

    /** @return expStaticCredentials */
    public function credentials()
    {
        if ( !$this->options['credentials'] instanceof expStaticCredentials )
            $this->options['credentials'] = new expStaticCredentials( (string)$this->target->setting( 'Credential' ),
                                                                      array( 'work_dir' => $this->workDirectory(), 'root' => $this->options['root'],
                                                                             'lookup' => $this->options['lookup'] ) );
        return $this->options['credentials'];
    }

    /** @return expStaticRepository */
    public function repository()
    {
        $push = $this->options['push'] !== null ? (bool)$this->options['push'] : self::setting( 'Push', 'enabled' ) !== 'disabled';
        return new expStaticRepository( $this->target, $this->checkoutDirectory(), $this->credentials(), array(
            'push' => $push,
            'commit_name' => $this->options['commit_name'] !== null ? (string)$this->options['commit_name'] : self::setting( 'CommitName', 'Exponential static publishing' ),
            'commit_email' => $this->options['commit_email'] !== null ? (string)$this->options['commit_email'] : self::setting( 'CommitEmail', '' ),
            'git_manager' => $this->options['git_manager'],
        ) );
    }

    /** @return string who runs it */
    protected function by()
    {
        if ( $this->options['by'] !== '' )
            return (string)$this->options['by'];
        return class_exists( 'expUpgradeRecord' ) ? expUpgradeRecord::by() : php_sapi_name();
    }

    protected function say( $type, $message )
    {
        if ( is_callable( $this->options['emit'] ) )
            call_user_func( $this->options['emit'], $type, $message );
    }

    /** @return array an empty report of a mode */
    protected function report( $mode, $runId )
    {
        return array( 'mode' => $mode, 'target' => $this->target->id(), 'run_id' => $runId, 'directory' => $this->checkoutDirectory(),
                      'branch' => (string)$this->target->setting( 'Branch' ), 'remote' => (string)$this->target->setting( 'Remote' ),
                      'credential' => $this->credentials()->describe(), 'build' => null, 'state' => '', 'commit_id' => '', 'tag_name' => '',
                      'tag_number' => 0, 'push_state' => expStaticRepository::NONE, 'counts' => array( 'pages' => 0, 'files' => 0, 'bytes' => 0,
                      'added' => 0, 'changed' => 0, 'removed' => 0 ), 'messages' => array(), 'errors' => array(), 'warnings' => array(), 'exit' => 0 );
    }

    /**
     * Publishes the target's build.
     *
     * @param array $options build (bool: build first), message (the commit message), push (bool|null)
     * @return array the report
     */
    public function publish( array $options = array() )
    {
        $options += array( 'build' => false, 'message' => '' );
        $started = time();
        $runId = (string)$this->options['run_id'] !== '' ? (string)$this->options['run_id']
               : ( isset( $this->options['build']['run_id'] ) && $this->options['build']['run_id'] !== '' ? (string)$this->options['build']['run_id'] : expStaticBuild::newRunId() );
        $report = $this->report( 'publish', $runId );
        $problem = '';
        $lock = expStaticLock::acquire( $this->workDirectory(), $this->target->id(), 'publish', $problem );
        if ( $lock === null )
        {
            $report['errors'][] = $problem;
            return $this->done( $report );
        }
        try
        {
            if ( $options['build'] )
            {
                $this->say( 'phase', 'Building ' . $this->target->id() );
                $build = new expStaticBuild( $this->target, array( 'run_id' => $runId, 'root' => $this->options['root'], 'var_dir' => $this->options['var_dir'],
                                                                   'work_dir' => $this->options['work_dir'], 'db' => $this->options['db'],
                                                                   'emit' => $this->options['emit'] ) + $this->options['build'] );
                $report['build'] = $build->run();
                $report['warnings'] = array_merge( $report['warnings'], (array)$report['build']['warnings'] );
                if ( empty( $report['build']['applied'] ) )
                {
                    $report['errors'] = array_merge( $report['errors'], (array)$report['build']['errors'] );
                    $report['errors'][] = 'the build did not finish, so nothing is committed or pushed';
                    $report['state'] = expStaticStatus::FAILED;
                    $this->record( $report, expStaticStatus::PUBLISH, $started );
                    return $this->done( $report );
                }
            }
            $this->say( 'phase', 'Committing in ' . $this->checkoutDirectory() );
            $repo = $this->repository();
            $git = array();
            $manifest = expStaticManifest::load( $this->checkoutDirectory() . '/' . $repo->prefix() );
            if ( $manifest !== null )
                $report['counts'] = array( 'pages' => $manifest->count( 'page' ), 'files' => count( $manifest->entries() ), 'bytes' => $manifest->bytes() ) + $report['counts'];
            $message = trim( (string)$options['message'] ) !== '' ? trim( (string)$options['message'] ) : self::message( $this->target, $manifest, $runId );
            $committed = $repo->prepare( $git ) && $repo->commit( $message, $git );
            $this->merge( $report, $git );
            if ( $git['problems'] )
                $report['state'] = expStaticStatus::FAILED;
            else
                $report['state'] = $committed ? expStaticStatus::DONE : expStaticStatus::UNCHANGED;
            if ( !$git['problems'] && ( $committed || $repo->hasHead() ) )
            {
                $this->say( 'phase', 'Pushing' );
                $pushed = array();
                $report['push_state'] = $repo->push( $pushed );
                $this->merge( $report, $pushed );
                if ( $report['push_state'] === expStaticRepository::PUSHED )
                    expStaticStatus::setPushState( $this->options['db'], array_column( expStaticStatus::pending( $this->options['db'], $this->target->id() ), 'run_id' ),
                                                   expStaticRepository::PUSHED );
                if ( !$committed )
                    $report['push_state'] = $report['push_state'] === expStaticRepository::PUSHED ? expStaticRepository::PUSHED : expStaticRepository::NONE;
            }
            $this->record( $report, expStaticStatus::PUBLISH, $started );
            if ( $committed && $manifest !== null )
                expStaticStatus::writePages( $this->options['db'], $this->target->id(), $manifest );
        }
        finally
        {
            $this->credentials()->cleanup();
            $lock->release();
        }
        return $this->done( $report );
    }

    /**
     * Rolls the target back to an earlier publish.
     *
     * @param string $to static/<target>/<n> or <n>
     * @param array $options apply (bool), message
     * @return array the report
     */
    public function rollback( $to, array $options = array() )
    {
        $options += array( 'apply' => false, 'message' => '' );
        $started = time();
        $report = $this->report( 'rollback', (string)$this->options['run_id'] !== '' ? (string)$this->options['run_id'] : expStaticBuild::newRunId() );
        $report['apply'] = (bool)$options['apply'];
        $report['from_tag'] = '';
        $report['files'] = array();
        $problem = '';
        $lock = expStaticLock::acquire( $this->workDirectory(), $this->target->id(), 'rollback', $problem );
        if ( $lock === null )
        {
            $report['errors'][] = $problem;
            return $this->done( $report );
        }
        try
        {
            $repo = $this->repository();
            $prefix = $repo->prefix();
            $before = expStaticManifest::load( $this->checkoutDirectory() . '/' . $prefix );
            $number = expStaticRepository::tagNumber( $to, $this->target->id() );
            $message = trim( (string)$options['message'] ) !== '' ? trim( (string)$options['message'] )
                     : 'Roll back ' . $this->target->id() . ' to ' . expStaticRepository::tagName( $this->target->id(), $number );
            $git = array( 'problems' => array() );
            $committed = false;
            $check = $repo->checkoutProblem();
            if ( $check !== '' )
                $git['problems'][] = $check;
            else if ( !file_exists( $repo->checkout() . '/.git' ) )
                $git['problems'][] = $repo->checkout() . ' is not a publishing checkout yet: publish the target first';
            else
                $committed = $repo->rollback( $to, $options['apply'], $message, $git );
            $this->merge( $report, $git );
            foreach ( array( 'from_tag', 'files' ) as $key )
                if ( isset( $git[$key] ) )
                    $report[$key] = $git[$key];
            if ( !$options['apply'] && !$git['problems'] )
            {
                $report['state'] = 'planned';
                return $this->done( $report );
            }
            $report['state'] = $git['problems'] ? expStaticStatus::FAILED : ( $committed ? expStaticStatus::DONE : expStaticStatus::UNCHANGED );
            $after = expStaticManifest::load( $this->checkoutDirectory() . '/' . $prefix );
            if ( $committed )
            {
                if ( $before !== null )
                    $before->save( $this->checkoutDirectory() . '/' . $prefix, expStaticManifest::PREVIOUS_FILE );
                if ( $after !== null )
                    $report['counts'] = array( 'pages' => $after->count( 'page' ), 'files' => count( $after->entries() ), 'bytes' => $after->bytes() ) + $report['counts'];
                $pushed = array();
                $report['push_state'] = $repo->push( $pushed );
                $this->merge( $report, $pushed );
                if ( $report['push_state'] === expStaticRepository::PUSHED )
                    expStaticStatus::setPushState( $this->options['db'], array_column( expStaticStatus::pending( $this->options['db'], $this->target->id() ), 'run_id' ),
                                                   expStaticRepository::PUSHED );
            }
            if ( $options['apply'] )
            {
                $this->record( $report, expStaticStatus::ROLLBACK, $started );
                if ( $committed && $after !== null )
                    expStaticStatus::writePages( $this->options['db'], $this->target->id(), $after );
            }
        }
        finally
        {
            $this->credentials()->cleanup();
            $lock->release();
        }
        return $this->done( $report );
    }

    /**
     * Pushes the target again when runs of it wait for a push.
     *
     * @return array the report: mode push, tried (runs), push_state, messages, errors
     */
    public function retry()
    {
        $report = $this->report( 'push', '' );
        $report['runs'] = array();
        $pending = expStaticStatus::pending( $this->options['db'], $this->target->id() );
        $report['runs'] = array_column( $pending, 'run_id' );
        $repo = $this->repository();
        if ( !$pending && !( $this->options['db'] === null && file_exists( $repo->checkout() . '/.git' ) ) )
        {
            $report['messages'][] = 'no run of ' . $this->target->id() . ' waits for a push';
            return $this->done( $report );
        }
        $problem = '';
        $lock = expStaticLock::acquire( $this->workDirectory(), $this->target->id(), 'push', $problem );
        if ( $lock === null )
        {
            $report['errors'][] = $problem;
            return $this->done( $report );
        }
        try
        {
            $check = $repo->checkoutProblem();
            if ( $check !== '' || !$repo->hasHead() )
            {
                $report['errors'][] = $check !== '' ? $check : $repo->checkout() . ' has no commit to push';
                return $this->done( $report );
            }
            $pushed = array();
            $report['push_state'] = $repo->push( $pushed );
            $this->merge( $report, $pushed );
            if ( $report['push_state'] === expStaticRepository::PUSHED )
                expStaticStatus::setPushState( $this->options['db'], $report['runs'], expStaticRepository::PUSHED );
            $this->event( 'system.staticpublish.publish', array( 'object' => array( 'type' => 'static_target', 'id' => $this->target->id() ), 'verb' => 'retry',
                                                                'result' => $report['push_state'] === expStaticRepository::PUSHED ? 'success' : 'failure',
                                                                'reason' => $report['push_state'] === expStaticRepository::PUSHED ? '' : $report['push_state'],
                                                                'x' => array( 'runs' => $report['runs'], 'push_state' => $report['push_state'],
                                                                              'remote' => $report['remote'], 'credential' => $report['credential'] ) ) );
        }
        finally
        {
            $this->credentials()->cleanup();
            $lock->release();
        }
        return $this->done( $report );
    }

    /**
     * The commit message of a publish without --message. Pure.
     *
     * @param expStaticTarget $target
     * @param expStaticManifest|null $manifest
     * @param string $runId
     * @return string
     */
    public static function message( expStaticTarget $target, $manifest, $runId )
    {
        $meta = $manifest !== null ? $manifest->meta() : array();
        $subject = 'Publish ' . $target->id() . ( $manifest !== null ? sprintf( ': %d pages, %d files', $manifest->count( 'page' ), count( $manifest->entries() ) ) : '' );
        return $subject . "\n\nSite access: " . implode( ", ", $target->siteAccesses() ) . "\nRun: " . $runId
             . ( isset( $meta['built_at'] ) ? "\nBuilt: " . $meta['built_at'] : '' ) . "\nPublished by Exponential static publishing.\n";
    }

    /** Adds a repository result to the report: messages, problems as errors (or as warnings), the commit fields. */
    protected function merge( array &$report, array $git, $problemsAs = 'errors' )
    {
        $report['messages'] = array_merge( $report['messages'], isset( $git['messages'] ) ? $git['messages'] : array() );
        $report['warnings'] = array_merge( $report['warnings'], isset( $git['warnings'] ) ? $git['warnings'] : array() );
        $report[$problemsAs] = array_merge( $report[$problemsAs], isset( $git['problems'] ) ? $git['problems'] : array() );
        foreach ( array( 'commit_id', 'tag_name', 'tag_number' ) as $key )
            if ( !empty( $git[$key] ) )
                $report[$key] = $git[$key];
        foreach ( array( 'added', 'changed', 'removed' ) as $key )
            if ( isset( $git[$key] ) )
                $report['counts'][$key] = (int)$git[$key];
    }

    /** Writes the run row and the audit event. */
    protected function record( array $report, $kind, $started )
    {
        $message = implode( '; ', array_merge( $report['errors'], array_slice( $report['messages'], -3 ) ) );
        expStaticStatus::writeRun( $this->options['db'], array(
            'run_id' => $report['run_id'], 'target' => $this->target->id(), 'kind' => $kind, 'state' => $report['state'],
            'commit_id' => $report['commit_id'], 'tag_name' => $report['tag_name'], 'push_state' => $report['push_state'], 'run_by' => $this->by(),
            'started_at' => $started, 'finished_at' => time(), 'message' => $message ) + $report['counts'] );
        $ok = $report['state'] !== expStaticStatus::FAILED;
        $this->event( $kind === expStaticStatus::ROLLBACK ? 'system.staticpublish.rollback' : 'system.staticpublish.publish', array(
            'object' => array( 'type' => 'static_target', 'id' => $this->target->id() ), 'verb' => $kind, 'result' => $ok ? 'success' : 'failure',
            'reason' => $ok ? '' : (string)reset( $report['errors'] ),
            'x' => array( 'run_id' => $report['run_id'], 'state' => $report['state'], 'commit' => $report['commit_id'], 'tag' => $report['tag_name'],
                          'push_state' => $report['push_state'], 'remote' => $report['remote'], 'credential' => $report['credential'],
                          'from_tag' => isset( $report['from_tag'] ) ? $report['from_tag'] : '' ) + $report['counts'] ) );
        $this->notify( $report );
    }

    /** The e-mail and webhook of the target after a run, when the notify option asks for them; never a credential. */
    protected function notify( array $report )
    {
        if ( !$this->options['notify'] )
            return;
        $notifier = $this->options['notify'] instanceof expStaticNotifier ? $this->options['notify'] : new expStaticNotifier();
        $credentials = $this->credentials();
        foreach ( array( 'messages', 'errors', 'warnings' ) as $key )
            $report[$key] = array_values( array_map( array( $credentials, 'redact' ), (array)$report[$key] ) );
        try
        {
            $notifier->notify( $this->target, $report );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'Static publishing: the notice could not be sent: ' . $e->getMessage(), __METHOD__ );
        }
    }

    /** An audit event of this publisher: to the audit option's callable, else to the audit. */
    protected function event( $name, array $data )
    {
        if ( is_callable( $this->options['audit'] ) )
            call_user_func( $this->options['audit'], $name, $data );
        else
            self::audit( $name, $data );
    }

    /** An audit event, when the audit is there. */
    public static function audit( $name, array $data )
    {
        try
        {
            if ( class_exists( 'expAudit' ) )
                expAudit::event( $name, $data );
        }
        catch ( Throwable $e )
        {
        }
    }

    /** The report with its exit code, every text redacted. */
    protected function done( array $report )
    {
        $credentials = $this->credentials();
        foreach ( array( 'messages', 'errors', 'warnings' ) as $key )
            $report[$key] = array_values( array_map( array( $credentials, 'redact' ), $report[$key] ) );
        $report['exit'] = $report['errors'] ? 1 : 0;
        return $report;
    }
}
