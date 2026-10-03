<?php
/**
 * Every real INI file of the installation (settings/ and extension/<ext>/settings/, recursively), on a copy in
 * var/tmp/ini-tests/: the writer reads it exactly as eZINI does and writes it back unchanged.
 *
 *  RT-01 — with no edit, the model's bytes are the file's bytes, and save() writes nothing
 *  RT-02 — the values the writer reads are the values eZINI::parseFile() reads (direct access, no codec)
 *  RT-03 — set a new variable in the file's last block and remove it again: byte-identical to the original
 *  RT-04 — add a new block and an array value; eZINI reads the original values plus the new ones, and the PHP
 *          wrapper still closes after the new block
 *
 * The real files are only read. The copies sit in var/tmp/ini-tests/roundtrip/<n>/ (overwritten on each run).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ini
 */

require_once __DIR__ . '/fixtures/expinienginetestfixtures.php';

class expIniRoundTripTest extends PHPUnit\Framework\TestCase
{
    /** @var array relative real path => relative copy path */
    protected static $copies = array();

    public static function setUpBeforeClass(): void
    {
        $real = expIniEngineTestFixtures::realRoot();
        $base = 'var/tmp/ini-tests/roundtrip';
        foreach ( expIniEngineTestFixtures::realIniFiles() as $n => $rel )
        {
            // one directory per file, named by the file: eZINI reads a file's .append and .append.php
            // neighbours too, so a copy left there by an earlier run (the numbering shifts whenever an INI file
            // is added) would be merged into the values read back
            $dir = "$base/" . md5( $rel );
            if ( !is_dir( $real . $dir ) )
                mkdir( $real . $dir, 0700, true );
            foreach ( (array)glob( $real . $dir . '/*' ) as $stale )
                if ( is_file( $stale ) )
                    unlink( $stale );
            $copy = $dir . '/' . basename( $rel );
            copy( $real . $rel, $real . $copy );
            // copies of settings/override can hold secrets
            chmod( $real . $copy, 0600 );
            self::$copies[$rel] = $copy;
        }
    }

    public function tearDown(): void
    {
        expIniEditor::setRoot( null );
        parent::tearDown();
    }

    /** An editor of one copy: a scope whose directory is the copy's directory. */
    private function editorFor( $copy )
    {
        $real = expIniEngineTestFixtures::realRoot();
        $name = basename( $copy );
        $suffix = substr( $name, strpos( $name, '.ini' ) );
        $scope = new expIniScope( 'copy', 'copy', dirname( $copy ), $real, 'copy', array( 'suffix' => $suffix ) );
        return new expIniEditor( $scope, $name );
    }

    private function eZINIValues( $copy )
    {
        $ini = new eZINI( $copy, false, false, false, false, true );
        return $ini->groups();
    }

    public function testThereAreManyFiles()
    {
        $this->assertGreaterThan( 300, count( self::$copies ), 'every real INI file is tested' );
        fwrite( STDERR, "\n[round-trip] " . count( self::$copies ) . " real INI files\n" );
    }

    /** RT-01, RT-02 */
    public function testReadUnchangedAndAsEZINIReads()
    {
        $real = expIniEngineTestFixtures::realRoot();
        $mismatch = array();
        foreach ( self::$copies as $rel => $copy )
        {
            $bytes = file_get_contents( $real . $copy );
            $writer = new expIniWriter( $bytes );
            $this->assertSame( $bytes, $writer->content(), "$rel: model bytes" );
            $e = $this->editorFor( $copy );
            $this->assertSame( $bytes, $e->content(), "$rel: editor bytes" );
            $this->assertFalse( $e->save( array( 'backup' => false ) )->written(), "$rel: nothing written" );
            $this->assertSame( $bytes, file_get_contents( $real . $copy ) );
            if ( $writer->values() != $this->eZINIValues( $copy ) )
                $mismatch[] = $rel;
        }
        $this->assertSame( array(), $mismatch, 'files the writer reads differently from eZINI' );
    }

    /** RT-03 */
    public function testSetAndRemoveIsByteIdentical()
    {
        $real = expIniEngineTestFixtures::realRoot();
        $tested = 0;
        foreach ( self::$copies as $rel => $copy )
        {
            $bytes = file_get_contents( $real . $copy );
            $e = $this->editorFor( $copy );
            $blocks = $e->blocks();
            if ( !$blocks )
                continue;
            $block = end( $blocks );
            if ( !expIniEditorTestBlockOk( $block ) )
                continue;
            $e->set( $block, 'ExpIniRoundTripProbe', '1' );
            $e->save( array( 'backup' => false ) );
            $this->assertSame( '1', $this->eZINIValues( $copy )[$block]['ExpIniRoundTripProbe'], "$rel: probe readable by eZINI" );
            $e2 = $this->editorFor( $copy );
            $e2->remove( $block, 'ExpIniRoundTripProbe' );
            $e2->save( array( 'backup' => false ) );
            $this->assertSame( $bytes, file_get_contents( $real . $copy ), "$rel: byte-identical after set + remove" );
            ++$tested;
        }
        $this->assertGreaterThan( 300, $tested );
    }

    /** RT-05 — a new block and removeBlock(): byte-identical, for every file */
    public function testNewBlockAndRemoveBlockIsByteIdentical()
    {
        $real = expIniEngineTestFixtures::realRoot();
        foreach ( self::$copies as $rel => $copy )
        {
            $bytes = file_get_contents( $real . $copy );
            $e = $this->editorFor( $copy );
            $e->set( 'ExpIniSmokeTest', 'Probe', '1' );
            $e->save( array( 'backup' => false ) );
            $e2 = $this->editorFor( $copy );
            $e2->remove( 'ExpIniSmokeTest', 'Probe' );
            $e2->removeBlock( 'ExpIniSmokeTest', true );
            $e2->save( array( 'backup' => false ) );
            $this->assertSame( $bytes, file_get_contents( $real . $copy ), "$rel: byte-identical after new block + removeBlock" );
        }
    }

    /** RT-06 — every block's lines re-inserted into an empty file give the same values, and the whole block
     *  inserted and removed again leaves the file byte-identical */
    public function testBlockLinesReinsertedGiveTheSameValues()
    {
        $real = expIniEngineTestFixtures::realRoot();
        $base = 'var/tmp/ini-tests/roundtrip-blocks';
        $blocks = 0;
        foreach ( self::$copies as $rel => $copy )
        {
            $e = $this->editorFor( $copy );
            $dir = $base . '/' . basename( dirname( $copy ) );
            if ( !is_dir( $real . $dir ) )
                mkdir( $real . $dir, 0700, true );
            $name = basename( $copy );
            // start from an empty file of the same kind each run (overwritten, never deleted)
            file_put_contents( $real . "$dir/$name", strncmp( ltrim( $e->originalContent() ), '<?php', 5 ) === 0 ? "<?php /* #?ini charset=\"utf-8\"?\n\n*/ ?>" : '' );
            chmod( $real . "$dir/$name", 0600 );
            $target = $this->editorFor( "$dir/$name" );
            foreach ( $e->blocks() as $block )
            {
                if ( !expIniEditorTestBlockOk( $block ) )
                    continue;
                try
                {
                    $target->insertBlockLines( $block, $e->blockLines( $block ) );
                }
                catch ( expIniException $x )
                {
                    $this->fail( "$rel [$block]: " . $x->getMessage() );
                }
                ++$blocks;
            }
            $target->save( array( 'backup' => false ) );
            // settings above the first block (block '') have no block to be moved with
            $want = $this->eZINIValues( $copy );
            unset( $want[''] );
            $want = array_filter( $want, function ( $k ) { return expIniEditorTestBlockOk( (string)$k ); }, ARRAY_FILTER_USE_KEY );
            $this->assertEquals( $want, $this->eZINIValues( "$dir/$name" ), "$rel: values after re-inserting every block" );

            // insert the last block again under a new name and remove it: byte-identical
            $all = $e->blocks();
            if ( $all )
            {
                $bytes = $e->originalContent();
                $e->insertBlockLines( 'ExpIniMovedBlock', (array)$e->blockLines( end( $all ) ) );
                $e->save( array( 'backup' => false ) );
                $e2 = $this->editorFor( $copy );
                $e2->removeBlock( 'ExpIniMovedBlock' );
                $e2->save( array( 'backup' => false ) );
                $this->assertSame( $bytes, file_get_contents( $real . $copy ), "$rel: byte-identical after inserting and removing a block" );
            }
        }
        $this->assertGreaterThan( 1000, $blocks );
        fwrite( STDERR, "\n[round-trip] $blocks blocks re-inserted\n" );
    }

    /** RT-04 */
    public function testNewBlockKeepsTheRest()
    {
        $real = expIniEngineTestFixtures::realRoot();
        foreach ( self::$copies as $rel => $copy )
        {
            $before = $this->eZINIValues( $copy );
            $e = $this->editorFor( $copy );
            $e->add( 'ExpIniRoundTripBlock', 'Values', 'a' );
            $e->set( 'ExpIniRoundTripBlock', 'Plain', 'b' );
            $e->save( array( 'backup' => false ) );
            $after = $this->eZINIValues( $copy );
            $this->assertSame( array( 'Values' => array( 'a' ), 'Plain' => 'b' ), $after['ExpIniRoundTripBlock'], $rel );
            unset( $after['ExpIniRoundTripBlock'] );
            $this->assertEquals( $before, $after, "$rel: other values unchanged" );
            $content = file_get_contents( $real . $copy );
            if ( strncmp( ltrim( $content ), '<?php', 5 ) === 0 && preg_match( '#\*/\s*\?>\s*$#', file_get_contents( $real . $rel ) ) )
                $this->assertMatchesRegularExpression( '#\[ExpIniRoundTripBlock\]\r?\nValues\[\]=a\r?\nPlain=b\r?\n(\s*\r?\n)*\s*(\*\s*)?\*/\s*\?>\s*$#', $content, "$rel: block before the PHP close" );
            // restore the copy for other tests
            copy( $real . $rel, $real . $copy );
        }
    }
}

/** Blocks the editor may address (eZINI accepts a few names the editor refuses, e.g. with '##'). */
function expIniEditorTestBlockOk( $block )
{
    return trim( $block ) === $block && strpbrk( $block, "]\r\n" ) === false && strpos( $block, '##' ) === false;
}
