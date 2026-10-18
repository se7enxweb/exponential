<?php
/**
 * The edit templates of the datatypes in the standard design carry no style="" attribute and no <style> block. The
 * admin design draws them (it has no edit templates of its own for most datatypes), and a Content-Security-Policy
 * without 'unsafe-inline' for style-src drops inline styles. Checked on the files alone (no database).
 *
 * The last one was the relation list's "Create new object" field
 * (design/standard/templates/content/datatype/edit/ezobjectrelationlist.tpl), hidden with style="display:none" until
 * its button showed it; it uses the hidden attribute now.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/StandardDatatypeEditNoStyleAttributeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class StandardDatatypeEditNoStyleAttributeTest extends PHPUnit\Framework\TestCase
{
    const DIRECTORY = 'design/standard/templates/content/datatype/edit';

    const RELATION_LIST = 'design/standard/templates/content/datatype/edit/ezobjectrelationlist.tpl';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** the template's source without its {* comments *} */
    private static function source( $path )
    {
        return preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( $path ) );
    }

    public function testNoInlineStyleInTheStandardDatatypeEditTemplates()
    {
        $files = glob( self::root() . '/' . self::DIRECTORY . '/*.tpl' );
        $this->assertNotEmpty( $files, 'the standard design has datatype edit templates' );
        $inline = array();
        foreach ( $files as $file )
        {
            $source = self::source( $file );
            if ( preg_match( '/<style\b/i', $source ) || preg_match( '/(?<![\w.\-])style\s*=\s*["\'{]/i', $source ) )
                $inline[] = basename( $file );
        }
        $this->assertSame( array(), $inline, 'datatype edit templates with inline CSS' );
    }

    public function testTheRelationListCreateFieldIsHiddenByAttributeAndLabelled()
    {
        $source = self::source( self::root() . '/' . self::RELATION_LIST );
        $this->assertMatchesRegularExpression( "/<div id='create_new_object_\\{\\\$attribute\\.id\\}' hidden>/", $source );
        $this->assertStringContainsString( 'divfield.hidden=false', $source );
        $this->assertStringContainsString( '<label for="attribute_{$attribute.id}_new_object_name">', $source );
        $this->assertStringNotContainsString( '.style.display', $source );
    }
}
