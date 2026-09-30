<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPackageVersion ezpackageversion.php
  \ingroup package
  \brief Understands, validates and compares package versions, Semantic Versioning 2.0.0 first

  A package has a version number (package.xml <version><number>) and a release number
  (<version><release>, 1 for a new package). eZPackage::getVersion() joins them as
  "<number>-<release>", e.g. "1.0.0-1"; that pair is also what site package requirements
  (min-version) and the ezpackage table use.

  How a version number is read (package.xml is never changed, only understood):
  - Semantic Versioning 2.0.0, MAJOR.MINOR.PATCH with optional -prerelease and +build,
    e.g. 1.0.0, 2.1.3-beta.2, 1.0.0+20260930.
  - The older forms packages were made with: fewer parts ("1" and "1.0" read as 1.0.0),
    more parts ("1.2.3.4": the fourth and later parts compare after PATCH), and a letter
    suffix directly after a part ("3.4.0beta1", "1.0rc2"), read as the prerelease
    "beta1" / "rc2" of that version.
  - Anything else is kept as text: it sorts before every version that can be read, and
    such texts compare among themselves in natural order.

  Precedence, as in SemVer 2.0.0 section 11: MAJOR, MINOR, PATCH (then any further legacy
  parts, missing ones as 0) numerically; a version with a prerelease comes before the same
  version without one; prerelease identifiers compare one by one, numeric ones numerically
  and before alphanumeric ones, alphanumeric ones in ASCII order, and a shorter list of equal
  identifiers first; build metadata is ignored. The release number is compared last,
  numerically: 1.0-1 < 1.0-2 < 1.0.1-1, and 1.0-1 equals 1.0.0-1.

  In a "<number>-<release>" pair the part after the last "-" is the release number only when
  it is all digits; so "1.0-1" is version 1.0 release 1 (the legacy reading, kept), and
  "1.0.0-beta.1" without a release is the prerelease "beta.1". A version number on its own
  (compare(), parse()) is never split this way.

  New packages from the creation wizards must have a strict SemVer 2.0.0 version, see
  isValidNew(); reading and comparing stay as tolerant as described above.
*/

class eZPackageVersion
{
    /// The version a new package gets in the creation wizards
    const DEFAULT_NEW_VERSION = '1.0.0';

    /// Longest version number the wizards accept: the ezpackage table stores "<number>-<release>" in 30 characters
    const MAX_NEW_LENGTH = 24;

    /// The SemVer 2.0.0 grammar, as the specification gives it
    const SEMVER_PATTERN = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/';

    /*!
     \return \c true if \a $version is a strict Semantic Versioning 2.0.0 version.
    */
    static function isSemVer( $version )
    {
        return is_string( $version ) and preg_match( self::SEMVER_PATTERN, $version ) === 1;
    }

    /*!
     \return \c true if \a $version may be given to a new package: strict SemVer 2.0.0 and at
             most MAX_NEW_LENGTH characters.
    */
    static function isValidNew( $version )
    {
        return self::isSemVer( $version ) and strlen( $version ) <= self::MAX_NEW_LENGTH;
    }

    /*!
     Reads the version number \a $version (see the class description).
     \return an array with 'numbers' (list of integers, at least three), 'prerelease' (list of
             identifiers, empty for none), 'build' (string or \c false), 'semver' (\c true when
             the text was strict SemVer) and 'text' (the trimmed original); or \c false when the
             text cannot be read as a version.
    */
    static function parse( $version )
    {
        if ( is_int( $version ) or is_float( $version ) )
            $version = (string)$version;
        if ( !is_string( $version ) )
            return false;
        $text = trim( $version );
        if ( $text === '' )
            return false;
        if ( preg_match( self::SEMVER_PATTERN, $text, $m ) )
        {
            return array( 'numbers' => array( (int)$m[1], (int)$m[2], (int)$m[3] ),
                          'prerelease' => ( isset( $m[4] ) and $m[4] !== '' ) ? explode( '.', $m[4] ) : array(),
                          'build' => ( isset( $m[5] ) and $m[5] !== '' ) ? $m[5] : false,
                          'semver' => true,
                          'text' => $text );
        }
        // Legacy forms: 1, 1.0, 1.2.3.4, 3.4.0beta1, 1.0rc2, with SemVer style -prerelease/+build
        // tolerated after them (1.0-beta, 01.2), since older tools wrote such numbers too.
        if ( !preg_match( '/^v?([0-9]+(?:\.[0-9]+)*)([a-zA-Z][0-9a-zA-Z]*)?(?:-([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/', $text, $m ) )
            return false;
        $numbers = array_map( 'intval', explode( '.', $m[1] ) );
        while ( count( $numbers ) < 3 )
            $numbers[] = 0;
        $prerelease = array();
        if ( isset( $m[2] ) and $m[2] !== '' )
            $prerelease[] = $m[2];
        if ( isset( $m[3] ) and $m[3] !== '' )
            $prerelease = array_merge( $prerelease, explode( '.', $m[3] ) );
        return array( 'numbers' => $numbers,
                      'prerelease' => $prerelease,
                      'build' => ( isset( $m[4] ) and $m[4] !== '' ) ? $m[4] : false,
                      'semver' => false,
                      'text' => $text );
    }

    /*!
     Compares two version numbers (no release number) by SemVer precedence.
     \return -1, 0 or 1 like strcmp().
    */
    static function compare( $a, $b )
    {
        $pa = self::parse( $a );
        $pb = self::parse( $b );
        if ( !$pa or !$pb )
        {
            if ( $pa )
                return 1;
            if ( $pb )
                return -1;
            return self::sign( strnatcmp( trim( (string)$a ), trim( (string)$b ) ) );
        }
        $count = max( count( $pa['numbers'] ), count( $pb['numbers'] ) );
        for ( $i = 0; $i < $count; ++$i )
        {
            $na = isset( $pa['numbers'][$i] ) ? $pa['numbers'][$i] : 0;
            $nb = isset( $pb['numbers'][$i] ) ? $pb['numbers'][$i] : 0;
            if ( $na != $nb )
                return $na < $nb ? -1 : 1;
        }
        return self::comparePrerelease( $pa['prerelease'], $pb['prerelease'] );
    }

    /*!
     Splits a "<number>-<release>" pair (see the class description).
     \return array( number, release ), the release an integer or \c false when there is none.
    */
    static function splitRelease( $full )
    {
        $full = trim( (string)$full );
        if ( preg_match( '/^(.+)-([0-9]+)$/', $full, $m ) )
            return array( $m[1], (int)$m[2] );
        return array( $full, false );
    }

    /*!
     Compares two "<number>-<release>" pairs (eZPackage::getVersion(), a site package's
     min-version): the version numbers first, then the release numbers, a missing one as 0.
     \return -1, 0 or 1.
    */
    static function compareFull( $a, $b )
    {
        list( $na, $ra ) = self::splitRelease( $a );
        list( $nb, $rb ) = self::splitRelease( $b );
        $result = self::compare( $na, $nb );
        if ( $result != 0 )
            return $result;
        $ra = $ra === false ? 0 : $ra;
        $rb = $rb === false ? 0 : $rb;
        return $ra == $rb ? 0 : ( $ra < $rb ? -1 : 1 );
    }

    /*!
     Compares two packages by version number, then release number, for usort().
    */
    static function comparePackages( $a, $b )
    {
        $result = self::compare( $a->attribute( 'version-number' ), $b->attribute( 'version-number' ) );
        if ( $result != 0 )
            return $result;
        return self::sign( (int)$a->attribute( 'release-number' ) - (int)$b->attribute( 'release-number' ) );
    }

    /*!
     Sorts a list of version numbers (or "<number>-<release>" pairs when \a $withRelease) in
     ascending precedence and returns it.
    */
    static function sort( array $versions, $withRelease = false )
    {
        usort( $versions, array( 'eZPackageVersion', $withRelease ? 'compareFull' : 'compare' ) );
        return $versions;
    }

    /*!
     \private
     SemVer 2.0.0 section 11.3 and 11.4 for two lists of prerelease identifiers.
    */
    private static function comparePrerelease( array $a, array $b )
    {
        if ( count( $a ) == 0 or count( $b ) == 0 )
        {
            if ( count( $a ) == count( $b ) )
                return 0;
            return count( $a ) == 0 ? 1 : -1;
        }
        $count = min( count( $a ), count( $b ) );
        for ( $i = 0; $i < $count; ++$i )
        {
            $ia = (string)$a[$i];
            $ib = (string)$b[$i];
            $numericA = ctype_digit( $ia );
            $numericB = ctype_digit( $ib );
            if ( $numericA and $numericB )
            {
                $result = self::compareDigits( $ia, $ib );
            }
            else if ( $numericA or $numericB )
            {
                $result = $numericA ? -1 : 1;
            }
            else
            {
                $result = self::sign( strcmp( $ia, $ib ) );
            }
            if ( $result != 0 )
                return $result;
        }
        return self::sign( count( $a ) - count( $b ) );
    }

    /*!
     \private
     Compares two strings of digits numerically, whatever their length.
    */
    private static function compareDigits( $a, $b )
    {
        $a = ltrim( $a, '0' );
        $b = ltrim( $b, '0' );
        if ( strlen( $a ) != strlen( $b ) )
            return strlen( $a ) < strlen( $b ) ? -1 : 1;
        return self::sign( strcmp( $a, $b ) );
    }

    private static function sign( $value )
    {
        return $value < 0 ? -1 : ( $value > 0 ? 1 : 0 );
    }
}

?>
