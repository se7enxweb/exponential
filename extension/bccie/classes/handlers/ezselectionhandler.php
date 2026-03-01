<?php
/**
 * File containing the ezselectionhandler class.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class eZSelectionHandler extends BaseHandler
{
    function exportAttribute( &$attribute, $seperationChar )
    {
        $ret = false;

        $content = $attribute->content();
        $contentObjectAttribute = $attribute->contentObjectAttribute();
        $contentClassAttribute = $attribute->contentClassAttribute();
        $contentClassAttributeContent = $contentClassAttribute->content();

        if ( isset( $contentClassAttributeContent['options'] ) )
        {
            // Build array( id => value ) and put it in cache
            $options = $contentClassAttributeContent['options'];
            $attGlobalKey = 'GlobalCollectexportAttribut_' . $contentClassAttribute->ID;

            if ( !isset( $GLOBALS[$attGlobalKey] ) )
            {
                foreach ( $options as $o )
                {
                    $GLOBALS[$attGlobalKey][$o['id']] = $o['name'];
                }
            }

            // multi-selection
            $arrayRet = array();

            foreach ( $content as $selectionID )
            {
                $arrayRet[] = $GLOBALS[$attGlobalKey][$selectionID];
            }

            $ret = implode( ', ', $arrayRet );
        }

        return $this->escape( $ret, $seperationChar );
    }
}

?>