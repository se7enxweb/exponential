<?php
/**
 * The permanent contract of the exp:url commands (URL map import and export): their names, options and arguments, the
 * exit codes and the output schema. Options, arguments and keys may be added (extend the snapshot); nothing here may
 * ever be renamed or removed. Needs no installation.
 *
 *  UC-01 every command and its option configuration (snapshot), and the arguments
 *  UC-02 every command has its entry point in bin/php with the @alias the console dispatches, and a description
 *  UC-03 the literal option configuration, arguments and help of each command (what exp:help:examples reads) are the
 *        contract's; no command declares --siteaccess (the script's own -s is the siteaccess)
 *  UC-04 the exit codes, the schema, listOption(), the text of a plan and an import (planLines())
 *  UC-05 the upgrade step is registered in settings/upgrade.ini, the audit events are known, error.ini answers the
 *        redirect statuses
 *  UC-06 the values of the helpers: --since (a date or a span back), a report value (suggestions, a redirect chain)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUrlCommandContractTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    /** The snapshot. Add, never change or remove. */
    const COMMANDS = array(
        'exp:url:import' => '[format:][language:][on-conflict:][dry-run][report:][host:*][login:][output-format:]',
        'exp:url:export' => '[format:][kind:*][language:][output:][include-node-aliases][output-format:]',
        'exp:url:batches' => '[limit:][format:]',
        'exp:url:undo' => '[force][format:][login:]',
        'exp:url:from-sitemap' => '[output:][output-format:][format:][limit:][only-host:*][any-host][keep-duplicates][full-urls][timeout:][insecure]',
        'exp:url:suggest' => '[output:][output-format:][format:][top:][fill-above:][min-score:][report:]',
        'exp:url:from-404' => '[output:][output-format:][format:][min-hits:][since:][limit:][include-assets][include-bots][log:*]',
        'exp:url:test' => '[base:][format:][report:][limit:][concurrency:][timeout:][max-redirects:][insecure]',
    );

    /** The arguments. Add, never change or remove. */
    const ARGUMENTS = array(
        'exp:url:import' => '[file]',
        'exp:url:undo' => '[batch]',
        'exp:url:from-sitemap' => '[source]',
        'exp:url:suggest' => '[file]',
        'exp:url:test' => '[file]',
    );

    /** command => entry point in bin/php */
    const ENTRY = array(
        'exp:url:import' => 'expurlimport',
        'exp:url:export' => 'expurlexport',
        'exp:url:batches' => 'expurlbatches',
        'exp:url:undo' => 'expurlundo',
        'exp:url:from-sitemap' => 'expurlfromsitemap',
        'exp:url:suggest' => 'expurlsuggest',
        'exp:url:from-404' => 'expurlfrom404',
        'exp:url:test' => 'expurltest',
    );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'upgrade/expupgradestep', 'upgrade/expupgraderegistry', 'upgrade/expupgrade' ) as $f )
            require_once self::$root . "/kernel/classes/$f.php";
        require_once self::$root . '/kernel/private/classes/runnable/runnable.php';
        require_once self::$root . '/kernel/private/classes/runnable/command.php';
        require_once self::$root . '/kernel/private/classes/commands/expurlbase.php';
    }

    /** @return array option => true of a config string */
    private static function options( $config )
    {
        preg_match_all( '/\[([a-z-]+:?\*?)\]/', $config, $m );
        return array_fill_keys( $m[1], true );
    }

    /** UC-01 */
    public function testCommandsAndOptionsOnlyGrow()
    {
        $now = \Exponential\Command\Kernel\ExpurlBase::contract();
        foreach ( self::COMMANDS as $command => $config )
        {
            $this->assertArrayHasKey( $command, $now, "the command $command is permanent" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( $option, self::options( $now[$command] ), "$command keeps --$option with the same arity" );
        }
        $help = \Exponential\Command\Kernel\ExpurlBase::optionHelp();
        foreach ( $now as $command => $config )
        {
            $this->assertArrayHasKey( $command, self::COMMANDS, "a new command $command: add it to the snapshot" );
            foreach ( array_keys( self::options( $config ) ) as $option )
                $this->assertArrayHasKey( rtrim( $option, ':*' ), $help, "--$option has a help text" );
            $this->assertArrayNotHasKey( 'siteaccess:', self::options( $config ), 'the siteaccess is the script\'s own -s' );
        }
        $arguments = \Exponential\Command\Kernel\ExpurlBase::arguments();
        $this->assertSame( self::ARGUMENTS, array_intersect_key( $arguments, self::ARGUMENTS ) );
        foreach ( array_keys( $arguments ) as $command )
            $this->assertArrayHasKey( $command, self::ARGUMENTS, "a new argument of $command: add it to the snapshot" );
    }

    /** UC-02 */
    public function testEntryPoints()
    {
        foreach ( self::ENTRY as $command => $file )
        {
            $path = self::$root . "/bin/php/$file.php";
            $this->assertFileExists( $path );
            $text = file_get_contents( $path );
            $this->assertStringContainsString( "@alias $command", $text );
            $this->assertStringStartsWith( "#!/usr/bin/env php\n", $text );
            $this->assertTrue( is_executable( $path ), "$path is executable" );
            $code = file_get_contents( self::$root . "/kernel/private/classes/commands/$file.php" );
            $this->assertStringContainsString( "@alias $command", $code );
            $this->assertMatchesRegularExpression( '/@description \S/', $code );
        }
        $this->assertSame( array_keys( self::COMMANDS ), array_keys( self::ENTRY ) );
    }

    /** UC-03 */
    public function testLiteralOptionsMatchTheContract()
    {
        require_once self::$root . '/kernel/classes/help/exphelpexamplesgenerator.php';
        $contract = \Exponential\Command\Kernel\ExpurlBase::contract();
        $arguments = \Exponential\Command\Kernel\ExpurlBase::arguments();
        $help = \Exponential\Command\Kernel\ExpurlBase::optionHelp();
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

    /** UC-04 */
    public function testCodesSchemaAndLines()
    {
        $this->assertSame( array( 0, 1, 2, 3, 4 ), array( expUpgrade::EXIT_OK, expUpgrade::EXIT_FAILED, expUpgrade::EXIT_USAGE, expUpgrade::EXIT_UNREACHABLE, expUpgrade::EXIT_INTERNAL ) );
        $this->assertSame( 'exp-url/1', \Exponential\Command\Kernel\ExpurlBase::REPORT );
        $this->assertSame( array( 'a.com=site', 'b=ger' ), \Exponential\Command\Kernel\ExpurlBase::listOption( array( 'a.com=site, b=ger', 'a.com=site' ) ) );
        $counts = array( 'rows' => 4, 'new' => 2, 'changed' => 1, 'unchanged' => 0, 'duplicate' => 0, 'conflict' => 0, 'broken-target' => 1, 'unknown-host' => 0,
                         'not-importable' => 0, 'create' => 2, 'update' => 0, 'skip' => 2 );
        $lines = \Exponential\Command\Kernel\ExpurlBase::planLines( array(
            'file' => 'old.csv', 'dry_run' => false, 'counts' => $counts,
            'result' => array( 'batch_id' => 7, 'counts' => array( 'created' => 2, 'updated' => 0, 'skipped' => 2, 'failed' => 0 ) ),
            'rows' => array( array( 'state' => 'broken-target', 'line' => 4, 'old_url' => '/x', 'new_url' => '/y', 'messages' => array( 'the target /y does not exist on this site' ) ) ),
        ) );
        $this->assertSame( 'URL map import: old.csv (4 rows)', $lines[0] );
        $this->assertStringContainsString( '2 new, 1 changed', $lines[1] );
        $this->assertStringContainsString( 'batch 7 (exp:url:undo 7 undoes it)', $lines[3] );
        $this->assertStringContainsString( 'broken-target  line 4     /x -> /y: the target /y does not exist', $lines[4] );
        $this->assertSame( array(), \Exponential\Command\Kernel\ExpurlBase::planLines( array() ) );
    }

    /** UC-06 */
    public function testHelperValues()
    {
        $now = 1700000000;
        $this->assertSame( array( $now - 30 * 86400, $now - 12 * 3600, $now - 2 * 604800, $now - 45 * 60, null, null ), array(
            \Exponential\Command\Kernel\ExpurlBase::sinceTime( '30d', $now ), \Exponential\Command\Kernel\ExpurlBase::sinceTime( '12h', $now ),
            \Exponential\Command\Kernel\ExpurlBase::sinceTime( '2W', $now ), \Exponential\Command\Kernel\ExpurlBase::sinceTime( '45m', $now ),
            \Exponential\Command\Kernel\ExpurlBase::sinceTime( 'yesterday', $now ), \Exponential\Command\Kernel\ExpurlBase::sinceTime( '2026-13-45', $now ) ) );
        $this->assertSame( strtotime( '2026-11-01' ), \Exponential\Command\Kernel\ExpurlBase::sinceTime( '2026-11-01', $now ) );
        $this->assertSame( array( 'yes', '/a (90); /b (40)', '301 https://x/old > 200 https://x/new', '7' ), array(
            \Exponential\Command\Kernel\ExpurlBase::reportValue( true ),
            \Exponential\Command\Kernel\ExpurlBase::reportValue( array( array( 'path' => '/a', 'score' => 90 ), array( 'path' => '/b', 'score' => 40 ) ) ),
            \Exponential\Command\Kernel\ExpurlBase::reportValue( array( array( 'status' => 301, 'url' => 'https://x/old' ), array( 'status' => 200, 'url' => 'https://x/new' ) ) ),
            \Exponential\Command\Kernel\ExpurlBase::reportValue( 7 ) ) );
    }

    /** UC-05 */
    public function testSettings()
    {
        $this->assertStringContainsString( "StepList[]=expUpgradeStepUrlMapTables\n", file_get_contents( self::$root . '/settings/upgrade.ini' ) );
        $taxonomy = file_get_contents( self::$root . '/kernel/classes/audit/expaudittaxonomy.php' );
        foreach ( array( 'content.urlalias.import', 'content.urlalias.import_undo' ) as $event )
            $this->assertStringContainsString( "'$event' =>", $taxonomy );
        $errorIni = file_get_contents( self::$root . '/settings/error.ini' );
        foreach ( array( '302' => 'Found', '303' => 'See Other', '307' => 'Temporary Redirect', '308' => 'Permanent Redirect' ) as $status => $name )
            $this->assertStringContainsString( "[HTTPError-$status]\nHTTPName=$name\n", $errorIni );
    }
}
