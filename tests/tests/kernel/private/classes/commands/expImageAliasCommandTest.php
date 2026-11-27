<?php
/**
 * The command exp:image:alias (bin/php/expimagealias.php, kernel/private/classes/commands/expimagealias.php), without a
 * database:
 *
 *  IACL-01 - the contract: the script and its alias, the option names, the exit codes, the subcommands and the machine
 *            output schema are permanent (an addition goes into the snapshot below in the same commit; a removal or a
 *            rename is not allowed)
 *  IACL-02 - the command line: status is the default; one known subcommand; --format text|json; each option only
 *            with the subcommands it belongs to; ids are whole numbers from 1; --node or --subtree; one of --pause,
 *            --resume, --cancel; --pause with and without a reason; --source with and without an environment
 *  IACL-03 - the safety rules: regenerate and purge over every image need --all, generate and resync do not; a dry run
 *            of a bulk run cannot be queued, one of a resync can; test needs --node, --object or --source, --alias is
 *            one alias name and needs an image, --dry-run goes with --alias, --path with --source
 *  IACL-04 - the scope: the options become the input of the page's scope (expImageAliasAdmin::scopeFromInput()), so a
 *            run here and a run of Setup > Image aliases cover the same images: node, subtree, classes, attributes,
 *            aliases of the site only ('original' for a resync only), changed since, at most, dry run and overwrite;
 *            the scope's errors name the options
 *  IACL-05 - the text output: counts, progress, a job, the dashboard (every check of the dashboard has a text) and an
 *            image of the test report
 *  IACL-06 - the wiring: the work goes through the existing classes (no copy of their logic), the command becomes the
 *            owner of var/ before it starts as root, exp:cache imagealias and Setup > Cache point to the new command
 *            and page
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/classes/commands/expImageAliasCommandTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\Command\Kernel\Expimagealias;

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expImageAliasCommandTest extends PHPUnit\Framework\TestCase
{
    const SCRIPT = 'bin/php/expimagealias.php';
    const CLASS_FILE = 'kernel/private/classes/commands/expimagealias.php';

    const OPTIONS = array( 'format', 'node', 'subtree', 'object', 'class', 'attribute', 'alias', 'since', 'limit', 'dry-run', 'overwrite', 'queue', 'wait',
                           'all', 'job', 'pause', 'resume', 'cancel', 'source', 'path' );

    const EXIT_CODES = array( 'EXIT_OK' => 0, 'EXIT_ERRORS' => 1, 'EXIT_USAGE' => 2, 'EXIT_UNAVAILABLE' => 3, 'EXIT_PAUSED' => 4 );

    const SUBCOMMANDS = array( 'status', 'generate', 'regenerate', 'purge', 'resync', 'test' );

    const KNOWN = array( 'small', 'large', 'i160', 'i320' );

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 6 ) );
    }

    private static function sameSet( array $expected, array $actual, $what )
    {
        self::assertSame( array(), array_values( array_diff( $expected, $actual ) ), "$what removed or renamed: not allowed" );
        self::assertSame( array(), array_values( array_diff( $actual, $expected ) ), "$what added: add it to the snapshot of expImageAliasCommandTest in the same commit" );
    }

    /** request() for a command line given as subcommand and options */
    private function request( $subcommand, array $options = array() )
    {
        if ( $subcommand !== null )
            $options['arguments'] = is_array( $subcommand ) ? $subcommand : array( $subcommand );
        return Expimagealias::request( $options );
    }

    private function errors( $subcommand, array $options = array() )
    {
        return implode( "\n", $this->request( $subcommand, $options )['errors'] );
    }

    public function testTheContract()
    {
        $this->assertFileExists( self::SCRIPT );
        if ( PHP_OS_FAMILY !== 'Windows' ) // Windows has no executable bit: is_executable() knows .exe, .bat and .cmd
            $this->assertTrue( is_executable( self::SCRIPT ), self::SCRIPT . ' is executable' );
        $this->assertStringStartsWith( "#!/usr/bin/env php\n", file_get_contents( self::SCRIPT ) );
        foreach ( array( self::SCRIPT, self::CLASS_FILE ) as $file )
        {
            preg_match_all( '/^\s*\*\s*@alias\s+(\S+)$/m', file_get_contents( $file ), $m );
            self::sameSet( array( 'exp:image:alias' ), $m[1], "an alias of $file" );
        }
        $this->assertStringContainsString( 'Expimagealias::main( __FILE__ )', file_get_contents( self::SCRIPT ) );

        $code = file_get_contents( self::CLASS_FILE );
        $this->assertSame( 1, preg_match( "/startup\\( '([^)]*)',\s*'\\[subcommand\\]'/s", $code, $m ) );
        preg_match_all( '/\[([a-z-]+)[:;]?\*?\]/', str_replace( array( "'", '.', ' ', "\n" ), '', $m[1] ), $names );
        self::sameSet( self::OPTIONS, $names[1], 'an option of exp:image:alias' );
        foreach ( self::EXIT_CODES as $name => $value )
            $this->assertSame( $value, constant( Expimagealias::class . "::$name" ), $name );
        $this->assertSame( 'exp-image-alias/1', Expimagealias::SCHEMA );
        self::sameSet( self::SUBCOMMANDS, Expimagealias::SUBCOMMANDS, 'a subcommand' );
        self::sameSet( self::SUBCOMMANDS, array_keys( Expimagealias::ALLOWED ), 'a subcommand of ALLOWED' );
        foreach ( Expimagealias::ALLOWED as $subcommand => $allowed )
            $this->assertSame( array(), array_values( array_diff( $allowed, self::OPTIONS ) ), "$subcommand allows only declared options" );
        // the help names every subcommand and every option
        $help = Expimagealias::helpText();
        foreach ( self::SUBCOMMANDS as $subcommand )
            $this->assertStringContainsString( $subcommand, $help );
        foreach ( array_diff( self::OPTIONS, array( 'format' ) ) as $option )
            $this->assertStringContainsString( "--$option", $help, "the help names --$option" );
        $this->assertStringContainsString( 'Exit codes: 0 done; 1 a run or test had failures; 2 wrong options; 3 not available', $help );
        $this->assertStringContainsString( 'setup/aliases', $help );
    }

    public function testTheCommandLine()
    {
        $r = $this->request( null );
        $this->assertSame( 'status', $r['command'] );
        $this->assertSame( array(), $r['errors'] );
        $this->assertSame( 'text', $r['format'] );
        $this->assertSame( 'json', $this->request( 'status', array( 'format' => 'json' ) )['format'] );
        $this->assertStringContainsString( '--format must be text or json', $this->errors( 'status', array( 'format' => 'xml' ) ) );
        $this->assertSame( 'generate', $this->request( 'GENERATE' )['command'] );
        $this->assertStringContainsString( 'unknown subcommand "make"', $this->errors( 'make' ) );
        $this->assertStringContainsString( 'one subcommand only', $this->errors( array( 'generate', 'purge' ) ) );

        // each option with its subcommands only
        $this->assertStringContainsString( '--overwrite does not go with generate (regenerate makes the aliases again)', $this->errors( 'generate', array( 'overwrite' => true ) ) );
        $this->assertSame( '', $this->errors( 'resync', array( 'overwrite' => true ) ) );
        $this->assertStringContainsString( '--object does not go with purge', $this->errors( 'purge', array( 'object' => '5', 'all' => true ) ) );
        $this->assertStringContainsString( '--pause does not go with generate', $this->errors( 'generate', array( 'pause' => true ) ) );
        $this->assertStringContainsString( '--subtree does not go with test', $this->errors( 'test', array( 'subtree' => '2', 'node' => '2' ) ) );
        $this->assertStringContainsString( '--node does not go with status', $this->errors( 'status', array( 'node' => '2' ) ) );
        $this->assertStringContainsString( '--wait does not go with resync', $this->errors( 'resync', array( 'wait' => true ) ) );
        // unset options (null, false) are not "given"
        $this->assertSame( '', $this->errors( 'status', array( 'node' => null, 'overwrite' => false, 'alias' => '' ) ) );

        // ids
        $this->assertStringContainsString( '--node must be a whole number from 1', $this->errors( 'generate', array( 'node' => 'x' ) ) );
        $this->assertStringContainsString( '--limit must be a whole number from 1', $this->errors( 'generate', array( 'limit' => '0' ) ) );
        $this->assertStringContainsString( '--job must be a whole number from 1', $this->errors( 'status', array( 'job' => '-3' ) ) );
        $this->assertStringContainsString( '--cancel must be a whole number from 1', $this->errors( 'status', array( 'cancel' => '1e3' ) ) );
        $this->assertStringContainsString( '--node or --subtree, not both', $this->errors( 'generate', array( 'node' => '2', 'subtree' => '2' ) ) );
        $this->assertTrue( Expimagealias::positiveId( '93' ) );
        $this->assertTrue( Expimagealias::positiveId( 93 ) );
        $this->assertFalse( Expimagealias::positiveId( '0' ) );
        $this->assertFalse( Expimagealias::positiveId( true ) );

        // pause, resume, cancel, job
        $r = $this->request( 'status', array( 'pause' => true ) );
        $this->assertSame( '', $r['pause'] );
        $this->assertSame( 'nightly import', $this->request( 'status', array( 'pause' => ' nightly import ' ) )['pause'] );
        $this->assertFalse( $this->request( 'status' )['pause'] );
        $this->assertTrue( $this->request( 'status', array( 'resume' => true ) )['resume'] );
        $this->assertSame( 12, $this->request( 'status', array( 'cancel' => '12' ) )['cancel'] );
        $this->assertSame( 7, $this->request( null, array( 'job' => '7' ) )['job'] );
        $this->assertStringContainsString( 'one of --pause, --resume, --cancel at a time', $this->errors( 'status', array( 'pause' => true, 'resume' => true ) ) );
        $this->assertStringContainsString( 'one of --pause, --resume, --cancel at a time', $this->errors( 'status', array( 'resume' => true, 'cancel' => '3' ) ) );

        // --source with and without an environment
        $this->assertTrue( $this->request( 'test', array( 'source' => true ) )['source'] );
        $this->assertSame( 'pro', $this->request( 'test', array( 'source' => 'PRO' ) )['source'] );
        $this->assertFalse( $this->request( 'test', array( 'node' => '93' ) )['source'] );
        $this->assertStringContainsString( '--source must be one of alpha, dev, beta, stage, pro, post, arc', $this->errors( 'test', array( 'source' => 'live' ) ) );
    }

    public function testTheSafetyRules()
    {
        foreach ( array( 'regenerate', 'purge' ) as $subcommand )
        {
            $this->assertStringContainsString( 'covers every image of the installation: add --all', $this->errors( $subcommand ) );
            $this->assertSame( '', $this->errors( $subcommand, array( 'all' => true ) ) );
            $this->assertSame( '', $this->errors( $subcommand, array( 'subtree' => '93' ) ) );
            $this->assertSame( '', $this->errors( $subcommand, array( 'alias' => 'i160' ) ) );
            $this->assertSame( '', $this->errors( $subcommand, array( 'since' => '2d' ) ) );
            $this->assertTrue( $this->request( $subcommand, array( 'limit' => '5' ) )['scoped'] );
        }
        $this->assertSame( '', $this->errors( 'generate' ) );
        $this->assertSame( '', $this->errors( 'resync' ) );
        $this->assertFalse( $this->request( 'generate' )['scoped'] );

        $this->assertStringContainsString( 'it queues nothing', $this->errors( 'generate', array( 'dry-run' => true, 'queue' => true ) ) );
        $this->assertSame( '', $this->errors( 'resync', array( 'dry-run' => true, 'queue' => true ) ) );
        $r = $this->request( 'regenerate', array( 'node' => '93', 'queue' => true, 'wait' => true ) );
        $this->assertTrue( $r['queue'] );
        $this->assertTrue( $r['wait'] );

        $this->assertStringContainsString( 'test needs --node=<id>, --object=<id> or --source', $this->errors( 'test' ) );
        $this->assertStringContainsString( '--node or --object, not both', $this->errors( 'test', array( 'node' => '1', 'object' => '1' ) ) );
        $this->assertStringContainsString( '--alias must be one alias name for test', $this->errors( 'test', array( 'node' => '93', 'alias' => 'i160,i320' ) ) );
        $this->assertStringContainsString( '--alias needs --node or --object', $this->errors( 'test', array( 'source' => true, 'alias' => 'i160' ) ) );
        $this->assertStringContainsString( '--dry-run goes with --alias', $this->errors( 'test', array( 'node' => '93', 'dry-run' => true ) ) );
        $this->assertStringContainsString( '--path goes with --source', $this->errors( 'test', array( 'node' => '93', 'path' => 'var/x.jpg' ) ) );
        $this->assertSame( '', $this->errors( 'test', array( 'object' => '91', 'alias' => 'i160', 'dry-run' => true ) ) );
        $this->assertSame( '', $this->errors( 'test', array( 'source' => 'pro', 'path' => 'var/site/storage/images/a/1-1-eng-GB/a.jpg' ) ) );
        $r = $this->request( 'test', array( 'node' => '93', 'alias' => 'i160' ) );
        $this->assertSame( 93, $r['node'] );
        $this->assertSame( 'i160', $r['alias'] );
    }

    private function scope( $subcommand, array $options, $now = 1800000000 )
    {
        $r = $this->request( $subcommand, $options );
        $this->assertSame( array(), $r['errors'], implode( "\n", $r['errors'] ) );
        return expImageAliasAdmin::scopeFromInput( $r['input'], self::KNOWN, $now );
    }

    public function testTheScopeIsThePagesScope()
    {
        $s = $this->scope( 'generate', array( 'subtree' => '93' ) );
        $this->assertSame( array(), $s['errors'] );
        $this->assertSame( array( 'subtree' => 93, 'action' => 'generate' ), $s['options'] );
        $this->assertFalse( $s['preview'] );

        $s = $this->scope( 'regenerate', array( 'node' => '93', 'class' => 'image, article', 'attribute' => 'image', 'alias' => 'i160,small',
                                                'since' => '2d', 'limit' => '10' ) );
        $this->assertSame( array( 'node' => 93, 'class' => array( 'image', 'article' ), 'attribute' => array( 'image' ), 'alias' => array( 'i160', 'small' ),
                                  'since' => 1800000000 - 2 * 86400, 'limit' => 10, 'action' => 'regenerate' ), $s['options'] );
        $this->assertSame( 'node', $s['target'] );

        $s = $this->scope( 'purge', array( 'all' => true ) );
        $this->assertSame( 'all', $s['target'] );
        $this->assertSame( array( 'action' => 'purge' ), $s['options'] );

        // a dry run of a bulk run only counts (preview); a resync passes its own dry run and overwrite on
        $this->assertTrue( $this->scope( 'generate', array( 'subtree' => '2', 'dry-run' => true ) )['preview'] );
        $s = $this->scope( 'resync', array( 'subtree' => '2', 'dry-run' => true, 'overwrite' => true, 'alias' => 'original,i160', 'since' => '2026-11-01' ) );
        $this->assertFalse( $s['preview'] );
        $this->assertSame( array( 'subtree' => 2, 'alias' => array( 'original', 'i160' ), 'since' => mktime( 0, 0, 0, 11, 1, 2026 ), 'overwrite' => true, 'dry_run' => true ),
                           $s['options'] );

        // the scope's own checks, named by option
        $s = $this->scope( 'generate', array( 'alias' => 'original' ) );
        $this->assertSame( array( '--alias: not an alias of this site: original' ), Expimagealias::scopeErrors( $s['errors'] ) );
        $s = $this->scope( 'generate', array( 'alias' => 'huge' ) );
        $this->assertArrayHasKey( 'alias', $s['errors'] );
        $s = $this->scope( 'generate', array( 'since' => 'yesterday' ) );
        $this->assertSame( array( '--since: a date (2026-11-01, 2026-11-01 14:30) or a time ago (2 days, 12 hours, 30 minutes)' ), Expimagealias::scopeErrors( $s['errors'] ) );
        $s = $this->scope( 'generate', array( 'class' => 'a/b' ) );
        $this->assertStringStartsWith( '--class: identifiers', Expimagealias::scopeErrors( $s['errors'] )[0] );
        $this->assertSame( array( '--node: a node id', '--subcommand: x' ), Expimagealias::scopeErrors( array( 'target' => 'a node id', 'action' => 'x' ) ) );

        // the input itself
        $input = Expimagealias::scopeInput( 'purge', array( 'subtree' => '5', 'node' => '7', 'alias' => 'a b;c' ) );
        $this->assertSame( 'subtree', $input['target'] );
        $this->assertSame( '5', $input['node'] );
        $this->assertSame( array( 'a', 'b', 'c' ), $input['alias'] );
        $this->assertFalse( $input['dry_run'] );
    }

    public function testTheTextOutput()
    {
        $this->assertSame( 'present 3, made 2, variants made 1', Expimagealias::countsText( array( 'present' => 3, 'made' => 2, 'failed' => 0, 'variants_made' => 1 ) ) );
        $this->assertSame( 'nothing', Expimagealias::countsText( array( 'made' => 0 ) ) );
        $this->assertSame( ' 5/40  12%  object 9 attribute 1-1-eng-GB  made 1',
                           Expimagealias::progressLine( array( 'done' => 5, 'total' => 40, 'path' => 'object 9 attribute 1-1-eng-GB', 'counts' => array( 'made' => 1 ) ) ) );
        $this->assertSame( '0/0 100%  -  nothing', Expimagealias::progressLine( array( 'done' => 0, 'total' => 0, 'path' => '-' ) ) );

        $job = array( 'id' => 12, 'type' => 'image.alias.bulk', 'action' => 'regenerate', 'state' => 'running', 'final' => false, 'can_cancel' => true,
                      'done' => 3, 'total' => 4, 'percent' => 75, 'message' => '', 'run' => 'bulk1', 'scope' => 'node=93', 'created' => 0, 'started' => 0,
                      'finished' => 0, 'error' => '', 'counts' => array( 'made' => 2 ) );
        $this->assertSame( '#12 regenerate running   3/4 node=93', Expimagealias::jobSummary( $job ) );
        $lines = implode( "\n", Expimagealias::jobLines( $job ) );
        $this->assertStringContainsString( 'can be cancelled: --cancel=12', $lines );
        $this->assertStringContainsString( '3/4 (75 %)', $lines );
        $this->assertStringContainsString( 'counts     made 2', $lines );

        // the dashboard: every check the dashboard can raise has a text
        preg_match_all( "/'key' => '([a-z-]+)', 'level'/", file_get_contents( 'kernel/classes/imagealias/expimagealiasadmin.php' ), $m );
        self::sameSet( array_unique( $m[1] ), array_keys( Expimagealias::CHECK_TEXTS ), 'a check of the dashboard' );
        $raw = array( 'mode' => 'on-demand', 'url_form' => 'token', 'delivery' => 'auto', 'max_wait' => 2, 'max_parallel' => 2, 'busy' => 1,
                      'negotiate' => true, 'pre_generate' => true, 'placeholder' => true, 'swap_script' => false, 'preferred' => 'auto',
                      'order' => array( 'imagick', 'gd' ), 'converters' => array( 'gd' => array( 'webp' ), 'imagick' => array( 'webp', 'avif' ) ),
                      'staging' => array( 'environment' => 'dev', 'sync_from' => 'pro', 'failures_before_pause' => 5 ),
                      'source' => array( 'base_url' => 'https://pro.example.org', 'paused' => false, 'failures' => 2, 'warning' => null, 'last_error' => 'timeout' ),
                      'queue' => array( 'available' => true, 'types' => array( 'image.alias.bulk' => array( 'waiting' => 1, 'running' => 0, 'dead' => 1, 'registered' => true ) ) ),
                      'bulk_pause' => array( 'since' => 1800000000, 'by' => 'cli:alpha', 'reason' => 'import' ),
                      'counts' => array( 'generated' => 3, 'placeholders' => 1, 'failures' => 0, 'deferred' => 5, 'staged' => 4, 'runs' => 2, 'lines' => 9, 'oldest' => 0, 'complete' => true ),
                      'key' => array( 'exists' => true, 'modified' => 1800000000 - 3 * 86400, 'previous_until' => 0 ),
                      'log_path' => 'var/log/imagealias.log', 'aliases' => 30 );
        $d = expImageAliasAdmin::buildDashboard( $raw, 1800000000 );
        $text = implode( "\n", Expimagealias::dashboardLines( $d, array( $job ) ) );
        foreach ( array( 'on-demand (address token, delivery auto)', 'conversions at once 1 of 2 busy', 'swap script off', '30 (this siteaccess',
                         'imagick webp+avif, gd webp', 'environment dev, source pro https://pro.example.org, warn, 2 failures in a row, last error timeout',
                         'waiting 1, running 0, failed for good 1', 'by cli:alpha: import', 'generated 3, placeholders 1, failures 0, deferred to a job 5, staged 4, runs 2',
                         '3 days old', 'check           warn: the staging source failed recently', 'check           warn: bulk runs are paused',
                         '#12 regenerate' ) as $part )
            $this->assertStringContainsString( $part, $text );
        $this->assertStringNotContainsString( 'aliases failed', $text, 'aliases deferred to a job are no failures' );
        $this->assertStringContainsString( 'a deferral to a job is no failure', Expimagealias::CHECK_TEXTS['failures'] );

        $one = array( 'attribute' => 5, 'version' => 2, 'language' => 'eng-GB', 'identifier' => 'image', 'name' => 'Image',
                      'original' => array( 'url' => 'var/s/a.jpg', 'width' => 800, 'height' => 600, 'mime' => 'image/jpeg', 'exists' => true ),
                      'aliases' => array( array( 'alias' => 'i160', 'siteaccess' => 'site', 'url' => 'content/image/t/a_i160.jpg', 'path' => 'var/s/a_i160.jpg',
                                                 'width' => 160, 'height' => 120, 'mime' => 'image/jpeg', 'exists' => false,
                                                 'formats' => array( 'webp' => 'missing' ) ),
                                          array( 'alias' => 'small', 'siteaccess' => 'site', 'url' => 'var/s/a_small.jpg', 'path' => 'var/s/a_small.jpg',
                                                 'width' => 100, 'height' => 75, 'mime' => 'image/jpeg', 'exists' => true, 'formats' => array() ) ) );
        $lines = Expimagealias::imageLines( $one );
        $this->assertCount( 4, $lines );
        $this->assertStringContainsString( 'attribute 5 version 2 eng-GB', $lines[0] );
        $this->assertStringContainsString( 'on demand', $lines[2] );
        $this->assertStringContainsString( '[webp missing]', $lines[2] );
        $this->assertCount( 3, Expimagealias::imageLines( $one, 'small' ) );
        $this->assertSame( array( 'image      image (Image), attribute 5 version 2 eng-GB', '  no original stored' ),
                           Expimagealias::imageLines( array( 'original' => null ) + $one ) );
    }

    public function testTheWiring()
    {
        $code = file_get_contents( self::CLASS_FILE );
        // the work of the page and the jobs, not a copy of it
        foreach ( array( '\expImageAliasAdmin::scopeFromInput(', '\expImageAliasAdmin::knownAliases()', '\expImageAliasJob::startBulk(',
                         '\expImageAliasJob::startResync(', '\expImageAliasJob::queueAvailable(', '\expImageAliasStager::countImages(',
                         '\expImageAliasAdmin::dashboard()', '\expImageAliasAdmin::testImage(', '\expImageAliasAdmin::testSource(',
                         '\expImageAliasAdmin::makeOne(', '\expImageAliasAdmin::cancelJob(', '\expImageAliasThrottle::pauseBulk(',
                         '\expImageAliasThrottle::resumeBulk(' ) as $call )
            $this->assertStringContainsString( $call, $code );
        foreach ( array( 'arrayQuery', 'eZDB::instance', 'createImageAlias', 'purgeAliases', 'expQueue::enqueue' ) as $own )
            $this->assertStringNotContainsString( $own, $code, "the command does not do $own itself" );
        // as root: the owner of var/ before the script starts (settings, caches, database)
        $drop = strpos( $code, '\expContentJobWorker::dropPrivileges(' );
        $this->assertNotFalse( $drop );
        $this->assertLessThan( strpos( $code, '$this->startup(' ), $drop );
        // the report of a test asks for the addresses of the settings' mode, and goes back to the command line's
        $this->assertStringContainsString( '\expImageAliasSettings::setMode( \expImageAliasSettings::configuredMode() )', $code );
        $this->assertStringContainsString( '\expImageAliasSettings::setMode( null )', $code );

        // the existing tools point to the new command and page
        $cache = file_get_contents( 'kernel/private/classes/commands/cache.php' );
        $this->assertGreaterThanOrEqual( 3, substr_count( $cache, 'exp:image:alias' ), 'exp:cache names exp:image:alias in its overview and its imagealias help' );
        $this->assertStringContainsString( 'Setup > Image aliases', $cache );
        $this->assertStringContainsString( "'imagealias' => array( 'imagealias [clear|purge]'", $cache, 'exp:cache imagealias stays as it is' );
        foreach ( array( 'design/admin/templates/setup/cache.tpl', 'design/admin4/templates/setup/cache.tpl' ) as $template )
        {
            $tpl = file_get_contents( $template );
            $this->assertStringContainsString( "<li><a href={'/setup/aliases'|ezurl}>{'Image aliases'|i18n( 'design/admin/setup/aliases' )}</a><code>./console exp:image:alias status --allow-root-user</code></li>",
                                               $tpl, $template );
        }
    }
}
