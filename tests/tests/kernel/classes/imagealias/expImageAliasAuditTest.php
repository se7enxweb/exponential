<?php
/**
 * What the runtime audit of on-demand image aliases (19 November 2026) found and fixed, without a database:
 *
 *  IAX-01 - index_image.php answers only files of the image storage (images/, images-versioned/): a storage path
 *           elsewhere (original/application, packages), which the web server keeps behind content/download, gets the
 *           placeholder with 404, whether the file is there or not
 *  IAX-02 - a token whose signature fails names no file outside the image storage, and its m never sets the
 *           Content-Type (the type comes from the file name)
 *  IAX-03 - the optional fields of a payload are dropped unless they have the kernel's type (w, h, r, x whole numbers,
 *           m a media type); a placeholder size that is not a finite number is the default size
 *  IAX-04 - an entry taken from the backlog carries the time it was taken, so recoverStale() does not give it back
 *           while a drainer makes it, however long it waited before
 *  IAX-05 - a signature the kept keys reject is checked against the store again only when they were read more than
 *           KEY_RECHECK seconds ago (old or forged URLs do not send every request to the database), and that check
 *           never loops when the store has no key
 *  IAX-06 - a bulk run or resync the queue refuses (a quota, a database error) runs nowhere when the caller says it
 *           must not run in the request (the admin page for more than SYNC_LIMIT images): mode refused, no exception;
 *           a queue that takes it gives mode queue and the job
 *  IAX-07 - the swap script finds an image whose srcset names it by its path (as ezroot writes it), not only by the
 *           absolute URL the browser reports, and a placeholder found after it gave up on the others starts it again
 *  IAX-08 - a queued bulk run that finds bulk runs paused is deferred at once: no checkpoint read, no scope read, no
 *           run-start in the log (it was asked again every PAUSE_RECHECK seconds)
 *  IAX-09 - the aliases a page queues for its page placeholders are written to the backlog after the answer, in the
 *           order they were added, each once, and not when one is already waiting; where the request cannot answer
 *           first they are written at once
 *  IAX-10 - a drainer that runs out of BacklogTime with entries still waiting starts the next drainers itself; one
 *           that made nothing in its run leaves them to the cronjob part
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasAuditTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** The server with the delivery fixed */
class expImageAliasAuditTestServer extends expImageAliasServer
{
    /** @var string */
    public $method = 'stream';

    public function deliveryMethod( $path )
    {
        return $this->method;
    }
}

/** A queued job as expQueue::enqueue() returns it */
class expImageAliasAuditTestJob
{
    public function attribute( $name )
    {
        return $name === 'id' ? 4711 : null;
    }
}

class expImageAliasAuditTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $base;

    /** @var string */
    private $images;

    /** @var string */
    private $original;

    /** @var string|false */
    private $cwd = false;

    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $id = 'exp_iax_' . getmypid() . '_' . mt_rand();
        $this->base = 'var/tmp/' . $id;
        // var/<one directory>/storage/..., as validStoragePath() takes them
        $this->images = 'var/tmp/storage/images/' . $id . '/5-1-eng-GB';
        $this->original = 'var/tmp/storage/original/' . $id;
        mkdir( $this->images, 0700, true );
        mkdir( $this->original, 0700, true );
        mkdir( $this->base . '/backlog', 0700, true );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->images . '/andernach.jpg' );
        copy( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg', $this->images . '/andernach_small.jpg' );
        file_put_contents( $this->original . '/secret.pdf', "%PDF-1.4 not public\n" );
        file_put_contents( $this->original . '/page.html', "<script>alert(1)</script>\n" );
        expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
        expImageAliasLock::setDirectory( $this->base . '/locks' );
        expImageAliasLog::setDirectory( $this->base . '/log' );
        expImageAliasBacklog::setDirectory( $this->base . '/backlog' );
    }

    protected function tearDown(): void
    {
        expImageAliasUrl::resetKeys();
        expImageAliasJob::setQueue( null );
        expImageAliasLock::setDirectory( null );
        expImageAliasLog::setDirectory( null );
        expImageAliasBacklog::setDirectory( null );
        self::removeTree( $this->base );
        self::removeTree( dirname( $this->images ) );
        self::removeTree( $this->original );
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    private static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( (array)scandir( $dir ) as $name )
        {
            if ( $name === '.' || $name === '..' )
                continue;
            $path = $dir . '/' . $name;
            if ( is_dir( $path ) && !is_link( $path ) )
                self::removeTree( $path );
            else
                @unlink( $path );
        }
        @rmdir( $dir );
    }

    private static function params( $url )
    {
        return explode( '/', substr( $url, strpos( $url, 'content/image/' ) + strlen( 'content/image/' ) ) );
    }

    private function payload( $original, $file, array $extra = array() )
    {
        return $extra + array( 'a' => 5, 'v' => 1, 'l' => 'eng-GB', 'n' => 'small', 'o' => $original, 'u' => $file,
                               'w' => 160, 'h' => 120, 'm' => 'image/jpeg' );
    }

    /** IAX-01 */
    public function testOnlyTheImageStorageIsAnswered()
    {
        $this->assertTrue( expImageAliasUrl::validImagePath( 'var/site/storage/images/a/1-1-eng-GB/a_small.jpg' ) );
        $this->assertTrue( expImageAliasUrl::validImagePath( 'var/storage/images-versioned/1/2-eng-GB/a_small.jpg' ) );
        foreach ( array( 'var/site/storage/original/application/x.pdf', 'var/storage/packages/a/b.xml', 'var/site/storage/images',
                         'var/site/storage/imagesX/a.jpg', 'var/site/storage/images/../original/x.pdf' ) as $path )
            $this->assertFalse( expImageAliasUrl::validImagePath( $path ), $path );

        foreach ( array( '/' . $this->original . '/secret.pdf', '/index_image.php/' . $this->original . '/secret.pdf',
                         '/' . $this->original . '/page.html' ) as $request )
        {
            $response = ( new expImageAliasAuditTestServer() )->handleStoragePath( $request );
            $this->assertSame( 404, $response['status'], $request );
            $this->assertArrayHasKey( 'X-Image-Placeholder', $response['headers'], $request );
            $this->assertNull( $response['file'], $request );
        }
        // An image of the storage still is
        $response = ( new expImageAliasAuditTestServer() )->handleStoragePath( '/index_image.php/' . $this->images . '/andernach_small.jpg' );
        $this->assertSame( 200, $response['status'] );
        $this->assertSame( $this->images . '/andernach_small.jpg', $response['file'] );
    }

    /** IAX-02 */
    public function testAForgedTokenNamesNoOtherFileAndNoType()
    {
        // Signed with a key the server does not have
        $forged = function ( array $payload ) {
            $keep = str_repeat( 'x', 64 );
            expImageAliasUrl::setKeys( $keep );
            $url = expImageAliasUrl::build( $payload, 'token', array( 'prefix' => '' ) );
            expImageAliasUrl::setKeys( str_repeat( 's', 64 ) );
            return $url;
        };
        foreach ( array( 'secret.pdf', 'page.html' ) as $file )
        {
            $url = $forged( $this->payload( $this->original . '/x.jpg', $file, array( 'm' => 'text/html' ) ) );
            $response = ( new expImageAliasAuditTestServer() )->handleParams( self::params( $url ) );
            $this->assertSame( 404, $response['status'], $file );
            $this->assertNull( $response['file'], $file );
            $this->assertStringStartsWith( 'image/svg+xml', $response['headers']['Content-Type'], $file );
        }
        // An existing image is still served for an old or wrong signature, but typed by its name
        $url = $forged( $this->payload( $this->images . '/andernach.jpg', 'andernach_small.jpg', array( 'm' => 'text/html' ) ) );
        $response = ( new expImageAliasAuditTestServer() )->handleParams( self::params( $url ) );
        $this->assertSame( 200, $response['status'] );
        $this->assertSame( 'image/jpeg', $response['headers']['Content-Type'] );
        // A valid token keeps its signed type
        $url = expImageAliasUrl::build( $this->payload( $this->images . '/andernach.jpg', 'andernach_small.jpg' ), 'token', array( 'prefix' => '' ) );
        $response = ( new expImageAliasAuditTestServer() )->handleParams( self::params( $url ) );
        $this->assertSame( 200, $response['status'] );
        $this->assertSame( 'image/jpeg', $response['headers']['Content-Type'] );
    }

    /** IAX-03 */
    public function testPayloadFieldsAndSizesAreChecked()
    {
        $base = array( 'a' => 5, 'v' => 1, 'l' => 'eng-GB', 'n' => 'small', 'o' => 'var/site/storage/images/a/5-1-eng-GB/a.jpg', 'u' => 'a_small.jpg' );
        $part = expImageAliasUrl::base64UrlEncode( json_encode( $base + array( 'w' => array( 1 ), 'h' => '12x', 'm' => array( 'text/html' ),
                                                                              'r' => 1.5, 'x' => 'soon' ) ) );
        $payload = expImageAliasUrl::decodePayload( $part );
        $this->assertIsArray( $payload );
        foreach ( array( 'w', 'h', 'm', 'r', 'x' ) as $key )
            $this->assertArrayNotHasKey( $key, $payload, $key );
        $payload = expImageAliasUrl::decodePayload( expImageAliasUrl::base64UrlEncode( json_encode( $base + array( 'w' => 160, 'h' => '106', 'm' => 'image/svg+xml', 'x' => 1800000000 ) ) ) );
        $this->assertSame( 160, $payload['w'] );
        $this->assertSame( '106', $payload['h'] );
        $this->assertSame( 'image/svg+xml', $payload['m'] );
        $this->assertSame( 1800000000, $payload['x'] );
        // 1e400 is INF after json_decode
        $payload = expImageAliasUrl::decodePayload( expImageAliasUrl::base64UrlEncode( str_replace( '"u":"a_small.jpg"', '"u":"a_small.jpg","w":1e400', json_encode( $base, JSON_UNESCAPED_SLASHES ) ) ) );
        $this->assertArrayNotHasKey( 'w', $payload );

        $default = expImagePlaceholder::size( null, null );
        $this->assertSame( $default, expImagePlaceholder::size( INF, NAN ) );
        $this->assertSame( $default, expImagePlaceholder::size( '1e400', -INF ) );
        $this->assertSame( array( 160, 106 ), expImagePlaceholder::size( 160, 106 ) );
    }

    /** IAX-04 */
    public function testATakenEntryIsNotGivenBackWhileItIsMade()
    {
        $this->assertTrue( expImageAliasBacklog::add( 5, 1, 'eng-GB', 'small', array(), null, 'site' ) );
        $files = glob( $this->base . '/backlog/*.json' );
        $this->assertCount( 1, $files );
        // It waited long before a drainer took it
        touch( $files[0], time() - expImageAliasBacklog::STALE_WORK - 60 );
        $entry = expImageAliasBacklog::take();
        $this->assertIsArray( $entry );
        clearstatcache( true, $entry['file'] );
        $this->assertGreaterThanOrEqual( time() - 5, filemtime( $entry['file'] ) );
        $this->assertSame( 0, expImageAliasBacklog::recoverStale() );
        $this->assertFileExists( $entry['file'] );
        // A process that died with it: given back after STALE_WORK
        touch( $entry['file'], time() - expImageAliasBacklog::STALE_WORK - 1 );
        $this->assertSame( 1, expImageAliasBacklog::recoverStale() );
        $this->assertCount( 1, glob( $this->base . '/backlog/*.json' ) );
    }

    /** IAX-05 */
    public function testRejectedSignaturesRecheckTheStoreRarely()
    {
        $keys = new ReflectionProperty( 'expImageAliasUrl', 'keys' );
        $fixed = new ReflectionProperty( 'expImageAliasUrl', 'fixedKeys' );
        if ( PHP_VERSION_ID < 80100 )
        {
            $keys->setAccessible( true );
            $fixed->setAccessible( true );
        }

        expImageAliasUrl::setKeys( str_repeat( 'b', 64 ) );
        $token = expImageAliasUrl::token( $this->payload( $this->images . '/andernach.jpg', 'andernach_small.jpg' ) );

        // Keys read 5 s ago: not asked again
        $keys->setValue( null, array( 'current' => str_repeat( 'a', 64 ), 'previous' => null, 'previous_until' => 0, 'read' => time() - 5 ) );
        $fixed->setValue( null, false );
        $check = expImageAliasUrl::verify( $token );
        $this->assertFalse( $check['valid'] );
        $this->assertSame( 'signature', $check['reason'] );
        $this->assertSame( str_repeat( 'a', 64 ), $keys->getValue()['current'] );

        // Read longer ago than KEY_RECHECK: asked again once (no store here: no key, and no loop)
        $keys->setValue( null, array( 'current' => str_repeat( 'a', 64 ), 'previous' => null, 'previous_until' => 0,
                                      'read' => time() - expImageAliasUrl::KEY_RECHECK - 1 ) );
        $check = expImageAliasUrl::verify( $token );
        $this->assertFalse( $check['valid'] );
        $this->assertNotSame( str_repeat( 'a', 64 ), $keys->getValue()['current'] );
    }

    /** IAX-06 */
    public function testARefusedQueueRunsNothingWhenTheCallerSaysSo()
    {
        $calls = 0;
        expImageAliasJob::setQueue( function ( $type, array $payload, array $options ) use ( &$calls ) {
            $calls++;
            throw new RuntimeException( 'quota reached' );
        } );
        $this->assertTrue( expImageAliasJob::queueAvailable( expImageAliasJob::TYPE_BULK ) );
        $bulk = expImageAliasJob::startBulk( array( 'action' => 'purge', 'subtree' => 2 ), true, null, array( 'sync_fallback' => false ) );
        $this->assertSame( 'refused', $bulk['mode'] );
        $this->assertNull( $bulk['summary'] );
        $resync = expImageAliasJob::startResync( array( 'subtree' => 2 ), true, null, false );
        $this->assertSame( 'refused', $resync['mode'] );
        $this->assertNull( $resync['summary'] );
        $this->assertSame( 2, $calls );

        expImageAliasJob::setQueue( function ( $type, array $payload, array $options ) {
            return new expImageAliasAuditTestJob();
        } );
        $bulk = expImageAliasJob::startBulk( array( 'action' => 'purge', 'subtree' => 2 ), true, null, array( 'sync_fallback' => false ) );
        $this->assertSame( 'queue', $bulk['mode'] );
        $this->assertSame( 4711, $bulk['job'] );
        $resync = expImageAliasJob::startResync( array( 'subtree' => 2 ), true, null, false );
        $this->assertSame( 'queue', $resync['mode'] );
        $this->assertSame( 4711, $resync['job'] );
    }

    /** IAX-07 */
    public function testTheSwapScriptMatchesPathsInSrcsetAndStartsAgain()
    {
        $js = (string)file_get_contents( dirname( __DIR__, 5 ) . '/design/standard/javascript/exp_image_swap.js' );
        $this->assertStringContainsString( 'function pathOf(url)', $js );
        $this->assertStringContainsString( 'function inSrcset(img, url)', $js );
        $this->assertStringContainsString( 'inSrcset(img, url) !== null', $js, 'imagesShowing() uses both forms' );
        $this->assertStringContainsString( 'img.srcset.split(named).join(replacement)', $js, 'swap() replaces the form the srcset uses' );
        $this->assertStringNotContainsString( 'img.srcset.split(url).join(replacement)', $js );
        $this->assertStringContainsString( 'headers: { Accept: IMAGE_ACCEPT }', $js, 'asks as an <img> asks (negotiated formats)' );
        $this->assertStringContainsString( "image/avif,image/webp", $js );
        $this->assertMatchesRegularExpression( '/if \\(!lastProgress \\|\\| now\\(\\) - lastProgress > GIVE_UP\\) \\{\\s*lastProgress = now\\(\\);\\s*idleRounds = 0;/', $js,
                                               'add() starts the script again after it gave up' );
    }

    /** IAX-08 */
    public function testAPausedBulkJobDefersBeforeItReadsItsScope()
    {
        if ( !class_exists( 'expImageAliasQueueHandler' ) || !class_exists( 'expQueueJob' ) || !class_exists( 'expQueueResult' ) )
            $this->markTestSkipped( 'expqueue is not loaded' );
        expImageAliasThrottle::setPauseStore( array( 'since' => time(), 'by' => 'test', 'reason' => 'test' ) );
        try
        {
            $job = $this->createStub( 'expQueueJob' );
            $job->method( 'attribute' )->willReturnCallback( function ( $name ) {
                return $name === 'type' ? expImageAliasJob::TYPE_BULK : null;
            } );
            $job->method( 'payloadArray' )->willReturn( expImageAliasJob::bulkPayload( array( 'action' => 'generate', 'subtree' => 2, 'run' => 'bulk-iax08' ) ) );
            $context = $this->createMock( 'expQueueContext' );
            $context->expects( $this->never() )->method( 'checkpoint' );
            $result = ( new expImageAliasQueueHandler() )->run( $job, $context );
            $this->assertInstanceOf( 'expQueueResult', $result );
            $this->assertStringNotContainsString( 'run-start', (string)@file_get_contents( $this->base . '/log/imagealias.log' ) );
        }
        finally
        {
            expImageAliasThrottle::setPauseStore( false );
        }
    }

    /** IAX-09 */
    public function testPageEntriesAreWrittenAfterTheAnswer()
    {
        $dir = $this->base . '/backlog';
        expImageAliasBacklog::setAnswerFirst( true );
        try
        {
            // One already waiting (a content/image request of the page added it meanwhile)
            $this->assertTrue( expImageAliasBacklog::add( 12, 1, 'eng-GB', 'small', array(), null, 'site' ) );
            foreach ( array( 10, 11, 12, 10, 13 ) as $attribute )
                $this->assertTrue( expImageAliasBacklog::addAfterAnswer( $attribute, 1, 'eng-GB', 'small', array(), null, 'site' ) );
            $this->assertCount( 1, glob( $dir . '/*.json' ), 'nothing written before the answer' );
            $this->assertSame( 3, expImageAliasBacklog::flushPending() );
            $this->assertSame( 0, expImageAliasBacklog::flushPending(), 'written once' );
            $order = array();
            foreach ( glob( $dir . '/*.json' ) as $file )
            {
                $entry = json_decode( (string)file_get_contents( $file ), true );
                $order[] = $entry['a'];
            }
            $this->assertSame( array( 12, 10, 11, 13 ), $order, 'in the order they were added, after the one waiting' );
        }
        finally
        {
            expImageAliasBacklog::setAnswerFirst( null );
        }
        // A request that cannot answer first writes at once
        expImageAliasBacklog::setAnswerFirst( false );
        try
        {
            $this->assertTrue( expImageAliasBacklog::addAfterAnswer( 14, 1, 'eng-GB', 'small', array(), null, 'site' ) );
            $this->assertCount( 5, glob( $dir . '/*.json' ) );
            $this->assertSame( 0, expImageAliasBacklog::flushPending() );
        }
        finally
        {
            expImageAliasBacklog::setAnswerFirst( null );
        }
    }

    /** IAX-10 */
    public function testADrainerOutOfTimeHandsOver()
    {
        mkdir( $this->base . '/slots', 0700, true );
        expImageAliasThrottle::setDirectory( $this->base . '/slots' );
        expImageAliasThrottle::setLimit( 6 );
        expImageAliasThrottle::setApplies( true );
        expImageAliasThrottle::reset();
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'MaxParallel', '6' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'BacklogTime', '1' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'BacklogDrainer', 'process' );
        $spawned = array();
        expImageAliasBacklog::setSpawner( function ( $slot ) use ( &$spawned ) {
            $spawned[] = $slot;
            return true;
        } );
        try
        {
            if ( !expImageAliasBacklog::usesDrainers() )
                $this->markTestSkipped( 'proc_open() is not allowed here' );
            for ( $i = 0; $i < 12; $i++ )
                expImageAliasBacklog::add( 300 + $i, 1, 'eng-GB', 'small', array(), null, 'site' );
            expImageAliasBacklog::setWorker( function ( array $entry ) {
                usleep( 300000 );
            } );
            $made = expImageAliasBacklog::runDrainer( 0 );
            $this->assertGreaterThan( 0, $made );
            $this->assertGreaterThan( 0, expImageAliasBacklog::count(), 'entries left when the time ran out' );
            $this->assertNotEmpty( $spawned, 'the next drainers started' );

            // Nothing made (every conversion slot held elsewhere): no successor
            $spawned = array();
            $held = array();
            for ( $slot = 0; $slot < 6; $slot++ )
            {
                $handle = fopen( $this->base . '/slots/slot-' . $slot . '.lock', 'c' );
                $this->assertTrue( flock( $handle, LOCK_EX | LOCK_NB ), "slot $slot is free" );
                $held[] = $handle;
            }
            $this->assertSame( 0, expImageAliasBacklog::runDrainer( 1 ) );
            $this->assertSame( array(), $spawned );
            foreach ( $held as $handle )
            {
                flock( $handle, LOCK_UN );
                fclose( $handle );
            }
        }
        finally
        {
            expImageAliasBacklog::setSpawner( null );
            expImageAliasBacklog::setWorker( null );
            expImageAliasThrottle::reset();
            expImageAliasThrottle::setDirectory( null );
            expImageAliasThrottle::setLimit( null );
            expImageAliasThrottle::setApplies( null );
            ezpINIHelper::restoreINISettings();
        }
    }
}
