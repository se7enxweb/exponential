<?php
/**
 * A subitems column rendered by a template named by Template=design:subitems/columns/<x>.tpl
 * in its [Column_<key>] block. The template gets $node, $column (the settings hash) and $key;
 * its output is the cell's HTML and its tag-stripped text is the value and the CSV text.
 *
 * The template name comes from the INI only, never from a request.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsTemplateColumn extends expSubitemsColumn
{
    /** @var array node id => rendered HTML, so value() and html() render once per node */
    protected $rendered = array();

    /**
     * @throws InvalidArgumentException when Template= is not a design: template path
     */
    public function __construct( $key, array $settings )
    {
        parent::__construct( $key, $settings );
        if ( !self::isValidTemplate( isset( $settings['Template'] ) ? $settings['Template'] : '' ) )
            throw new InvalidArgumentException( "Column '$key': Template must be design:<path>.tpl" );
    }

    /**
     * @param string $template
     * @return bool whether it is a design: template path without ".." segments
     */
    public static function isValidTemplate( $template )
    {
        return (bool)preg_match( '#^design:[A-Za-z0-9_/.\-]+\.tpl$#', (string)$template )
            && strpos( (string)$template, '..' ) === false;
    }

    /**
     * The template's output for a node.
     *
     * @param eZContentObjectTreeNode $node
     * @return string
     */
    public function render( eZContentObjectTreeNode $node )
    {
        $id = (int)$node->attribute( 'node_id' );
        if ( $id && isset( $this->rendered[$id] ) )
            return $this->rendered[$id];

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'node', $node );
        $tpl->setVariable( 'column', $this->settings );
        $tpl->setVariable( 'key', $this->key );
        $html = trim( (string)$tpl->fetch( $this->settings['Template'] ) );
        $tpl->unsetVariable( 'node' );
        $tpl->unsetVariable( 'column' );
        $tpl->unsetVariable( 'key' );

        if ( $id )
            $this->rendered[$id] = $html;
        return $html;
    }

    /** The tag-stripped text of the rendered template. */
    public function value( eZContentObjectTreeNode $node )
    {
        return self::oneLine( self::markupToText( $this->render( $node ) ) );
    }

    /** The rendered template itself. */
    public function html( eZContentObjectTreeNode $node, $value )
    {
        return $this->render( $node );
    }
}
