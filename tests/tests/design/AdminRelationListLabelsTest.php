<?php
/**
 * The relation list's edit template gives every control of a relation row an accessible name, in the classic design
 * (design/standard, which the admin design draws) and in admin4 (which admin4l draws too), checked on the files alone
 * (no database):
 *
 * - the remove checkbox has an aria-label (the related object's name, else "Remove selected"), including the empty row
 *   the browse script copies when a relation is added;
 * - the order field and the translation base select are labelled by their column heading (aria-labelledby), and the
 *   heading they point to carries that id in the same template;
 * - the strings the labels use are translated in eng-US and ger-DE.
 *
 * Why: axe reported six controls without a label (serious) on the classic edit form of an article with three
 * relation lists: the checkbox and the order field of each list's empty row.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminRelationListLabelsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminRelationListLabelsTest extends PHPUnit\Framework\TestCase
{
    const TEMPLATES = array(
        'classic' => 'design/standard/templates/content/datatype/edit/ezobjectrelationlist.tpl',
        'admin4' => 'design/admin4/templates/content/datatype/edit/ezobjectrelationlist.tpl',
    );

    const CONTEXT = 'design/standard/content/datatype';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function source( $relative )
    {
        $path = self::root() . '/' . $relative;
        self::assertFileExists( $path );
        return file_get_contents( $path );
    }

    /** The relation row controls: the remove checkbox, the order field, the translation base select */
    private static function rowControls( $source )
    {
        preg_match_all( '/<(?:input|select)\b[^>]*>/s', $source, $m );
        $controls = array();
        foreach ( $m[0] as $tag )
        {
            if ( preg_match( '/type="hidden"/', $tag ) )
                continue;
            if ( preg_match( '/name="\{\$attribute_base\}_(selection|priority|translation_source)_?[\[{]/', $tag ) )
                $controls[] = $tag;
        }
        return $controls;
    }

    public static function templates()
    {
        $out = array();
        foreach ( self::TEMPLATES as $design => $path )
            $out[$design] = array( $path );
        return $out;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'templates' )]
    public function testEveryRowControlHasAnAccessibleName( $path )
    {
        $controls = self::rowControls( self::source( $path ) );
        // a checkbox and an order field in each of the four row kinds, the empty row included, plus the translation base
        $this->assertGreaterThanOrEqual( 9, count( $controls ), $path );
        foreach ( $controls as $tag )
            $this->assertMatchesRegularExpression( '/\saria-label(ledby)?="[^"]+"/', $tag, $path . ': a relation row control without a label: ' . $tag );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'templates' )]
    public function testTheEmptyRowIsLabelled( $path )
    {
        $source = self::source( $path );
        $this->assertMatchesRegularExpression( '/<input type="checkbox"[^>]*value="--id--"[^>]*aria-label="\{\'Remove selected\'\|i18n\( \'' . preg_quote( self::CONTEXT, '/' ) . '\' \)\|wash\}"/', $source, $path );
        $this->assertMatchesRegularExpression( '/<input[^>]*aria-labelledby="ezcoa-\{\$attribute\.id\}_order_head"[^>]*value="0" \/>/', $source, $path );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'templates' )]
    public function testEveryLabelledbyPointsToAHeadingOfTheTemplate( $path )
    {
        $source = self::source( $path );
        preg_match_all( '/aria-labelledby="([^"]+)"/', $source, $m );
        $this->assertNotEmpty( $m[1], $path );
        foreach ( array_unique( $m[1] ) as $id )
        {
            if ( strpos( $id, '_head' ) === false )
                continue;
            $this->assertMatchesRegularExpression( '/<th\b[^>]*\sid="' . preg_quote( $id, '/' ) . '"/', $source, $path . ': no heading carries ' . $id );
        }
        // the order heading in both the advanced and the simple table
        $this->assertSame( 2, substr_count( $source, 'id="ezcoa-{$attribute.id}_order_head"' ), $path );
    }

    public function testTheLabelStringsAreTranslated()
    {
        foreach ( array( 'eng-US', 'ger-DE' ) as $locale )
        {
            $ts = self::source( 'share/translations/' . $locale . '/translation.ts' );
            $this->assertSame( 1, preg_match( '#<name>' . preg_quote( self::CONTEXT, '#' ) . '</name>(.*?)</context>#s', $ts, $context ), $locale );
            foreach ( array( 'Remove selected', 'Order' ) as $string )
                $this->assertMatchesRegularExpression( '#<source>' . preg_quote( $string, '#' ) . '</source>\s*<translation>[^<]+</translation>#', $context[1], $locale . ': ' . $string );
        }
    }
}
