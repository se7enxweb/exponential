<?php
/**
 * The help's safe HTML (expHelpMarkdownRenderer) of a topic field as DITA 1.3 elements, for the DITA export of help
 * maintained as content (expHelpContentDita, doc/eng-us/features/6.0/help-content.md, "DITA export").
 *
 * Block elements: p, ul, ol, li, codeblock (from pre), lq (from blockquote), simpletable (from table), note (from the
 * help's callouts, with their type), fig (from a figure with a caption). Inline elements: uicontrol (from bold: the
 * style guide writes a quoted UI label in bold), i, codeph, line-through, sup, sub, image, xref. Headings start a
 * section where the container allows one (concept and reference bodies) and become a bold paragraph elsewhere.
 * Buttons, scripts, forms and heading anchors of the screen are left out; a link the export cannot resolve keeps its
 * text.
 *
 * Links: a link to another topic arrives from the renderer as /__dita_topic__/<help_id>; the callable "topic_link"
 * decides its target (array( href, scope ) or null). Other links keep their (absolute) address as external links.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpDitaMarkup
{
    /** The address the renderer gives a link to another topic */
    const TOPIC_LINK_PREFIX = '/__dita_topic__/';

    /** Callout kinds of the renderer => DITA note types */
    const NOTE_TYPES = array( 'note' => 'note', 'tip' => 'tip', 'important' => 'important', 'warning' => 'warning',
                              'caution' => 'caution', 'danger' => 'danger', 'info' => 'note', 'success' => 'note' );

    /** @var DOMDocument the DITA document elements are created in */
    protected $out;

    /** @var callable function( help_id ): array( href, scope )|null */
    protected $topicLink;

    /**
     * @param DOMDocument $out
     * @param callable|null $topicLink function( string $helpId ): array( 'href' => ..., 'scope' => local|external )|null
     */
    public function __construct( DOMDocument $out, $topicLink = null )
    {
        $this->out = $out;
        $this->topicLink = $topicLink;
    }

    /**
     * Parses the renderer's HTML and returns the top-level nodes to convert (wrappers of the renderer unwrapped)
     *
     * @param string $html
     * @return DOMNode[]
     */
    public static function htmlNodes( $html )
    {
        $html = trim( (string)$html );
        if ( $html === '' )
            return array();
        $doc = new DOMDocument( '1.0', 'UTF-8' );
        $previous = libxml_use_internal_errors( true );
        $doc->loadHTML( '<?xml encoding="UTF-8"?><html><body><div id="__dita">' . $html . '</div></body></html>', LIBXML_NONET | LIBXML_NOERROR );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        $root = $doc->getElementById( '__dita' );
        if ( !$root )
            return array();
        $nodes = array();
        foreach ( $root->childNodes as $child )
        {
            // the renderer's body wrapper (div.a4-help-body) holds the document
            if ( $child instanceof DOMElement && $child->nodeName === 'div' && self::hasClass( $child, 'a4-help-body' ) )
            {
                foreach ( $child->childNodes as $inner )
                    $nodes[] = $inner;
                continue;
            }
            $nodes[] = $child;
        }
        return $nodes;
    }

    /**
     * Appends the HTML's content as blocks to $parent.
     *
     * @param DOMElement $parent
     * @param string $html
     * @param string $mode 'sections': headings open a section (conbody); 'refsections': every block is in a section
     *                     (refbody); 'flat': headings become a bold paragraph (context, prereq, result, cause ...)
     */
    public function appendBlocks( DOMElement $parent, $html, $mode = 'flat' )
    {
        $this->appendNodes( $parent, self::htmlNodes( $html ), $mode );
    }

    /**
     * @param DOMElement $parent
     * @param DOMNode[] $nodes
     * @param string $mode
     */
    public function appendNodes( DOMElement $parent, array $nodes, $mode = 'flat' )
    {
        $section = null;
        $inline = null;
        foreach ( $nodes as $node )
        {
            $target = $section !== null ? $section : $parent;
            if ( $node instanceof DOMElement && preg_match( '/^h[1-6]$/', $node->nodeName ) )
            {
                $inline = null;
                $text = self::text( $node );
                if ( $mode === 'flat' )
                {
                    if ( $text !== '' )
                        $target->appendChild( $this->el( 'p', array(), array( $this->el( 'b', array(), $text ) ) ) );
                    continue;
                }
                $section = $this->el( 'section' );
                if ( $text !== '' )
                    $section->appendChild( $this->el( 'title', array(), $text ) );
                $parent->appendChild( $section );
                continue;
            }
            if ( $mode === 'refsections' && $section === null && !self::isBlank( $node ) )
            {
                $section = $this->el( 'section' );
                $parent->appendChild( $section );
                $target = $section;
            }
            if ( self::isBlockElement( $node ) )
            {
                $inline = null;
                foreach ( $this->block( $node ) as $block )
                    $target->appendChild( $block );
                continue;
            }
            if ( self::isBlank( $node ) )
                continue;
            // loose inline content becomes a paragraph
            if ( $inline === null || $inline->parentNode !== $target )
            {
                $inline = $this->el( 'p' );
                $target->appendChild( $inline );
            }
            $this->inline( $node, $inline );
        }
    }

    /**
     * The steps of a task (or remedy): exactly one numbered list becomes steps (each item a step: its first line the
     * cmd, the rest the info); anything else becomes steps-informal
     *
     * @param string $html
     * @return DOMElement
     */
    public function steps( $html )
    {
        $nodes = array_values( array_filter( self::htmlNodes( $html ), function ( $n ) { return !self::isBlank( $n ); } ) );
        if ( count( $nodes ) === 1 && $nodes[0] instanceof DOMElement && $nodes[0]->nodeName === 'ol' )
        {
            $steps = $this->el( 'steps' );
            foreach ( $nodes[0]->childNodes as $li )
            {
                if ( !$li instanceof DOMElement || $li->nodeName !== 'li' )
                    continue;
                $step = $this->el( 'step' );
                $cmd = $this->el( 'cmd' );
                $step->appendChild( $cmd );
                $rest = array();
                $cmdDone = false;
                foreach ( $li->childNodes as $child )
                {
                    if ( $cmdDone )
                    {
                        $rest[] = $child;
                        continue;
                    }
                    if ( $child instanceof DOMElement && $child->nodeName === 'p' && !$cmd->hasChildNodes() )
                    {
                        $this->inlineChildren( $child, $cmd );
                        $cmdDone = true;
                        continue;
                    }
                    if ( self::isBlockElement( $child ) )
                    {
                        $cmdDone = true;
                        $rest[] = $child;
                        continue;
                    }
                    $this->inline( $child, $cmd );
                }
                self::trimText( $cmd );
                $rest = array_values( array_filter( $rest, function ( $n ) { return !self::isBlank( $n ); } ) );
                if ( $rest )
                {
                    $info = $this->el( 'info' );
                    $this->appendNodes( $info, $rest, 'flat' );
                    $step->appendChild( $info );
                }
                $steps->appendChild( $step );
            }
            if ( $steps->hasChildNodes() )
                return $steps;
        }
        $informal = $this->el( 'steps-informal' );
        $this->appendNodes( $informal, $nodes, 'flat' );
        return $informal;
    }

    /**
     * One block element of the HTML as DITA blocks
     *
     * @param DOMNode $node
     * @return DOMElement[]
     */
    public function block( DOMNode $node )
    {
        if ( !$node instanceof DOMElement )
            return array();
        $name = $node->nodeName;
        switch ( $name )
        {
            case 'p':
                if ( self::hasClass( $node, 'a4-help-callout-title' ) )
                    return array();
                $p = $this->el( 'p' );
                $this->inlineChildren( $node, $p );
                self::trimText( $p );
                return $p->hasChildNodes() ? array( $p ) : array();
            case 'ul':
            case 'ol':
                $list = $this->el( $name );
                foreach ( $node->childNodes as $li )
                {
                    if ( !$li instanceof DOMElement || $li->nodeName !== 'li' )
                        continue;
                    $item = $this->el( 'li' );
                    $this->mixed( $li, $item );
                    $list->appendChild( $item );
                }
                return $list->hasChildNodes() ? array( $list ) : array();
            case 'pre':
                return array( $this->el( 'codeblock', array(), rtrim( $node->textContent, "\n" ) ) );
            case 'blockquote':
                $lq = $this->el( 'lq' );
                $this->appendNodes( $lq, iterator_to_array( $node->childNodes ), 'flat' );
                return $lq->hasChildNodes() ? array( $lq ) : array();
            case 'table':
                return $this->table( $node );
            case 'figure':
                return $this->figure( $node );
            case 'hr':
            case 'script':
            case 'style':
            case 'button':
            case 'form':
            case 'input':
                return array();
            case 'div':
                if ( preg_match( '/\ba4-help-callout--([a-z]+)\b/', (string)$node->getAttribute( 'class' ), $m ) )
                {
                    $note = $this->el( 'note', array( 'type' => isset( self::NOTE_TYPES[$m[1]] ) ? self::NOTE_TYPES[$m[1]] : 'note' ) );
                    $this->appendNodes( $note, iterator_to_array( $node->childNodes ), 'flat' );
                    return array( $note );
                }
                // fall through: wrappers (code, table) hold the real block
            case 'section':
            case 'details':
            case 'aside':
            case 'nav':
            case 'header':
            case 'footer':
            case 'main':
            case 'article':
            case 'summary':
                $holder = $this->el( 'lq' );
                if ( $name === 'summary' )
                {
                    $text = self::text( $node );
                    return $text !== '' ? array( $this->el( 'p', array(), array( $this->el( 'b', array(), $text ) ) ) ) : array();
                }
                $this->appendNodes( $holder, iterator_to_array( $node->childNodes ), 'flat' );
                $blocks = array();
                foreach ( iterator_to_array( $holder->childNodes ) as $child )
                    $blocks[] = $holder->removeChild( $child );
                return $blocks;
            case 'dl':
                $dl = $this->el( 'dl' );
                $entry = null;
                foreach ( $node->childNodes as $child )
                {
                    if ( !$child instanceof DOMElement )
                        continue;
                    if ( $child->nodeName === 'dt' )
                    {
                        $entry = $this->el( 'dlentry' );
                        $dt = $this->el( 'dt' );
                        $this->inlineChildren( $child, $dt );
                        $entry->appendChild( $dt );
                        $dl->appendChild( $entry );
                    }
                    elseif ( $child->nodeName === 'dd' && $entry !== null )
                    {
                        $dd = $this->el( 'dd' );
                        $this->mixed( $child, $dd );
                        $entry->appendChild( $dd );
                    }
                }
                return $dl->hasChildNodes() ? array( $dl ) : array();
        }
        if ( preg_match( '/^h[1-6]$/', $name ) )
        {
            $text = self::text( $node );
            return $text !== '' ? array( $this->el( 'p', array(), array( $this->el( 'b', array(), $text ) ) ) ) : array();
        }
        $p = $this->el( 'p' );
        $this->inline( $node, $p );
        self::trimText( $p );
        return $p->hasChildNodes() ? array( $p ) : array();
    }

    /**
     * Content that may be inline or blocks (list items, cells): inline runs stay inline, blocks are converted
     */
    protected function mixed( DOMElement $from, DOMElement $to )
    {
        foreach ( $from->childNodes as $child )
        {
            if ( $child instanceof DOMElement && $child->nodeName === 'input' )
                continue;
            if ( self::isBlockElement( $child ) )
            {
                foreach ( $this->block( $child ) as $block )
                    $to->appendChild( $block );
            }
            else
                $this->inline( $child, $to );
        }
        self::trimText( $to );
    }

    protected function table( DOMElement $table )
    {
        $simple = $this->el( 'simpletable' );
        $head = null;
        $rows = array();
        $xpath = new DOMXPath( $table->ownerDocument );
        foreach ( $xpath->query( './/tr', $table ) as $tr )
        {
            $isHead = $tr->parentNode instanceof DOMElement && $tr->parentNode->nodeName === 'thead';
            $row = $this->el( $isHead ? 'sthead' : 'strow' );
            foreach ( $tr->childNodes as $cell )
            {
                if ( !$cell instanceof DOMElement || ( $cell->nodeName !== 'td' && $cell->nodeName !== 'th' ) )
                    continue;
                $entry = $this->el( 'stentry' );
                $this->mixed( $cell, $entry );
                $row->appendChild( $entry );
            }
            if ( !$row->hasChildNodes() )
                continue;
            if ( $isHead && $head === null )
                $head = $row;
            else
            {
                if ( $isHead )
                    $row = $this->renamed( $row, 'strow' );
                $rows[] = $row;
            }
        }
        if ( $head === null && !$rows )
            return array();
        if ( $head !== null )
            $simple->appendChild( $head );
        if ( !$rows )
            $simple->appendChild( $this->el( 'strow', array(), array( $this->el( 'stentry' ) ) ) );
        foreach ( $rows as $row )
            $simple->appendChild( $row );
        return array( $simple );
    }

    protected function figure( DOMElement $figure )
    {
        $fig = $this->el( 'fig' );
        $caption = '';
        foreach ( $figure->getElementsByTagName( 'figcaption' ) as $node )
            $caption = self::text( $node );
        if ( $caption !== '' )
            $fig->appendChild( $this->el( 'title', array(), $caption ) );
        foreach ( $figure->getElementsByTagName( 'img' ) as $img )
        {
            $image = $this->image( $img, 'break' );
            if ( $image !== null )
                $fig->appendChild( $image );
        }
        return $fig->hasChildNodes() ? array( $fig ) : array();
    }

    /**
     * An HTML img as a DITA image, null without a usable address
     */
    public function image( DOMElement $img, $placement = 'inline' )
    {
        $src = trim( (string)$img->getAttribute( 'src' ) );
        if ( $src === '' || !preg_match( '#^(https?://|/)#i', $src ) )
            return null;
        $image = $this->el( 'image', array( 'href' => $src, 'scope' => 'external', 'placement' => $placement ) );
        $alt = trim( (string)$img->getAttribute( 'alt' ) );
        if ( $alt !== '' )
            $image->appendChild( $this->el( 'alt', array(), $alt ) );
        return $image;
    }

    protected function inlineChildren( DOMNode $from, DOMElement $to )
    {
        foreach ( $from->childNodes as $child )
            $this->inline( $child, $to );
    }

    /**
     * One inline node of the HTML appended to $to
     *
     * @param DOMNode $node
     * @param DOMElement $to
     */
    public function inline( DOMNode $node, DOMElement $to )
    {
        if ( $node instanceof DOMText )
        {
            $text = preg_replace( '/\s+/u', ' ', $node->data );
            if ( $text !== '' )
                $to->appendChild( $this->out->createTextNode( $text ) );
            return;
        }
        if ( !$node instanceof DOMElement )
            return;
        $map = array( 'strong' => 'uicontrol', 'b' => 'uicontrol', 'em' => 'i', 'i' => 'i', 'code' => 'codeph', 'kbd' => 'codeph',
                      'del' => 'line-through', 's' => 'line-through', 'sup' => 'sup', 'sub' => 'sub' );
        $name = $node->nodeName;
        if ( in_array( $name, array( 'button', 'script', 'style', 'input', 'form' ), true ) )
            return;
        if ( $name === 'a' && self::hasClass( $node, 'a4-help-anchor' ) )
            return;
        if ( $name === 'br' )
        {
            $to->appendChild( $this->out->createTextNode( ' ' ) );
            return;
        }
        if ( $name === 'img' )
        {
            $image = $this->image( $node );
            if ( $image !== null )
                $to->appendChild( $image );
            return;
        }
        if ( $name === 'a' )
        {
            $xref = $this->xref( (string)$node->getAttribute( 'href' ) );
            if ( $xref === null || self::inside( $to, 'xref' ) )
            {
                $this->inlineChildren( $node, $to );
                return;
            }
            $this->inlineChildren( $node, $xref );
            self::trimText( $xref );
            $to->appendChild( $xref );
            return;
        }
        if ( isset( $map[$name] ) )
        {
            // codeph holds text only; a phrase inside a phrase of the same kind is flattened
            if ( $map[$name] === 'codeph' )
            {
                $to->appendChild( $this->el( 'codeph', array(), $node->textContent ) );
                return;
            }
            if ( self::inside( $to, $map[$name] ) || self::inside( $to, 'codeph' ) )
            {
                $this->inlineChildren( $node, $to );
                return;
            }
            $element = $this->el( $map[$name] );
            $this->inlineChildren( $node, $element );
            if ( $element->hasChildNodes() )
                $to->appendChild( $element );
            return;
        }
        // span, abbr, mark, block elements met inline ...: their content
        $this->inlineChildren( $node, $to );
    }

    /**
     * An xref for an address, null when the address is only an anchor of the page or cannot be used
     *
     * @param string $href
     * @return DOMElement|null
     */
    public function xref( $href )
    {
        $href = trim( $href );
        if ( $href === '' || $href[0] === '#' )
            return null;
        if ( strpos( $href, self::TOPIC_LINK_PREFIX ) === 0 )
        {
            $helpId = substr( $href, strlen( self::TOPIC_LINK_PREFIX ) );
            $hash = strpos( $helpId, '#' );
            if ( $hash !== false )
                $helpId = substr( $helpId, 0, $hash );
            $target = is_callable( $this->topicLink ) ? call_user_func( $this->topicLink, $helpId ) : null;
            if ( !is_array( $target ) || empty( $target['href'] ) )
                return null;
            return $target['scope'] === 'local'
                ? $this->el( 'xref', array( 'href' => $target['href'], 'format' => 'dita' ) )
                : $this->el( 'xref', array( 'href' => $target['href'], 'scope' => 'external', 'format' => 'html' ) );
        }
        if ( preg_match( '#^(https?://|mailto:)#i', $href ) )
            return $this->el( 'xref', array( 'href' => $href, 'scope' => 'external', 'format' => preg_match( '#^mailto:#i', $href ) ? 'email' : 'html' ) );
        if ( $href[0] === '/' )
            return $this->el( 'xref', array( 'href' => $href, 'scope' => 'external', 'format' => 'html' ) );
        return null;
    }

    /**
     * A DITA element with attributes and children (strings become text)
     *
     * @param string $name
     * @param array $attributes
     * @param string|array $children
     * @return DOMElement
     */
    public function el( $name, array $attributes = array(), $children = array() )
    {
        $element = $this->out->createElement( $name );
        foreach ( $attributes as $key => $value )
        {
            if ( $value !== null && $value !== '' )
                $element->setAttribute( $key, (string)$value );
        }
        foreach ( is_array( $children ) ? $children : array( $children ) as $child )
        {
            if ( $child instanceof DOMNode )
                $element->appendChild( $child );
            elseif ( (string)$child !== '' )
                $element->appendChild( $this->out->createTextNode( self::clean( (string)$child ) ) );
        }
        return $element;
    }

    protected function renamed( DOMElement $element, $name )
    {
        $new = $this->el( $name );
        foreach ( iterator_to_array( $element->childNodes ) as $child )
            $new->appendChild( $child );
        return $new;
    }

    /**
     * Text without characters XML 1.0 does not allow
     */
    public static function clean( $text )
    {
        return (string)preg_replace( '/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', (string)$text );
    }

    protected static function text( DOMNode $node )
    {
        $copy = $node->cloneNode( true );
        if ( $copy instanceof DOMElement )
        {
            $xpath = new DOMXPath( $node->ownerDocument );
            foreach ( iterator_to_array( $xpath->query( './/a[contains(concat(" ", @class, " "), " a4-help-anchor ")]|.//button|.//script', $copy ) ) as $drop )
                $drop->parentNode->removeChild( $drop );
        }
        return trim( preg_replace( '/\s+/u', ' ', $copy->textContent ) );
    }

    protected static function hasClass( DOMElement $element, $class )
    {
        return in_array( $class, preg_split( '/\s+/', (string)$element->getAttribute( 'class' ) ), true );
    }

    protected static function isBlank( DOMNode $node )
    {
        return ( $node instanceof DOMText && trim( $node->data ) === '' ) || $node instanceof DOMComment;
    }

    protected static function isBlockElement( DOMNode $node )
    {
        return $node instanceof DOMElement && in_array( $node->nodeName, array(
            'p', 'ul', 'ol', 'pre', 'blockquote', 'table', 'figure', 'hr', 'div', 'section', 'details', 'summary', 'aside',
            'nav', 'header', 'footer', 'main', 'article', 'dl', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true );
    }

    protected static function inside( DOMNode $node, $name )
    {
        for ( $n = $node; $n instanceof DOMElement; $n = $n->parentNode )
        {
            if ( $n->nodeName === $name )
                return true;
        }
        return false;
    }

    /**
     * Collapses leading and trailing blanks of an element's text
     */
    protected static function trimText( DOMElement $element )
    {
        $first = $element->firstChild;
        if ( $first instanceof DOMText )
        {
            $first->data = ltrim( $first->data );
            if ( $first->data === '' )
                $element->removeChild( $first );
        }
        $last = $element->lastChild;
        if ( $last instanceof DOMText )
        {
            $last->data = rtrim( $last->data );
            if ( $last->data === '' )
                $element->removeChild( $last );
        }
    }
}

?>
