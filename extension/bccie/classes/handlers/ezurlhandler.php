<?php
/**
 * File containing the ezurlhandler class.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class eZURLHandler extends BaseHandler
{

    function exportAttribute( &$attribute, $seperationChar )
    {
        $tempstring = $attribute->content() . $seperationChar . $attribute->DataText;

        return $this->escape( $tempstring, $seperationChar );
    }
}

?>