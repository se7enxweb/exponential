<?php
/**
 * File containing the eZContentObjectPackageCreator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \ingroup package
  \class eZContentObjectPackageCreator ezcontentclasspackagecreator.php
  \brief A package creator for content objects
*/

class eZContentObjectPackageCreator extends eZPackageCreationHandler
{
    public function __construct( $id )
    {
        $steps = array();
        $steps[] = array( 'id' => 'object',
                          'name' => ezpI18n::tr( 'kernel/package', 'Content objects to include' ),
                          'methods' => array( 'initialize' => 'initializeObjectList',
                                              'load' => 'loadObjectList',
                                              'validate' => 'validateObjectList' ),
                          'template' => 'object_select.tpl' );
        $steps[] = array( 'id' => 'object_limits',
                          'name' => ezpI18n::tr( 'kernel/package', 'Content object limits' ),
                          'methods' => array( 'initialize' => 'initializeObjectLimits',
                                              'load' => 'loadObjectLimits',
                                              'validate' => 'validateObjectLimits' ),
                          'template' => 'object_limit.tpl' );
        $steps[] = $this->packageInformationStep();
        $steps[] = $this->packageMaintainerStep();
        $steps[] = $this->packageChangelogStep();
        parent::__construct( $id, ezpI18n::tr( 'kernel/package', 'Content object export' ), $steps );
    }

    /*!
     Creates the package and adds the selected content classes.
    */
    function finalize( &$package, $http, &$persistentData )
    {
        $this->createPackage( $package, $http, $persistentData, $cleanupFiles );

        $objectHandler = eZPackage::packageHandler( 'ezcontentobject' );
        $nodeList = $persistentData['node_list'];
        $options = $persistentData['object_options'];

        foreach( $nodeList as $nodeInfo )
        {
            $objectHandler->addNode( $nodeInfo['id'], $nodeInfo['type'] == 'subtree' );
        }
        $objectHandler->generatePackage( $package, $options );

        $package->setAttribute( 'is_active', true );
        $package->store();
    }

    /*!
     Returns \c 'stable', content class packages are always stable.
    */
    function packageInitialState( $package, &$persistentData )
    {
        return 'stable';
    }

    /*!
     \return \c 'contentclass'.
    */
    function packageType( $package, &$persistentData )
    {
        return 'contentobject';
    }

    function initializeObjectList( $package, $http, $step, &$persistentData, $tpl )
    {
        // An entry (package/create/(creator)/ezcontentobject/(node)/N ...) starts with its node or subtree selected
        $persistentData['node_list'] = isset( $persistentData['entry']['node_list'] ) && is_array( $persistentData['entry']['node_list'] )
                                     ? $persistentData['entry']['node_list'] : array();
    }

    function loadObjectList( $package, $http, $step, &$persistentData, $tpl, &$module )
    {
        if ( $http->hasPostVariable( 'AddSubtree' ) )
        {
            eZContentBrowse::browse( array( 'action_name' => 'FindLimitationSubtree',
                                            'description_template' => 'design:package/creators/ezcontentobject/browse_subtree.tpl',
                                            'from_page' => '/package/create',
                                            'persistent_data' => array( 'PackageStep' => $http->postVariable( 'PackageStep' ),
                                                                        'CreatorItemID' => $http->postVariable( 'CreatorItemID' ),
                                                                        'CreatorStepID' => $http->postVariable( 'CreatorStepID' ),
                                                                        'Subtree' => 1 ) ),
                                     $module );
        }
        else if ( $http->hasPostVariable( 'AddNode' ) )
        {
            eZContentBrowse::browse( array( 'action_name' => 'FindLimitationNode',
                                            'description_template' => 'design:package/creators/ezcontentobject/browse_node.tpl',
                                            'from_page' => '/package/create',
                                            'persistent_data' => array( 'PackageStep' => $http->postVariable( 'PackageStep' ),
                                                                        'CreatorItemID' => $http->postVariable( 'CreatorItemID' ),
                                                                        'CreatorStepID' => $http->postVariable( 'CreatorStepID' ),
                                                                        'Node' => 1) ),
                                     $module );
        }
        else if( $http->hasPostVariable( 'RemoveSelected' ) )
        {
            foreach ( array_keys( $persistentData['node_list'] ) as $key )
            {
                if ( in_array( $persistentData['node_list'][$key]['id'], $http->postVariable( 'DeleteIDArray' ) ) )
                {
                    unset( $persistentData['node_list'][$key] );
                }
            }
        }
        else if( $http->hasPostVariable( 'SelectedNodeIDArray' ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
        {
            if ( $http->hasPostVariable( 'Subtree' ) &&
                 $http->hasPostVariable( 'Subtree' ) == 1 )
            {
                foreach( $http->postVariable( 'SelectedNodeIDArray' ) as $nodeID )
                {
                    $persistentData['node_list'][] = array( 'id' => $nodeID,
                                                            'type' => 'subtree' );
                }
            }
            else if ( $http->hasPostVariable( 'Node' ) &&
                 $http->hasPostVariable( 'Node' ) == 1 )
            {
                foreach( $http->postVariable( 'SelectedNodeIDArray' ) as $nodeID )
                {
                    $persistentData['node_list'][] = array( 'id' => $nodeID,
                                                            'type' => 'node' );
                }
            }
        }

        $tpl->setVariable( 'node_list', $persistentData['node_list'] );
    }

    /*!
     Checks if at least one content class has been selected.
    */
    function validateObjectList( $package, $http, $currentStepID, &$stepMap, &$persistentData, &$errorList )
    {
        if ( count( $persistentData['node_list'] ) == 0 )
        {
            $errorList[] = array( 'field' => ezpI18n::tr( 'kernel/package', 'Selected nodes' ),
                                  'description' => ezpI18n::tr( 'kernel/package', 'You must select one or more node(s)/subtree(s) for export.' ) );
            return false;
        }

        return true;
    }

    function initializeObjectLimits( $package, $http, $step, &$persistentData, $tpl )
    {
        $persistentData['object_options'] = array( 'include_classes' => 1,
                                                   'include_templates' => 0, // templates are opted into: they are site design, not content
                                                   'site_access_array' => array(),
                                                   'versions' => 'current',
                                                   'language_array' => array(),
                                                   'node_assignment' => 'selected',
                                                   'related_objects' => 'selected',
                                                   'embed_objects' => 'selected' );

        $ini = eZINI::instance();
        $persistentData['object_options']['site_access_array'] = array( $ini->variable( 'SiteSettings', 'DefaultAccess' ) );

        $availableLanguages = eZContentObject::translationList();
        foreach ( $availableLanguages as $language )
        {
            $persistentData['object_options']['language_array'][] = $language->attribute( 'locale_code' );
        }

        // The choices an entry preset (the content tree's Export menu): only the ones it may set
        if ( isset( $persistentData['entry']['object_options'] ) && is_array( $persistentData['entry']['object_options'] ) )
        {
            foreach ( $persistentData['entry']['object_options'] as $name => $value )
            {
                if ( in_array( $name, array( 'include_classes', 'versions' ), true ) )
                    $persistentData['object_options'][$name] = $value;
            }
        }
    }

    function loadObjectLimits( $package, $http, $step, &$persistentData, $tpl, &$module )
    {
        $ini = eZINI::instance();
        $availableSiteAccesses = $ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' );

        $availableLanguages = eZContentObject::translationList();
        $availableLanguageArray = array();
        foreach ( $availableLanguages as $language )
        {
            $availableLanguageArray[] = array( 'name' => $language->attribute( 'language_name' ),
                                               'locale' => $language->attribute( 'locale_code' ) );
        }

        $tpl->setVariable( 'available_site_accesses', $availableSiteAccesses );
        $tpl->setVariable( 'available_languages', $availableLanguageArray );
        $tpl->setVariable( 'options', $persistentData['object_options'] );
    }

    /*!
     Checks if at least one content class has been selected.
    */
    function validateObjectLimits( $package, $http, $currentStepID, &$stepMap, &$persistentData, &$errorList )
    {
        $options =& $persistentData['object_options'];

        $options['include_classes'] = $http->hasPostVariable( 'IncludeClasses' ) ? $http->postVariable( 'IncludeClasses' ) : false;
        $options['include_templates'] = $http->hasPostVariable( 'IncludeTemplates' ) ? $http->postVariable( 'IncludeTemplates' ) : false;
        $options['site_access_array'] = $http->postVariable( 'SiteAccesses' );
        $options['versions'] = $http->postVariable( 'VersionExport' );
        $options['language_array'] = $http->postVariable( 'Languages' );
        $options['node_assignment'] = $http->postVariable( 'NodeAssignment' );
        $options['related_objects'] = $http->postVariable( 'RelatedObjects' );
        $options['minimal_template_set'] = $http->hasPostVariable( 'MinimalTemplateSet' ) ? $http->postVariable( 'MinimalTemplateSet' ) : false;

        $result = true;
        if ( count( $persistentData['object_options']['language_array'] ) == 0 )
        {
            $errorList[] = array( 'field' => ezpI18n::tr( 'kernel/package', 'Selected nodes' ),
                                  'description' => ezpI18n::tr( 'kernel/package', 'You must choose one or more languages.' ) );
            $result = false;
        }

        if ( $persistentData['object_options']['include_templates'] &&
             count( $persistentData['object_options']['site_access_array'] ) == 0 )
        {
            $errorList[] = array( 'field' => ezpI18n::tr( 'kernel/package', 'Selected nodes' ),
                                  'description' => ezpI18n::tr( 'kernel/package', 'You must choose one or more site access.' ) );
            $result = false;
        }

        return $result;
    }

    /**
     * The entry parameters of package/create that open this wizard pre-filled (the content tree's Advanced > Export
     * menu): package/create/(creator)/ezcontentobject/(node)/<node id>[/(subtree)/1][/(classes)/0|1][/(versions)/current|all].
     * Only reads and checks them; the view checks the node and the policies. Each value is false when it was not
     * given (eZModule's unordered parameters), a string when it was.
     *
     * @param array<string, mixed> $params the view's parameters: Creator, NodeID, Subtree, IncludeClasses, Versions
     * @return array{node_id: int, subtree: bool, object_options: array<string, mixed>, node_list: array<int, array{id: int, type: string}>, errors: array<int, string>}
     */
    static function entryParameters( array $params )
    {
        $value = function ( $name ) use ( $params )
        {
            return isset( $params[$name] ) && $params[$name] !== false && $params[$name] !== null ? (string)$params[$name] : null;
        };
        $errors = array();
        $creator = $value( 'Creator' );
        if ( $creator !== 'ezcontentobject' )
            $errors[] = 'creator';
        $nodeID = self::positiveID( $value( 'NodeID' ) );
        if ( $nodeID === 0 )
            $errors[] = 'node';
        $subtree = $value( 'Subtree' );
        if ( $subtree !== null && $subtree !== '0' && $subtree !== '1' )
            $errors[] = 'subtree';
        $options = array();
        $classes = $value( 'IncludeClasses' );
        if ( $classes !== null )
        {
            if ( $classes === '0' || $classes === '1' )
                $options['include_classes'] = (int)$classes;
            else
                $errors[] = 'classes';
        }
        $versions = $value( 'Versions' );
        if ( $versions !== null )
        {
            if ( $versions === 'current' || $versions === 'all' )
                $options['versions'] = $versions;
            else
                $errors[] = 'versions';
        }
        $type = $subtree === '1' ? 'subtree' : 'node';
        return array( 'node_id' => $nodeID,
                      'subtree' => $type === 'subtree',
                      'object_options' => $options,
                      'node_list' => $nodeID ? array( array( 'id' => $nodeID, 'type' => $type ) ) : array(),
                      'errors' => $errors );
    }

    /**
     * Whether the view was opened with entry parameters (any of them given).
     *
     * @param array<string, mixed> $params the view's parameters
     */
    static function isEntry( array $params )
    {
        foreach ( array( 'Creator', 'NodeID', 'Subtree', 'IncludeClasses', 'Versions' ) as $name )
        {
            if ( isset( $params[$name] ) && $params[$name] !== false && $params[$name] !== null )
                return true;
        }
        return false;
    }

    /**
     * A database id given as text: 1 to 2147483647 in plain digits, else 0.
     *
     * @param mixed $value
     */
    static function positiveID( $value )
    {
        if ( is_int( $value ) )
            return $value > 0 && $value <= 2147483647 ? $value : 0;
        if ( !is_string( $value ) || $value === '' || strlen( $value ) > 10 || !ctype_digit( $value ) || $value[0] === '0' )
            return 0;
        return (int)$value <= 2147483647 ? (int)$value : 0;
    }

    /**
     * Whether the results of hasAccessTo() for package/create and package/export let a user export content as a
     * package: the content object wizard (CreatorType ezcontentobject) for content object packages (Type
     * contentobject), and the download of the package made. A limited policy counts when it does not leave either
     * out.
     *
     * @param array<string, mixed> $createAccess hasAccessTo( 'package', 'create' )
     * @param array<string, mixed> $exportAccess hasAccessTo( 'package', 'export' )
     */
    static function policyAllowsContentExport( array $createAccess, array $exportAccess )
    {
        $allows = function ( array $access, array $required )
        {
            $word = isset( $access['accessWord'] ) ? $access['accessWord'] : 'no';
            if ( $word === 'yes' )
                return true;
            if ( $word !== 'limited' || !isset( $access['policies'] ) || !is_array( $access['policies'] ) )
                return false;
            foreach ( $access['policies'] as $limitations )
            {
                $fits = true;
                foreach ( $required as $identifier => $needed )
                {
                    if ( is_array( $limitations ) && isset( $limitations[$identifier] ) && !in_array( $needed, (array)$limitations[$identifier] ) )
                        $fits = false;
                }
                if ( $fits )
                    return true;
            }
            return false;
        };
        return $allows( $createAccess, array( 'CreatorType' => 'ezcontentobject', 'Type' => 'contentobject' ) )
            && $allows( $exportAccess, array( 'Type' => 'contentobject' ) );
    }

    /**
     * policyAllowsContentExport() for the current user.
     */
    static function currentUserCanExportContent()
    {
        $user = eZUser::currentUser();
        return self::policyAllowsContentExport( $user->hasAccessTo( 'package', 'create' ), $user->hasAccessTo( 'package', 'export' ) );
    }

    /*!
     Fetches the selected content classes and generates a name, summary and description from the selection.
    */
    function generatePackageInformation( &$packageInformation, $package, $http, $step, &$persistentData )
    {
        $nodeList = $persistentData['node_list'];
        $options = $persistentData['object_options'];
        $nodeCount = 0;
        $description = 'This package contains the following nodes :' . "\n";
        $nodeNames = array();
        foreach( $nodeList as $nodeInfo )
        {
            $contentNode = eZContentObjectTreeNode::fetch( $nodeInfo['id'] );
            $description .= $contentNode->attribute( 'name' ) . ' - ' . $nodeInfo['type'] . "\n";
            $nodeNames[] = trim( $contentNode->attribute( 'name' ) );
            if ( $nodeInfo['type'] == 'node' )
            {
                ++$nodeCount;
            }
            else if ( $nodeInfo['type'] == 'subtree' )
            {
                $nodeCount += $contentNode->subTreeCount();
            }
        }

        $packageInformation['name'] = implode( ',', $nodeNames );
        $packageInformation['summary'] = implode( ', ', $nodeNames );
        $packageInformation['description'] = $description;
    }

}
?>
