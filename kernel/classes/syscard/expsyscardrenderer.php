<?php
/**
 * File containing the expSysCardRenderer class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Turns the facts of the system card into text: the logo and the facts side by side (text()), plain lines for
 * pasting (plain()) or one JSON document (json()).
 *
 * The layout follows the width of the terminal:
 *
 *   >= 82 columns   the big "e" with the wordmark under it, then the facts
 *   >= 60           the big "e"
 *   >= 50           the small "e"
 *   >= 41           the mini "e"
 *   narrower        stacked: the mini "e" on top, the facts under it
 *
 * (the logo and a gap of three columns next to at least 36 columns of facts). Facts that do not fit are cut with
 * an ellipsis; paragraphs (exp:about) are wrapped. Severity markers: ok, info, warn and error, as colored dots and
 * signs, or + - ! x in ASCII. A fact with a hint adds a line to the Hints block. Color bars close the card: the
 * terminal's 16 colors (the palette rows of a system information tool) and a bar of the theme's gradient.
 */
class expSysCardRenderer
{
    /** The narrowest column of facts next to a logo */
    const MIN_FACTS = 36;
    /** Columns between the logo and the facts */
    const GAP = 3;
    /** The widest label column */
    const MAX_LABEL = 16;

    const SECTION_TITLES = array(
        'system' => 'System', 'php' => 'PHP', 'exponential' => 'Exponential', 'engine' => 'Engine', 'database' => 'Database',
        'site' => 'Site', 'extensions' => 'Extensions', 'siteaccesses' => 'Siteaccesses', 'settings' => 'Settings',
        'cron' => 'Cronjobs', 'queue' => 'Queue', 'caches' => 'Caches', 'help' => 'Help', 'about' => 'About',
        'project' => 'Project', 'links' => 'Links', 'contribute' => 'Contribute', 'credits' => 'Credits',
    );

    /** @var int */
    public $width = 80;
    /** @var string */
    public $depth = expSysCardColor::NONE;
    /** @var expSysCardTheme */
    public $theme;
    /** @var expSysCardLogo|null null: no logo */
    public $logo = null;
    /** @var bool */
    public $wordmark = true;
    /** @var bool */
    public $unicode = true;
    /** @var bool */
    public $colorBars = true;
    /** @var bool */
    public $hints = true;
    /** @var array section id => title (syscard.ini [Sections] Titles[]) */
    public $titles = array();

    public function __construct( ?expSysCardTheme $theme = null )
    {
        $this->theme = $theme !== null ? $theme : new expSysCardTheme( 'rainbow' );
    }

    /**
     * @param string $section
     * @return string
     */
    public function title( $section )
    {
        if ( isset( $this->titles[$section] ) && $this->titles[$section] !== '' )
            return (string)$this->titles[$section];
        return isset( self::SECTION_TITLES[$section] ) ? self::SECTION_TITLES[$section] : ucfirst( str_replace( array( '-', '_' ), ' ', $section ) );
    }

    /**
     * The facts grouped by section: the sections in the order they first appear, the facts of each in their order.
     *
     * @param expSysCardFact[] $facts
     * @return expSysCardFact[]
     */
    public static function grouped( array $facts )
    {
        $sections = array();
        foreach ( $facts as $fact )
            $sections[$fact->section][] = $fact;
        $out = array();
        foreach ( $sections as $list )
            foreach ( $list as $fact )
                $out[] = $fact;
        return $out;
    }

    /**
     * The facts that carry a hint, worst first.
     *
     * @param expSysCardFact[] $facts
     * @return expSysCardFact[]
     */
    public static function hintsOf( array $facts )
    {
        $hints = array();
        foreach ( $facts as $i => $fact )
            if ( $fact->hint !== '' )
                $hints[] = array( expSysCardFact::rank( $fact->severity ), $i, $fact );
        usort( $hints, function ( $a, $b ) { return $a[0] <=> $b[0] ?: $a[1] <=> $b[1]; } );
        return array_map( function ( $h ) { return $h[2]; }, $hints );
    }

    /**
     * Which logo goes next to the facts at the width, or how the card is stacked.
     *
     * @return array( 'size' => big|small|mini|null, 'wordmark' => bool, 'stacked' => bool, 'logo' => lines, 'logoWidth' => int )
     */
    public function layout()
    {
        $none = array( 'size' => null, 'wordmark' => false, 'stacked' => false, 'logo' => array(), 'logoWidth' => 0 );
        if ( $this->logo === null )
            return $none;
        $candidates = array();
        $wordmark = $this->wordmark ? $this->logo->wordmark() : array();
        $big = $this->logo->lines( expSysCardLogo::BIG );
        if ( !empty( $wordmark ) && !empty( $big ) )
            $candidates[] = array( expSysCardLogo::BIG, true, array_merge( $big, array( '' ), $wordmark ) );
        foreach ( array( expSysCardLogo::BIG, expSysCardLogo::SMALL, expSysCardLogo::MINI ) as $size )
        {
            $lines = $this->logo->lines( $size );
            if ( !empty( $lines ) )
                $candidates[] = array( $size, false, $lines );
        }
        if ( empty( $candidates ) )
            return $none;
        foreach ( $candidates as $candidate )
        {
            $w = expSysCardLogo::widthOf( $candidate[2] );
            if ( $w + self::GAP + self::MIN_FACTS <= $this->width )
                return array( 'size' => $candidate[0], 'wordmark' => $candidate[1], 'stacked' => false, 'logo' => $candidate[2], 'logoWidth' => $w );
        }
        $last = end( $candidates );
        return array( 'size' => $last[0], 'wordmark' => false, 'stacked' => true, 'logo' => $last[2], 'logoWidth' => expSysCardLogo::widthOf( $last[2] ) );
    }

    /**
     * The card as text for a terminal.
     *
     * @param expSysCardFact[] $facts
     * @param array $meta title, subtitle
     * @return string
     */
    public function text( array $facts, array $meta = array() )
    {
        $layout = $this->layout();
        $logo = $this->paintLogo( $layout );
        if ( $layout['stacked'] )
        {
            $factLines = $this->factLines( $facts, $meta, max( 20, $this->width ) );
            $out = array();
            foreach ( $logo as $line )
                $out[] = rtrim( $line );
            $out[] = '';
            foreach ( $factLines as $line )
                $out[] = $line;
            return implode( "\n", $out ) . "\n";
        }
        $logoWidth = $layout['logoWidth'];
        $factWidth = max( 20, $this->width - ( $logoWidth > 0 ? $logoWidth + self::GAP : 0 ) );
        $factLines = $this->factLines( $facts, $meta, $factWidth );
        $rows = max( count( $logo ), count( $factLines ) );
        $out = array();
        for ( $i = 0; $i < $rows; $i++ )
        {
            $left = '';
            if ( $logoWidth > 0 )
            {
                $raw = isset( $logo[$i] ) ? $logo[$i] : '';
                $left = $raw . str_repeat( ' ', max( 0, $logoWidth - expSysCardColor::width( $raw ) ) + self::GAP );
            }
            $right = isset( $factLines[$i] ) ? $factLines[$i] : '';
            $out[] = $right === '' ? rtrim( $left ) : $left . $right;
        }
        return implode( "\n", $out ) . "\n";
    }

    /**
     * The logo lines of a layout in their colors (and the wordmark in the gradient).
     *
     * @param array $layout
     * @param float $offset the gradient's shift (--animate)
     * @return string[]
     */
    public function paintLogo( array $layout, $offset = 0.0 )
    {
        if ( empty( $layout['logo'] ) )
            return array();
        $lines = $layout['logo'];
        if ( !$layout['wordmark'] )
            return expSysCardLogo::paint( $lines, $this->theme, $this->depth );
        $markLines = $this->logo->wordmark();
        $logoLines = array_slice( $lines, 0, count( $lines ) - count( $markLines ) - 1 );
        $painted = expSysCardLogo::paint( $logoLines, $this->theme, $this->depth );
        $painted[] = '';
        foreach ( $this->theme->paintBlock( $markLines, $this->depth, $offset ) as $line )
            $painted[] = $line;
        return $painted;
    }

    /**
     * The column of facts: the title, the sections, the hints and the color bars.
     *
     * @param expSysCardFact[] $facts
     * @param array $meta
     * @param int $width
     * @return string[]
     */
    public function factLines( array $facts, array $meta, $width )
    {
        $facts = self::grouped( $facts );
        $t = $this->theme;
        $d = $this->depth;
        $lines = array();
        $title = isset( $meta['title'] ) ? (string)$meta['title'] : 'Exponential';
        $subtitle = isset( $meta['subtitle'] ) ? (string)$meta['subtitle'] : '';
        $titleText = self::cut( $title . ( $subtitle !== '' ? ' @ ' . $subtitle : '' ), $width, $this->unicode );
        $at = strpos( $titleText, ' @ ' );
        $lines[] = $at === false ? $t->paint( $titleText, 'Accent', $d, true )
                 : $t->paint( substr( $titleText, 0, $at ), 'Accent', $d, true ) . $t->paint( ' @ ', 'Dim', $d ) . $t->paint( substr( $titleText, $at + 3 ), 'Accent', $d, true );
        $lines[] = $t->paint( str_repeat( $this->unicode ? '─' : '-', min( $width, expSysCardColor::width( $titleText ) ) ), 'Dim', $d );

        $labelWidth = 0;
        foreach ( $facts as $fact )
            if ( $fact->kind === expSysCardFact::KIND_ROW )
                $labelWidth = max( $labelWidth, expSysCardColor::width( $fact->label ) );
        $labelWidth = min( self::MAX_LABEL, $labelWidth );

        $section = null;
        foreach ( $facts as $fact )
        {
            $first = false;
            if ( $fact->section !== $section )
            {
                $section = $fact->section;
                $lines[] = '';
                $lines[] = $t->paint( self::cut( $this->title( $section ), $width, $this->unicode ), 'Accent', $d, true );
                $first = true;
            }
            if ( $fact->kind === expSysCardFact::KIND_TEXT )
            {
                if ( !$first )
                    $lines[] = '';
                if ( $fact->label !== '' )
                    $lines[] = $t->paint( self::cut( $fact->label, $width, $this->unicode ), 'Label', $d, true );
                foreach ( self::wrap( $fact->value, $width ) as $line )
                    $lines[] = $t->paint( $line, 'Value', $d );
                continue;
            }
            $label = self::cut( $fact->label, $labelWidth, $this->unicode );
            $prefix = $this->marker( $fact->severity ) . ' ' . $t->paint( $label, 'Label', $d, true ) . str_repeat( ' ', max( 0, $labelWidth - expSysCardColor::width( $label ) ) ) . ' ';
            $room = max( 4, $width - 2 - $labelWidth - 1 );
            $value = self::cut( $fact->value, $room, $this->unicode );
            $role = $fact->severity === expSysCardFact::WARN ? 'Warn' : ( $fact->severity === expSysCardFact::ERROR ? 'Error' : 'Value' );
            $lines[] = $prefix . $t->paint( $value, $role, $d );
        }

        if ( $this->hints )
        {
            $hints = self::hintsOf( $facts );
            if ( !empty( $hints ) )
            {
                $lines[] = '';
                $lines[] = $t->paint( 'Hints', 'Accent', $d, true );
                foreach ( $hints as $fact )
                {
                    $wrapped = self::wrap( $fact->hint, max( 10, $width - 2 ) );
                    foreach ( $wrapped as $i => $line )
                        $lines[] = ( $i === 0 ? $this->marker( $fact->severity ) : ' ' ) . ' ' . $line;
                }
            }
        }

        if ( $this->colorBars && $d !== expSysCardColor::NONE )
        {
            $lines[] = '';
            foreach ( $this->colorBars( $width ) as $line )
                $lines[] = $line;
        }
        return $lines;
    }

    /**
     * The palette rows (the terminal's 16 colors) and a bar of the theme's gradient.
     *
     * @param int $width
     * @return string[]
     */
    public function colorBars( $width )
    {
        if ( $this->depth === expSysCardColor::NONE )
            return array();
        $cell = $width >= 24 ? 3 : 2;
        $normal = '';
        $bright = '';
        for ( $i = 0; $i < 8; $i++ )
        {
            $normal .= "\033[" . ( 40 + $i ) . 'm' . str_repeat( ' ', $cell );
            $bright .= "\033[" . ( 100 + $i ) . 'm' . str_repeat( ' ', $cell );
        }
        $lines = array( $normal . "\033[0m", $bright . "\033[0m" );
        $stops = $this->theme->gradient();
        if ( !empty( $stops ) )
        {
            $bar = str_repeat( $this->unicode ? '▀' : '=', min( $width, 8 * $cell ) );
            $lines[] = expSysCardColor::paintGradient( $bar, $stops, $this->depth );
        }
        return $lines;
    }

    /**
     * The marker of a severity: a colored dot or sign, or + - ! x in ASCII.
     *
     * @param string $severity
     * @return string
     */
    public function marker( $severity )
    {
        $signs = $this->unicode ? array( 'ok' => '●', 'info' => '·', 'warn' => '▲', 'error' => '✖' )
                                : array( 'ok' => '+', 'info' => '-', 'warn' => '!', 'error' => 'x' );
        $roles = array( 'ok' => 'Ok', 'info' => 'Dim', 'warn' => 'Warn', 'error' => 'Error' );
        $severity = isset( $signs[$severity] ) ? $severity : 'info';
        return $this->theme->paint( $signs[$severity], $roles[$severity], $this->depth, $severity === 'error' );
    }

    /**
     * The card as plain text: no colors, no logo, ASCII marks, one "Label: value" per line (for a support request
     * or a log).
     *
     * @param expSysCardFact[] $facts
     * @param array $meta
     * @return string
     */
    public function plain( array $facts, array $meta = array() )
    {
        $title = isset( $meta['title'] ) ? (string)$meta['title'] : 'Exponential';
        if ( !empty( $meta['subtitle'] ) )
            $title .= ' @ ' . $meta['subtitle'];
        $out = array( $title, str_repeat( '=', max( 3, count( expSysCardColor::chars( $title ) ) ) ) );
        $facts = self::grouped( $facts );
        $section = null;
        foreach ( $facts as $fact )
        {
            if ( $fact->section !== $section )
            {
                $section = $fact->section;
                $out[] = '';
                $out[] = '[' . $this->title( $section ) . ']';
            }
            if ( $fact->kind === expSysCardFact::KIND_TEXT )
            {
                if ( substr( (string)end( $out ), 0, 1 ) !== '[' )
                    $out[] = '';
                if ( $fact->label !== '' )
                    $out[] = $fact->label . ':';
                foreach ( self::wrap( $fact->value, max( 40, $this->width ) ) as $line )
                    $out[] = $line;
                continue;
            }
            $mark = $fact->severity === expSysCardFact::WARN || $fact->severity === expSysCardFact::ERROR ? ' [' . $fact->severity . ']' : '';
            $out[] = $fact->label . ': ' . $fact->value . $mark;
        }
        $hints = $this->hints ? self::hintsOf( $facts ) : array();
        if ( !empty( $hints ) )
        {
            $out[] = '';
            $out[] = '[Hints]';
            foreach ( $hints as $fact )
                $out[] = $fact->severity . ': ' . $fact->hint;
        }
        return implode( "\n", $out ) . "\n";
    }

    /**
     * The card as one JSON document:
     *
     *   { "card": "card|info|about", "generated": "2026-10-20T08:00:00+00:00", "title": "...",
     *     "sections": { "<section>": [ { "key", "label", "value", "severity", "hint", "kind" }, ... ] },
     *     "hints": [ { "key", "severity", "hint" } ],
     *     "providers": [ { "id", "class", "status", "seconds", "priority", "budget" } ] }
     *
     * @param expSysCardFact[] $facts
     * @param array $runs
     * @param array $meta card, generated, title, subtitle
     * @return string
     */
    public function json( array $facts, array $runs = array(), array $meta = array() )
    {
        $sections = array();
        foreach ( $facts as $fact )
        {
            $row = $fact->toArray();
            unset( $row['section'], $row['provider'] );
            if ( $row['hint'] === '' )
                unset( $row['hint'] );
            $sections[$fact->section][] = $row;
        }
        $hints = array();
        foreach ( self::hintsOf( $facts ) as $fact )
            $hints[] = array( 'key' => $fact->key, 'severity' => $fact->severity, 'hint' => $fact->hint );
        $document = array(
            'card' => isset( $meta['card'] ) ? (string)$meta['card'] : 'card',
            'generated' => isset( $meta['generated'] ) ? (string)$meta['generated'] : date( 'c' ),
            'title' => isset( $meta['title'] ) ? (string)$meta['title'] : 'Exponential',
            'host' => isset( $meta['subtitle'] ) ? (string)$meta['subtitle'] : '',
            'sections' => empty( $sections ) ? new stdClass() : $sections,
            'hints' => $hints,
            'providers' => array_values( $runs ),
        );
        $json = json_encode( $document, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
        return ( $json === false ? '{}' : $json ) . "\n";
    }

    /**
     * A text cut to a width, with an ellipsis.
     *
     * @param string $text
     * @param int $width
     * @param bool $unicode
     * @return string
     */
    public static function cut( $text, $width, $unicode = true )
    {
        $chars = expSysCardColor::chars( (string)$text );
        if ( count( $chars ) <= $width )
            return (string)$text;
        if ( $width <= 1 )
            return implode( '', array_slice( $chars, 0, max( 0, $width ) ) );
        if ( $unicode )
            return rtrim( implode( '', array_slice( $chars, 0, $width - 1 ) ) ) . '…';
        return $width <= 3 ? implode( '', array_slice( $chars, 0, $width ) ) : rtrim( implode( '', array_slice( $chars, 0, $width - 3 ) ) ) . '...';
    }

    /**
     * A text wrapped at word boundaries to a width (a word longer than the width is cut).
     *
     * @param string $text
     * @param int $width
     * @return string[]
     */
    public static function wrap( $text, $width )
    {
        $width = max( 4, (int)$width );
        $lines = array();
        foreach ( explode( "\n", str_replace( "\r", '', (string)$text ) ) as $paragraph )
        {
            $line = '';
            foreach ( preg_split( '/\s+/u', trim( $paragraph ), -1, PREG_SPLIT_NO_EMPTY ) ?: array() as $word )
            {
                while ( count( expSysCardColor::chars( $word ) ) > $width )
                {
                    if ( $line !== '' )
                    {
                        $lines[] = $line;
                        $line = '';
                    }
                    $chars = expSysCardColor::chars( $word );
                    $lines[] = implode( '', array_slice( $chars, 0, $width ) );
                    $word = implode( '', array_slice( $chars, $width ) );
                }
                if ( $word === '' )
                    continue;
                $candidate = $line === '' ? $word : $line . ' ' . $word;
                if ( count( expSysCardColor::chars( $candidate ) ) > $width )
                {
                    $lines[] = $line;
                    $line = $word;
                }
                else
                    $line = $candidate;
            }
            $lines[] = $line;
        }
        return $lines;
    }
}
