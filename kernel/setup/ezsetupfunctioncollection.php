<?php
/**
 * File containing the eZSetupFunctionCollection class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

    /**
     * The extension counts of eZExtension::extensionCounts(): on_disk, global, here, anywhere, inactive, missing.
     * Without $allSiteAccesses the kinds that read every siteaccess's settings (anywhere, inactive) are null.
     */
    function fetchExtensionCounts( $allSiteAccesses = false )
    {
        return array( 'result' => eZExtension::extensionCounts( null, $allSiteAccesses ? null : false ) );
    }

    /**
     * The cronjob pauses for the Setup overview (expCronjobPause): enabled (cronjob.ini [PauseSettings] Pause), all
     * (every part is paused) and count (the parts paused one by one, the scripts of [CronjobSettings] included). One
     * look into the directory of the state files; no rights of its own, so it is drawn only where the user may open
     * Setup > Cronjobs.
     */
    function fetchCronjobPauses()
    {
        if ( !expCronjobPause::enabled() )
            return array( 'result' => array( 'enabled' => false, 'all' => false, 'count' => 0 ) );
        $paused = expCronjobPause::pausedParts();
        $all = in_array( expCronjobPause::ALL, $paused, true );
        return array( 'result' => array( 'enabled' => true,
                                         'all' => $all,
                                         'count' => count( $paused ) - ( $all ? 1 : 0 ) ) );
    }
}
}


?>
