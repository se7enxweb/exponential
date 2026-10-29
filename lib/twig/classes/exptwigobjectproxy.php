<?php
/**
 * An object of the template system as a Twig template sees it: node.name reads attribute( 'name' ) through
 * ArrayAccess, which Twig tries first. Every key counts as present, so an attribute the object does not have is
 * null (as in a .tpl) and never makes Twig look for a method of that name, which the sandbox would refuse.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigObjectProxy extends expTemplateObjectProxy
{
    public function offsetExists( $offset ): bool
    {
        return is_string( $offset ) || is_int( $offset );
    }

    public function __isset( $name )
    {
        return parent::offsetExists( $name );
    }

    public function __call( $method, $arguments )
    {
        if ( !method_exists( $this->expObject(), $method ) )
            throw new BadMethodCallException( "no method $method" );
        return parent::__call( $method, $arguments );
    }
}
