<?php
/**
 * The kernel's translation files load, and they carry every text the kernel's templates translate.
 *
 * A translation file that does not validate against schemas/translation/ts.rng is not loaded at all
 * (eZTSTranslator::validateDOMTree()): every text of that language is then missing, and with debug output on each
 * one is written to debug.log as "Missing translation", thousands of lines a minute.
 *
 *  TS-01 - eng-US, ger-DE and untranslated validate against the schema the translator uses
 *  TS-02 - every 'text'|i18n( 'design/...' ) of the templates under design/ is in eng-US, ger-DE and untranslated
 *
 * Run: php vendor/bin/phpunit tests/tests/design/TranslationFilesValidTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class TranslationFilesValidTest extends PHPUnit\Framework\TestCase
{
    const LOCALES = array( 'eng-US', 'ger-DE', 'untranslated' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function load( $locale )
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        self::assertTrue( $dom->load( self::root() . "/share/translations/$locale/translation.ts" ), "$locale parses" );
        return $dom;
    }

    public static function locales()
    {
        $cases = array();
        foreach ( self::LOCALES as $locale )
            $cases[$locale] = array( $locale );
        return $cases;
    }

    /**
     * TS-01
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'locales' )]
    public function testFileValidatesAgainstTheTranslatorSchema( $locale )
    {
        $dom = self::load( $locale );
        $previous = libxml_use_internal_errors( true );
        libxml_clear_errors();
        $valid = $dom->relaxNGValidate( self::root() . '/schemas/translation/ts.rng' );
        $errors = array();
        foreach ( array_slice( libxml_get_errors(), 0, 5 ) as $error )
            $errors[] = 'line ' . $error->line . ': ' . trim( $error->message );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        $this->assertTrue( $valid, "share/translations/$locale/translation.ts does not validate, so it is not loaded:\n" . implode( "\n", $errors ) );
    }

    /** context => source => template, of the templates under design/ (literal texts and contexts only) */
    private static function templateTexts()
    {
        static $texts = null;
        if ( $texts !== null )
            return $texts;
        $texts = array();
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/design', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
        {
            if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                continue;
            $code = file_get_contents( $file->getPathname() );
            if ( strpos( $code, 'i18n(' ) === false )
                continue;
            preg_match_all( "/'((?:[^'\\\\\\n]|\\\\.){0,400})'\\s*\\|\\s*i18n\\(\\s*'(design\\/[^'\\n]{1,200})'/", $code, $m, PREG_SET_ORDER );
            foreach ( $m as $match )
                // The text as the template engine reads it: \' is a quote and \} a closing brace (which would
                // otherwise end the tag), the way the translation file has them
                $texts[$match[2]][preg_replace( "/\\\\(['{}])/", '$1', $match[1] )] = substr( $file->getPathname(), strlen( self::root() ) + 1 );
        }
        return $texts;
    }

    /**
     * TS-02
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'locales' )]
    public function testEveryTemplateTextIsInTheFile( $locale )
    {
        $known = array();
        foreach ( ( new DOMXPath( self::load( $locale ) ) )->query( '//context' ) as $context )
        {
            $name = $context->getElementsByTagName( 'name' )->item( 0 )->textContent;
            foreach ( $context->getElementsByTagName( 'source' ) as $source )
                $known[$name][$source->textContent] = true;
        }
        $missing = array();
        foreach ( self::templateTexts() as $context => $sources )
            foreach ( $sources as $source => $template )
                if ( !isset( $known[$context][$source] ) )
                    $missing[] = "$context | $source | $template";
        $this->assertSame( array(), array_slice( $missing, 0, 20 ), count( $missing ) . " template texts are not in share/translations/$locale/translation.ts" );
    }
}
