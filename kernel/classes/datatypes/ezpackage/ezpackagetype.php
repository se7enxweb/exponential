<?php
/**
 * File containing the eZPackageType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPackageType ezpackagetype.php
  \ingroup eZDatatype
  \brief The class eZPackageType does

*/


class eZPackageType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezpackage';
    const TYPE_FIELD = 'data_text1';
    const TYPE_VARIABLE = '_ezpackage_type_';
    const VIEW_MODE_FIELD = 'data_int1';
    const VIEW_MODE_VARIABLE = '_ezpackage_view_mode_';

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', 'Package', 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezpackage_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezpackage_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            // The name is appended to the package repository path; one that
            // could leave it (../, a slash, a NUL byte) or is not a string is not
            // a package the form offered, and is not stored
            if ( !self::isSafePackageName( $data ) )
            {
                return false;
            }

            // Save in ini files if the package type is sitestyle.
            $classAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
            if ( $classAttribute->attribute( self::TYPE_FIELD ) == 'sitestyle' )
            {
                $package = eZPackage::fetch( $data );
                if ( $package )
                {
                    // Written as before (an empty setting) when the package has
                    // no such file, without the undefined variable warning
                    $siteCSS = null;
                    $classesCSS = null;
                    $fileList = $package->fileList( 'default' );
                    foreach ( array_keys( $fileList ) as $key )
                    {
                        $file =& $fileList[$key];
                        $fileIdentifier = $file["variable-name"];
                        if ( $fileIdentifier == 'sitecssfile' )
                        {
                            $siteCSS = $package->fileItemPath( $file, 'default' );
                        }
                        else if ( $fileIdentifier == 'classescssfile' )
                        {
                            $classesCSS = $package->fileItemPath( $file, 'default' );
                        }
                    }
                    $currentSiteAccess = $http->hasPostVariable( 'CurrentSiteAccess' )
                                         ? $http->postVariable( 'CurrentSiteAccess' )
                                         : false;
                    // The siteaccess becomes a directory under settings/siteaccess/
                    // that design.ini.append.php is written to. Only one of the
                    // configured siteaccesses (what the form lists) is taken: a
                    // posted "../../x" wrote an ini file anywhere the web server
                    // can write. Anything else is not saved at all.
                    if ( $currentSiteAccess !== false and $currentSiteAccess !== 'Global' and
                         !self::isAvailableSiteAccess( $currentSiteAccess ) )
                    {
                        eZDebug::writeWarning( 'Ignoring a sitestyle for a siteaccess that is not configured', __METHOD__ );
                        return false;
                    }
                    $iniPath = 'settings/override';
                    if ( $currentSiteAccess != 'Global' and $currentSiteAccess !== false )
                    {
                        $data .= ':' . $currentSiteAccess;
                        $iniPath = 'settings/siteaccess/' . $currentSiteAccess;
                    }

                    $designINI = eZINI::instance( 'design.ini.append.php', $iniPath, null, false, null, true );
                    $designINI->setVariable( 'StylesheetSettings', 'SiteCSS', $siteCSS );
                    $designINI->setVariable( 'StylesheetSettings', 'ClassesCSS', $classesCSS );
                    $designINI->save();
                }
            }
            $contentObjectAttribute->setAttribute( 'data_text', $data );
        }
        return true;
    }

    /*!
     Does nothing since it uses the data_text field in the content object attribute.
     See fetchObjectAttributeHTTPInput for the actual storing.
    */
    function storeObjectAttribute( $attribute )
    {
        $ini = eZINI::instance();
        // Delete compiled template
        $siteINI = eZINI::instance();
        if ( $siteINI->hasVariable( 'FileSettings', 'CacheDir' ) )
        {
            $cacheDir = (string)$siteINI->variable( 'FileSettings', 'CacheDir' );
            // $cacheDir[0] raised a warning on PHP 8 when the setting is empty
            if ( substr( $cacheDir, 0, 1 ) == "/" )
            {
                $cacheDir = eZDir::path( array( $cacheDir ) );
            }
            else
            {
                if ( $siteINI->hasVariable( 'FileSettings', 'VarDir' ) )
                {
                    $varDir = $siteINI->variable( 'FileSettings', 'VarDir' );
                    $cacheDir = eZDir::path( array( $varDir, $cacheDir ) );
                }
            }
        }
        else if ( $siteINI->hasVariable( 'FileSettings', 'VarDir' ) )
        {
            $varDir = $siteINI->variable( 'FileSettings', 'VarDir' );
            $cacheDir = $ini->variable( 'FileSettings', 'CacheDir' );
            $cacheDir = eZDir::path( array( $varDir, $cacheDir ) );
        }
        else
        {
            $cacheDir =  eZSys::cacheDirectory();
        }
        $compiledTemplateDir = $cacheDir ."/template/compiled";
        eZDir::unlinkWildcard( $compiledTemplateDir . "/", "*pagelayout*.*" );

        // Expire template block cache
        eZContentCacheManager::clearTemplateBlockCacheIfNeeded( false );
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $packageTypeName = $base . self::TYPE_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $packageTypeName ) )
        {
            $packageTypeValue = $http->postVariable( $packageTypeName );
            if ( is_string( $packageTypeValue ) )
                $classAttribute->setAttribute( self::TYPE_FIELD, $packageTypeValue );
        }
        $packageViewModeName = $base . self::VIEW_MODE_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $packageViewModeName ) )
        {
            // The form offers 0 (combo box) and 1 (icon view)
            $packageViewModeValue = $http->postVariable( $packageViewModeName );
            $classAttribute->setAttribute( self::VIEW_MODE_FIELD, ( is_string( $packageViewModeValue ) && trim( $packageViewModeValue ) === '1' ) ? 1 : 0 );
        }
        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $packageName = $contentObjectAttribute->attribute( "data_text" );
        // eZPackage::fetch() appends the name to the repository path and reads
        // the package.xml it finds there, so a stored name that leaves the
        // repository is not looked up; there is no package by that name
        if ( !self::isSafePackageName( $packageName ) )
            return false;
        $package = eZPackage::fetch( $packageName );
        return $package;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /*!
     Returns the content of the string for use as a title
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        // data_text is null on an attribute that was never stored
        return trim( (string)$contentObjectAttribute->attribute( 'data_text' ) ) != '';
    }

    function isIndexable()
    {
        return false;
    }

    /*!
     \return the stored package name (name or name:siteaccess) for simplified
     export. The generic toString() returned '', so an export lost the value.
    */
    function toString( $contentObjectAttribute )
    {
        return (string)$contentObjectAttribute->attribute( 'data_text' );
    }

    /*!
     Sets the package name from toString() output. A name that could leave the
     package repository is refused, as in the edit form.
    */
    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string === '' || $string === null )
        {
            $contentObjectAttribute->setAttribute( 'data_text', '' );
            return true;
        }
        if ( !self::isSafePackageName( $string ) )
            return false;
        $contentObjectAttribute->setAttribute( 'data_text', $string );
        return true;
    }

    function sortKey( $contentObjectAttribute )
    {
        return strtolower( (string)$contentObjectAttribute->attribute( 'data_text' ) );
    }

    function sortKeyType()
    {
        return 'string';
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $type = $classAttribute->attribute( self::TYPE_FIELD );
        $dom = $attributeParametersNode->ownerDocument;
        $typeNode = $dom->createElement( 'type' );
        $typeNode->appendChild( $dom->createTextNode( $type ) );
        $attributeParametersNode->appendChild( $typeNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $typeNode = $attributeParametersNode->getElementsByTagName( 'type' )->item( 0 );
        $type = $typeNode ? $typeNode->textContent : '';
        $classAttribute->setAttribute( self::TYPE_FIELD, $type );
    }

    /*!
     Reads what eZDataType::serializeContentObjectAttribute() writes for this
     type (data-int, data-float, data-text). The generic reader called
     ->textContent on ->item( 0 ) of each element, a fatal error for a package
     that leaves one out; a missing element now keeps the attribute's value.
    */
    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $dataInt = $attributeNode->getElementsByTagName( 'data-int' )->item( 0 );
        if ( $dataInt )
            $objectAttribute->setAttribute( 'data_int', (int)$dataInt->textContent );
        $dataFloat = $attributeNode->getElementsByTagName( 'data-float' )->item( 0 );
        if ( $dataFloat )
            $objectAttribute->setAttribute( 'data_float', (float)$dataFloat->textContent );
        $dataText = $attributeNode->getElementsByTagName( 'data-text' )->item( 0 );
        if ( $dataText )
            $objectAttribute->setAttribute( 'data_text', $dataText->textContent );
    }

    function diff( $old, $new, $options = false )
    {
        return null;
    }

    /*!
     \private
     \return true if \a $name can be looked up as a directory inside the package
     repository: a non-empty string without a path separator or NUL byte that
     is not . or .. . A sitestyle stored as name:siteaccess still passes.
    */
    static function isSafePackageName( $name )
    {
        if ( !is_string( $name ) || $name === '' || $name === '.' || $name === '..' )
            return false;
        return strpbrk( $name, "/\\\0" ) === false;
    }

    /*!
     \private
     \return true if \a $siteAccess is one of SiteAccessSettings/AvailableSiteAccessList.
    */
    static function isAvailableSiteAccess( $siteAccess )
    {
        if ( !is_string( $siteAccess ) || $siteAccess === '' )
            return false;
        $list = eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        return is_array( $list ) && in_array( $siteAccess, $list, true );
    }
}

eZDataType::register( eZPackageType::DATA_TYPE_STRING, 'eZPackageType' );

?>
