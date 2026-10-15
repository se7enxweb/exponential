<?php
/**
 * The language choice of "Create new" (content/action, design:content/create_languages.tpl) has a page title: its
 * result carries a path (Content / Language selection), so the window title is no longer "- Exponential Admin", and
 * the navigation part of the place the object is created in. Checked on the view's source (no database).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminCreateLanguagesTitleTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminCreateLanguagesTitleTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** The code of content/action from the fetch of create_languages.tpl to the view's return */
    private static function languageChoice()
    {
        $code = (string)file_get_contents( self::root() . '/kernel/private/classes/views/content/action.php' );
        $start = strpos( $code, "\$tpl->fetch( 'design:content/create_languages.tpl' )" );
        self::assertNotFalse( $start, 'content/action fetches create_languages.tpl' );
        $end = strpos( $code, 'return $this->viewResult(', $start );
        return substr( $code, $start, $end - $start );
    }

    public function testTheLanguageChoiceHasAPath()
    {
        $block = self::languageChoice();
        $this->assertStringContainsString( "\$Result['path'] = array(", $block );
        $this->assertStringContainsString( "\\ezpI18n::tr( 'kernel/content', 'Content' )", $block );
        $this->assertStringContainsString( "\\ezpI18n::tr( 'design/admin/content/create_languages', 'Language selection' )", $block );
        $this->assertStringContainsString( "\$Result['navigation_part'] = \$section->attribute( 'navigation_part_identifier' );", $block );
    }

    public function testTheTitleIsTranslated()
    {
        $dom = new DOMDocument();
        $this->assertTrue( $dom->load( self::root() . '/share/translations/ger-DE/translation.ts' ) );
        $xpath = new DOMXPath( $dom );
        $this->assertSame( 'Sprachauswahl', $xpath->evaluate( "string(//context[name='design/admin/content/create_languages']/message[source='Language selection']/translation)" ) );
        $this->assertSame( 'Inhalt', $xpath->evaluate( "string(//context[name='kernel/content']/message[source='Content']/translation)" ) );
        // the page's own heading uses the same text
        $this->assertStringContainsString( "{'Language selection'|i18n( 'design/admin/content/create_languages' )}",
            (string)file_get_contents( self::root() . '/design/admin4/templates/content/create_languages.tpl' ) );
    }
}
