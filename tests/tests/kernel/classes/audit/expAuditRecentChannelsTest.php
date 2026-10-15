<?php
/**
 * The audit/recent view (\Exponential\View\Kernel\Audit\Recent) shows only the channels the Channel limitation of
 * audit/read allows, as the console does, and "Verify now" there needs audit/manage. The helpers are pure and
 * read through a reader held in memory; no database, no files.
 *
 *  AR-01 - every channel without a limitation; only the allowed ones with it
 *  AR-02 - the latest events of all channels: only those of the allowed channels, the newest of them up to the limit
 *  AR-03 - the latest events of one channel: none when the channel is not allowed
 *  AR-04 - the view refuses a channel that is not allowed and checks audit/manage before verifying
 *  AR-05 - the Verify now form of both designs posts back to the same channel filter
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Audit\Recent;

/** A reader held in memory: channel => records, newest first */
class expAuditRecentChannelsTestReader
{
    public $records;

    public function __construct( array $records )
    {
        $this->records = $records;
    }

    public function channels()
    {
        $out = array();
        foreach ( $this->records as $c => $records )
            $out[$c] = array( 'files' => array(), 'bytes' => 0, 'newest' => '' );
        return $out;
    }

    public function latest( $limit = 100, $channel = null )
    {
        $all = array();
        foreach ( $this->records as $c => $records )
            if ( $channel === null || $c === $channel )
                $all = array_merge( $all, $records );
        usort( $all, function ( $a, $b ) { return strcmp( $b['time'], $a['time'] ); } );
        return array_slice( $all, 0, $limit );
    }
}

class expAuditRecentChannelsTest extends PHPUnit\Framework\TestCase
{
    private static function reader()
    {
        $records = array();
        foreach ( array( 'access', 'content', 'system' ) as $i => $c )
            for ( $n = 0; $n < 5; $n++ )
                $records[$c][] = array( 'name' => "$c.event", 'channel' => $c, 'time' => sprintf( '2026-10-14T10:%02d:%02dZ', $n, $i ), 'id' => "$c-$n" );
        return new expAuditRecentChannelsTestReader( $records );
    }

    private static function channelsOf( array $records )
    {
        $c = array_values( array_unique( array_map( function ( $r ) { return $r['channel']; }, $records ) ) );
        sort( $c );
        return $c;
    }

    /** AR-01 */
    public function testTheChannelListFollowsTheLimitation()
    {
        $channels = self::reader()->channels();
        $this->assertSame( array( 'access', 'content', 'system' ), array_keys( Recent::allowedChannelInfo( $channels, null ) ) );
        $this->assertSame( array( 'content' ), array_keys( Recent::allowedChannelInfo( $channels, array( 'content' ) ) ) );
        $this->assertSame( array(), Recent::allowedChannelInfo( $channels, array() ) );
        $this->assertTrue( Recent::channelAllowed( 'access', null ) );
        $this->assertFalse( Recent::channelAllowed( 'access', array( 'content' ) ) );
    }

    /** AR-02 */
    public function testAllChannelsMeansTheAllowedOnes()
    {
        $reader = self::reader();
        $this->assertSame( array( 'access', 'content', 'system' ), self::channelsOf( Recent::latestAllowed( $reader, 100, null, null ) ) );

        $events = Recent::latestAllowed( $reader, 100, null, array( 'content', 'system' ) );
        $this->assertCount( 10, $events );
        $this->assertNotContains( 'access', self::channelsOf( $events ) );

        $events = Recent::latestAllowed( $reader, 3, null, array( 'access' ) );
        $this->assertSame( array( 'access-4', 'access-3', 'access-2' ), array_column( $events, 'id' ), 'the newest of the allowed channel, up to the limit' );

        $this->assertSame( array(), Recent::latestAllowed( $reader, 100, null, array() ) );
    }

    /** AR-03 */
    public function testOneChannelOnlyWhenAllowed()
    {
        $reader = self::reader();
        $this->assertSame( array(), Recent::latestAllowed( $reader, 100, 'access', array( 'content' ) ) );
        $this->assertSame( array( 'content' ), self::channelsOf( Recent::latestAllowed( $reader, 100, 'content', array( 'content' ) ) ) );
        $this->assertSame( array( 'access' ), self::channelsOf( Recent::latestAllowed( $reader, 100, 'access', null ) ) );
    }

    /** AR-04 */
    public function testTheViewChecksTheChannelAndManage()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/audit/recent.php' );
        $run = substr( $source, strpos( $source, 'public function run(' ), strpos( $source, 'public static function channelAllowed' ) - strpos( $source, 'public function run(' ) );
        $this->assertMatchesRegularExpression( '/!self::channelAllowed\( \$channel, \$allowed \) \)\s*return \$Params\[\'Module\'\]->handleError\( \\\\eZError::KERNEL_ACCESS_DENIED/', $run );
        $this->assertStringContainsString( 'self::allowedChannelInfo( $reader->channels(), $allowed )', $run );
        $this->assertStringContainsString( 'self::latestAllowed( $reader, self::LIMIT, $channel, $allowed )', $run );
        $this->assertStringNotContainsString( '$reader->latest(', $run );
        $verify = substr( $run, strpos( $run, 'if ( $verifyNow )' ) );
        $this->assertLessThan( strpos( $verify, 'expAuditReauth::gate' ), strpos( $verify, '\\expAuditConsole::canManage()' ), 'audit/manage before the password form' );
    }

    /** AR-05 */
    public function testVerifyNowKeepsTheChannelFilter()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $source = file_get_contents( dirname( __DIR__, 5 ) . "/design/$design/templates/audit/recent.tpl" );
            $this->assertStringContainsString( "concat( '/(channel)/', \$channel )", $source, $design );
        }
    }
}
