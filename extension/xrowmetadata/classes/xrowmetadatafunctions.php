<?php

class xrowMetaDataFunctions
{
    static function fetchByNode( eZContentObjectTreeNode $node )
    {
        $attributes = $node->attribute( 'data_map' );
        foreach ( $attributes as $attribute )
        {
            if ( $attribute->DataTypeString == 'xrowmetadata' and $attribute->hasContent() )
            {
                return $attribute->content();
            }
        }
        return false;
    }

    static function fetchByObject( eZContentObject $object )
    {
        $attributes = $object->fetchDataMap();
        foreach ( $attributes as $attribute )
        {
            if ( $attribute->DataTypeString == 'xrowmetadata' and $attribute->hasContent() )
            {
                return $attribute->content();
            }
        }
        return false;
    }
}
