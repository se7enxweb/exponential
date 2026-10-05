<?php
/**
 * The text diff of lib/ezdiff (used by the version comparison of the content edit view):
 *   - eZDiff: engine types by name, the engine instance per type, diff() without an engine
 *   - eZDiffTextEngine::createDifferenceObject(): paragraphs (split on CRLF) unchanged, added and removed, words
 *     within a changed paragraph, and for every case and for seeded random edits: the unchanged and removed parts
 *     give the old text and the unchanged and added parts give the new text, word for word and in order
 *   - eZDiffContainerObjectEngine, eZDiffContent attributes, eZDiffMatrix (sparse storage, zero not stored)
 *
 * Plain PHP, no kernel bootstrap, no database. eZDiffXMLTextEngine needs content objects and is not covered here.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdiff
 */

class eZDiffTextEngineTest extends PHPUnit\Framework\TestCase
{
    private function changes( $old, $new )
    {
        $engine = new eZDiffTextEngine();
        $diff = $engine->createDifferenceObject( $old, $new );
        $this->assertInstanceOf( eZTextDiff::class, $diff );
        return $diff->getChanges();
    }

    private static function words( $text )
    {
        return preg_split( '/\s+/', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
    }

    /**
     * Asserts that the change list rebuilds both texts, word for word.
     */
    private function assertRebuildsBothTexts( $old, $new, $changes )
    {
        $oldWords = array();
        $newWords = array();
        foreach ( $changes as $change )
        {
            $this->assertArrayHasKey( 'status', $change );
            switch ( $change['status'] )
            {
                case 0:
                    $oldWords = array_merge( $oldWords, self::words( $change['unchanged'] ) );
                    $newWords = array_merge( $newWords, self::words( $change['unchanged'] ) );
                    break;
                case 1:
                    $oldWords = array_merge( $oldWords, self::words( $change['removed'] ) );
                    break;
                case 2:
                    $newWords = array_merge( $newWords, self::words( $change['added'] ) );
                    break;
                default:
                    $this->fail( 'unknown status ' . var_export( $change['status'], true ) );
            }
        }
        $this->assertSame( self::words( $old ), $oldWords, 'old text' );
        $this->assertSame( self::words( $new ), $newWords, 'new text' );
    }

    private static function statuses( $changes )
    {
        return array_map( function ( $c ) { return $c['status']; }, $changes );
    }

    public static function pairProvider()
    {
        return array(
            'same text'             => array( 'one two three', 'one two three' ),
            'word replaced'         => array( 'one two three', 'one 2 three' ),
            'word added at the end' => array( 'one two', 'one two three' ),
            'word removed'          => array( 'one two three', 'one three' ),
            'paragraph added'       => array( "a\r\nb", "a\r\nb\r\nc" ),
            'paragraph removed'     => array( "a\r\nb\r\nc", "a\r\nc" ),
            'paragraph changed'     => array( "first\r\nthe quick brown fox", "first\r\nthe slow brown dog jumps" ),
            'everything new'        => array( 'alpha beta', 'gamma delta' ),
            'from empty'            => array( '', 'new words' ),
            'to empty'              => array( 'old words', '' ),
            'repeated words'        => array( 'a a a b a', 'a b a a' ),
            'multi-byte'            => array( 'grüße aus köln', 'grüße aus münchen' ),
            'extra spaces'          => array( 'a    b  c', 'a b c' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('pairProvider')]
    public function testChangesRebuildBothTexts( $old, $new )
    {
        $this->assertRebuildsBothTexts( $old, $new, $this->changes( $old, $new ) );
    }

    public function testUnchangedTextIsOneUnchangedPart()
    {
        $changes = $this->changes( 'one two three', 'one two three' );
        $this->assertSame( array( 0 ), self::statuses( $changes ) );
        $this->assertSame( 'one two three', trim( $changes[0]['unchanged'] ) );
    }

    public function testAddedParagraph()
    {
        $changes = $this->changes( "a\r\nb", "a\r\nb\r\nc" );
        $this->assertSame( array( 0, 2 ), self::statuses( $changes ) );
        $this->assertSame( 'c', trim( $changes[1]['added'] ) );
    }

    public function testRemovedParagraph()
    {
        $changes = $this->changes( "a\r\nb\r\nc", "a\r\nc" );
        $this->assertSame( array( 0, 1, 0 ), self::statuses( $changes ) );
        $this->assertSame( 'b', trim( $changes[1]['removed'] ) );
    }

    public function testReplacedWordIsAddedAndRemoved()
    {
        $changes = $this->changes( 'one two three', 'one 2 three' );
        $this->assertContains( 1, self::statuses( $changes ) );
        $this->assertContains( 2, self::statuses( $changes ) );
        foreach ( $changes as $change )
        {
            if ( $change['status'] === 1 )
                $this->assertSame( 'two', trim( $change['removed'] ) );
            if ( $change['status'] === 2 )
                $this->assertSame( '2', trim( $change['added'] ) );
        }
    }

    /**
     * Seeded random edits of a sentence: words replaced, inserted and deleted.
     */
    public function testRandomEditsRebuildBothTexts()
    {
        $vocabulary = array( 'red', 'green', 'blue', 'cat', 'dog', 'runs', 'sleeps', 'the', 'a', 'big' );
        mt_srand( 20261005 );
        for ( $round = 0; $round < 60; ++$round )
        {
            $old = array();
            for ( $i = mt_rand( 1, 12 ); $i > 0; --$i )
                $old[] = $vocabulary[mt_rand( 0, 9 )];
            $new = $old;
            for ( $edits = mt_rand( 1, 4 ); $edits > 0; --$edits )
            {
                $at = mt_rand( 0, count( $new ) );
                switch ( mt_rand( 0, 2 ) )
                {
                    case 0: array_splice( $new, $at, 0, array( $vocabulary[mt_rand( 0, 9 )] ) ); break;
                    case 1: if ( count( $new ) > 1 ) array_splice( $new, min( $at, count( $new ) - 1 ), 1 ); break;
                    case 2: $new[min( $at, count( $new ) - 1 )] = $vocabulary[mt_rand( 0, 9 )]; break;
                }
            }
            $oldText = implode( ' ', $old );
            $newText = implode( ' ', $new );
            $this->assertRebuildsBothTexts( $oldText, $newText, $this->changes( $oldText, $newText ) );
        }
        mt_srand();
    }

    public function testDiffEngineSelection()
    {
        $diff = new eZDiff();
        $this->assertNull( $diff->getDiffEngineType() );
        $this->assertNull( $diff->diff( 'a', 'b' ), 'no engine, no diff' );

        $this->assertSame( 0, $diff->engineType( 'text' ) );
        $this->assertSame( 1, $diff->engineType( 'xml' ) );
        $this->assertSame( 2, $diff->engineType( 'container' ) );
        $this->assertNull( $diff->engineType( 'nothing' ) );

        $diff->setDiffEngineType( $diff->engineType( 'text' ) );
        $this->assertSame( 0, $diff->getDiffEngineType() );
        $diff->initDiffEngine();
        $this->assertInstanceOf( eZDiffTextEngine::class, $diff->DiffEngineInstance );
        $result = $diff->diff( 'a b', 'a c' );
        $this->assertInstanceOf( eZTextDiff::class, $result );

        $container = new eZDiff( 2 );
        $container->initDiffEngine();
        $this->assertInstanceOf( eZDiffContainerObjectEngine::class, $container->DiffEngineInstance );
        $xml = new eZDiff( 1 );
        $xml->initDiffEngine();
        $this->assertInstanceOf( eZDiffXMLTextEngine::class, $xml->DiffEngineInstance );
    }

    public function testContainerEngineKeepsBothContents()
    {
        $diff = new eZDiff( 2 );
        $diff->initDiffEngine();
        $result = $diff->diff( array( 'old' ), array( 'new' ) );
        $this->assertInstanceOf( eZDiffContainerObject::class, $result );
        $this->assertSame( array( 'old' ), $result->attribute( 'old_content' ) );
        $this->assertSame( array( 'new' ), $result->attribute( 'new_content' ) );
        $this->assertNull( $result->attribute( 'changes' ) );
        $this->assertTrue( $result->hasAttribute( 'changes' ) );
        $this->assertFalse( $result->hasAttribute( 'nothing' ) );
        $this->assertSame( array( 'changes', 'old_content', 'new_content' ), $result->attributes() );
    }

    public function testMatrixStoresOnlyValuesThatAreNotZero()
    {
        $m = new eZDiffMatrix( 3, 4 );
        $this->assertSame( 3, $m->Rows );
        $this->assertSame( 4, $m->Cols );
        $m->set( 2, 3, 7 );
        $m->set( 0, 1, 0 );
        $m->set( 1, 0, 5 );
        $this->assertSame( 7, $m->get( 2, 3 ) );
        $this->assertSame( 5, $m->get( 1, 0 ) );
        $this->assertSame( 0, $m->get( 0, 1 ) );
        $this->assertSame( 0, $m->get( 0, 0 ) );
        $this->assertCount( 2, $m->Matrix );
        $m->setSize( 10, 10 );
        $this->assertSame( 10, $m->Cols );
        $this->assertNull( ( new eZDiffMatrix( 'x', null ) )->Rows );
    }
}
