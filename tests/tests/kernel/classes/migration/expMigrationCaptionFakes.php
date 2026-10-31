<?php
/**
 * The fakes of expMigrationCaptionTest: a run context with the run row in memory, a caption store in a folder of the
 * test (no database dump, no git_manager), a rollback that only records what it was asked, and git answered by a
 * closure.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerFakes.php';

/** The context of expMigrationRunnerTest with runRecord() and the root of the test. */
class expMigrationCaptionTestContext extends expMigrationRunnerTestContext
{
    public function runRecord()
    {
        return $this->runs ? $this->runs[count( $this->runs ) - 1] : null;
    }

    public function root()
    {
        return (string)$this->option( 'root' );
    }
}

/**
 * A caption store in a folder: takeCaption() makes <store>/capN with upgrade/caption.json and upgrade/checksums.json.
 * Public: taken (kind, label), restores (id, options), gitCalls, fail (kinds that fail), marker (what databaseMarker()
 * answers), gitFn (null: git_manager is not active), restoreExit.
 */
class expMigrationCaptionFake extends expMigrationCaption
{
    public $store;
    public $taken = array();
    public $restores = array();
    public $gitCalls = array();
    public $fail = array();
    public $marker = 'database-1';
    public $gitFn = null;
    public $restoreExit = 0;
    public $existing = array();

    public function __construct( expMigrationContext $context, $store, array $settings = array() )
    {
        parent::__construct( $context, $settings );
        $this->store = $store;
    }

    protected function takeCaption( $kind, $label )
    {
        $this->context->calls[] = "caption-$kind";
        $this->taken[] = array( $kind, $label );
        if ( in_array( $kind, $this->fail, true ) )
            return array( 'ok' => false, 'id' => '', 'dir' => '', 'source' => 'exp:upgrade', 'label' => $label, 'kind' => $kind,
                          'messages' => array( 'the database dump failed: disk full' ) );
        $id = 'cap' . count( $this->taken );
        $dir = $this->store . '/' . $id;
        mkdir( $dir . '/upgrade', 0700, true );
        file_put_contents( $dir . '/database.sqlite', 'dump' );
        file_put_contents( $dir . '/upgrade/caption.json', json_encode( array( 'kind' => $kind, 'label' => $label ) ) );
        file_put_contents( $dir . '/upgrade/checksums.json', json_encode( expUpgradeCaption::checksums( $dir ) ) );
        array_unshift( $this->existing, array( 'id' => $id, 'dir' => $dir, 'source' => 'exp:upgrade', 'label' => $label, 'kind' => $kind,
                                               'upgrade_part' => true, 'created' => time() ) );
        return array( 'ok' => true, 'id' => $id, 'dir' => $dir, 'source' => 'exp:upgrade', 'label' => $label, 'kind' => $kind, 'messages' => array() );
    }

    protected function captions()
    {
        return $this->existing;
    }

    protected function rollback( $captionId, array $options )
    {
        $this->context->calls[] = "restore-$captionId";
        $this->restores[] = array( $captionId, $options );
        return array( 'exit' => $this->restoreExit, 'errors' => $this->restoreExit ? array( 'database: loading the dump failed' ) : array(),
                      'rollback' => array( 'parts' => array( 'database' => array( 'load the dump' ), 'settings' => array( 'copy back' ) ) ) );
    }

    protected function databaseMarker()
    {
        return $this->marker;
    }

    protected function gitAvailable()
    {
        return $this->gitFn !== null;
    }

    protected function git( $path, array $arguments, $seconds = 120 )
    {
        $this->gitCalls[] = array( $path, $arguments );
        return call_user_func( $this->gitFn, $path, $arguments );
    }
}

/** A repository whose git answers a closure (gitFn), the calls kept in gitCalls. */
class expMigrationRepositoryFake extends expMigrationRepository
{
    public $gitFn;
    public $gitCalls = array();

    protected function gitAvailable()
    {
        return $this->gitFn !== null;
    }

    protected function git( $path, array $arguments, $seconds = 120 )
    {
        $this->gitCalls[] = $arguments;
        return call_user_func( $this->gitFn, $path, $arguments );
    }

    /** @return string[] the git subcommands called, in order (the -c options left out) */
    public function commands()
    {
        $out = array();
        foreach ( $this->gitCalls as $args )
        {
            while ( $args && $args[0] === '-c' )
                $args = array_slice( $args, 2 );
            $out[] = $args[0];
        }
        return $out;
    }
}

/**
 * git without git_manager, for the git round trip where GitManager is not installed (CI): the same contract as
 * GitManager::git() (no shell, the arguments as given, ok/exit/output).
 */
class expMigrationTestGit
{
    public static function run( $path, array $arguments )
    {
        $process = proc_open( array_merge( array( 'git' ), array_map( 'strval', $arguments ) ),
                              array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $path,
                              array( 'GIT_TERMINAL_PROMPT' => '0', 'LC_ALL' => 'C', 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin',
                                     'HOME' => (string)( getenv( 'HOME' ) ?: $path ) ) );
        if ( !is_resource( $process ) )
            return array( 'ok' => false, 'exit' => 1, 'output' => '' );
        fclose( $pipes[0] );
        $output = (string)stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        stream_get_contents( $pipes[2] );
        fclose( $pipes[2] );
        $exit = proc_close( $process );
        return array( 'ok' => $exit === 0, 'exit' => $exit, 'output' => $output );
    }
}

/** A repository that runs real git: through git_manager when it is installed, else expMigrationTestGit. */
class expMigrationRepositoryRealGit extends expMigrationRepository
{
    protected function gitAvailable()
    {
        return true;
    }

    protected function git( $path, array $arguments, $seconds = 120 )
    {
        return expUpgradeGit::available() ? parent::git( $path, $arguments, $seconds ) : expMigrationTestGit::run( $path, $arguments );
    }
}
