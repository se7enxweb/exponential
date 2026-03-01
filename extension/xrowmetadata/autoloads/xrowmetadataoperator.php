<?php

class xrowMetaDataOperator
{
    function operatorList()
    {
        return array(
            'metadata'
        );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'metadata' => array(
                'node_id' => array(
                    'type' => 'int' ,
                    'required' => true ,
                    'default' => null
                )
            )
        );
    }

    function getPlaceholders($str)
    {
        $result = array();
        preg_match_all('/(?<=\[)([^\]]*?)(?=\])/',$str, $result);
        return $result[0];
    }

    function modify( $tpl, $operatorName, $operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        $ini= eZINI::instance('xrowmetadata.ini');
        $page_limit = $ini->variable( 'EditorInputSettings', 'MaxPageLimit' );
        $uri = eZURI::instance( eZSys::requestURI() );
        $viewParameters = $uri->UserParameters();
        if(count($viewParameters)==0)
        {
            $page_offset=0;
        }else{
            $page_offset=$viewParameters['offset'];
        }
        switch ( $operatorName )
        {
            case 'metadata':
            {
                if( isset( $namedParameters['node_id'] ) )
                {
                    $node = eZContentObjectTreeNode::fetch( $namedParameters['node_id'] );
                    if( $node instanceof eZContentObjectTreeNode )
                    {
                        $cur_parent = $node->fetchParent();
                        $replaceArray['name'] = $node->getName();
                        $replaceArray['count'] = $node->subTreeCount(array( 'IgnoreVisibility' => true ));
                        $replaceArray['count:localbusiness']= $node->subTreeCount(array( 'IgnoreVisibility' => true,
                                                                                         'ClassFilterType'=> 'include',
                                                                                         'ClassFilterArray'=> array('localbusiness')));
                        $path_array_temp = array_reverse(explode('/', $node->pathWithNames()));
                        $replaceArray['path'] = implode(' | ', $path_array_temp);
                        $replaceArray['parentname'] = $cur_parent->Name;
                        $page_count=ceil($replaceArray['count:localbusiness']/$page_limit);
                        $page_nr=($page_offset/$page_limit)+1;
                        $replaceArray['pagecount']=ezpI18n::tr( 'kernel/classes/datatypes', 'page' )." ".$page_nr."/".$page_count;

                        $operatorValue = xrowMetaDataFunctions::fetchByObject( $node->attribute( 'object' ) );
                        if($operatorValue !== false)
                        {
                            if(isset($operatorValue->title))
                            {
                                $operatorValue->title = $this->getReplaceValue( $operatorValue->title,$replaceArray );
                            }
                            if(isset($operatorValue->description))
                            {
                                $operatorValue->description = $this->getReplaceValue( $operatorValue->description,$replaceArray );
                            }
                        }
                    }
                    else
                    {
                        $operatorValue = false;
                    }
                }
                else
                {
                    $operatorValue = false;
                }
            }break;
        }
    }

    /**
     * getReplaceValue()
     *
     * @param string $operatorValue
     * @param array $replaceArray
     * @return string
     */
    protected function getReplaceValue($operatorValue,$replaceArray)
    {
        $search_value = $operatorValue;
        $placeholder_array = $this->getPlaceholders($search_value);
        if(count($placeholder_array) !== 0)
        {
            foreach($placeholder_array as $placeholder_value)
            {
                switch($placeholder_value)
                {
                    case "count":
                        $meta_title = str_replace("[count]", $replaceArray['count'], $search_value);
                        continue 2;
                    case "name":
                        $meta_title = str_replace("[name]", $replaceArray['name'], $search_value);
                        continue 2;
                    case "path":
                        $meta_title = str_replace("[path]", $replaceArray['path'], $search_value);
                        continue 2;
                    case "parentname":
                        $meta_title = str_replace("[parentname]", $replaceArray['parentname'], $search_value);
                        continue 2;
                    case "count:localbusiness":
                        $meta_title = str_replace("[count:localbusiness]", $replaceArray['count:localbusiness'], $search_value);
                        continue 2;
                    case "pagecount":
                        $meta_title = str_replace("[pagecount]", $replaceArray['pagecount'], $search_value);
                        continue 2;
                }
                $search_value = $meta_title;
            }
        }
        return $search_value;
    }
}
