<?php
/**
 * Tests of eZPackageComparisonDiff, the word level difference the package comparison viewer shows: the result
 * always gives both texts back (equal + delete runs are the old text, equal + insert runs the new one), the
 * token edit script is a shortest one (checked against a longest common subsequence for many generated pairs),
 * multi-line texts are matched line by line first, replaced phrases come out as one removed and one added block,
 * and differences too big to trace fall back to lines and then to whole texts.
 *
 * Generated inputs use a fixed seed, so every run sees the same pairs.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageComparisonDiffTest extends PHPUnit\Framework\TestCase
{
    private static function side( array $runs, $skip )
    {
        $text = '';
        foreach ( $runs as $run )
        {
            if ( $run['op'] !== $skip )
                $text .= $run['text'];
        }
        return $text;
    }

    private function assertGivesBothBack( $old, $new, array $runs )
    {
        $this->assertSame( (string)$old, self::side( $runs, 'insert' ), 'the old text' );
        $this->assertSame( (string)$new, self::side( $runs, 'delete' ), 'the new text' );
        for ( $i = 1; $i < count( $runs ); ++$i )
        {
            $this->assertFalse( $runs[$i]['op'] === $runs[$i - 1]['op'], 'neighbouring runs of one kind are merged' );
        }
        foreach ( $runs as $run )
            $this->assertNotSame( '', $run['text'] );
    }

    private static function lcsLength( array $a, array $b )
    {
        $prev = array_fill( 0, count( $b ) + 1, 0 );
        foreach ( $a as $x )
        {
            $row = array( 0 );
            foreach ( $b as $j => $y )
                $row[] = $x === $y ? $prev[$j] + 1 : max( $prev[$j + 1], $row[$j] );
            $prev = $row;
        }
        return $prev[count( $b )];
    }

    public function testEqualTexts()
    {
        $this->assertSame( array(), eZPackageComparisonDiff::words( '', '' ) );
        $this->assertSame( array( array( 'op' => 'equal', 'text' => 'same text' ) ), eZPackageComparisonDiff::words( 'same text', 'same text' ) );
        $this->assertSame( array( array( 'op' => 'equal', 'text' => '12' ) ), eZPackageComparisonDiff::words( 12, '12' ) );
    }

    public function testOneSideEmpty()
    {
        $this->assertSame( array( array( 'op' => 'insert', 'text' => 'new words' ) ), eZPackageComparisonDiff::words( '', 'new words' ) );
        $this->assertSame( array( array( 'op' => 'delete', 'text' => 'old words' ) ), eZPackageComparisonDiff::words( 'old words', null ) );
    }

    public function testOneWordChanged()
    {
        $this->assertSame( array( array( 'op' => 'equal', 'text' => 'The ' ),
                                  array( 'op' => 'delete', 'text' => 'quick' ),
                                  array( 'op' => 'insert', 'text' => 'slow' ),
                                  array( 'op' => 'equal', 'text' => ' brown fox.' ) ),
                           eZPackageComparisonDiff::words( 'The quick brown fox.', 'The slow brown fox.' ) );
    }

    public function testAReplacedPhraseIsOneBlockEachWay()
    {
        $runs = eZPackageComparisonDiff::words( 'Keep this one two three end', 'Keep this four five six end' );
        $this->assertSame( array( array( 'op' => 'equal', 'text' => 'Keep this ' ),
                                  array( 'op' => 'delete', 'text' => 'one two three' ),
                                  array( 'op' => 'insert', 'text' => 'four five six' ),
                                  array( 'op' => 'equal', 'text' => ' end' ) ), $runs );
    }

    public function testLinesOnlyOneSideHasAreShownWhole()
    {
        $old = "first line\nsecond line\nthird line\n";
        $new = "first line\nthird line\nadded line\n";
        $runs = eZPackageComparisonDiff::words( $old, $new );
        $this->assertGivesBothBack( $old, $new, $runs );
        $this->assertContains( array( 'op' => 'delete', 'text' => "second line\n" ), $runs );
        $this->assertContains( array( 'op' => 'insert', 'text' => "added line\n" ), $runs );
    }

    public function testChangedLinePairIsComparedWordByWord()
    {
        $runs = eZPackageComparisonDiff::words( "a\nthe red car\nz\n", "a\nthe blue car\nz\n" );
        $this->assertSame( array( array( 'op' => 'equal', 'text' => "a\nthe " ),
                                  array( 'op' => 'delete', 'text' => 'red' ),
                                  array( 'op' => 'insert', 'text' => 'blue' ),
                                  array( 'op' => 'equal', 'text' => " car\nz\n" ) ), $runs );
    }

    public function testUnicodeWordsStayWhole()
    {
        $runs = eZPackageComparisonDiff::words( 'Größe ändern', 'Größe übernehmen' );
        $this->assertContains( array( 'op' => 'delete', 'text' => 'ändern' ), $runs );
        $this->assertContains( array( 'op' => 'insert', 'text' => 'übernehmen' ), $runs );
    }

    public function testInvalidUtf8IsStillDiffed()
    {
        $old = "caf\xE9 au lait";
        $new = "caf\xE9 noir";
        $this->assertGivesBothBack( $old, $new, eZPackageComparisonDiff::words( $old, $new ) );
    }

    public function testTokensAndLines()
    {
        $this->assertSame( array( 'Hello', ',', ' ', 'wörld_1', '  ', '!' ), eZPackageComparisonDiff::tokens( 'Hello, wörld_1  !' ) );
        $this->assertSame( array(), eZPackageComparisonDiff::tokens( '' ) );
        $this->assertSame( array( "a\n", "\n", 'b' ), eZPackageComparisonDiff::lines( "a\n\nb" ) );
        $this->assertSame( array(), eZPackageComparisonDiff::lines( '' ) );
    }

    public function testSides()
    {
        $sides = eZPackageComparisonDiff::sides( 'a b c', 'a x c' );
        $this->assertTrue( $sides['changed'] );
        $this->assertSame( 'a b c', self::side( $sides['old'], 'insert' ) );
        $this->assertSame( 'a x c', self::side( $sides['new'], 'delete' ) );
        $this->assertSame( array( 'equal', 'delete', 'equal' ), array_column( $sides['old'], 'op' ) );
        $this->assertSame( array( 'equal', 'insert', 'equal' ), array_column( $sides['new'], 'op' ) );
        $this->assertFalse( eZPackageComparisonDiff::sides( 'x', 'x' )['changed'] );
    }

    public function testMergeJoinsAndOrdersRuns()
    {
        $ops = array( array( 'op' => 'equal', 'text' => 'a' ), array( 'op' => 'equal', 'text' => 'b' ),
                      array( 'op' => 'insert', 'text' => 'X' ), array( 'op' => 'delete', 'text' => 'c' ),
                      array( 'op' => 'equal', 'text' => ' ' ),
                      array( 'op' => 'delete', 'text' => 'd' ), array( 'op' => 'insert', 'text' => 'Y' ),
                      array( 'op' => 'equal', 'text' => 'e' ) );
        $this->assertSame( array( array( 'op' => 'equal', 'text' => 'ab' ),
                                  array( 'op' => 'delete', 'text' => 'c d' ),
                                  array( 'op' => 'insert', 'text' => 'X Y' ),
                                  array( 'op' => 'equal', 'text' => 'e' ) ), eZPackageComparisonDiff::merge( $ops ) );
    }

    public function testMyersEdgeCases()
    {
        $this->assertSame( array(), eZPackageComparisonDiff::myers( array(), array() ) );
        $this->assertSame( array( array( 'op' => 'insert', 'text' => 'x' ) ), eZPackageComparisonDiff::myers( array(), array( 'x' ) ) );
        $this->assertSame( array( array( 'op' => 'delete', 'text' => 'x' ) ), eZPackageComparisonDiff::myers( array( 'x' ), array() ) );
    }

    public static function generatedPairProvider()
    {
        mt_srand( 20261005 );
        $alphabet = array( 'a', 'b', 'c', 'd', ' ', 'e' );
        $pairs = array();
        for ( $i = 0; $i < 60; ++$i )
        {
            $a = array();
            $b = array();
            for ( $j = mt_rand( 0, 14 ); $j > 0; --$j )
                $a[] = $alphabet[mt_rand( 0, count( $alphabet ) - 1 )];
            for ( $j = mt_rand( 0, 14 ); $j > 0; --$j )
                $b[] = $alphabet[mt_rand( 0, count( $alphabet ) - 1 )];
            $pairs["pair $i"] = array( $a, $b );
        }
        mt_srand();
        return $pairs;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('generatedPairProvider')]
    public function testTokenDiffIsAShortestEditScript( array $a, array $b )
    {
        $ops = eZPackageComparisonDiff::tokenDiff( $a, $b );
        $this->assertSame( implode( '', $a ), self::side( $ops, 'insert' ) );
        $this->assertSame( implode( '', $b ), self::side( $ops, 'delete' ) );
        $equal = count( array_filter( $ops, function ( $op ) { return $op['op'] === 'equal'; } ) );
        $this->assertSame( self::lcsLength( $a, $b ), $equal, 'as many tokens kept as the longest common subsequence has' );
        $this->assertGivesBothBack( implode( '', $a ), implode( '', $b ), eZPackageComparisonDiff::words( implode( '', $a ), implode( '', $b ) ) );
    }

    public function testTooManyEditsGiveUp()
    {
        $a = array();
        $b = array();
        for ( $i = 0; $i < eZPackageComparisonDiff::MAX_EDITS; ++$i )
        {
            $a[] = "a$i";
            $b[] = "b$i";
        }
        $this->assertNull( eZPackageComparisonDiff::tokenDiff( $a, $b ) );
        $old = implode( ' ', $a );
        $new = implode( ' ', $b );
        $this->assertSame( array( array( 'op' => 'delete', 'text' => $old ), array( 'op' => 'insert', 'text' => $new ) ),
                           eZPackageComparisonDiff::words( $old, $new ), 'one line each, too different: whole texts' );
    }

    public function testTooDifferentWordsFallBackToLines()
    {
        $oldLines = array();
        $newLines = array();
        for ( $i = 0; $i < 300; ++$i )
        {
            $oldLines[] = "old $i x y z";
            $newLines[] = $i % 2 ? "old $i x y z" : "new $i p q r";
        }
        $old = implode( "\n", $oldLines );
        $new = implode( "\n", $newLines );
        $this->assertGivesBothBack( $old, $new, eZPackageComparisonDiff::words( $old, $new ) );
    }
}
