<?php
class XroweZDateHandler extends XrowBaseHandler
{
    public function exportAttribute( &$attribute )
    {
        return $this->escape( strftime( '%Y-%m-%d', $attribute->metaData() ) );
    }
}
?>