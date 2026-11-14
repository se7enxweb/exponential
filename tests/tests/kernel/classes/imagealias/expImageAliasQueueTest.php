<?php
/**
 * The background work on image aliases (expqueue jobs), its priorities, the conversion slots, the pause of bulk runs,
 * the union of aliases over related siteaccesses and the pre-generation decision, without a database:
 *
 *  IAQ-01 - image.alias.generate payloads: items in either form, kept only when valid, each once, at most MAX_ITEMS;
 *           aliases as a list or per siteaccess, never 'original' or a bad name; formats webp/avif; origin; run; what
 *           cannot run says why
 *  IAQ-02 - image.alias.bulk payloads (action, scope, aliases, formats, where to go on) and the resync payload as
 *           before; a siteaccess plan narrowed to some aliases
 *  IAQ-03 - priorities: a visitor's miss interactive 80, publishing editorial 60, the administration interactive 60,
 *           bulk 40; a class the site has not got falls back, else the type's class with the same priority
 *  IAQ-04 - conversion slots: at most MaxParallel at once (slots held by other open files count), the wait is bounded
 *           and reported, a freed slot is taken, reentrant in one process, no new conversion after the deadline,
 *           nothing counted in render mode, the hold reported for the log
 *  IAQ-05 - the union of AliasList[] over siteaccesses: the first siteaccess that lists an alias makes it; 'original'
 *           and bad names never; the order kept
 *  IAQ-06 - pre-generation after publishing: the decision (disabled, render mode, nothing to do, no queue -> skipped,
 *           queue), and which stored images need work (an alias missing, a variant missing; not an SVG, not an
 *           attribute without an image, not one that has everything)
 *  IAQ-07 - the pause of bulk runs (set, read, resumed) and the nice value of bulk runs
 *  IAQ-08 - a request whose conversion slots are all taken answers the alias in its own format within MaxWait and
 *           makes no variant; with a slot free the variant is made; a job (forJob()) waits for the same per-file lock
 *           as a request and for LockTimeout, not MaxWait
 *  IAQ-09 - the kernel registers the three job types for expqueue (settings/expqueue.ini) with the handler; the
 *           publish operation calls the pre-generation hook; the manager converts only inside a slot
 *  IAQ-10 - parallel requests for the aliases of one image keep each other's entries in the stored XML
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasQueueTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** The server with the delivery fixed and the variant step reachable */
class expImageAliasQueueTestServer extends expImageAliasServer
{
    public function deliveryMethod( $path )
    {
        return 'redirect';
    }

    public function makeVariant( $aliasPath, $format, $aliasName )
    {
        return $this->variant( $aliasPath, $format, $aliasName, true );
    }

    public static function forTestJob()
    {
        $server = new self();
        $server->job = true;
        return $server;
    }

    /** @var array the completions this request would hand to the queue (recorded, never queued: a test has no queue of its own) */
    public $completions = array();

    protected function queueCompletion( $aliasName, array $formats, $target )
    {
        if ( $this->job || $this->miss === null )
            return;
        $this->completions[] = array( $this->miss, $aliasName, $formats, $target );
    }
}

class expImageAliasQueueTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $base;

    /** @var string */
    private $dir;

    /** @var string|false */
    private $cwd = false;

    /** @var resource[] slot files held by the test (as another process would) */
    private $held = array();

    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->base = 'var/tmp/storage/images/exp_iaq_' . getmypid() . '_' . mt_rand();
        $this->dir = $this->base . '/7-1-eng-GB';
        mkdir( $this->dir, 0700, true );
        mkdir( $this->base . '/slots', 0700, true );
        expImageAliasLock::setDirectory( $this->base . '/locks' );
        expImageAliasLog::setDirectory( $this->base . '/log' );
        expImageAliasThrottle::setDirectory( $this->base . '/slots' );
        expImageAliasThrottle::setLimit( 2 );
        expImageAliasThrottle::setApplies( true );
        expImageAliasThrottle::setPauseStore( null );
        expImageAliasThrottle::reset();
    }

    protected function tearDown(): void
    {
        foreach ( $this->held as $handle )
        {
            @flock( $handle, LOCK_UN );
            @fclose( $handle );
        }
        $this->held = array();
        expImageAliasThrottle::reset();
        expImageAliasThrottle::setDirectory( null );
        expImageAliasThrottle::setLimit( null );
        expImageAliasThrottle::setApplies( null );
        expImageAliasThrottle::setPauseStore( false );
        ezpINIHelper::restoreINISettings();
        expImageFormatConverter::setCapabilities( null );
        expImageAliasUrl::resetKeys();
        expImageAliasLock::setDirectory( null );
        expImageAliasLog::setDirectory( null );
        expImageAliasLock::setWaitLimit( null );
        foreach ( array( $this->dir, $this->base . '/locks', $this->base . '/log', $this->base . '/slots' ) as $dir )
        {
            foreach ( array_merge( (array)glob( $dir . '/*' ), (array)glob( $dir . '/.*.tmp' ) ) as $file )
                @unlink( $file );
            @rmdir( $dir );
        }
        @rmdir( $this->base );
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    /** Holds slot $n through a file handle of its own, as another process does */
    private function holdSlot( $n )
    {
        $handle = fopen( $this->base . '/slots/slot-' . $n . '.lock', 'c' );
        $this->assertTrue( flock( $handle, LOCK_EX | LOCK_NB ), "slot $n is free" );
        $this->held[$n] = $handle;
    }

    private function freeSlot( $n )
    {
        flock( $this->held[$n], LOCK_UN );
        fclose( $this->held[$n] );
        unset( $this->held[$n] );
    }

    /** IAQ-01 */
    public function testGeneratePayload()
    {
        $payload = expImageAliasJob::generatePayload( array(
            'items' => array( array( 12, 3, 'eng-GB' ), array( 'attribute' => '13', 'version' => '1', 'language' => 'ger-DE' ),
                              array( 12, 3, 'eng-GB' ), array( 0, 1, 'eng-GB' ), array( 14, -1, 'eng-GB' ), array( 15, 1, '../x' ), 'junk' ),
            'aliases' => array( 'small', 'original', 'bad name', 'large', 'small' ),
            'formats' => array( 'WEBP', 'gif', 'avif', 'webp' ), 'origin' => 'visitor', 'run' => 'gen-1', 'evil' => 'x' ) );
        $this->assertSame( array( 'items' => array( array( 12, 3, 'eng-GB' ), array( 13, 1, 'ger-DE' ) ),
                                  'aliases' => array( '-' => array( 'small', 'large' ) ), 'formats' => array( 'webp', 'avif' ),
                                  'origin' => 'visitor', 'run' => 'gen-1' ), $payload );
        $this->assertNull( expImageAliasJob::generatePayloadError( $payload ) );

        // per siteaccess; an unknown origin is the administration; no formats key when none
        $payload = expImageAliasJob::generatePayload( array( 'items' => array( array( 1, 1, 'eng-GB' ) ),
                                                             'aliases' => array( 'admin' => array( 'small' ), 'site' => array( 'listitem', 'x y' ), 'bad sa' => array( 'large' ) ),
                                                             'origin' => 'robot' ) );
        $this->assertSame( array( 'admin' => array( 'small' ), 'site' => array( 'listitem' ) ), $payload['aliases'] );
        $this->assertSame( 'admin', $payload['origin'] );
        $this->assertArrayNotHasKey( 'formats', $payload );

        $this->assertSame( 'no image attribute (items)', expImageAliasJob::generatePayloadError( expImageAliasJob::generatePayload( array( 'aliases' => array( 'small' ) ) ) ) );
        $this->assertSame( 'no alias (aliases)', expImageAliasJob::generatePayloadError( expImageAliasJob::generatePayload( array( 'items' => array( array( 1, 1, 'eng-GB' ) ) ) ) ) );
        $this->assertNotNull( expImageAliasJob::generatePayloadError( array( 'items' => array( array( 1, 1, 'eng-GB' ) ), 'aliases' => array( '-' => array( 'small' ) ),
                                                                             'origin' => 'admin', 'extra' => 1 ) ), 'a key it does not take' );

        $many = array();
        for ( $i = 1; $i <= expImageAliasJob::MAX_ITEMS + 20; $i++ )
            $many[] = array( $i, 1, 'eng-GB' );
        $this->assertCount( expImageAliasJob::MAX_ITEMS, expImageAliasJob::generatePayload( array( 'items' => $many ) )['items'] );
    }

    /** IAQ-02 */
    public function testBulkAndResyncPayloads()
    {
        $payload = expImageAliasJob::bulkPayload( array( 'action' => 'regenerate', 'subtree' => '92', 'node' => 0, 'since' => 'x', 'limit' => 50,
                                                         'class' => array( 'article', 'bad class' ), 'attribute' => 'image', 'alias' => array( 'small', 'small' ),
                                                         'aliases' => array( 'site' => array( 'small' ) ), 'formats' => array( 'webp' ),
                                                         'after' => array( '1500', 'eng-US' ), 'run' => 'bulk-1', 'dry' => true ) );
        $this->assertSame( array( 'action' => 'regenerate', 'subtree' => 92, 'limit' => 50, 'class' => array( 'article' ), 'attribute' => array( 'image' ),
                                  'alias' => array( 'small' ), 'aliases' => array( 'site' => array( 'small' ) ), 'formats' => array( 'webp' ),
                                  'after' => array( 1500, 'eng-US' ), 'run' => 'bulk-1' ), $payload );
        $this->assertSame( 'generate', expImageAliasJob::bulkPayload( array( 'action' => 'delete-all' ) )['action'], 'an unknown action is generate' );
        $this->assertArrayNotHasKey( 'after', expImageAliasJob::bulkPayload( array( 'after' => array( 0, 'eng-GB' ) ) ) );
        $this->assertSame( array( 'subtree' => 2, 'alias' => array( 'small', 'original' ), 'run' => 'r1' ),
                           expImageAliasJob::resyncPayload( array( 'subtree' => '2', 'alias' => array( 'small', 'original', 'bad name' ), 'run' => 'r1' ) ) );

        $plan = array( 'admin' => array( 'small', 'large' ), 'site' => array( 'listitem', 'billboard' ) );
        $this->assertSame( $plan, expImageAliasJob::filterPlan( $plan, array() ) );
        $this->assertSame( array( 'admin' => array( 'large' ), 'site' => array( 'billboard' ) ), expImageAliasJob::filterPlan( $plan, array( 'billboard', 'large', 'nope' ) ) );
        $this->assertSame( array(), expImageAliasJob::filterPlan( $plan, array( 'nope' ) ) );
    }

    /** IAQ-03 */
    public function testPriorities()
    {
        $all = array( 'interactive', 'editorial', 'chain', 'bulk', 'system' );
        $visitor = expImageAliasJob::priorityFor( 'visitor', $all );
        $publish = expImageAliasJob::priorityFor( 'publish', $all );
        $admin = expImageAliasJob::priorityFor( 'admin', $all );
        $bulk = expImageAliasJob::priorityFor( 'bulk', $all );
        $this->assertSame( array( 'class' => 'interactive', 'priority' => 80 ), $visitor );
        $this->assertSame( array( 'class' => 'editorial', 'priority' => 60 ), $publish );
        $this->assertSame( array( 'class' => 'interactive', 'priority' => 60 ), $admin );
        $this->assertSame( array( 'class' => 'bulk', 'priority' => 40 ), $bulk );
        $this->assertGreaterThan( $bulk['priority'], $visitor['priority'] );
        $this->assertGreaterThan( $publish['priority'], $visitor['priority'] );
        // a site without the class interactive: the next one; without any of them the type's class
        $this->assertSame( array( 'class' => 'editorial', 'priority' => 80 ), expImageAliasJob::priorityFor( 'visitor', array( 'editorial', 'bulk' ) ) );
        $this->assertSame( array( 'class' => null, 'priority' => 40 ), expImageAliasJob::priorityFor( 'bulk', array( 'interactive' ) ) );
        $this->assertSame( expImageAliasJob::priorityFor( 'admin', $all ), expImageAliasJob::priorityFor( 'unknown', $all ) );
        // expqueue's own weights put interactive far ahead of bulk (stride scheduling)
        if ( class_exists( 'expQueueConfig' ) )
        {
            $weights = expQueueConfig::DEFAULT_CLASSES;
            $this->assertGreaterThan( $weights['bulk'], $weights['interactive'] );
            $this->assertGreaterThan( $weights['bulk'], $weights['editorial'] );
        }
    }

    /** IAQ-04 */
    public function testSlots()
    {
        // two other processes convert: the third waits, within its limit, and gives up
        $this->holdSlot( 0 );
        $this->holdSlot( 1 );
        $this->assertSame( 2, expImageAliasThrottle::busy() );
        $t = microtime( true );
        $this->assertFalse( expImageAliasThrottle::acquire( 0.3 ) );
        $waited = microtime( true ) - $t;
        $this->assertTrue( expImageAliasThrottle::timedOut() );
        $this->assertGreaterThanOrEqual( 0.29, $waited );
        $this->assertLessThan( 1.5, $waited );
        $this->assertNull( expImageAliasThrottle::heldSlot() );

        // one is freed: taken, reported for the log, reentrant, given back
        $this->freeSlot( 1 );
        expImageAliasThrottle::forgetLastHold();
        $this->assertTrue( expImageAliasThrottle::acquire( 0.3 ) );
        $this->assertFalse( expImageAliasThrottle::timedOut() );
        $this->assertSame( 1, expImageAliasThrottle::heldSlot() );
        $this->assertSame( 2, expImageAliasThrottle::busy() );
        $this->assertTrue( expImageAliasThrottle::acquire( 0 ), 'the reference alias on the way uses the slot held' );
        expImageAliasThrottle::release();
        $this->assertSame( 1, expImageAliasThrottle::heldSlot() );
        expImageAliasThrottle::release();
        $this->assertNull( expImageAliasThrottle::heldSlot() );
        $hold = expImageAliasThrottle::lastHold();
        $this->assertSame( 1, $hold['slot'] );
        $this->assertNotNull( $hold['until'] );
        $this->assertGreaterThanOrEqual( $hold['from'], $hold['until'] );
        $this->assertSame( 1, expImageAliasThrottle::busy() );

        // MaxParallel 3: a third slot file
        expImageAliasThrottle::setLimit( 3 );
        $this->holdSlot( 1 );
        $this->assertTrue( expImageAliasThrottle::acquire( 0.1 ) );
        $this->assertSame( 2, expImageAliasThrottle::heldSlot() );
        expImageAliasThrottle::release();
        expImageAliasThrottle::setLimit( 2 );

        // the deadline of a request: no new conversion after it, even with a slot free
        $this->freeSlot( 1 );
        $before = expImageAliasThrottle::setDeadline( microtime( true ) - 0.01 );
        $this->assertFalse( expImageAliasThrottle::acquire( 5 ) );
        $this->assertTrue( expImageAliasThrottle::timedOut() );
        // a deadline shorter than the wait bounds it
        $this->holdSlot( 1 );
        expImageAliasThrottle::setDeadline( microtime( true ) + 0.2 );
        $t = microtime( true );
        $this->assertFalse( expImageAliasThrottle::acquire( 5 ) );
        $this->assertLessThan( 1.0, microtime( true ) - $t );
        expImageAliasThrottle::setDeadline( $before );

        // render mode: nothing counted, nothing waited for
        expImageAliasThrottle::setApplies( false );
        $this->assertTrue( expImageAliasThrottle::acquire( 0 ) );
        $this->assertNull( expImageAliasThrottle::heldSlot() );
        expImageAliasThrottle::release();
        expImageAliasThrottle::setApplies( null );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'AliasGeneration', 'render' );
        $this->assertFalse( expImageAliasThrottle::applies() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'AliasGeneration', 'on-demand' );
        $this->assertTrue( expImageAliasThrottle::applies(), 'on-demand counts in every process, the command line too' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'MaxParallel', '99' );
        expImageAliasThrottle::setLimit( null );
        $this->assertSame( 64, expImageAliasThrottle::limit(), 'bounded' );
        $this->assertMatchesRegularExpression( '#/imagealias-slots/[A-Za-z0-9_.-]+$#', ( function () {
            expImageAliasThrottle::setDirectory( null );
            $directory = expImageAliasThrottle::directory();
            expImageAliasThrottle::setDirectory( $this->base . '/slots' );
            return $directory;
        } )(), 'per server' );
    }

    /** IAQ-05 */
    public function testSiteAccessAliasUnion()
    {
        $plan = expImageAliasSettings::aliasUnion( array(
            'admin' => array( 'reference', 'small', 'original', 'large', 'small' ),
            'site' => array( 'small', 'listitem', 'bad name', 'billboard' ),
            'starter' => array( 'listitem', 'billboard' ),
            'eng' => array( 'opengraph' ) ) );
        $this->assertSame( array( 'admin' => array( 'reference', 'small', 'large' ), 'site' => array( 'listitem', 'billboard' ),
                                  'eng' => array( 'opengraph' ) ), $plan );
        $this->assertSame( array(), expImageAliasSettings::aliasUnion( array( 'a' => array(), 'b' => array( 'original' ) ) ) );
        // as a generate payload it keeps the siteaccesses
        $payload = expImageAliasJob::generatePayload( array( 'items' => array( array( 1, 1, 'eng-GB' ) ), 'aliases' => $plan ) );
        $this->assertSame( $plan, $payload['aliases'] );
        // the related siteaccesses: this one first, each once, bad names left out
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'RelatedSiteAccessList', array( 'site', 'bad name', 'admin', 'site' ) );
        $related = expImageAliasSettings::relatedSiteAccesses();
        $current = expImageAliasSettings::currentSiteAccess();
        $this->assertSame( array_values( array_unique( array_merge( $current !== '' ? array( $current ) : array(), array( 'site', 'admin' ) ) ) ), $related );
        // the current siteaccess runs as it is (no image.ini swapped)
        $this->assertSame( 'same', expImageAliasSettings::withSiteAccess( '', function () { return 'same'; } ) );
        $this->assertSame( 'same', expImageAliasSettings::withSiteAccess( 'bad name', function () { return 'same'; } ) );
    }

    /** IAQ-06 */
    public function testPreGenerateDecision()
    {
        $this->assertSame( 'disabled', expImageAliasPublish::decide( false, true, 3, true ) );
        $this->assertSame( 'render-mode', expImageAliasPublish::decide( true, false, 3, true ) );
        $this->assertSame( 'nothing', expImageAliasPublish::decide( true, true, 0, true ) );
        $this->assertSame( 'no-queue', expImageAliasPublish::decide( true, true, 2, false ), 'no queue: skipped, never converted while publishing' );
        $this->assertSame( 'queue', expImageAliasPublish::decide( true, true, 1, true ) );

        $plan = array( 'admin' => array( 'small', 'large' ), 'site' => array( 'listitem' ) );
        $xml = function ( $original, array $aliases ) {
            $out = '<?xml version="1.0" encoding="utf-8"?><ezimage serial_number="1" is_valid="1" filename="a.jpg" suffix="jpg" basename="a"'
                 . ' dirpath="' . $this->dir . '" url="' . $original . '" original_filename="a.jpg" mime_type="image/jpeg" width="10" height="10"'
                 . ' alternative_text="" alias_key="1293033771" timestamp="1">';
            foreach ( $aliases as $name )
                $out .= '<alias name="' . $name . '" filename="a_' . $name . '.jpg" suffix="jpg" dirpath="' . $this->dir . '" url="' . $this->dir . '/a_' . $name
                      . '.jpg" mime_type="image/jpeg" width="5" height="5" alias_key="1" timestamp="1" is_valid="1"/>';
            return $out . '</ezimage>';
        };
        $this->assertFalse( expImageAliasPublish::needsWork( '', $plan ), 'no image' );
        $this->assertFalse( expImageAliasPublish::needsWork( $xml( '', array() ), $plan ), 'an attribute without an image' );
        $this->assertTrue( expImageAliasPublish::needsWork( $xml( $this->dir . '/a.jpg', array( 'small' ) ), $plan ), 'large and listitem missing' );
        $this->assertFalse( expImageAliasPublish::needsWork( $xml( $this->dir . '/a.svg', array() ), $plan ), 'an SVG has no aliases of its own' );
        $this->assertFalse( expImageAliasPublish::needsWork( $xml( $this->dir . '/a.jpg', array( 'small', 'large', 'listitem' ) ), $plan ), 'all there' );

        // a variant the alias block lists and a converter writes, missing: work; there: none
        ezpINIHelper::setINISetting( 'image.ini', 'small', 'Formats', array( 'webp' ) );
        expImageFormatConverter::setCapabilities( array( 'gd' => array( 'webp' ), 'imagick' => array(), 'imagemagick' => array() ) );
        $this->assertTrue( expImageAliasPublish::needsWork( $xml( $this->dir . '/a.jpg', array( 'small', 'large', 'listitem' ) ), $plan ) );
        file_put_contents( $this->dir . '/a_small.jpg.webp', 'RIFF' );
        $this->assertFalse( expImageAliasPublish::needsWork( $xml( $this->dir . '/a.jpg', array( 'small', 'large', 'listitem' ) ), $plan ) );
        expImageFormatConverter::setCapabilities( array( 'gd' => array(), 'imagick' => array(), 'imagemagick' => array() ) );
        unlink( $this->dir . '/a_small.jpg.webp' );
        $this->assertFalse( expImageAliasPublish::needsWork( $xml( $this->dir . '/a.jpg', array( 'small', 'large', 'listitem' ) ), $plan ), 'nobody writes WebP here' );

        // disabled or render mode: decided before anything is read
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'PreGenerate', 'disabled' );
        $this->assertSame( 'disabled', expImageAliasPublish::published( 1, 1 ) );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'PreGenerate', 'enabled' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'AliasGeneration', 'render' );
        $this->assertSame( 'render-mode', expImageAliasPublish::published( 1, 1 ) );
    }

    /** IAQ-07 */
    public function testBulkPauseAndNice()
    {
        $this->assertFalse( expImageAliasThrottle::isBulkPaused() );
        $this->assertTrue( expImageAliasThrottle::pauseBulk( 'night backup', 'admin' ) );
        $this->assertTrue( expImageAliasThrottle::isBulkPaused() );
        $pause = expImageAliasThrottle::bulkPause();
        $this->assertSame( 'night backup', $pause['reason'] );
        $this->assertSame( 'admin', $pause['by'] );
        $this->assertGreaterThan( 0, $pause['since'] );
        $this->assertTrue( expImageAliasThrottle::resumeBulk( 'admin' ) );
        $this->assertFalse( expImageAliasThrottle::isBulkPaused() );
        $this->assertFalse( expImageAliasThrottle::resumeBulk(), 'not paused' );

        $this->assertSame( 10, expImageAliasThrottle::bulkNice() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'BulkNice', '40' );
        $this->assertSame( 19, expImageAliasThrottle::bulkNice() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'BulkNice', '0' );
        $this->assertNull( expImageAliasThrottle::lowerPriority(), '0 leaves the priority alone' );
        $this->assertFalse( expImageAliasThrottle::restorePriority( null ) );
    }

    /** IAQ-08 */
    public function testRequestAndJobUseSlotsAndTheSameLock()
    {
        $converters = expImageFormatConverter::convertersFor( 'webp', 'auto' );
        if ( !$converters )
            $this->markTestSkipped( 'No converter on this machine writes WebP' );
        expImageFormatConverter::setCapabilities( array( $converters[0] => array( 'webp' ) ) );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->dir . '/andernach.jpg' );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->dir . '/andernach_small.jpg' );
        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        ezpINIHelper::setINISetting( 'image.ini', 'small', 'Formats', array( 'webp' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'Negotiate', 'enabled' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'MaxWait', '0.4' );
        $token = expImageAliasUrl::token( array( 'a' => 7, 'v' => 1, 'l' => 'eng-GB', 'n' => 'small', 'o' => $this->dir . '/andernach.jpg',
                                                 'u' => 'andernach_small.jpg', 'w' => 160, 'h' => 120, 'm' => 'image/jpeg' ) );
        $variant = $this->dir . '/andernach_small.jpg.webp';

        // every slot taken: within MaxWait the browser gets the alias itself, no variant is made
        $this->holdSlot( 0 );
        $this->holdSlot( 1 );
        $server = new expImageAliasQueueTestServer();
        $server->setAccept( 'image/webp,*/*' );
        $t = microtime( true );
        $response = $server->handleParams( array( $token, 'andernach_small.jpg' ) );
        $this->assertLessThan( 1.5, microtime( true ) - $t, 'MaxWait bounds the wait for a slot' );
        $this->assertSame( 302, $response['status'] );
        $this->assertStringEndsWith( 'andernach_small.jpg', $response['headers']['Location'] );
        $this->assertSame( 'Accept', $response['headers']['Vary'] );
        $this->assertFileDoesNotExist( $variant );
        $log = (string)file_get_contents( expImageAliasLog::path() );
        $this->assertStringContainsString( 'event=defer result=timeout', $log, 'no slot in time: deferred to a job' );
        $this->assertStringNotContainsString( 'event=fail', $log );
        $this->assertSame( array( array( array( 'attribute' => 7, 'version' => 1, 'language' => 'eng-GB' ), 'small', array( 'webp' ), $this->dir . '/andernach_small.jpg' ) ),
                           $server->completions, 'the variant is handed to the queue at the visitors\' priority' );

        // a slot free: the variant is made, in that slot
        $this->freeSlot( 1 );
        $server = new expImageAliasQueueTestServer();
        $server->setAccept( 'image/webp,*/*' );
        $response = $server->handleParams( array( $token, 'andernach_small.jpg' ) );
        $this->assertSame( 302, $response['status'] );
        $this->assertStringEndsWith( 'andernach_small.jpg.webp', $response['headers']['Location'] );
        $this->assertFileExists( $variant );
        $this->assertMatchesRegularExpression( '/event=generate result=ok .*slot=1 held_at=[0-9.]+ held_ms=\d+ slot_wait_ms=\d+/', (string)file_get_contents( expImageAliasLog::path() ) );
        unlink( $variant );
        $this->freeSlot( 0 );

        // a job takes the same per-file lock as a request, and waits LockTimeout (not MaxWait) for it
        ezpINIHelper::setINISetting( 'image.ini', 'ImageConverterSettings', 'LockTimeout', '0.3' );
        $lock = new expImageAliasLock( $variant );
        $this->assertTrue( $lock->acquire( 0 ) );
        $job = expImageAliasQueueTestServer::forTestJob();
        $t = microtime( true );
        $this->assertNull( $job->makeVariant( $this->dir . '/andernach_small.jpg', 'webp', 'small' ) );
        $this->assertGreaterThanOrEqual( 0.29, microtime( true ) - $t );
        $this->assertSame( array(), $job->completions, 'a job queues nothing' );
        $lock->release();
        $this->assertSame( $variant, $job->makeVariant( $this->dir . '/andernach_small.jpg', 'webp', 'small' ) );
        $this->assertMatchesRegularExpression( '/event=generate result=ok .*by=job path=' . preg_quote( $variant, '/' ) . '/', (string)file_get_contents( expImageAliasLog::path() ) );
    }

    /** IAQ-10 */
    public function testParallelAliasWritesKeepEachOther()
    {
        $xml = function ( array $aliases ) {
            $out = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<ezimage serial_number="1" is_valid="1" filename="a.jpg" url="var/s/storage/images/a/7-1-eng-GB/a.jpg" dirpath="var/s/storage/images/a/7-1-eng-GB">';
            foreach ( $aliases as $name => $width )
                $out .= '<alias name="' . $name . '" url="var/s/storage/images/a/7-1-eng-GB/a_' . $name . '.jpg" width="' . $width . '"/>';
            return $out . '</ezimage>';
        };
        // this process read the row before another one stored "large"; it made "small" (and has an older "tiny")
        $memory = new DOMDocument();
        $memory->loadXML( $xml( array( 'tiny' => 10, 'small' => 100 ) ) );
        $this->assertSame( 2, eZImageAliasHandler::mergeStoredAliases( $xml( array( 'tiny' => 11, 'large' => 600, 'rss' => 88 ) ), $memory ) );
        $names = array();
        foreach ( $memory->getElementsByTagName( 'alias' ) as $node )
            $names[$node->getAttribute( 'name' )] = $node->getAttribute( 'width' );
        $this->assertSame( array( 'tiny' => '10', 'small' => '100', 'large' => '600', 'rss' => '88' ), $names, 'its own entries win, the others are kept' );
        $this->assertSame( 0, eZImageAliasHandler::mergeStoredAliases( '', $memory ) );
        $this->assertSame( 0, eZImageAliasHandler::mergeStoredAliases( 'not xml', $memory ) );
        $this->assertSame( 0, eZImageAliasHandler::mergeStoredAliases( $xml( array( 'large' => 1 ) ), $memory ), 'nothing new' );
    }

    /** IAQ-09 */
    public function testRegistrationAndHooks()
    {
        $root = dirname( __DIR__, 5 );
        $ini = file_get_contents( $root . '/settings/expqueue.ini' );
        foreach ( array( 'image.alias.generate' => 'editorial', 'image.alias.bulk' => 'bulk', 'image.alias.resync' => 'bulk' ) as $type => $class )
        {
            $this->assertSame( 1, preg_match( '/\[JobType_' . preg_quote( $type, '/' ) . '\]\n(.*?)(?:\n\n|\z)/s', $ini, $m ), $type );
            $this->assertStringContainsString( "Handler=expImageAliasQueueHandler\n", $m[1] . "\n" );
            $this->assertStringContainsString( "Class=$class\n", $m[1] . "\n" );
        }
        $this->assertStringContainsString( 'expImageAliasPublish::published( (int)$objectID, (int)$versionNum )',
                                           file_get_contents( $root . '/kernel/content/ezcontentoperationcollection.php' ) );
        $manager = file_get_contents( $root . '/lib/ezimage/classes/ezimagemanager.php' );
        $this->assertMatchesRegularExpression( '/expImageAliasThrottle::acquire\(\).*\$this->convert\( \$sourceMimeData.*expImageAliasThrottle::release\(\)/s', $manager );
        $this->assertContains( 'queue', expImageAliasLog::EVENTS );
        $this->assertContains( 'pause', expImageAliasLog::EVENTS );
        $this->assertContains( 'resume', expImageAliasLog::EVENTS );
        // without expqueue the job types are not taken and nothing is queued for a miss
        if ( !class_exists( 'expQueue' ) )
        {
            $this->assertFalse( expImageAliasJob::queueAvailable( expImageAliasJob::TYPE_GENERATE ) );
            $this->assertNull( expImageAliasJob::queueMiss( 1, 1, 'eng-GB', 'small' ) );
        }
    }
}
