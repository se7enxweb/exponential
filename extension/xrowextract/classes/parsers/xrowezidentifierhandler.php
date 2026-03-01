<?php

class XroweZIdentifierHandler extends XrowBaseHandler
{
    function exportAttribute( &$attribute )
    {
        return $this->escape( $attribute->content() );
    }
}

?>