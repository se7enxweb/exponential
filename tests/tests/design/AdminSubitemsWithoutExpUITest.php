<?php
/**
 * The sub-items table of the node view is drawn by Exponential UI (extension expui, exp::datatable). Without
 * it the table cannot be drawn; the place of the table then says so instead of staying empty.
 *
 *  SX-01 - Both designs (admin, admin4; admin4 from its page script, the text in data-no-table) put a note into #content-sub-items-list when Exponential UI is missing or
 *          not loaded, once, as text (textContent, no markup), with role status, after the page is loaded
 *  SX-02 - The note is the design's warning box: a div (both designs style their warning on a div), admin4's
 *          a4-notice is-warn, admin's message-warning with its text in a paragraph
 *  SX-03 - Its text comes from the template, translated and washed for JavaScript, and is in ger-DE and eng-US once
 *  SX-04 - With Exponential UI the table is set up as before
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSubitemsWithoutExpUITest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminNodeBinary.php';

class AdminSubitemsWithoutExpUITest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = 'design/admin/node/view/full';

    const TEXT = 'The sub items cannot be listed: Exponential UI (the extension expui) is not active or not loaded on this siteaccess.';

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    /** The classic design, whose sub-items script is inline; admin4's is a file (testAdmin4SaysWhyTheTableIsMissing) */
    public static function designs()
    {
        return array(
            'admin'  => array( 'design/admin/templates/children_detailed.tpl', 'message-warning', 'text.textContent = noTableText;' ),
        );
    }

    /**
     * SX-01 to SX-04 for admin4, which has no inline script (doc/eng-us/features/6.0/content-security-policy.md): the text is
     * the data-no-table attribute of #subitems-config, translated and washed for the attribute, and
     * javascript/page/children_detailed.js inserts it; the script runs in node against js/mini_dom.js
     * (js/subitems_no_table.js)
     */
    public function testAdmin4SaysWhyTheTableIsMissing()
    {
        $tpl = self::file( 'design/admin4/templates/children_detailed.tpl' );
        $this->assertStringContainsString( 'data-no-table="{\'' . self::TEXT . '\'|i18n( \'' . self::CONTEXT . '\' )|wash}"', $tpl );
        $this->assertStringContainsString( "{ezscript( array( 'page/children_detailed.js' ) )}", $tpl );
        $script = self::file( 'design/admin4/javascript/page/children_detailed.js' );
        $this->assertStringContainsString( "config.getAttribute('data-no-table')", $script );
        $this->assertStringNotContainsString( 'innerHTML', $script, 'the text is inserted as text' );

        $node = AdminNodeBinary::require();
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/subitems_no_table.js' ) . ' '
                                   . escapeshellarg( dirname( __DIR__, 3 ) . '/design/admin4/javascript/page/children_detailed.js' ) . ' 2>&1' );
        $cases = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, "the harness ran: $out" );
            $cases[$row['name']] = $row;
        }
        $this->assertSame( 1, $cases['without']['notes'], 'one note, also when the script runs twice' );
        $this->assertSame( 'No table <b>here</b> & "now"', $cases['without']['text'], 'the text as it is, no markup' );
        $this->assertSame( 0, $cases['without']['markup'] );
        $this->assertSame( 'status', $cases['without']['role'] );
        $this->assertSame( 'a4-notice is-warn exp-no-subitems-table', $cases['without']['cls'] );
        $this->assertSame( 0, $cases['without']['inits'] );
        $this->assertSame( 0, $cases['with']['notes'], 'with Exponential UI the table is set up as before' );
        $this->assertGreaterThan( 0, $cases['with']['inits'] );
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
