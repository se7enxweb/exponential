<?php

class XroweZFloatHandler extends XrowBaseHandler
{
    public function exportAttribute( &$attribute )
    {
        return $this->escape( $attribute->content() );
    }
}

?>