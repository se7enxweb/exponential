<?php
/**
 * File containing the ExponentialSDK class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \brief contains the Exponential SDK version.

  Until 6.0.15 this class was called eZPublishSDK. That name still works:
  lib/ezpublishsdk.php declares it as an empty subclass, and this file loads
  it at the end, so code that includes lib/version.php directly (instead of
  going through the autoloader) sees both names.
*/

class ExponentialSDK
{
    const VERSION_MAJOR = 6;
    const VERSION_MINOR = 0;
    const VERSION_RELEASE = 15;
    const VERSION_STATE = 'stable';
    const VERSION_DEVELOPMENT = false;
    const VERSION_ALIAS = '6.0';
    const EDITION = 'Exponential';

    /*!
      \return the SDK version as a string
      \param withRelease If true the release version is appended
      \param withAlias If true the alias is used instead
    */
    static function version( $withRelease = true, $asAlias = false, $withState = true )
    {
        if ( $asAlias )
        {
            $versionText = self::alias();
            if ( $withState && self::state() )
                $versionText .= "-" . self::state();
        }
        else
        {
            $versionText = self::majorVersion() . '.' . self::minorVersion();
//            $development = self::developmentVersion();
//            if ( $development !== false )
//                $versionText .= '.' . $development;
            if ( $withRelease )
                $versionText .= "." . self::release();
            if ( $withState )
                $versionText .= self::state();
        }
        return $versionText;
    }

    /*!
     \return the major version
    */
    static function majorVersion()
    {
        return self::VERSION_MAJOR;
    }

    /*!
     \return the minor version
    */
    static function minorVersion()
    {
        return self::VERSION_MINOR;
    }

    /*!
     \return the state of the release
    */
    static function state()
    {
        return self::VERSION_STATE;
    }

    /*!
     \return the development version or \c false if this is not a development version
    */
    static function developmentVersion()
    {
        return self::VERSION_DEVELOPMENT;
    }

    /*!
     \return the release number
    */
    static function release()
    {
        return self::VERSION_RELEASE;
    }

    /*!
     \return the alias name for the release, this is often used for beta releases and release candidates.
    */
    static function alias()
    {
        return self::VERSION_ALIAS;
    }

    /*!
      \return the version of the database.
      \param withRelease If true the release version is appended
    */
    static function databaseVersion( $withRelease = true )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT value as version FROM ezsite_data WHERE name='ezpublish-version'" );
        $version = false;
        if ( count( $rows ) > 0 )
        {
            $version = $rows[0]['version'];
            if ( $withRelease )
            {
                $release = self::databaseRelease();
                $version .= '-' . $release;
            }
        }
        return $version;
    }

    /*!
      \return the release of the database.
    */
    static function databaseRelease()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT value as release FROM ezsite_data WHERE name='ezpublish-release'" );
        $release = false;
        if ( count( $rows ) > 0 )
        {
            $release = $rows[0]['release'];
        }
        return $release;
    }
}

// Former name, kept for compatibility (see lib/ezpublishsdk.php).
require_once __DIR__ . '/ezpublishsdk.php';

?>
