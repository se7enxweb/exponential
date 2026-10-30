<?php
/**
 * Right-to-left administration (pull request #286 and the Interface languages page), without a database or browser:
 *   - every administration pagelayout (admin, admin3, admin4, admin4l) sets dir="rtl" from the locale's text direction
 *   - the dashboard's greeting is one translated message with the name isolated in <bdi>, and every catalogue of a
 *     right-to-left language that has the dashboard translates it with both placeholders (Arabic with its own comma)
 *   - values that are read left to right inside a right-to-left page keep their order: the dashboard facts
 *     (unicode-bidi: plaintext, aligned to the label's side under [dir="rtl"]), the version (#ez-version, isolated
 *     ltr), the figures of the debug bar (plaintext)
 *   - the chain on Setup > Interface languages points the other way under [dir="rtl"]
 *   - every locale with TextDirection=rtl is a right-to-left language by its own script, and the Arabic and Hebrew
 *     translations' locales are right to left
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group design
 */

class AdminRightToLeftTest extends PHPUnit\Framework\TestCase
{
    /** the scripts written right to left (CLDR's characterOrder right-to-left) */
    const RTL_SCRIPT = '/[\x{0590}-\x{08FF}\x{07C0}-\x{07FF}\x{FB1D}-\x{FDFF}\x{FE70}-\x{FEFF}]/u';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $relative )
    {
        $file = self::root() . '/' . $relative;
        return is_file( $file ) ? (string)file_get_contents( $file ) : '';
    }

    public function testEveryAdministrationPagelayoutSetsTheDirection()
    {
        foreach ( array( 'admin', 'admin3', 'admin4', 'admin4l' ) as $design )
        {
            $layout = self::read( 'design/' . $design . '/templates/pagelayout.tpl' );
            $this->assertNotSame( '', $layout, $design );
            $this->assertMatchesRegularExpression( '/text_direction.{0,80}dir="rtl"/s', $layout, "design/$design sets dir=\"rtl\" from the locale" );
        }
    }

    public function testTheGreetingIsOneMessageWithTheNameIsolated()
    {
        $tpl = self::read( 'design/admin4/templates/content/dashboard.tpl' );
        $this->assertStringContainsString( "'%greeting, %name'|i18n( 'design/admin/dashboard'", $tpl );
        $this->assertStringContainsString( "concat( '<bdi>', \$user.contentobject.name|wash, '</bdi>' )", $tpl, 'the name washed, then isolated' );
    }

    public static function rightToLeftCatalogues()
    {
        $cases = array();
        foreach ( glob( self::root() . '/share/translations/*/translation.ts' ) as $file )
        {
            $code = basename( dirname( $file ) );
            $locale = self::root() . '/share/locale/' . $code . '.ini';
            if ( is_file( $locale ) && preg_match( '/^TextDirection=rtl\s*$/m', (string)file_get_contents( $locale ) ) )
                $cases[$code] = array( $code, $file );
        }
        return $cases;
    }

    /**
     * @dataProvider rightToLeftCatalogues
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'rightToLeftCatalogues' )]
    public function testARightToLeftCatalogueTranslatesTheGreeting( $code, $file )
    {
        $text = (string)file_get_contents( $file );
        if ( strpos( $text, '<name>design/admin/dashboard</name>' ) === false )
            $this->markTestSkipped( "$code has no dashboard translation" );
        $this->assertMatchesRegularExpression( '#<source>%greeting, %name</source>\s*<translation>([^<]*%greeting[^<]*%name[^<]*|[^<]*%name[^<]*%greeting[^<]*)</translation>#u', $text,
                                               "$code translates the greeting with both placeholders" );
        if ( strpos( $code, 'ara-' ) === 0 )
            $this->assertMatchesRegularExpression( '#<source>%greeting, %name</source>\s*<translation>%greeting، %name</translation>#u', $text, 'Arabic writes its own comma' );
    }

    public function testTheShippedRightToLeftCataloguesAreThere()
    {
        $codes = array_keys( self::rightToLeftCatalogues() );
        $this->assertContains( 'ara-SA', $codes );
        $this->assertContains( 'heb-IL', $codes );
    }

    public function testValuesReadLeftToRightKeepTheirOrder()
    {
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertMatchesRegularExpression( '/\.a4-dash-facts dd \{[^}]*unicode-bidi: plaintext/', $css, 'a fact takes the direction of its own text' );
        $this->assertMatchesRegularExpression( '/\[dir="rtl"\]\)? \.a4-dash-facts dd \{[^}]*text-align: right/', $css, 'and stays beside its label' );
        $content = self::read( 'design/admin4/stylesheets/base.css' );
        $this->assertMatchesRegularExpression( '/#ez-version\s*\{[^}]*direction: ltr;[^}]*unicode-bidi: isolate/', $content );
        $this->assertMatchesRegularExpression( '/#debug \.exp-debug-chip \{[^}]*unicode-bidi: plaintext/', self::read( 'design/standard/stylesheets/expdebugbar.css' ) );
        $this->assertMatchesRegularExpression( '/\.exp-debug-chip-text \{[^}]*unicode-bidi: plaintext/', self::read( 'design/standard/stylesheets/expdebugbar-toolbar.css' ) );
    }

    public function testTheFallbackChainPointsTheOtherWay()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $css = self::read( 'design/' . $design . '/stylesheets/modules/setup.css' );
            $this->assertStringContainsString( '.a4-setup-languages-chain > li + li::before { content: "\2192";', $css, $design );
            $this->assertStringContainsString( '[dir="rtl"] .a4-setup-languages-chain > li + li::before { content: "\2190"; }', $css, $design );
        }
    }

    public function testEveryRightToLeftLocaleIsWrittenInARightToLeftScript()
    {
        $count = 0;
        foreach ( glob( self::root() . '/share/locale/*.ini' ) as $file )
        {
            $text = (string)file_get_contents( $file );
            if ( !preg_match( '/^TextDirection=rtl\s*$/m', $text ) )
                continue;
            $count++;
            $this->assertMatchesRegularExpression( '/^LanguageName=.*' . trim( self::RTL_SCRIPT, '/u' ) . '/mu', $text, basename( $file ) . ': its own name is in a right-to-left script' );
        }
        $this->assertGreaterThanOrEqual( 40, $count );
    }
}
