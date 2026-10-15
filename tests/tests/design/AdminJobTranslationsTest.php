<?php
/**
 * The German admin shows the content job pages, the hide confirmation and the cache page in German, and the
 * "Edit in" menus name a language as the rest of the admin does.
 *
 *  JT-01 - every text of the content job views and templates (context design/admin/content/job) is in ger-DE and eng-US
 *  JT-02 - the cache page's title is translated into German, not left in English
 *  JT-03 - the "Edit in" menus of the tree and the sub-items table take the content languages' names
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminJobTranslationsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminJobTranslationsTest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = 'design/admin/content/job';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** source => translation of $context in the translation file of $locale (all contexts of that name) */
    private static function messages( $locale, $context )
    {
        $dom = new DOMDocument();
        self::assertTrue( $dom->load( self::root() . "/share/translations/$locale/translation.ts" ) );
        $messages = array();
        foreach ( ( new DOMXPath( $dom ) )->query( "//context[name='$context']/message" ) as $message )
        {
            $translation = $message->getElementsByTagName( 'translation' )->item( 0 );
            $messages[$message->getElementsByTagName( 'source' )->item( 0 )->textContent] = $translation ? $translation->textContent : '';
        }
        return $messages;
    }

    /** The texts of the job context in the job views and templates */
    private static function jobTexts()
    {
        $texts = array();
        $files = array_merge( glob( self::root() . '/kernel/private/classes/views/content/*.php' ),
                              glob( self::root() . '/kernel/classes/contentjob/*.php' ) );
        foreach ( $files as $file )
        {
            $code = (string)file_get_contents( $file );
            $tr = '(?:\\\\?ezpI18n::tr\(\s*\'' . preg_quote( self::CONTEXT, '/' ) . '\'\s*,|\$tr\()';
            // a call with the text, or with a choice of two texts ($a ? 'x' : 'y')
            preg_match_all( '/' . $tr . '\s*(?:\$\w+\s*\?\s*)?\'((?:[^\'\\\\]|\\\\.)*)\'(?:\s*:\s*\'((?:[^\'\\\\]|\\\\.)*)\')?/', $code, $m, PREG_SET_ORDER );
            foreach ( $m as $match )
                foreach ( array_slice( $match, 1 ) as $text )
                    if ( $text !== '' )
                        $texts[stripslashes( $text )] = basename( $file );
        }
        foreach ( glob( self::root() . '/design/admin4/templates/content/job*.tpl' ) as $file )
        {
            preg_match_all( '/\'((?:[^\'\\\\]|\\\\.)*)\'\|i18n\(\s*\'' . preg_quote( self::CONTEXT, '/' ) . '\'/', (string)file_get_contents( $file ), $m );
            foreach ( $m[1] as $text )
                $texts[stripslashes( $text )] = basename( $file );
        }
        return $texts;
    }

    /** JT-01 */
    public function testTheJobTextsAreTranslated()
    {
        $texts = self::jobTexts();
        $this->assertGreaterThan( 40, count( $texts ) );
        foreach ( array( 'Reveal %name', 'What this touches', 'How to run it', 'This job cannot be resumed.', 'Node %id' ) as $known )
            $this->assertArrayHasKey( $known, $texts, 'the extraction finds ' . $known );
        $german = self::messages( 'ger-DE', self::CONTEXT );
        $english = self::messages( 'eng-US', self::CONTEXT );
        foreach ( $texts as $text => $file )
        {
            $this->assertNotSame( '', isset( $german[$text] ) ? trim( $german[$text] ) : '', "ger-DE: $text ($file)" );
            $this->assertArrayHasKey( $text, $english, "eng-US: $text ($file)" );
        }
    }

    /** JT-02 */
    public function testTheCachePageTitleIsGerman()
    {
        foreach ( array( 'kernel/setup', 'design/standard/setup' ) as $context )
            $this->assertSame( 'Cache-Verwaltung', self::messages( 'ger-DE', $context )['Cache admin'], $context );
    }

    /** JT-03 */
    public function testTheEditInMenusTakeTheContentLanguagesNames()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            foreach ( array( 'children_detailed.tpl', 'contentstructuremenu/content_structure_menu_dynamic.tpl' ) as $path )
            {
                $tpl = (string)file_get_contents( self::root() . "/design/$design/templates/$path" );
                $this->assertMatchesRegularExpression( "/\\{foreach fetch\\( 'content', 'prioritized_languages' \\) as \\\$language\\}\\{\\*\\s*\\*\\}['\"]\\{\\\$language\\.locale\\|wash\\(javascript\\)\\}['\"]: ?['\"]\\{\\\$language\\.name\\|wash\\(javascript\\)\\}/",
                                                       $tpl, "$design/$path" );
                $this->assertStringNotContainsString( 'intl_language_name', $tpl, "$design/$path" );
            }
        }
    }
}
