<?php
/**
 * Cuts a TrueType font down to the glyphs a PDF uses (doc/features/6.0/help-system.md, "Print and PDF").
 *
 * A composite font is embedded whole by the PDF library: DejaVu Sans is 740 kB, and a help page with bold and
 * italic text carried three of them (two megabytes for two pages of text). The glyph numbers stay the same (the
 * text and the width table address glyphs by number), so the outlines of the glyphs that are not used are simply
 * left empty: glyf holds only the used glyphs (and the parts of the composite ones), loca is rewritten to match,
 * and the tables a PDF viewer does not read for an embedded CID font (layout, kerning, bitmaps, glyph names) are
 * left out. Anything unexpected returns the font unchanged, so the worst case is the old, larger file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpPdfFontSubset
{
    /** The tables kept; the rest are not used by a PDF viewer drawing an embedded CIDFontType2 */
    const KEEP = array( 'cmap', 'cvt ', 'fpgm', 'glyf', 'head', 'hhea', 'hmtx', 'loca', 'maxp', 'name', 'OS/2', 'post', 'prep' );

    /**
     * The font with only the given glyphs' outlines.
     *
     * @param string $data the TrueType file
     * @param int[] $glyphs glyph ids in use (glyph 0 is always kept)
     * @return string the smaller font, or $data unchanged when it cannot be subset
     */
    public static function subset( $data, array $glyphs )
    {
        try
        {
            $result = self::build( (string)$data, $glyphs );
        }
        catch ( Exception $e )
        {
            $result = null;
        }
        return $result === null ? (string)$data : $result;
    }

    protected static function build( $data, array $glyphs )
    {
        if ( strlen( $data ) < 12 || !in_array( substr( $data, 0, 4 ), array( "\x00\x01\x00\x00", 'true' ), true ) )
            return null;
        $tables = array();
        $count = self::u16( $data, 4 );
        for ( $i = 0; $i < $count; $i++ )
        {
            $entry = 12 + 16 * $i;
            if ( $entry + 16 > strlen( $data ) )
                return null;
            $offset = self::u32( $data, $entry + 8 );
            $length = self::u32( $data, $entry + 12 );
            if ( $offset + $length > strlen( $data ) )
                return null;
            $tables[substr( $data, $entry, 4 )] = substr( $data, $offset, $length );
        }
        foreach ( array( 'head', 'maxp', 'loca', 'glyf', 'hmtx', 'hhea', 'cmap' ) as $required )
        {
            if ( !isset( $tables[$required] ) )
                return null;
        }
        $numGlyphs = self::u16( $tables['maxp'], 4 );
        $long = self::u16( $tables['head'], 50 ) === 1;
        $loca = array();
        for ( $g = 0; $g <= $numGlyphs; $g++ )
        {
            $loca[$g] = $long ? self::u32( $tables['loca'], 4 * $g ) : 2 * self::u16( $tables['loca'], 2 * $g );
        }
        $glyf = $tables['glyf'];

        // the glyphs to keep, with the parts of composite glyphs
        $keep = array( 0 => true );
        $queue = array_map( 'intval', $glyphs );
        while ( $queue )
        {
            $g = array_pop( $queue );
            if ( $g < 0 || $g >= $numGlyphs || isset( $keep[$g] ) && $g !== 0 )
                continue;
            $keep[$g] = true;
            $start = $loca[$g];
            $end = $loca[$g + 1];
            if ( $end - $start < 10 || self::s16( $glyf, $start ) >= 0 )
                continue;
            // a composite glyph: its components
            $p = $start + 10;
            do
            {
                if ( $p + 4 > $end )
                    break;
                $flags = self::u16( $glyf, $p );
                $component = self::u16( $glyf, $p + 2 );
                if ( !isset( $keep[$component] ) )
                    $queue[] = $component;
                $p += 4 + ( $flags & 0x0001 ? 4 : 2 );
                if ( $flags & 0x0008 )
                    $p += 2;
                elseif ( $flags & 0x0040 )
                    $p += 4;
                elseif ( $flags & 0x0080 )
                    $p += 8;
            }
            while ( $flags & 0x0020 );
        }

        $newGlyf = '';
        $newLoca = '';
        for ( $g = 0; $g < $numGlyphs; $g++ )
        {
            $newLoca .= pack( 'N', strlen( $newGlyf ) );
            if ( isset( $keep[$g] ) && $loca[$g + 1] > $loca[$g] )
            {
                $newGlyf .= substr( $glyf, $loca[$g], $loca[$g + 1] - $loca[$g] );
                if ( strlen( $newGlyf ) % 4 )
                    $newGlyf .= str_repeat( "\0", 4 - strlen( $newGlyf ) % 4 );
            }
        }
        $newLoca .= pack( 'N', strlen( $newGlyf ) );

        $tables['glyf'] = $newGlyf;
        $tables['loca'] = $newLoca;
        // head: long offsets, the whole-file checksum left to the reader
        $tables['head'] = substr( $tables['head'], 0, 8 ) . "\0\0\0\0" . substr( $tables['head'], 12, 38 ) . pack( 'n', 1 ) . substr( $tables['head'], 52 );
        // post: format 3, no glyph names
        if ( isset( $tables['post'] ) && strlen( $tables['post'] ) >= 32 )
            $tables['post'] = "\x00\x03\x00\x00" . substr( $tables['post'], 4, 28 );

        $tables = array_intersect_key( $tables, array_flip( self::KEEP ) );
        ksort( $tables, SORT_STRING );
        return self::write( $tables );
    }

    protected static function write( array $tables )
    {
        $count = count( $tables );
        $power = 1;
        $log = 0;
        while ( $power * 2 <= $count )
        {
            $power *= 2;
            $log++;
        }
        $out = pack( 'Nnnnn', 0x00010000, $count, $power * 16, $log, $count * 16 - $power * 16 );
        $offset = 12 + 16 * $count;
        $body = '';
        foreach ( $tables as $tag => $table )
        {
            $length = strlen( $table );
            $padded = $table . str_repeat( "\0", ( 4 - $length % 4 ) % 4 );
            $out .= $tag . pack( 'NNN', self::checksum( $padded ), $offset + strlen( $body ), $length );
            $body .= $padded;
        }
        return $out . $body;
    }

    protected static function checksum( $data )
    {
        $sum = 0;
        foreach ( unpack( 'N*', $data ) as $word )
            $sum = ( $sum + $word ) & 0xFFFFFFFF;
        return $sum;
    }

    protected static function u16( $data, $offset )
    {
        if ( $offset + 2 > strlen( $data ) )
            throw new Exception( 'Read past the end of a font table' );
        $v = unpack( 'n', substr( $data, $offset, 2 ) );
        return $v[1];
    }

    protected static function s16( $data, $offset )
    {
        $v = self::u16( $data, $offset );
        return $v >= 0x8000 ? $v - 0x10000 : $v;
    }

    protected static function u32( $data, $offset )
    {
        if ( $offset + 4 > strlen( $data ) )
            throw new Exception( 'Read past the end of a font table' );
        $v = unpack( 'N', substr( $data, $offset, 4 ) );
        return $v[1];
    }
}

?>
