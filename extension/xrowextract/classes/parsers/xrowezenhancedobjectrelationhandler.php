<?php

class XroweZenhancedobjectrelationHandler extends XrowBaseHandler
{
    public function exportAttribute(&$attribute)
    {
        //var_export($attribute->content());
        $content=$attribute->content();
        $id_list=$content['id_list'];

        $ini = eZINI::instance( "csv.ini" );
        if ($ini->variable( "ezenhancedobjectrelation", "OutputRelatedObjectNames" ))
        {
            $names=array();
            foreach ($id_list as $id)
            {
                $object=eZContentObject::fetch($id);
                if (is_object($object))
                    $names[]=$object->name();
            }
            return $this->escape( join(" ", $names) );
        }
        else
        {
            return $this->escape( join(" ", $id_list ) );
        }
    }
}

?>
