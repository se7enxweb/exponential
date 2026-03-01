<?php

class XroweZStringHandler extends XrowBaseHandler
{
        public function exportAttribute( &$attribute )
        {
            return $this->escape( mb_convert_encoding( $attribute->content(), "UTF-8", mb_detect_encoding( $attribute->content() ) ) );
        }
}

?>
