<?php
/**
 * admin4 shows a content language as a short code badge (DE, EN; EN-GB and EN-US when the installation has both)
 * and its name, not as a flag: a flag stands for a country. Every place goes through design:parts/language_badge.tpl
 * (templates) or the languageBadges map of children_detailed.tpl (the sub-items table's Translations column). The
 * code comes from eZLocale's content_language_code, the locale file's [HTTP] ContentLanguage, which site.ini
 * [RegionalSettings] HTTPLocale does not override. doc/features/6.0/admin4-design.md, doc/bc/6.0/admin4-styling.md.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLanguageBadgeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminLanguageBadgeTest extends PHPUnit\Framework\TestCase
{
    const INCLUDE_FILE = 'design/admin4/templates/parts/language_badge.tpl';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** Every template, override template and script of admin4 */
    private static function admin4Files()
    {
        $files = array();
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/design/admin4', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
        {
            if ( preg_match( '/\.(tpl|js)$/', $file->getFilename() ) )
                $files[] = $file->getPathname();
        }
        sort( $files );
        return $files;
    }

    public function testAdmin4DrawsNoFlag()
    {
        $found = array();
        foreach ( self::admin4Files() as $file )
        {
            $source = (string)file_get_contents( $file );
            if ( preg_match( '/flag_icon|share\/icons\/flags|flagIcons/', $source ) )
                $found[] = substr( $file, strlen( self::root() ) + 1 );
        }
        $this->assertSame( array(), $found, 'These admin4 files still draw a flag; use design:parts/language_badge.tpl' );
    }

    public function testTheIncludeExistsAndUsesTheContentLanguageCode()
    {
        $file = self::root() . '/' . self::INCLUDE_FILE;
        $this->assertFileExists( $file );
        $source = (string)file_get_contents( $file );
        $this->assertStringContainsString( '.content_language_code', $source );
        $this->assertStringNotContainsString( 'http_locale_code', $source, 'http_locale_code follows site.ini HTTPLocale, which is one tag for the whole site' );
        $this->assertStringContainsString( 'class="a4-lang', $source );
        $this->assertStringContainsString( 'aria-label=', $source, 'A badge without its name next to it needs an accessible name' );
        $this->assertStringEndsNotWith( "\n", $source, 'A trailing newline would end up in the scripts and attributes that use plain=true()' );
    }

    public function testEveryBadgeGoesThroughTheInclude()
    {
        $uses = 0;
        foreach ( self::admin4Files() as $file )
        {
            if ( substr( $file, -3 ) !== 'tpl' )
                continue;
            $uses += substr_count( (string)file_get_contents( $file ), "design:parts/language_badge.tpl" );
            $this->assertDoesNotMatchRegularExpression( '/class="a4-lang"[^>]*>[A-Z]{2}</', (string)file_get_contents( $file ),
                                                        basename( $file ) . ' writes a language code by hand' );
        }
        $this->assertGreaterThanOrEqual( 20, $uses );
    }

    /**
     * An include sees the variables of the template that includes it, so an optional parameter that is not passed
     * reads the caller's variable of the same name: $class is the eZContentClass in class/view.tpl and class/edit.tpl,
     * and {$class|wash} on it is a fatal error. The optional parameters therefore carry the prefix badge_.
     */
    public function testTheOptionalParametersCannotReadACallersVariable()
    {
        $source = (string)file_get_contents( self::root() . '/' . self::INCLUDE_FILE );
        $code = (string)preg_replace( '/\{\*.*?\*\}/s', '', $source );
        preg_match_all( '/\$([a-z_0-9]+)/', $code, $matches );
        $read = array_values( array_unique( $matches[1] ) );
        foreach ( $read as $name )
        {
            $this->assertMatchesRegularExpression( '/^(locale|badge_[a-z]+|a4_lang_[a-z_]+)$/', $name,
                                                   "The include reads \$$name, which a caller may have set for itself" );
        }
        foreach ( array( 'badge_name', 'badge_named', 'badge_plain', 'badge_class' ) as $name )
            $this->assertContains( $name, $read );
    }

    public function testEveryIncludePassesOnlyKnownParameters()
    {
        $known = array( 'uri', 'locale', 'badge_name', 'badge_named', 'badge_plain', 'badge_class' );
        $includes = 0;
        foreach ( self::admin4Files() as $file )
        {
            if ( substr( $file, -3 ) !== 'tpl' )
                continue;
            preg_match_all( "/\{include\s+uri='design:parts\/language_badge\.tpl'[^}]*\}/", (string)file_get_contents( $file ), $matches );
            foreach ( $matches[0] as $include )
            {
                ++$includes;
                preg_match_all( '/\s([a-z_]+)=/', $include, $names );
                $this->assertSame( array(), array_values( array_diff( $names[1], $known ) ), basename( $file ) . ": $include" );
                $this->assertStringContainsString( ' locale=', $include, basename( $file ) . ": $include" );
            }
        }
        $this->assertGreaterThanOrEqual( 20, $includes );
    }

    public function testTheSubItemsColumnEscapesAndNamesItsBadges()
    {
        $script = (string)file_get_contents( self::root() . '/design/admin4/javascript/ezajaxsubitems_expdatatable.js' );
        $this->assertStringContainsString( 'confObj.languageBadges', $script );
        // the class carries the state (a4-lang is-main|is-present|is-missing, AdminLanguageStatesTest)
        $this->assertStringContainsString( "cls = 'a4-lang is-' + state", $script );
        $this->assertMatchesRegularExpression( '/class="\' \+ cls \+ \'"[^;]*aria-label=/', $script );
        // A badge link's name starts with the code it shows (WCAG 2.5.3 label in name): the name follows as hidden text
        $this->assertStringContainsString( "+ badge + '<span class=\"a4-sr-only\"> ' + label + '</span></a>'", $script );
        $body = (string)file_get_contents( self::root() . '/design/admin4/templates/node/view/a4_full_body.tpl' );
        // the node's badges show each language's state (parts/language_state_badge.tpl): the code, then the state as hidden text
        $this->assertStringContainsString( "design:parts/language_state_badge.tpl' state_entry=\$language", $body );
        $state = (string)file_get_contents( self::root() . '/design/admin4/templates/parts/language_state_badge.tpl' );
        $this->assertStringContainsString( '{$a4_ls_code|wash}<span class="a4-sr-only"> {$a4_ls_label|wash}</span></a>', $state );
        $template = (string)file_get_contents( self::root() . '/design/admin4/templates/children_detailed.tpl' );
        $this->assertStringContainsString( 'languageBadges: languageBadges', $template );
        $this->assertStringContainsString( "design:parts/language_badge.tpl' locale=\$locale.locale_code badge_plain=true()", $template );
    }

    /**
     * The classic admin design shows the code too: its copy of the sub-items script is kept identical to admin4's
     * (AdminContextMenuTest), so its template has to hand the script the same languageBadges map, built by its own
     * identical copy of the include. The badge's style is in both copies of components.css.
     */
    public function testNeitherDesignDrawsAFlagInTheSubItemsTable()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $script = (string)file_get_contents( self::root() . "/design/$design/javascript/ezajaxsubitems_expdatatable.js" );
            $this->assertDoesNotMatchRegularExpression( '/flagIcons|flag_icon|share\/icons\/flags/', $script,
                                                        "design/$design's sub-items script still draws a flag" );
            $this->assertStringContainsString( 'confObj.languageBadges', $script, $design );
            $template = (string)file_get_contents( self::root() . "/design/$design/templates/children_detailed.tpl" );
            $this->assertDoesNotMatchRegularExpression( '/flagIcons|flag_icon/', $template,
                                                        "design/$design's children_detailed.tpl still hands the script flags" );
            $this->assertStringContainsString( 'languageBadges: languageBadges', $template, $design );
        }
        $this->assertFileEquals( self::root() . '/' . self::INCLUDE_FILE,
                                 self::root() . '/design/admin/templates/parts/language_badge.tpl',
                                 'The classic admin design needs the same language badge include as admin4' );
    }

    public function testTheComponentIsInBothCopiesOfTheComponentsStylesheet()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $css = (string)file_get_contents( self::root() . "/design/$design/stylesheets/components.css" );
            $this->assertMatchesRegularExpression( '/^\.a4-lang \{/m', $css, $design );
            $this->assertMatchesRegularExpression( '/^\.a4-langs \{/m', $css, $design );
        }
    }

    public function testEZLocaleNamesItsContentLanguageCode()
    {
        $source = (string)file_get_contents( self::root() . '/lib/ezlocale/classes/ezlocale.php' );
        $this->assertStringContainsString( "'content_language_code' => 'contentLanguageCode'", $source );
        $this->assertMatchesRegularExpression( '/function contentLanguageCode\(\)\s*\{\s*return \(string\)\$this->HTTPLocaleCode;/', $source );
    }
}
