<?php
/**
 * The value of a rich text field (an XML block, the type RichText): the rendered HTML, plain text, the stored XML
 * and an excerpt.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLRichText
{
    /** @var string the stored XML */
    protected $xml;

    /** @var eZContentObjectAttribute|null */
    protected $attribute;

    /** @var string|null */
    protected $text = null;

    /**
     * @param string $xml
     * @param eZContentObjectAttribute|null $attribute the field, to render the HTML (null: no HTML)
     */
    public function __construct( $xml, $attribute = null )
    {
        $this->xml = (string)$xml;
        $this->attribute = $attribute;
    }

    /** @return string */
    public function xml()
    {
        return $this->xml;
    }

    /** @return string|null the HTML the site's templates render */
    public function html()
    {
        if ( !$this->attribute instanceof eZContentObjectAttribute )
            return null;
        $content = $this->attribute->content();
        if ( !is_object( $content ) || !$content->hasAttribute( 'output' ) )
            return null;
        $output = $content->attribute( 'output' );
        return is_object( $output ) ? trim( (string)$output->attribute( 'output_text' ) ) : null;
    }

    /** @return string the text without markup: blocks on lines of their own, spaces collapsed */
    public function text()
    {
        if ( $this->text === null )
            $this->text = self::plainText( $this->xml );
        return $this->text;
    }

    /**
     * @param int $length
     * @return string at most $length characters of the text, cut at a word, with … when cut
     */
    public function excerpt( $length )
    {
        $length = max( 1, (int)$length );
        $text = preg_replace( '/\s+/u', ' ', $this->text() );
        if ( mb_strlen( $text, 'UTF-8' ) <= $length )
            return $text;
        $cut = mb_substr( $text, 0, $length, 'UTF-8' );
        $space = mb_strrpos( $cut, ' ', 0, 'UTF-8' );
        if ( $space !== false && $space > $length / 2 )
            $cut = mb_substr( $cut, 0, $space, 'UTF-8' );
        return rtrim( $cut, " ,.;:-" ) . '…';
    }

    /**
     * @param string $xml
     * @return string
     */
    public static function plainText( $xml )
    {
        $xml = (string)$xml;
        if ( $xml === '' )
            return '';
        // a line after every block, so words of two paragraphs do not run together
        $xml = preg_replace( '#</(paragraph|header|li|th|td|line|section|literal|custom)>#i', "$0\n", $xml );
        $xml = preg_replace( '#<\?xml[^>]*\?>#', '', $xml );
        $text = html_entity_decode( strip_tags( $xml ), ENT_QUOTES | ENT_XML1, 'UTF-8' );
        $lines = array();
        foreach ( preg_split( '/\n+/', $text ) as $line )
        {
            $line = trim( preg_replace( '/[ \t\r]+/u', ' ', $line ) );
            if ( $line !== '' )
                $lines[] = $line;
        }
        return implode( "\n", $lines );
    }
}

?>
