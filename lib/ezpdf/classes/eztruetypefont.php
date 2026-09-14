<?php
/**
 * File containing the eZPDFTrueTypeFont class.
 *
 * Reads a TrueType font well enough to embed it in a pdf as a composite font:
 * the character to glyph mapping, the advance widths, the descriptor the pdf
 * needs, and the file itself.
 *
 * The library this sits beside can only write simple fonts - one font, 256
 * slots, one byte per character - so text has always had to be squeezed into a
 * single byte charset before it could be drawn, and anything that did not fit
 * became a question mark. Reading the font's own tables is what makes a
 * composite font possible, and with it any character the font actually has.
 *
 * Only the tables needed for that are read. Hinting, kerning, ligatures and
 * the outlines themselves are left alone: the whole font file is embedded and
 * the viewer uses them.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package lib
 */

class eZPDFTrueTypeFont
{
    /** Raw font file. */
    private $data;

    /** tag => array( offset, length ) */
    private $tables = array();

    private $unitsPerEm = 1000;
    private $numGlyphs = 0;
    private $numberOfHMetrics = 0;

    /** Unicode code point => glyph id, filled as it is asked for. */
    private $glyphCache = array();

    /** Glyph id => advance width in 1000ths of an em. */
    private $widthCache = array();

    /** cmap subtable offsets, best first. */
    private $cmapTables = array();

    private $postScriptName = '';

    /**
     * @param string $path Path to a .ttf file.
     * @throws Exception when the file cannot be read or is not TrueType.
     */
    public function __construct( $path )
    {
        if ( !file_exists( $path ) || !is_readable( $path ) )
            throw new Exception( "TrueType font not readable: $path" );

        $this->data = (string)file_get_contents( $path );
        if ( strlen( $this->data ) < 12 )
            throw new Exception( "TrueType font is too short to be one: $path" );

        $version = substr( $this->data, 0, 4 );
        // 0x00010000 for TrueType outlines, 'true' on older Apple fonts.
        // 'OTTO' is CFF outlines, which cannot be embedded as FontFile2.
        if ( $version === 'OTTO' )
            throw new Exception( "$path has CFF outlines; embed a TrueType outline font instead." );
        if ( $version !== "\x00\x01\x00\x00" && $version !== 'true' && $version !== 'ttcf' )
            throw new Exception( "$path is not a TrueType font." );

        $this->readTableDirectory();
        $this->readHead();
        $this->readMaxp();
        $this->readHhea();
        $this->readCmapDirectory();
        $this->readName();
    }

    private function ushort( $offset )
    {
        $v = unpack( 'n', substr( $this->data, $offset, 2 ) );
        return $v[1];
    }

    private function short( $offset )
    {
        $v = $this->ushort( $offset );
        return $v >= 0x8000 ? $v - 0x10000 : $v;
    }

    private function ulong( $offset )
    {
        $v = unpack( 'N', substr( $this->data, $offset, 4 ) );
        return $v[1];
    }

    private function readTableDirectory()
    {
        $numTables = $this->ushort( 4 );
        for ( $i = 0; $i < $numTables; $i++ )
        {
            $entry = 12 + $i * 16;
            if ( $entry + 16 > strlen( $this->data ) )
                break;
            $tag = substr( $this->data, $entry, 4 );
            $this->tables[$tag] = array( 'offset' => $this->ulong( $entry + 8 ),
                                         'length' => $this->ulong( $entry + 12 ) );
        }

        foreach ( array( 'head', 'maxp', 'hhea', 'hmtx', 'cmap' ) as $required )
        {
            if ( !isset( $this->tables[$required] ) )
                throw new Exception( "TrueType font has no $required table." );
        }
    }

    private function table( $tag )
    {
        return isset( $this->tables[$tag] ) ? $this->tables[$tag]['offset'] : false;
    }

    private function readHead()
    {
        $head = $this->table( 'head' );
        $this->unitsPerEm = $this->ushort( $head + 18 );
        if ( $this->unitsPerEm < 16 )
            $this->unitsPerEm = 1000;
    }

    private function readMaxp()
    {
        $this->numGlyphs = $this->ushort( $this->table( 'maxp' ) + 4 );
    }

    private function readHhea()
    {
        $this->numberOfHMetrics = $this->ushort( $this->table( 'hhea' ) + 34 );
    }

    /**
     * Records the cmap subtables that can map unicode, best first.
     *
     * Format 12 is preferred because it reaches beyond the basic multilingual
     * plane; format 4 covers the plane and is what most fonts carry.
     */
    private function readCmapDirectory()
    {
        $cmap = $this->table( 'cmap' );
        $count = $this->ushort( $cmap + 2 );

        $format12 = array();
        $format4 = array();
        for ( $i = 0; $i < $count; $i++ )
        {
            $entry = $cmap + 4 + $i * 8;
            $platform = $this->ushort( $entry );
            $encoding = $this->ushort( $entry + 2 );
            $offset = $cmap + $this->ulong( $entry + 4 );
            $format = $this->ushort( $offset );

            $isUnicode = ( $platform === 0 )                        // unicode
                      || ( $platform === 3 && $encoding === 1 )     // windows BMP
                      || ( $platform === 3 && $encoding === 10 );   // windows full
            if ( !$isUnicode )
                continue;

            if ( $format === 12 )
                $format12[] = $offset;
            else if ( $format === 4 )
                $format4[] = $offset;
        }

        $this->cmapTables = array_merge( $format12, $format4 );
        if ( !$this->cmapTables )
            throw new Exception( 'TrueType font has no unicode cmap subtable.' );
    }

    private function readName()
    {
        $name = $this->table( 'name' );
        if ( $name === false )
            return;

        $count = $this->ushort( $name + 2 );
        $storage = $name + $this->ushort( $name + 4 );
        for ( $i = 0; $i < $count; $i++ )
        {
            $entry = $name + 6 + $i * 12;
            $nameID = $this->ushort( $entry + 6 );
            if ( $nameID !== 6 )   // postscript name
                continue;

            $platform = $this->ushort( $entry );
            $length = $this->ushort( $entry + 8 );
            $offset = $storage + $this->ushort( $entry + 10 );
            $value = substr( $this->data, $offset, $length );

            // Platform 0 and 3 store it as utf-16be; platform 1 as macroman.
            if ( $platform === 0 || $platform === 3 )
                $value = @iconv( 'UTF-16BE', 'ASCII//TRANSLIT//IGNORE', $value );

            $value = preg_replace( '/[^\x21-\x7e]/', '', (string)$value );
            if ( $value !== '' )
            {
                $this->postScriptName = $value;
                return;
            }
        }
    }

    /**
     * The glyph for a unicode code point, 0 when the font has none.
     *
     * @param int $codePoint
     * @return int
     */
    public function glyphIndex( $codePoint )
    {
        if ( isset( $this->glyphCache[$codePoint] ) )
            return $this->glyphCache[$codePoint];

        $glyph = 0;
        foreach ( $this->cmapTables as $offset )
        {
            $format = $this->ushort( $offset );
            $glyph = $format === 12
                   ? $this->lookupFormat12( $offset, $codePoint )
                   : $this->lookupFormat4( $offset, $codePoint );
            if ( $glyph )
                break;
        }

        $this->glyphCache[$codePoint] = $glyph;
        return $glyph;
    }

    private function lookupFormat4( $offset, $codePoint )
    {
        if ( $codePoint > 0xFFFF )
            return 0;

        $segCountX2 = $this->ushort( $offset + 6 );
        $segCount = $segCountX2 >> 1;

        $endCodes = $offset + 14;
        $startCodes = $endCodes + $segCountX2 + 2;
        $idDeltas = $startCodes + $segCountX2;
        $idRangeOffsets = $idDeltas + $segCountX2;

        // The segments are sorted, so the one that matters is the first whose
        // end is at or past the character.
        $low = 0;
        $high = $segCount - 1;
        $segment = -1;
        while ( $low <= $high )
        {
            $middle = ( $low + $high ) >> 1;
            if ( $this->ushort( $endCodes + $middle * 2 ) < $codePoint )
                $low = $middle + 1;
            else
            {
                $segment = $middle;
                $high = $middle - 1;
            }
        }
        if ( $segment < 0 )
            return 0;

        $start = $this->ushort( $startCodes + $segment * 2 );
        if ( $start > $codePoint )
            return 0;

        $idRangeOffset = $this->ushort( $idRangeOffsets + $segment * 2 );
        $idDelta = $this->ushort( $idDeltas + $segment * 2 );

        if ( $idRangeOffset === 0 )
            return ( $codePoint + $idDelta ) & 0xFFFF;

        $glyphOffset = $idRangeOffsets + $segment * 2 + $idRangeOffset + ( $codePoint - $start ) * 2;
        if ( $glyphOffset + 2 > strlen( $this->data ) )
            return 0;

        $glyph = $this->ushort( $glyphOffset );
        return $glyph === 0 ? 0 : ( ( $glyph + $idDelta ) & 0xFFFF );
    }

    private function lookupFormat12( $offset, $codePoint )
    {
        $nGroups = $this->ulong( $offset + 12 );
        $groups = $offset + 16;

        $low = 0;
        $high = $nGroups - 1;
        while ( $low <= $high )
        {
            $middle = ( $low + $high ) >> 1;
            $group = $groups + $middle * 12;
            $startChar = $this->ulong( $group );
            $endChar = $this->ulong( $group + 4 );

            if ( $codePoint < $startChar )
                $high = $middle - 1;
            else if ( $codePoint > $endChar )
                $low = $middle + 1;
            else
                return $this->ulong( $group + 8 ) + ( $codePoint - $startChar );
        }

        return 0;
    }

    /**
     * Advance width of a glyph, in the 1000ths of an em that pdf works in.
     *
     * @param int $glyph
     * @return int
     */
    public function glyphWidth( $glyph )
    {
        if ( isset( $this->widthCache[$glyph] ) )
            return $this->widthCache[$glyph];

        $hmtx = $this->table( 'hmtx' );
        // Past the last full metric every glyph carries the last advance; only
        // the left side bearings continue.
        $index = $glyph < $this->numberOfHMetrics ? $glyph : $this->numberOfHMetrics - 1;
        $offset = $hmtx + $index * 4;

        $width = $offset + 2 <= strlen( $this->data ) ? $this->ushort( $offset ) : 0;
        $scaled = (int)round( $width * 1000 / $this->unitsPerEm );

        $this->widthCache[$glyph] = $scaled;
        return $scaled;
    }

    /**
     * Advance width of a code point, in 1000ths of an em.
     */
    public function codePointWidth( $codePoint )
    {
        return $this->glyphWidth( $this->glyphIndex( $codePoint ) );
    }

    /**
     * What the pdf font descriptor needs, all scaled to 1000ths of an em.
     *
     * @return array
     */
    public function descriptor()
    {
        $head = $this->table( 'head' );
        $hhea = $this->table( 'hhea' );
        $os2 = $this->table( 'OS/2' );
        $post = $this->table( 'post' );

        $scale = 1000 / $this->unitsPerEm;

        $descriptor = array(
            'FontBBox' => array( (int)round( $this->short( $head + 36 ) * $scale ),
                                 (int)round( $this->short( $head + 38 ) * $scale ),
                                 (int)round( $this->short( $head + 40 ) * $scale ),
                                 (int)round( $this->short( $head + 42 ) * $scale ) ),
            'Ascent'   => (int)round( $this->short( $hhea + 4 ) * $scale ),
            'Descent'  => (int)round( $this->short( $hhea + 6 ) * $scale ),
            'ItalicAngle' => 0,
            'CapHeight'   => 0,
            'StemV'       => 80,
            // Nonsymbolic: the font is used with a standard latin-ish
            // character set rather than its own private one.
            'Flags'       => 32 );

        if ( $post !== false )
        {
            // Fixed 16.16, so the whole part is the top two bytes.
            $descriptor['ItalicAngle'] = $this->short( $post + 4 );
        }

        if ( $os2 !== false )
        {
            $version = $this->ushort( $os2 );
            if ( $version >= 2 )
                $descriptor['CapHeight'] = (int)round( $this->short( $os2 + 88 ) * $scale );

            // StemV is not in the tables; derived from the weight the way most
            // producers do it, which is good enough for a viewer's fallback.
            $weight = $this->ushort( $os2 + 4 );
            $descriptor['StemV'] = (int)round( 50 + pow( $weight / 65.0, 2 ) );

            $selection = $this->ushort( $os2 + 62 );
            if ( $selection & 1 )   // italic
                $descriptor['Flags'] |= 64;
        }

        if ( !$descriptor['CapHeight'] )
            $descriptor['CapHeight'] = $descriptor['Ascent'];

        return $descriptor;
    }

    public function postScriptName()
    {
        return $this->postScriptName !== '' ? $this->postScriptName : 'EmbeddedFont';
    }

    public function numGlyphs()
    {
        return $this->numGlyphs;
    }

    public function unitsPerEm()
    {
        return $this->unitsPerEm;
    }

    /** The font file itself, for FontFile2. */
    public function fontData()
    {
        return $this->data;
    }

    /**
     * Splits a utf-8 string into code points.
     *
     * @param string $text
     * @return array
     */
    public static function codePoints( $text )
    {
        $points = array();
        $length = strlen( $text );
        for ( $i = 0; $i < $length; )
        {
            $byte = ord( $text[$i] );
            if ( $byte < 0x80 )          { $points[] = $byte; $i += 1; continue; }
            if ( ( $byte & 0xE0 ) === 0xC0 && $i + 1 < $length )
            {
                $points[] = ( ( $byte & 0x1F ) << 6 ) | ( ord( $text[$i+1] ) & 0x3F );
                $i += 2; continue;
            }
            if ( ( $byte & 0xF0 ) === 0xE0 && $i + 2 < $length )
            {
                $points[] = ( ( $byte & 0x0F ) << 12 ) | ( ( ord( $text[$i+1] ) & 0x3F ) << 6 )
                          | ( ord( $text[$i+2] ) & 0x3F );
                $i += 3; continue;
            }
            if ( ( $byte & 0xF8 ) === 0xF0 && $i + 3 < $length )
            {
                $points[] = ( ( $byte & 0x07 ) << 18 ) | ( ( ord( $text[$i+1] ) & 0x3F ) << 12 )
                          | ( ( ord( $text[$i+2] ) & 0x3F ) << 6 ) | ( ord( $text[$i+3] ) & 0x3F );
                $i += 4; continue;
            }
            // Not valid utf-8; take the byte as latin-1 so nothing is lost.
            $points[] = $byte;
            $i += 1;
        }

        return $points;
    }
}

?>
