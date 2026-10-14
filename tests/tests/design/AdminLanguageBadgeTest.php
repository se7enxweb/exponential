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

    public function testTheSubItemsColumnEscapesAndNamesItsBadges()
    {
        $script = (string)file_get_contents( self::root() . '/design/admin4/javascript/ezajaxsubitems_expdatatable.js' );
        $this->assertStringContainsString( 'confObj.languageBadges', $script );
        $this->assertMatchesRegularExpression( '/class="a4-lang"[^;]*aria-label=/', $script );
        $template = (string)file_get_contents( self::root() . '/design/admin4/templates/children_detailed.tpl' );
        $this->assertStringContainsString( 'languageBadges: languageBadges', $template );
        $this->assertStringContainsString( "design:parts/language_badge.tpl' locale=\$locale.locale_code plain=true()", $template );
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
