<?php
/**
 * The tags of lib/twig, all with named parameters (name=expression ...), as the converters write them:
 *
 *   {% expinclude uri='design:x.tpl' name='ns' a=b %}                 the include function
 *   {% expset name='x' value=v scope='global' namespace='ns' %}        set with a scope
 *   {% expsetblock variable='x' scope='global' %}...{% endexpsetblock %}  set-block (expappendblock: append-block)
 *   {% expcacheblock id=1 keys=[...] expiry=... subtree_expiry=... %}...{% endexpcacheblock %}  cache-block
 *   {% exprunonce id=1 %}...{% endexprunonce %}                        run-once
 *   {% expwhile condition %}...{% endexpwhile %}                       while/do, at most WhileLoopLimit rounds
 *   {% expundefall %}                                                  undef without parameters
 *   {% <block function> a=b %}...{% end<block function> %}             a template function with children
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigTagTokenParser extends \Twig\TokenParser\AbstractTokenParser
{
    /** tag => kind of the fixed tags */
    const TAGS = array(
        'expinclude' => 'include', 'expset' => 'set', 'expsetblock' => 'setblock', 'expappendblock' => 'appendblock',
        'expcacheblock' => 'cacheblock', 'exprunonce' => 'runonce', 'expwhile' => 'while', 'expundefall' => 'undefall',
    );

    /** kinds with a body and an end tag */
    const WITH_BODY = array( 'setblock' => true, 'appendblock' => true, 'cacheblock' => true, 'runonce' => true, 'while' => true, 'blockfunction' => true );

    /** @var string */
    private $tag;

    /** @var string */
    private $kind;

    public function __construct( $tag, $kind = null )
    {
        $this->tag = $tag;
        $this->kind = $kind !== null ? $kind : ( isset( self::TAGS[$tag] ) ? self::TAGS[$tag] : 'blockfunction' );
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function parse( \Twig\Token $token ): \Twig\Node\Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $nodes = array();
        if ( $this->kind === 'while' )
            $nodes['condition'] = $this->parser->parseExpression();
        else
            $nodes['params'] = $this->parseParameters();
        $stream->expect( \Twig\Token::BLOCK_END_TYPE );
        if ( isset( self::WITH_BODY[$this->kind] ) )
        {
            $end = 'end' . $this->tag;
            $nodes['body'] = $this->parser->subparse( function ( \Twig\Token $t ) use ( $end ) { return $t->test( $end ); }, true );
            $stream->expect( \Twig\Token::BLOCK_END_TYPE );
        }
        return new expTwigTagNode( $nodes, array( 'kind' => $this->kind, 'name' => $this->tag ), $lineno );
    }

    /**
     * name=expression pairs up to the end of the tag.
     */
    private function parseParameters()
    {
        $stream = $this->parser->getStream();
        $params = array();
        while ( !$stream->test( \Twig\Token::BLOCK_END_TYPE ) )
        {
            $nameToken = $stream->getCurrent();
            if ( !$nameToken->test( \Twig\Token::NAME_TYPE ) && !( $nameToken->test( \Twig\Token::OPERATOR_TYPE ) && preg_match( '/^[a-z_][a-z0-9_]*$/i', $nameToken->getValue() ) ) )
                throw new \Twig\Error\SyntaxError( sprintf( 'The "%s" tag takes name=value parameters.', $this->tag ), $nameToken->getLine(), $stream->getSourceContext() );
            $stream->next();
            $stream->expect( \Twig\Token::OPERATOR_TYPE, '=' );
            $params[$nameToken->getValue()] = $this->parser->parseExpression();
        }
        return new \Twig\Node\Nodes( $params );
    }
}
