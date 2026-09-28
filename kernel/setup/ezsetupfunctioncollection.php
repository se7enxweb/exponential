<?php
/**
 * File containing the eZSetupFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZSetupFunctionCollection ezsetupfunctioncollection.php
  \brief The class eZSetupFunctionCollection does

*/


if ( !class_exists( 'eZSetupFunctionCollection', false ) ) {
class eZSetupFunctionCollection
{
    function fetchFullVersionString()
    {
        return array( 'result' => ExponentialSDK::version() );
    }

    function fetchVersionAlias()
    {
        return array( 'result' => ExponentialSDK::version( false, true ) );
    }

    function fetchMajorVersion()
    {
        return array( 'result' => ExponentialSDK::majorVersion() );
    }

    function fetchMinorVersion()
    {
        return array( 'result' => ExponentialSDK::minorVersion() );
    }

    function fetchRelease()
    {
        return array( 'result' => ExponentialSDK::release() );

    }

    function fetchState()
    {
        return array( 'result' => ExponentialSDK::state() );
    }

    function fetchIsDevelopment()
    {
        return array( 'result' => ExponentialSDK::developmentVersion() ? true : false );
    }

    function fetchDatabaseVersion( $withRelease = true )
    {
        return array( 'result' => ExponentialSDK::databaseVersion( $withRelease ) );
    }

    function fetchDatabaseRelease()
    {
        return array( 'result' => ExponentialSDK::databaseRelease() );
    }

    function fetchEdition()
    {
        return array( 'result' => ExponentialSDK::EDITION );
    }
}
}


?>
