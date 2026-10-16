<?php
/**
 * File containing the expTranslationQualityTest class
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * The translations of the administration that are kept complete (Spanish of Spain and of Mexico, Hebrew, Latin and
 * Klingon, the kernel's catalogue and those of ezoe and ezjscore, which ship with it) must stay usable: a finished translation keeps the
 * placeholders (%1, %name ...), the HTML tags and entities, the leading and trailing whitespace and the number of
 * line breaks of its source, is not empty, and is not the English text itself unless that is right in the language
 * (a product name, a code, a word spelled the same: translation-identical-allowlist.json, each entry with its
 * reason). A broken placeholder shows "%count" to the user or loses a value; a lost tag breaks the page.
 *
 * DB-less: the files are read with DOMDocument.
 */
class expTranslationQualityTest extends PHPUnit\Framework\TestCase
{
    const LANGUAGES = array( 'esl-ES', 'esl-MX', 'heb-IL', 'lat-VA', 'tlh-QO' );

    const TAG = '/<[ \t\n\r]*\/?[ \t\n\r]*[A-Za-z][A-Za-z0-9]*(?:[ \t\n\r][^<>]*)?\/?[ \t\n\r]*>/';
    const ENTITY = '/&(?:#[0-9]+|#x[0-9A-Fa-f]+|[A-Za-z][A-Za-z0-9]*);/';
    const LEADING = '/^[ \t\n\r\f\v]*/';
    const TRAILING = '/[ \t\n\r\f\v]*\z/';

    private static $allowlist;

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    /**
     * The placeholder pattern: %1, %name, and %name% for the allowlist's closed_placeholders.
     */
    public static function placeholderPattern()
    {
        static $pattern;
        if ( $pattern === null )
        {
            $allow = self::allowlist();
            $closed = isset( $allow['closed_placeholders'] ) ? array_keys( $allow['closed_placeholders'] ) : array();
            usort( $closed, function ( $a, $b ) { return strlen( $b ) - strlen( $a ); } );
            $alt = '';
            foreach ( $closed as $name )
                $alt .= preg_quote( $name, '/' ) . '%|';
            $pattern = '/%(?:' . $alt . '[0-9]+|[A-Za-z_][A-Za-z0-9_]*)/';
        }
        return $pattern;
    }

    public static function allowlist()
    {
        if ( self::$allowlist === null )
            self::$allowlist = json_decode( file_get_contents( __DIR__ . '/translation-identical-allowlist.json' ), true );
        return self::$allowlist;
    }

    private static function all( $pattern, $text )
    {
        preg_match_all( $pattern, $text, $m );
        $list = $m[0];
        sort( $list );
        return $list;
    }

    private static function edge( $pattern, $text )
    {
        preg_match( $pattern, $text, $m );
        return $m[0];
    }

    /**
     * The sorted placeholders of a text: %1, %name and %name% (closed by a percent sign). A placeholder the code
     * replaces although a word follows it without a space (the allowlist's "glued_placeholders", e.g.
     * "%login_link_startLog in") counts as that placeholder alone.
     */
    public static function placeholders( $text )
    {
        $allow = self::allowlist();
        $glued = isset( $allow['glued_placeholders'] ) ? array_keys( $allow['glued_placeholders'] ) : array();
        $out = array();
        foreach ( self::all( self::placeholderPattern(), $text ) as $p )
        {
            if ( substr( $p, -1 ) !== '%' )
            {
                foreach ( $glued as $g )
                {
                    if ( $p !== $g && strpos( $p, $g ) === 0 )
                    {
                        $p = $g;
                        break;
                    }
                }
            }
            $out[] = $p;
        }
        sort( $out );
        return $out;
    }

    /**
     * May the translation of $source be the English text itself in $language?
     */
    public static function mayStayIdentical( $source, $language )
    {
        $rest = preg_replace( array( self::placeholderPattern(), self::TAG, self::ENTITY ), '', $source );
        if ( !preg_match( '/[A-Za-z]/', $rest ) )
            return true;
        $allow = self::allowlist();
        $source = trim( $source );
        return isset( $allow['*'][$source] ) || isset( $allow[$language][$source] );
    }

    /**
     * The rules a finished translation breaks; an empty array when it passes.
     */
    public static function problems( $source, $translation, $language )
    {
        if ( trim( $translation ) === '' )
            return array( 'empty' );
        $out = array();
        if ( self::placeholders( $source ) !== self::placeholders( $translation ) )
            $out[] = 'placeholders ' . implode( ' ', self::placeholders( $source ) ) . ' != ' . implode( ' ', self::placeholders( $translation ) );
        foreach ( array( 'tags' => self::TAG, 'entities' => self::ENTITY ) as $what => $pattern )
        {
            if ( self::all( $pattern, $source ) !== self::all( $pattern, $translation ) )
                $out[] = $what . ' ' . implode( ' ', self::all( $pattern, $source ) ) . ' != ' . implode( ' ', self::all( $pattern, $translation ) );
        }
        if ( self::edge( self::LEADING, $source ) !== self::edge( self::LEADING, $translation ) )
            $out[] = 'leading whitespace';
        if ( self::edge( self::TRAILING, $source ) !== self::edge( self::TRAILING, $translation ) )
            $out[] = 'trailing whitespace';
        if ( substr_count( $source, "\n" ) !== substr_count( $translation, "\n" ) )
            $out[] = 'line breaks';
        if ( $translation === $source && !self::mayStayIdentical( $source, $language ) )
            $out[] = 'identical to the English source';
        return $out;
    }

    public static function catalogueProvider()
    {
        $root = self::root();
        $rows = array();
        foreach ( self::LANGUAGES as $language )
        {
            $files = array_merge(
                glob( $root . '/share/translations/' . $language . '/translation.ts' ),
                glob( $root . '/extension/{ezoe,ezjscore}/translations/' . $language . '/translation.ts', GLOB_BRACE )
            );
            foreach ( $files as $file )
                $rows[substr( $file, strlen( $root ) + 1 )] = array( $file, $language );
        }
        return $rows;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('catalogueProvider')]
    public function testCatalogueValidatesAndHasNoDuplicate( $file, $language )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $this->assertTrue( @$doc->load( $file ), "$file is XML" );
        $this->assertTrue( @eZTSTranslator::validateDOMTree( $doc ), "$file validates against ts.rng" );
        $seen = array();
        $duplicates = array();
        foreach ( $doc->getElementsByTagName( 'context' ) as $context )
        {
            $name = $context->getElementsByTagName( 'name' )->item( 0 )->textContent;
            foreach ( $context->getElementsByTagName( 'message' ) as $message )
            {
                $comment = $message->getElementsByTagName( 'comment' )->item( 0 );
                $key = $name . "\0" . $message->getElementsByTagName( 'source' )->item( 0 )->textContent . "\0" . ( $comment ? $comment->textContent : '' );
                if ( isset( $seen[$key] ) )
                    $duplicates[] = str_replace( "\0", ' | ', $key );
                $seen[$key] = true;
            }
        }
        $this->assertSame( array(), $duplicates, "$file: messages that appear more than once" );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('catalogueProvider')]
    public function testFinishedTranslationsKeepTheirSourcesMarkup( $file, $language )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $this->assertTrue( @$doc->load( $file ), "$file is XML" );
        $broken = array();
        foreach ( $doc->getElementsByTagName( 'message' ) as $message )
        {
            $translation = $message->getElementsByTagName( 'translation' )->item( 0 );
            if ( !$translation || $translation->getAttribute( 'type' ) !== '' )
                continue;
            $source = $message->getElementsByTagName( 'source' )->item( 0 )->textContent;
            $problems = self::problems( $source, $translation->textContent, $language );
            if ( $problems )
                $broken[] = mb_substr( $source, 0, 60 ) . ': ' . implode( '; ', $problems );
        }
        $this->assertSame( array(), array_slice( $broken, 0, 20 ), "$file: " . count( $broken ) . ' finished translations break the rules (first 20)' );
    }

    public function testAllowlistGivesAReasonForEachEntry()
    {
        $allow = self::allowlist();
        $this->assertIsArray( $allow, 'translation-identical-allowlist.json is JSON' );
        foreach ( array_merge( array( '*' ), self::LANGUAGES ) as $list )
        {
            $this->assertArrayHasKey( $list, $allow );
            foreach ( $allow[$list] as $source => $reason )
                $this->assertTrue( is_string( $reason ) && trim( $reason ) !== '', "$list: '$source' has a reason" );
        }
    }

    public static function ruleProvider()
    {
        return array(
            'passes' => array( 'Remove %count items', 'Eliminar %count elementos', array() ),
            'lost placeholder' => array( 'Remove %count items', 'Eliminar elementos', array( 'placeholders' ) ),
            'placeholder glued to a word' => array( 'Remove %count items', "Ha'DIbaH %countDaq", array( 'placeholders' ) ),
            'numbered placeholders before letters' => array( 'Try %1Advanced search%2.', 'Prueba %1Búsqueda avanzada%2.', array() ),
            'placeholder closed by a percent sign' => array( '%login_link_start%here%login_link_end%', '%login_link_start%aquí%login_link_end%', array() ),
            'glued placeholder of the allowlist' => array( '%login_link_startLog in%login_link_end', '%login_link_startConectarse%login_link_end', array() ),
            'lost tag' => array( '<b>%file</b> uploaded', '%file subido', array( 'tags' ) ),
            'lost entity' => array( 'a&nbsp;b', 'a b', array( 'entities' ) ),
            'lost trailing space' => array( 'Name: ', 'Nombre:', array( 'trailing whitespace' ) ),
            'lost line break' => array( "One\nTwo", 'Uno dos', array( 'line breaks' ) ),
            'empty' => array( 'Edit', ' ', array( 'empty' ) ),
            'identical' => array( 'Edit', 'Edit', array( 'identical to the English source' ) ),
            'identical product name' => array( 'Exponential', 'Exponential', array() ),
            'identical without letters' => array( '%1 / %2', '%1 / %2', array() ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('ruleProvider')]
    public function testRules( $source, $translation, $expected )
    {
        $got = array_map( function ( $p ) { return preg_replace( '/^(placeholders|tags|entities) .*/', '$1', $p ); }, self::problems( $source, $translation, 'esl-ES' ) );
        $this->assertSame( $expected, $got );
    }
}
