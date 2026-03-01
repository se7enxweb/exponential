<?php

class XroweZTextHandler extends XrowBaseHandler
{
    public function exportAttribute( &$attribute )
    {
        $content = $attribute->content();
        return $this->escape( $content );
    }
}
?>
