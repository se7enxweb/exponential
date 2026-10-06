<?php
/**
 * The catalogue of Setup > Caches (expCacheCatalogue), from described items built by hand: no database, no files.
 *
 *  CC-01 - Caches are grouped by id, then by their first known tag, else "other"
 *  CC-02 - Each cache says what it holds, its masked directory, its command and the Velocity hints
 *  CC-03 - Sizes are only there when measured; a cut-short count is marked
 *  CC-04 - Groups come in page order with their totals, ids of enabled caches and command; empty groups are left
 *          out except "velocity", which also holds the server's own caches
 *  CC-05 - The overview counts caches, enabled and disabled, sums sizes and takes the latest clear
 *  CC-06 - consequences() names what a clear reaches and whether Velocity needs a restart afterwards
 *  CC-07 - Audit records are normalised: time, how, asked, cleared ids, who (only when allowed), shell or page
 *  CC-08 - The newest clear per cache from records: cleared ids, else asked ids or tags, else all; merged with the
 *          kernel's expiry timestamps (a record within half a minute of the expiry is the same clear), shown as "just
 *          now", minutes or hours ago, with who cleared; this request's own clear before its record is written
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expCacheCatalogueTest extends PHPUnit\Framework\TestCase
{
    const ROOT = '/var/www/vhosts/example.org/doc/site';

    private function item( $id, $name, array $tags, $path = null, array $extra = array() )
    {
        return $extra + array( 'id' => $id, 'name' => $name, 'tags' => $tags, 'enabled' => true, 'how' => 'directory removed',
                               'path' => $path, 'exists' => $path !== null );
    }

    private function catalogue( $measured = false, array $lastCleared = array() )
    {
        $size = function ( $files, $bytes, $complete = true ) use ( $measured ) {
            return $measured ? array( 'files' => $files, 'bytes' => $bytes, 'complete' => $complete ) : array();
        };
        return new expCacheCatalogue( array(
            $this->item( 'content', 'Content view cache', array( 'content' ), self::ROOT . '/var/site/cache/content', $size( 100, 1048576 ) ),
            $this->item( 'template-block', 'Template block cache', array( 'template', 'content' ), self::ROOT . '/var/site/cache/template-block', $size( 10, 2048 ) ),
            $this->item( 'global_ini', 'Global INI cache', array( 'ini' ), self::ROOT . '/var/cache/ini', $size( 5, 4096 ) ),
            $this->item( 'sslzones', 'SSL Zones cache', array( 'ini' ), null, array( 'enabled' => false ) ),
            $this->item( 'imagealias', 'Image alias', array( 'image' ) ),
            $this->item( 'exphttpcache', 'HTTP cache', array( 'content', 'template' ) ),
            $this->item( 'myext_feeds', 'Feed cache of an extension', array( 'content' ), '/srv/other/feeds', $size( 3, 100, false ) ),
            $this->item( 'myext_misc', 'Misc cache of an extension', array( 'something' ) ),
        ), array( 'root' => self::ROOT, 'measured' => $measured, 'last_cleared' => $lastCleared ) );
    }

    private function find( expCacheCatalogue $c, $id )
    {
        foreach ( $c->items() as $item )
            if ( $item['id'] === $id )
                return $item;
        return null;
    }

    /** CC-01 */
    public function testGroups()
    {
        $this->assertSame( 'content', expCacheCatalogue::groupOf( 'content' ) );
        $this->assertSame( 'templates', expCacheCatalogue::groupOf( 'template-block', array( 'template', 'content' ) ) );
        $this->assertSame( 'settings', expCacheCatalogue::groupOf( 'global_ini' ) );
        $this->assertSame( 'images', expCacheCatalogue::groupOf( 'imagealias' ) );
        $this->assertSame( 'velocity', expCacheCatalogue::groupOf( 'exphttpcache' ) );
        $this->assertSame( 'content', expCacheCatalogue::groupOf( 'myext_feeds', array( 'rest', 'content' ) ) );
        $this->assertSame( 'other', expCacheCatalogue::groupOf( 'myext_misc', array( 'something' ) ) );
    }

    /** CC-02 */
    public function testItems()
    {
        $c = $this->catalogue();
        $content = $this->find( $c, 'content' );
        $this->assertSame( 'var/site/cache/content', $content['path'] );
        $this->assertStringContainsString( 'rendered views', $content['holds'] );
        $this->assertSame( 'php bin/php/ezcache.php --clear-id=content --allow-root-user', $content['command'] );
        $this->assertTrue( $content['response_cache'] );
        $this->assertFalse( $content['restart'] );
        $ini = $this->find( $c, 'global_ini' );
        $this->assertTrue( $ini['restart'] );
        $this->assertSame( '…/other/feeds', $this->find( $c, 'myext_feeds' )['path'] );
        $this->assertSame( '', $this->find( $c, 'imagealias' )['path'] );
        $this->assertSame( '', $this->find( $c, 'myext_misc' )['holds'] );
        $this->assertStringContainsString( 'content view cache', $content['search'] );
    }

    /** CC-03 */
    public function testSizes()
    {
        $this->assertFalse( $this->find( $this->catalogue(), 'content' )['measured'] );
        $this->assertNull( $this->find( $this->catalogue(), 'content' )['bytes'] );
        $c = $this->catalogue( true );
        $content = $this->find( $c, 'content' );
        $this->assertSame( 1048576, $content['bytes'] );
        $this->assertSame( '1.0 MB', $content['size_text'] );
        $this->assertSame( '≥ 100 B', $this->find( $c, 'myext_feeds' )['size_text'] );
    }

    /** CC-04 */
    public function testGroupList()
    {
        $groups = $this->catalogue( true )->groups();
        $this->assertSame( array( 'content', 'templates', 'settings', 'images', 'velocity', 'other' ), array_keys( $groups ) );
        $this->assertSame( array( 'content', 'myext_feeds' ), $groups['content']['ids'] );
        $this->assertSame( 1048676, $groups['content']['bytes'] );
        $this->assertStringStartsWith( '≥ ', $groups['content']['size_text'] );
        $this->assertSame( array( 'global_ini' ), $groups['settings']['ids'], 'a disabled cache is not cleared' );
        $this->assertSame( 2, $groups['settings']['count'] );
        $this->assertTrue( $groups['settings']['restart'] );
        $this->assertSame( 'php bin/php/ezcache.php --clear-id=content,myext_feeds --allow-root-user', $groups['content']['command'] );

        $only = new expCacheCatalogue( array( $this->item( 'content', 'Content view cache', array( 'content' ) ) ) );
        $this->assertSame( array( 'content', 'velocity' ), array_keys( $only->groups() ) );
    }

    /** CC-05 */
    public function testOverview()
    {
        $o = $this->catalogue( true, array( 'content' => 1000, 'template-block' => 2000 ) )->overview();
        $this->assertSame( 8, $o['caches'] );
        $this->assertSame( 7, $o['enabled'] );
        $this->assertSame( 1, $o['disabled'] );
        $this->assertSame( 118, $o['files'] );
        $this->assertSame( 1048576 + 2048 + 4096 + 100, $o['bytes'] );
        $this->assertFalse( $o['complete'] );
        $this->assertSame( 2000, $o['last_cleared'] );
        $this->assertSame( array( 'global_ini', 'sslzones' ), $o['restart'] );
        $this->assertSame( '', $this->catalogue()->overview()['size_text'] );
        $this->assertSame( 2000, $this->find( $this->catalogue( false, array( 'template-block' => 2000 ) ), 'template-block' )['last_cleared'] );
    }

    /** CC-06 */
    public function testConsequences()
    {
        $c = $this->catalogue();
        $what = $c->consequences( array( 'content', 'global_ini' ) );
        $this->assertSame( array( 'Content view cache', 'Global INI cache' ), $what['names'] );
        $this->assertTrue( $what['restart'] );
        $this->assertTrue( $what['response_cache'] );
        $this->assertSame( 'php bin/php/ezcache.php --clear-id=content,global_ini --allow-root-user', $what['command'] );
        $this->assertSame( array(), $c->consequences( array( 'nope' ) )['names'] );
        $this->assertSame( 7, count( $c->ids() ) );
        $this->assertSame( array( 'imagealias' ), $c->ids( 'images' ) );
    }

    private function record( $time, array $object, ?array $after = null, array $actor = array( 'login' => 'editor', 'user_id' => 14 ), $engine = 'fpm' )
    {
        $rec = array( 'name' => 'system.cache.clear', 'id' => 'r' . $time . implode( ',', $object ), 'time' => gmdate( 'Y-m-d\TH:i:s.000\Z', $time ),
                      'object' => $object + array( 'type' => 'cache' ), 'actor' => $actor, 'request' => array( 'engine' => $engine ) );
        if ( $after !== null )
            $rec['after'] = $after;
        return $rec;
    }

    /** CC-07 */
    public function testNormaliseRecord()
    {
        $r = expCacheCatalogue::normaliseRecord( $this->record( 1000, array( 'how' => 'id', 'id' => 'content,template-block' ), array( 'ids' => array( 'content', 'template-block' ) ) ) );
        $this->assertSame( 1000, $r['time'] );
        $this->assertSame( 'id', $r['how'] );
        $this->assertSame( array( 'content', 'template-block' ), $r['asked'] );
        $this->assertSame( array( 'content', 'template-block' ), $r['ids'] );
        $this->assertSame( 'editor', $r['who'] );
        $this->assertFalse( $r['shell'] );
        $shell = expCacheCatalogue::normaliseRecord( $this->record( 1000, array( 'how' => 'all', 'id' => 'all' ), null,
                                                                    array( 'cli' => array( 'os_user' => 'root' ) ), 'cli' ) );
        $this->assertSame( 'os:root', $shell['who'] );
        $this->assertTrue( $shell['shell'] );
        $this->assertSame( array(), $shell['asked'] );
        // the login of the index row when the record has none; nobody when the user may not read the channel
        $this->assertSame( 'admin', expCacheCatalogue::normaliseRecord( $this->record( 1, array( 'how' => 'id', 'id' => 'x' ), null, array() ), 'admin' )['who'] );
        $this->assertSame( '', expCacheCatalogue::normaliseRecord( $this->record( 1, array( 'how' => 'id', 'id' => 'x' ) ), '', false )['who'] );
        $this->assertNull( expCacheCatalogue::normaliseRecord( array( 'name' => 'system.settings.change', 'time' => '2026-10-06T10:00:00Z' ) ) );
        $this->assertNull( expCacheCatalogue::normaliseRecord( array( 'name' => 'system.cache.clear' ) ) );
    }

    /** CC-08 */
    public function testLastClearedFromAudit()
    {
        $records = array_map( array( 'expCacheCatalogue', 'normaliseRecord' ), array(
            $this->record( 100, array( 'how' => 'all', 'id' => 'all' ), null, array( 'cli' => array( 'os_user' => 'root' ) ), 'cli' ),
            $this->record( 200, array( 'how' => 'tag', 'id' => 'template' ) ),
            $this->record( 300, array( 'how' => 'id', 'id' => 'content' ), array( 'ids' => array( 'content' ) ), array( 'login' => 'admin' ) ),
            $this->record( 250, array( 'how' => 'purge', 'id' => 'imagealias' ) ),
        ) );
        $map = expCacheCatalogue::lastClearedFromAudit( $records, array( 'template' => array( 'template', 'template-block' ) ),
                                                        array( 'content', 'template', 'template-block', 'imagealias', 'global_ini' ) );
        $this->assertSame( 300, $map['content']['time'] );
        $this->assertSame( 'admin', $map['content']['who'] );
        $this->assertSame( 200, $map['template-block']['time'] );
        $this->assertSame( 'editor', $map['template-block']['who'] );
        $this->assertSame( 250, $map['imagealias']['time'] );
        $this->assertSame( 100, $map['global_ini']['time'] );
        $this->assertTrue( $map['global_ini']['shell'] );

        // merged with the expiry timestamps: the newer wins, the audit brings who
        $now = 10000;
        $c = new expCacheCatalogue( array(
            $this->item( 'content', 'Content view cache', array( 'content' ) ),
            $this->item( 'template-block', 'Template block cache', array( 'template' ) ),
            $this->item( 'global_ini', 'Global INI cache', array( 'ini' ) ),
            $this->item( 'sortkey', 'Sort key cache', array( 'content' ) ),
        ), array( 'time' => $now, 'last_cleared' => array( 'content' => $now - 60, 'template-block' => $now - 7000 ),
                  'audit' => array( 'content' => array( 'time' => $now - 4000, 'who' => 'admin', 'shell' => false ),
                                    'template-block' => array( 'time' => $now - 30, 'who' => 'admin', 'shell' => false ),
                                    'global_ini' => array( 'time' => $now - 7300, 'who' => 'os:root', 'shell' => true ) ) ) );
        $content = $this->find( $c, 'content' );
        $this->assertSame( 'just now', $content['last_cleared_text'] );
        $this->assertSame( '', $content['last_cleared_by'], 'the expiry timestamp is newer and names nobody' );
        $block = $this->find( $c, 'template-block' );
        $this->assertSame( 'just now', $block['last_cleared_text'] );
        $this->assertSame( 'admin', $block['last_cleared_by'] );
        $ini = $this->find( $c, 'global_ini' );
        $this->assertSame( '2 hours ago', $ini['last_cleared_text'] );
        $this->assertTrue( $ini['last_cleared_shell'] );
        $this->assertSame( '', $this->find( $c, 'sortkey' )['last_cleared_text'] );
        $this->assertSame( '15 minutes ago', $c->ago( $now - 900 ) );

        // the same clear: a record up to half a minute before the expiry names who cleared; an older one does not
        $near = new expCacheCatalogue( array( $this->item( 'content', 'Content view cache', array( 'content' ) ),
                                              $this->item( 'sortkey', 'Sort key cache', array( 'content' ) ) ),
                                       array( 'time' => 5000, 'last_cleared' => array( 'content' => 4990, 'sortkey' => 4990 ),
                                              'audit' => array( 'content' => array( 'time' => 4970, 'who' => 'admin', 'shell' => false ),
                                                                'sortkey' => array( 'time' => 4900, 'who' => 'admin', 'shell' => false ) ) ) );
        $this->assertSame( 'admin', $this->find( $near, 'content' )['last_cleared_by'] );
        $this->assertSame( 4990, $this->find( $near, 'content' )['last_cleared'] );
        $this->assertSame( '', $this->find( $near, 'sortkey' )['last_cleared_by'] );

        // cleared by this request, before its own audit record is written
        $now = new expCacheCatalogue( array( $this->item( 'sortkey', 'Sort key cache', array( 'content' ) ) ),
                                      array( 'time' => 5000, 'cleared_now' => array( 'ids' => array( 'sortkey' ), 'who' => 'admin' ) ) );
        $this->assertSame( 'just now', $this->find( $now, 'sortkey' )['last_cleared_text'] );
        $this->assertSame( 'admin', $this->find( $now, 'sortkey' )['last_cleared_by'] );
        $this->assertSame( array( 'sortkey' ), $now->consequences( array( 'sortkey' ) )['ids'] );
    }
}
