<?php
/**
 * The sub-items table of the node view is drawn by Exponential UI (extension expui, Exp.$.fn.expDataTable). Without
 * it the table cannot be drawn; the place of the table then says so instead of staying empty.
 *
 *  SX-01 - Both designs (admin, admin4) put a note into #content-sub-items-list when Exponential UI is missing or
 *          not loaded, once, as text (textContent, no markup), with role status, after the page is loaded
 *  SX-02 - The note is the design's warning box: a div (both designs style their warning on a div), admin4's
 *          a4-notice is-warn, admin's message-warning with its text in a paragraph
 *  SX-03 - Its text comes from the template, translated and washed for JavaScript, and is in ger-DE and eng-US once
 *  SX-04 - With Exponential UI the table is set up as before
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSubitemsWithoutExpUITest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSubitemsWithoutExpUITest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = 'design/admin/node/view/full';

    const TEXT = 'The sub items cannot be listed: Exponential UI (the extension expui) is not active or not loaded on this siteaccess.';

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public static function designs()
    {
        return array(
            'admin4' => array( 'design/admin4/templates/children_detailed.tpl', 'a4-notice is-warn', 'note.textContent = noTableText;' ),
            'admin'  => array( 'design/admin/templates/children_detailed.tpl', 'message-warning', 'text.textContent = noTableText;' ),
        );
    }

    /**
     * @dataProvider designs
     */
    #[PHPUnit\Framework\Attributes\DataProvider('designs')]
    public function testThePlaceOfTheTableSaysWhyItIsEmpty( $template, $class, $insert )
    {
        $tpl = self::file( $template );
        $this->assertStringContainsString( 'var noTableText = "{\'' . self::TEXT . '\'|i18n( \'' . self::CONTEXT . '\' )|wash(\'javascript\')}";', $tpl );
        $this->assertMatchesRegularExpression(
            '#if \( window\.Exp && Exp\.\$ && Exp\.\$\.fn\.expDataTable && window\.eZAjaxSubitemsExpDataTable \) \{\s*Exp\.ready\( function\(\) \{\s*'
            . 'eZAjaxSubitemsExpDataTable\.init\(confObj, labelsObj, createGroups, createOptions\);\s*\} \);\s*\}\s*else \{#', $tpl,
            'with Exponential UI the table is set up as before, else the note' );
        $else = substr( $tpl, strpos( $tpl, "else {\n    // Without Exponential UI" ) );
        $else = substr( $else, 0, strpos( $else, '{/literal}' ) );
        $this->assertStringContainsString( "document.getElementById( 'content-sub-items-list' )", $else );
        $this->assertStringContainsString( "var note = document.createElement( 'div' );", $else, 'the warning box of both designs is a div' );
        $this->assertStringContainsString( "note.className = '" . $class . " exp-no-subitems-table';", $else );
        $this->assertStringContainsString( "note.setAttribute( 'role', 'status' );", $else );
        $this->assertStringContainsString( $insert, $else );
        $this->assertStringNotContainsString( 'innerHTML', $else, 'the text is inserted as text' );
        $this->assertStringContainsString( "list.querySelector( '.exp-no-subitems-table' )", $else, 'once' );
        $this->assertStringContainsString( "document.addEventListener( 'DOMContentLoaded', showNoTable );", $else, 'the place comes after the script' );
    }

    public function testTheTextIsTranslated()
    {
        foreach ( array( 'ger-DE', 'eng-US' ) as $language )
        {
            $dom = new DOMDocument();
            $dom->load( dirname( __DIR__, 3 ) . '/share/translations/' . $language . '/translation.ts' );
            $found = ( new DOMXPath( $dom ) )->query( "//context[name='" . self::CONTEXT . "']/message[source='" . self::TEXT . "']/translation" );
            $this->assertSame( 1, $found->length, "$language: once" );
            $this->assertNotSame( '', trim( $found->item( 0 )->textContent ), $language );
        }
    }
}
