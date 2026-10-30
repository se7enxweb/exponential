<?php
/**
 * The content edit form of the standard design (the public edit form of every design without its own: starter and
 * the other public siteaccesses) gives every control an accessible name, so axe finds no missing label or alt text
 * (WCAG 2.2 AA 1.1.1 and 4.1.2), checked on the files alone; no database.
 *
 * - design/standard/templates/content/edit_placement.tpl: the sort field select, the ascending and descending radios
 *   and the main location radio have an aria-label, the move and remove image buttons an alt text.
 * - design/standard/templates/content/edit_right_menu.tpl: the translation radios, the related object checkboxes and
 *   the class select have an aria-label, the find and remove image buttons an alt text.
 * - design/standard/templates/content/edit_attribute.tpl: an attribute's label points to its field (for="ezcoa-...",
 *   the id the datatype edit templates give it), unless the datatype groups several inputs.
 * - Every name is translated through i18n( 'design/standard/content/edit' ) (or is the language's own name).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/StandardContentEditAccessibleNamesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class StandardContentEditAccessibleNamesTest extends PHPUnit\Framework\TestCase
{
    const DIR = 'design/standard/templates/content/';

    private static function read( $file )
    {
        $source = (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . self::DIR . $file );
        return preg_replace( '/\{\*.*?\*\}/s', '', $source );
    }

    /**
     * @return string[] every tag of $source that matches $pattern
     */
    private static function tags( $source, $pattern )
    {
        preg_match_all( '/<(?:input|select)\b(?:[^<>{}]|\{[^}]*\})*>/', $source, $m );
        return array_values( array_filter( $m[0], function ( $tag ) use ( $pattern ) { return preg_match( $pattern, $tag ); } ) );
    }

    private function assertNamed( array $tags, $what, $min = 1 )
    {
        $this->assertGreaterThanOrEqual( $min, count( $tags ), "no $what found" );
        foreach ( $tags as $tag )
        {
            if ( preg_match( '/type="image"/', $tag ) )
                $this->assertMatchesRegularExpression( '/\salt="\{[^}]*\|i18n\(\s*["\']design\/standard\/content\/edit["\']\s*\)\}"/', $tag, "$what without a translated alt text: $tag" );
            else
                $this->assertMatchesRegularExpression( '/\saria-label="\{[^}]*(\|i18n\(\s*["\']design\/standard\/content\/edit["\']\s*\)|\.name\|wash)\}/', $tag, "$what without a translated aria-label: $tag" );
        }
    }

    public function testPlacementControlsAreNamed()
    {
        $source = self::read( 'edit_placement.tpl' );
        $this->assertNamed( self::tags( $source, '/name="SortFieldMap\[/' ), 'the sort field select' );
        $this->assertNamed( self::tags( $source, '/name="SortOrderMap\[/' ), 'the sort order radios', 2 );
        $this->assertNamed( self::tags( $source, '/type="radio" name="MainNodeID"/' ), 'the main location radio' );
        $this->assertNamed( self::tags( $source, '/MoveNodeID_|RemoveNodeID_/' ), 'the move and remove buttons', 3 );
    }

    public function testRightMenuControlsAreNamed()
    {
        $source = self::read( 'edit_right_menu.tpl' );
        $this->assertNamed( self::tags( $source, '/name="FromLanguage"/' ), 'the translation radios', 2 );
        $this->assertNamed( self::tags( $source, '/name="DeleteRelationIDArray\[\]"/' ), 'the related object checkbox' );
        $this->assertNamed( self::tags( $source, '/name="BrowseObjectButton"|name="DeleteRelationButton"/' ), 'the find and remove buttons', 2 );
        $this->assertNamed( self::tags( $source, '/name="ClassID"/' ), 'the class select' );
    }

    public function testImageButtonsHaveAltText()
    {
        foreach ( array( 'edit_placement.tpl', 'edit_right_menu.tpl' ) as $file )
            foreach ( self::tags( self::read( $file ), '/type="image"/' ) as $tag )
                $this->assertMatchesRegularExpression( '/\salt="/', $tag, "$file: an image button without alt text: $tag" );
    }

    public function testAttributeLabelPointsToItsField()
    {
        $source = self::read( 'edit_attribute.tpl' );
        $this->assertMatchesRegularExpression(
            '/<label\{if \$ContentObjectAttribute:item\.has_validation_error\} class="validation-error"\{\/if\}'
            . '\{if \$ContentObjectAttribute:item\.display_info\.edit\.grouped_input\|not\} for="ezcoa-\{if ne\( \$attribute_base, \'ContentObjectAttribute\' \)\}\{\$attribute_base\}-\{\/if\}'
            . '\{\$ContentObjectAttribute:item\.contentclassattribute_id\}_\{\$ContentObjectAttribute:item\.contentclass_attribute_identifier\}"\{\/if\}>/',
            $source,
            'the label of an editable attribute does not point to the field id the datatype edit templates use'
        );
        // the id the label points to is the one the string and text datatypes give their field
        foreach ( array( 'datatype/edit/ezstring.tpl', 'datatype/edit/eztext.tpl' ) as $file )
            $this->assertStringContainsString( 'id="ezcoa-{if ne( $attribute_base, \'ContentObjectAttribute\' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}"', self::read( $file ), $file );
    }
}
