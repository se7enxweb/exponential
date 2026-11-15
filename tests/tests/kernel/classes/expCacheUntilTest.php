<?php
/**
 * {cache_until()} and seconds_until (expCacheUntil, expCacheUntilOperator): the page expiry every cache honours.
 *
 *  CU-01 — Time strings are read in the site's time zone: 'tomorrow' is midnight there, not in PHP's zone
 *  CU-02 — A time of day that has passed today is its next occurrence; a time backwards or a past date is not rolled
 *  CU-03 — Earliest wins: a later call never raises the expiry; open scopes (a view, a cache-block) get it as well
 *  CU-04 — 0, false and '0' mean "do not keep the page"; null, '', true and a word that is no time are ignored
 *  CU-05 — seconds_until gives the seconds from now; 0 for the past, false and 0
 *  CU-06 — cache_ttl is the expiry time() + cache_ttl when the view is generated; the earlier of the two wins
 *  CU-07 — The reported bug: a view cache entry replayed at 23:00 gives the HTTP cache the hour that is left, not the
 *          lifetime it had at 09:00 -- also for an entry written before 6.0.15 (file time + cache_ttl)
 *  CU-08 — The HTTP cache keeps an entry until its expiry at most, never serves it stale after it, stores nothing past
 *          it, and its answers carry a max-age no longer than the time left
 *  CU-09 — Cache-block files: a block with an expiry is kept with it, read back as the text alone, and is expired
 *          (generated again) once it has come; a block without one is stored exactly as before
 *  CU-10 — A cache_until() inside a {cache-block}: rendered interpreted and compiled, the block is kept until the
 *          expiry, its replay limits the page, and after the expiry it is rendered again; a constant argument is
 *          evaluated each time the compiled template runs, never at compile time
 *  CU-11 — Response headers: max-age and s-maxage only lowered, s-maxage added where a shared cache would keep the
 *          page longer, Expires lowered, LiteSpeed's no-cache, X-Exp-Cache-Until with the template under debug
 *  CU-12 — The debug bar shows the expiry and what set it as a chip of the Rendering tab
 *  CU-13 — The installer's time zone: as given, else from the primary language's country, else PHP's, else UTC
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group cacheuntil
 */

require_once __DIR__ . '/../../../../kernel/classes/expcacheuntil.php';
require_once __DIR__ . '/../../../../kernel/classes/expinstalltimezone.php';
require_once __DIR__ . '/../../../../kernel/private/classes/httpcache/ezphttpcachecontract.php';

#[\PHPUnit\Framework\Attributes\Group('cacheuntil')]
class expCacheUntilTest extends PHPUnit\Framework\TestCase
{
    /** 2026-11-15 09:00:00 in Berlin (CET, UTC+1) */
    const NINE_BERLIN = 1794729600;

    private $dir;

    /** @var string[] directories this test made, removed in tearDown() */
    private $dirs = array();

    private $savedGlobals = array();

    private $handler;

    private $token;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        expCacheUntil::reset();
        expCacheUntil::setClock( null );
        expCacheUntil::setTimeZone( 'Europe/Berlin' );
        expCacheUntil::setSharedCacheDefault( 30 );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZCurrentAccess' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $this->handler = self::exceptionHandler();
        $this->token = 'cu' . getmypid() . substr( md5( uniqid( '', true ) ), 0, 8 );
    }

    protected function tearDown(): void
    {
        expCacheUntil::reset();
        expCacheUntil::setClock( null );
        expCacheUntil::setTimeZone( null );
        expCacheUntil::setSharedCacheDefault( null );
        while ( self::exceptionHandler() !== $this->handler && self::exceptionHandler() !== null )
            restore_exception_handler();
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        if ( class_exists( 'eZTemplateCacheBlock' ) && is_dir( $blocks = eZTemplateCacheBlock::templateBlockCacheDir() ) )
        {
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $blocks, FilesystemIterator::SKIP_DOTS ) ) as $file )
            {
                if ( $file->isFile() && strpos( (string)file_get_contents( $file->getPathname() ), $this->token ) !== false )
                    unlink( $file->getPathname() );
            }
        }
        foreach ( $this->dirs as $dir )
            self::removeTree( $dir );
    }

    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    private static function berlin( $text )
    {
        return ( new DateTimeImmutable( $text, new DateTimeZone( 'Europe/Berlin' ) ) )->getTimestamp();
    }

    /** CU-01 */
    public function testTimeStringsAreReadInTheSiteZone()
    {
        $now = self::NINE_BERLIN;
        $this->assertSame( self::berlin( '2026-11-15 09:00' ), $now, 'the fixed clock is 09:00 in Berlin' );
        $this->assertSame( self::berlin( '2026-11-16 00:00' ), expCacheUntil::resolve( 'tomorrow', $now ) );
        $this->assertSame( self::berlin( '2026-11-15 10:00' ), expCacheUntil::resolve( 'next hour', $now ) );
        $this->assertSame( $now + 900, expCacheUntil::resolve( '+15 minutes', $now ) );
        $this->assertSame( self::berlin( '2026-11-16 00:00' ), expCacheUntil::resolve( 'next monday', $now ), '15 November 2026 is a Sunday' );
        $this->assertSame( self::berlin( '2026-12-24 18:00' ), expCacheUntil::resolve( '2026-12-24 18:00', $now ) );

        // The same moment in a site in Los Angeles: 'tomorrow' is that zone's midnight
        expCacheUntil::setTimeZone( 'America/Los_Angeles' );
        $la = ( new DateTimeImmutable( '2026-11-16 00:00', new DateTimeZone( 'America/Los_Angeles' ) ) )->getTimestamp();
        $this->assertSame( $la, expCacheUntil::resolve( 'tomorrow', $now ) );
        $this->assertNotSame( self::berlin( '2026-11-16 00:00' ), $la );
    }

    /** CU-02 */
    public function testPassedTimesOfDayRollToTheirNextOccurrence()
    {
        $now = self::berlin( '2026-11-16 19:30' ); // a Monday evening
        $this->assertSame( self::berlin( '2026-11-17 00:00' ), expCacheUntil::resolve( 'midnight', $now ) );
        $this->assertSame( self::berlin( '2026-11-17 00:00' ), expCacheUntil::resolve( 'today', $now ) );
        $this->assertSame( self::berlin( '2026-11-17 12:00' ), expCacheUntil::resolve( 'noon', $now ) );
        $this->assertSame( self::berlin( '2026-11-17 18:00' ), expCacheUntil::resolve( '18:00', $now ) );
        $this->assertSame( self::berlin( '2026-11-16 20:00' ), expCacheUntil::resolve( '20:00', $now ), 'still to come today' );
        $this->assertSame( self::berlin( '2026-11-23 00:00' ), expCacheUntil::resolve( 'monday', $now ), 'on a Monday: the next one' );

        // backwards or a fixed date that has passed: not rolled, the page is not kept
        $problem = null;
        $this->assertSame( 0, expCacheUntil::resolve( '-5 minutes', $now, $problem ) );
        $this->assertNotNull( $problem );
        $this->assertSame( 0, expCacheUntil::resolve( '2026-01-01 10:00', $now ) );
        $this->assertSame( 0, expCacheUntil::resolve( 'yesterday', $now ) );
    }

    /** CU-03 */
    public function testEarliestWins()
    {
        expCacheUntil::setClock( self::NINE_BERLIN );
        $this->assertNull( expCacheUntil::expires() );
        $this->assertNull( expCacheUntil::remaining() );

        expCacheUntil::request( 'tomorrow', 'design:full/event.tpl' );
        $this->assertSame( self::berlin( '2026-11-16 00:00' ), expCacheUntil::expires() );
        expCacheUntil::request( '+15 minutes', 'design:parts/countdown.tpl' );
        $this->assertSame( self::NINE_BERLIN + 900, expCacheUntil::expires() );
        $this->assertSame( 'design:parts/countdown.tpl', expCacheUntil::source() );
        expCacheUntil::request( 'next monday', 'design:pagelayout.tpl' );
        $this->assertSame( self::NINE_BERLIN + 900, expCacheUntil::expires(), 'a later time never raises it' );
        $this->assertSame( 900, expCacheUntil::remaining() );

        // a scope (a view or a cache-block being generated) gets what is asked inside it, the request everything
        expCacheUntil::reset();
        expCacheUntil::request( '+1 hour', 'outside' );
        $outer = expCacheUntil::beginScope();
        $inner = expCacheUntil::beginScope();
        expCacheUntil::request( '+2 hours', 'in the inner block' );
        $innerScope = expCacheUntil::endScope( $inner );
        expCacheUntil::request( '+30 minutes', 'in the outer block' );
        $outerScope = expCacheUntil::endScope( $outer );
        $this->assertSame( self::NINE_BERLIN + 7200, $innerScope['expires'], 'the inner block lives until its own expiry' );
        $this->assertSame( self::NINE_BERLIN + 1800, $outerScope['expires'], 'the outer block: the earliest inside it' );
        $this->assertSame( self::NINE_BERLIN + 1800, expCacheUntil::expires(), 'the page: the earliest of all' );
        $this->assertNull( expCacheUntil::endScope( $outer ), 'a scope ends once' );

        // a scope left open by its caller ends with the scope around it
        $a = expCacheUntil::beginScope();
        expCacheUntil::beginScope();
        $this->assertNotNull( expCacheUntil::endScope( $a ) );
        expCacheUntil::limit( 0 );
        $this->assertSame( 0, expCacheUntil::expires() );
    }

    /** CU-04 */
    public function testZeroAndFalseDisableAndNonTimesAreIgnored()
    {
        $now = self::NINE_BERLIN;
        foreach ( array( 0, false, '0', 0.0 ) as $off )
            $this->assertSame( 0, expCacheUntil::resolve( $off, $now ), var_export( $off, true ) );
        foreach ( array( null, '', true, array( 1 ), 'whenever it suits' ) as $none )
        {
            $problem = null;
            $this->assertNull( expCacheUntil::resolve( $none, $now, $problem ), var_export( $none, true ) );
            $this->assertNotNull( $problem );
        }
        $this->assertSame( $now + 60, expCacheUntil::resolve( (string)( $now + 60 ), $now ), 'a numeric string is a Unix time' );
        $this->assertSame( $now + 60, expCacheUntil::resolve( new DateTime( '@' . ( $now + 60 ) ), $now ) );
        $problem = null;
        $this->assertSame( 0, expCacheUntil::resolve( 3600, $now, $problem ), 'a number is a Unix time, never a lifetime' );
        $this->assertStringContainsString( '+N seconds', $problem );

        expCacheUntil::setClock( $now );
        expCacheUntil::request( null, 'a missing variable' );
        $this->assertNull( expCacheUntil::expires(), 'an ignored call sets nothing' );
        expCacheUntil::request( 0, 'design:full/live_ticker.tpl' );
        $this->assertSame( 0, expCacheUntil::expires() );
        $this->assertSame( 0, expCacheUntil::remaining() );
        expCacheUntil::request( 'tomorrow' );
        $this->assertSame( 0, expCacheUntil::expires(), 'not cached stays not cached' );
    }

    /** CU-05 */
    public function testSecondsUntil()
    {
        $now = self::NINE_BERLIN;
        $this->assertSame( 15 * 3600, expCacheUntil::secondsUntil( 'tomorrow', $now ) );
        $this->assertSame( 900, expCacheUntil::secondsUntil( '+15 minutes', $now ) );
        $this->assertSame( 0, expCacheUntil::secondsUntil( 0, $now ) );
        $this->assertSame( 0, expCacheUntil::secondsUntil( $now - 10, $now ) );
        $this->assertNull( expCacheUntil::secondsUntil( 'no time at all', $now ) );

        // the operators, as a template calls them
        expCacheUntil::setClock( $now );
        $operator = new expCacheUntilOperator();
        $this->assertSame( array( 'cache_until', 'seconds_until' ), $operator->operatorList() );
        $this->assertFalse( method_exists( $operator, 'operatorTemplateHints' ), 'no hints: never folded at compile time' );
        $value = 'tomorrow';
        $operator->modify( null, 'seconds_until', array(), '', '', $value, array( 'until' => null ), false );
        $this->assertSame( 15 * 3600, $value, 'piped' );
        $value = null;
        $operator->modify( null, 'seconds_until', array( array( 'x' ) ), '', '', $value, array( 'until' => '+1 hour' ), false );
        $this->assertSame( 3600, $value );
        $value = null;
        $operator->modify( null, 'cache_until', array( array( 'x' ) ), '', '', $value, array( 'until' => 'next hour' ),
                           array( array( 1, 0 ), array( 1, 20 ), 'design:parts/opening_hours.tpl' ) );
        $this->assertSame( '', $value, 'cache_until prints nothing' );
        $this->assertSame( self::berlin( '2026-11-15 10:00' ), expCacheUntil::expires() );
        $this->assertSame( 'design:parts/opening_hours.tpl', expCacheUntil::source() );
    }

    /** CU-06 */
    public function testCacheTtlIsMappedToAnAbsoluteExpiry()
    {
        $now = self::NINE_BERLIN;
        $this->assertNull( eZNodeviewfunctions::viewExpiry( null, -1, $now ), 'no expiry, no change' );
        $this->assertSame( array( 'expires' => $now + 600, 'source' => 'cache_ttl=600' ), eZNodeviewfunctions::viewExpiry( null, 600, $now ) );
        $this->assertSame( array( 'expires' => 0, 'source' => 'cache_ttl=0' ), eZNodeviewfunctions::viewExpiry( null, 0, $now ) );
        $this->assertSame( array( 'expires' => $now + 300, 'source' => 'design:full/event.tpl' ),
                           eZNodeviewfunctions::viewExpiry( array( 'expires' => $now + 300, 'source' => 'design:full/event.tpl' ), 600, $now ),
                           'cache_until earlier than cache_ttl' );
        $this->assertSame( array( 'expires' => $now + 600, 'source' => 'cache_ttl=600' ),
                           eZNodeviewfunctions::viewExpiry( array( 'expires' => $now + 3600, 'source' => 'x' ), 600, $now ) );
        $this->assertSame( array( 'expires' => $now + 3600, 'source' => 'x' ),
                           eZNodeviewfunctions::viewExpiry( array( 'expires' => $now + 3600, 'source' => 'x' ), '-1', $now ) );
    }

    /** CU-07 */
    public function testViewCacheReplayGivesTheHttpCacheTheRemainingTime()
    {
        // 09:00: the full view sets cache_ttl to the seconds until midnight (54000); both caches end at midnight
        $nine = self::NINE_BERLIN;
        $midnight = self::berlin( '2026-11-16 00:00' );
        $ttl = $midnight - $nine;
        $this->assertSame( 54000, $ttl );
        $generated = eZNodeviewfunctions::viewExpiry( null, $ttl, $nine );
        $this->assertSame( $midnight, $generated['expires'] );

        // 23:00: only the HTTP cache entry was purged; the view cache entry is still valid and is replayed
        $eleven = self::berlin( '2026-11-15 23:00' );
        $cached = array( 'content' => '...', 'cache_ttl' => $ttl, 'cache_expires' => $generated['expires'] );
        $this->assertSame( $midnight, eZNodeviewfunctions::cachedViewExpiry( $cached, $nine ) );
        // an entry written before 6.0.15 has only cache_ttl: its file time plus cache_ttl
        $this->assertSame( $midnight, eZNodeviewfunctions::cachedViewExpiry( array( 'cache_ttl' => $ttl ), $nine ) );
        $this->assertNull( eZNodeviewfunctions::cachedViewExpiry( array( 'cache_ttl' => -1 ), $nine ) );

        expCacheUntil::setClock( $eleven );
        expCacheUntil::limit( eZNodeviewfunctions::cachedViewExpiry( array( 'cache_ttl' => $ttl ), $nine ), 'view cache' );
        $this->assertSame( 3600, expCacheUntil::remaining(), 'one hour, not the 54000 seconds the view had at 09:00' );

        // the HTTP cache stores the page with what is left: (with MaxAge=86400) it lives one hour, until midnight
        $contract = $this->contract( array( 'maxAge' => 86400 ) );
        $expires = time() + 3600;
        $meta = $contract->storeEntry( 'k' . $this->token, 200, array(), 'page', array( 'ez-all' ), array(), null, $expires );
        $this->assertIsArray( $meta );
        $this->assertLessThanOrEqual( 3600, $meta['maxAge'] );
        $this->assertGreaterThanOrEqual( 3598, $meta['maxAge'] );
        $this->assertSame( $expires, $meta['expires'] );
    }

    private function contract( array $extra = array() )
    {
        $this->dir = dirname( __DIR__, 4 ) . '/var/tmp/cacheuntil-test-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir . '/sessions', 0770, true );
        $this->dirs[] = $this->dir;
        return new ezpHttpCacheContract( $extra + array(
            'enabled' => true, 'secret' => str_repeat( 'k', 64 ), 'dir' => $this->dir,
            'hosts' => array( 'example.org' => 'site' ), 'sessionCookie' => array( 'site' => 'eZSESSID' ),
            'sessionSavePath' => $this->dir . '/sessions', 'formTokenSecret' => 'secret', 'maxAge' => 3600, 'swr' => 60,
            'apcu' => false,
        ) );
    }

    /** CU-08 */
    public function testHttpCacheHonoursTheExpiry()
    {
        $contract = $this->contract();
        $key = 'k' . $this->token;

        // no expiry: as before
        $meta = $contract->storeEntry( $key, 200, array(), 'page', array( 'ez-all' ), array() );
        $this->assertSame( 3600, $meta['maxAge'] );
        $this->assertSame( 60, $meta['swr'] );
        $this->assertArrayNotHasKey( 'expires', $meta );
        $this->assertSame( ezpHttpCacheContract::HIT_MAX_AGE, ezpHttpCacheContract::hitMaxAge( $meta ) );

        // an expiry later than MaxAge: MaxAge
        $meta = $contract->storeEntry( $key, 200, array(), 'page', array( 'ez-all' ), array(), null, time() + 86400 );
        $this->assertSame( 3600, $meta['maxAge'] );
        $this->assertSame( 0, $meta['swr'], 'never served stale past an expiry' );

        // sooner: the time left, and the answers say so to browsers and proxies
        $meta = $contract->storeEntry( $key, 200, array(), 'page', array( 'ez-all' ), array(), null, time() + 120 );
        $this->assertLessThanOrEqual( 120, $meta['maxAge'] );
        $this->assertLessThanOrEqual( 120, ezpHttpCacheContract::hitMaxAge( $meta ) );
        $this->assertSame( 20, ezpHttpCacheContract::hitMaxAge( $meta, $meta['expires'] - 20 ) );
        $this->assertSame( 0, ezpHttpCacheContract::hitMaxAge( $meta, $meta['expires'] + 5 ) );
        $this->assertNotNull( $contract->loadEntry( $key ), 'served before the expiry' );

        // an entry whose expiry has come is a miss, also within what would have been the stale window
        $meta['expires'] = time() - 1;
        $meta['swr'] = 600;
        $path = $this->dir . '/e/' . substr( $key, 0, 2 ) . '/' . $key . '.meta';
        file_put_contents( $path, json_encode( $meta ) );
        $this->assertNull( $contract->loadEntry( $key ) );
        $this->assertSame( 'expired (cache_until)', $contract->lastReason );

        // past: nothing is stored
        $this->assertFalse( $contract->storeEntry( $key, 200, array(), 'page', array( 'ez-all' ), array(), null, time() ) );
        $this->assertFalse( $contract->storeEntry( $key, 200, array(), 'page', array( 'ez-all' ), array(), null, 0 ) );
    }

    /** CU-09 */
    public function testCacheBlockFiles()
    {
        $this->assertSame( 'text', expCacheUntil::wrapBlock( 'text', null ), 'without an expiry: exactly as before' );
        $wrapped = expCacheUntil::wrapBlock( 'text', 1800000000 );
        $this->assertSame( array( 'text', 1800000000 ), expCacheUntil::unwrapBlock( $wrapped ) );
        $this->assertSame( array( 'plain', null ), expCacheUntil::unwrapBlock( 'plain' ) );

        expCacheUntil::setClock( 1800000000 - 100 );
        $this->assertSame( 'text', eZTemplateCacheBlock::cachedText( $wrapped ) );
        $this->assertSame( 1800000000, expCacheUntil::expires(), 'a block read back limits the page' );
        $this->assertSame( 'cache-block', expCacheUntil::source() );
        $this->assertSame( 'plain', eZTemplateCacheBlock::cachedText( 'plain' ) );
        expCacheUntil::setClock( 1800000000 );
        $this->assertInstanceOf( 'eZClusterFileFailure', eZTemplateCacheBlock::cachedText( $wrapped ), 'expired: generated again' );

        $this->assertSame( array( 'scope' => 'template-block', 'binarydata' => 'text' ), eZTemplateCacheBlock::storeData( null, 'text' ) );
        $data = eZTemplateCacheBlock::storeData( 1800000000 + 60, 'text' );
        $this->assertSame( expCacheUntil::wrapBlock( 'text', 1800000060 ), $data['binarydata'] );
        $this->assertArrayNotHasKey( 'store', $data );
        $this->assertFalse( eZTemplateCacheBlock::storeData( 0, 'text' )['store'], 'cache_until( 0 ) in a block: not stored' );
    }

    /**
     * Renders $source as a template file compiled or interpreted; array( output without white space, errors ).
     */
    private function render( $name, $source, array $variables, $compiled )
    {
        $dir = 'var/tmp/phpunit-cacheuntil-' . $this->token;
        if ( !is_dir( $dir . '/compiled' ) )
            mkdir( $dir . '/compiled', 0777, true );
        $this->dirs[] = dirname( __DIR__, 4 ) . '/' . $dir;
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'phpunit' );
        if ( $compiled )
        {
            unset( $GLOBALS['eZSiteBasics'] );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $dir . '/compiled';
        }
        else
        {
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        }
        $file = $dir . '/' . $name . '.tpl';
        file_put_contents( $file, $source );
        $tpl = new eZTemplate();
        // the kernel's operators as eZTemplate::factory() loads them (cache_until, seconds_until)
        $tpl->setAutoloadPathList( array( 'lib/eztemplate/classes/', 'kernel/common/' ) );
        $tpl->autoload();
        foreach ( $variables as $variable => $value )
            $tpl->setVariable( $variable, $value );
        $output = $tpl->fetch( $file );
        return array( preg_replace( '/\s+/', '', (string)$output ), $tpl->errorLog() );
    }

    /** CU-10 */
    public function testCacheUntilInsideACacheBlock()
    {
        foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
        {
            $this->token .= $mode[0];
            $source = '{cache-block keys=array( $token )}[{$token}]{cache_until( \'+1 hour\' )}<{$n}>{/cache-block}';
            $start = time();
            expCacheUntil::setClock( $start );

            expCacheUntil::reset();
            list( $output, $errors ) = $this->render( 'block_' . $mode, $source, array( 'token' => $this->token, 'n' => 1 ), $compiled );
            $this->assertSame( '[' . $this->token . ']<1>', $output, "$mode: rendered" );
            $this->assertSame( array(), $errors, $mode );
            $this->assertSame( $start + 3600, expCacheUntil::expires(), "$mode: the call limits the page" );

            // from the cache: the body does not run, the stored expiry limits the page all the same
            expCacheUntil::reset();
            expCacheUntil::setClock( $start + 600 );
            list( $output ) = $this->render( 'block_' . $mode, $source, array( 'token' => $this->token, 'n' => 2 ), $compiled );
            $this->assertSame( '[' . $this->token . ']<1>', $output, "$mode: from the cache" );
            $this->assertSame( $start + 3600, expCacheUntil::expires(), "$mode: the replay limits the page" );
            $this->assertSame( 3000, expCacheUntil::remaining(), "$mode: what is left" );

            // after the expiry: rendered again, with an expiry counted from then (the argument is read at run time)
            expCacheUntil::reset();
            expCacheUntil::setClock( $start + 3600 );
            list( $output ) = $this->render( 'block_' . $mode, $source, array( 'token' => $this->token, 'n' => 3 ), $compiled );
            $this->assertSame( '[' . $this->token . ']<3>', $output, "$mode: expired by cache_until, rendered again" );
            $this->assertSame( $start + 7200, expCacheUntil::expires(), "$mode: a constant argument is evaluated at run time" );
        }

        // without a cache-block, in a compiled template: each run reads the clock again
        $source = 'x{cache_until( \'+15 minutes\' )}{def $s = \'+1 hour\'|seconds_until}{$s}';
        foreach ( array( 1000, 5000 ) as $offset )
        {
            expCacheUntil::reset();
            expCacheUntil::setClock( self::NINE_BERLIN + $offset );
            list( $output, $errors ) = $this->render( 'plain', $source, array(), true );
            $this->assertSame( 'x3600', $output );
            $this->assertSame( array(), $errors );
            $this->assertSame( self::NINE_BERLIN + $offset + 900, expCacheUntil::expires() );
        }
    }

    /** CU-11 */
    public function testResponseHeaders()
    {
        // Cache-Control: lowered, never raised
        $this->assertSame( 'public, max-age=60', expCacheUntil::adjustCacheControl( 'public, max-age=120', 60 ) );
        $this->assertSame( 'public, max-age=120', expCacheUntil::adjustCacheControl( 'public, max-age=120', 600 ) );
        $this->assertSame( 'public, max-age=0, s-maxage=60', expCacheUntil::adjustCacheControl( 'public, max-age=0, s-maxage=120', 60 ) );
        // the kernel's default: a shared cache that keeps it DefaultTtl seconds (Velocity) gets s-maxage below that
        $this->assertSame( 'no-cache, must-revalidate, s-maxage=10', expCacheUntil::adjustCacheControl( 'no-cache, must-revalidate', 10, 30 ) );
        $this->assertSame( 'no-cache, must-revalidate', expCacheUntil::adjustCacheControl( 'no-cache, must-revalidate', 600, 30 ), 'never raised' );
        $this->assertSame( 'no-cache, must-revalidate, s-maxage=0', expCacheUntil::adjustCacheControl( 'no-cache, must-revalidate', 0, 0 ) );
        $this->assertSame( 'private, no-cache, must-revalidate', expCacheUntil::adjustCacheControl( 'private, no-cache, must-revalidate', 0, 30 ) );
        $this->assertSame( 'private, max-age=5', expCacheUntil::adjustCacheControl( 'private, max-age=300', 5, 30 ) );

        // Expires: only lowered; the kernel's 1997 stays
        $this->assertNull( expCacheUntil::adjustExpires( 'Mon, 26 Jul 1997 05:00:00 GMT', 1800000000 ) );
        $this->assertSame( gmdate( 'D, d M Y H:i:s', 1800000000 ) . ' GMT', expCacheUntil::adjustExpires( gmdate( 'D, d M Y H:i:s', 1800009000 ) . ' GMT', 1800000000 ) );
        $this->assertNull( expCacheUntil::adjustExpires( null, 1800000000 ) );

        // nothing without an expiry: a page without cache_until() is sent as before
        $this->assertSame( array(), expCacheUntil::headerChanges( array( 'cache-control' => 'no-cache, must-revalidate' ) ) );

        $now = self::NINE_BERLIN;
        expCacheUntil::setClock( $now );
        expCacheUntil::request( '+20 seconds', 'design:pagelayout/opening_hours.tpl' );
        $changes = expCacheUntil::headerChanges( array( 'cache-control' => 'no-cache, must-revalidate', 'expires' => 'Mon, 26 Jul 1997 05:00:00 GMT' ) );
        $this->assertSame( 'no-cache, must-revalidate, s-maxage=20', $changes['Cache-Control'] );
        $this->assertArrayNotHasKey( 'Expires', $changes );
        $this->assertArrayNotHasKey( 'X-LiteSpeed-Cache-Control', $changes, 'a lifetime goes to LiteSpeed only when asked for' );
        $this->assertSame( '2026-11-15T09:00:20+01:00', $changes['X-Exp-Cache-Until'] );

        $changes = expCacheUntil::headerChanges( array( 'cache-control' => 'public, max-age=120' ), true, true, true );
        $this->assertSame( 'public, max-age=20', $changes['Cache-Control'] );
        $this->assertSame( 'max-age=20', $changes['X-LiteSpeed-Cache-Control'] );
        $this->assertSame( '2026-11-15T09:00:20+01:00; source="design:pagelayout/opening_hours.tpl"', $changes['X-Exp-Cache-Until'], 'the template under debug' );
        $this->assertArrayNotHasKey( 'X-LiteSpeed-Cache-Control', expCacheUntil::headerChanges( array( 'cache-control' => 'public, max-age=120' ), false, true ),
                                     'never for a signed-in visitor' );

        expCacheUntil::request( 0, 'design:full/live.tpl' );
        $changes = expCacheUntil::headerChanges( array( 'cache-control' => 'public, max-age=120' ) );
        $this->assertSame( 'public, max-age=0', $changes['Cache-Control'] );
        $this->assertSame( 'no-cache', $changes['X-LiteSpeed-Cache-Control'], 'kept out of LiteSpeed, always' );
        $this->assertSame( '0', $changes['X-Exp-Cache-Until'] );
        $this->assertStringContainsString( 'not kept', expCacheUntil::summary() );
    }

    /** CU-12 */
    public function testDebugBarChip()
    {
        $rendering = array( 'view_cache' => null, 'templates' => array( 'compiled' => array(), 'executed' => 1 ), 'cache_until' => null );
        $ids = function ( $chips ) { return array_column( $chips, 1 ); };
        $this->assertNotContains( 'cacheuntil', $ids( expDebugBarReport::renderingChips( $rendering ) ), 'no chip without an expiry' );

        expCacheUntil::setClock( self::NINE_BERLIN );
        expCacheUntil::request( 'tomorrow', 'design:full/event.tpl' );
        $rendering['cache_until'] = expCacheUntil::report();
        $chips = expDebugBarReport::renderingChips( $rendering );
        $chip = $chips[array_search( 'cacheuntil', $ids( $chips ), true )];
        $this->assertSame( 'Until 00:00', $chip[3] );
        $this->assertStringContainsString( 'design:full/event.tpl', $chip[4] );
        $this->assertStringContainsString( '54000', $chip[4] );

        expCacheUntil::request( 0, 'design:full/live.tpl' );
        $rendering['cache_until'] = expCacheUntil::report();
        $chips = expDebugBarReport::renderingChips( $rendering );
        $chip = $chips[array_search( 'cacheuntil', $ids( $chips ), true )];
        $this->assertSame( array( 'warn', 'Not cached' ), array( $chip[2], $chip[3] ) );
    }

    /** CU-13 */
    public function testInstallerTimeZone()
    {
        $this->assertSame( array( 'zone' => 'Asia/Tokyo', 'reason' => 'requested' ), expInstallTimeZone::choose( 'Asia/Tokyo', 'ger-DE', 'UTC' ) );
        $this->assertSame( array( 'zone' => 'Europe/Berlin', 'reason' => 'language' ), expInstallTimeZone::choose( '', 'ger-DE', 'America/Los_Angeles' ),
                           'the reported case: a German site in a container whose PHP says Los Angeles' );
        $this->assertSame( 'Europe/London', expInstallTimeZone::fromLocale( 'eng-GB' ) );
        $this->assertSame( 'Europe/Paris', expInstallTimeZone::fromLocale( 'fre-FR' ) );
        $this->assertSame( 'Europe/Oslo', expInstallTimeZone::fromLocale( 'nor-NO' ) );
        $this->assertSame( 'Europe/Belgrade', expInstallTimeZone::fromLocale( 'ser-SR' ), 'ser-SR is Serbian, not Suriname' );
        $this->assertNull( expInstallTimeZone::fromLocale( 'eng-US' ), 'several zones: never guessed' );
        $this->assertNull( expInstallTimeZone::fromLocale( 'por-BR' ) );
        $this->assertSame( array( 'zone' => 'America/Chicago', 'reason' => 'php' ), expInstallTimeZone::choose( '', 'eng-US', 'America/Chicago' ) );
        $this->assertSame( array( 'zone' => 'UTC', 'reason' => 'default' ), expInstallTimeZone::choose( '', 'eng-US', '' ) );
        $this->assertSame( array( 'zone' => 'Europe/Berlin', 'reason' => 'language' ), expInstallTimeZone::choose( 'Mars/Olympus', 'ger-DE', '' ),
                           'a zone PHP does not know is not taken (exp:install refuses it before)' );
        $this->assertTrue( expInstallTimeZone::isValid( 'UTC' ) );
        $this->assertFalse( expInstallTimeZone::isValid( 'Mars/Olympus' ) );
        $this->assertFalse( expInstallTimeZone::isValid( '' ) );
    }
}
