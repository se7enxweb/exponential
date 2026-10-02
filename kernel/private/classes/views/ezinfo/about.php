<?php
/**
 * The code of kernel/ezinfo/about.php, moved into a class (#207 stage 1). The file kernel/ezinfo/about.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of ./kernel/ezinfo/about.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'getLicense' ) ) {
/*!
  Returns contents of LICENSE file in ezp legacy root directory, or false on failure.
*/
function getLicense()
{
    return file_get_contents( 'LICENSE' );
}
}

if ( !function_exists( 'getContributors' ) ) {
/*!
  Returns list of contributors;
  Searches all php files in \a $pathToDir and tries to fetch contributor's info
*/
function getContributors( $pathToDir )
{
    $contribFiles = eZDir::recursiveFind( $pathToDir, "php" );
    $contributors = array();
    if ( count( $contribFiles ) )
    {
        foreach ( $contribFiles as $contribFile )
        {
            // include, not include_once: a persistent worker would skip the file on later
            // views, leaving the About page empty
            include( $contribFile );
            if ( !isset( $contributorSettings ) )
                continue;

            $tmpFiles = explode( ',', $contributorSettings['files'] );
            $updatedFiles = array();
            foreach ( $tmpFiles as $file )
            {
                if ( trim( $file ) )
                    $updatedFiles[] = trim( $file,"\n\r" );
            }
            $files = implode( ', ', $updatedFiles );
            $contributorSettings['files'] = $files;
            $contributors[] = $contributorSettings;
        }
    }
    return $contributors;
}
}

if ( !function_exists( 'getThirdPartySoftware' ) ) {
/*!
  Returns third-party software from \a $pathToFile
*/
function getThirdPartySoftware( $pathToFile )
{
    if ( !file_exists( $pathToFile ) )
        return array();

    // include, not include_once, so later views in a persistent worker still read it
    include( $pathToFile );
    if ( !isset( $thirdPartySoftware ) )
        return array();

    $thirdPartySoftware = array_unique( $thirdPartySoftware );
    return $thirdPartySoftware;
}
}

if ( !function_exists( 'getExtensionsInfo' ) ) {
/*!
  Returns active extentions info in run-time
*/
function getExtensionsInfo()
{
    $siteINI = eZINI::instance();
    $selectedExtensionArray       = $siteINI->variable( 'ExtensionSettings', "ActiveExtensions" );
    $selectedAccessExtensionArray = $siteINI->variable( 'ExtensionSettings', "ActiveAccessExtensions" );
    $selectedExtensions           = array_merge( $selectedExtensionArray, $selectedAccessExtensionArray );
    $selectedExtensions           = array_unique( $selectedExtensions );
    $result = array();
    foreach ( $selectedExtensions as $extension )
    {
        $extensionInfo = eZExtension::extensionInfo( $extension );
        if ( !$extensionInfo )
            continue;

        // An ezinfo.php says 'Version'; extensionInfo() adds the same value as 'version': list it once
        if ( isset( $extensionInfo['Version'], $extensionInfo['version'] ) && $extensionInfo['Version'] === $extensionInfo['version'] )
            unset( $extensionInfo['version'] );
        $result[$extension] = $extensionInfo;
    }
    return $result;
}
}

if ( !function_exists( 'ezinfoAboutPlainText' ) ) {
/*!
  The text of an info value without markup, or '' for an empty value, a non-string or an
  unexpanded build placeholder such as //autogentag//
*/
function ezinfoAboutPlainText( $value )
{
    if ( !is_string( $value ) && !is_numeric( $value ) )
        return '';
    $text = trim( preg_replace( '/\s+/', ' ', html_entity_decode( strip_tags( (string)$value ), ENT_QUOTES, 'UTF-8' ) ) );
    return strpos( $text, '//' ) === 0 ? '' : $text;
}
}

if ( !function_exists( 'ezinfoAboutField' ) ) {
/*!
  The first non-empty value among \a $keys in \a $info, compared without case
*/
function ezinfoAboutField( array $info, array $keys )
{
    $lower = array();
    foreach ( $info as $key => $value )
    {
        if ( is_string( $key ) && !isset( $lower[strtolower( $key )] ) && ezinfoAboutPlainText( $value ) !== '' )
            $lower[strtolower( $key )] = $value;
    }
    foreach ( $keys as $key )
    {
        if ( isset( $lower[$key] ) )
            return ezinfoAboutPlainText( $lower[$key] );
    }
    return '';
}
}

if ( !function_exists( 'ezinfoAboutUrl' ) ) {
/*!
  A web address as a link can use it: with a scheme, and only http or https
*/
function ezinfoAboutUrl( $url )
{
    $url = ezinfoAboutPlainText( $url );
    if ( $url === '' )
        return '';
    if ( !preg_match( '#^[a-z][a-z0-9+.-]*://#i', $url ) )
        $url = 'https://' . ltrim( $url, '/' );
    return preg_match( '#^https?://[^\s/]+#i', $url ) ? $url : '';
}
}

if ( !function_exists( 'getExtensionRows' ) ) {
/*!
  One row per extension of \a $extensions (as getExtensionsInfo() returns them) for a table:
  position (the loading order), identifier, name, description, version, license, url,
  copyright, author and includes (the third-party software it says it includes, each with
  name, version, license, copyright and url). Every value is plain text.
*/
function getExtensionRows( array $extensions )
{
    $rows = array();
    $position = 0;
    foreach ( $extensions as $identifier => $info )
    {
        if ( !is_array( $info ) )
            continue;
        $includes = array();
        foreach ( $info as $key => $value )
        {
            if ( !is_string( $key ) || !is_array( $value ) || stripos( $key, 'includes' ) !== 0 )
                continue;
            // One piece of software as a hash, or a list of them
            $list = ( isset( $value[0] ) && is_array( $value[0] ) ) ? $value : array( $value );
            foreach ( $list as $software )
            {
                if ( !is_array( $software ) )
                    continue;
                $name = ezinfoAboutField( $software, array( 'name' ) );
                if ( $name === '' )
                    continue;
                $includes[] = array( 'name'      => $name,
                                     'version'   => ezinfoAboutField( $software, array( 'version' ) ),
                                     'license'   => ezinfoAboutField( $software, array( 'license' ) ),
                                     'copyright' => ezinfoAboutField( $software, array( 'copyright' ) ),
                                     'url'       => ezinfoAboutUrl( ezinfoAboutField( $software, array( 'info_url', 'for more information', 'url' ) ) ) );
            }
        }
        $name = ezinfoAboutField( $info, array( 'name' ) );
        $rows[] = array( 'position'    => ++$position,
                         'identifier'  => (string)$identifier,
                         'name'        => $name !== '' ? $name : (string)$identifier,
                         'description' => ezinfoAboutField( $info, array( 'description', 'summary' ) ),
                         'version'     => ezinfoAboutField( $info, array( 'version' ) ),
                         'license'     => ezinfoAboutField( $info, array( 'license' ) ),
                         'url'         => ezinfoAboutUrl( ezinfoAboutField( $info, array( 'info_url' ) ) ),
                         'copyright'   => ezinfoAboutField( $info, array( 'copyright' ) ),
                         'author'      => ezinfoAboutField( $info, array( 'author', 'maintainer' ) ),
                         'includes'    => $includes );
    }
    // The address as it is shown: without the scheme and a slash at the end
    foreach ( $rows as $index => $row )
        $rows[$index]['url_label'] = rtrim( preg_replace( '#^https?://(www\.)?#i', '', $row['url'] ), '/' );
    return $rows;
}
}

if ( !function_exists( 'sortExtensionRows' ) ) {
/*!
  \a $rows sorted by \a $field (position, name, identifier, version or license) in
  \a $direction (asc or desc); rows with the same value keep the loading order
*/
function sortExtensionRows( array $rows, $field, $direction )
{
    usort( $rows, function( $a, $b ) use ( $field, $direction )
    {
        if ( $field === 'position' )
            $result = $a['position'] - $b['position'];
        else if ( $field === 'version' )
            $result = version_compare( (string)$a['version'], (string)$b['version'] );
        else
            $result = strnatcasecmp( (string)$a[$field], (string)$b[$field] );
        if ( $direction === 'desc' )
            $result = -$result;
        return $result !== 0 ? $result : $a['position'] - $b['position'];
    } );
    return $rows;
}
}

if ( !function_exists( 'strReplaceByArray' ) ) {
/*!
  Replaces all occurrences (in \a $subjects) of the search string (keys of \a $searches )
  with the replacement string (values of \a $searches)

  Returns array with replacements
*/
function strReplaceByArray( $searches = array(), $subjects = array() )
{
    $retArray = array();
    foreach( $subjects as $key => $subject )
    {
        if ( is_array( $subject ) )
        {
            $retArray[$key] = strReplaceByArray( $searches, $subject );
        }
        else
        {
            $retArray[$key] = str_replace( array_keys( $searches ), $searches, $subject );
        }
    }
    return $retArray;
}
}
}

namespace Exponential\View\Kernel\Ezinfo
{

class About extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        // Guarded: a persistent worker runs this view many times, and a second define() warns
        if ( !defined( 'EZ_ABOUT_CONTRIBUTORS_DIR' ) )
            define( 'EZ_ABOUT_CONTRIBUTORS_DIR', 'var/storage/contributors' );
        if ( !defined( 'EZ_ABOUT_THIRDPARTY_SOFTWARE_FILE' ) )
            define( 'EZ_ABOUT_THIRDPARTY_SOFTWARE_FILE', 'var/storage/third_party_software.php' );







        $ezinfo = \ExponentialSDK::version( true );

        $whatIsEzPublish = '<p>Exponential is a professional PHP application framework with advanced
        CMS (content management system) functionality. As a CMS, its most notable
        feature is its revolutionary, fully customizable and extendable content
        model. This is also what makes Exponential suitable as a platform for
        general PHP development, allowing you to rapidly create professional
        web-based applications.</p>

        <p>Standard CMS functionality (such as news publishing, e-commerce and
        forums) are already implemented and ready to use. Standalone libraries
        can be used for cross-platform database-independent browser-neutral
        PHP projects. Because Exponential is a web-based application, it can
        be accessed from anywhere you have an internet connection.</p>';

        $license = getLicense();
        $contributors = getContributors( EZ_ABOUT_CONTRIBUTORS_DIR );
        $thirdPartySoftware = getThirdPartySoftware( EZ_ABOUT_THIRDPARTY_SOFTWARE_FILE );
        $extensions = getExtensionsInfo();

        // The extensions and the software they include as table rows of plain text, made before the
        // links are put into the texts below. Sorted by the (sort) and (dir) view parameters.
        $extensionSortFields = array( 'order' => 'position', 'name' => 'name', 'identifier' => 'identifier',
                                      'version' => 'version', 'license' => 'license' );
        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();
        $sortBy = isset( $userParameters['sort'] ) ? (string)$userParameters['sort'] : 'order';
        $sortBy = isset( $extensionSortFields[$sortBy] ) ? $sortBy : 'order';
        $sortDirection = ( isset( $userParameters['dir'] ) && $userParameters['dir'] === 'desc' ) ? 'desc' : 'asc';

        $extensionRows = getExtensionRows( $extensions );
        $thirdPartyRows = array();
        foreach ( $extensionRows as $row )
        {
            foreach ( $row['includes'] as $software )
            {
                $key = strtolower( $software['name'] . "\n" . $software['version'] . "\n" . $row['identifier'] );
                $thirdPartyRows[$key] = $software + array( 'extension' => $row['name'], 'extension_identifier' => $row['identifier'] );
            }
        }
        uasort( $thirdPartyRows, function( $a, $b ) { return strnatcasecmp( $a['name'], $b['name'] ); } );
        $extensionRows = sortExtensionRows( $extensionRows, $extensionSortFields[$sortBy], $sortDirection );

        list( $whatIsEzPublish,
              $contributors,
              $thirdPartySoftware,
              $extensions ) = strReplaceByArray( array( 'eZ Systems AS' => '<a href="http://ez.no/">eZ Systems AS</a>',
                                                        'eZ Systems as' => '<a href="http://ez.no/">eZ Systems AS</a>',
                                                        'eZ systems AS' => '<a href="http://ez.no/">eZ Systems AS</a>',
                                                        'eZ systems as' => '<a href="http://ez.no/">eZ Systems AS</a>',
                                                        'Exponential' => '<a href="https://exponential.earth">Exponential</a>',
                                                        'exponential cms' => '<a href="https://exponential.earth">Exponential</a>' ),
                                                 array( $whatIsEzPublish, $contributors, $thirdPartySoftware, $extensions ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'ezinfo', $ezinfo );
        $tpl->setVariable( 'what_is_ez_publish', $whatIsEzPublish );
        $tpl->setVariable( 'license', $license );
        $tpl->setVariable( 'contributors', $contributors );
        $tpl->setVariable( 'third_party_software', $thirdPartySoftware );
        $tpl->setVariable( 'extensions', $extensions );
        $tpl->setVariable( 'extension_rows', $extensionRows );
        $tpl->setVariable( 'third_party_rows', array_values( $thirdPartyRows ) );
        $tpl->setVariable( 'extension_sort', array( 'field'     => $sortBy,
                                                    'direction' => $sortDirection,
                                                    'opposite'  => $sortDirection === 'asc' ? 'desc' : 'asc' ) );
        $tpl->setVariable( 'view_parameters', array( 'sort' => $sortBy, 'dir' => $sortDirection ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:ezinfo/about.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/ezinfo', 'Info' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/ezinfo', 'About' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
