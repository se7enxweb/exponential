<?php

class eZBirthdayCsvHandler extends BaseHandler
{
    public function exportAttribute( &$attribute )
    {
        return $this->escape( $attribute->attribute( 'data_text' ) );
    }
}

?>
