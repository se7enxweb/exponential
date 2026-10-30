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
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

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
        $files = array_merge( AdminFiles::files( self::root() . '/kernel/private/classes/views/content/*.php' ),
                              AdminFiles::files( self::root() . '/kernel/classes/contentjob/*.php' ) );
        foreach ( $files as $file )
        {
            $code = AdminFiles::read( $file );
            $tr = '(?:\\\\?ezpI18n::tr\(\s*\'' . preg_quote( self::CONTEXT, '/' ) . '\'\s*,|\$tr\()';
            // a call with the text, or with a choice of two texts ($a ? 'x' : 'y')
            preg_match_all( '/' . $tr . '\s*(?:\$\w+\s*\?\s*)?\'((?:[^\'\\\\]|\\\\.)*)\'(?:\s*:\s*\'((?:[^\'\\\\]|\\\\.)*)\')?/', $code, $m, PREG_SET_ORDER );
            foreach ( $m as $match )
                foreach ( array_slice( $match, 1 ) as $text )
                    if ( $text !== '' )
                        $texts[stripslashes( $text )] = basename( $file );
        }
        foreach ( AdminFiles::files( self::root() . '/design/admin4/templates/content/job*.tpl' ) as $file )
        {
            preg_match_all( '/\'((?:[^\'\\\\]|\\\\.)*)\'\|i18n\(\s*\'' . preg_quote( self::CONTEXT, '/' ) . '\'/', AdminFiles::read( $file ), $m );
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
            // the tree takes them as JSON (data-tree-params, no inline script), and so does admin4's sub-items table
            // (data-conf); the classic design's sub-items table as a script value
            $patterns = array(
                'children_detailed.tpl' => $design === 'admin4'
                    ? "/\\{foreach fetch\\( 'content', 'prioritized_languages' \\) as \\\$language\\}\\{set \\\$subitems_languages = \\\$subitems_languages\\|merge\\( hash\\( \\\$language\\.locale, \\\$language\\.name \\) \\)\\}/"
                    : "/\\{foreach fetch\\( 'content', 'prioritized_languages' \\) as \\\$language\\}\\{\\*\\s*\\*\\}['\"]\\{\\\$language\\.locale\\|wash\\(javascript\\)\\}['\"]: ?['\"]\\{\\\$language\\.name\\|wash\\(javascript\\)\\}/",
                'contentstructuremenu/content_structure_menu_dynamic.tpl' => "/\\{foreach fetch\\( 'content', 'prioritized_languages' \\) as \\\$language\\}\\{set \\\$tree_languages = \\\$tree_languages\\|merge\\( hash\\( \\\$language\\.locale, \\\$language\\.name \\) \\)\\}/",
            );
            foreach ( $patterns as $path => $pattern )
            {
                $tpl = AdminFiles::read( self::root() . "/design/$design/templates/$path" );
                $this->assertMatchesRegularExpression( $pattern, $tpl, "$design/$path" );
                $this->assertStringNotContainsString( 'intl_language_name', $tpl, "$design/$path" );
            }
        }
    }
}
