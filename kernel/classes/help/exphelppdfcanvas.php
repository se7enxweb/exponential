<?php
/**
 * The page surface of a help page's PDF (doc/eng-us/features/6.0/help-system.md, "Print and PDF"): the kernel's own PDF
 * library (lib/ezpdf, Cpdf) with the few changes expHelpPdfWriter needs.
 *
 * - Text is drawn as it is. Cpdf reads <b>, <i> and <c:...> inside a text as font directives; a help page that
 *   explains HTML has those as words, so no directive is read here (the writer picks the faces itself).
 * - Images come from GD and are written as JPEG without a temporary file (Cpdf::addImage() writes to /tmp).
 * - The document information (title, subject) is written as UTF-16 when it is not plain ASCII, so a title in any
 *   script shows correctly in a viewer's title bar and document properties.
 * - A carriage return inside a string is escaped (Cpdf left it raw, which a reader takes for a line end).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpPdfCanvas extends Cpdf
{
    /**
     * No directives: every character of a text is drawn.
     */
    function PRVTcheckTextDirective( &$text, $i, &$f, $final = 0 )
    {
        return array( 'directive' => 0, 'y' => 0.0, 'noClose' => 0, 'run_final' => false );
    }

    function PRVTcheckTextDirective1( &$text, $i, &$f, $final, &$x, &$y, $size = 0, $angle = 0, $wordSpaceAdjust = 0 )
    {
        return array( 'directive' => 0, 'y' => 0.0, 'noClose' => 0, 'run_final' => false );
    }

    /**
     * The embedded font file, cut down to the glyphs the document uses (expHelpPdfFontSubset).
     */
    function o_fontFile2( $id, $action, $options = '' )
    {
        if ( $action !== 'out' || !isset( $this->objects[$id]['info']['fontKey'] ) )
            return parent::o_fontFile2( $id, $action, $options );
        $font = $this->fonts[$this->objects[$id]['info']['fontKey']];
        $data = expHelpPdfFontSubset::subset( $font['ttf']->fontData(), array_keys( $font['usedGlyphs'] ) );
        $original = strlen( $data );
        $filter = '';
        if ( function_exists( 'gzcompress' ) )
        {
            $compressed = gzcompress( $data, 6 );
            if ( $compressed !== false && strlen( $compressed ) < $original )
            {
                $data = $compressed;
                $filter = "/Filter /FlateDecode\n";
            }
        }
        return "\n" . $id . " 0 obj\n<< " . $filter . '/Length ' . strlen( $data ) . "\n/Length1 " . $original . "\n>>\nstream\n"
            . $data . "\nendstream\nendobj";
    }

    function filterText( $text )
    {
        return str_replace( "\r", '\\r', parent::filterText( $text ) );
    }

    /**
     * A document information entry (Title, Subject, Author, Creator ...), UTF-16 when it is not ASCII.
     *
     * @param string $label
     * @param string $value UTF-8
     */
    public function addUnicodeInfo( $label, $value )
    {
        $value = (string)$value;
        if ( preg_match( '/[^\x20-\x7E]/', $value ) === 1 && function_exists( 'mb_convert_encoding' ) )
            $value = "\xFE\xFF" . mb_convert_encoding( $value, 'UTF-16BE', 'UTF-8' );
        $this->addInfo( $label, $value );
    }

    /**
     * Draws a GD image as a JPEG at x, y (its lower left corner), w by h points.
     *
     * @param resource|\GdImage $image
     * @param float $x
     * @param float $y
     * @param float $w
     * @param float $h
     * @param int $quality
     * @return bool
     */
    public function addGdImage( $image, $x, $y, $w, $h, $quality = 82 )
    {
        $width = imagesx( $image );
        $height = imagesy( $image );
        if ( $width < 1 || $height < 1 )
            return false;
        ob_start();
        $ok = imagejpeg( $image, null, $quality );
        $data = (string)ob_get_clean();
        if ( !$ok || $data === '' )
            return false;
        $this->addJpegImage_common( $data, $x, $y, $w, $h, $width, $height );
        return true;
    }

    /**
     * Whether the TrueType font registered under $path has a glyph for $codePoint.
     *
     * @param string $path
     * @param int $codePoint
     * @return bool
     */
    public function fontHasGlyph( $path, $codePoint )
    {
        if ( !isset( $this->fonts[$path]['ttf'] ) )
            return false;
        return $this->fonts[$path]['ttf']->glyphIndex( $codePoint ) > 0;
    }

    /**
     * Whether a font is loaded under this name.
     *
     * @param string $name
     * @return bool
     */
    public function hasFont( $name )
    {
        return isset( $this->fonts[$name] );
    }

    /**
     * The number of pages so far.
     *
     * @return int
     */
    public function pageCount()
    {
        return (int)$this->numPages;
    }
}

?>
