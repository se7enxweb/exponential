<?php
/**
 * Every place a user sees the copyright notice shows exactly "Exponential Copyright © 1998-2026 Exponential
 * Foundation and 7x" (#284, owner decision 2026-10-29): the footers and sign-in pages of the administration designs,
 * the standard and base design footers, the setup wizard, the page of an undefined module, the fatal error page,
 * ./console --version, the /ezinfo/copyright page and the help's documentation notice. The notice is never
 * translated: in every catalogue the footer message's finished translation is its source. DB-less.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/CopyrightFooterNoticeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class CopyrightFooterNoticeTest extends PHPUnit\Framework\TestCase
{
    const VISIBLE = 'Exponential Copyright © 1998-2026 Exponential Foundation and 7x';
    const MESSAGE = '<a href="%ezpublish_link">Exponential</a> Copyright &copy; 1998-2026 <a href="%foundation_link">Exponential Foundation</a> and <a href="%7x_link">7x</a>';
    const CONTEXT = 'design/admin/page_copyright';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /**
     * What a reader sees of a source text: template comments and tags removed, entities decoded, the opening of a
     * template string ({' ... '|i18n) dropped, whitespace collapsed.
     */
    private static function seen( $text )
    {
        $text = preg_replace( '/\{\*.*?\*\}/s', '', $text );
        $text = str_replace( array( "{'", "{\"" ), ' ', $text );
        $text = html_entity_decode( strip_tags( $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        return preg_replace( '/\s+/u', ' ', $text );
    }

    public static function visibleProvider()
    {
        return array(
            'admin footer' => array( 'design/admin/templates/page_copyright.tpl' ),
            'admin sign-in' => array( 'design/admin/templates/page_login_copyright.tpl' ),
            'admin4 footer' => array( 'design/admin4/templates/page_copyright.tpl' ),
            'admin4 sign-in' => array( 'design/admin4/templates/page_login_copyright.tpl' ),
            'standard footer' => array( 'design/standard/templates/page_copyright.tpl' ),
            'base page layout' => array( 'design/base/templates/pagelayout.tpl' ),
            'base sign-in layout' => array( 'design/base/templates/loginpagelayout.tpl' ),
            'setup wizard layout' => array( 'design/standard/templates/setup_pagelayout.tpl' ),
            'setup wizard footer' => array( 'design/standard/templates/setup/page_copyright.tpl' ),
            'undefined module' => array( 'design/standard/templates/undefinedmodule.tpl' ),
            'fatal error page' => array( 'lib/ezutils/classes/ezexecution.php' ),
            'console --version' => array( 'bin/php/console' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('visibleProvider')]
    public function testShowsTheNotice( $path )
    {
        $text = self::read( $path );
        $shown = false;
        // line by line: a whole template or PHP file is no HTML, and strip_tags() would eat it from its first "<"
        foreach ( preg_split( '/\R/', $text ) as $line )
        {
            if ( strpos( self::seen( $line ), self::VISIBLE ) !== false )
                $shown = true;
        }
        $this->assertTrue( $shown, "$path shows the notice" );
        foreach ( array( '7x</a> and others', '7x and others', '7x / Exponential Foundation', '7x</a> &amp; <a', '(except where otherwise noted)' ) as $old )
            $this->assertStringNotContainsString( $old, $text, "$path: no old notice" );
    }

    public function testTheAdminFootersShareOneMessage()
    {
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            foreach ( array( 'page_copyright.tpl', 'page_login_copyright.tpl' ) as $name )
            {
                $tpl = self::read( "$design/templates/$name" );
                $this->assertStringContainsString( "{'" . self::MESSAGE . "'|i18n( '" . self::CONTEXT . "',,", $tpl, "$design/$name" );
                $this->assertStringContainsString( "'%foundation_link', 'https://foundation.exponential.earth'", $tpl, "$design/$name" );
                $this->assertStringContainsString( "'%7x_link', 'https://se7enx.com'", $tpl, "$design/$name" );
            }
        }
    }

    public function testTheCopyrightPage()
    {
        $view = self::read( 'kernel/private/classes/views/ezinfo/copyright.php' );
        $this->assertStringContainsString( "'years'           => '1998-2026'", $view );
        $this->assertStringContainsString( "'holder'          => 'Exponential Foundation and 7x'", $view );
        $this->assertStringContainsString( "<p>Exponential Copyright &copy; {\$copyrightInfo['years']} {\$copyrightInfo['holder']}</p>", $view );
        // the original developer's notice stays
        $this->assertStringContainsString( "'original_holder' => 'eZ Systems AS'", $view );
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            $tpl = self::read( "$design/templates/ezinfo/copyright.tpl" );
            $this->assertStringContainsString( '<p class="ezinfo-notice-holder">Exponential Copyright &copy; {$copyright_info.years|wash} {$copyright_info.holder|wash}</p>', $tpl, $design );
            $this->assertStringNotContainsString( "'Copyright (C) %years %holder. All rights reserved.'|i18n", $tpl, "$design: the notice is not translated" );
        }
    }

    public function testTheHelpNotice()
    {
        $info = expHelpLicense::info();
        $this->assertSame( self::VISIBLE, $info['copyright'] );
        $ini = self::read( 'settings/exphelp.ini' );
        $this->assertStringContainsString( "\nCopyrightYears=1998-2026\n", $ini );
        $this->assertStringContainsString( "\nCopyrightHolder=Exponential Foundation and 7x\n", $ini );
    }

    public function testTheSystemCard()
    {
        $this->assertStringContainsString( self::VISIBLE, self::read( 'share/syscard/about.txt' ) );
    }

    /**
     * The footer message is in every catalogue, and a finished translation is the notice itself.
     */
    public function testNoCatalogueTranslatesTheNotice()
    {
        $files = glob( self::root() . '/share/translations/*/translation.ts' );
        $this->assertGreaterThan( 100, count( $files ) );
        $missing = array();
        $translated = array();
        foreach ( $files as $file )
        {
            $doc = new DOMDocument( '1.0', 'utf-8' );
            $this->assertTrue( @$doc->load( $file ), "$file is XML" );
            $xpath = new DOMXPath( $doc );
            $found = false;
            foreach ( $xpath->query( '//context[name="' . self::CONTEXT . '"]/message' ) as $message )
            {
                $source = $xpath->query( 'source', $message )->item( 0 )->textContent;
                $this->assertStringNotContainsString( 'and others.', $source, "$file: old notice" );
                if ( $source !== self::MESSAGE )
                    continue;
                $found = true;
                $translation = $xpath->query( 'translation', $message )->item( 0 );
                $unfinished = $translation->getAttribute( 'type' ) === 'unfinished' && trim( $translation->textContent ) === '';
                if ( !$unfinished && $translation->textContent !== self::MESSAGE )
                    $translated[] = substr( $file, strlen( self::root() ) + 1 );
            }
            if ( !$found )
                $missing[] = substr( $file, strlen( self::root() ) + 1 );
        }
        $this->assertSame( array(), $missing, 'catalogues without the footer message' );
        $this->assertSame( array(), $translated, 'catalogues that translate the notice' );
    }
}
