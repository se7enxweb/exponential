<?php
/**
 * File containing the ezenhancedobjectrelationhandler class.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class ezenhancedobjectrelationHandler extends BaseHandler
{

    function exportAttribute( &$attribute, $seperationChar )
    {
        $content = $attribute->content();
        $id_list = $content['id_list'];

        $ini = eZINI::instance( "export.ini" );
        if ( $ini->variable( "ezenhancedobjectrelation", "OutputRelatedObjectNames" )  !== 'false' )
        {
            $names = array();
            foreach ( $id_list as $id )
            {
                $object = eZContentObject::fetch( $id );
                $names[] = $object->name();
            }

            return $this->escape( join( " ", $names ), $seperationChar );

        }
        else
        {
            return $this->escape( join( " ", $id_list ), $seperationChar );
        }
    }
}

?>