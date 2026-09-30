<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPackageLicense ezpackagelicense.php
  \ingroup package
  \brief The licenses a package can be given, as configured in package.ini [LicenseSettings]

  The package creation wizards offer the licenses of this list as a strict choice; the
  identifier of the chosen license is what a package stores in its package.xml (<licence>).
  A stored value that is not in the list (a package made before the choice existed, or
  one made elsewhere) is described as it is, without a name or link.

  Every entry is an array with the keys:
  - identifier   the stored value, an SPDX identifier where one exists
  - name         the official name of the license
  - url          the official page of the license, or false
  - description  a short text, or false
  - group        the identifier of the group it is listed under
*/

class eZPackageLicense
{
    /*!
     \return the configured licenses, identifier => entry, in the configured order.
             Entries without a [License_<identifier>] block or without a Name are left out.
    */
    static function licenseList()
    {
        $ini = eZINI::instance( 'package.ini' );
        $list = array();
        $identifiers = $ini->hasVariable( 'LicenseSettings', 'LicenseList' ) ? $ini->variable( 'LicenseSettings', 'LicenseList' ) : array();
        foreach ( $identifiers as $identifier )
        {
            $identifier = trim( $identifier );
            if ( $identifier === '' or isset( $list[$identifier] ) )
                continue;
            $block = 'License_' . $identifier;
            if ( !$ini->hasGroup( $block ) or !$ini->hasVariable( $block, 'Name' ) or trim( $ini->variable( $block, 'Name' ) ) === '' )
            {
                eZDebug::writeWarning( "License '$identifier' in package.ini LicenseList has no [$block] block with a Name, left out", __METHOD__ );
                continue;
            }
            $list[$identifier] = array( 'identifier' => $identifier,
                                        'name' => trim( $ini->variable( $block, 'Name' ) ),
                                        'url' => self::variable( $ini, $block, 'URL' ),
                                        'description' => self::variable( $ini, $block, 'Description' ),
                                        'group' => self::variable( $ini, $block, 'Group' ) );
        }
        return $list;
    }

    /*!
     \return the licenses grouped for the drop-down: a list of groups, each an array with
             identifier, name (translated) and licenses (a list of entries). Groups follow
             GroupList; licenses of an unlisted group come last, under "Other licenses".
             Empty groups are left out.
    */
    static function groupedList()
    {
        $ini = eZINI::instance( 'package.ini' );
        $groups = array();
        $groupIdentifiers = $ini->hasVariable( 'LicenseSettings', 'GroupList' ) ? $ini->variable( 'LicenseSettings', 'GroupList' ) : array();
        foreach ( $groupIdentifiers as $groupIdentifier )
        {
            $groupIdentifier = trim( $groupIdentifier );
            if ( $groupIdentifier === '' or isset( $groups[$groupIdentifier] ) )
                continue;
            $name = self::variable( $ini, 'LicenseGroup_' . $groupIdentifier, 'Name' );
            $groups[$groupIdentifier] = array( 'identifier' => $groupIdentifier,
                                               'name' => ezpI18n::tr( 'kernel/package', $name ? $name : $groupIdentifier ),
                                               'licenses' => array() );
        }
        $other = array( 'identifier' => 'other',
                        'name' => ezpI18n::tr( 'kernel/package', 'Other licenses' ),
                        'licenses' => array() );
        foreach ( self::licenseList() as $license )
        {
            if ( $license['group'] and isset( $groups[$license['group']] ) )
                $groups[$license['group']]['licenses'][] = $license;
            else
                $other['licenses'][] = $license;
        }
        $groups[] = $other;
        $result = array();
        foreach ( $groups as $group )
        {
            if ( count( $group['licenses'] ) > 0 )
                $result[] = $group;
        }
        return $result;
    }

    /*!
     \return the identifier of the configured license \a $value stands for (an identifier or an
             alias from AliasList, matched exactly), or \c false when it is not a configured license.
    */
    static function normalize( $value )
    {
        if ( !is_string( $value ) )
            return false;
        $value = trim( $value );
        if ( $value === '' )
            return false;
        $list = self::licenseList();
        if ( isset( $list[$value] ) )
            return $value;
        $ini = eZINI::instance( 'package.ini' );
        $aliases = $ini->hasVariable( 'LicenseSettings', 'AliasList' ) ? $ini->variable( 'LicenseSettings', 'AliasList' ) : array();
        if ( isset( $aliases[$value] ) and isset( $list[trim( $aliases[$value] )] ) )
            return trim( $aliases[$value] );
        return false;
    }

    /*!
     \return \c true if \a $value is a configured license or one of its aliases.
    */
    static function isAllowed( $value )
    {
        return self::normalize( $value ) !== false;
    }

    /*!
     \return the entry of the configured license \a $value stands for, or \c false.
    */
    static function fetch( $value )
    {
        $identifier = self::normalize( $value );
        if ( $identifier === false )
            return false;
        $list = self::licenseList();
        return $list[$identifier];
    }

    /*!
     \return the identifier the wizards preselect: DefaultLicense when it is a configured
             license, else the first configured license, else \c false.
    */
    static function defaultIdentifier()
    {
        $ini = eZINI::instance( 'package.ini' );
        if ( $ini->hasVariable( 'LicenseSettings', 'DefaultLicense' ) )
        {
            $identifier = self::normalize( $ini->variable( 'LicenseSettings', 'DefaultLicense' ) );
            if ( $identifier !== false )
                return $identifier;
            eZDebug::writeWarning( 'package.ini [LicenseSettings] DefaultLicense is not in LicenseList, using the first license', __METHOD__ );
        }
        $list = self::licenseList();
        if ( count( $list ) == 0 )
            return false;
        reset( $list );
        return key( $list );
    }

    /*!
     \return how a stored license value \a $stored is shown: the entry of the configured license
             it stands for plus 'known' => true and 'stored' => the stored value, or, for a value
             that is not configured, an entry with the stored text as name, no URL and
             'known' => false. \c false when nothing is stored.
    */
    static function describe( $stored )
    {
        if ( !is_string( $stored ) or trim( $stored ) === '' )
            return false;
        $license = self::fetch( $stored );
        if ( $license )
        {
            $license['known'] = true;
            $license['stored'] = $stored;
            return $license;
        }
        return array( 'identifier' => $stored,
                      'name' => $stored,
                      'url' => false,
                      'description' => false,
                      'group' => false,
                      'known' => false,
                      'stored' => $stored );
    }

    /*!
     \private
     \return the trimmed value of \a $name in \a $block, or \c false when it is missing or empty.
    */
    private static function variable( $ini, $block, $name )
    {
        if ( !$ini->hasVariable( $block, $name ) )
            return false;
        $value = trim( $ini->variable( $block, $name ) );
        return $value === '' ? false : $value;
    }
}

?>
