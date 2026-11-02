<?php
/**
 * The permanent contract of the exp:static commands (static publishing): their names, their options and arguments,
 * the exit codes and the keys of the machine output exp-static/1. Options, arguments and keys may be added (extend
 * the snapshot); nothing here may ever be renamed or removed. Needs no installation.
 *
 *  SC-01 every command and its option configuration (snapshot), and the arguments
 *  SC-02 exit codes and the format name
 *  SC-03 the keys of a build report, of the manifest and the keys the commands add (command, schema)
 *  SC-04 every command has its entry point in bin/php with the @alias the console dispatches, and a description
 *  SC-05 the literal option configuration, arguments and help of each command (what exp:help:examples reads) are
 *        the contract's
 *  SC-06 the text output of a build report names the counts, the diff and the link check (buildLines())
 *  SC-07 the shipped settings: [StaticPublishSettings] and the [Target_example] template hold every setting of a
 *        target, with the defaults of expStaticTarget::DEFAULTS
 *  SC-08 the publish report keys, push and run states, tag names, tables, the shipped Push and Commit* settings, the
 *        cronjob part staticpublish, the upgrade step and the audit events
 *  SC-09 the text output of a publish report (gitLines())
 *  SC-10 a job started from the administration (--siteaccess=admin, the script option: whose settings are read) is
 *        accepted: no exp:static command declares --siteaccess itself, and every option a job passes is declared
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticCommandContractTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    /** The snapshot. Add, never change or remove. */
    const COMMANDS = array(
        'exp:static:targets' => '[format:][export:]',
        'exp:static:build' => '[full][paths:*][nodes:*][dry-run][report:][format:][crawl-url:][max-pages:][keep-staging][login:][run-id:][progress:][only-siteaccess:]',
        'exp:static:diff' => '[format:][verify][check-links][mount:*]',
        'exp:static:serve' => '[port:][host:][base-path:][format:]',
        'exp:static:publish' => '[build][nodes:*][message:][no-push][format:][login:][run-id:][progress:]',
        'exp:static:rollback' => '[apply][message:][no-push][format:][login:][run-id:][progress:]',
        'exp:static:push' => '[format:]',
        'exp:static:keys' => '[generate][replace][format:]',
        'exp:static:status' => '[limit:][format:]',
        'exp:static:github' => '[create-repo][public][private][pages:][domain:][no-https][plan][client-id:][secret:][format:]',
        'exp:static:dns' => '[owner:][format:]',
    );

    /** The arguments. Add, never change or remove. */
    const ARGUMENTS = array(
        'exp:static:build' => '[target]',
        'exp:static:diff' => '[target]',
        'exp:static:serve' => '[target]',
        'exp:static:publish' => '[target]',
        'exp:static:rollback' => '[target][to]',
        'exp:static:push' => '[target]',
        'exp:static:keys' => '[target]',
        'exp:static:status' => '[target]',
        'exp:static:github' => '[action][target]',
        'exp:static:dns' => '[domain]',
    );

    /** command => entry point in bin/php */
    const ENTRY = array(
        'exp:static:targets' => 'expstatictargets',
        'exp:static:build' => 'expstaticbuild',
        'exp:static:diff' => 'expstaticdiff',
        'exp:static:serve' => 'expstaticserve',
        'exp:static:publish' => 'expstaticpublish',
        'exp:static:rollback' => 'expstaticrollback',
        'exp:static:push' => 'expstaticpush',
        'exp:static:keys' => 'expstatickeys',
        'exp:static:status' => 'expstaticstatus',
        'exp:static:github' => 'expstaticgithub',
        'exp:static:dns' => 'expstaticdns',
    );

    /** The keys of a publish, rollback or push report (expStaticPublisher), the push states and run states. Add only. */
    const PUBLISH_KEYS = array( 'mode', 'target', 'run_id', 'directory', 'branch', 'remote', 'credential', 'build', 'state', 'commit_id', 'tag_name',
                                'tag_number', 'push_state', 'counts', 'messages', 'errors', 'warnings', 'exit' );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'upgrade/expupgradestep', 'upgrade/expupgraderegistry', 'upgrade/expupgrade' ) as $f )
            require_once self::$root . "/kernel/classes/$f.php";
        require_once self::$root . '/kernel/private/classes/runnable/runnable.php';
        require_once self::$root . '/kernel/private/classes/runnable/command.php';
        require_once self::$root . '/kernel/private/classes/commands/expstaticbase.php';
    }

    /** @return array option => true of a config string */
    private static function options( $config )
    {
        preg_match_all( '/\[([a-z-]+:?\*?)\]/', $config, $m );
        return array_fill_keys( $m[1], true );
    }

    /** SC-01 */
    public function testCommandsAndOptionsOnlyGrow()
    {
        $now = \Exponential\Command\Kernel\ExpstaticBase::contract();
        foreach ( self::COMMANDS as $command => $config )
        {
            $this->assertArrayHasKey( $command, $now, "the command $command is permanent" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( $option, self::options( $now[$command] ), "$command keeps --$option with the same arity" );
        }
        $help = \Exponential\Command\Kernel\ExpstaticBase::optionHelp();
        foreach ( $now as $command => $config )
        {
            $this->assertArrayHasKey( $command, self::COMMANDS, "a new command $command: add it to the snapshot" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( rtrim( $option, ':*' ), $help, "--$option has a help text" );
        }
        $arguments = \Exponential\Command\Kernel\ExpstaticBase::arguments();
        foreach ( self::ARGUMENTS as $command => $config )
            $this->assertSame( $config, isset( $arguments[$command] ) ? $arguments[$command] : null, "$command keeps its argument $config" );
        foreach ( array_keys( $arguments ) as $command )
            $this->assertArrayHasKey( $command, self::ARGUMENTS, "a new argument of $command: add it to the snapshot" );
    }

    /** SC-02 */
    public function testCodesAndFormat()
    {
        $this->assertSame( 'exp-static/1', expStaticBuild::REPORT );
        $this->assertSame( 'exp-static-manifest/1', expStaticManifest::FORMAT );
        $this->assertSame( '.static-publish/manifest.json', expStaticManifest::FILE );
        $this->assertSame( array( 0, 1, 2, 3, 4 ), array( expUpgrade::EXIT_OK, expUpgrade::EXIT_FAILED, expUpgrade::EXIT_USAGE,
                                                           expUpgrade::EXIT_UNREACHABLE, expUpgrade::EXIT_INTERNAL ) );
    }

    /** SC-03 */
    public function testReportKeys()
    {
        $source = file_get_contents( self::$root . '/kernel/classes/staticpublish/expstaticbuild.php' );
        foreach ( array( 'schema', 'mode', 'scope', 'target', 'siteaccess', 'run_id', 'started_at', 'duration_ms', 'directory', 'links', 'counts', 'diff',
                         'pages', 'refused', 'skipped', 'files_failed', 'link_check', 'limits', 'applied', 'errors', 'warnings', 'exit' ) as $key )
            $this->assertStringContainsString( "'$key' =>", $source, "report key $key" );
        foreach ( array( 'pages', 'pages_refused', 'pages_skipped', 'pages_excluded', 'files', 'storage', 'files_failed', 'extras', 'links_rewritten',
                         'links_live', 'links_external', 'site_broken_links', 'bytes', 'stripped' ) as $key )
            $this->assertStringContainsString( "'$key' =>", $source, "count $key" );
        $manifest = file_get_contents( self::$root . '/kernel/classes/staticpublish/expstaticmanifest.php' );
        foreach ( array( 'kind', 'sha256', 'size', 'url', 'node', 'modified', 'format', 'meta', 'files' ) as $key )
            $this->assertStringContainsString( "'$key' =>", $manifest, "manifest key $key" );
        $base = file_get_contents( self::$root . '/kernel/private/classes/commands/expstaticbase.php' );
        $this->assertStringContainsString( "array( 'schema' => \\expStaticBuild::REPORT, 'command' => \$this->name() ) + \$report", $base,
                                           'every command output names the schema and the command' );
    }

    /** SC-04 */
    public function testEntryPoints()
    {
        foreach ( self::ENTRY as $command => $file )
        {
            $path = self::$root . "/bin/php/$file.php";
            $this->assertFileExists( $path );
            $this->assertTrue( is_executable( $path ), "$file.php is executable" );
            $text = file_get_contents( $path );
            $this->assertStringStartsWith( "#!/usr/bin/env php\n", $text );
            $this->assertStringContainsString( "@alias $command\n", $text );
            $class = self::$root . "/kernel/private/classes/commands/$file.php";
            $this->assertFileExists( $class );
            $code = file_get_contents( $class );
            $this->assertMatchesRegularExpression( '/@description \S/', $code );
            $this->assertStringContainsString( "@alias $command\n", $code );
            $this->assertStringContainsString( "return '$command';", $code, "$file names itself $command" );
        }
        $this->assertSame( array_keys( self::COMMANDS ), array_keys( self::ENTRY ), 'every command has its entry point' );
    }

    /** SC-05 */
    public function testLiteralOptionsMatchTheContract()
    {
        require_once self::$root . '/kernel/classes/help/exphelpexamplesgenerator.php';
        $contract = \Exponential\Command\Kernel\ExpstaticBase::contract();
        $arguments = \Exponential\Command\Kernel\ExpstaticBase::arguments();
        $help = \Exponential\Command\Kernel\ExpstaticBase::optionHelp();
        foreach ( self::ENTRY as $command => $file )
        {
            $code = file_get_contents( self::$root . "/kernel/private/classes/commands/$file.php" );
            $this->assertSame( 1, preg_match( "/->startup\\( '([^']+)', '([^']*)'/", $code, $m ), "$file: startup() with a literal configuration" );
            $this->assertSame( $contract[$command], $m[1], "$command: the literal option configuration is the contract's" );
            $this->assertSame( isset( $arguments[$command] ) ? $arguments[$command] : '', $m[2], "$command: the literal arguments are the contract's" );
            $parsed = expHelpExamplesGenerator::scriptHelp( $code );
            $this->assertNotSame( '', $parsed['description'], "$command: a literal description" );
            foreach ( $parsed['options'] as $name => $option )
                $this->assertSame( $help[$name], $option['help'], "$command --$name: the help text of optionHelp()" );
            $this->assertCount( count( self::options( $contract[$command] ) ), $parsed['options'], "$command: every option has its help" );
            $this->assertStringNotContainsString( 'eZ Publish', $parsed['description'] );
        }
    }

    /** SC-06 */
    public function testBuildLines()
    {
        $report = array( 'mode' => 'build', 'target' => 'github-public', 'siteaccess' => 'site', 'scope' => 'full', 'run_id' => '20261101-120000-a1b2c3',
                         'duration_ms' => 12345, 'directory' => 'var/site/static-publish/github-public', 'applied' => true,
                         'counts' => array( 'pages' => 120, 'pages_refused' => 0, 'pages_skipped' => 2, 'pages_excluded' => 1, 'files' => 300, 'storage' => 500,
                                            'files_failed' => 1, 'extras' => 5, 'links_rewritten' => 9000, 'links_live' => 40, 'links_external' => 12,
                                            'site_broken_links' => 0, 'bytes' => 52428800, 'stripped' => array( 'debug' => 120 ) ),
                         'diff' => array( 'added' => 10, 'changed' => 3, 'removed' => 1, 'unchanged' => 900 ),
                         'refused' => array(), 'files_failed' => array( array( 'file' => 'x.pdf', 'url' => '/content/download/1/2/file/x.pdf', 'error' => 'the site answered 403' ) ),
                         'link_check' => array( 'mount' => '/my-site/', 'files' => 125, 'links' => 20000, 'broken' => array(), 'broken_count' => 0 ) );
        $text = implode( "\n", \Exponential\Command\Kernel\ExpstaticBase::buildLines( $report ) );
        $this->assertStringContainsString( 'Static publishing: build of github-public (site, full), run 20261101-120000-a1b2c3 in 12.3s', $text );
        $this->assertStringContainsString( 'Pages:  120 exported, 0 refused, 2 skipped, 1 excluded', $text );
        $this->assertStringContainsString( 'Files:  300 named by the pages, 500 of the storage, 5 extras, 1 could not be collected', $text );
        $this->assertStringContainsString( 'Size:   50M in 925 files', $text );
        $this->assertStringContainsString( 'Taken out: 120 debug', $text );
        $this->assertStringContainsString( 'Diff:   10 added, 3 changed, 1 removed, 900 unchanged', $text );
        $this->assertStringContainsString( 'missing /content/download/1/2/file/x.pdf: the site answered 403', $text );
        $this->assertStringContainsString( 'Link check under /my-site/: 20000 links in 125 files, 0 broken', $text );
        $this->assertStringContainsString( 'Written to var/site/static-publish/github-public', $text );
    }

    /** SC-07 */
    public function testShippedSettings()
    {
        $ini = file_get_contents( self::$root . '/settings/staticcache.ini' );
        $this->assertSame( 1, preg_match( '/^\[StaticPublishSettings\]\n(.*?)(?=^\[)/ms', $ini, $m ) );
        foreach ( array( 'Enabled=disabled', 'TargetList[]', 'WorkDir=static-publish', 'MaxSiteBytes=1000000000', 'MaxFileBytes=100000000' ) as $line )
            $this->assertStringContainsString( "\n$line\n", "\n" . $m[1], $line );
        $this->assertSame( 1, preg_match( '/^\[Target_example\]\n(.*)$/ms', $ini, $m ) );
        $block = $m[1];
        foreach ( expStaticTarget::DEFAULTS as $name => $default )
        {
            if ( is_array( $default ) )
            {
                $this->assertStringContainsString( "\n{$name}[]\n", $block, "{$name}[] is reset in the template" );
                foreach ( $default as $value )
                    $this->assertStringContainsString( "\n{$name}[]=$value\n", $block, "{$name}[]=$value" );
                $this->assertSame( count( $default ), preg_match_all( '/^' . preg_quote( $name, '/' ) . '\[\]=/m', $block ), "{$name}[] holds the defaults only" );
            }
            else if ( $name !== 'Name' && $name !== 'SiteAccess' )
                $this->assertStringContainsString( "\n$name=$default\n", $block, "$name=$default" );
        }
    }

    /** SC-08 */
    public function testPublishContract()
    {
        $source = file_get_contents( self::$root . '/kernel/classes/staticpublish/expstaticpublisher.php' );
        foreach ( self::PUBLISH_KEYS as $key )
            $this->assertStringContainsString( "'$key' =>", $source, "publish report key $key" );
        $this->assertSame( array( 'pushed', 'pending', 'disabled', 'no-remote', 'none' ), array( expStaticRepository::PUSHED, expStaticRepository::PENDING,
                                                                                               expStaticRepository::DISABLED, expStaticRepository::NO_REMOTE,
                                                                                               expStaticRepository::NONE ) );
        $this->assertSame( array( 'done', 'unchanged', 'failed', 'publish', 'rollback' ), array( expStaticStatus::DONE, expStaticStatus::UNCHANGED,
                                                                                                expStaticStatus::FAILED, expStaticStatus::PUBLISH,
                                                                                                expStaticStatus::ROLLBACK ) );
        $this->assertSame( 'static/github-public/3', expStaticRepository::tagName( 'github-public', 3 ) );
        $this->assertSame( array( 'exp_static_run', 'exp_static_page' ), array( expStaticStatus::RUN_TABLE, expStaticStatus::PAGE_TABLE ) );
        $ini = file_get_contents( self::$root . '/settings/staticcache.ini' );
        foreach ( array( 'Push=enabled', 'CommitName=Exponential static publishing', 'CommitEmail=' ) as $line )
            $this->assertStringContainsString( "\n$line\n", $ini, $line );
        $this->assertStringContainsString( "\n[CronjobPart-staticpublish]\nScripts[]=staticpublish.php\n", file_get_contents( self::$root . '/settings/cronjob.ini' ) );
        $this->assertFileExists( self::$root . '/cronjobs/staticpublish.php' );
        $upgrade = file_get_contents( self::$root . '/settings/upgrade.ini' );
        $this->assertStringContainsString( "\nStepList[]=expUpgradeStepStaticPublishTables\n", $upgrade );
        $audit = file_get_contents( self::$root . '/settings/audit.ini' );
        foreach ( array( 'publish', 'rollback', 'credential' ) as $event )
            $this->assertStringContainsString( "\nEnabled[]=system.staticpublish.$event\n", $audit );
    }

    /** SC-09 */
    public function testGitLines()
    {
        $report = array( 'mode' => 'publish', 'target' => 'github-public', 'branch' => 'gh-pages', 'directory' => 'var/site/static-publish/github-public',
                         'messages' => array( 'committed 0123456789 on gh-pages: 3 added, 1 changed, 0 removed', 'tagged static/github-public/2' ),
                         'commit_id' => str_repeat( 'a', 40 ), 'tag_name' => 'static/github-public/2', 'push_state' => 'pending',
                         'remote' => 'https://github.com/owner/site.git', 'credential' => 'token:pages_token' );
        $text = implode( "\n", \Exponential\Command\Kernel\ExpstaticBase::gitLines( $report ) );
        $this->assertStringContainsString( 'Static publishing: publish of github-public, branch gh-pages in var/site/static-publish/github-public', $text );
        $this->assertStringContainsString( 'Commit: aaaaaaaaaaaa, tag static/github-public/2', $text );
        $this->assertStringContainsString( 'Push:   pending (https://github.com/owner/site.git, credential token:pages_token)', $text );
    }

    /** SC-10 */
    public function testJobFromTheAdministrationKeepsTheScriptSiteaccess()
    {
        $contract = \Exponential\Command\Kernel\ExpstaticBase::contract();
        foreach ( $contract as $command => $config )
            $this->assertArrayNotHasKey( 'siteaccess:', self::options( $config ), "$command must not take over the script option --siteaccess" );
        $this->assertArrayHasKey( 'only-siteaccess:', self::options( $contract['exp:static:build'] ) );
        $standard = array( 'siteaccess' => true, 'allow-root-user' => true );
        foreach ( array( 'build' => array(), 'publish' => array( expStaticJob::NODES => array( 57 ), 'message' => 'm' ), 'rollback' => array( 'to' => '2' ) ) as $kind => $options )
        {
            $arguments = expStaticJob::arguments( $kind, '20261102-120000-a1b2c3', 'demo-site-static', $options, 'editor', 'admin', 'p', true );
            $this->assertContains( '--siteaccess=admin', $arguments );
            $declared = self::options( $contract['exp:static:' . $kind] );
            foreach ( $arguments as $argument )
            {
                if ( !preg_match( '/^--([a-z-]+)(=|$)/', $argument, $m ) || isset( $standard[$m[1]] ) )
                    continue;
                $this->assertTrue( isset( $declared[$m[1]] ) || isset( $declared[$m[1] . ':'] ) || isset( $declared[$m[1] . ':*'] ),
                                   "exp:static:$kind declares --{$m[1]}, which a job passes" );
            }
        }
    }
}
