<?php
/**
 * Every page of the administration interface that assigns a section offers which objects below get it too
 * (eZSection::SCOPE_*): the Details tab of the node view, the section part of the edit form and the content browser
 * of section/assign, in design/admin and design/admin4. doc/eng-us/guides/sections.md, section 8.
 *
 *  SS-01 - each of the five templates of each design sends SectionScope with subtree (preselected), inherited and
 *          object, the values of eZSection::scopes(), and nothing else
 *  SS-02 - the radios of section/assign sit above the browse form and belong to it: form="browse-form", and both
 *          browse.tpl give the form that id; page/section_browse_assign.js keeps the choice per section while the
 *          browser reloads its pages and forgets it when the form is sent
 *  SS-03 - the Details tab always offers the choice (children_count leaves out what the user may not read), and
 *          highlights Set when it changes (admin: inline script, admin4: page/node_details.js)
 *  SS-04 - the copies in design/admin and design/admin4 that were the same file stay the same
 *  SS-05 - every new text is in eng-US, ger-DE and untranslated, in the context the template uses
 *
 * No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSectionScopeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminSectionScopeTest extends PHPUnit\Framework\TestCase
{
    const TEXTS = array(
        'design/admin/node/view/full' => array( 'Apply to', 'Whole subtree', 'Subtree, except branches in another section', 'This object only' ),
        'design/admin/section/browse_assign' => array( 'Which objects get the section', 'The chosen item and every object below it',
                                                       'The chosen item and the objects below it that have its section; branches in another section keep theirs',
                                                       'The chosen item only' ),
        'design/admin/content/job' => array( 'The objects in the subtree that have the section of this item get the section; branches in another section keep theirs, and objects you may not assign it to are skipped.' ),
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function file( $path )
    {
        return AdminFiles::read( self::root() . '/' . $path );
    }

    public static function templates()
    {
        $cases = array();
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            foreach ( array( 'details.tpl', 'content/parts/edit_sections.tpl', 'section/browse_assign.tpl' ) as $template )
                $cases["$design $template"] = array( "design/$design/templates/$template" );
        }
        return $cases;
    }

    /** SS-01 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'templates' )]
    public function testTheTemplateSendsTheThreeScopes( $path )
    {
        require_once self::root() . '/kernel/classes/ezsection.php';
        $tpl = self::file( $path );
        preg_match_all( '/name="SectionScope"[^>]*?value="([a-z]+)"|<option value="([a-z]+)"[^>]*>\{\'(?:Whole subtree|Subtree, except|This object only)/', $tpl, $m );
        $values = array_values( array_filter( array_merge( $m[1], $m[2] ) ) );
        if ( strpos( $tpl, '<select id="' ) !== false && strpos( $path, 'browse_assign' ) === false )
        {
            preg_match( '/<select id="[^"]*" name="SectionScope".*?<\/select>/s', $tpl, $select );
            $this->assertNotEmpty( $select, "$path: a select named SectionScope" );
            preg_match_all( '/<option value="([a-z]+)"( selected="selected")?/', $select[0], $options, PREG_SET_ORDER );
            $values = array_map( function ( $o ) { return $o[1]; }, $options );
            $this->assertSame( 'subtree', $options[0][1] );
            $this->assertSame( ' selected="selected"', $options[0][2] ?? '', "$path: subtree is preselected" );
        }
        else
        {
            $this->assertMatchesRegularExpression( '/name="SectionScope" value="subtree" form="browse-form" checked="checked"/', $tpl );
        }
        sort( $values );
        $scopes = eZSection::scopes();
        sort( $scopes );
        $this->assertSame( $scopes, $values, "$path offers exactly the scopes of eZSection" );
    }

    /** SS-02 */
    public function testTheBrowseRadiosBelongToTheBrowseForm()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $this->assertStringContainsString( '<form name="browse" id="browse-form" method="post"',
                                               self::file( "design/$design/templates/content/browse.tpl" ) );
            $tpl = self::file( "design/$design/templates/section/browse_assign.tpl" );
            $this->assertSame( 3, preg_match_all( '/<input type="radio" name="SectionScope" value="[a-z]+" form="browse-form"/', $tpl ) );
            // the old note said every sub item changes, which is no longer always so
            $this->assertStringNotContainsString( 'Note that the section assignment of the sub items will also be changed.', $tpl );
            $this->assertStringContainsString( '<fieldset class="section-assign-scope" data-section-id="{$browse.content.section_id|wash}">', $tpl );
            $this->assertStringContainsString( "{ezscript( array( 'page/section_browse_assign.js' ) )}", $tpl );
        }
        $script = self::file( 'design/admin4/javascript/page/section_browse_assign.js' );
        $this->assertSame( $script, self::file( 'design/admin/javascript/page/section_browse_assign.js' ) );
        foreach ( array( "'exp-section-assign-scope-' + ( box.getAttribute( 'data-section-id' )", 'window.sessionStorage.getItem( key )',
                         'window.sessionStorage.setItem( key, value )', 'window.sessionStorage.removeItem( key )',
                         "document.getElementById( 'browse-form' )", "addEventListener( 'submit'", 'catch ( e )' ) as $part )
            $this->assertStringContainsString( $part, $script );
        $this->assertDoesNotMatchRegularExpression( '/jQuery|\$\(/', $script, 'plain JavaScript' );
    }

    /** SS-03 */
    public function testTheDetailsTabOffersTheChoiceForANodeWithChildren()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = self::file( "design/$design/templates/details.tpl" );
            $this->assertMatchesRegularExpression( '/\*\}\s*<select id="selected-section-scope" name="SectionScope"/', $tpl, 'not behind a condition' );
            $this->assertMatchesRegularExpression( '/<form name="changesection".*selected-section-scope.*<\/form>/s', $tpl, 'inside the form that sets the section' );
        }
        $this->assertStringContainsString( "$('#selected-section-id, #selected-section-scope').on('change'", self::file( 'design/admin/templates/details.tpl' ) );
        $this->assertStringContainsString( "querySelectorAll('#selected-section-id, #selected-section-scope')", self::file( 'design/admin4/javascript/page/node_details.js' ) );
    }

    /** SS-04 */
    public function testTheSharedCopiesStayTheSame()
    {
        foreach ( array( 'content/parts/edit_sections.tpl', 'section/browse_assign.tpl' ) as $template )
            $this->assertSame( self::file( "design/admin/templates/$template" ), self::file( "design/admin4/templates/$template" ), $template );
    }

    /** SS-05 */
    public function testTheTextsAreTranslated()
    {
        foreach ( array( 'eng-US', 'ger-DE', 'untranslated' ) as $locale )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->load( self::root() . "/share/translations/$locale/translation.ts" ) );
            $xpath = new DOMXPath( $dom );
            foreach ( self::TEXTS as $context => $texts )
            {
                foreach ( $texts as $text )
                {
                    $found = $xpath->query( "//context[name='$context']/message[source=" . self::literal( $text ) . "]/translation" );
                    $this->assertSame( 1, $found->length, "$locale: \"$text\" in $context" );
                    if ( $locale !== 'untranslated' )
                        $this->assertNotSame( '', trim( $found->item( 0 )->textContent ), "$locale: \"$text\" is translated" );
                }
            }
        }
        $templates = self::file( 'design/admin4/templates/details.tpl' ) . self::file( 'design/admin4/templates/section/browse_assign.tpl' )
                   . self::file( 'kernel/private/classes/views/section/assign.php' );
        foreach ( self::TEXTS as $context => $texts )
        {
            foreach ( $texts as $text )
                $this->assertStringContainsString( $text, $templates, "\"$text\" is used" );
        }
    }

    private static function literal( $text )
    {
        return strpos( $text, "'" ) === false ? "'$text'" : '"' . $text . '"';
    }
}
