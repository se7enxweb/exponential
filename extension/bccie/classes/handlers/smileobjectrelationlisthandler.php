<?php
/**
 * File containing the smileobjectrelationlistHandler class.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */
 
class smileobjectrelationlistHandler extends BaseHandler
{
    function exportAttribute( &$attribute, $seperationChar )
    {
        eZDebug::writeDebug( $attribute, "SMILE" );
        $content = $attribute->content();

        $relation_list = $content['relation_list'];
        eZDebug::writeDebug( $content, "SMILE" );

        $names = array();
        foreach ( $relation_list as $relation )
        {
            $object = eZContentObject::fetch( $relation['contentobject_id'] );
            $names[] = $object->name();
        }
        return $this->escape( join( " ", $names ), $seperationChar );
    }
}

?>