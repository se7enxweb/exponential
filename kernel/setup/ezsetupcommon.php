<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// This file holds shared functions for the ezsetup files


if ( !function_exists( 'eZSetupCriticalTests' ) ) {
/*!
 \return an array with tests that need to be run
         and succeed for the setup to continue.
*/
function eZSetupCriticalTests()
{
    $ini = eZINI::instance();
    return $ini->variableArray( 'SetupSettings', 'CriticalTests' );
}
}

if ( !function_exists( 'eZSetupOptionalTests' ) ) {
/*!
 \return an array with tests that when run will give information on finetuning.
*/
function eZSetupOptionalTests()
{
    $ini = eZINI::instance();
    return $ini->variableArray( 'SetupSettings', 'OptionalTests' );
}
}

if ( !function_exists( 'eZSetupDatabaseMap' ) ) {
function eZSetupDatabaseMap()
{
    return array( 'mysqli' => array( 'type' => 'mysqli',
                                     'driver' => 'ezmysqli',
                                     'name' => 'MySQL Improved',
                                     'required_version' => '4.1.1',
                                     'has_demo_data' => true,
                                     'supports_unicode' => true ),
                  'pgsql' => array( 'type' => 'pgsql',
                                    'driver' => 'ezpostgresql',
                                    'name' => 'PostgreSQL',
                                    'required_version' => '8.0',
                                    'has_demo_data' => false,
                                    'supports_unicode' => true ),
                  'sqlite3' => array( 'type' => 'sqlite3',
                                      'driver' => 'sqlite3',
                                      'name' => 'SQLite',
                                      'required_version' => '3.0.1',
                                      'has_demo_data' => true,
                                      'supports_unicode' => true ),
                  'mongodb' => array( 'type' => 'mongodb',
                                      'driver' => 'mongodb',
                                      'name' => 'MongoDB',
                                      'required_version' => '4.0',
                                      'has_demo_data' => false,
                                      'supports_unicode' => true ),
                  // the driver lives in an extension, activated for the
                  // installation by eZSetupActivateDatabaseExtension()
                  'oci8' => array( 'type' => 'oci8',
                                   'driver' => 'ezoracle',
                                   'extension' => 'ezoracle',
                                   'name' => 'Oracle',
                                   'required_version' => '19.0',
                                   'has_demo_data' => false,
                                   'supports_unicode' => true )
                   );
}
}

if ( !function_exists( 'eZSetupActivateDatabaseExtension' ) ) {
/**
 * Loads the settings of the extension that carries the database driver of
 * $databaseInfo (an entry of eZSetupDatabaseMap() with an 'extension' key,
 * Oracle's ezoracle), so the wizard and the kickstarter can connect and
 * create the schema before the extension is active in settings/override.
 * CreateSites then writes it into ActiveExtensions of the new site.
 *
 * @param array $databaseInfo
 * @return bool false when the extension is not installed
 */
function eZSetupActivateDatabaseExtension( $databaseInfo )
{
    if ( !is_array( $databaseInfo ) || empty( $databaseInfo['extension'] ) )
        return true;

    static $activated = array();
    $extension = $databaseInfo['extension'];
    if ( isset( $activated[$extension] ) )
        return true;

    $settingsDir = eZExtension::baseDirectory() . '/' . $extension . '/settings';
    if ( !is_dir( $settingsDir ) )
    {
        eZDebug::writeError( "The $extension extension, which the database driver " . $databaseInfo['driver'] . " needs, is not installed", __FUNCTION__ );
        return false;
    }

    $ini = eZINI::instance();
    if ( !in_array( $extension, $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) ) )
    {
        // global: INI files read from now on (dbschema.ini ...) see the settings too
        $ini->prependOverrideDir( $settingsDir, true, 'extension:' . $extension, 'extension' );
        $ini->load();
        foreach ( array( 'dbschema.ini', 'file.ini' ) as $iniFile )
        {
            if ( eZINI::isLoaded( $iniFile ) )
                eZINI::resetInstance( $iniFile );
        }
    }
    $activated[$extension] = true;
    return true;
}
}

if ( !function_exists( 'eZSetupFetchPersistenceList' ) ) {
function eZSetupFetchPersistenceList()
{
    $persistenceList = array();
    $http = eZHTTPTool::instance();
    $postVariables = $http->attribute( 'post' );

    foreach ( $postVariables as $name => $value )
    {
        if ( preg_match( '/^P_([a-zA-Z0-9_]+)-([a-zA-Z0-9_]+)$/', $name, $matches ) )
        {
            $persistenceGroup = $matches[1];
            $persistenceName = $matches[2];
            $persistenceList[$persistenceGroup][$persistenceName] = $value;
        }
    }

    return $persistenceList;
}
}

if ( !function_exists( 'eZSetupSetPersistencePostVariable' ) ) {
function eZSetupSetPersistencePostVariable( $var, $value )
{
    $http = eZHTTPTool::instance();
    if ( is_array( $value ) )
    {
        foreach ( $value as $valueKey => $valueItem )
        {
            $http->setPostVariable( 'P_' . $var . '-' . $valueKey, $valueItem );
        }
    }
    else
    {
        $http->setPostVariable( 'P_' . $var . '-0', $value );
    }
}
}

if ( !function_exists( 'eZSetupMergePersistenceList' ) ) {
function eZSetupMergePersistenceList( &$persistenceList, $persistenceDataList )
{
    foreach ( $persistenceDataList as $persistenceData )
    {
        $persistenceName = $persistenceData[0];
        $persistenceValues = $persistenceData[1];
        if ( !isset( $persistenceList[$persistenceName] ) )
        {
            $values =& $persistenceList[$persistenceName];
            foreach ( $persistenceValues as $persistenceValueName => $persistenceValueData )
            {
                $values[$persistenceValueName] = $persistenceValueData['value'];
            }
        }
        else
        {
            $oldValues =& $persistenceList[$persistenceName];
            foreach ( $persistenceValues as $persistenceValueName => $persistenceValueData )
            {
                if ( !isset( $oldValues[$persistenceValueName] ) )
                {
                    $oldValues[$persistenceValueName] = $persistenceValueData['value'];
                }
                else if ( is_array( $persistenceValueData['value'] ) and
                          isset( $persistenceValueData['merge'] ) and
                          $persistenceValueData['merge'] )
                {
                     $merged = array_merge( $oldValues[$persistenceValueName], $persistenceValueData['value'] );
                     if ( isset( $persistenceValueData['unique'] ) and
                          $persistenceValueData['unique'] )
                          $merged = array_unique( $merged );
                     $oldValues[$persistenceValueName] = $merged;
                }
                else
                {
                    $oldValues[$persistenceValueName] = $persistenceValueData['value'];
                }
            }
        }
    }
}
}

if ( !function_exists( 'eZSetupLanguageList' ) ) {
function eZSetupLanguageList( &$languageList, &$defaultLanguage, &$defaultExtraLanguages )
{
    $locales = eZLocale::localeList( true );
    $languageList = array();
    $httpMap   = array();
    $httpMapShort = array();
    // This alias array must be filled in with known names.
    // The key is the value from the locale INI file (HTTP group)
    // and the value is the HTTP alias.
    // The Norwegian locales say nb-NO and nn-NO (BCP 47); a browser asking for
    // the macrolanguage "no" still means Bokmål.
    $httpAliases = array( 'nb-no' => 'no',
                          'no-bokmaal' => 'nb',
                          'no-nynorsk' => 'nn',
                          'ru-ru' => 'ru' );

    foreach ( array_keys( $locales ) as $localeKey )
    {
        $locale =& $locales[$localeKey];
        if ( !$locale->attribute( 'country_variation' ) )
        {
            $languageList[] = $locale;
            $httpLocale = strtolower( $locale->httpLocaleCode() );
            $httpMap[$httpLocale] = $locale;
        }
    }

    // bubble sort language based on language name. bubble bad, but only about 8-9 elements
    for ( $i =0; $i < count( $languageList ); $i++ )
        for ( $n = 0; $n < count( $languageList ) - 1; $n++ )
        {
            if ( strcmp( $languageList[$n]->attribute( 'language_name' ), $languageList[$n+1]->attribute( 'language_name' ) ) > 0 )
            {
                $tmpElement = $languageList[$n];
                $languageList[$n] = $languageList[$n+1];
                $languageList[$n+1] = $tmpElement;
            }
        }

    // A bare language ("de", "en") names no country, and several locales can
    // share it: the one whose country matches the language (de -> ger-DE,
    // fr -> fre-FR, pl -> pol-PL) wins, and for English the language the
    // bundled data is in. The short map above kept only the last locale seen,
    // under its full code, so "de" and "en" matched nothing at all.
    $httpMapShort = array();
    $dataLanguage = eZSetupBundledDataLanguage();
    foreach ( $languageList as $locale )
    {
        $httpLocale = strtolower( $locale->httpLocaleCode() );
        $parts = explode( '-', $httpLocale );
        $short = $parts[0];
        $rank = 2;
        if ( $locale->localeCode() == $dataLanguage )
            $rank = 0;
        else if ( isset( $parts[1] ) && $parts[1] == $short )
            $rank = 1;
        $keys = array( $short );
        if ( isset( $httpAliases[$httpLocale] ) )
            $keys[] = $httpAliases[$httpLocale];
        foreach ( $keys as $key )
        {
            if ( !isset( $httpMapShort[$key] ) || $rank < $httpMapShort[$key][0] )
                $httpMapShort[$key] = array( $rank, $locale );
        }
    }

    $defaultLanguage = false;
    $defaultExtraLanguages = array();
    foreach ( eZSetupParseAcceptLanguage( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '' ) as $acceptLanguageCode )
    {
        $languageCode = false;
        list( $acceptLanguageShort ) = explode( '-', $acceptLanguageCode );
        if ( isset( $httpMap[$acceptLanguageCode] ) )
            $languageCode = $httpMap[$acceptLanguageCode]->localeCode();
        else if ( isset( $httpMapShort[$acceptLanguageCode] ) )
            $languageCode = $httpMapShort[$acceptLanguageCode][1]->localeCode();
        // de-AT, en-IE: no locale of their own, the language still counts
        else if ( isset( $httpMapShort[$acceptLanguageShort] ) )
            $languageCode = $httpMapShort[$acceptLanguageShort][1]->localeCode();
        if ( !$languageCode )
            continue;
        if ( $defaultLanguage === false )
            $defaultLanguage = $languageCode;
        else
            $defaultExtraLanguages[] = $languageCode;
    }
    if ( $defaultLanguage === false )
    {
        // The language the bundled base data (share/db_data.dba) and the
        // default site package are written in: with eng-GB, a browser that
        // sent no Accept-Language got a site whose objects had no attributes
        // in its primary language, and the package installer aborted
        $defaultLanguage = $dataLanguage;
    }
    $defaultExtraLanguages = array_values( array_unique( array_diff( $defaultExtraLanguages, array( $defaultLanguage ) ) ) );
}
}

if ( !function_exists( 'eZSetupBundledDataLanguage' ) ) {
/*!
 The language the bundled base data (share/db_data.dba) and the default site
 packages are written in: eZStepInstaller::CLEAN_DATA_LANGUAGE where the
 installer defines it, else eng-US.
*/
function eZSetupBundledDataLanguage()
{
    if ( class_exists( 'eZStepInstaller' ) && defined( 'eZStepInstaller::CLEAN_DATA_LANGUAGE' ) )
        return constant( 'eZStepInstaller::CLEAN_DATA_LANGUAGE' );
    return 'eng-US';
}
}

if ( !function_exists( 'eZSetupParseAcceptLanguage' ) ) {
/*!
 \return the language ranges of an Accept-Language header, lower case, most
         preferred first (by q value, then by position); "*" and q=0 dropped.
*/
function eZSetupParseAcceptLanguage( $header )
{
    $ranges = array();
    $position = 0;
    foreach ( explode( ',', (string)$header ) as $item )
    {
        $parts = explode( ';', $item );
        $code = strtolower( trim( $parts[0] ) );
        if ( $code === '' || $code === '*' || !preg_match( '/^[a-z]{1,8}(-[a-z0-9]{1,8})*$/', $code ) )
            continue;
        $q = 1.0;
        for ( $i = 1; $i < count( $parts ); ++$i )
        {
            if ( preg_match( '/^\s*q\s*=\s*([0-9.]+)\s*$/i', $parts[$i], $m ) )
                $q = (float)$m[1];
        }
        if ( $q <= 0 )
            continue;
        $ranges[] = array( $q, $position++, $code );
    }
    usort( $ranges, function( $a, $b )
    {
        if ( $a[0] != $b[0] )
            return $a[0] < $b[0] ? 1 : -1;
        return $a[1] - $b[1];
    } );
    $codes = array();
    foreach ( $ranges as $range )
        $codes[] = $range[2];
    return $codes;
}
}

if ( !function_exists( 'eZSetupValidateLanguageChoice' ) ) {
/*!
 Checks a primary language and a list of additional languages against the
 languages the wizard offers (the locales in share/locale without a variation).

 \return an array: 'errors' (a list of messages, empty when the choice is
         valid), 'primary_language', 'extra_languages' (without the primary,
         duplicates removed, in the order given) and 'languages' (the primary
         first, then the additional ones)
*/
function eZSetupValidateLanguageChoice( $primaryLanguage, $extraLanguages )
{
    $languageList = false;
    $defaultLanguage = false;
    $defaultExtraLanguages = false;
    eZSetupLanguageList( $languageList, $defaultLanguage, $defaultExtraLanguages );
    $offered = array();
    foreach ( $languageList as $locale )
        $offered[] = $locale->localeCode();

    $errors = array();
    $primaryLanguage = is_string( $primaryLanguage ) ? trim( $primaryLanguage ) : '';
    if ( $primaryLanguage === '' )
    {
        $errors[] = ezpI18n::tr( 'design/standard/setup/init', 'Choose a primary language.' );
    }
    else if ( !in_array( $primaryLanguage, $offered, true ) )
    {
        $errors[] = ezpI18n::tr( 'design/standard/setup/init', 'The primary language %1 is not a language this installation has a locale for (share/locale).', null, array( $primaryLanguage ) );
    }

    $extras = array();
    foreach ( (array)$extraLanguages as $extraLanguage )
    {
        if ( !is_string( $extraLanguage ) || trim( $extraLanguage ) === '' )
            continue;
        $extraLanguage = trim( $extraLanguage );
        if ( !in_array( $extraLanguage, $offered, true ) )
        {
            $errors[] = ezpI18n::tr( 'design/standard/setup/init', 'The additional language %1 is not a language this installation has a locale for (share/locale).', null, array( $extraLanguage ) );
            continue;
        }
        if ( $extraLanguage === $primaryLanguage )
        {
            $errors[] = ezpI18n::tr( 'design/standard/setup/init', '%1 is the primary language and cannot also be an additional language. Uncheck it, or choose another primary language.', null, array( $extraLanguage ) );
            continue;
        }
        if ( !in_array( $extraLanguage, $extras, true ) )
            $extras[] = $extraLanguage;
    }

    return array( 'errors' => $errors,
                  'primary_language' => $primaryLanguage,
                  'extra_languages' => $extras,
                  'languages' => $primaryLanguage === '' ? $extras : array_merge( array( $primaryLanguage ), $extras ) );
}
}








?>
