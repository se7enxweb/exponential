<?php
/**
 * The pages of module views kept for one session of one signed-in user (6.0.15, ezpHttpCacheListener::noteModuleView())
 * and sessions of the PHP redis handler read before the kernel boots.
 *
 *  MP-01 — A session context differs per session, per user and per siteaccess, and from the user's private context
 *  MP-02 — The early exit finds a page kept for the session, and never for another session of the same user
 *  MP-03 — Purging one of its tags drops the page; a user without a record is never answered
 *  MP-04 — The redis handler's save path gives host, port, prefix and database; a password is never taken
 *  MP-05 — The user ID is read from the data of a PHP session
 *
 * No database, no Valkey: the files handler stands in for the session store in MP-02 and MP-03.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group httpcache
 */

require_once __DIR__ . '/../../../../../kernel/private/classes/httpcache/ezphttpcachecontract.php';

class ezpHttpCacheModulePagesTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $sessions;

    protected function setUp(): void
    {
        $base = is_dir( __DIR__ . '/../../../../../var/tmp' ) ? realpath( __DIR__ . '/../../../../../var/tmp' ) : sys_get_temp_dir();
        $this->dir = $base . '/httpcache-modpages-' . getmypid() . '-' . mt_rand();
        $this->sessions = $this->dir . '-sessions';
        mkdir( $this->dir, 0770, true );
        mkdir( $this->sessions, 0770, true );
    }

    protected function tearDown(): void
    {
        foreach ( array( $this->dir, $this->sessions ) as $d )
            $this->removeTree( $d );
    }

    private function removeTree( $d )
    {
        if ( !is_dir( $d ) )
            return;
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $d, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $f )
            $f->isDir() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
        rmdir( $d );
    }

    private function contract()
    {
        return new ezpHttpCacheContract( array(
            'enabled' => true, 'secret' => str_repeat( 'k', 64 ), 'dir' => $this->dir,
            'hosts' => array( 'example.org' => 'site' ), 'sessionCookie' => array( 'site' => 'eZSESSID' ),
            'sessionSavePath' => $this->sessions, 'formTokenSecret' => 'secret', 'maxAge' => 300, 'swr' => 0,
            'apcu' => false,
        ) );
    }

    private function request( $uri, $sid )
    {
        return array( 'scheme' => 'https', 'host' => 'example.org', 'uri' => $uri, 'method' => 'GET',
                      'cookies' => array( 'eZSESSID' => $sid ), 'acceptEncoding' => '', 'ifNoneMatch' => null );
    }

    /** A signed-in user's session and their page of a module view, as the listener stores it */
    private function signIn( ezpHttpCacheContract $c, $userID, $sid, $uri, $body, array $tags )
    {
        file_put_contents( $this->sessions . '/sess_' . $sid, 'eZUserLoggedInID|i:' . $userID . ';' );
        $c->storeRecord( $userID, $c->privateContext( $userID, 'site' ) );
        $key = $c->entryKey( 'https', 'example.org', 'site', $uri, $c->sessionContext( $userID, $sid, 'site' ) );
        $c->storeEntry( $key, 200, array( 'Content-Type' => 'text/html' ), $body, $tags, array(), 60 );
    }

    /** MP-01 */
    public function testASessionContextBelongsToOneSessionOfOneUser()
    {
        $c = $this->contract();
        $a = $c->sessionContext( 14, str_repeat( 'a', 26 ), 'site' );
        $this->assertSame( $a, $c->sessionContext( 14, str_repeat( 'a', 26 ), 'site' ) );
        foreach ( array( $c->sessionContext( 14, str_repeat( 'b', 26 ), 'site' ), $c->sessionContext( 15, str_repeat( 'a', 26 ), 'site' ),
                         $c->sessionContext( 14, str_repeat( 'a', 26 ), 'admin' ), $c->privateContext( 14, 'site' ) ) as $other )
            $this->assertNotSame( $a, $other );
    }

    /** MP-02 */
    public function testThePageIsServedToItsSessionOnly()
    {
        $c = $this->contract();
        $mine = str_repeat( 'm', 26 );
        $this->signIn( $c, 14, $mine, '/centre/work', '<p>work of 14</p>', array( 'centre', 'centre-u14' ) );
        $hit = $c->serve( $this->request( '/centre/work', $mine ) );
        $this->assertIsArray( $hit );
        $this->assertSame( '<p>work of 14</p>', $hit[2] );
        $this->assertStringStartsWith( 'private', $hit[1]['Cache-Control'] );

        // the same user in another browser: their own session, never this page
        $other = str_repeat( 'o', 26 );
        file_put_contents( $this->sessions . '/sess_' . $other, 'eZUserLoggedInID|i:14;' );
        $this->assertNull( $c->serve( $this->request( '/centre/work', $other ) ) );
        // another user who sends the first session's address with a session of their own
        $third = str_repeat( 't', 26 );
        file_put_contents( $this->sessions . '/sess_' . $third, 'eZUserLoggedInID|i:15;' );
        $c->storeRecord( 15, $c->privateContext( 15, 'site' ) );
        $this->assertNull( $c->serve( $this->request( '/centre/work', $third ) ) );
        // anonymous
        $this->assertNull( $c->serve( $this->request( '/centre/work', '' ) ) );
    }

    /** MP-03 */
    public function testAPurgedTagOrAForgottenUserDropsThePage()
    {
        $c = $this->contract();
        $sid = str_repeat( 's', 26 );
        $this->signIn( $c, 14, $sid, '/centre/home', '<p>home</p>', array( 'centre', 'centre-u14' ) );
        $this->assertIsArray( $c->serve( $this->request( '/centre/home', $sid ) ) );
        usleep( 20000 );
        $c->purgeTags( array( 'centre-u14' ) );
        $this->assertNull( $c->serve( $this->request( '/centre/home', $sid ) ) );

        $this->signIn( $c, 14, $sid, '/centre/home', '<p>home 2</p>', array( 'centre' ) );
        $this->assertIsArray( $c->serve( $this->request( '/centre/home', $sid ) ) );
        $c->deleteRecord( 14 );           // a changed role (eZUser) forgets the user
        $this->assertNull( $c->serve( $this->request( '/centre/home', $sid ) ) );
    }

    /** MP-04 */
    public function testTheRedisSavePathIsRead()
    {
        $this->assertSame( array( '127.0.0.1', 6379, 'PHPREDIS_SESSION:', 0 ), ezpHttpCacheContract::redisSessionTarget( 'redis', 'tcp://127.0.0.1:6379' ) );
        $this->assertSame( array( '10.0.0.5', 6380, 'S:', 2 ), ezpHttpCacheContract::redisSessionTarget( 'redis', 'tcp://10.0.0.5:6380?prefix=S:&database=2' ) );
        $this->assertSame( array( '/run/valkey.sock', 0, 'PHPREDIS_SESSION:', 0 ), ezpHttpCacheContract::redisSessionTarget( 'redis', 'unix:///run/valkey.sock' ) );
        $this->assertNull( ezpHttpCacheContract::redisSessionTarget( 'redis', 'tcp://127.0.0.1:6379?auth=secret' ), 'a password never goes into the contract' );
        $this->assertNull( ezpHttpCacheContract::redisSessionTarget( 'redis', 'tcp://a:1, tcp://b:2' ), 'several servers: not known' );
        $this->assertNull( ezpHttpCacheContract::redisSessionTarget( 'files', '/var/lib/php/session' ) );
        $this->assertNull( ezpHttpCacheContract::redisSessionTarget( 'redis', '' ) );
    }

    /** MP-05 */
    public function testTheUserIdOfASessionIsRead()
    {
        $this->assertSame( 14, ezpHttpCacheContract::sessionUserIDOf( 'a|i:1;eZUserLoggedInID|i:14;' ) );
        $this->assertSame( 15, ezpHttpCacheContract::sessionUserIDOf( 'eZUserLoggedInID|s:2:"15";' ) );
        $this->assertSame( 0, ezpHttpCacheContract::sessionUserIDOf( 'xeZUserLoggedInID|i:14;' ) );
        $this->assertSame( 0, ezpHttpCacheContract::sessionUserIDOf( '' ) );
    }
}
