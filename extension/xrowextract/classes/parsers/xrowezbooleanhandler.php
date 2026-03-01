<?php
class XroweZBooleanHandler extends XrowBaseHandler
{
    public $encloseChar = "'";
    public $separationChar = ',';

    public function exportAttribute( &$attribute )
    {
        if ( $attribute->content() )
            return $this->escape( '1' );
        else
            return $this->escape( '0' );
    }
}
?>
