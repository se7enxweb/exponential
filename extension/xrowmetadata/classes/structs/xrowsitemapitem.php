<?php

abstract class xrowSitemapItem extends ezcBaseStruct
{

    abstract public function DOMElement( xrowSitemapList $sitemap );

    public function hasattribute( $name )
    {
        $classname = get_class( $this );
        $vars = get_class_vars( $classname );
        if ( array_key_exists( $name, $vars ) )
            return true;
        else
            return false;
    }

    public function attribute( $name )
    {
        return $this->$name;
    }
}
