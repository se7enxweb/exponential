<?php
/**
 * File containing the eZIniSettingType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZIniSettingType ezinisettingtype.php
  \ingroup eZDatatype
  \brief A content datatype for setting ini file settings

  Enable editing and versioning of ini files from the admin interface
*/



class eZIniSettingType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezinisetting';

    const CLASS_TYPE = '_ezinisetting_type_';
    const CLASS_FILE = '_ezinisetting_file_';
    const CLASS_SECTION = '_ezinisetting_section_';
    const CLASS_PARAMETER = '_ezinisetting_parameter_';
    const CLASS_INI_INSTANCE = '_ezinisetting_ini_instance_';

    const CLASS_FILE_FIELD = 'data_text1';
    const CLASS_SECTION_FIELD = 'data_text2';
    const CLASS_PARAMETER_FIELD = 'data_text3';
    const CLASS_TYPE_FIELD = 'data_int1';
    const CLASS_INI_INSTANCE_FIELD = 'data_text4';
    const SITE_ACCESS_LIST_FIELD = 'data_text5';

    const CLASS_TYPE_ARRAY = 6;

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', 'Ini Setting', 'Datatype name' ),
                                                         array( 'translation_allowed' => false,
                                                                'serialize_supported' => true ) );
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ini_setting_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $contentClassAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
            $iniFile = eZIniSettingType::iniFile( $contentClassAttribute );
            $iniSection = eZIniSettingType::iniSection( $contentClassAttribute );
            $iniParameterName = eZIniSettingType::iniParameterName( $contentClassAttribute );
            $value = $http->postVariable( $base . '_ini_setting_' . $contentObjectAttribute->attribute( 'id' ) );

            // The class names the file the value is written to on publish. A name
            // with a directory part would point outside the settings tree.
            if ( !eZIniSettingType::isValidIniFileName( $iniFile ) ||
                 !eZIniSettingType::isValidIniName( $iniSection ) ||
                 !eZIniSettingType::isValidIniName( $iniParameterName ) )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'Could not locate the ini file.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            $config = eZINI::instance( $iniFile );
            if ( $config == null )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'Could not locate the ini file.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            // The value is written verbatim into an INI file: a line break would
            // start a setting or a section of the editor's choosing, and "*/"
            // would end the PHP comment that wraps an .ini.append.php file.
            $type = (int)$contentClassAttribute->attribute( self::CLASS_TYPE_FIELD );
            if ( $type == self::CLASS_TYPE_ARRAY )
            {
                if ( !eZIniSettingType::isValidIniArrayText( $value ) )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Wrong text field value.' ) );

                    return eZInputValidator::STATE_INVALID;
                }
            }
            // (trimmed, as fetchObjectAttributeHTTPInput stores it)
            else if ( !is_string( $value ) ||
                      !eZIniSettingType::isValidIniValue( trim( $value ) ) ||
                      !eZIniSettingType::isValidTypedValue( $type, trim( $value ) ) )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Wrong text field value.' ) );

                return eZInputValidator::STATE_INVALID;
            }
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \static
     \return true if \a $fileName is a bare INI file name. It is appended to a
     settings directory to build the path of the file that is read and written,
     so a directory part ("../", "/") would reach outside the settings tree.
    */
    static function isValidIniFileName( $fileName )
    {
        return is_string( $fileName ) &&
               preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_.-]*$/', $fileName ) &&
               strpos( $fileName, '..' ) === false;
    }

    /*!
     \static
     \return true if \a $name can be written as an INI section or setting name:
     no line break (it would start a line of its own), no brackets or "=" (they
     delimit sections, array keys and values) and no "*" "/" (it would close the
     PHP comment an .ini.append.php file is wrapped in).
    */
    static function isValidIniName( $name )
    {
        return is_string( $name ) && trim( $name ) !== '' &&
               !preg_match( '/[\x00-\x1f\x7f\[\]=]|\*\//', $name );
    }

    /*!
     \static
     \return true if \a $value can be written as the value of an INI setting,
     that is it stays on one line and cannot close the PHP comment. Tabs are
     kept, they are ordinary value content.
    */
    static function isValidIniValue( $value )
    {
        return is_string( $value ) &&
               !preg_match( '/[\x00-\x08\x0a-\x1f]|\*\//', $value );
    }

    /*!
     \static
     \return true if \a $text, the array form "key=value" per line, parses and
     every key and value in it can be written to an INI file.
    */
    static function isValidIniArrayText( $text )
    {
        if ( !is_string( $text ) )
            return false;
        $iniArray = array();
        if ( eZIniSettingType::parseArrayInput( $text, $iniArray ) === false )
            return false;
        foreach ( $iniArray as $key => $value )
        {
            if ( !is_int( $key ) && !eZIniSettingType::isValidIniName( $key ) )
                return false;
            if ( !eZIniSettingType::isValidIniValue( (string)$value ) )
                return false;
        }
        return true;
    }

    /*!
     \static
     \return true if \a $value fits the setting type the class gives: the two
     boolean types only have the values their select box offers, the numeric
     types a number. An empty value is allowed, as before.
    */
    static function isValidTypedValue( $type, $value )
    {
        if ( !is_string( $value ) || $value === '' )
            return is_string( $value );
        switch ( (int)$type )
        {
            case 2: return $value === 'enabled' || $value === 'disabled';
            case 3: return $value === 'true' || $value === 'false';
            case 4: return preg_match( '/^[-+]?\d+$/', $value ) === 1;
            case 5: return is_numeric( $value );
        }
        return true;
    }

    /*!
     \static
     \return the settings directory ini instance \a $iniInstance stands for, or
     false if it names none. -1 is the base settings directory (only when
     \a $allowBase is set: it is read, never written), 0 and an empty entry the
     override directory (PHP 8 no longer takes '' == 0, which used to point the
     path at settings/siteaccess/ itself), anything else a site access of
     \a $siteAccessArray by its index.
    */
    static function iniInstancePath( $iniInstance, $siteAccessArray, $allowBase = false )
    {
        if ( is_int( $iniInstance ) )
            $index = $iniInstance;
        else if ( is_string( $iniInstance ) && trim( $iniInstance ) === '' )
            $index = 0;
        else if ( is_string( $iniInstance ) && preg_match( '/^-?\d+$/', trim( $iniInstance ) ) )
            $index = (int)trim( $iniInstance );
        else
            return false;

        if ( $index == -1 )
            return $allowBase ? 'settings' : false;
        if ( $index == 0 )
            return 'settings/override';
        if ( $index < 0 || !isset( $siteAccessArray[$index] ) )
            return false;
        $siteAccess = trim( $siteAccessArray[$index] );
        // The site access name becomes a directory name
        if ( !eZIniSettingType::isValidIniFileName( $siteAccess ) )
            return false;
        return 'settings/siteaccess/' . $siteAccess;
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $fileParam = $base . self::CLASS_FILE . $classAttribute->attribute( 'id' );
        $sectionParam = $base . self::CLASS_SECTION . $classAttribute->attribute( 'id' );
        $parameterParam = $base . self::CLASS_PARAMETER . $classAttribute->attribute( 'id' );
        $typeParam = $base . self::CLASS_TYPE . $classAttribute->attribute( 'id' );
        $iniInstanceParam = $base . self::CLASS_INI_INSTANCE . $classAttribute->attribute( 'id' );

        if ( $http->hasPostVariable( $fileParam ) &&
             $http->hasPostVariable( $sectionParam ) &&
             $http->hasPostVariable( $parameterParam ) &&
             $http->hasPostVariable( $typeParam ) )
        {
            $iniFile = $http->postVariable( $fileParam );
            $iniSection = $http->postVariable( $sectionParam );
            $iniParameter = $http->postVariable( $parameterParam );
            $type = $http->postVariable( $typeParam );

            // These name the file, section and setting every published object
            // writes to, so they must not reach outside the settings tree or
            // break out of the line they are written on
            if ( !eZIniSettingType::isValidIniFileName( $iniFile ) ||
                 !eZIniSettingType::isValidIniName( $iniSection ) ||
                 !eZIniSettingType::isValidIniName( $iniParameter ) ||
                 !is_string( $type ) || !preg_match( '/^[1-6]$/', $type ) )
            {
                return eZInputValidator::STATE_INVALID;
            }

            // Every selected location must be one of the list the edit form
            // offers (override plus the available site accesses)
            if ( $http->hasPostVariable( $iniInstanceParam ) )
            {
                $iniInstanceArray = $http->postVariable( $iniInstanceParam );
                if ( !is_array( $iniInstanceArray ) )
                    $iniInstanceArray = explode( ';', (string)( is_scalar( $iniInstanceArray ) ? $iniInstanceArray : 'x' ) );
                $siteAccessArray = array_merge( array( 'override' ),
                                                eZINI::instance( 'site.ini' )->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );
                foreach ( $iniInstanceArray as $iniInstance )
                {
                    if ( !is_string( $iniInstance ) || eZIniSettingType::iniInstancePath( $iniInstance, $siteAccessArray ) === false )
                        return eZInputValidator::STATE_INVALID;
                }
            }

            $config = eZINI::instance( $iniFile );
            if ( $config == null )
            {
                return eZInputValidator::STATE_INVALID;
            }

            if ( !$config->hasGroup( $iniSection ) )
            {
                return eZInputValidator::STATE_INVALID;
            }
            return eZInputValidator::STATE_ACCEPTED;
        }

        // json_encode: a missing variable is null and a posted one may be an
        // array, neither of which concatenates into a string cleanly
        eZDebug::writeNotice( 'Could not validate parameters: ' . "\n" .
                              $fileParam . ': ' .  json_encode( $http->postVariable( $fileParam ) ) . "\n" .
                              $sectionParam . ': ' .  json_encode( $http->postVariable( $sectionParam ) ) . "\n" .
                              $parameterParam . ': ' .  json_encode( $http->postVariable( $parameterParam ) ) . "\n" .
                              $typeParam . ': ' .  json_encode( $http->postVariable( $typeParam ) ). "\n" .
                              $iniInstanceParam. ': '. json_encode( $http->postVariable( $iniInstanceParam ) ), 'eZIniSettingType::validateClassAttributeHTTPInput',
                              'eZIniSettingType::validateClassAttributeHTTPInput' );
        return eZInputValidator::STATE_INVALID;
    }

    function initializeClassAttribute( $classAttribute )
    {
        eZIniSettingType::setSiteAccessList( $classAttribute );
    }

    function initializeObjectAttribute( $objectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $objectAttribute->setAttribute( 'data_text', $originalContentObjectAttribute->attribute( 'data_text' ) );
        }
        else
        {
            $contentClassAttribute = $objectAttribute->attribute( 'contentclass_attribute' );
            $iniInstanceArray = explode( ';', (string)$contentClassAttribute->attribute( self::CLASS_INI_INSTANCE_FIELD ) );
            $siteAccessArray = explode( ';', (string)$contentClassAttribute->attribute( self::SITE_ACCESS_LIST_FIELD ) );
            $filename = $contentClassAttribute->attribute( self::CLASS_FILE_FIELD );
            $section = $contentClassAttribute->attribute( self::CLASS_SECTION_FIELD );
            $parameter = $contentClassAttribute->attribute( self::CLASS_PARAMETER_FIELD );

            // A broken class definition names no file that can be read
            if ( !eZIniSettingType::isValidIniFileName( $filename ) ||
                 !eZIniSettingType::isValidIniName( $section ) ||
                 !eZIniSettingType::isValidIniName( $parameter ) )
                return;

            if ( ! in_array( 'settings/override', array_map( function ( $i ) use ( $siteAccessArray ) { return eZIniSettingType::iniInstancePath( $i, $siteAccessArray ); }, $iniInstanceArray ), true ) )  /* Makes sure it check 'settings' and 'settings/override' last */
                array_unshift( $iniInstanceArray,  0 );
            array_unshift( $iniInstanceArray, -1 );

            $configArray = array();

            foreach ( $iniInstanceArray as $iniInstance )
            {
                $path = eZIniSettingType::iniInstancePath( $iniInstance, $siteAccessArray, true );
                if ( $path === false )
                    continue;

                if ( !eZINI::parameterSet( $filename, $path, $section, $parameter ) )
                    continue;

                $config = eZINI::instance( $filename, $path, null, null, null, true );

                $configValue = $config->variable( $section, $parameter );

                if ( is_array( $configValue ) )
                {
                    foreach ( array_keys( $configValue ) as $key )
                    {
                        $configArray[$key] = $configValue[$key];
                    }
                }
                else
                {
                    $objectAttribute->setAttribute( 'data_text', $configValue );
                    eZDebug::writeNotice( "Loaded following values from $path/$filename:\n    $configValue", __METHOD__ );
                }
            }

            if ( count( $configArray ) > 0 )
            {
                $data = '';
                foreach( array_keys( $configArray ) as $key )
                {
                    if ( is_int( $key ) )
                    {
                        $data .= '=' . $configArray[$key] . "\n" ;
                    }
                    else
                    {
                        $data .= $key . '=' . $configArray[$key] . "\n" ;
                    }
                }
                $objectAttribute->setAttribute( 'data_text', $data );
            }
        }
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $fileParam = $base . self::CLASS_FILE . $classAttribute->attribute( 'id' );
        $sectionParam = $base . self::CLASS_SECTION . $classAttribute->attribute( 'id' );
        $paramParam = $base . self::CLASS_PARAMETER . $classAttribute->attribute( 'id' );
        $typeParam = $base . self::CLASS_TYPE . $classAttribute->attribute( 'id' );
        $iniInstanceParam = $base . self::CLASS_INI_INSTANCE . $classAttribute->attribute( 'id' );

        if ( $http->hasPostVariable( $fileParam ) &&
             $http->hasPostVariable( $sectionParam ) &&
             $http->hasPostVariable( $paramParam ) &&
             $http->hasPostVariable( $typeParam ) )
        {
            $file = $http->postVariable( $fileParam );
            $section = $http->postVariable( $sectionParam );
            $parameter = $http->postVariable( $paramParam );
            $type = $http->postVariable( $typeParam );

            // Only plain strings are stored; a nested array or anything else
            // from a forged form would otherwise fail to convert
            foreach ( array( 'file', 'section', 'parameter', 'type' ) as $name )
            {
                if ( !is_string( $$name ) )
                    $$name = '';
            }

            $iniInstanceArray = $http->hasPostVariable( $iniInstanceParam ) ? $http->postVariable( $iniInstanceParam ) : [];
            if ( is_array( $iniInstanceArray ) )
            {
                // Locations are indexes into the site access list: anything
                // else cannot name one and is left out
                $iniInstance = implode( ';', array_filter( $iniInstanceArray, function ( $instance ) {
                    return is_string( $instance ) && preg_match( '/^\d+$/', $instance );
                } ) );
            }
            else
            {
                $iniInstance = is_string( $iniInstanceArray ) ? $iniInstanceArray : '';
            }

            eZIniSettingType::setSiteAccessList( $classAttribute );
            $classAttribute->setAttribute( self::CLASS_FILE_FIELD, $file );
            $classAttribute->setAttribute( self::CLASS_SECTION_FIELD, $section );
            $classAttribute->setAttribute( self::CLASS_PARAMETER_FIELD, $parameter );
            $classAttribute->setAttribute( self::CLASS_TYPE_FIELD, $type );
            $classAttribute->setAttribute( self::CLASS_INI_INSTANCE_FIELD, $iniInstance );

            return true;
        }
        return false;
    }

    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ini_setting_' . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . '_ini_setting_' . $contentObjectAttribute->attribute( "id" ) );
            // A forged array cannot be a setting value (validation rejects it)
            if ( !is_string( $data ) )
                return false;
            $contentObjectAttribute->setAttribute( 'data_text', trim( $data ) );
            if ( $http->hasPostVariable( $base . '_ini_setting_make_empty_array_' . $contentObjectAttribute->attribute( "id" ) ) )
            {
                $isChecked = $http->postVariable( $base . '_ini_setting_make_empty_array_' . $contentObjectAttribute->attribute( "id" ) );
                if ( isset( $isChecked ) )
                    $isChecked = 1;
                $contentObjectAttribute->setAttribute( 'data_int', $isChecked );
            }
            else
            {
                $contentObjectAttribute->setAttribute( 'data_int', 0 );
            }
            return true;
        }
        return false;
    }

    function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )
    {
        $contentClassAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
        $section = $contentClassAttribute->attribute( self::CLASS_SECTION_FIELD );
        $parameter = $contentClassAttribute->attribute( self::CLASS_PARAMETER_FIELD );
        $iniInstanceArray = explode( ';', (string)$contentClassAttribute->attribute( self::CLASS_INI_INSTANCE_FIELD ) );
        $siteAccessArray = explode( ';', (string)$contentClassAttribute->attribute( self::SITE_ACCESS_LIST_FIELD ) );
        $filename = $contentClassAttribute->attribute( self::CLASS_FILE_FIELD );
        $makeEmptyArray = $contentObjectAttribute->attribute( 'data_int' );
        $isArray = $contentClassAttribute->attribute( self::CLASS_TYPE_FIELD ) == self::CLASS_TYPE_ARRAY;
        $value = $contentObjectAttribute->attribute( 'data_text' );

        // This is the one place that writes to disk, so everything that goes
        // into the file is checked here again: the value may come from a
        // package or a string import that never passed HTTP validation, and the
        // class definition from a package or an older installation.
        if ( !eZIniSettingType::isValidIniFileName( $filename ) ||
             !eZIniSettingType::isValidIniName( $section ) ||
             !eZIniSettingType::isValidIniName( $parameter ) )
        {
            eZDebug::writeError( 'Refusing to write the ini setting of class attribute ' . $contentClassAttribute->attribute( 'id' ) .
                                 ': the file, section or setting name is not valid', __METHOD__ );
            return;
        }
        if ( $value !== null && $value !== '' &&
             !( $isArray ? eZIniSettingType::isValidIniArrayText( (string)$value ) : eZIniSettingType::isValidIniValue( (string)$value ) ) )
        {
            eZDebug::writeError( "Refusing to write $filename [$section] $parameter: the value holds a line break or a comment end", __METHOD__ );
            return;
        }

        foreach ( array_unique( $iniInstanceArray ) as $iniInstance )
        {
            $path = eZIniSettingType::iniInstancePath( $iniInstance, $siteAccessArray );
            if ( $path === false )
            {
                eZDebug::writeError( "Skipping ini location '$iniInstance': it names no known site access", __METHOD__ );
                continue;
            }

            $config = new eZINI( $filename . '.append', $path, null, false, null, true, true );

            if ( $config == null )
            {
                eZDebug::writeError( 'Could not open ' . $path . '/' . $filename );
                continue;
            }
            if ( $contentClassAttribute->attribute( self::CLASS_TYPE_FIELD ) == self::CLASS_TYPE_ARRAY )
            {
                if ( $contentObjectAttribute->attribute( 'data_text' ) != null )
                {
                    $iniArray = array();
                    eZIniSettingType::parseArrayInput( $contentObjectAttribute->attribute( 'data_text' ), $iniArray, $makeEmptyArray );
                    $config->setVariable( $section, $parameter, $iniArray );
                }
                else
                {
                    $config->removeSetting( $section, $parameter );
                }
            }
            else
            {
                $config->setVariable( $section, $parameter, $contentObjectAttribute->attribute( 'data_text' ) );
                eZDebug::writeNotice( 'Saved ini settings to file: ' . $path . '/' . $filename . "\n" .
                                      '                            ['. $section . ']' . "\n" .
                                      '                            ' . $parameter . '=' . $contentObjectAttribute->attribute( 'data_text' ),
                                      __METHOD__ );
            }
            $config->save();
        }
    }

    /*!
     \private
     Parse array input text into array with korrect keys.

     \param input text
     \param array to store parsed file to

     \return true if parsed successfully, false if illegal syntax
    */
    static function parseArrayInput( $inputText, &$outputArray, $makeEmptyArray = false )
    {
        // Static: the validation helpers call it without an instance. A stored
        // value may be null (a new attribute), which is no input at all.
        if ( $inputText === null )
            $inputText = '';
        if ( !is_string( $inputText ) )
            return false;
        $lineArray = explode( "\n", $inputText );

        if( $makeEmptyArray )
        {
            $outputArray[] = "";
        }

        foreach ( array_keys( $lineArray ) as $key )
        {
            $line = str_replace( "\r", '', $lineArray[$key] );

            if ( strlen( $line ) <= 2 )
                continue;

            if ( strstr( $line, '=' ) === false )
                return false;

            $lineElements = explode( '=', $line );
            if ( count( $lineElements ) == 1 )
            {
                $outputArray[] = $lineElements[0];
            }
            else
            {
                if ( $lineElements[0] != '' )
                {
                    $outputArray[ $lineElements[0] ] = implode( '=', array_slice( $lineElements, 1 ) );
                }
                else
                    $outputArray[] = implode( '=', array_slice( $lineElements, 1 ) );
            }
        }
        return true;
    }

    function objectAttributeContent( $contentObjectAttribute )
    {
        $contentClassAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
        $section = $contentClassAttribute->attribute( self::CLASS_SECTION_FIELD );
        $parameter = $contentClassAttribute->attribute( self::CLASS_PARAMETER_FIELD );

        $iniInstanceArray = explode( ';', (string)$contentClassAttribute->attribute( self::CLASS_INI_INSTANCE_FIELD ) );
        $siteAccessArray = explode( ';', (string)$contentClassAttribute->attribute( self::SITE_ACCESS_LIST_FIELD ) );
        $filename = $contentClassAttribute->attribute( self::CLASS_FILE_FIELD );

        $modified = array();

        $contentObject = $contentObjectAttribute->attribute( 'object' );
        // A broken class definition names no file to compare with
        if ( !eZIniSettingType::isValidIniFileName( $filename ) ||
             !eZIniSettingType::isValidIniName( $section ) ||
             !eZIniSettingType::isValidIniName( $parameter ) )
            $iniInstanceArray = array();
        foreach ( $iniInstanceArray as $iniInstance )
        {
            $path = eZIniSettingType::iniInstancePath( $iniInstance, $siteAccessArray );
            if ( $path === false )
                continue;

            if ( !eZINI::parameterSet( $filename, $path, $section, $parameter ) )
                continue;

            $config = eZINI::instance( $filename, $path, null, null, null, true );

            if ( is_array( $config->variable( $section, $parameter ) ) )
            {
                $objectIniArray = array();
                eZIniSettingType::parseArrayInput( $contentObjectAttribute->attribute( 'data_text' ), $objectIniArray );
                $existingIniArray = $config->variable( $section, $parameter );
                foreach ( array_keys( $existingIniArray ) as $key )
                {
                    // A key the object value does not have differs from the file too
                    if ( !is_int( $key ) && $existingIniArray[$key] != ( $objectIniArray[$key] ?? null ) )
                    {
                        $modified[] = array( 'ini_value' => $parameter . '[' . $key . ']=' . $existingIniArray[$key],
                                             'file' => $path . '/' . $filename );
                    }
                }
            }
            else if ( $config->variable( $section, $parameter ) != $contentObjectAttribute->attribute( 'data_text' ) )
            {
                $modified[] = array( 'ini_value' => $parameter . '=' . $config->variable( $section, $parameter ),
                                     'file' => $path . '/' . $filename );
            }
        }

        $data = array( 'data' => $contentObjectAttribute->attribute( 'data_text' ),
                       'modified' => $modified );
        return $data;
    }

    function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $file = $classAttribute->attribute( self::CLASS_FILE_FIELD );
        $section = $classAttribute->attribute( self::CLASS_SECTION_FIELD );
        $parameter = $classAttribute->attribute( self::CLASS_PARAMETER_FIELD );
        $type = $classAttribute->attribute( self::CLASS_TYPE_FIELD );
        $iniInstance = $classAttribute->attribute( self::CLASS_INI_INSTANCE_FIELD );
        $siteAccess = $classAttribute->attribute( self::SITE_ACCESS_LIST_FIELD );

        $dom = $attributeParametersNode->ownerDocument;
        $fileNode = $dom->createElement( 'file' );
        $fileNode->appendChild( $dom->createTextNode( $file ) );
        $attributeParametersNode->appendChild( $fileNode );
        $sectionNode = $dom->createElement( 'section' );
        $sectionNode->appendChild( $dom->createTextNode( $section ) );
        $attributeParametersNode->appendChild( $sectionNode );
        $parameterNode = $dom->createElement( 'parameter' );
        $parameterNode->appendChild( $dom->createTextNode( $parameter ) );
        $attributeParametersNode->appendChild( $parameterNode );
        $typeNode = $dom->createElement( 'type' );
        $typeNode->appendChild( $dom->createTextNode( $type ) );
        $attributeParametersNode->appendChild( $typeNode );
        $iniInstanceNode = $dom->createElement( 'ini_instance' );
        $iniInstanceNode->appendChild( $dom->createTextNode( $iniInstance ) );
        $attributeParametersNode->appendChild( $iniInstanceNode );
        $siteAccessListNode = $dom->createElement( 'site_access_list' );
        $siteAccessListNode->appendChild( $dom->createTextNode( $siteAccess ) );
        $attributeParametersNode->appendChild( $siteAccessListNode );
    }

    /*!

     Use Override to do ini alterations if the specified site access does not exist
    */
    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        // A package from another version may leave any of these out
        $text = function ( $name ) use ( $attributeParametersNode ) {
            $node = $attributeParametersNode->getElementsByTagName( $name )->item( 0 );
            return $node ? $node->textContent : '';
        };
        $file = $text( 'file' );
        $section = $text( 'section' );
        $parameter = $text( 'parameter' );
        $type = $text( 'type' );

        $classAttribute->setAttribute( self::CLASS_FILE_FIELD, $file );
        $classAttribute->setAttribute( self::CLASS_SECTION_FIELD, $section );
        $classAttribute->setAttribute( self::CLASS_PARAMETER_FIELD, $parameter );
        $classAttribute->setAttribute( self::CLASS_TYPE_FIELD, (int)$type );


        /* Get and check if site access settings exist in this setup */
        $remoteIniInstanceList = $text( 'ini_instance' );
        $remoteSiteAccessList = $text( 'site_access_list' );
        $remoteIniInstanceArray = explode( ';', $remoteIniInstanceList );
        $remoteSiteAccessArray = explode( ';', $remoteSiteAccessList );

        $config = eZINI::instance( 'site.ini' );
        $localSiteAccessArray = array_merge( array( 'override' ), $config->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );

        // Each remote location that is a site access of this installation too
        // is mapped to that site access' index here. (This used to append the
        // array of matching keys to the site access list instead, which stored
        // "Array" as a site access name and never kept a location.)
        $localIniInstanceArray = array();
        foreach ( $remoteIniInstanceArray as $remoteIniInstance )
        {
            if ( !preg_match( '/^\d+$/', trim( $remoteIniInstance ) ) )
                continue;
            $remoteIniInstance = (int)trim( $remoteIniInstance );
            if ( isset( $remoteSiteAccessArray[$remoteIniInstance] ) )
            {
                $localIndex = array_search( $remoteSiteAccessArray[$remoteIniInstance], $localSiteAccessArray, true );
                if ( $localIndex !== false && !in_array( $localIndex, $localIniInstanceArray, true ) )
                    $localIniInstanceArray[] = $localIndex;
            }
        }

        if ( count( $localIniInstanceArray ) == 0 )
        {
            $localIniInstanceArray = array( 0 );
        }

        $iniInstance = implode( ';', $localIniInstanceArray );
        $siteAccess = implode( ';', $localSiteAccessArray );

        $classAttribute->setAttribute( self::CLASS_INI_INSTANCE_FIELD, $iniInstance );
        $classAttribute->setAttribute( self::SITE_ACCESS_LIST_FIELD, $siteAccess );
    }


    /*!
     \private
     Get Ini section parameter name

     \param Content Class Attribute
    */
    function iniParameterName( $contentClassAttribute )
    {
        return $contentClassAttribute->attribute( self::CLASS_PARAMETER_FIELD );
    }

    /*!
     \private
     Get ini settings file

     \param Content Class Attribute
    */
    function iniFile( $contentClassAttribute )
    {
        return $contentClassAttribute->attribute( self::CLASS_FILE_FIELD );
    }

    /*!
     \private
     Get Ini file section name

     \param Content Class Attribute
    */
    function iniSection( $contentClassAttribute )
    {
        return $contentClassAttribute->attribute( self::CLASS_SECTION_FIELD );
    }

    /*!
     \private
     \static
     Set site access list, including override option

     \param contentClassAttribute to set site access list and override options
    */
    function setSiteAccessList( $contentClassAttribute )
    {
        $config = eZINI::instance( 'site.ini' );
        $siteAccessArray = $config->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        $siteAccessList = 'override';
        foreach ( $siteAccessArray as $idx => $siteAccess )
        {
            $siteAccessList .= ';' . $siteAccess;
        }

        $contentClassAttribute->setAttribute( self::SITE_ACCESS_LIST_FIELD, $siteAccessList );
    }

    function toString( $contentObjectAttribute )
    {
        $makeEmptyArray = $contentObjectAttribute->attribute( 'data_int' );
        $value = $contentObjectAttribute->attribute( 'data_text' );
        return implode( '|', array( $value, $makeEmptyArray ) );
    }


    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string == '' )
            return true;
        // toString() writes "value|flag" and a value may hold "|" itself (a
        // regular expression, a list), so the flag is what follows the LAST
        // "|", and only when it is a number; otherwise it all is the value.
        $separatorPos = strrpos( $string, '|' );
        $flag = $separatorPos === false ? false : substr( $string, $separatorPos + 1 );
        if ( $flag !== false && ( $flag === '' || ctype_digit( $flag ) ) )
        {
            $contentObjectAttribute->setAttribute( 'data_text', substr( $string, 0, $separatorPos ) );
            $contentObjectAttribute->setAttribute( 'data_int', (int)$flag );
        }
        else
        {
            $contentObjectAttribute->setAttribute( 'data_text', $string );
        }
        return true;
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );
        $makeEmptyArray = $objectAttribute->attribute( 'data_int' );
        $value = $objectAttribute->attribute( 'data_text' );

        $dom = $node->ownerDocument;

        $makeEmptyArrayNode = $dom->createElement( 'make_empty_array' );
        // (string): a new attribute has null in both fields
        $makeEmptyArrayNode->appendChild( $dom->createTextNode( (string)$makeEmptyArray ) );
        $node->appendChild( $makeEmptyArrayNode );
        $valueNode = $dom->createElement( 'value' );
        $valueNode->appendChild( $dom->createTextNode( (string)$value ) );
        $node->appendChild( $valueNode );

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        // Either element may be missing: item( 0 ) is then null, not a node
        $makeEmptyArrayNode = $attributeNode->getElementsByTagName( 'make_empty_array' )->item( 0 );
        $valueNode = $attributeNode->getElementsByTagName( 'value' )->item( 0 );
        $makeEmptyArray = $makeEmptyArrayNode ? (int)$makeEmptyArrayNode->textContent : 0;
        $value = $valueNode ? $valueNode->textContent : '';

        $objectAttribute->setAttribute( 'data_int', $makeEmptyArray );
        $objectAttribute->setAttribute( 'data_text', $value );
    }

    function diff( $old, $new, $options = false )
    {
        return null;
    }
}

eZDataType::register( eZIniSettingType::DATA_TYPE_STRING, 'eZIniSettingType' );

?>
