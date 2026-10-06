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
}
