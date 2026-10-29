<?php
/**
 * File containing the expConfigPhpCatalogue class.
 *
 * The settings config.php-RECOMMENDED documents: every commented "// define( 'NAME', value );" line of the file,
 * with the value it proposes, the type of that value, the section it is in (the heading of the comment block above
 * it, such as CLUSTERING) and its description (the comment directly above it). exp:config:list shows them next to
 * the values config.php has; exp:config:set types a value by them and adds only these names unless
 * --allow-unknown. The file is read as PHP tokens, never run.
 *
 * Guide: doc/eng-us/guides/setup-files-from-the-console.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expConfigPhpCatalogue
{
    /** @var array name => setting, see settings() */
    protected $settings = array();

    /**
     * @param string $code the content of config.php-RECOMMENDED
     */
    public function __construct( $code )
    {
        $this->settings = self::parse( (string)$code );
    }

    /**
     * The catalogue of a file; an empty one when the file does not exist.
     *
     * @param string $file
     * @return expConfigPhpCatalogue
     */
    public static function fromFile( $file )
    {
        return new self( is_file( $file ) ? (string)file_get_contents( $file ) : '' );
    }

    /**
     * The documented settings in the order of the file: name => array( name, raw (the proposed value as written),
     * type, value, section, description, line, conditional (the commented define() is part of a commented if) ).
     *
     * @return array
     */
    public function settings()
    {
        return $this->settings;
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function setting( $name )
    {
        return isset( $this->settings[$name] ) ? $this->settings[$name] : null;
    }

    /**
     * @param string $code
     * @return array see settings()
     */
    public static function parse( $code )
    {
        if ( $code === '' )
            return array();
        $settings = array();
        // the commented define() lines (the editor knows commented conditions), each with the last block comment
        // before it as its description and the last heading before it as its section
        $editor = new expConfigPhpEditor( $code );
        $blocks = self::commentBlocks( $code );
        foreach ( $editor->entries() as $entry )
        {
            if ( $entry['state'] !== 'commented' || isset( $settings[$entry['name']] ) )
                continue;
            $section = '';
            $description = '';
            foreach ( $blocks as $block )
            {
                if ( $block['end'] > $entry['start'] )
                    break;
                if ( $block['title'] !== '' )
                    $section = $block['title'];
                $description = $block['text'];
            }
            $settings[$entry['name']] = array(
                'name' => $entry['name'],
                'raw' => $entry['raw'],
                'type' => $entry['type'],
                'value' => $entry['value'],
                'section' => $section,
                'description' => $description,
                'line' => $entry['line'],
                'conditional' => $entry['conditional'],
            );
        }
        return $settings;
    }

    /**
     * The block comments of the code: start, end (offsets), title (a heading line underlined with dashes), text.
     *
     * @param string $code
     * @return array
     */
    protected static function commentBlocks( $code )
    {
        $blocks = array();
        foreach ( expConfigPhpEditor::tokens( $code ) as $t )
        {
            if ( $t[0] === T_DOC_COMMENT || ( $t[0] === T_COMMENT && substr( $t[1], 0, 2 ) === '/*' ) )
            {
                list( $title, $text ) = self::commentText( $t[1] );
                $blocks[] = array( 'start' => $t[3], 'end' => $t[3] + strlen( $t[1] ), 'title' => $title, 'text' => $text );
            }
        }
        return $blocks;
    }

    /**
     * The heading and the text of a block comment: the markers and the leading "*" of each line taken off, the
     * common indentation removed. A heading is a line followed by a line of dashes ("CLUSTERING" / "----------").
     *
     * @param string $comment
     * @return array title, text
     */
    public static function commentText( $comment )
    {
        $body = preg_replace( '#^/\*\*?#', '', $comment );
        $body = preg_replace( '#\*/$#', '', $body );
        $lines = explode( "\n", str_replace( "\r", '', $body ) );
        foreach ( $lines as $i => $line )
            $lines[$i] = rtrim( preg_replace( '/^\s*\*(?!\/)\s?/', '', $line ) );
        $lines = self::dedent( $lines );
        while ( $lines && trim( $lines[0] ) === '' )
            array_shift( $lines );
        while ( $lines && trim( $lines[count( $lines ) - 1] ) === '' )
            array_pop( $lines );
        $title = '';
        if ( count( $lines ) >= 2 && preg_match( '/^-{3,}$/', trim( $lines[1] ) ) )
        {
            $title = trim( $lines[0] );
            $lines = array_slice( $lines, 2 );
            $lines = self::dedent( $lines );
        }
        while ( $lines && trim( $lines[0] ) === '' )
            array_shift( $lines );
        return array( $title, implode( "\n", $lines ) );
    }

    /** @return array the lines without the indentation they have in common */
    protected static function dedent( array $lines )
    {
        $indent = null;
        foreach ( $lines as $line )
        {
            if ( trim( $line ) === '' )
                continue;
            $width = strlen( $line ) - strlen( ltrim( $line ) );
            $indent = $indent === null ? $width : min( $indent, $width );
        }
        if ( !$indent )
            return $lines;
        return array_map( function ( $line ) use ( $indent ) { return (string)substr( $line, $indent ); }, $lines );
    }

    /**
     * The first sentence of a description, on one line (for the text listing).
     *
     * @param string $description
     * @param int $width at most this many characters
     * @return string
     */
    public static function summary( $description, $width = 100 )
    {
        // the lines up to the end of the first sentence; a line without a full stop followed by a line that starts
        // with a capital is a sentence of its own ("Cluster storage user" / "Required")
        $taken = array();
        $lines = explode( "\n", trim( (string)$description ) );
        foreach ( $lines as $i => $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                break;
            $taken[] = $line;
            if ( preg_match( '/[.!?]$/', $line ) )
                break;
            $next = isset( $lines[$i + 1] ) ? trim( $lines[$i + 1] ) : '';
            if ( $next !== '' && preg_match( '/^[A-Z]/', $next ) && !preg_match( '/[,;:(]$/', $line ) )
                break;
        }
        $text = trim( preg_replace( '/\s+/', ' ', implode( ' ', $taken ) ) );
        if ( preg_match( '/^(.+?[.!?])(\s|$)/', $text, $m ) )
            $text = $m[1];
        if ( strlen( $text ) > $width )
            $text = rtrim( substr( $text, 0, $width - 3 ) ) . '...';
        return $text;
    }
}
