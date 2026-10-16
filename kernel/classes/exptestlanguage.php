<?php
/**
 * File containing the expTestLanguage class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Test languages: locales marked [LanguageSettings] TestLanguage=true in share/locale (the shipped one is Lorem Ipsum,
 * qlo-ZZ), and the lorem ipsum text exp:loremipsum (bin/php/exploremipsumfill.php) writes into a translation in one,
 * per datatype. Reads files only; no database, no kernel bootstrap needed.
 *
 * Reference: doc/languages.md (test languages), guide: doc/guides/test-language-lorem-ipsum.md
 */
class expTestLanguage
{
    /** the test language Exponential ships */
    const LOREM_IPSUM = 'qlo-ZZ';

    const LOCALE_PATTERN = '/^[a-z]{3}-[A-Z]{2}$/';

    /** the datatypes that get lorem ipsum text; every other attribute keeps what the translation was copied from */
    const TEXT_DATATYPES = array( 'ezstring', 'eztext', 'ezxmltext', 'ezkeyword' );

    const WORDS = array( 'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'sed', 'do',
                         'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore', 'magna', 'aliqua', 'enim',
                         'ad', 'minim', 'veniam', 'quis', 'nostrud', 'exercitation', 'ullamco', 'laboris', 'nisi',
                         'aliquip', 'ex', 'ea', 'commodo', 'consequat', 'duis', 'aute', 'irure', 'in', 'reprehenderit',
                         'voluptate', 'velit', 'esse', 'cillum', 'fugiat', 'nulla', 'pariatur', 'excepteur', 'sint',
                         'occaecat', 'cupidatat', 'non', 'proident', 'sunt', 'culpa', 'qui', 'officia', 'deserunt',
                         'mollit', 'anim', 'id', 'est', 'laborum' );

    /**
     * Whether a locale file marks its language as a test language: TestLanguage=true in [LanguageSettings]. Read
     * line by line, as the INI says it; a missing or unreadable file is no test language.
     *
     * @param string $file
     * @return bool
     */
    public static function isTestLocaleFile( $file )
    {
        $lines = is_file( $file ) ? @file( $file, FILE_IGNORE_NEW_LINES ) : false;
        $group = '';
        foreach ( (array)$lines as $line )
        {
            $line = trim( (string)$line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            if ( preg_match( '/^\[([^\]]+)\]$/', $line, $match ) )
                $group = $match[1];
            elseif ( $group === 'LanguageSettings' && preg_match( '/^TestLanguage\s*=\s*(.*)$/', $line, $match ) )
                return strtolower( trim( $match[1] ) ) === 'true';
        }
        return false;
    }

    /**
     * Whether the locale with this code (no variant) is a test language of the installation.
     *
     * @param string $code
     * @param string $root the installation root; '' is the working directory
     * @return bool
     */
    public static function isTestLocale( $code, $root = '' )
    {
        if ( !is_string( $code ) || !preg_match( self::LOCALE_PATTERN, $code ) )
            return false;
        return self::isTestLocaleFile( self::path( $root, 'share/locale/' . $code . '.ini' ) );
    }

    /**
     * Every test language of share/locale, sorted by code.
     *
     * @param string $root
     * @return string[]
     */
    public static function testLocaleCodes( $root = '' )
    {
        $codes = array();
        foreach ( (array)glob( self::path( $root, 'share/locale/*.ini' ) ) as $file )
        {
            $code = basename( $file, '.ini' );
            if ( preg_match( self::LOCALE_PATTERN, $code ) && self::isTestLocaleFile( $file ) )
                $codes[] = $code;
        }
        sort( $codes, SORT_STRING );
        return $codes;
    }

    protected static function path( $root, $relative )
    {
        return ( (string)$root !== '' ? rtrim( (string)$root, '/' ) . '/' : '' ) . $relative;
    }

    // ---- lorem ipsum text --------------------------------------------------------------------------------------

    /**
     * $count words of lorem ipsum, the same for the same seed.
     *
     * @param int $count
     * @param string $seed
     * @return string[]
     */
    public static function words( $count, $seed )
    {
        $words = array();
        $total = count( self::WORDS );
        $start = crc32( (string)$seed ) % $total;
        for ( $i = 0; $i < max( 0, (int)$count ); $i++ )
            $words[] = self::WORDS[( $start + $i * 7 ) % $total];
        return $words;
    }

    /**
     * One sentence: capitalized, ending in a full stop.
     *
     * @param string $seed
     * @param int $count words
     * @return string
     */
    public static function sentence( $seed, $count = 9 )
    {
        return ucfirst( implode( ' ', self::words( $count, $seed ) ) ) . '.';
    }

    /**
     * A text line (ezstring, a title or name): "Lorem ipsum" and a few seeded words, title case, no full stop, at most
     * $maxLength characters when it is above 0.
     *
     * @param string $seed
     * @param int $maxLength
     * @return string
     */
    public static function textLine( $seed, $maxLength = 0 )
    {
        $line = 'Lorem ipsum ' . implode( ' ', self::words( 3, $seed ) );
        $line = ucwords( $line );
        if ( (int)$maxLength > 0 && strlen( $line ) > (int)$maxLength )
            $line = rtrim( substr( $line, 0, (int)$maxLength ) );
        return $line;
    }

    /**
     * Paragraphs of a few sentences each.
     *
     * @param string $seed
     * @param int $paragraphs
     * @return string[]
     */
    public static function paragraphs( $seed, $paragraphs = 2 )
    {
        $result = array();
        for ( $p = 0; $p < max( 1, (int)$paragraphs ); $p++ )
        {
            $sentences = array();
            for ( $s = 0; $s < 3; $s++ )
                $sentences[] = self::sentence( $seed . ':' . $p . ':' . $s, 8 + ( ( $p + $s ) % 5 ) );
            $result[] = implode( ' ', $sentences );
        }
        return $result;
    }

    /**
     * A text block (eztext): paragraphs separated by an empty line.
     *
     * @param string $seed
     * @param int $paragraphs
     * @return string
     */
    public static function textBlock( $seed, $paragraphs = 2 )
    {
        return implode( "\n\n", self::paragraphs( $seed, $paragraphs ) );
    }

    /**
     * Rich text (ezxmltext) as the datatype stores it: a heading and paragraphs, one word emphasized.
     *
     * @param string $seed
     * @param int $paragraphs
     * @return string
     */
    public static function xmlText( $seed, $paragraphs = 2 )
    {
        $esc = function ( $text ) { return htmlspecialchars( $text, ENT_XML1 | ENT_QUOTES, 'UTF-8' ); };
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n"
             . '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">'
             . '<section><header>' . $esc( self::textLine( $seed . ':h' ) ) . '</header>';
        foreach ( self::paragraphs( $seed, $paragraphs ) as $i => $paragraph )
        {
            if ( $i === 0 )
            {
                list( $first, $rest ) = array_pad( explode( ' ', $paragraph, 2 ), 2, '' );
                $xml .= '<paragraph><emphasize>' . $esc( $first ) . '</emphasize> ' . $esc( $rest ) . '</paragraph>';
            }
            else
                $xml .= '<paragraph>' . $esc( $paragraph ) . '</paragraph>';
        }
        return $xml . '</section></section>' . "\n";
    }

    /**
     * Keywords (ezkeyword) as its fromString() takes them: comma separated, distinct.
     *
     * @param string $seed
     * @param int $count
     * @return string
     */
    public static function keywords( $seed, $count = 4 )
    {
        $words = array_values( array_unique( array_merge( array( 'lorem ipsum' ), self::words( (int)$count + 3, $seed ) ) ) );
        return implode( ', ', array_slice( $words, 0, max( 1, (int)$count ) ) );
    }

    /**
     * The value for an attribute of a datatype, in the form its fromString() takes, or null for a datatype that keeps
     * the copied value (numbers, images, relations, ...).
     *
     * @param string $dataTypeString
     * @param string $seed object id and attribute identifier, so a run again writes the same text
     * @param int $maxLength for ezstring, the class attribute's maximum length (0: none)
     * @return string|null
     */
    public static function valueFor( $dataTypeString, $seed, $maxLength = 0 )
    {
        switch ( (string)$dataTypeString )
        {
            case 'ezstring':
                return self::textLine( $seed, $maxLength );
            case 'eztext':
                return self::textBlock( $seed );
            case 'ezxmltext':
                return self::xmlText( $seed );
            case 'ezkeyword':
                return self::keywords( $seed );
        }
        return null;
    }
}
