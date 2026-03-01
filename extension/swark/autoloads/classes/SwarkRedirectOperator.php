<?php
//
// Swark - extension for eZ Publish
// Author: Jan Kudlicka <jk@seeds.no>
// Copyright (C) 2008 Seeds Consulting AS, http://www.seeds.no/
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of version 2.0 of the GNU General
// Public License as published by the Free Software Foundation.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
// MA 02110-1301, USA.
//

class SwarkRedirectOperator extends SwarkOperator
{
    function __construct()
    {
        parent::__construct( 'redirect', 'url', 'status=302' );
    }

    static function execute( $operatorValue, $namedParameters )
    {
        include_once( 'lib/ezutils/classes/ezsys.php' );
        include_once( 'lib/ezutils/classes/ezhttptool.php' );
        include_once( 'lib/ezutils/classes/ezexecution.php' );

        $redirectUri = $namedParameters['url'];
        if ( is_null( $redirectUri ) )
        {
            $redirectUri = $operatorValue;
        }

        // if $redirectUri is not starting with scheme://
        if ( !preg_match( '#^\w+://#', $redirectUri ) )
        {
            // path to eZ Publish index
            $indexDir = eZSys::indexDir();

            /* We need to make sure we have one
               and only one slash at the concatenation point
               between $indexDir and $redirectUri. */
            $redirectUri = rtrim( $indexDir, '/' ) . '/' . ltrim( $redirectUri, '/' );
        }

        // Redirect to $redirectUri by returning given status code and exit.
        eZHTTPTool::redirect( $redirectUri, array(), $namedParameters['status'] );
        eZExecution::cleanExit();
    }
}

?>