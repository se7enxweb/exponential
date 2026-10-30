<?php
/**
 * An object of the template system (anything with attribute() and hasAttribute(): content objects, nodes, data
 * maps ...) as an engine template sees it: $node.name in Smarty and node.name in Twig read attribute( 'name' ),
 * and the result is wrapped again (expTemplateValues::wrap()), so a chain such as node.data_map.title.content works.
 * Engines subclass it where they need their own lookup rules (expSmartyObjectProxy, expTwigObjectProxy).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateObjectProxy implements ArrayAccess
{
    /** @var object */
    private $object;

    public function __construct( $object )
    {
        $this->object = $object;
    }

    /**
     * @return object the object of the template system
     */
    public function expObject()
    {
        return $this->object;
    }

    public function offsetExists( $offset ): bool
    {
        return $this->object->hasAttribute( $offset ) && $this->object->attribute( $offset ) !== null;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet( $offset )
    {
        if ( !$this->object->hasAttribute( $offset ) )
            return null;
        return expTemplateValues::wrap( $this->object->attribute( $offset ), static::class );
    }

    public function offsetSet( $offset, $value ): void
    {
    }

    public function offsetUnset( $offset ): void
    {
    }

    public function __get( $name )
    {
        return $this->offsetGet( $name );
    }

    public function __isset( $name )
    {
        return $this->offsetExists( $name );
    }

    public function __call( $method, $arguments )
    {
        return expTemplateValues::wrap( call_user_func_array( array( $this->object, $method ), array_map( array( 'expTemplateValues', 'unwrap' ), $arguments ) ), static::class );
    }

    public function __toString()
    {
        // the iterator of {section var=}: printed, it is its item
        if ( $this->object instanceof eZTemplateSectionIterator )
        {
            $item = $this->object->templateValue();
            return is_scalar( $item ) || ( is_object( $item ) && method_exists( $item, '__toString' ) ) ? (string)$item : '';
        }
        return method_exists( $this->object, '__toString' ) ? (string)$this->object : '';
    }
}
