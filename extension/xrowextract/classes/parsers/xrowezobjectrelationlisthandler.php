<?php

class XroweZObjectRelationListHandler extends XrowBaseHandler
{
	public function exportAttribute( &$attribute )
	{
		$xmlText = $attribute->attribute( 'data_text' );
        if ( trim( $xmlText ) == '' )
        {
            return "";
        }
        $rl = new eZObjectRelationListType();
        $doc = $rl->parseXML( $xmlText );
        $content = $rl->createObjectContentStructure( $doc );
        $names = array();

        foreach ($content["relation_list"] as $id)
        {
            $object=eZContentObject::fetch($id["contentobject_id"]);
            if (is_object($object))
            {
                $names[]=$object->name();
            }
        }
        $joinString = join(", ", $names);
        return $this->escape( mb_convert_encoding( $joinString, "UTF-8", mb_detect_encoding( $joinString ) ) );
        }
}
?>
