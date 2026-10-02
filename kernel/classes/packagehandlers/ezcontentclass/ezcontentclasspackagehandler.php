<?php
/**
 * File containing the eZContentClassPackageHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZContentClassPackageHandler ezcontentclasspackagehandler.php
  \brief Handles content classes in the package system

*/

class eZContentClassPackageHandler extends eZPackageHandler
{
    public $HandlerType;
    const ERROR_EXISTS = 1;
    const ERROR_HAS_OBJECTS = 101;

    const ACTION_REPLACE = 1;
    const ACTION_SKIP = 2;
    const ACTION_NEW = 3;
    const ACTION_DELETE = 4;
    /** Keep the existing class and bring it up to the package's definition: see updateExistingClass(). */
    const ACTION_UPDATE = 5;

    public function __construct()
    {
        parent::__construct( 'ezcontentclass', array( 'extract-install-content' => true ) );
    }

    /*!
     Returns an explanation for the content class install item.
     Use $requestedInfo to request portion of info.
    */
    function explainInstallItem( $package, $installItem, $requestedInfo = array( 'name', 'identifier', 'description', 'language_info' ) )
    {
        if ( $installItem['filename'] )
        {
            $explainClassName = in_array( 'name', $requestedInfo );
            $explainClassIdentitier = in_array( 'identifier', $requestedInfo );
            $explainDescription = in_array( 'description', $requestedInfo );
            $explainLanguageInfo = in_array( 'language_info', $requestedInfo );

            $filename = $installItem['filename'];
            $subdirectory = $installItem['sub-directory'];
            if ( $subdirectory )
                $filepath = $subdirectory . '/' . $filename . '.xml';
            else
                $filepath = $filename . '.xml';

            $filepath = $package->path() . '/' . $filepath;

            $dom = $package->fetchDOMFromFile( $filepath );
            if ( $dom )
            {
                $languageInfo = array();

                $content = $dom->documentElement;
                $classIdentifier = $explainClassIdentitier ? $content->getElementsByTagName( 'identifier' )->item( 0 )->textContent : '';

                $className = '';
                if ( $explainClassName )
                {
                    // BC ( <= 3.8 )
                    $classNameNode = $content->getElementsByTagName( 'name' )->item( 0 );

                    if( $classNameNode )
                    {
                        $className = $classNameNode->textContent;
                    }
                    else
                    {
                        // get info about translations.
                        $serializedNameListNode = $content->getElementsByTagName( 'serialized-name-list' )->item( 0 );
                        if( $serializedNameListNode )
                        {
                            $serializedNameList = $serializedNameListNode->textContent;
                            $nameList = new eZContentClassNameList( $serializedNameList );
                            $languageInfo = $explainLanguageInfo ? $nameList->languageLocaleList() : array();
                            $className = $nameList->name();
                        }
                    }
                }

                $description = $explainDescription ? ezpI18n::tr( 'kernel/package', "Content class '%classname' (%classidentifier)", false,
                                                             array( '%classname' => $className,
                                                                    '%classidentifier' => $classIdentifier ) ) : '';
                $explainInfo = array( 'description' => $description,
                                      'language_info' => $languageInfo );
                return $explainInfo;
            }
        }
    }

    /*!
     Uninstalls all previously installed content classes.
    */
    function uninstall( $package, $installType, $parameters,
                      $name, $os, $filename, $subdirectory,
                      $content, &$installParameters,
                      &$installData )
    {
        $classRemoteID = $content->getElementsByTagName( 'remote-id' )->item( 0 )->textContent;

        $class = eZContentClass::fetchByRemoteID( $classRemoteID );

        if ( $class == null )
        {
            eZDebug::writeNotice( "Class having remote id '$classRemoteID' not found.", __METHOD__ );
            return true;
        }

        if ( $class->isRemovable() )
        {
            $choosenAction = $this->errorChoosenAction( self::ERROR_HAS_OBJECTS,
                                                        $installParameters, false, $this->HandlerType );
            if ( $choosenAction == self::ACTION_SKIP )
            {
                return true;
            }
            if ( $choosenAction != self::ACTION_DELETE )
            {
                $objectsCount = eZContentObject::fetchSameClassListCount( $class->attribute( 'id' ) );
                $name = $class->attribute( 'name' );
                if ( $objectsCount )
                {
                    $installParameters['error'] = array( 'error_code' => self::ERROR_HAS_OBJECTS,
                                                         'element_id' => $classRemoteID,
                                                         'description' => ezpI18n::tr( 'kernel/package',
                                                                                  "Removing class '%classname' will result in the removal of %objectscount object(s) of this class and all their sub-items. Are you sure you want to uninstall it?",
                                                                                  false,
                                                                                  array( '%classname' => $name,
                                                                                         '%objectscount' => $objectsCount ) ),
                                                         'actions' => array( self::ACTION_DELETE => "Uninstall class and object(s)",
                                                                             self::ACTION_SKIP => 'Skip' ) );
                    return false;
                }
            }

            eZDebug::writeNotice( sprintf( "Removing class '%s' (%d)", $class->attribute( 'name' ), $class->attribute( 'id' ) ) );

            eZContentClassOperations::remove( $class->attribute( 'id' ) );
        }

        return true;
    }

    /*!
     Creates a new contentclass as defined in the xml structure.
    */
    function install( $package, $installType, $parameters,
                      $name, $os, $filename, $subdirectory,
                      $content, &$installParameters,
                      &$installData )
    {
        $serializedNameListNode = $content->getElementsByTagName( 'serialized-name-list' )->item( 0 );
        $serializedNameList = $serializedNameListNode ? $serializedNameListNode->textContent : false;
        $classNameList = new eZContentClassNameList( $serializedNameList );
        if ( $classNameList->isEmpty() )
        {
            $classNameList->initFromString( $content->getElementsByTagName( 'name' )->item( 0 )->textContent ); // for backward compatibility( <= 3.8 )
        }
        $classNameList->validate( );

        $serializedDescriptionListNode = $content->getElementsByTagName( 'serialized-description-list' )->item( 0 );
        $serializedDescriptionList = $serializedDescriptionListNode ? $serializedDescriptionListNode->textContent : false;
        $classDescriptionList = new eZSerializedObjectNameList( $serializedDescriptionList );

        $classIdentifier = $content->getElementsByTagName( 'identifier' )->item( 0 )->textContent;
        $classRemoteID = $content->getElementsByTagName( 'remote-id' )->item( 0 )->textContent;
        $classObjectNamePattern = $content->getElementsByTagName( 'object-name-pattern' )->item( 0 )->textContent;
        $classURLAliasPattern = is_object( $content->getElementsByTagName( 'url-alias-pattern' )->item( 0 ) ) ?
            $content->getElementsByTagName( 'url-alias-pattern' )->item( 0 )->textContent :
            null;
        $classIsContainer = $content->getAttribute( 'is-container' );
        if ( $classIsContainer !== false )
            $classIsContainer = $classIsContainer == 'true' ? 1 : 0;

        $classRemoteNode = $content->getElementsByTagName( 'remote' )->item( 0 );
        $classID = $classRemoteNode->getElementsByTagName( 'id' )->item( 0 )->textContent;
        $classGroupsNode = $classRemoteNode->getElementsByTagName( 'groups' )->item( 0 );
        $classCreated = $classRemoteNode->getElementsByTagName( 'created' )->item( 0 )->textContent;
        $classModified = $classRemoteNode->getElementsByTagName( 'modified' )->item( 0 )->textContent;
        $classCreatorNode = $classRemoteNode->getElementsByTagName( 'creator' )->item( 0 );
        $classModifierNode = $classRemoteNode->getElementsByTagName( 'modifier' )->item( 0 );

        $classAttributesNode = $content->getElementsByTagName( 'attributes' )->item( 0 );

        $dateTime = time();
        $classCreated = $dateTime;
        $classModified = $dateTime;

        $userID = false;
        if ( isset( $installParameters['user_id'] ) )
            $userID = $installParameters['user_id'];

        $class = eZContentClass::fetchByRemoteID( $classRemoteID );

        // Updating an existing class matches it by identifier as well: the same class installed
        // on another site may carry a remote id of its own, and creating "<identifier>_1" beside it
        // is not an update
        $wantsUpdate = isset( $installParameters['error_default_actions'][$this->HandlerType][self::ERROR_EXISTS] ) &&
                       $installParameters['error_default_actions'][$this->HandlerType][self::ERROR_EXISTS] == self::ACTION_UPDATE;
        if ( !$class && $wantsUpdate )
            $class = eZContentClass::fetchByIdentifier( $classIdentifier );

        if ( $class )
        {
            $className = $class->name();
            $description = ezpI18n::tr( 'kernel/package', "Class '%classname' already exists.", false,
                                   array( '%classname' => $className ) );

            $choosenAction = $this->errorChoosenAction( self::ERROR_EXISTS,
                                                        $installParameters, $description, $this->HandlerType );
            switch( $choosenAction )
            {
            case eZPackage::NON_INTERACTIVE:
                // Non-interactive installs (e.g. CLI) must not destructively
                // replace existing classes and their content objects.
                eZDebug::writeNotice( "Class '$className' already exists, skipping in non-interactive mode.", 'eZContentClassPackageHandler' );
            case self::ACTION_SKIP:
                return true;

            case self::ACTION_UPDATE:
                return $this->updateExistingClass( $class, $content, $classNameList, $classDescriptionList, $installParameters, $installData );

            case self::ACTION_REPLACE:
                if ( eZContentClassOperations::remove( $class->attribute( 'id' ) ) == false )
                {
                    eZDebug::writeWarning( "Unable to remove class '$className'." );
                    return true;
                }
                eZDebug::writeNotice( "Class '$className' will be replaced.", 'eZContentClassPackageHandler' );
                break;

            case self::ACTION_NEW:
                $class->setAttribute( 'remote_id', eZRemoteIdUtility::generate( 'class' ) );
                $class->store();
                $classNameList->appendGroupName( " (imported)" );
                break;

            default:
                $installParameters['error'] = array( 'error_code' => self::ERROR_EXISTS,
                                                     'element_id' => $classRemoteID,
                                                     'description' => $description,
                                                     'actions' => array() );
                if ( $class->isRemovable() )
                {
                    $errorMsg = ezpI18n::tr( 'kernel/package', "Replace existing class" );
                    $objectsCount = eZContentObject::fetchSameClassListCount( $class->attribute( 'id' ) );
                    if ( $objectsCount )
                        $errorMsg .= ' ' . ezpI18n::tr( 'kernel/package', "(Warning! $objectsCount content object(s) and their sub-items will be removed)" );
                    $installParameters['error']['actions'][self::ACTION_REPLACE] = $errorMsg;
                }
                $installParameters['error']['actions'][self::ACTION_UPDATE] = ezpI18n::tr( 'kernel/package', 'Update existing class (attributes are added and updated, none removed)' );
                $installParameters['error']['actions'][self::ACTION_SKIP] = ezpI18n::tr( 'kernel/package', 'Skip installing this class' );
                $installParameters['error']['actions'][self::ACTION_NEW] = ezpI18n::tr( 'kernel/package', 'Keep existing and create a new one' );
                return false;
            }
        }

        unset( $class );

        // Try to create a unique class identifier
        $currentClassIdentifier = $classIdentifier;
        $unique = false;

        while( !$unique )
        {
            $classList = eZContentClass::fetchByIdentifier( $currentClassIdentifier );
            if ( $classList )
            {
                // "increment" class identifier
                if ( preg_match( '/^(.*)_(\d+)$/', $currentClassIdentifier, $matches ) )
                    $currentClassIdentifier = $matches[1] . '_' . ( $matches[2] + 1 );
                else
                    $currentClassIdentifier = $currentClassIdentifier . '_1';
            }
            else
                $unique = true;

            unset( $classList );
        }

        $classIdentifier = $currentClassIdentifier;

        $values = array( 'version' => 0,
                         'serialized_name_list' => $classNameList->serializeNames(),
                         'serialized_description_list' => $classDescriptionList->serializeNames(),
                         'create_lang_if_not_exist' => true,
                         'identifier' => $classIdentifier,
                         'remote_id' => $classRemoteID,
                         'contentobject_name' => $classObjectNamePattern,
                         'url_alias_name' => $classURLAliasPattern,
                         'is_container' => $classIsContainer,
                         'created' => $classCreated,
                         'modified' => $classModified );

        if ( $content->hasAttribute( 'sort-field' ) )
        {
            $values['sort_field'] = eZContentObjectTreeNode::sortFieldID( $content->getAttribute( 'sort-field' ) );
        }
        else
        {
            eZDebug::writeNotice( 'The sort field was not specified in the content class package. ' .
                                  'This property is exported and imported since eZ Publish 4.0.2', __METHOD__ );
        }

        if ( $content->hasAttribute( 'sort-order' ) )
        {
            $values['sort_order'] = $content->getAttribute( 'sort-order' );
        }
        else
        {
            eZDebug::writeNotice( 'The sort order was not specified in the content class package. ' .
                                  'This property is exported and imported since eZ Publish 4.0.2', __METHOD__ );
        }

        if ( $content->hasAttribute( 'always-available' ) )
        {
            $values['always_available'] = ( $content->getAttribute( 'always-available' ) === 'true' ? 1 : 0 );
        }
        else
        {
            eZDebug::writeNotice( 'The default object availability was not specified in the content class package. ' .
                                  'This property is exported and imported since eZ Publish 4.0.2', __METHOD__ );
        }

        // create class
        $class = eZContentClass::create( $userID,
                                         $values );
        $class->store();

        $classID = $class->attribute( 'id' );

        if ( !isset( $installData['classid_list'] ) )
            $installData['classid_list'] = array();
        if ( !isset( $installData['classid_map'] ) )
            $installData['classid_map'] = array();
        $installData['classid_list'][] = $class->attribute( 'id' );
        $installData['classid_map'][$classID] = $class->attribute( 'id' );

        // create class attributes
        $classAttributeList = $classAttributesNode->getElementsByTagName( 'attribute' );
        foreach ( $classAttributeList as $classAttributeNode )
        {
            $isNotSupported = strtolower( $classAttributeNode->getAttribute( 'unsupported' ) ) == 'true';
            if ( $isNotSupported )
                continue;

            $attributeDatatype = $classAttributeNode->getAttribute( 'datatype' );
            $attributeIsRequired = strtolower( $classAttributeNode->getAttribute( 'required' ) ) == 'true';
            $attributeIsSearchable = strtolower( $classAttributeNode->getAttribute( 'searchable' ) ) == 'true';
            $attributeIsInformationCollector = strtolower( $classAttributeNode->getAttribute( 'information-collector' ) ) == 'true';
            $attributeIsTranslatable = strtolower( $classAttributeNode->getAttribute( 'translatable' ) ) == 'true';
            $attributeSerializedNameListNode = $classAttributeNode->getElementsByTagName( 'serialized-name-list' )->item( 0 );
            $attributeSerializedNameListContent = $attributeSerializedNameListNode ? $attributeSerializedNameListNode->textContent : false;
            $attributeSerializedNameList = new eZSerializedObjectNameList( $attributeSerializedNameListContent );
            if ( $attributeSerializedNameList->isEmpty() )
                $attributeSerializedNameList->initFromString( $classAttributeNode->getElementsByTagName( 'name' )->item( 0 )->textContent ); // for backward compatibility( <= 3.8 )
            $attributeSerializedNameList->validate( );

            $attributeSerializedDescriptionListNode = $classAttributeNode->getElementsByTagName( 'serialized-description-list' )->item( 0 );
            $attributeSerializedDescriptionListContent = $attributeSerializedDescriptionListNode ? $attributeSerializedDescriptionListNode->textContent : false;
            $attributeSerializedDescriptionList = new eZSerializedObjectNameList( $attributeSerializedDescriptionListContent );

            $attributeCategoryNode = $classAttributeNode->getElementsByTagName( 'category' )->item( 0 );
            $attributeCategory = $attributeCategoryNode ? $attributeCategoryNode->textContent : '';

            $attributeSerializedDataTextNode = $classAttributeNode->getElementsByTagName( 'serialized-description-text' )->item( 0 );
            $attributeSerializedDataTextContent = $attributeSerializedDataTextNode ? $attributeSerializedDataTextNode->textContent : false;
            $attributeSerializedDataText = new eZSerializedObjectNameList( $attributeSerializedDataTextContent );

            $attributeIdentifier = $classAttributeNode->getElementsByTagName( 'identifier' )->item( 0 )->textContent;
            $attributePlacement = $classAttributeNode->getElementsByTagName( 'placement' )->item( 0 )->textContent;
            $attributeDatatypeParameterNode = $classAttributeNode->getElementsByTagName( 'datatype-parameters' )->item( 0 );

            $classAttribute = $class->fetchAttributeByIdentifier( $attributeIdentifier );
            if ( !$classAttribute )
            {
                $classAttribute = eZContentClassAttribute::create( $class->attribute( 'id' ),
                                                                   $attributeDatatype,
                                                                   array( 'version' => 0,
                                                                          'identifier' => $attributeIdentifier,
                                                                          'serialized_name_list' => $attributeSerializedNameList->serializeNames(),
                                                                          'serialized_description_list' => $attributeSerializedDescriptionList->serializeNames(),
                                                                          'category' => $attributeCategory,
                                                                          'serialized_data_text' => $attributeSerializedDataText->serializeNames(),
                                                                          'is_required' => $attributeIsRequired,
                                                                          'is_searchable' => $attributeIsSearchable,
                                                                          'is_information_collector' => $attributeIsInformationCollector,
                                                                          'can_translate' => $attributeIsTranslatable,
                                                                          'placement' => $attributePlacement ) );

                $dataType = $classAttribute->dataType();
                $classAttribute->store();
                if ( $dataType != null )
                $dataType->unserializeContentClassAttribute( $classAttribute, $classAttributeNode, $attributeDatatypeParameterNode );
                $classAttribute->sync();
            }
        }

        // add class to a class group
        $classGroupsList = $classGroupsNode->getElementsByTagName( 'group' );
        foreach ( $classGroupsList as $classGroupNode )
        {
            $classGroupName = $classGroupNode->getAttribute( 'name' );
            $classGroup = eZContentClassGroup::fetchByName( $classGroupName );
            if ( !$classGroup )
            {
                $classGroup = eZContentClassGroup::create();
                $classGroup->setAttribute( 'name', $classGroupName );
                $classGroup->store();
            }
            $classGroup->appendClass( $class );
        }
        return true;
    }

    /**
     * ACTION_UPDATE: brings an existing class up to the package's definition without removing
     * anything. The class's names and descriptions (per language; a language only the site has
     * keeps its text), its name and URL alias patterns, container, availability and sorting come
     * from the package. Every attribute the package defines is updated - names, descriptions,
     * flags, category, placement and datatype parameters - or, when the class has no attribute of
     * that identifier, added and initialised in the class's existing objects. An attribute only the
     * site has is kept, and one whose datatype differs is left as it is: changing a datatype is not
     * an update of the definition but a conversion of the data.
     *
     * What was done is noted in $installData['class_update'][<identifier>] as
     * array( 'added', 'updated', 'kept', 'datatype_differs' ) lists of attribute identifiers.
     *
     * Transaction unsafe: eZPackage::installItem() runs it inside one.
     */
    function updateExistingClass( eZContentClass $class, DOMElement $content, $classNameList, $classDescriptionList, &$installParameters, &$installData )
    {
        $report = array( 'added' => array(), 'updated' => array(), 'kept' => array(), 'datatype_differs' => array() );
        $userID = isset( $installParameters['user_id'] ) ? $installParameters['user_id'] : eZUser::currentUserID();
        $text = function ( DOMElement $parent, $name )
        {
            $node = $parent->getElementsByTagName( $name )->item( 0 );
            return $node ? $node->textContent : null;
        };
        $knownLanguage = function ( $locale ) { return is_string( $locale ) && $locale !== '' && eZContentLanguage::fetchByLocale( $locale ); };

        foreach ( $classNameList->cleanNameList() as $locale => $name )
        {
            if ( $knownLanguage( $locale ) && is_string( $name ) )
                $class->setName( $name, $locale );
        }
        foreach ( $classDescriptionList->cleanNameList() as $locale => $description )
        {
            if ( $knownLanguage( $locale ) && is_string( $description ) )
                $class->setDescription( $description, $locale );
        }
        if ( ( $pattern = $text( $content, 'object-name-pattern' ) ) !== null )
            $class->setAttribute( 'contentobject_name', $pattern );
        if ( ( $pattern = $text( $content, 'url-alias-pattern' ) ) !== null )
            $class->setAttribute( 'url_alias_name', $pattern );
        if ( $content->hasAttribute( 'is-container' ) )
            $class->setAttribute( 'is_container', $content->getAttribute( 'is-container' ) == 'true' ? 1 : 0 );
        if ( $content->hasAttribute( 'always-available' ) )
            $class->setAttribute( 'always_available', $content->getAttribute( 'always-available' ) === 'true' ? 1 : 0 );
        if ( $content->hasAttribute( 'sort-field' ) )
            $class->setAttribute( 'sort_field', eZContentObjectTreeNode::sortFieldID( $content->getAttribute( 'sort-field' ) ) );
        if ( $content->hasAttribute( 'sort-order' ) )
            $class->setAttribute( 'sort_order', $content->getAttribute( 'sort-order' ) );
        $class->setAttribute( 'modified', time() );
        if ( $userID )
            $class->setAttribute( 'modifier_id', $userID );
        $class->NameList->setHasDirtyData( true );
        $class->store();

        $packageIdentifiers = array();
        $newAttributes = array();
        $classAttributesNode = $content->getElementsByTagName( 'attributes' )->item( 0 );
        $classAttributeList = $classAttributesNode ? $classAttributesNode->getElementsByTagName( 'attribute' ) : array();
        foreach ( $classAttributeList as $classAttributeNode )
        {
            if ( strtolower( $classAttributeNode->getAttribute( 'unsupported' ) ) == 'true' )
                continue;
            $identifier = $text( $classAttributeNode, 'identifier' );
            if ( $identifier === null || $identifier === '' )
                continue;
            $packageIdentifiers[$identifier] = true;
            $datatype = $classAttributeNode->getAttribute( 'datatype' );
            $nameList = new eZSerializedObjectNameList( (string)$text( $classAttributeNode, 'serialized-name-list' ) );
            $descriptionList = new eZSerializedObjectNameList( (string)$text( $classAttributeNode, 'serialized-description-list' ) );
            $parametersNode = $classAttributeNode->getElementsByTagName( 'datatype-parameters' )->item( 0 );
            $values = array(
                'is_required' => strtolower( $classAttributeNode->getAttribute( 'required' ) ) == 'true' ? 1 : 0,
                'is_searchable' => strtolower( $classAttributeNode->getAttribute( 'searchable' ) ) == 'true' ? 1 : 0,
                'is_information_collector' => strtolower( $classAttributeNode->getAttribute( 'information-collector' ) ) == 'true' ? 1 : 0,
                'can_translate' => strtolower( $classAttributeNode->getAttribute( 'translatable' ) ) == 'true' ? 1 : 0,
                'category' => (string)$text( $classAttributeNode, 'category' ),
                'placement' => (int)$text( $classAttributeNode, 'placement' ),
            );

            $classAttribute = $class->fetchAttributeByIdentifier( $identifier );
            if ( $classAttribute && $classAttribute->attribute( 'data_type_string' ) !== $datatype )
            {
                $report['datatype_differs'][] = $identifier;
                continue;
            }
            if ( !$classAttribute )
            {
                $nameList->validate();
                $classAttribute = eZContentClassAttribute::create( $class->attribute( 'id' ), $datatype,
                                                                   $values + array( 'version' => eZContentClass::VERSION_STATUS_DEFINED,
                                                                                    'identifier' => $identifier,
                                                                                    'serialized_name_list' => $nameList->serializeNames(),
                                                                                    'serialized_description_list' => $descriptionList->serializeNames() ) );
                if ( !$classAttribute->dataType() )
                {
                    $report['datatype_differs'][] = $identifier;
                    continue;
                }
                $classAttribute->store();
                $classAttribute->dataType()->unserializeContentClassAttribute( $classAttribute, $classAttributeNode, $parametersNode );
                $classAttribute->store();
                $newAttributes[] = $classAttribute;
                $report['added'][] = $identifier;
                continue;
            }
            foreach ( $values as $name => $value )
                $classAttribute->setAttribute( $name, $value );
            foreach ( $nameList->cleanNameList() as $locale => $name )
            {
                if ( $knownLanguage( $locale ) && is_string( $name ) )
                    $classAttribute->setName( $name, $locale );
            }
            foreach ( $descriptionList->cleanNameList() as $locale => $description )
            {
                if ( $knownLanguage( $locale ) && is_string( $description ) )
                    $classAttribute->setDescription( $description, $locale );
            }
            if ( $parametersNode && $classAttribute->dataType() )
                $classAttribute->dataType()->unserializeContentClassAttribute( $classAttribute, $classAttributeNode, $parametersNode );
            $classAttribute->store();
            $report['updated'][] = $identifier;
        }
        foreach ( $class->fetchAttributes() as $classAttribute )
        {
            if ( !isset( $packageIdentifiers[$classAttribute->attribute( 'identifier' )] ) )
                $report['kept'][] = $classAttribute->attribute( 'identifier' );
        }

        // An added attribute gets its value row in every existing object, as the class editor does
        foreach ( $newAttributes as $classAttribute )
            $classAttribute->initializeObjectAttributes();

        // What eZContentClass::storeVersioned() tells the caches after a class edit
        eZContentClass::expireCache();
        $handler = eZExpiryHandler::instance();
        $time = time();
        $handler->setTimestamp( 'user-class-cache', $time );
        $handler->setTimestamp( 'class-identifier-cache', $time );
        $handler->setTimestamp( 'sort-key-cache', $time );
        $handler->store();
        eZContentCacheManager::clearAllContentCache();

        if ( !isset( $installData['class_update'] ) )
            $installData['class_update'] = array();
        // Not in 'classid_list': that list is what uninstalling the package removes, and an updated
        // class belongs to the site, not to the package
        $installData['class_update'][$class->attribute( 'identifier' )] = $report;
        return true;
    }

    function add( $packageType, $package, $cli, $parameters )
    {
        foreach ( $parameters['class-list'] as $classItem )
        {
            $classID = $classItem['id'];
            $classIdentifier = $classItem['identifier'];
            $classValue = $classItem['value'];
            $cli->notice( "Adding class $classValue to package" );
            $this->addClass( $package, $classID, $classIdentifier );
        }
    }

    /*!
     \static
     Adds the content class with ID \a $classID to the package.
     If \a $classIdentifier is \c false then it will be fetched from the class.
    */
    static function addClass( $package, $classID, $classIdentifier = false )
    {
        $class = false;
        if ( is_numeric( $classID ) )
            $class = eZContentClass::fetch( $classID );
        if ( !$class )
            return;
        $classNode = eZContentClassPackageHandler::classDOMTree( $class );
        if ( !$classNode )
            return;
        if ( !$classIdentifier )
            $classIdentifier = $class->attribute( 'identifier' );
        $package->appendInstall( 'ezcontentclass', false, false, true,
                                 'class-' . $classIdentifier, 'ezcontentclass',
                                 array( 'content' => $classNode ) );
        $package->appendProvides( 'ezcontentclass', 'contentclass', $class->attribute( 'identifier' ) );
        $package->appendInstall( 'ezcontentclass', false, false, false,
                                 'class-' . $classIdentifier, 'ezcontentclass',
                                 array( 'content' => false ) );
    }

    function handleAddParameters( $packageType, $package, $cli, $arguments )
    {
        return $this->handleParameters( $packageType, $package, $cli, 'add', $arguments );
    }

    /*!
     \private
    */
    function handleParameters( $packageType, $package, $cli, $type, $arguments )
    {
        $classList = false;
        foreach ( $arguments as $argument )
        {
            if ( $argument[0] == '-' )
            {
                if ( strlen( $argument ) > 1 and
                     $argument[1] == '-' )
                {
                }
                else
                {
                }
            }
            else
            {
                if ( $classList === false )
                {
                    $classList = array();
                    $classArray = explode( ',', $argument );
                    $error = false;
                    foreach ( $classArray as $classID )
                    {
                        if ( in_array( $classID, $classList ) )
                        {
                            $cli->notice( "Content class $classID already in list" );
                            continue;
                        }
                        if ( is_numeric( $classID ) )
                        {
                            if ( !eZContentClass::exists( $classID, 0, false, false ) )
                            {
                                $cli->error( "Content class with ID $classID does not exist" );
                                $error = true;
                            }
                            else
                            {
                                unset( $class );
                                $class = eZContentClass::fetch( $classID );
                                $classList[] = array( 'id' => $classID,
                                                      'identifier' => $class->attribute( 'identifier' ),
                                                      'value' => $classID );
                            }
                        }
                        else
                        {
                            $realClassID = eZContentClass::exists( $classID, 0, false, true );
                            if ( !$realClassID )
                            {
                                $cli->error( "Content class with identifier $classID does not exist" );
                                $error = true;
                            }
                            else
                            {
                                unset( $class );
                                $class = eZContentClass::fetch( $realClassID );
                                $classList[] = array( 'id' => $realClassID,
                                                      'identifier' => $class->attribute( 'identifier' ),
                                                      'value' => $classID );
                            }
                        }
                    }
                    if ( $error )
                        return false;
                }
            }
        }
        if ( $classList === false )
        {
            $cli->error( "No class ids chosen" );
            return false;
        }
        return array( 'class-list' => $classList );
    }

    /*!
     \static
     Creates the DOM tree for the content class \a $class and returns the root node.
    */
    static function classDOMTree( $class )
    {
        if ( !$class )
        {
            $retValue = false;
            return $retValue;
        }

        $dom = new DOMDocument( '1.0', 'utf-8' );
        $classNode = $dom->createElement( 'content-class' );
        $dom->appendChild( $classNode );

        $serializedNameListNode = $dom->createElement( 'serialized-name-list' );
        $serializedNameListNode->appendChild( $dom->createTextNode( $class->attribute( 'serialized_name_list' ) ) );
        $classNode->appendChild( $serializedNameListNode );

        $identifierNode = $dom->createElement( 'identifier' );
        $identifierNode->appendChild( $dom->createTextNode( $class->attribute( 'identifier' ) ) );
        $classNode->appendChild( $identifierNode );

        $serializedDescriptionListNode = $dom->createElement( 'serialized-description-list' );
        $serializedDescriptionListNode->appendChild( $dom->createTextNode( $class->attribute( 'serialized_description_list' ) ) );
        $classNode->appendChild( $serializedDescriptionListNode );

        $remoteIDNode = $dom->createElement( 'remote-id' );
        $remoteIDNode->appendChild( $dom->createTextNode( $class->attribute( 'remote_id' ) ) );
        $classNode->appendChild( $remoteIDNode );

        $objectNamePatternNode = $dom->createElement( 'object-name-pattern' );
        $objectNamePatternNode->appendChild( $dom->createTextNode( $class->attribute( 'contentobject_name' ) ) );
        $classNode->appendChild( $objectNamePatternNode );

        $urlAliasPatternNode = $dom->createElement( 'url-alias-pattern' );
        $urlAliasPatternNode->appendChild( $dom->createTextNode( $class->attribute( 'url_alias_name' ) ) );
        $classNode->appendChild( $urlAliasPatternNode );

        $isContainer = $class->attribute( 'is_container' ) ? 'true' : 'false';
        $classNode->setAttribute( 'is-container', $isContainer );
        $classNode->setAttribute( 'always-available', $class->attribute( 'always_available' ) ? 'true' : 'false' );
        $classNode->setAttribute( 'sort-field', eZContentObjectTreeNode::sortFieldName( $class->attribute( 'sort_field' ) ) );
        $classNode->setAttribute( 'sort-order', $class->attribute( 'sort_order' ) );

        // Remote data start
        $remoteNode = $dom->createElement( 'remote' );
        $classNode->appendChild( $remoteNode );

        $ini = eZINI::instance();
        $siteName = $ini->variable( 'SiteSettings', 'SiteURL' );

        $classURL = 'http://' . $siteName . '/class/view/' . $class->attribute( 'id' );
        $siteURL = 'http://' . $siteName . '/';

        $siteUrlNode = $dom->createElement( 'site-url' );
        $siteUrlNode->appendChild( $dom->createTextNode( $siteURL ) );
        $remoteNode->appendChild( $siteUrlNode );

        $urlNode = $dom->createElement( 'url' );
        $urlNode->appendChild( $dom->createTextNode( $classURL ) );
        $remoteNode->appendChild( $urlNode );

        $classGroupsNode = $dom->createElement( 'groups' );

        $classGroupList = eZContentClassClassGroup::fetchGroupList( $class->attribute( 'id' ),
                                                                    $class->attribute( 'version' ) );
        foreach ( $classGroupList as $classGroupLink )
        {
            $classGroup = eZContentClassGroup::fetch( $classGroupLink->attribute( 'group_id' ) );
            if ( $classGroup )
            {
                unset( $groupNode );
                $groupNode = $dom->createElement( 'group' );
                $groupNode->setAttribute( 'id', $classGroup->attribute( 'id' ) );
                $groupNode->setAttribute( 'name', $classGroup->attribute( 'name' ) );
                $classGroupsNode->appendChild( $groupNode );
            }
        }
        $remoteNode->appendChild( $classGroupsNode );

        $idNode = $dom->createElement( 'id' );
        $idNode->appendChild( $dom->createTextNode( $class->attribute( 'id' ) ) );
        $remoteNode->appendChild( $idNode );
        $createdNode = $dom->createElement( 'created' );
        $createdNode->appendChild( $dom->createTextNode( $class->attribute( 'created' ) ) );
        $remoteNode->appendChild( $createdNode );
        $modifiedNode = $dom->createElement( 'modified' );
        $modifiedNode->appendChild( $dom->createTextNode( $class->attribute( 'modified' ) ) );
        $remoteNode->appendChild( $modifiedNode );

        $creatorNode = $dom->createElement( 'creator' );
        $remoteNode->appendChild( $creatorNode );
        $creatorIDNode = $dom->createElement( 'user-id' );
        $creatorIDNode->appendChild( $dom->createTextNode( $class->attribute( 'creator_id' ) ) );
        $creatorNode->appendChild( $creatorIDNode );
        $creator = $class->attribute( 'creator' );
        if ( $creator )
        {
            $creatorLoginNode = $dom->createElement( 'user-login' );
            $creatorLoginNode->appendChild( $dom->createTextNode( $creator->attribute( 'login' ) ) );
            $creatorNode->appendChild( $creatorLoginNode );
        }

        $modifierNode = $dom->createElement( 'modifier' );
        $remoteNode->appendChild( $modifierNode );
        $modifierIDNode = $dom->createElement( 'user-id' );
        $modifierIDNode->appendChild( $dom->createTextNode( $class->attribute( 'modifier_id' ) ) );
        $modifierNode->appendChild( $modifierIDNode );
        $modifier = $class->attribute( 'modifier' );
        if ( $modifier )
        {
            $modifierLoginNode = $dom->createElement( 'user-login' );
            $modifierLoginNode->appendChild( $dom->createTextNode( $modifier->attribute( 'login' ) ) );
            $modifierNode->appendChild( $modifierLoginNode );
        }
        // Remote data end

        $attributesNode = $dom->createElementNS( 'http://ezpublish/contentclassattribute', 'ezcontentclass-attribute:attributes' );
        $classNode->appendChild( $attributesNode );

        $attributes = $class->fetchAttributes();
        foreach( $attributes as $attribute )
        {
            $attributeNode = $dom->createElement( 'attribute' );
            $attributeNode->setAttribute( 'datatype', $attribute->attribute( 'data_type_string' ) );
            $required = $attribute->attribute( 'is_required' ) ? 'true' : 'false';
            $attributeNode->setAttribute( 'required' , $required );
            $searchable = $attribute->attribute( 'is_searchable' ) ? 'true' : 'false';
            $attributeNode->setAttribute( 'searchable' , $searchable );
            $informationCollector = $attribute->attribute( 'is_information_collector' ) ? 'true' : 'false';
            $attributeNode->setAttribute( 'information-collector' , $informationCollector );
            $translatable = $attribute->attribute( 'can_translate' ) ? 'true' : 'false';
            $attributeNode->setAttribute( 'translatable' , $translatable );

            $attributeRemoteNode = $dom->createElement( 'remote' );
            $attributeNode->appendChild( $attributeRemoteNode );

            $attributeIDNode = $dom->createElement( 'id' );
            $attributeIDNode->appendChild( $dom->createTextNode( $attribute->attribute( 'id' ) ) );
            $attributeRemoteNode->appendChild( $attributeIDNode );

            $attributeSerializedNameListNode = $dom->createElement( 'serialized-name-list' );
            $attributeSerializedNameListNode->appendChild( $dom->createTextNode( $attribute->attribute( 'serialized_name_list' ) ) );
            $attributeNode->appendChild( $attributeSerializedNameListNode );

            $attributeIdentifierNode = $dom->createElement( 'identifier' );
            $attributeIdentifierNode->appendChild( $dom->createTextNode( $attribute->attribute( 'identifier' ) ) );
            $attributeNode->appendChild( $attributeIdentifierNode );

            $attributeSerializedDescriptionListNode = $dom->createElement( 'serialized-description-list' );
            $attributeSerializedDescriptionListNode->appendChild( $dom->createTextNode( $attribute->attribute( 'serialized_description_list' ) ) );
            $attributeNode->appendChild( $attributeSerializedDescriptionListNode );

            $attributeCategoryNode = $dom->createElement( 'category' );
            $attributeCategoryNode->appendChild( $dom->createTextNode( $attribute->attribute( 'category' ) ) );
            $attributeNode->appendChild( $attributeCategoryNode );

            $attributeSerializedDataTextNode = $dom->createElement( 'serialized-data-text' );
            $attributeSerializedDataTextNode->appendChild( $dom->createTextNode( $attribute->attribute( 'serialized_data_text' ) ) );
            $attributeNode->appendChild( $attributeSerializedDataTextNode );

            $attributePlacementNode = $dom->createElement( 'placement' );
            $attributePlacementNode->appendChild( $dom->createTextNode( $attribute->attribute( 'placement' ) ) );
            $attributeNode->appendChild( $attributePlacementNode );

            $attributeParametersNode = $dom->createElement( 'datatype-parameters' );
            $attributeNode->appendChild( $attributeParametersNode );

            $dataType = $attribute->dataType();
            if ( is_object( $dataType ) )
            {
                $dataType->serializeContentClassAttribute( $attribute, $attributeNode, $attributeParametersNode );
            }

            $attributesNode->appendChild( $attributeNode );
        }
        eZDebug::writeDebug( $dom->saveXML(), 'content class package XML' );
        return $classNode;
    }

    function contentclassDirectory()
    {
        return 'ezcontentclass';
    }
}

?>
