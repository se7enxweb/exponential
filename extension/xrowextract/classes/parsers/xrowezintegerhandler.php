<?php

class XroweZIntegerHandler extends XrowBaseHandler
{
    public function exportAttribute(&$attribute )
    {
        return $this->escape( $attribute->content() );
    }
}

?>