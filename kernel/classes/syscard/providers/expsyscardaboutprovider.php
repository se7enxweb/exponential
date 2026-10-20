<?php
/**
 * File containing the expSysCardAboutProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The project information of exp:about: what Exponential is, the Foundation and 7x, Velocity, the license, the links,
 * how to contribute, the partner program and the credits. The text is share/syscard/about.txt (or syscard.ini
 * [About] TextFile), written from the documentation; this class only reads it:
 *
 *   @ <section> | <heading> | from <source>
 *   a paragraph, on as many lines as it needs
 *   - Label: value          (a row)
 */
class expSysCardAboutProvider extends expSysCardBaseProvider
{
    protected $id = 'about';
    protected $section = 'about';
    protected $modes = array( expSysCardContext::MODE_ABOUT );
    protected $priority = 95;
    protected $budget = 0.5;

    public function collect( expSysCardContext $context )
    {
        $file = (string)$context->ini( 'syscard.ini', 'About', 'TextFile', 'share/syscard/about.txt' );
        $text = $context->file( $file !== '' ? $file : 'share/syscard/about.txt', 262144 );
        if ( $text === null )
            return array( $this->fact( 'missing', 'About', 'n/a (' . basename( $file ) . ' not found)' ) );
        $facts = array();
        foreach ( self::parse( $text ) as $n => $block )
        {
            $paragraph = trim( implode( ' ', $block['lines'] ) );
            if ( $paragraph !== '' )
                $facts[] = $this->text( 'block' . $n, $paragraph, $block['heading'], $block['section'] );
            foreach ( $block['rows'] as $i => $row )
                $facts[] = $this->fact( 'block' . $n . '.' . $i, $row[0], $row[1], expSysCardFact::INFO, '', $block['section'] );
        }
        return $facts;
    }

    /**
     * The blocks of an about text.
     *
     * @param string $text
     * @return array list of array( 'section', 'heading', 'source', 'lines' => list, 'rows' => list of array( label, value ) )
     */
    public static function parse( $text )
    {
        $blocks = array();
        $current = null;
        foreach ( explode( "\n", str_replace( "\r", '', (string)$text ) ) as $line )
        {
            $trimmed = trim( $line );
            if ( $trimmed === '' || $trimmed[0] === '#' )
                continue;
            if ( $trimmed[0] === '@' )
            {
                if ( $current !== null )
                    $blocks[] = $current;
                $parts = array_map( 'trim', explode( '|', substr( $trimmed, 1 ) ) );
                $current = array( 'section' => strtolower( $parts[0] !== '' ? $parts[0] : 'about' ),
                                  'heading' => isset( $parts[1] ) ? $parts[1] : '',
                                  'source' => isset( $parts[2] ) ? (string)preg_replace( '/^from\s+/', '', $parts[2] ) : '',
                                  'lines' => array(), 'rows' => array() );
                continue;
            }
            if ( $current === null )
                $current = array( 'section' => 'about', 'heading' => '', 'source' => '', 'lines' => array(), 'rows' => array() );
            if ( preg_match( '/^-\s+([^:]+):\s+(.+)$/', $trimmed, $m ) )
                $current['rows'][] = array( trim( $m[1] ), trim( $m[2] ) );
            else
                $current['lines'][] = $trimmed;
        }
        if ( $current !== null )
            $blocks[] = $current;
        return $blocks;
    }
}
