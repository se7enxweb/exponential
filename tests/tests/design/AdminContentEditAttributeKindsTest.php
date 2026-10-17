<?php
/**
 * admin4 content/edit draws each attribute as a card that names the kind of its input (an icon and a word) in its
 * corner, lets two short inputs share a row and lists the fields of a long form above it
 * (design/admin4/templates/content/edit_attribute.tpl). The mapping datatype -> kind -> name and icon is in
 * admininterface.ini [ContentEditAttributeSettings]. This test keeps the mapping complete: every kernel datatype has
 * a kind, every kind a name and an icon, every icon a mask rule in both copies of components.css and an entry in
 * design.ini IconList[], every name a message in the German and the English catalogue. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminContentEditAttributeKindsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminContentEditAttributeKindsTest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = 'design/admin/content/edit_attribute';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * The settings of one block of an INI file: name => value, name => array for name[] and name[key]
     *
     * @param string $file
     * @param string $block
     * @return array
     */
    private static function block( $file, $block )
    {
        $settings = array();
        $current = null;
        foreach ( preg_split( '/\R/', AdminFiles::read( self::root() . '/' . $file ) ) as $line )
        {
            if ( preg_match( '/^\[([^\]]+)\]\s*$/', $line, $m ) )
            {
                $current = $m[1];
                continue;
            }
            if ( $current !== $block || $line === '' || $line[0] === '#' )
                continue;
            if ( preg_match( '/^(\w+)\[\]$/', $line, $m ) )
                $settings[$m[1]] = array();
            else if ( preg_match( '/^(\w+)\[\]=(.*)$/', $line, $m ) )
                $settings[$m[1]][] = $m[2];
            else if ( preg_match( '/^(\w+)\[([^\]]+)\]=(.*)$/', $line, $m ) )
                $settings[$m[1]][$m[2]] = $m[3];
            else if ( preg_match( '/^(\w+)=(.*)$/', $line, $m ) )
                $settings[$m[1]] = $m[2];
        }
        return $settings;
    }

    private static function settings()
    {
        return self::block( 'settings/admininterface.ini', 'ContentEditAttributeSettings' );
    }

    public function testEveryKindHasANameAndAnIcon()
    {
        $settings = self::settings();
        foreach ( array( 'DefaultKind', 'KindName', 'KindIcon', 'DatatypeKind', 'CompactDatatypes', 'FieldNavigationFrom' ) as $name )
            $this->assertArrayHasKey( $name, $settings );
        $kinds = array_unique( array_merge( array_values( $settings['DatatypeKind'] ), array( $settings['DefaultKind'] ) ) );
        foreach ( $kinds as $kind )
        {
            $this->assertNotSame( '', $settings['KindName'][$kind] ?? '', "kind $kind has a name" );
            $this->assertMatchesRegularExpression( '/^[a-z0-9-]+$/', $settings['KindIcon'][$kind] ?? '', "kind $kind has an icon" );
        }
        $this->assertSame( array_keys( $settings['KindName'] ), array_keys( $settings['KindIcon'] ), 'a name and an icon for the same kinds' );
        $this->assertEmpty( array_diff( array_keys( $settings['KindName'] ), $kinds ), 'no kind that no datatype uses' );
        $this->assertGreaterThan( 0, (int)$settings['FieldNavigationFrom'] );
    }

    public function testEveryKernelDatatypeHasAKind()
    {
        $kinds = self::settings()['DatatypeKind'];
        foreach ( AdminFiles::files( self::root() . '/kernel/classes/datatypes/*', GLOB_ONLYDIR ) as $dir )
            $this->assertArrayHasKey( basename( $dir ), $kinds, basename( $dir ) . ' has a kind' );
    }

    public function testCompactDatatypesHaveAKind()
    {
        $settings = self::settings();
        $this->assertNotEmpty( $settings['CompactDatatypes'] );
        foreach ( $settings['CompactDatatypes'] as $datatype )
            $this->assertArrayHasKey( $datatype, $settings['DatatypeKind'], "compact $datatype has a kind" );
    }

    public function testEveryIconIsShippedAndListed()
    {
        preg_match_all( '/^IconList\[\]=(\S+)$/m', AdminFiles::read( self::root() . '/settings/design.ini' ), $m );
        $icons = $m[1];
        $this->assertNotEmpty( $icons );
        foreach ( array_unique( self::settings()['KindIcon'] ) as $icon )
        {
            $this->assertFileExists( self::root() . "/share/icons/bootstrap-icons/svg/$icon.svg" );
            $this->assertContains( $icon, $icons, "design.ini IconList[] names $icon" );
            foreach ( array( 'admin4', 'admin' ) as $design )
            {
                $css = AdminFiles::read( self::root() . "/design/$design/stylesheets/components.css" );
                $this->assertStringContainsString( ".a4-bi-$icon { -webkit-mask-image: url(../../../share/icons/bootstrap-icons/svg/$icon.svg)", $css, "$design: .a4-bi-$icon" );
            }
        }
    }

    public function testEveryNameIsInTheCatalogues()
    {
        $sources = array_values( array_unique( self::settings()['KindName'] ) );
        $sources[] = 'Fields of this form';
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $document = new DOMDocument();
            $this->assertTrue( $document->load( self::root() . "/share/translations/$locale/translation.ts" ) );
            $found = array();
            foreach ( ( new DOMXPath( $document ) )->query( '/TS/context[name="' . self::CONTEXT . '"]/message' ) as $message )
            {
                $translation = $message->getElementsByTagName( 'translation' )->item( 0 );
                if ( $translation && $translation->textContent !== '' && $translation->getAttribute( 'type' ) !== 'obsolete' )
                    $found[] = $message->getElementsByTagName( 'source' )->item( 0 )->textContent;
            }
            foreach ( $sources as $source )
                $this->assertContains( $source, $found, "$locale translates \"$source\"" );
            $this->assertSame( count( $found ), count( array_unique( $found ) ), "$locale: no message twice" );
        }
    }

    public function testTheTemplateReadsTheSettingsAndHidesTheKindFromScreenReaders()
    {
        $tpl = AdminFiles::read( self::root() . '/design/admin4/templates/content/edit_attribute.tpl' );
        foreach ( array( 'DatatypeKind', 'KindName', 'KindIcon', 'DefaultKind', 'CompactDatatypes', 'FieldNavigationFrom' ) as $name )
            $this->assertStringContainsString( "ezini( 'ContentEditAttributeSettings', '$name', 'admininterface.ini' )", $tpl );
        // the kind is a visual hint; the control states its kind itself
        $this->assertMatchesRegularExpression( '/<span class="a4-content-attr-kind" aria-hidden="true">/', $tpl );
        $this->assertStringContainsString( "|i18n( '" . self::CONTEXT . "' )|wash", $tpl );
        $this->assertStringContainsString( '<nav class="a4-content-attr-nav" aria-label="{\'Fields of this form\'|i18n( \'' . self::CONTEXT . '\' )}">', $tpl );
        // a translation keeps one card per row: the original stands beside each field
        $this->assertStringContainsString( '$is_translating_content|not, $a4_compact|contains( $attribute.data_type_string )', $tpl );
    }

    public function testALinkToACardOpensItsCollapsedGroup()
    {
        $js = AdminFiles::read( self::root() . '/design/admin4/javascript/page/content_edit_attribute.js' );
        $this->assertStringContainsString( "'a[href^=\"#ezcca-attr-\"]'", $js );
        $this->assertStringContainsString( "removeClass('ezcca-collapsed')", $js );
    }

    /**
     * A control without a name of its own (the author list, an enhanced selection) is named by its attribute's name
     * and its column header, and a control that has a name keeps it.
     */
    public function testAControlWithoutANameIsNamedByItsAttribute()
    {
        $js = AdminFiles::read( self::root() . '/design/admin4/javascript/page/content_edit_attribute.js' );
        $this->assertStringContainsString( "this.id.replace( 'ezcca-attr-', 'ezcca-name-' )", $js );
        $this->assertStringContainsString( "this.labels && this.labels.length", $js );
        $this->assertStringContainsString( "this.getAttribute('aria-labelledby')", $js );
        $this->assertStringContainsString( "this.setAttribute( 'aria-label', label )", $js );
    }

    public function testTheCardsUseLogicalPropertiesInBothCopies()
    {
        $admin4 = AdminFiles::read( self::root() . '/design/admin4/stylesheets/modules/content.css' );
        $this->assertSame( $admin4, AdminFiles::read( self::root() . '/design/admin/stylesheets/modules/content.css' ), 'both copies of modules/content.css' );
        $start = strpos( $admin4, '.a4-content-attrs {' );
        $end = strpos( $admin4, '.a4-content-attr-nav a.is-invalid' );
        $this->assertNotFalse( $start );
        $this->assertNotFalse( $end );
        $cards = substr( $admin4, $start, $end - $start );
        // right-to-left: the kind stands at the inline end, the name keeps room for it there
        $this->assertDoesNotMatchRegularExpression( '/(?<![-\w])(?:left|right|padding-left|padding-right|margin-left|margin-right)\s*:/', $cards );
        $this->assertStringContainsString( 'inset-inline-end: var(--a4-space-3);', $cards );
    }
}
