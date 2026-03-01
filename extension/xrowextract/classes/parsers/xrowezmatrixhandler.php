<?php

class XroweZMatrixExportHandler extends XrowBaseHandler
{
    function exportAttribute(&$attribute)
    {
        $content = $attribute->content();
        $rows = $content->attribute( 'rows' );
        $matrixArray = array();
        foreach( $rows['sequential'] as $row )
        {

        	$matrixArray[] = eZStringUtils::implodeStr( $row['columns'], '|' );
        	
        }
        $string = eZStringUtils::implodeStr( $matrixArray, '&' );

        return $string;
    }
}
?>
