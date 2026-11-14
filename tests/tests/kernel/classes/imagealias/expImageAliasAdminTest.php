<?php
/**
 * Setup > Image aliases (setup/aliases, expImageAliasAdmin), without a database:
 *
 *  IAA-01 - the settings editor checks each value against its definition: choices, whole numbers and numbers in their
 *           range, on/off, addresses without user or query, the NAME of a secret (never "user:password"), lists of
 *           e-mail addresses and path maps
 *  IAA-02 - a submitted form becomes changes only where it differs from what is in force; lists are cleared and
 *           written again; "remove it here" removes only what settings/override sets; values that are not allowed
 *           are errors and change nothing; a source cannot be the installation's own environment
 *  IAA-03 - the changes are written line by line into settings/override/image.ini.append.php of an installation
 *           root (a preview writes nothing); removing the last setting of a block removes the block, but a block that
 *           keeps a comment of its own (a commented setting to switch back) stays as it is
 *  IAA-04 - the scope of a bulk run: action, whole site / node / subtree, classes, attributes, aliases of the site
 *           only ('original' only for a resync), changed since, at most; dry run and overwrite per action
 *  IAA-05 - "changed since" takes a date, a date and time, or a time ago, never the future
 *  IAA-06 - the run history: the filter from view parameters (defer a choice of its own, older "fail result=timeout"
 *           lines of an alias read as defer), the log lines it keeps, the counts of the dashboard
 *           (generated, placeholders, failures, deferred, staged, runs) since a time, and whether the lines read reach back
 *  IAA-07 - the dashboard's verdicts: render mode, no WebP writer, staging without an address, a paused or failing
 *           source, no job queue, failed jobs, paused bulk runs, failures (aliases deferred to a job are none), the
 *           signing key's age; the converters in
 *           the order they are tried
 *  IAA-08 - the connection test's verdict per status and error
 *  IAA-09 - the wiring: the views and policies in module.php, the Setup menu entry right after Cache, the templates
 *           and the context menu identical in design/admin and design/admin4 without inline CSS or script, the page
 *           script, and no setting value shown for a credential
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasAdminTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/ini/fixtures/expinienginetestfixtures.php';

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expImageAliasAdminTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $kernelRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernelRoot = dirname( __DIR__, 5 ) . '/';
    }

    protected function tearDown(): void
    {
        expIniEditor::setRoot( null );
        parent::tearDown();
    }

    /** The state of every setting as the shipped defaults, nothing in settings/override */
    private function defaultState( array $values = array(), array $overridden = array() )
    {
        $state = array();
        foreach ( expImageAliasAdmin::allDefinitions() as $block => $variables )
        {
            foreach ( $variables as $variable => $def )
                $state[$block][$variable] = array( 'value' => isset( $values["$block.$variable"] ) ? $values["$block.$variable"] : $def['default'],
                                                   'overridden' => in_array( "$block.$variable", $overridden, true ) );
        }
        return $state;
    }

    public function testValuesAreCheckedAgainstTheirDefinition()
    {
        $defs = expImageAliasAdmin::settingsDefinition();
        $check = function ( $block, $variable, $value ) use ( $defs ) {
            return expImageAliasAdmin::validateValue( $defs[$block][$variable], $value );
        };
        $this->assertSame( 'render', $check( 'AliasSettings', 'AliasGeneration', ' Render ' )['value'] );
        $this->assertFalse( $check( 'AliasSettings', 'AliasGeneration', 'sometimes' )['ok'] );
        $this->assertSame( '4', $check( 'AliasSettings', 'MaxParallel', '04' )['value'] );
        $this->assertFalse( $check( 'AliasSettings', 'MaxParallel', '0' )['ok'] );
        $this->assertFalse( $check( 'AliasSettings', 'MaxParallel', '65' )['ok'] );
        $this->assertFalse( $check( 'AliasSettings', 'MaxParallel', 'lots' )['ok'] );
        $this->assertSame( '2.5', $check( 'AliasSettings', 'MaxWait', '2.50' )['value'] );
        $this->assertSame( '3', $check( 'AliasSettings', 'MaxWait', '3.0' )['value'] );
        $this->assertFalse( $check( 'AliasSettings', 'MaxWait', '301' )['ok'] );
        $this->assertFalse( $check( 'AliasSettings', 'Negotiate', 'yes' )['ok'] );
        $this->assertSame( '', $check( 'AliasStaging', 'SyncFrom', '' )['value'] );
        $this->assertFalse( $check( 'AliasStaging', 'Environment', 'production' )['ok'] );
        $this->assertFalse( $check( 'AliasSettings', 'MaxWait', "2\n" . 'x' )['ok'] );

        $source = expImageAliasAdmin::sourceDefinition();
        $this->assertSame( 'https://www.example.org', expImageAliasAdmin::validateValue( $source['BaseURL'], 'https://www.example.org/' )['value'] );
        $this->assertFalse( expImageAliasAdmin::validateValue( $source['BaseURL'], 'https://user:pass@www.example.org' )['ok'] );
        $this->assertFalse( expImageAliasAdmin::validateValue( $source['BaseURL'], 'https://www.example.org/?a=1' )['ok'] );
        $this->assertFalse( expImageAliasAdmin::validateValue( $source['BaseURL'], 'ftp://www.example.org' )['ok'] );
        $this->assertTrue( expImageAliasAdmin::validateValue( $source['Credential'], 'staging_source_credential' )['ok'] );
        $this->assertTrue( expImageAliasAdmin::validateValue( $source['Credential'], 'env:STAGING_SOURCE' )['ok'] );
        // a credential typed in instead of a name is refused, and not echoed back as accepted
        $this->assertFalse( expImageAliasAdmin::validateValue( $source['Credential'], 'editor:secret-password' )['ok'] );
        $this->assertFalse( expImageAliasAdmin::validateValue( $source['Credential'], 'Bearer abcdef' )['ok'] );

        $list = expImageAliasAdmin::validateValue( $defs['AliasStaging']['AlertRecipients'], "ops@example.org\r\n\r\nops@example.org\nweb@example.org " );
        $this->assertSame( array( 'ops@example.org', 'web@example.org' ), $list['value'] );
        $this->assertFalse( expImageAliasAdmin::validateValue( $defs['AliasStaging']['AlertRecipients'], 'not an address' )['ok'] );
        $this->assertTrue( expImageAliasAdmin::validateValue( $defs['AliasStaging']['PathMap'], "var/site/storage;var/live/storage\nvar/a;https://cdn.example.org/a" )['ok'] );
        $this->assertFalse( expImageAliasAdmin::validateValue( $defs['AliasStaging']['PathMap'], 'var/site/storage' )['ok'] );
        $this->assertFalse( expImageAliasAdmin::validateValue( $defs['AliasStaging']['PathMap'], 'var/site;../../etc' )['ok'] );
    }

    public function testAFormBecomesChangesOnlyWhereItDiffers()
    {
        $state = $this->defaultState( array( 'AliasStaging.AlertRecipients' => array( 'ops@example.org' ) ), array( 'AliasSettings.Delivery' ) );
        $input = array( 'AliasSettings' => array( 'MaxWait' => '2', 'MaxParallel' => '3', 'Delivery' => 'auto' ),
                        'AliasStaging' => array( 'AlertRecipients' => "ops@example.org", 'PathMap' => "var/a;var/b" ),
                        'AliasSource_pro' => array( 'BaseURL' => '', 'Credential' => '' ) );
        $planned = expImageAliasAdmin::settingsChanges( $input, array(), $state );
        $this->assertSame( array(), $planned['errors'] );
        $this->assertSame( array( 'AliasSettings.MaxParallel', 'AliasStaging.PathMap' ), $planned['changed'] );
        $this->assertSame( array(
            array( 'op' => 'set', 'block' => 'AliasSettings', 'variable' => 'MaxParallel', 'value' => '3' ),
            array( 'op' => 'clear', 'block' => 'AliasStaging', 'variable' => 'PathMap', 'value' => '' ),
            array( 'op' => 'add', 'block' => 'AliasStaging', 'variable' => 'PathMap', 'value' => 'var/a;var/b' ),
        ), $planned['changes'] );

        // remove it here: only what settings/override sets
        $planned = expImageAliasAdmin::settingsChanges( array(), array( 'AliasSettings' => array( 'Delivery' => '1', 'MaxWait' => '1' ) ), $state );
        $this->assertSame( array( array( 'op' => 'remove', 'block' => 'AliasSettings', 'variable' => 'Delivery', 'value' => '' ) ), $planned['changes'] );

        // errors change nothing of that setting; a source of its own environment is refused
        $planned = expImageAliasAdmin::settingsChanges( array( 'AliasSettings' => array( 'MaxParallel' => 'lots' ),
                                                               'AliasStaging' => array( 'Environment' => 'dev', 'SyncFrom' => 'dev' ) ), array(), $state );
        $this->assertArrayHasKey( 'AliasSettings.MaxParallel', $planned['errors'] );
        $this->assertArrayHasKey( 'AliasStaging.SyncFrom', $planned['errors'] );
        $this->assertNotContains( 'AliasSettings.MaxParallel', $planned['changed'] );
    }

    public function testChangesAreWrittenIntoTheOverrideFileAndBlocksWithCommentsStay()
    {
        $root = expIniEngineTestFixtures::makeRoot( $this->name() );
        $file = $root . 'settings/override/image.ini.append.php';
        if ( !is_dir( dirname( $file ) ) )
            mkdir( dirname( $file ), 0700, true );
        $original = "<?php /* #?ini charset=\"utf-8\"?\n\n[AliasSettings]\n# Roll back by uncommenting the next line.\n#AliasGeneration=render\n\n[i160]\nFormats[]=webp\n\n*/ ?>";
        file_put_contents( $file, $original );

        $set = array( array( 'op' => 'set', 'block' => 'AliasSettings', 'variable' => 'MaxWait', 'value' => '3' ),
                      array( 'op' => 'set', 'block' => 'AliasSource_dev', 'variable' => 'BaseURL', 'value' => 'https://dev.example.org' ) );
        $preview = expImageAliasAdmin::applySettings( $set, true );
        $this->assertTrue( $preview['ok'], $preview['error'] );
        $this->assertFalse( $preview['written'] );
        $this->assertStringContainsString( '+MaxWait=3', $preview['diff'] );
        $this->assertSame( $original, file_get_contents( $file ), 'a preview writes nothing' );

        $done = expImageAliasAdmin::applySettings( $set, false );
        $this->assertTrue( $done['written'], $done['error'] );
        $this->assertSame( 'settings/override/image.ini.append.php', $done['path'] );
        $written = file_get_contents( $file );
        $this->assertStringContainsString( "#AliasGeneration=render", $written );
        $this->assertStringContainsString( "MaxWait=3", $written );
        $this->assertStringContainsString( "[AliasSource_dev]\nBaseURL=https://dev.example.org", $written );

        $back = expImageAliasAdmin::applySettings( array( array( 'op' => 'remove', 'block' => 'AliasSettings', 'variable' => 'MaxWait', 'value' => '' ),
                                                         array( 'op' => 'remove', 'block' => 'AliasSource_dev', 'variable' => 'BaseURL', 'value' => '' ) ), false );
        $this->assertTrue( $back['ok'], $back['error'] );
        $this->assertSame( $original, file_get_contents( $file ), 'removing what was saved gives the file back as it was, its commented setting included' );
    }

    public function testTheScopeOfARun()
    {
        $known = array( 'small', 'large', 'i160' );
        $scope = expImageAliasAdmin::scopeFromInput( array( 'action' => 'regenerate', 'target' => 'subtree', 'node' => '93', 'class' => 'article, image article',
                                                           'attribute' => 'image', 'alias' => array( 'small', 'i160', 'small' ), 'since' => '2 days',
                                                           'limit' => '20', 'dry_run' => '1' ), $known, 1800000000 );
        $this->assertSame( array(), $scope['errors'] );
        $this->assertSame( array( 'subtree' => 93, 'class' => array( 'article', 'image' ), 'attribute' => array( 'image' ),
                                  'alias' => array( 'small', 'i160' ), 'since' => 1800000000 - 2 * 86400, 'limit' => 20, 'action' => 'regenerate' ), $scope['options'] );
        $this->assertTrue( $scope['preview'], 'a dry run of a regenerate only counts' );

        $scope = expImageAliasAdmin::scopeFromInput( array( 'action' => 'resync', 'alias' => array( 'original' ), 'overwrite' => '1', 'dry_run' => '1' ), $known );
        $this->assertSame( array(), $scope['errors'] );
        $this->assertSame( array( 'alias' => array( 'original' ), 'overwrite' => true, 'dry_run' => true ), $scope['options'] );
        $this->assertFalse( $scope['preview'], 'a resync does its own dry run (it asks the source)' );

        $scope = expImageAliasAdmin::scopeFromInput( array( 'action' => 'generate', 'alias' => array( 'original' ), 'overwrite' => '1' ), $known );
        $this->assertArrayHasKey( 'alias', $scope['errors'], 'original is a resync alias only' );
        $this->assertSame( array( 'overwrite-means-regenerate' ), $scope['notes'] );

        $scope = expImageAliasAdmin::scopeFromInput( array( 'action' => 'delete', 'target' => 'node', 'node' => 'x', 'class' => 'a;b DROP',
                                                           'alias' => array( 'huge' ), 'since' => 'tomorrow', 'limit' => '0' ), $known );
        foreach ( array( 'action', 'node', 'alias', 'since', 'limit' ) as $field )
            $this->assertArrayHasKey( $field, $scope['errors'], $field );
        $this->assertArrayNotHasKey( 'class', $scope['errors'], 'a;b DROP are three identifiers' );
        $this->assertArrayHasKey( 'class', expImageAliasAdmin::scopeFromInput( array( 'action' => 'purge', 'class' => "a'b" ), $known )['errors'] );

        $scope = expImageAliasAdmin::scopeFromInput( array( 'action' => 'purge', 'target' => 'all' ), $known );
        $this->assertSame( array( 'action' => 'purge' ), $scope['options'], 'the whole site: no scope keys' );
        $this->assertSame( array( 'small', 'large', 'i160' ), expImageAliasAdmin::knownAliases( array( 'admin' => array( 'small', 'large' ), 'site' => array( 'large', 'i160' ) ) ) );
    }

    public function testChangedSince()
    {
        $now = mktime( 12, 0, 0, 11, 13, 2026 );
        $this->assertSame( 0, expImageAliasAdmin::parseSince( '', $now ) );
        $this->assertSame( $now - 2 * 86400, expImageAliasAdmin::parseSince( '2 days', $now ) );
        $this->assertSame( $now - 2 * 86400, expImageAliasAdmin::parseSince( '2d', $now ) );
        $this->assertSame( $now - 12 * 3600, expImageAliasAdmin::parseSince( '12 hours ago', $now ) );
        $this->assertSame( $now - 30 * 60, expImageAliasAdmin::parseSince( '30 minutes', $now ) );
        $this->assertSame( $now - 30 * 60, expImageAliasAdmin::parseSince( '30m', $now ) );
        $this->assertSame( $now - 604800, expImageAliasAdmin::parseSince( '1 week', $now ) );
        $this->assertSame( mktime( 0, 0, 0, 11, 1, 2026 ), expImageAliasAdmin::parseSince( '2026-11-01', $now ) );
        $this->assertSame( mktime( 14, 30, 0, 11, 1, 2026 ), expImageAliasAdmin::parseSince( '2026-11-01 14:30', $now ) );
        $this->assertFalse( expImageAliasAdmin::parseSince( '2026-12-01', $now ), 'not in the future' );
        $this->assertFalse( expImageAliasAdmin::parseSince( '2026-02-30', $now ) );
        $this->assertFalse( expImageAliasAdmin::parseSince( 'last monday', $now ) );
    }

    public function testTheRunHistory()
    {
        $line = function ( $time, $message ) {
            return expImageAliasLog::parse( '[ ' . date( 'M d Y H:i:s', $time ) . ' ][ site ][ /content/image/x ] ' . $message );
        };
        $now = mktime( 12, 0, 0, 11, 13, 2026 );
        // newest first, as expImageAliasLog::read() gives them
        $entries = array(
            $line( $now - 10, 'event=generate result=ok ms=84 alias=small run=- by=job path=var/site/storage/images/a/1-1-eng-GB/a_small.jpg' ),
            $line( $now - 20, 'event=placeholder result=timeout ms=2001 alias=large run=- path=var/site/storage/images/a/1-1-eng-GB/a_large.jpg' ),
            $line( $now - 21, 'event=defer result=timeout ms=2000 alias=small run=- path=var/site/storage/images/a/1-1-eng-GB/a_small.jpg' ),
            // written before the defer event: a timeout of an alias is read as defer, a staging fetch that timed out stays fail
            $line( $now - 22, 'event=fail result=timeout ms=2000 alias=small.webp run=- path=var/site/storage/images/a/1-1-eng-GB/a_small.jpg.webp' ),
            $line( $now - 23, 'event=fail result=timeout ms=5000 alias=- source=pro bytes=0 stored=0 path=var/site/storage/images/d/5-1-eng-GB/d.jpg' ),
            $line( $now - 30, 'event=fail result=failed ms=5 alias=large run=bulk20261113-aa path=var/site/storage/images/b/2-1-eng-GB/b_large.jpg' ),
            $line( $now - 40, 'event=stage result=fetched ms=19 alias=original run=rs-1 path=var/site/storage/images/c/3-1-eng-GB/c.jpg' ),
            $line( $now - 50, 'event=run-start result=generate ms=0 alias=- run=bulk20261113-aa origin=bulk path=subtree=93' ),
            $line( $now - 90000, 'event=generate result=ok ms=50 alias=small run=- path=var/site/storage/images/old/4-1-eng-GB/old_small.jpg' ),
        );
        $this->assertNotNull( $entries[0] );

        $filter = expImageAliasAdmin::logFilter( array( 'event' => 'GENERATE', 'run' => 'x y', 'alias' => '', 'text' => "a\x01b", 'lines' => '9999' ) );
        $this->assertSame( array( 'event' => 'generate', 'run' => '', 'alias' => '', 'text' => 'ab', 'lines' => expImageAliasAdmin::MAX_LINES ), $filter );
        $this->assertSame( '', expImageAliasAdmin::logFilter( array( 'event' => 'drop table' ) )['event'] );

        $kept = expImageAliasAdmin::filterLog( $entries, expImageAliasAdmin::logFilter( array( 'event' => 'generate' ) ) );
        $this->assertCount( 2, $kept );
        $kept = expImageAliasAdmin::filterLog( $entries, expImageAliasAdmin::logFilter( array( 'run' => 'bulk20261113-aa' ) ) );
        $this->assertSame( array( 'fail', 'run-start' ), array_column( $kept, 'event' ) );
        $kept = expImageAliasAdmin::filterLog( $entries, expImageAliasAdmin::logFilter( array( 'event' => 'runs' ) ) );
        $this->assertSame( array( 'run-start' ), array_column( $kept, 'event' ) );
        $kept = expImageAliasAdmin::filterLog( $entries, expImageAliasAdmin::logFilter( array( 'alias' => 'large', 'text' => '/B/' ) ) );
        $this->assertSame( array( 'fail' ), array_column( $kept, 'event' ), 'alias by prefix, text in the path without regard to case' );
        $this->assertCount( 3, expImageAliasAdmin::filterLog( $entries, array( 'lines' => 3 ) + expImageAliasAdmin::logFilter( array() ) ) );

        $public = expImageAliasAdmin::publicEntry( $entries[0] );
        $this->assertSame( 'by=job', $public['extra'] );
        $this->assertSame( '', $public['run'], 'the "-" of no run is no run' );

        // defer: a choice of the filter, kept apart from fail (older "fail result=timeout" lines with it)
        $this->assertSame( 'defer', expImageAliasAdmin::logFilter( array( 'event' => 'defer' ) )['event'] );
        $kept = expImageAliasAdmin::filterLog( $entries, expImageAliasAdmin::logFilter( array( 'event' => 'defer' ) ) );
        $this->assertSame( array( 'small', 'small.webp' ), array_column( $kept, 'alias' ) );
        $this->assertSame( 'defer', expImageAliasAdmin::publicEntry( $kept[1] )['event'] );
        $kept = expImageAliasAdmin::filterLog( $entries, expImageAliasAdmin::logFilter( array( 'event' => 'fail' ) ) );
        $this->assertSame( array( 'timeout', 'failed' ), array_column( $kept, 'result' ), 'the staging fetch and the real failure' );

        $counts = expImageAliasAdmin::logCounts( $entries, $now - 86400 );
        $this->assertSame( array( 'generated' => 1, 'placeholders' => 1, 'failures' => 2, 'deferred' => 2, 'staged' => 1, 'runs' => 1, 'lines' => 9,
                                  'oldest' => $now - 90000, 'complete' => true ), $counts );
    }

    private function raw( array $over = array() )
    {
        return $over + array(
            'mode' => 'on-demand', 'url_form' => 'token', 'delivery' => 'auto', 'max_wait' => 2, 'max_parallel' => 2, 'busy' => 0,
            'negotiate' => true, 'pre_generate' => true, 'placeholder' => true, 'swap_script' => true, 'preferred' => 'auto',
            'order' => array( 'imagick', 'imagemagick', 'gd' ),
            'converters' => array( 'gd' => array( 'webp', 'avif' ), 'imagick' => array( 'webp' ), 'imagemagick' => array() ),
            'staging' => array( 'environment' => 'pro', 'sync_from' => '', 'failures_before_pause' => 5 ), 'source' => null,
            'queue' => array( 'available' => true, 'types' => array( 'image.alias.bulk' => array( 'waiting' => 1, 'running' => 1, 'dead' => 0, 'registered' => true ),
                                                                     'image.alias.generate' => array( 'waiting' => 2, 'running' => 0, 'dead' => 0, 'registered' => true ) ) ),
            'bulk_pause' => null,
            'counts' => array( 'generated' => 3, 'placeholders' => 0, 'failures' => 0, 'deferred' => 0, 'staged' => 0, 'runs' => 0, 'lines' => 3, 'oldest' => 0, 'complete' => true ),
            'key' => array( 'exists' => true, 'modified' => 1800000000 - 10 * 86400, 'previous_until' => 0 ),
            'log_path' => 'var/log/imagealias.log', 'aliases' => 30 );
    }

    private function keys( array $dashboard )
    {
        return array_column( $dashboard['checks'], 'level', 'key' );
    }

    public function testTheDashboardsVerdicts()
    {
        $now = 1800000000;
        $d = expImageAliasAdmin::buildDashboard( $this->raw(), $now );
        $this->assertSame( array(), $d['checks'] );
        $this->assertSame( 'ok', $d['worst'] );
        $this->assertSame( 10, $d['key_age_days'] );
        $this->assertSame( array( 'waiting' => 3, 'running' => 1, 'dead' => 0 ), $d['queue_total'] );
        $this->assertSame( array( 'imagick', 'imagemagick', 'gd' ), array_column( $d['converter_rows'], 'converter' ) );
        $this->assertSame( 'none', $d['source_state'] );
        // aliases deferred to a job are no failures: no warning for them
        $d = expImageAliasAdmin::buildDashboard( $this->raw( array( 'counts' => array( 'deferred' => 3310 ) + $this->raw()['counts'] ) ), $now );
        $this->assertSame( array(), $d['checks'] );
        $this->assertSame( 3310, $d['counts']['deferred'] );

        $d = expImageAliasAdmin::buildDashboard( $this->raw( array(
            'mode' => 'render', 'converters' => array( 'gd' => array() ), 'bulk_pause' => array( 'since' => 1, 'by' => 'x', 'reason' => '' ),
            'queue' => array( 'available' => false, 'types' => array() ),
            'counts' => array( 'failures' => 2 ) + $this->raw()['counts'],
            'key' => array( 'exists' => true, 'modified' => $now - 400 * 86400, 'previous_until' => $now + 60 ) ) ), $now );
        $this->assertSame( array( 'render-mode' => 'info', 'no-webp' => 'warn', 'no-queue' => 'warn', 'bulk-paused' => 'warn', 'failures' => 'warn',
                                  'key-old' => 'info' ), $this->keys( $d ) );
        $this->assertSame( 'warn', $d['worst'] );
        $this->assertTrue( $d['previous_key_valid'] );

        $staging = array( 'environment' => 'dev', 'sync_from' => 'pro', 'failures_before_pause' => 5 );
        $d = expImageAliasAdmin::buildDashboard( $this->raw( array( 'staging' => $staging, 'source' => array( 'base_url' => '', 'paused' => false, 'failures' => 0, 'warning' => null ) ) ), $now );
        $this->assertSame( array( 'source-no-url' => 'bad' ), $this->keys( $d ) );
        $this->assertSame( 'bad', $d['worst'] );
        $d = expImageAliasAdmin::buildDashboard( $this->raw( array( 'staging' => $staging, 'source' => array( 'base_url' => 'https://pro.example.org', 'paused' => true, 'failures' => 5, 'warning' => 'x' ) ) ), $now );
        $this->assertSame( 'paused', $d['source_state'] );
        $d = expImageAliasAdmin::buildDashboard( $this->raw( array( 'staging' => $staging, 'source' => array( 'base_url' => 'https://pro.example.org', 'paused' => false, 'failures' => 2, 'warning' => null ) ) ), $now );
        $this->assertSame( array( 'source-failing' => 'warn' ), $this->keys( $d ) );
        $d = expImageAliasAdmin::buildDashboard( $this->raw( array( 'staging' => array( 'sync_from' => '' ) + $staging ) ), $now );
        $this->assertSame( array( 'staging-no-source' => 'info' ), $this->keys( $d ) );
        $d = expImageAliasAdmin::buildDashboard( $this->raw( array( 'key' => array( 'exists' => false, 'modified' => 0, 'previous_until' => 0 ) ) ), $now );
        $this->assertSame( array( 'no-key' => 'info' ), $this->keys( $d ) );
        $this->assertNull( $d['key_age_days'] );
    }

    public function testTheConnectionTestsVerdict()
    {
        $this->assertSame( 'ok', expImageAliasAdmin::sourceVerdict( 200, '' ) );
        $this->assertSame( 'unauthorized', expImageAliasAdmin::sourceVerdict( 401, '' ) );
        $this->assertSame( 'unauthorized', expImageAliasAdmin::sourceVerdict( 403, '' ) );
        $this->assertSame( 'missing', expImageAliasAdmin::sourceVerdict( 404, '' ) );
        $this->assertSame( 'redirect', expImageAliasAdmin::sourceVerdict( 302, '' ) );
        $this->assertSame( 'http', expImageAliasAdmin::sourceVerdict( 500, '' ) );
        $this->assertSame( 'down', expImageAliasAdmin::sourceVerdict( 0, 'down' ) );
        $this->assertSame( 'timeout', expImageAliasAdmin::sourceVerdict( 0, 'timeout' ) );
        $this->assertSame( 'bad-environment', expImageAliasAdmin::testSource( 'production' )['result'] );
    }

    public function testTheWiring()
    {
        $root = $this->kernelRoot;
        $Module = null;
        $ViewList = $FunctionList = array();
        include $root . 'kernel/setup/module.php';
        $this->assertSame( array( 'aliases' ), $ViewList['aliases']['functions'] );
        $this->assertSame( array( 'aliases' ), $ViewList['aliasesfeed']['functions'] );
        $this->assertArrayHasKey( 'aliases', $FunctionList );
        $this->assertArrayHasKey( 'managealiases', $FunctionList );
        $this->assertStringContainsString( ' or aliases', $ViewList['menu']['functions'][0], 'the Setup overview opens for setup/aliases alone' );
        foreach ( array( 'tab', 'job', 'event', 'run', 'node', 'object', 'lines' ) as $parameter )
            $this->assertArrayHasKey( $parameter, $ViewList['aliases']['unordered_params'] );
        foreach ( array( 'job', 'log', 'event', 'lines' ) as $parameter )
            $this->assertArrayHasKey( $parameter, $ViewList['aliasesfeed']['unordered_params'] );

        $menu = file_get_contents( $root . 'settings/menu.ini' );
        $this->assertMatchesRegularExpression( '/^GroupLinks_system\[\]=cache\nGroupLinks_system\[\]=image_aliases$/m', $menu, 'right after Cache' );
        $this->assertStringContainsString( "Links[image_aliases]=setup/aliases\n", $menu );
        $this->assertStringContainsString( "PolicyList_image_aliases[]=setup/aliases\n", $menu );

        foreach ( array( 'templates/setup/aliases.tpl', 'templates/setup/aliases_setting_row.tpl', 'templates/popupmenu/popup_sub_advance_menu.tpl' ) as $file )
        {
            $admin4 = file_get_contents( $root . 'design/admin4/' . $file );
            $this->assertSame( $admin4, file_get_contents( $root . 'design/admin/' . $file ), "$file is the same in design/admin and design/admin4" );
            $this->assertDoesNotMatchRegularExpression( '/<style\b|\sstyle="|<script\b(?![^>]*\bsrc=)/i', $admin4, "$file has no inline CSS or script" );
        }
        $page = file_get_contents( $root . 'design/admin4/templates/setup/aliases.tpl' );
        $this->assertStringContainsString( "ezscript( array( 'page/setup_aliases.js' ) )", $page );
        $this->assertFileExists( $root . 'design/admin4/javascript/page/setup_aliases.js' );
        $this->assertStringNotContainsString( '?', preg_replace( '/[\'"][^\'"]*\?[^\'"]*[\'"]/', '', (string)preg_replace( '/\{\*.*?\*\}/s', '', implode( "\n", preg_grep( '/ezurl/', explode( "\n", $page ) ) ) ) ), 'no query string in an address' );
        $row = file_get_contents( $root . 'design/admin4/templates/setup/aliases_setting_row.tpl' );
        $this->assertStringContainsString( "list=\"ia-secret-names\"", $row, 'a credential is a name picked from the secret store' );

        $popup = file_get_contents( $root . 'design/admin4/templates/popupmenu/popup_sub_advance_menu.tpl' );
        $this->assertStringContainsString( "'function', 'managealiases'", $popup, 'the context menu entries only with setup/managealiases' );
        foreach ( array( 'generate', 'regenerate', 'purge' ) as $action )
            foreach ( array( 'node', 'subtree' ) as $scope )
                $this->assertStringContainsString( "data-menu-form=\"menu-form-ia-$action-$scope\"", $popup );
        $this->assertStringContainsString( 'name="NodeActionButton"', $popup );
        $this->assertArrayHasKey( 'NodeActionButton', $ViewList['aliases']['single_post_actions'] );

        $css = file_get_contents( $root . 'design/admin4/stylesheets/modules/setup.css' );
        $this->assertStringContainsString( '.a4-setup-alias-path', $css );
        $this->assertSame( $css, file_get_contents( $root . 'design/admin/stylesheets/modules/setup.css' ) );
    }
}
