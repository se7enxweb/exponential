<?php
/**
 * The manifest of a static build (expStaticManifest): pure apart from the file round trip under var/tmp.
 *
 *  MF-01 entries, counts and bytes by kind; paths in their one form, paths that leave the export refused
 *  MF-02 the diff: added, changed, removed, unchanged, bytes; against no manifest everything is new
 *  MF-03 JSON: sorted, \n line ends, the same bytes whatever the order of adding; Windows separators normalised
 *  MF-04 save and load round trip; a broken or foreign file loads as null
 *  MF-05 a partial build keeps the entries of the build before it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticManifestTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function tearDown(): void
    {
        if ( $this->dir !== null )
            expStaticBuild::removeDirectory( $this->dir );
    }

    private static function manifest( array $files )
    {
        $m = new expStaticManifest( array( 'target' => 't' ) );
        foreach ( $files as $path => $entry )
            $m->add( $path, $entry[0], $entry[1], $entry[2], isset( $entry[3] ) ? $entry[3] : array() );
        return $m;
    }

    /** MF-01 */
    public function testEntries()
    {
        $m = self::manifest( array( 'index.html' => array( 'page', 'a', 10, array( 'url' => '/', 'node' => 2, 'modified' => 1700000000 ) ),
                                    'var/x.png' => array( 'file', 'b', 100 ), 'var/s.jpg' => array( 'storage', 'c', 1000 ), '.nojekyll' => array( 'extra', 'd', 0 ) ) );
        $this->assertSame( 4, $m->count() );
        $this->assertSame( 1, $m->count( 'page' ) );
        $this->assertSame( 1110, $m->bytes() );
        $this->assertSame( 1000, $m->bytes( 'storage' ) );
        $this->assertSame( array( 'kind' => 'page', 'sha256' => 'a', 'size' => 10, 'url' => '/', 'node' => 2, 'modified' => 1700000000 ), $m->get( 'index.html' ) );
        $this->assertSame( array( '.nojekyll', 'index.html', 'var/s.jpg', 'var/x.png' ), array_keys( $m->entries() ) );
        $this->assertSame( 'a/b.html', expStaticManifest::normalisePath( 'a\\b.html' ) );
        foreach ( array( '/abs', '../x', 'a/../b', 'a//b', 'C:/x', '', './a' ) as $bad )
            $this->assertNull( expStaticManifest::normalisePath( $bad ), $bad );
        $this->expectException( InvalidArgumentException::class );
        $m->add( '../outside', 'page', 'x', 1 );
    }

    /** MF-02 */
    public function testDiff()
    {
        $old = self::manifest( array( 'a.html' => array( 'page', '1', 10 ), 'b.html' => array( 'page', '2', 20 ), 'c.css' => array( 'file', '3', 30 ) ) );
        $new = self::manifest( array( 'a.html' => array( 'page', '1', 10 ), 'b.html' => array( 'page', '9', 25 ), 'd.js' => array( 'file', '4', 40 ) ) );
        $diff = expStaticManifest::diff( $old, $new );
        $this->assertSame( array( 'd.js' ), $diff['added'] );
        $this->assertSame( array( 'b.html' ), $diff['changed'] );
        $this->assertSame( array( 'c.css' ), $diff['removed'] );
        $this->assertSame( 1, $diff['unchanged'] );
        $this->assertSame( 65, $diff['bytes_added'] );
        $this->assertSame( 50, $diff['bytes_removed'] );
        $first = expStaticManifest::diff( null, $new );
        $this->assertSame( array( 'a.html', 'b.html', 'd.js' ), $first['added'] );
        $this->assertSame( array(), $first['removed'] );
    }

    /** MF-03 */
    public function testJson()
    {
        $a = self::manifest( array( 'b.html' => array( 'page', '2', 2 ), 'a.html' => array( 'page', '1', 1 ) ) );
        $b = self::manifest( array( 'a.html' => array( 'page', '1', 1 ), 'sub\\b.html' => array( 'page', '2', 2 ) ) );
        $this->assertSame( $a->toJson(), self::manifest( array( 'a.html' => array( 'page', '1', 1 ), 'b.html' => array( 'page', '2', 2 ) ) )->toJson() );
        $json = $a->toJson();
        $this->assertStringNotContainsString( "\r", $json );
        $this->assertStringEndsWith( "}\n", $json );
        $data = json_decode( $json, true );
        $this->assertSame( 'exp-static-manifest/1', $data['format'] );
        $this->assertSame( array( 'a.html', 'b.html' ), array_keys( $data['files'] ) );
        $this->assertTrue( $b->has( 'sub/b.html' ), 'Windows separators are stored as /' );
        $this->assertNull( expStaticManifest::normalisePath( 'b\\..\\b.html' ), 'a path with .. is refused, not normalised into another one' );
    }

    /** MF-04 */
    public function testSaveAndLoad()
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/static-manifest-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        $m = self::manifest( array( 'index.html' => array( 'page', hash( 'sha256', 'x' ), 1, array( 'node' => 2 ) ) ) );
        $this->assertTrue( $m->save( $this->dir ) );
        $this->assertFileExists( $this->dir . '/.static-publish/manifest.json' );
        $loaded = expStaticManifest::load( $this->dir );
        $this->assertNotNull( $loaded );
        $this->assertSame( $m->toJson(), $loaded->toJson() );
        $this->assertNull( expStaticManifest::load( $this->dir, 'missing.json' ) );
        file_put_contents( $this->dir . '/broken.json', '{"format":"other/1","files":{}}' );
        $this->assertNull( expStaticManifest::load( $this->dir, 'broken.json' ) );
        file_put_contents( $this->dir . '/broken.json', 'not json' );
        $this->assertNull( expStaticManifest::load( $this->dir, 'broken.json' ) );
    }

    /** MF-05 */
    public function testKeepFrom()
    {
        $old = self::manifest( array( 'a.html' => array( 'page', '1', 1 ), 'b.html' => array( 'page', '2', 2 ) ) );
        $new = self::manifest( array( 'b.html' => array( 'page', '3', 3 ) ) );
        $new->keepFrom( $old );
        $this->assertSame( array( 'a.html', 'b.html' ), array_keys( $new->entries() ) );
        $this->assertSame( '3', $new->get( 'b.html' )['sha256'], 'the new entry wins' );
    }
}
