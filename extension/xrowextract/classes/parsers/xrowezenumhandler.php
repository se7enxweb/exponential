<?php
class XroweZEnumHandler extends XrowBaseHandler
{
   public function exportAttribute( &$attribute )
   {
        return $this->escape( $attribute->metaData() );
   }
}
?>