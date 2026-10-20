<?php
/**
 * Lays a rendered help page out as a PDF (doc/en/us/features/6.0/help-system.md, "Print and PDF").
 *
 * Input is the safe HTML of expHelpMarkdownRenderer (the same markup the viewer shows), read with DOMDocument;
 * output is the PDF as a string, drawn on expHelpPdfCanvas (the kernel's own PDF library, lib/ezpdf). Nothing is
 * fetched from the network and no outside program runs.
 *
 * What it draws: a title block (title, summary, source file, last change, address), headings (with PDF
 * destinations, so the links of the page's contents work inside the file), paragraphs with bold, italic, inline
 * code and links (clickable), lists (nested, numbered, task lists), tables (column widths from their content, the
 * head row repeated on every page), code blocks (monospace, wrapped, coloured tokens for paper), callouts, quotes,
 * figures and images from the help (PNG, JPEG, GIF, WebP through GD; anything else is its alternative text),
 * rules, details and footnotes, and on every page a footer with the title and "Page n of N".
 *
 * Text: with a TrueType font (exphelp.ini [PdfSettings] FontFiles[] and the others, the first readable file of
 * each list) every character the font has is drawn, embedded as a composite font, and a fallback font
 * (FallbackFontFiles[], e.g. for Chinese, Japanese and Korean) takes the characters the main font lacks. Right to
 * left text (Hebrew, Arabic) is put in visual order per line and right to left paragraphs are right aligned;
 * Arabic letters are drawn in their isolated forms (no contextual shaping). Without any TrueType font the PDF
 * core fonts (Helvetica, Courier) are used and text is transliterated to Windows-1252.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpPdfWriter
{
    /** Bumped when the output changes, so cached files are made again */
    const VERSION = 1;

    /** Page sizes in points */
    const PAGE_SIZES = array( 'A4' => array( 595.28, 841.89 ), 'LETTER' => array( 612.0, 792.0 ) );

    /** Colours (RGB 0-1) for paper: always light, whatever the screen's theme */
    const COLORS = array(
        'ink' => array( 0.122, 0.141, 0.188 ),
        'muted' => array( 0.365, 0.396, 0.451 ),
        'line' => array( 0.788, 0.808, 0.839 ),
        'soft' => array( 0.965, 0.969, 0.976 ),
        'head' => array( 0.949, 0.957, 0.969 ),
        'link' => array( 0.678, 0.220, 0.035 ),
        'code' => array( 0.122, 0.141, 0.188 ),
        'quote' => array( 0.788, 0.808, 0.839 ),
    );

    /** Callout kind => array( bar/title colour, background ) */
    const CALLOUTS = array(
        'note' => array( array( 0.114, 0.306, 0.847 ), array( 0.937, 0.965, 1.0 ) ),
        'tip' => array( array( 0.086, 0.396, 0.204 ), array( 0.941, 0.992, 0.957 ) ),
        'important' => array( array( 0.427, 0.157, 0.851 ), array( 0.961, 0.953, 1.0 ) ),
        'warning' => array( array( 0.541, 0.294, 0.0 ), array( 1.0, 0.984, 0.922 ) ),
        'caution' => array( array( 0.725, 0.110, 0.110 ), array( 0.996, 0.949, 0.949 ) ),
    );

    /** Syntax token kind => colour on the light code background (all at least 4.5:1 on it) */
    const TOKENS = array(
        'comment' => array( 0.384, 0.416, 0.459 ), 'keyword' => array( 0.682, 0.122, 0.180 ),
        'string' => array( 0.016, 0.380, 0.200 ), 'number' => array( 0.0, 0.322, 0.627 ),
        'variable' => array( 0.639, 0.251, 0.0 ), 'function' => array( 0.384, 0.184, 0.667 ),
        'tag' => array( 0.071, 0.420, 0.161 ), 'attr' => array( 0.384, 0.184, 0.667 ),
        'operator' => array( 0.682, 0.122, 0.180 ), 'constant' => array( 0.0, 0.322, 0.627 ),
        'meta' => array( 0.384, 0.416, 0.459 ), 'section' => array( 0.0, 0.322, 0.627 ),
        'inserted' => array( 0.071, 0.420, 0.161 ), 'deleted' => array( 0.702, 0.114, 0.157 ),
        'property' => array( 0.0, 0.322, 0.627 ), 'type' => array( 0.384, 0.184, 0.667 ),
    );

    /** Body font size and line height factor */
    const SIZE = 10.0;
    const LEADING = 1.45;

    /** @var array options (see __construct) */
    protected $options;

    /** @var expHelpPdfCanvas */
    protected $pdf;

    /** @var array face => font name for the canvas (a .ttf path or a core font path) */
    protected $faces = array();

    /** @var bool whether the regular face is a TrueType (composite) font */
    protected $unicode = false;

    protected $pageWidth;
    protected $pageHeight;
    protected $marginLeft = 56.0;
    protected $marginRight = 56.0;
    protected $marginTop = 62.0;
    protected $marginBottom = 58.0;

    /** @var float the top of the next line, in PDF coordinates (0 at the bottom) */
    protected $y;

    /** @var array the decorations every drawn line gets (callout bars and backgrounds, code backgrounds) */
    protected $decorations = array();

    /** @var array ids on the page => true (the destinations internal links may point to) */
    protected $ids = array();

    /** @var array ids already given a destination */
    protected $placed = array();

    /** @var array the contents object of every page, for the footers */
    protected $pageContents = array();

    /** @var string the font selected on the canvas */
    protected $currentFont = '';

    /** @var array the page's facts (title, summary, path, changed, url) */
    protected $page = array();

    /** @var bool whether the first h1 (the title again) was skipped */
    protected $titleSkipped = false;

    /**
     * Options:
     *   fonts          array( regular, bold, italic, bolditalic, mono, fallback ) => .ttf path or '' (see
     *                  defaultFonts()); without a regular font the core fonts are used
     *   page_size      'A4' (default) or 'Letter'
     *   base_url       scheme and host put before addresses that start with / (links into the administration)
     *   image_resolver callable( string $src ): ?string  the file of an image of the page, null = alternative text
     *   translate      callable( string $text, array $params ): string  the words of the PDF (Page %number of %count ...)
     *   creator        the document's Creator entry (default "Exponential")
     *   compress       whether the content streams are compressed (default true)
     *
     * @param array $options
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array(
            'fonts' => array(),
            'page_size' => 'A4',
            'base_url' => '',
            'image_resolver' => null,
            'translate' => null,
            'creator' => 'Exponential',
            'compress' => true,
        );
        $size = strtoupper( (string)$this->options['page_size'] );
        list( $this->pageWidth, $this->pageHeight ) = isset( self::PAGE_SIZES[$size] ) ? self::PAGE_SIZES[$size] : self::PAGE_SIZES['A4'];
    }

    /**
     * The fonts of exphelp.ini [PdfSettings]: of each list the first file that exists and can be read. A relative
     * path is taken from the install root.
     *
     * @return array face => path ('' when none was found)
     */
    public static function defaultFonts()
    {
        $lists = array( 'regular' => 'FontFiles', 'bold' => 'BoldFontFiles', 'italic' => 'ItalicFontFiles',
                        'bolditalic' => 'BoldItalicFontFiles', 'mono' => 'MonoFontFiles', 'fallback' => 'FallbackFontFiles' );
        $root = class_exists( 'expHelp' ) ? rtrim( (string)expHelp::installRoot(), '/' ) : '';
        $fonts = array();
        foreach ( $lists as $face => $setting )
        {
            $fonts[$face] = '';
            $files = class_exists( 'expHelp' ) ? expHelp::setting( 'PdfSettings', $setting, array() ) : array();
            foreach ( (array)$files as $file )
            {
                $file = trim( (string)$file );
                if ( $file === '' || strtolower( substr( $file, -4 ) ) !== '.ttf' )
                    continue;
                if ( $file[0] !== '/' && $root !== '' )
                    $file = $root . '/' . $file;
                if ( is_file( $file ) && is_readable( $file ) )
                {
                    $fonts[$face] = $file;
                    break;
                }
            }
        }
        return $fonts;
    }

    /**
     * The PDF of one help page.
     *
     * @param array $page title, summary, path (doc/...), changed (timestamp), url (absolute address of the page),
     *                    language (optional)
     * @param string $html the renderer's HTML of the page
     * @return string the PDF
     */
    public function render( array $page, $html )
    {
        $this->page = $page + array( 'title' => '', 'summary' => '', 'path' => '', 'changed' => 0, 'url' => '' );
        $this->pdf = new expHelpPdfCanvas( array( 0, 0, $this->pageWidth, $this->pageHeight ) );
        $this->pdf->options['compression'] = $this->options['compress'] ? 1 : 0;
        $this->setupFonts();
        $this->pageContents = array( $this->pdf->currentContents );
        $this->y = $this->pageHeight - $this->marginTop;
        $this->decorations = $this->ids = $this->placed = array();
        $this->titleSkipped = false;

        $this->pdf->addUnicodeInfo( 'Title', (string)$this->page['title'] );
        if ( (string)$this->page['summary'] !== '' )
            $this->pdf->addUnicodeInfo( 'Subject', (string)$this->page['summary'] );
        $this->pdf->addUnicodeInfo( 'Creator', (string)$this->options['creator'] );
        $this->pdf->addUnicodeInfo( 'Producer', (string)$this->options['creator'] );

        $root = $this->parse( (string)$html );
        // a summary taken from the first paragraph is not printed twice
        if ( $root !== null && (string)$this->page['summary'] !== '' )
        {
            $first = $root->getElementsByTagName( 'p' )->item( 0 );
            $norm = function ( $text ) { return preg_replace( '/[\s.]+$/u', '', preg_replace( '/\s+/u', ' ', trim( (string)$text ) ) ); };
            $summary = preg_replace( '/(\.\.\.|\x{2026})$/u', '', $norm( $this->page['summary'] ) );
            if ( $first !== null && $summary !== '' && strpos( $norm( $first->textContent ), $summary ) === 0 )
                $this->page['summary'] = '';
        }
        $this->titleBlock();
        if ( $root !== null )
        {
            foreach ( ( new DOMXPath( $root->ownerDocument ) )->query( '//*[@id]', $root ) as $element )
                $this->ids[$element->getAttribute( 'id' )] = true;
            $this->blocks( $root, $this->context() );
        }
        $this->notice();
        $this->footers();
        return $this->pdf->output();
    }

    /* ------------------------------------------------------------------------------------------------ setup */

    protected function setupFonts()
    {
        $fonts = $this->options['fonts'] + array( 'regular' => '', 'bold' => '', 'italic' => '', 'bolditalic' => '', 'mono' => '', 'fallback' => '' );
        $core = rtrim( class_exists( 'expHelp' ) ? (string)expHelp::installRoot() : '', '/' );
        $core = ( $core === '' ? '' : $core . '/' ) . 'lib/ezpdf/classes/fonts/';
        $this->unicode = $fonts['regular'] !== '' && is_readable( $fonts['regular'] );
        if ( $this->unicode )
        {
            $this->faces = array(
                'regular' => $fonts['regular'],
                'bold' => $fonts['bold'] !== '' ? $fonts['bold'] : $fonts['regular'],
                'italic' => $fonts['italic'] !== '' ? $fonts['italic'] : $fonts['regular'],
                'bolditalic' => $fonts['bolditalic'] !== '' ? $fonts['bolditalic'] : ( $fonts['bold'] !== '' ? $fonts['bold'] : $fonts['regular'] ),
                'mono' => $fonts['mono'] !== '' ? $fonts['mono'] : $core . 'Courier',
                'fallback' => $fonts['fallback'],
            );
        }
        else
        {
            $this->faces = array( 'regular' => $core . 'Helvetica', 'bold' => $core . 'Helvetica-Bold',
                                  'italic' => $core . 'Helvetica-Oblique', 'bolditalic' => $core . 'Helvetica-BoldOblique',
                                  'mono' => $core . 'Courier', 'fallback' => '' );
        }
        $this->currentFont = '';
    }

    /**
     * Selects a font on the canvas (loading it the first time).
     */
    protected function useFont( $font )
    {
        if ( $this->currentFont === $font )
            return;
        if ( self::isTrueType( $font ) )
            $this->pdf->selectFont( $font );
        else
            $this->pdf->selectFont( $font, array( 'encoding' => 'WinAnsiEncoding' ) );
        $this->currentFont = $font;
    }

    protected static function isTrueType( $font )
    {
        return strtolower( substr( (string)$font, -4 ) ) === '.ttf';
    }

    /**
     * The face of a style.
     */
    protected function faceOf( array $style )
    {
        if ( !empty( $style['mono'] ) )
            return 'mono';
        if ( !empty( $style['b'] ) )
            return !empty( $style['i'] ) ? 'bolditalic' : 'bold';
        return !empty( $style['i'] ) ? 'italic' : 'regular';
    }

    /**
     * A text cut into the pieces one font each draws: array of array( font, text as the font takes it ).
     *
     * @param string $text UTF-8
     * @param array $style
     * @return array
     */
    protected function pieces( $text, array $style )
    {
        $face = $this->faceOf( $style );
        $font = $this->faces[$face];
        if ( !self::isTrueType( $font ) )
        {
            // a core font: Windows-1252; a mono text it cannot hold is drawn in the regular TrueType face instead
            $converted = self::toWinAnsi( $text );
            if ( $face === 'mono' && $this->unicode && self::toUtf8FromWinAnsi( $converted ) !== $text )
                return $this->pieces( $text, array( 'b' => !empty( $style['b'] ) ) );
            return array( array( $font, $converted ) );
        }
        $this->useFont( $font );
        $fallback = $this->faces['fallback'];
        if ( $fallback === '' || preg_match( '/[^\x00-\x7F]/', $text ) !== 1 )
            return array( array( $font, $text ) );

        $pieces = array();
        $current = null;
        $buffer = '';
        foreach ( preg_split( '//u', $text, -1, PREG_SPLIT_NO_EMPTY ) as $char )
        {
            $use = $font;
            $point = self::codePoint( $char );
            if ( $point > 0x7F && !$this->pdf->fontHasGlyph( $font, $point ) )
            {
                if ( !$this->pdf->hasFont( $fallback ) )
                {
                    $this->pdf->selectFont( $fallback, '', 0 );
                }
                if ( $this->pdf->fontHasGlyph( $fallback, $point ) )
                    $use = $fallback;
            }
            if ( $use !== $current && $buffer !== '' )
            {
                $pieces[] = array( $current, $buffer );
                $buffer = '';
            }
            $current = $use;
            $buffer .= $char;
        }
        if ( $buffer !== '' )
            $pieces[] = array( $current, $buffer );
        return $pieces;
    }

    /**
     * The width of a text in a style at a size, in points.
     */
    protected function width( $text, array $style, $size )
    {
        if ( $text === '' )
            return 0.0;
        $width = 0.0;
        foreach ( $this->pieces( $text, $style ) as $piece )
        {
            $this->useFont( $piece[0] );
            $width += $this->pdf->getTextWidth( $size, $piece[1] );
        }
        return $width;
    }

    /**
     * Draws a text with its baseline at x, y.
     */
    protected function text( $x, $y, $text, array $style, $size )
    {
        $color = isset( $style['color'] ) ? $style['color'] : self::COLORS['ink'];
        $this->pdf->setColorRGB( $color[0], $color[1], $color[2], 1 );
        foreach ( $this->pieces( $text, $style ) as $piece )
        {
            $this->useFont( $piece[0] );
            $this->pdf->addText( $x, $y, $size, $piece[1] );
            $x += $this->pdf->getTextWidth( $size, $piece[1] );
        }
    }

    protected function fillRect( $x, $y, $w, $h, array $color )
    {
        $this->pdf->setColorRGB( $color[0], $color[1], $color[2], 1 );
        $this->pdf->filledRectangle( $x, $y, $w, $h );
    }

    protected function line( $x1, $y1, $x2, $y2, array $color, $width = 0.6 )
    {
        $this->pdf->setStrokeColorRGB( $color[0], $color[1], $color[2], 1 );
        $this->pdf->setLineStyle( $width );
        $this->pdf->line( $x1, $y1, $x2, $y2 );
    }

    protected function t( $text, array $params = array() )
    {
        if ( is_callable( $this->options['translate'] ) )
            return (string)call_user_func( $this->options['translate'], $text, $params );
        return strtr( $text, $params );
    }

    /* ------------------------------------------------------------------------------------------------ pages */

    protected function bottom()
    {
        return $this->marginBottom;
    }

    /**
     * A new page when less than $height is left on this one.
     */
    protected function ensure( $height )
    {
        if ( $this->y - $height < $this->bottom() )
            $this->newPage();
    }

    protected function newPage()
    {
        $this->pdf->newPage();
        $this->currentFont = '';
        $this->pageContents[] = $this->pdf->currentContents;
        $this->y = $this->pageHeight - $this->marginTop;
    }

    /**
     * Vertical space, drawn with the open decorations (a callout's background goes on through it).
     */
    protected function space( $height )
    {
        if ( $height <= 0 )
            return;
        if ( $this->y - $height < $this->bottom() )
        {
            $this->newPage();
            return;
        }
        $this->decorate( $this->y, $this->y - $height );
        $this->y -= $height;
    }

    /**
     * Draws the open decorations from $top down to $bottom.
     */
    protected function decorate( $top, $bottom )
    {
        foreach ( $this->decorations as $d )
        {
            if ( isset( $d['bg'] ) )
                $this->fillRect( $d['x0'], $bottom, $d['x1'] - $d['x0'], $top - $bottom, $d['bg'] );
            if ( isset( $d['bar'] ) )
                $this->fillRect( $d['x0'], $bottom, $d['barWidth'], $top - $bottom, $d['bar'] );
        }
    }

    /**
     * Title, summary, source, last change and address of the page, above its text.
     */
    protected function titleBlock()
    {
        $left = $this->marginLeft;
        $width = $this->pageWidth - $this->marginLeft - $this->marginRight;
        $label = array( 'color' => self::COLORS['muted'] );
        $this->paragraph( $this->atoms( $this->t( 'Exponential help' ), $label ), array( 'left' => $left, 'width' => $width, 'size' => 8.5 ) );
        $this->space( 2 );
        $title = (string)$this->page['title'];
        $this->paragraph( $this->atoms( $title, array( 'b' => true ) ), array( 'left' => $left, 'width' => $width, 'size' => 20, 'leading' => 1.25 ) );
        if ( (string)$this->page['summary'] !== '' )
        {
            $this->space( 3 );
            $this->paragraph( $this->atoms( (string)$this->page['summary'], $label ), array( 'left' => $left, 'width' => $width, 'size' => 11 ) );
        }
        $this->space( 4 );
        $facts = array();
        if ( (string)$this->page['path'] !== '' )
        {
            $facts = array_merge( $facts, $this->atoms( $this->t( 'Source file:' ) . ' ', $label ),
                                  $this->atoms( (string)$this->page['path'], array( 'mono' => true, 'color' => self::COLORS['ink'] ) ) );
        }
        if ( (int)$this->page['changed'] > 0 )
        {
            if ( $facts )
                $facts = array_merge( $facts, $this->atoms( '   ', $label ) );
            $facts = array_merge( $facts, $this->atoms( $this->t( 'Last changed' ) . ' ' . gmdate( 'Y-m-d', (int)$this->page['changed'] ), $label ) );
        }
        if ( $facts )
            $this->paragraph( $facts, array( 'left' => $left, 'width' => $width, 'size' => 8.5 ) );
        if ( (string)$this->page['url'] !== '' )
        {
            $this->paragraph( $this->atoms( (string)$this->page['url'], array( 'color' => self::COLORS['link'], 'href' => (string)$this->page['url'] ) ),
                              array( 'left' => $left, 'width' => $width, 'size' => 8.5 ) );
        }
        $this->space( 6 );
        $this->line( $left, $this->y, $left + $width, $this->y, self::COLORS['line'], 0.8 );
        $this->space( 12 );
    }

    /**
     * The page's license notice (page fact "notice", plain text, lines kept) after its text, small and muted.
     */
    protected function notice()
    {
        $notice = trim( (string)( isset( $this->page['notice'] ) ? $this->page['notice'] : '' ) );
        if ( $notice === '' )
            return;
        $left = $this->marginLeft;
        $width = $this->pageWidth - $this->marginLeft - $this->marginRight;
        $style = array( 'color' => self::COLORS['muted'] );
        $this->space( 10 );
        $this->ensure( 60 );
        $this->line( $left, $this->y, $left + $width, $this->y, self::COLORS['line'], 0.6 );
        $this->space( 6 );
        foreach ( preg_split( '/\r\n|\r|\n/', $notice ) as $line )
        {
            if ( trim( $line ) === '' )
            {
                $this->space( 4 );
                continue;
            }
            $atoms = array();
            foreach ( preg_split( '/(https?:\/\/[^\s<>"]+)/', $line, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ) as $part )
            {
                $partStyle = preg_match( '#^https?://#', $part ) ? array( 'color' => self::COLORS['link'], 'href' => $part ) : $style;
                $atoms = array_merge( $atoms, $this->atoms( $part, $partStyle ) );
            }
            $this->paragraph( $atoms, array( 'left' => $left, 'width' => $width, 'size' => 7.5 ) );
        }
    }

    /**
     * "Title ... Page n of N" at the foot of every page, written once the number of pages is known.
     */
    protected function footers()
    {
        $count = count( $this->pageContents );
        $saved = $this->pdf->currentContents;
        $size = 7.5;
        $style = array( 'color' => self::COLORS['muted'] );
        $y = $this->marginBottom - 26;
        $right = $this->pageWidth - $this->marginRight;
        foreach ( $this->pageContents as $n => $contents )
        {
            $this->pdf->currentContents = $contents;
            $this->currentFont = '';
            $this->line( $this->marginLeft, $y + 11, $right, $y + 11, self::COLORS['line'], 0.5 );
            $number = $this->t( 'Page %number of %count', array( '%number' => $n + 1, '%count' => $count ) );
            $numberWidth = $this->width( $number, $style, $size );
            $this->text( $right - $numberWidth, $y, $number, $style, $size );
            $title = $this->fit( (string)$this->page['title'], $style, $size, $right - $this->marginLeft - $numberWidth - 24 );
            $atoms = $this->atoms( $title, $style );
            $lines = $this->wrap( $atoms, PHP_FLOAT_MAX, $size );
            $x = $this->marginLeft;
            foreach ( self::reorder( $lines ? $lines[0] : array(), self::direction( $atoms ) === 'rtl' ) as $item )
            {
                $this->text( $x, $y, $item['text'], $item['style'], $size );
                $x += $item['w'];
            }
        }
        $this->pdf->currentContents = $saved;
    }

    /**
     * A text cut to fit a width, with an ellipsis.
     */
    protected function fit( $text, array $style, $size, $width )
    {
        if ( $this->width( $text, $style, $size ) <= $width )
            return $text;
        $chars = preg_split( '/(\X)/u', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
        while ( $chars && $this->width( implode( '', $chars ) . '...', $style, $size ) > $width )
            array_pop( $chars );
        return rtrim( implode( '', $chars ) ) . '...';
    }

    /* ------------------------------------------------------------------------------------------------ input */

    /**
     * The renderer's HTML as a DOM element (its outer div), or null.
     *
     * @param string $html
     * @return DOMElement|null
     */
    protected function parse( $html )
    {
        if ( trim( $html ) === '' )
            return null;
        $dom = new DOMDocument( '1.0', 'UTF-8' );
        $previous = libxml_use_internal_errors( true );
        $dom->loadHTML( '<?xml encoding="UTF-8"?><!DOCTYPE html><html><body><div id="exphelp-pdf-root">' . $html . '</div></body></html>',
                        LIBXML_NONET | LIBXML_COMPACT );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        $root = $dom->getElementById( 'exphelp-pdf-root' );
        if ( $root === null )
        {
            $divs = $dom->getElementsByTagName( 'div' );
            $root = $divs->length ? $divs->item( 0 ) : null;
        }
        return $root;
    }

    protected static function hasClass( DOMNode $node, $class )
    {
        return $node instanceof DOMElement
            && in_array( $class, preg_split( '/\s+/', (string)$node->getAttribute( 'class' ), -1, PREG_SPLIT_NO_EMPTY ), true );
    }

    protected static function classPrefixed( DOMNode $node, $prefix )
    {
        if ( !$node instanceof DOMElement )
            return null;
        foreach ( preg_split( '/\s+/', (string)$node->getAttribute( 'class' ), -1, PREG_SPLIT_NO_EMPTY ) as $class )
        {
            if ( strpos( $class, $prefix ) === 0 )
                return substr( $class, strlen( $prefix ) );
        }
        return null;
    }

    /** Elements laid out as blocks; everything else is inline */
    const BLOCKS = array( 'p', 'div', 'section', 'article', 'header', 'footer', 'nav', 'aside', 'main', 'h1', 'h2', 'h3',
                          'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'table', 'pre', 'blockquote', 'figure', 'figcaption', 'hr',
                          'details', 'summary', 'dl', 'dt', 'dd', 'img' );

    /** Elements left out (and all inside them) */
    const SKIP = array( 'script', 'style', 'button', 'template', 'svg', 'iframe', 'object', 'form', 'input', 'select', 'textarea', 'noscript' );

    protected function context( array $changes = array() )
    {
        return $changes + array(
            'left' => $this->marginLeft,
            'width' => $this->pageWidth - $this->marginLeft - $this->marginRight,
            'size' => self::SIZE,
            'style' => array(),
            'marker' => null,
            'depth' => 0,
        );
    }

    /**
     * The children of an element: runs of inline content become paragraphs, block elements are laid out.
     */
    protected function blocks( DOMNode $parent, array $ctx )
    {
        $atoms = array();
        foreach ( $parent->childNodes as $child )
        {
            if ( $child instanceof DOMElement && $this->skipped( $child ) )
                continue;
            if ( $child instanceof DOMElement && in_array( strtolower( $child->tagName ), self::BLOCKS, true ) )
            {
                $this->flush( $atoms, $ctx );
                $this->block( $child, $ctx );
                continue;
            }
            $this->inline( $child, $ctx['style'], $atoms );
        }
        $this->flush( $atoms, $ctx );
    }

    protected function skipped( DOMElement $element )
    {
        $tag = strtolower( $element->tagName );
        return in_array( $tag, self::SKIP, true )
            || self::hasClass( $element, 'a4-help-anchor' )
            || self::hasClass( $element, 'a4-sr-only' )
            || self::hasClass( $element, 'a4-help-copy' )
            || self::hasClass( $element, 'a4-help-fnback' )
            || ( self::hasClass( $element, 'a4-icon' ) && !self::hasClass( $element, 'a4-help-task-box' ) )
            || $element->hasAttribute( 'hidden' ) && $tag !== 'section';
    }

    /**
     * Inline content into atoms: array( text, style ) and "\n" for a line break.
     */
    protected function inline( DOMNode $node, array $style, array &$atoms )
    {
        if ( $node instanceof DOMText )
        {
            $text = preg_replace( '/[ \t\r\n\f]+/', ' ', $node->data );
            if ( $text !== '' )
                $atoms = array_merge( $atoms, $this->atoms( $text, $style ) );
            return;
        }
        if ( !$node instanceof DOMElement || $this->skipped( $node ) )
            return;
        $tag = strtolower( $node->tagName );
        switch ( $tag )
        {
            case 'br':
                $atoms[] = array( "\n", $style );
                return;
            case 'strong':
            case 'b':
            case 'th':
                $style['b'] = true;
                break;
            case 'em':
            case 'i':
            case 'cite':
                $style['i'] = true;
                break;
            case 'code':
            case 'kbd':
            case 'samp':
            case 'tt':
                $style['mono'] = true;
                $style['inlinecode'] = true;
                break;
            case 'a':
                $href = $this->href( (string)$node->getAttribute( 'href' ) );
                if ( $href !== null )
                {
                    $style['href'] = $href;
                    $style['color'] = self::COLORS['link'];
                }
                break;
            case 'span':
                if ( self::hasClass( $node, 'a4-help-task-box' ) )
                {
                    $done = self::hasClass( $node, 'a4-help-task-box--done' );
                    $atoms = array_merge( $atoms, $this->atoms( $this->unicode ? ( $done ? "\u{2611} " : "\u{2610} " ) : ( $done ? '[x] ' : '[ ] ' ), $style ) );
                    return;
                }
                $kind = self::classPrefixed( $node, 'a4-help-tok-' );
                if ( $kind !== null && isset( self::TOKENS[$kind] ) )
                    $style['color'] = self::TOKENS[$kind];
                break;
            case 'sup':
                if ( self::hasClass( $node, 'a4-help-fnref' ) || $node->getElementsByTagName( 'a' )->length )
                {
                    $atoms = array_merge( $atoms, $this->atoms( '[' . trim( $node->textContent ) . ']', $style ) );
                    return;
                }
                break;
        }
        foreach ( $node->childNodes as $child )
            $this->inline( $child, $style, $atoms );
    }

    /**
     * A text into atoms (words and spaces).
     */
    protected function atoms( $text, array $style )
    {
        $atoms = array();
        foreach ( preg_split( '/( )/', (string)$text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ) as $part )
            $atoms[] = array( $part, $style );
        return $atoms;
    }

    /**
     * The address a link gets in the PDF, or null for no link.
     */
    protected function href( $href )
    {
        $href = trim( $href );
        if ( $href === '' )
            return null;
        if ( $href[0] === '#' )
        {
            $id = rawurldecode( substr( $href, 1 ) );
            return isset( $this->ids[$id] ) ? '#' . $id : null;
        }
        if ( preg_match( '#^(https?:|mailto:)#i', $href ) )
            return $href;
        if ( $href[0] === '/' && substr( $href, 0, 2 ) !== '//' && (string)$this->options['base_url'] !== '' )
            return rtrim( (string)$this->options['base_url'], '/' ) . $href;
        return null;
    }

    protected function flush( array &$atoms, array $ctx )
    {
        // nothing but spaces: nothing to draw
        $hasText = false;
        foreach ( $atoms as $atom )
        {
            if ( trim( $atom[0] ) !== '' )
            {
                $hasText = true;
                break;
            }
        }
        if ( $hasText )
        {
            $this->paragraph( $atoms, $ctx );
            $this->space( $ctx['size'] * 0.55 );
        }
        $atoms = array();
    }

    /* ------------------------------------------------------------------------------------------------ blocks */

    protected function block( DOMElement $element, array $ctx )
    {
        $tag = strtolower( $element->tagName );
        // a heading places its destination after it has moved to its page
        if ( preg_match( '/^h[1-6]$/', $tag ) !== 1 )
            $this->destination( $element );
        switch ( $tag )
        {
            case 'h1': case 'h2': case 'h3': case 'h4': case 'h5': case 'h6':
                $this->heading( $element, (int)$tag[1], $ctx );
                return;
            case 'p':
                if ( self::hasClass( $element, 'a4-help-callout-title' ) )
                {
                    $atoms = array();
                    $this->inline( $element, $ctx['style'] + array( 'b' => true ), $atoms );
                    $this->paragraph( $atoms, $ctx );
                    $this->space( 3 );
                    return;
                }
                $atoms = array();
                foreach ( $element->childNodes as $child )
                {
                    if ( $child instanceof DOMElement && strtolower( $child->tagName ) === 'img' )
                    {
                        $this->flush( $atoms, $ctx );
                        $this->image( $child, $ctx );
                        continue;
                    }
                    $this->inline( $child, $ctx['style'], $atoms );
                }
                $this->flush( $atoms, $ctx );
                return;
            case 'ul':
            case 'ol':
                $this->listBlock( $element, $tag === 'ol', $ctx );
                return;
            case 'li':
                $this->blocks( $element, $ctx );
                return;
            case 'table':
                $this->table( $element, $ctx );
                return;
            case 'pre':
                $this->code( $element, $ctx, '', self::hasClass( $element->parentNode, 'a4-help-plain' ) );
                return;
            case 'blockquote':
                $this->boxed( $element, $ctx, self::COLORS['quote'], null, array( 'color' => self::COLORS['muted'] ) );
                return;
            case 'figure':
                $this->ensure( 40 );
                $this->blocks( $element, $ctx );
                return;
            case 'figcaption':
                $atoms = array();
                $this->inline( $element, array( 'i' => true, 'color' => self::COLORS['muted'] ), $atoms );
                $this->paragraph( $atoms, array( 'size' => $ctx['size'] - 1.5, 'align' => 'center' ) + $ctx );
                $this->space( 6 );
                return;
            case 'img':
                $this->image( $element, $ctx );
                return;
            case 'hr':
                $this->space( 4 );
                $this->line( $ctx['left'], $this->y, $ctx['left'] + $ctx['width'], $this->y, self::COLORS['line'], 0.8 );
                $this->space( 8 );
                return;
            case 'summary':
                $atoms = array();
                $this->inline( $element, $ctx['style'] + array( 'b' => true ), $atoms );
                $this->paragraph( $atoms, $ctx );
                $this->space( 3 );
                return;
            case 'dt':
                $atoms = array();
                $this->inline( $element, $ctx['style'] + array( 'b' => true ), $atoms );
                $this->paragraph( $atoms, $ctx );
                return;
            case 'dd':
                $this->blocks( $element, array( 'left' => $ctx['left'] + 16, 'width' => $ctx['width'] - 16 ) + $ctx );
                return;
            case 'section':
                if ( self::hasClass( $element, 'a4-help-footnotes' ) )
                {
                    // the renderer's own rule separates the notes from the text
                    $this->space( 4 );
                    $this->blocks( $element, array( 'size' => $ctx['size'] - 1.5 ) + $ctx );
                    return;
                }
                $this->blocks( $element, $ctx );
                return;
            case 'div':
                if ( self::hasClass( $element, 'a4-help-code' ) )
                {
                    $pre = $element->getElementsByTagName( 'pre' );
                    if ( $pre->length )
                    {
                        $this->code( $pre->item( 0 ), $ctx, (string)$element->getAttribute( 'data-label' ), false );
                        return;
                    }
                }
                $kind = self::classPrefixed( $element, 'a4-help-callout--' );
                if ( $kind !== null )
                {
                    $colors = isset( self::CALLOUTS[$kind] ) ? self::CALLOUTS[$kind] : self::CALLOUTS['note'];
                    $this->boxed( $element, $ctx, $colors[0], $colors[1], array( 'calloutColor' => $colors[0] ) );
                    return;
                }
                $this->blocks( $element, $ctx );
                return;
            default:
                $this->blocks( $element, $ctx );
        }
    }

    /**
     * A destination for an element's id, at the current position.
     */
    protected function destination( DOMElement $element )
    {
        $id = (string)$element->getAttribute( 'id' );
        if ( $id === '' || isset( $this->placed[$id] ) )
            return;
        $this->placed[$id] = true;
        $this->pdf->addDestination( $id, 'XYZ', 0, $this->y + 4, 0 );
    }

    protected function heading( DOMElement $element, $level, array $ctx )
    {
        $atoms = array();
        $this->inline( $element, $ctx['style'] + array( 'b' => true ), $atoms );
        $text = '';
        foreach ( $atoms as $atom )
            $text .= $atom[0];
        if ( $level === 1 && !$this->titleSkipped && trim( $text ) === trim( (string)$this->page['title'] ) )
        {
            // the title block already shows it
            $this->titleSkipped = true;
            $this->destination( $element );
            return;
        }
        $sizes = array( 1 => 17, 2 => 14, 3 => 12, 4 => 11, 5 => 10, 6 => 10 );
        $size = $sizes[$level];
        $before = array( 1 => 14, 2 => 12, 3 => 9, 4 => 7, 5 => 6, 6 => 6 );
        $this->space( $before[$level] );
        // a heading is kept with at least three lines of what follows
        $this->ensure( $size * 1.3 + self::SIZE * self::LEADING * 3 );
        $this->destination( $element );
        $this->paragraph( $atoms, array( 'size' => $size, 'leading' => 1.3 ) + $ctx );
        if ( $level <= 2 )
        {
            $this->space( 2 );
            $this->line( $ctx['left'], $this->y, $ctx['left'] + $ctx['width'], $this->y, self::COLORS['line'], $level === 1 ? 0.8 : 0.5 );
        }
        $this->space( $level <= 2 ? 6 : 4 );
    }

    protected function listBlock( DOMElement $list, $ordered, array $ctx )
    {
        $number = $ordered && $list->hasAttribute( 'start' ) ? (int)$list->getAttribute( 'start' ) : 1;
        $bullets = $this->unicode ? array( "\u{2022}", "\u{25E6}", "\u{25AA}" ) : array( "\u{2022}", '-', "\u{2022}" );
        $indent = 16;
        $tasks = self::hasClass( $list, 'a4-help-tasks' );
        foreach ( $list->childNodes as $item )
        {
            if ( !$item instanceof DOMElement || strtolower( $item->tagName ) !== 'li' )
                continue;
            $this->destination( $item );
            $marker = $tasks ? null : (object)array(
                'text' => $ordered ? $number . '.' : $bullets[$ctx['depth'] % 3],
                'right' => $ctx['left'] + $indent - 5,
            );
            $number++;
            $this->blocks( $item, array( 'left' => $ctx['left'] + $indent, 'width' => $ctx['width'] - $indent,
                                         'marker' => $marker, 'depth' => $ctx['depth'] + 1 ) + $ctx );
            // paragraphs end with space; list items sit closer together
        }
        $this->space( 2 );
    }

    /**
     * A box with a bar on its start side (callout, quote) and optionally a background.
     */
    protected function boxed( DOMElement $element, array $ctx, array $bar, $bg, array $extra )
    {
        $pad = 10;
        $this->space( 2 );
        $decoration = array( 'x0' => $ctx['left'], 'x1' => $ctx['left'] + $ctx['width'], 'bar' => $bar, 'barWidth' => 3 );
        if ( $bg !== null )
            $decoration['bg'] = $bg;
        $this->ensure( self::SIZE * self::LEADING * 3 );
        $this->decorations[] = $decoration;
        $this->space( $bg !== null ? 6 : 2 );
        $style = $ctx['style'];
        if ( isset( $extra['color'] ) )
            $style['color'] = $extra['color'];
        if ( isset( $extra['calloutColor'] ) )
        {
            // the callout's title in its colour
            foreach ( $element->childNodes as $child )
            {
                if ( $child instanceof DOMElement && self::hasClass( $child, 'a4-help-callout-title' ) )
                {
                    $atoms = array();
                    $this->inline( $child, $style + array( 'b' => true, 'color' => $extra['calloutColor'] ), $atoms );
                    $this->paragraph( $atoms, array( 'left' => $ctx['left'] + $pad + 3, 'width' => $ctx['width'] - 2 * $pad - 3 ) + $ctx );
                    $this->space( 3 );
                    $child->parentNode->removeChild( $child );
                    break;
                }
            }
        }
        $this->blocks( $element, array( 'left' => $ctx['left'] + $pad + 3, 'width' => $ctx['width'] - 2 * $pad - 3, 'style' => $style, 'marker' => null ) + $ctx );
        // the last paragraph's space is enough at the bottom of the box
        array_pop( $this->decorations );
        $this->space( 8 );
    }

    /* ------------------------------------------------------------------------------------------------ paragraphs */

    /**
     * Lays out atoms in lines and draws them.
     *
     * @param array $atoms
     * @param array $ctx left, width, size, leading, align (left|center|right), marker
     */
    protected function paragraph( array $atoms, array $ctx )
    {
        $ctx += array( 'size' => self::SIZE, 'leading' => self::LEADING, 'align' => '', 'marker' => null, 'style' => array() );
        $size = $ctx['size'];
        $lineHeight = $size * $ctx['leading'];
        $rtl = self::direction( $atoms ) === 'rtl';
        $align = $ctx['align'] !== '' ? $ctx['align'] : ( $rtl ? 'right' : 'left' );
        $lines = $this->wrap( $atoms, $ctx['width'], $size );
        $first = true;
        foreach ( $lines as $line )
        {
            $this->ensure( $lineHeight );
            $top = $this->y;
            $this->decorate( $top, $top - $lineHeight );
            $baseline = $top - $size * 1.08 - ( $lineHeight - $size * 1.2 ) / 2;
            $width = 0.0;
            foreach ( $line as $item )
                $width += $item['w'];
            $x = $ctx['left'];
            if ( $align === 'right' )
                $x = $ctx['left'] + $ctx['width'] - $width;
            elseif ( $align === 'center' )
                $x = $ctx['left'] + ( $ctx['width'] - $width ) / 2;
            if ( $first && $ctx['marker'] !== null && empty( $ctx['marker']->used ) )
            {
                $marker = $ctx['marker'];
                $markerStyle = array( 'color' => self::COLORS['muted'] );
                $this->text( $marker->right - $this->width( $marker->text, $markerStyle, $size ), $baseline, $marker->text, $markerStyle, $size );
                $marker->used = true;
            }
            $ordered = self::reorder( $line, $rtl );
            // the backgrounds of inline code first, so none covers the text before it
            $fx = $x;
            foreach ( $ordered as $item )
            {
                if ( !empty( $item['style']['inlinecode'] ) && trim( $item['text'] ) !== '' )
                    $this->fillRect( $fx - 1, $baseline - $size * 0.25, $item['w'] + 2, $size * 1.15, self::COLORS['soft'] );
                $fx += $item['w'];
            }
            foreach ( $ordered as $item )
            {
                $style = $item['style'];
                if ( $item['text'] !== '' )
                    $this->text( $x, $baseline, $item['text'], $style, $size );
                if ( isset( $style['href'] ) && $item['text'] !== '' )
                {
                    if ( $style['href'][0] === '#' )
                        $this->pdf->addInternalLink( substr( $style['href'], 1 ), $x, $baseline - $size * 0.25, $x + $item['w'], $baseline + $size * 0.9 );
                    else
                        $this->pdf->addLink( $style['href'], $x, $baseline - $size * 0.25, $x + $item['w'], $baseline + $size * 0.9 );
                    $this->line( $x, $baseline - $size * 0.12, $x + $item['w'], $baseline - $size * 0.12, $style['color'], 0.4 );
                }
                $x += $item['w'];
            }
            $this->y -= $lineHeight;
            $first = false;
        }
    }

    /**
     * Atoms into lines that fit $width: each line a list of array( text, style, w ), without spaces at its ends.
     */
    protected function wrap( array $atoms, $width, $size )
    {
        $lines = array();
        $line = array();
        $lineWidth = 0.0;
        $pending = null;
        foreach ( $atoms as $atom )
        {
            list( $text, $style ) = $atom;
            if ( $text === "\n" )
            {
                $lines[] = $line;
                $line = array();
                $lineWidth = 0.0;
                $pending = null;
                continue;
            }
            if ( $text === ' ' )
            {
                if ( $line )
                    $pending = array( 'text' => ' ', 'style' => $style, 'w' => $this->width( ' ', $style, $size ) );
                continue;
            }
            $w = $this->width( $text, $style, $size );
            $spaceWidth = $pending !== null ? $pending['w'] : 0.0;
            if ( $line && $lineWidth + $spaceWidth + $w > $width )
            {
                $lines[] = $line;
                $line = array();
                $lineWidth = 0.0;
                $pending = null;
                $spaceWidth = 0.0;
            }
            if ( $pending !== null )
            {
                $line[] = $pending;
                $lineWidth += $pending['w'];
                $pending = null;
            }
            if ( $w > $width )
            {
                // longer than a line (an address, a path, text without spaces): cut where it fills the line
                foreach ( $this->cut( $text, $style, $size, $width - $lineWidth, $width ) as $i => $chunk )
                {
                    if ( $i > 0 )
                    {
                        $lines[] = $line;
                        $line = array();
                        $lineWidth = 0.0;
                    }
                    $line[] = $chunk;
                    $lineWidth += $chunk['w'];
                }
                continue;
            }
            $line[] = array( 'text' => $text, 'style' => $style, 'w' => $w );
            $lineWidth += $w;
        }
        if ( $line )
            $lines[] = $line;
        return $lines;
    }

    /**
     * A word cut into chunks: the first fits $firstWidth, the others $width.
     */
    protected function cut( $text, array $style, $size, $firstWidth, $width )
    {
        $chunks = array();
        $current = '';
        $currentWidth = 0.0;
        $limit = $firstWidth > $size ? $firstWidth : $width;
        foreach ( preg_split( '/(\X)/u', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ) as $char )
        {
            $w = $this->width( $char, $style, $size );
            if ( $current !== '' && $currentWidth + $w > $limit )
            {
                $chunks[] = array( 'text' => $current, 'style' => $style, 'w' => $currentWidth );
                $current = '';
                $currentWidth = 0.0;
                $limit = $width;
            }
            $current .= $char;
            $currentWidth += $w;
        }
        if ( $current !== '' )
            $chunks[] = array( 'text' => $current, 'style' => $style, 'w' => $currentWidth );
        if ( $firstWidth <= $size && $chunks )
            array_unshift( $chunks, array( 'text' => '', 'style' => $style, 'w' => 0.0 ) );
        return $chunks;
    }

    /* ------------------------------------------------------------------------------------------------ code */

    /**
     * A code block (or a plain text page): monospace, its lines wrapped, on a light background.
     */
    protected function code( DOMElement $pre, array $ctx, $label, $plain )
    {
        $size = $plain ? 8.5 : 8.0;
        $lineHeight = $size * 1.4;
        $pad = $plain ? 0 : 8;
        $runs = array();
        $this->codeRuns( $pre, array( 'mono' => true, 'color' => self::COLORS['code'] ), $runs );
        // lines of runs
        $lines = array( array() );
        foreach ( $runs as $run )
        {
            $parts = explode( "\n", str_replace( array( "\r\n", "\r", "\t" ), array( "\n", "\n", '    ' ), $run[0] ) );
            foreach ( $parts as $i => $part )
            {
                if ( $i > 0 )
                    $lines[] = array();
                if ( $part !== '' )
                    $lines[count( $lines ) - 1][] = array( $part, $run[1] );
            }
        }
        while ( $lines && !end( $lines ) )
            array_pop( $lines );
        if ( !$lines )
            return;
        // wrapped to the box
        $width = $ctx['width'] - 2 * $pad;
        $visual = array();
        foreach ( $lines as $line )
        {
            $current = array();
            $currentWidth = 0.0;
            foreach ( $line as $run )
            {
                foreach ( preg_split( '/(\X)/u', $run[0], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ) as $char )
                {
                    $w = $this->width( $char, $run[1], $size );
                    if ( $currentWidth + $w > $width && $current )
                    {
                        $visual[] = $current;
                        $current = array();
                        $currentWidth = 0.0;
                    }
                    $last = count( $current ) - 1;
                    if ( $last >= 0 && $current[$last]['style'] === $run[1] )
                    {
                        $current[$last]['text'] .= $char;
                        $current[$last]['w'] += $w;
                    }
                    else
                        $current[] = array( 'text' => $char, 'style' => $run[1], 'w' => $w );
                    $currentWidth += $w;
                }
            }
            $visual[] = $current;
        }

        $total = count( $visual ) * $lineHeight + 2 * $pad + ( $label !== '' ? 14 : 0 );
        $body = $this->pageHeight - $this->marginTop - $this->marginBottom;
        // a short block is not split over two pages
        $this->ensure( $total < $body * 0.4 ? $total : $lineHeight * 3 + 2 * $pad );
        if ( $label !== '' )
        {
            $this->paragraph( $this->atoms( $label, array( 'b' => true, 'color' => self::COLORS['muted'] ) ), array( 'size' => 7.5, 'leading' => 1.5 ) + $ctx );
        }
        if ( !$plain )
            $this->decorations[] = array( 'x0' => $ctx['left'], 'x1' => $ctx['left'] + $ctx['width'], 'bg' => self::COLORS['soft'], 'bar' => self::COLORS['line'], 'barWidth' => 2 );
        $this->space( $pad );
        foreach ( $visual as $line )
        {
            $this->ensure( $lineHeight );
            $top = $this->y;
            $this->decorate( $top, $top - $lineHeight );
            $baseline = $top - $size * 1.1;
            $x = $ctx['left'] + $pad;
            foreach ( $line as $item )
            {
                if ( $item['text'] !== '' )
                    $this->text( $x, $baseline, $item['text'], $item['style'], $size );
                $x += $item['w'];
            }
            $this->y -= $lineHeight;
        }
        $this->space( $pad );
        if ( !$plain )
            array_pop( $this->decorations );
        $this->space( 8 );
    }

    /**
     * The text of a pre with the colours of its syntax tokens: array of array( text, style ).
     */
    protected function codeRuns( DOMNode $node, array $style, array &$runs )
    {
        foreach ( $node->childNodes as $child )
        {
            if ( $child instanceof DOMText )
            {
                if ( $child->data !== '' )
                    $runs[] = array( $child->data, $style );
                continue;
            }
            if ( !$child instanceof DOMElement || $this->skipped( $child ) )
                continue;
            $childStyle = $style;
            $kind = self::classPrefixed( $child, 'a4-help-tok-' );
            if ( $kind !== null && isset( self::TOKENS[$kind] ) )
                $childStyle['color'] = self::TOKENS[$kind];
            if ( strtolower( $child->tagName ) === 'br' )
            {
                $runs[] = array( "\n", $style );
                continue;
            }
            $this->codeRuns( $child, $childStyle, $runs );
        }
    }

    /* ------------------------------------------------------------------------------------------------ tables */

    protected function table( DOMElement $table, array $ctx )
    {
        $size = 8.5;
        $pad = 4.0;
        $lineHeight = $size * 1.4;
        $rows = array();
        $headRows = 0;
        foreach ( ( new DOMXPath( $table->ownerDocument ) )->query( './/tr', $table ) as $tr )
        {
            $cells = array();
            $isHead = $tr->parentNode instanceof DOMElement && strtolower( $tr->parentNode->tagName ) === 'thead';
            foreach ( $tr->childNodes as $cell )
            {
                if ( !$cell instanceof DOMElement || !in_array( strtolower( $cell->tagName ), array( 'td', 'th' ), true ) )
                    continue;
                $atoms = array();
                $this->inline( $cell, $ctx['style'], $atoms );
                $align = self::classPrefixed( $cell, 'a4-help-align-' );
                $cells[] = array( 'atoms' => $atoms, 'align' => in_array( $align, array( 'center', 'right' ), true ) ? $align : '',
                                  'head' => strtolower( $cell->tagName ) === 'th' );
            }
            if ( !$cells )
                continue;
            if ( $isHead && count( $rows ) === $headRows )
                $headRows++;
            $rows[] = $cells;
        }
        if ( !$rows )
            return;
        $columns = 0;
        foreach ( $rows as $cells )
            $columns = max( $columns, count( $cells ) );

        // natural and smallest widths of each column
        $natural = array_fill( 0, $columns, 0.0 );
        $smallest = array_fill( 0, $columns, 0.0 );
        foreach ( $rows as $cells )
        {
            foreach ( $cells as $c => $cell )
            {
                $total = 0.0;
                foreach ( $cell['atoms'] as $atom )
                {
                    if ( $atom[0] === "\n" )
                        continue;
                    $w = $this->width( $atom[0], $atom[1], $size );
                    $total += $w;
                    $smallest[$c] = max( $smallest[$c], min( $w, 120.0 ) );
                }
                $natural[$c] = max( $natural[$c], $total );
            }
        }
        $available = $ctx['width'];
        $widths = array();
        foreach ( $natural as $c => $w )
            $widths[$c] = $w + 2 * $pad;
        if ( array_sum( $widths ) > $available )
        {
            $min = array();
            foreach ( $smallest as $c => $w )
                $min[$c] = $w + 2 * $pad;
            if ( array_sum( $min ) >= $available )
            {
                $scale = $available / array_sum( $min );
                foreach ( $min as $c => $w )
                    $widths[$c] = $w * $scale;
            }
            else
            {
                $extra = $available - array_sum( $min );
                $wish = 0.0;
                foreach ( $widths as $c => $w )
                    $wish += max( 0.0, $w - $min[$c] );
                foreach ( $widths as $c => $w )
                    $widths[$c] = $min[$c] + ( $wish > 0 ? $extra * max( 0.0, $w - $min[$c] ) / $wish : 0 );
            }
        }
        else
        {
            // a table fills the width when it is nearly as wide anyway
            $sum = array_sum( $widths );
            if ( $sum > $available * 0.6 )
            {
                foreach ( $widths as $c => $w )
                    $widths[$c] = $w * $available / $sum;
            }
        }

        $this->space( 2 );
        $this->ensure( $lineHeight * 2 + 4 * $pad );
        foreach ( $rows as $r => $cells )
        {
            $isHead = $r < $headRows;
            $layout = $this->tableRow( $cells, $widths, $size, $pad, $isHead );
            if ( $this->y - $layout['height'] < $this->bottom() )
            {
                $this->newPage();
                if ( !$isHead && $headRows > 0 )
                {
                    for ( $h = 0; $h < $headRows; $h++ )
                        $this->drawRow( $this->tableRow( $rows[$h], $widths, $size, $pad, true ), $widths, $ctx['left'], $size, $pad, $lineHeight, true );
                }
            }
            $this->drawRow( $layout, $widths, $ctx['left'], $size, $pad, $lineHeight, $isHead );
        }
        $this->space( 10 );
    }

    protected function tableRow( array $cells, array $widths, $size, $pad, $isHead )
    {
        $lines = array();
        $most = 1;
        foreach ( $widths as $c => $w )
        {
            $atoms = isset( $cells[$c] ) ? $cells[$c]['atoms'] : array();
            if ( $isHead || ( isset( $cells[$c] ) && $cells[$c]['head'] ) )
            {
                foreach ( $atoms as $i => $atom )
                    $atoms[$i][1]['b'] = true;
            }
            $lines[$c] = $this->wrap( $atoms, max( 4.0, $w - 2 * $pad ), $size );
            $most = max( $most, count( $lines[$c] ) );
        }
        return array( 'lines' => $lines, 'cells' => $cells, 'height' => $most * $size * 1.4 + 2 * $pad );
    }

    protected function drawRow( array $layout, array $widths, $left, $size, $pad, $lineHeight, $isHead )
    {
        $height = $layout['height'];
        $top = $this->y;
        $total = array_sum( $widths );
        if ( $isHead )
            $this->fillRect( $left, $top - $height, $total, $height, self::COLORS['head'] );
        $x = $left;
        foreach ( $widths as $c => $w )
        {
            $align = isset( $layout['cells'][$c] ) ? $layout['cells'][$c]['align'] : '';
            $y = $top - $pad;
            foreach ( $layout['lines'][$c] as $line )
            {
                $lineWidth = 0.0;
                foreach ( $line as $item )
                    $lineWidth += $item['w'];
                $rtl = self::direction( array_map( function ( $item ) { return array( $item['text'], $item['style'] ); }, $line ) ) === 'rtl';
                $lx = $x + $pad;
                if ( $align === 'right' || ( $align === '' && $rtl ) )
                    $lx = $x + $w - $pad - $lineWidth;
                elseif ( $align === 'center' )
                    $lx = $x + ( $w - $lineWidth ) / 2;
                $baseline = $y - $size * 1.05;
                foreach ( self::reorder( $line, $rtl ) as $item )
                {
                    if ( $item['text'] !== '' )
                    {
                        $this->text( $lx, $baseline, $item['text'], $item['style'], $size );
                        if ( isset( $item['style']['href'] ) && $item['style']['href'][0] !== '#' )
                            $this->pdf->addLink( $item['style']['href'], $lx, $baseline - $size * 0.25, $lx + $item['w'], $baseline + $size * 0.9 );
                    }
                    $lx += $item['w'];
                }
                $y -= $lineHeight;
            }
            $x += $w;
        }
        // the grid: the line under the row and the column edges
        $this->line( $left, $top - $height, $left + $total, $top - $height, self::COLORS['line'], 0.5 );
        if ( $isHead )
            $this->line( $left, $top, $left + $total, $top, self::COLORS['line'], 0.5 );
        $this->y = $top - $height;
    }

    /* ------------------------------------------------------------------------------------------------ images */

    protected function image( DOMElement $img, array $ctx )
    {
        $alt = trim( (string)$img->getAttribute( 'alt' ) );
        $file = null;
        if ( is_callable( $this->options['image_resolver'] ) )
            $file = call_user_func( $this->options['image_resolver'], (string)$img->getAttribute( 'src' ) );
        $gd = $file !== null ? self::loadImage( $file ) : null;
        if ( $gd === null )
        {
            $text = $alt !== '' ? '[' . $this->t( 'Image: %alt', array( '%alt' => $alt ) ) . ']' : '[' . $this->t( 'Image' ) . ']';
            $this->paragraph( $this->atoms( $text, array( 'i' => true, 'color' => self::COLORS['muted'] ) ), $ctx );
            $this->space( 4 );
            return;
        }
        $pxWidth = imagesx( $gd );
        $pxHeight = imagesy( $gd );
        // 96 pixels to the inch, never wider than the column, never higher than half the page
        $w = min( $ctx['width'], $pxWidth * 0.75 );
        $h = $w * $pxHeight / $pxWidth;
        $maxHeight = ( $this->pageHeight - $this->marginTop - $this->marginBottom ) * 0.55;
        if ( $h > $maxHeight )
        {
            $h = $maxHeight;
            $w = $h * $pxWidth / $pxHeight;
        }
        // rather a somewhat smaller image than a half empty page
        $left = $this->y - $this->bottom() - 8;
        if ( $h > $left && $left >= $h * 0.6 )
        {
            $w *= $left / $h;
            $h = $left;
        }
        $this->ensure( $h + 6 );
        $x = $ctx['left'] + ( $ctx['width'] - $w ) / 2;
        $this->space( 2 );
        $this->pdf->addGdImage( $gd, $x, $this->y - $h, $w, $h );
        // a thin frame, so a white screenshot keeps its edge on white paper
        $this->pdf->setStrokeColorRGB( self::COLORS['line'][0], self::COLORS['line'][1], self::COLORS['line'][2] );
        $this->pdf->setLineStyle( 0.5 );
        $this->pdf->rectangle( $x, $this->y - $h, $w, $h );
        $this->y -= $h;
        $this->space( 6 );
    }

    /**
     * An image file as a GD image on white, at most 1600 pixels wide; null when GD cannot read it.
     *
     * @param string $file
     * @return resource|\GdImage|null
     */
    public static function loadImage( $file )
    {
        if ( !function_exists( 'imagecreatetruecolor' ) || !is_file( $file ) || !is_readable( $file ) )
            return null;
        $readers = array( 'png' => 'imagecreatefrompng', 'jpg' => 'imagecreatefromjpeg', 'jpeg' => 'imagecreatefromjpeg',
                          'gif' => 'imagecreatefromgif', 'webp' => 'imagecreatefromwebp' );
        $suffix = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
        if ( !isset( $readers[$suffix] ) || !function_exists( $readers[$suffix] ) )
            return null;
        $source = @call_user_func( $readers[$suffix], $file );
        if ( !$source )
            return null;
        $width = imagesx( $source );
        $height = imagesy( $source );
        if ( $width < 1 || $height < 1 )
            return null;
        $scale = $width > 1600 ? 1600 / $width : 1;
        $w = max( 1, (int)round( $width * $scale ) );
        $h = max( 1, (int)round( $height * $scale ) );
        $canvas = imagecreatetruecolor( $w, $h );
        imagefill( $canvas, 0, 0, imagecolorallocate( $canvas, 255, 255, 255 ) );
        imagecopyresampled( $canvas, $source, 0, 0, 0, 0, $w, $h, $width, $height );
        return $canvas;
    }

    /* ------------------------------------------------------------------------------------------------ text */

    /** Right to left scripts: Hebrew, Arabic, Syriac, Thaana, NKo, Samaritan, Mandaic and their presentation forms */
    const RTL = '/[\x{0590}-\x{08FF}\x{FB1D}-\x{FDFF}\x{FE70}-\x{FEFF}]/u';

    /** A letter of a left to right script */
    const LTR = '/[A-Za-z\x{00C0}-\x{058F}\x{0900}-\x{1FFF}\x{2C00}-\x{FB1C}]/u';

    /**
     * The direction of a paragraph: that of its first strong character.
     *
     * @param array $atoms array( text, style )
     * @return string ltr|rtl
     */
    public static function direction( array $atoms )
    {
        foreach ( $atoms as $atom )
        {
            if ( preg_match( '/' . substr( self::RTL, 1, -2 ) . '|' . substr( self::LTR, 1, -2 ) . '/u', (string)$atom[0], $m ) === 1 )
                return preg_match( self::RTL, $m[0] ) === 1 ? 'rtl' : 'ltr';
        }
        return 'ltr';
    }

    /**
     * The items of one line in the order they are drawn from left to right: a simplified bidirectional algorithm
     * on words. Right to left words have their characters reversed; runs of right to left words (with the spaces
     * and punctuation between them) are reversed in a left to right line, and in a right to left line the whole
     * line is reversed except the runs of left to right words.
     *
     * @param array $line items array( text, style, w )
     * @param bool $rtl the paragraph's direction
     * @return array
     */
    public static function reorder( array $line, $rtl )
    {
        $hasRtl = false;
        foreach ( $line as $i => $item )
        {
            $line[$i]['dir'] = preg_match( self::RTL, $item['text'] ) === 1 ? 'rtl' : ( preg_match( self::LTR . '', $item['text'] ) === 1 || preg_match( '/[0-9]/', $item['text'] ) === 1 ? 'ltr' : 'n' );
            if ( $line[$i]['dir'] === 'rtl' )
            {
                $hasRtl = true;
                $line[$i]['text'] = self::visual( $item['text'] );
            }
        }
        if ( !$hasRtl && !$rtl )
            return $line;
        $strong = $rtl ? 'ltr' : 'rtl';
        // the runs that go against the paragraph: from a strong word of the other direction to the last one
        $out = array();
        $count = count( $line );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( $line[$i]['dir'] !== $strong )
            {
                $out[] = array( $line[$i] );
                continue;
            }
            $end = $i;
            for ( $j = $i + 1; $j < $count; $j++ )
            {
                if ( $line[$j]['dir'] === $strong )
                    $end = $j;
                elseif ( $line[$j]['dir'] !== 'n' )
                    break;
            }
            $run = array_slice( $line, $i, $end - $i + 1 );
            $out[] = $rtl ? $run : array_reverse( $run );
            $i = $end;
        }
        if ( $rtl )
            $out = array_reverse( $out );
        $result = array();
        foreach ( $out as $group )
        {
            foreach ( $group as $item )
                $result[] = $item;
        }
        return $result;
    }

    /**
     * Right to left characters of a text in visual order (grapheme clusters kept together); a text without
     * any comes back unchanged. Digits and Latin inside stay left to right.
     *
     * @param string $text
     * @return string
     */
    public static function visual( $text )
    {
        if ( preg_match( self::RTL, (string)$text ) !== 1 )
            return (string)$text;
        // keep runs of digits and Latin letters as they are, reverse the order of everything
        preg_match_all( '/[0-9A-Za-z.,:%\/+-]+|\X/u', (string)$text, $m );
        return implode( '', array_reverse( $m[0] ) );
    }

    protected static function codePoint( $char )
    {
        if ( function_exists( 'mb_ord' ) )
            return (int)mb_ord( $char, 'UTF-8' );
        $u = unpack( 'N', (string)iconv( 'UTF-8', 'UCS-4BE', $char ) );
        return (int)$u[1];
    }

    /**
     * UTF-8 to Windows-1252 for the core fonts; what it cannot hold becomes its closest ASCII, else "?".
     */
    public static function toWinAnsi( $text )
    {
        $text = (string)$text;
        if ( preg_match( '/[^\x00-\x7F]/', $text ) !== 1 )
            return $text;
        $converted = @iconv( 'UTF-8', 'Windows-1252//TRANSLIT', $text );
        if ( $converted === false )
            $converted = @iconv( 'UTF-8', 'Windows-1252//IGNORE', $text );
        return $converted === false ? preg_replace( '/[^\x00-\x7F]/', '?', $text ) : $converted;
    }

    protected static function toUtf8FromWinAnsi( $text )
    {
        $converted = @iconv( 'Windows-1252', 'UTF-8', (string)$text );
        return $converted === false ? '' : $converted;
    }
}

?>
