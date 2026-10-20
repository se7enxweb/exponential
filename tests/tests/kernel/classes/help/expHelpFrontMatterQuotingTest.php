<?php
/**
 * File containing the expHelpFrontMatterQuotingTest class.
 *
 * Quoted front matter values are unescaped as YAML does, so a title such as
 * "Objection: \"Is it still alive?\"" shows its quotes and no backslashes in the heading, the navigation and search.
 *
 *  FQ-01  Double quotes: \" \\ \/ \t \n and \uXXXX; an unknown escape is kept as written
 *  FQ-02  Single quotes: '' is one quote, a backslash is literal
 *  FQ-03  Plain values and list values are unchanged
 *  FQ-04  No shipped page's title or summary keeps a backslash before a quote
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class expHelpFrontMatterQuotingTest extends PHPUnit\Framework\TestCase
{
    private static function meta( $lines )
    {
        list( $meta ) = expHelpFrontMatter::split( "---\n" . $lines . "\n---\n# Body\n" );
        return $meta;
    }

    // ------------------------------------------------------------------ FQ-01

    public function testDoubleQuotedValuesAreUnescaped()
    {
        $meta = self::meta( 'title: "Objection: \"Is it still alive?\""' );
        $this->assertSame( 'Objection: "Is it still alive?"', $meta['title'] );
        $meta = self::meta( 'title: "a \\\\ b \/ c"' );
        $this->assertSame( 'a \\ b / c', $meta['title'] );
        $meta = self::meta( 'title: "caf\u00e9"' );
        $this->assertSame( "caf\u{00e9}", $meta['title'] );
        $meta = self::meta( 'title: "C:\dir"' );
        $this->assertSame( 'C:\dir', $meta['title'], 'an unknown escape is kept' );
    }

    // ------------------------------------------------------------------ FQ-02

    public function testSingleQuotedValuesKeepBackslashes()
    {
        $meta = self::meta( "title: 'It''s \"fine\" \\n'" );
        $this->assertSame( 'It\'s "fine" \\n', $meta['title'] );
    }

    // ------------------------------------------------------------------ FQ-03

    public function testPlainAndListValuesAreUnchanged()
    {
        $meta = self::meta( "title: Plain title\nkeywords: a, \"b\", 'c'" );
        $this->assertSame( 'Plain title', $meta['title'] );
        $this->assertSame( array( 'a', 'b', 'c' ), expHelpFrontMatter::listValue( 'a, "b", \'c\'' ) );
    }

    // ------------------------------------------------------------------ FQ-04

    public function testNoShippedTitleShowsAnEscapeBackslash()
    {
        $root = dirname( __DIR__, 5 );
        $files = glob( $root . '/doc/eng-us/help/advocates/*.md' );
        $this->assertNotEmpty( $files );
        $checked = 0;
        foreach ( $files as $file )
        {
            list( $meta ) = expHelpFrontMatter::split( file_get_contents( $file ) );
            foreach ( array( 'title', 'summary' ) as $key )
            {
                if ( !isset( $meta[$key] ) || !is_string( $meta[$key] ) )
                    continue;
                $this->assertDoesNotMatchRegularExpression( '/\\\\["\']/', $meta[$key], basename( $file ) . " $key" );
                $checked++;
            }
        }
        $this->assertGreaterThan( 0, $checked );
    }
}
