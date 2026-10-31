<?php
/**
 * File containing the expMigrationRepository class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The migrations repository: where the record of the runs is kept under version control (migration.ini
 * [RepositorySettings]). The status mirror (status/<environment>.json) and the run logs (runs/<run-id>.log,
 * expMigrationStatusMirror) are written into its folder Path; after every run, the failed ones too, they are
 * committed, the commit is tagged and pushed:
 *
 *   Mode=site      Path is a folder of the installation's own checkout; only the run's two files are committed
 *                  (git commit -- <files>: whatever else is staged there stays as it is)
 *   Mode=separate  Path is a git repository of its own
 *
 * One run is one revision: a commit and a tag (TagPattern, default migrations/<environment>/<run-id>). A tag that
 * exists is never moved or replaced. The push sends the branch and the tag to Remote; a push that fails leaves the
 * commit and tag local, the run's push_state says "pending", and retryPushes() (the cronjob part migrationpush)
 * tries again.
 *
 * Every git command goes through expUpgradeGit::git() (git_manager's GitManager::git(): no shell, a time limit).
 * Without git_manager nothing is committed and the report says so. A checkout that belongs to another system user
 * than the process is left alone (the files git would write would belong to the wrong user).
 *
 * The push states of exp_migration_run.push_state: pushed, pending (to try again), disabled (Commit or Push
 * disabled, or --no-push), no-remote (Remote empty or not a remote of the repository), none (nothing was committed).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRepository
{
    const SITE = 'site';
    const SEPARATE = 'separate';

    const PUSHED = 'pushed';
    const PENDING = 'pending';
    const DISABLED = 'disabled';
    const NO_REMOTE = 'no-remote';
    const NONE = 'none';

    /** @var string */
    protected $root;

    /** @var array */
    protected $settings;

    /**
     * @param string $root the installation's root
     * @param array $settings as defaults()
     */
    public function __construct( $root, array $settings = array() )
    {
        $this->root = rtrim( (string)$root, '/' );
        $this->settings = $settings + self::defaults();
    }

    /**
     * @return array mode, path, remote, branch, commit, tag, push (bool), tag-pattern
     */
    public static function defaults()
    {
        return array( 'mode' => self::SITE, 'path' => 'migrations', 'remote' => 'origin', 'branch' => '', 'commit' => true, 'tag' => true,
                      'push' => true, 'tag-pattern' => 'migrations/<environment>/<run-id>' );
    }

    /**
     * The repository of migration.ini [RepositorySettings] with the options of a run over it: no-commit (no commit,
     * no tag, no push), no-tag, no-push; root (the installation's, eZSys::rootDir() when not given).
     *
     * @param array $options
     * @param eZINI|null $ini
     * @return expMigrationRepository
     */
    public static function fromSettings( array $options = array(), $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $settings = self::defaults();
        $get = function ( $name ) use ( $ini ) {
            return $ini->hasVariable( 'RepositorySettings', $name ) ? trim( (string)$ini->variable( 'RepositorySettings', $name ) ) : null;
        };
        foreach ( array( 'mode' => 'Mode', 'path' => 'Path', 'remote' => 'Remote', 'branch' => 'Branch', 'tag-pattern' => 'TagPattern' ) as $key => $name )
            if ( $get( $name ) !== null )
                $settings[$key] = $get( $name );
        foreach ( array( 'commit' => 'Commit', 'tag' => 'Tag', 'push' => 'Push' ) as $key => $name )
            if ( $get( $name ) !== null )
                $settings[$key] = $get( $name ) !== 'disabled';
        if ( !empty( $options['no-commit'] ) )
            $settings['commit'] = $settings['tag'] = $settings['push'] = false;
        if ( !empty( $options['no-tag'] ) )
            $settings['tag'] = false;
        if ( !empty( $options['no-push'] ) )
            $settings['push'] = false;
        $root = isset( $options['root'] ) && $options['root'] !== '' ? $options['root'] : eZSys::rootDir();
        return new self( $root, $settings );
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function setting( $name )
    {
        return isset( $this->settings[$name] ) ? $this->settings[$name] : null;
    }

    /** @return string site or separate */
    public function mode()
    {
        return $this->settings['mode'] === self::SEPARATE ? self::SEPARATE : self::SITE;
    }

    /** @return string the folder of the status files and run logs (Path, absolute) */
    public function directory()
    {
        $path = rtrim( (string)$this->settings['path'], '/' );
        if ( $path === '' )
            $path = 'migrations';
        return $path[0] === '/' ? $path : $this->root . '/' . $path;
    }

    /** @return string the working tree git runs in: the installation's root (site) or the folder (separate) */
    public function top()
    {
        return $this->mode() === self::SEPARATE ? $this->directory() : $this->root;
    }

    /**
     * The name of a run's tag. Pure.
     *
     * @param string $pattern with <environment>, <run-id>, <date>
     * @param string $environment '' is "default"
     * @param string $runId
     * @param int|null $time for <date>
     * @return string
     */
    public static function tagName( $pattern, $environment, $runId, $time = null )
    {
        $environment = strtolower( trim( (string)$environment ) );
        return strtr( (string)$pattern, array(
            '<environment>' => $environment !== '' ? $environment : expMigrationStatusMirror::DEFAULT_ENVIRONMENT,
            '<run-id>' => (string)$runId,
            '<date>' => date( 'Ymd', $time === null ? time() : (int)$time ),
        ) );
    }

    /**
     * Whether a name can be a tag (the rules of git check-ref-format). Pure.
     *
     * @param string $name
     * @return bool
     */
    public static function validTagName( $name )
    {
        $name = (string)$name;
        if ( $name === '' || strlen( $name ) > 150 || $name[0] === '/' || $name[0] === '-' || substr( $name, -1 ) === '/' || substr( $name, -1 ) === '.'
             || substr( $name, -5 ) === '.lock' || $name === '@' )
            return false;
        if ( preg_match( '#\.\.|//|@\{|[\x00-\x20\x7f~^:?*\[\\\\]#', $name ) )
            return false;
        foreach ( explode( '/', $name ) as $part )
            if ( $part === '' || $part[0] === '.' || substr( $part, -5 ) === '.lock' )
                return false;
        return true;
    }

    /**
     * The commit message of a run. Pure.
     *
     * @param array $report the runner's report
     * @return string
     */
    public static function commitMessage( array $report )
    {
        $failed = isset( $report['exit'] ) && (int)$report['exit'] !== expUpgrade::EXIT_OK;
        $environment = isset( $report['environment'] ) && $report['environment'] !== '' ? $report['environment'] : expMigrationStatusMirror::DEFAULT_ENVIRONMENT;
        $summary = isset( $report['summary'] ) ? (array)$report['summary'] : array();
        $applied = isset( $summary['applied'] ) ? (int)$summary['applied'] : 0;
        $title = 'Updated: Migration run ' . $report['run_id'] . ' in ' . $environment . ( $failed ? ' failed' : ": $applied applied" );
        $lines = array();
        foreach ( isset( $report['migrations'] ) ? (array)$report['migrations'] : array() as $entry )
            if ( in_array( $entry['action'], array( 'applied', 'failed', 'restored', 'accepted' ), true ) )
                $lines[] = '- ' . $entry['action'] . ' ' . ( $entry['source'] !== '' ? $entry['source'] . ':' : '' ) . $entry['id'];
        if ( !empty( $report['restore']['state'] ) )
            $lines[] = 'Restore: ' . $report['restore']['state'] . ( !empty( $report['restore']['caption'] ) ? ' (caption ' . $report['restore']['caption'] . ')' : '' );
        return $title . ( $lines ? "\n\n" . implode( "\n", $lines ) : '' ) . "\n";
    }

    /**
     * Commits, tags and pushes the record of a run (its status file and run log).
     *
     * @param expMigrationContext $context
     * @param array $report the runner's report
     * @return array commit_id, tag_name, push_state, messages (what happened), problems (what went wrong)
     */
    public function record( expMigrationContext $context, array $report )
    {
        $result = array( 'commit_id' => '', 'tag_name' => '', 'push_state' => self::NONE, 'messages' => array(), 'problems' => array() );
        if ( !$this->settings['commit'] )
        {
            $result['push_state'] = self::DISABLED;
            $result['messages'][] = 'the record of the run is not committed ([RepositorySettings] Commit=disabled or --no-commit)';
            return $result;
        }
        $top = $this->top();
        $problem = $this->checkout( $top );
        if ( $problem !== '' )
        {
            $result['problems'][] = $problem . ': the record of the run is not committed';
            return $result;
        }
        $branch = $this->branch( $top, $result['problems'] );
        if ( $branch === '' )
            return $result;

        $files = $this->files( $context );
        if ( !$files )
        {
            $result['messages'][] = 'nothing to commit: the run wrote no status file or log';
            return $result;
        }
        $add = $this->git( $top, array_merge( array( 'add', '--' ), $files ) );
        if ( !$add['ok'] )
        {
            $result['problems'][] = 'git add failed for ' . implode( ', ', $files ) . ' (ignored by .gitignore?)';
            return $result;
        }
        $staged = $this->git( $top, array_merge( array( 'diff', '--cached', '--quiet', '--' ), $files ) );
        if ( $staged['ok'] )
        {
            $result['messages'][] = 'nothing to commit: the status file and log are as committed';
            return $result;
        }
        $commit = $this->git( $top, array_merge( $this->identity( $top ), array( 'commit', '--quiet', '-m', self::commitMessage( $report ), '--' ), $files ) );
        if ( !$commit['ok'] )
        {
            $result['problems'][] = 'git commit failed (exit ' . $commit['exit'] . '): the record of the run stays uncommitted in ' . $top;
            return $result;
        }
        $head = $this->git( $top, array( 'rev-parse', '--verify', 'HEAD' ) );
        $result['commit_id'] = $head['ok'] ? trim( $head['output'] ) : '';
        $result['messages'][] = 'committed ' . substr( $result['commit_id'], 0, 10 ) . ' in ' . $top . ' (' . $branch . ')';

        if ( $this->settings['tag'] && $result['commit_id'] !== '' )
        {
            $name = self::tagName( $this->settings['tag-pattern'], $context->environment(), $context->runId() );
            if ( !self::validTagName( $name ) )
                $result['problems'][] = "not a tag name: $name ([RepositorySettings] TagPattern)";
            else if ( $this->git( $top, array( 'rev-parse', '--verify', '--quiet', 'refs/tags/' . $name ) )['ok'] )
                $result['problems'][] = "the tag $name exists already: it is never moved, the commit stays untagged";
            else
            {
                $tag = $this->git( $top, array_merge( $this->identity( $top ), array( 'tag', '-a', $name, '-m', strtok( self::commitMessage( $report ), "\n" ), $result['commit_id'] ) ) );
                if ( $tag['ok'] )
                {
                    $result['tag_name'] = $name;
                    $result['messages'][] = "tagged $name";
                }
                else
                    $result['problems'][] = "git tag $name failed (exit {$tag['exit']})";
            }
        }

        if ( !$this->settings['push'] )
        {
            $result['push_state'] = self::DISABLED;
            $result['messages'][] = 'not pushed ([RepositorySettings] Push=disabled or --no-push)';
            return $result;
        }
        $result['push_state'] = $this->push( $top, $branch, $result['tag_name'], $result );
        $context->audit( 'system.migration.push', array( 'object' => array( 'type' => 'migration_run', 'id' => $context->runId() ), 'verb' => 'push',
                                                         'result' => $result['push_state'] === self::PUSHED ? 'success' : 'failure',
                                                         'reason' => $result['push_state'] === self::PUSHED ? '' : $result['push_state'],
                                                         'x' => array( 'remote' => (string)$this->settings['remote'], 'branch' => $branch, 'tag' => $result['tag_name'],
                                                                       'commit' => $result['commit_id'], 'push_state' => $result['push_state'] ) ) );
        return $result;
    }

    /**
     * Pushes a branch and a tag to Remote.
     *
     * @param string $top
     * @param string $branch
     * @param string $tag '' for none
     * @param array $result messages and problems are added here
     * @return string pushed, pending or no-remote
     */
    protected function push( $top, $branch, $tag, array &$result )
    {
        $remote = (string)$this->settings['remote'];
        $remotes = $this->git( $top, array( 'remote' ) );
        if ( $remote === '' || !$remotes['ok'] || !in_array( $remote, array_map( 'trim', explode( "\n", $remotes['output'] ) ), true ) )
        {
            $result['messages'][] = $remote === '' ? 'not pushed: no remote ([RepositorySettings] Remote)' : "not pushed: the repository has no remote $remote";
            return self::NO_REMOTE;
        }
        $refs = array( "refs/heads/$branch:refs/heads/$branch" );
        if ( $tag !== '' )
            $refs[] = "refs/tags/$tag:refs/tags/$tag";
        $push = $this->git( $top, array_merge( array( 'push', '--quiet', $remote ), $refs ), 300 );
        if ( $push['ok'] )
        {
            $result['messages'][] = "pushed $branch" . ( $tag !== '' ? " and $tag" : '' ) . " to $remote";
            return self::PUSHED;
        }
        $result['problems'][] = "the push to $remote failed (exit {$push['exit']}): the commit stays local and is pushed again by the cronjob part migrationpush";
        return self::PENDING;
    }

    /**
     * Pushes again what the runs whose push failed left local (push_state pending), oldest first, and records the
     * new push state. The cronjob part migrationpush runs it.
     *
     * @param eZDBInterface $db
     * @param int $limit runs at most
     * @return array tried, pushed, pending (counts), messages
     */
    public function retryPushes( $db, $limit = 20 )
    {
        $out = array( 'tried' => 0, 'pushed' => 0, 'pending' => 0, 'messages' => array() );
        $runs = array_reverse( array_filter( expMigrationRecord::runs( $db, 0 ), function ( $run ) {
            return $run['push_state'] === expMigrationRepository::PENDING;
        } ) );
        if ( !$runs )
            return $out;
        if ( !$this->settings['push'] )
        {
            $out['messages'][] = count( $runs ) . ' run(s) wait for a push, but [RepositorySettings] Push is disabled';
            return $out;
        }
        $top = $this->top();
        $problem = $this->checkout( $top );
        if ( $problem !== '' )
        {
            $out['messages'][] = $problem;
            return $out;
        }
        $problems = array();
        $branch = $this->branch( $top, $problems );
        if ( $branch === '' )
        {
            $out['messages'] = array_merge( $out['messages'], $problems );
            return $out;
        }
        foreach ( array_slice( array_values( $runs ), 0, max( 1, (int)$limit ) ) as $run )
        {
            $out['tried']++;
            $result = array( 'messages' => array(), 'problems' => array() );
            $tag = $run['tag_name'] !== '' && $this->git( $top, array( 'rev-parse', '--verify', '--quiet', 'refs/tags/' . $run['tag_name'] ) )['ok'] ? $run['tag_name'] : '';
            $state = $this->push( $top, $branch, $tag, $result );
            $run['push_state'] = $state;
            expMigrationRecord::writeRun( $db, $run );
            $out[$state === self::PUSHED ? 'pushed' : 'pending']++;
            $out['messages'] = array_merge( $out['messages'], array_map( function ( $m ) use ( $run ) { return $run['run_id'] . ': ' . $m; },
                                                                         array_merge( $result['messages'], $result['problems'] ) ) );
            expUpgrade::audit( 'system.migration.push', array( 'object' => array( 'type' => 'migration_run', 'id' => $run['run_id'] ), 'verb' => 'retry',
                                                               'result' => $state === self::PUSHED ? 'success' : 'failure',
                                                               'x' => array( 'remote' => (string)$this->settings['remote'], 'branch' => $branch, 'tag' => $tag,
                                                                             'push_state' => $state ) ) );
            if ( $state === self::NO_REMOTE )
                break;
        }
        return $out;
    }

    /**
     * The run's files relative to the working tree, those that exist: the status file of its environment and its log.
     *
     * @param expMigrationContext $context
     * @return string[]
     */
    public function files( expMigrationContext $context )
    {
        $dir = $this->directory();
        $top = rtrim( $this->top(), '/' );
        if ( $dir !== $top && strpos( $dir, $top . '/' ) !== 0 )
            return array();
        $prefix = $dir === $top ? '' : substr( $dir, strlen( $top ) + 1 ) . '/';
        $mirror = new expMigrationStatusMirror( $dir );
        $out = array();
        foreach ( array( $mirror->statusFile( $context->environment() ), $context->logFile() !== '' ? $mirror->logFile( $context->runId() ) : '' ) as $file )
            if ( $file !== '' && is_file( $file ) )
                $out[] = $prefix . substr( $file, strlen( $dir ) + 1 );
        return $out;
    }

    /**
     * Why git cannot be used in a working tree, '' when it can.
     *
     * @param string $top
     * @return string
     */
    protected function checkout( $top )
    {
        if ( !$this->gitAvailable() )
            return 'git_manager is not active';
        if ( !file_exists( rtrim( $top, '/' ) . '/.git' ) )
            return "$top is not a git repository ([RepositorySettings] Mode=" . $this->mode() . ')';
        return expUpgradeComposer::ownerProblem( rtrim( $top, '/' ) . '/.git' );
    }

    /**
     * The branch the run commits on, '' (with a problem) when the checkout is on no branch or on another than
     * [RepositorySettings] Branch.
     *
     * @param string $top
     * @param string[] $problems
     * @return string
     */
    protected function branch( $top, array &$problems )
    {
        $current = $this->git( $top, array( 'rev-parse', '--abbrev-ref', 'HEAD' ) );
        $current = $current['ok'] ? trim( $current['output'] ) : '';
        if ( $current === '' || $current === 'HEAD' )
        {
            $problems[] = "$top is on no branch: the record of the run is not committed";
            return '';
        }
        $want = (string)$this->settings['branch'];
        if ( $want !== '' && $want !== $current )
        {
            $problems[] = "$top is on the branch $current, not $want ([RepositorySettings] Branch): the record of the run is not committed";
            return '';
        }
        return $current;
    }

    /**
     * The committer for a repository that names none (git config user.email empty): Exponential migrations.
     *
     * @param string $top
     * @return string[] arguments before the git command
     */
    protected function identity( $top )
    {
        $email = $this->git( $top, array( 'config', 'user.email' ) );
        if ( $email['ok'] && trim( $email['output'] ) !== '' )
            return array();
        return array( '-c', 'user.name=Exponential migrations', '-c', 'user.email=migrations@' . ( preg_replace( '/[^A-Za-z0-9.-]/', '', (string)php_uname( 'n' ) ) ?: 'localhost' ) );
    }

    /** @return bool */
    protected function gitAvailable()
    {
        return expUpgradeGit::available();
    }

    /**
     * expUpgradeGit::git().
     *
     * @param string $path
     * @param string[] $arguments
     * @param int $seconds
     * @return array ok, exit, output
     */
    protected function git( $path, array $arguments, $seconds = 120 )
    {
        return expUpgradeGit::git( $path, $arguments, $seconds );
    }
}
