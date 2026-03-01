<?php
/**
 * File containing the ezcountryhandler class.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class eZCountryHandler extends BaseHandler
{
    function exportAttribute( &$attribute, $seperationChar )
    {
        $ret = false;
        $objectAttribute = $attribute->contentObjectAttribute();
        $objectAttributeContent = $attribute->content();
        if ( $objectAttributeContent['value'] )
        {
            $ret = $objectAttributeContent['value'][$attribute->DataText]['Name'];
        }

        return $this->escape( $ret, $seperationChar );
    }
}

?>