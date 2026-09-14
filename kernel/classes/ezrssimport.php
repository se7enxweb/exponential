<?php
/**
 * File containing the eZRSSImport class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZRSSImport ezrssimport.php
  \brief Handles RSS Import in eZ Publish

  RSSImport is used to create RSS feeds from published content. See kernel/rss for more files.
*/

class eZRSSImport extends eZPersistentObject
{
    public $ObjectOwnerID;
    public $ModifierID;
    public $DestinationNodeID;
    const STATUS_VALID = 1;
    const STATUS_DRAFT = 0;

    static function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'modified' => array( 'name' => 'Modified',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'modifier_id' => array( 'name' => 'ModifierID',
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true,
                                                                 'foreign_class' => 'eZUser',
                                                                 'foreign_attribute' => 'contentobject_id',
                                                                 'multiplicity' => '1..*' ),
                                         'created' => array( 'name' => 'Created',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'creator_id' => array( 'name' => 'CreatorID',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true,
                                                                'foreign_class' => 'eZUser',
                                                                'foreign_attribute' => 'contentobject_id',
                                                                'multiplicity' => '1..*' ),
                                         'object_owner_id' => array( 'name' => 'ObjectOwnerID',
                                                                     'datatype' => 'integer',
                                                                     'default' => 0,
                                                                     'required' => true,
                                                                     'foreign_class' => 'eZUser',
                                                                     'foreign_attribute' => 'contentobject_id',
                                                                     'multiplicity' => '1..*' ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'name' => array( 'name' => 'Name',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'url' => array( 'name' => 'URL',
                                                         'datatype' => 'string',
                                                         'default' => '',
                                                         'required' => true ),
                                         'destination_node_id' => array( 'name' => 'DestinationNodeID',
                                                                         'datatype' => 'int',
                                                                         'default' => '',
                                                                         'required' => true,
                                                                         'foreign_class' => 'eZContentObjectTreeNode',
                                                                         'foreign_attribute' => 'node_id',
                                                                         'multiplicity' => '1..*' ),
                                         'class_id' => array( 'name' => 'ClassID',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true,
                                                              'foreign_class' => 'eZContentClass',
                                                              'foreign_attribute' => 'id',
                                                              'multiplicity' => '1..*' ),
                                         'class_title' => array( 'name' => 'ClassTitle', // deprecated
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         'class_url' => array( 'name' => 'ClassURL', // deprecated
                                                               'datatype' => 'string',
                                                               'default' => '',
                                                               'required' => false ),
                                         'class_description' => array( 'name' => 'ClassDescription', // deprecated
                                                                       'datatype' => 'string',
                                                                       'default' => '',
                                                                       'required' => false ),
                                         'active' => array( 'name' => 'Active',
                                                            'datatype' => 'integer',
                                                            'default' => 1,
                                                            'required' => true ),
                                         'import_description' => array( 'name' => 'ImportDescriptionValue',
                                                                        'datatype' => 'string',
                                                                        'default' => '',
                                                                        'required' => true ) ),
                      "keys" => array( "id", 'status' ),
                      'function_attributes' => array( 'class_attributes' => 'classAttributes',
                                                      'destination_path' => 'destinationPath',
                                                      'modifier' => 'modifier',
                                                      'object_owner' => 'objectOwner',
                                                      'import_description_array' => 'importDescription',
                                                      'field_map' => 'fieldMap',
                                                      'object_attribute_list' => 'objectAttributeList' ),
                      "increment_key" => "id",
                      "class_name" => "eZRSSImport",
                      "name" => "ezrss_import" );
    }

    /*!
     \static
     Creates a new RSS Import
     \param User ID

     \return the new RSS Import object
    */
    static function create( $userID = false )
    {
        if ( $userID === false )
        {
            $user = eZUser::currentUser();
            $userID = $user->attribute( "contentobject_id" );
        }

        $dateTime = time();
        $row = array( 'id' => null,
                      'name' => ezpI18n::tr( 'kernel/rss', 'New RSS Import' ),
                      'modifier_id' => $userID,
                      'modified' => $dateTime,
                      'creator_id' => $userID,
                      'created' => $dateTime,
                      'object_owner_id' => $userID,
                      'url' => '',
                      'status' => self::STATUS_DRAFT,
                      'destination_node_id' => 0,
                      'class_id' => 0,
                      'class_title' => '',
                      'class_url' => '',
                      'class_description' => '',
                      'active' => 1 );

        return new eZRSSImport( $row );
    }

    /*!
     Store Object to database
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function store( $fieldFilters = null )
    {
        $dateTime = time();
        $user = eZUser::currentUser();

        $this->setAttribute( 'modifier_id', $user->attribute( 'contentobject_id' ) );
        $this->setAttribute( 'modified', $dateTime );
        parent::store( $fieldFilters );
    }

    /*!
     \static
      Fetches the RSS Import by ID.

     \param RSS Import ID
    */
    static function fetch( $id, $asObject = true, $status = eZRSSImport::STATUS_VALID )
    {
        return eZPersistentObject::fetchObject( eZRSSImport::definition(),
                                                null,
                                                array( "id" => $id,
                                                       'status' => $status ),
                                                $asObject );
    }

    /*!
     \static
      Fetches complete list of RSS Imports.
    */
    /**
     * Fetches the RSS imports, a page at a time when asked.
     *
     * @param bool $asObject
     * @param int|false $status the status to filter on, false for every status.
     * @param int|false $offset first row to return.
     * @param int|false $limit  how many rows to return, false for all of them.
     * @param array|null $sorts  field => 'asc'|'desc', or null for the default order.
     * @return array
     */
    static function fetchList( $asObject = true, $status = eZRSSImport::STATUS_VALID, $offset = false, $limit = false, $sorts = null )
    {
        $cond = null;
        if ( $status !== false )
        {
            $cond = array( 'status' => $status );
        }

        $limitArray = null;
        if ( $limit !== false && $limit !== null )
            $limitArray = array( 'offset' => (int) $offset, 'length' => (int) $limit );

        return eZPersistentObject::fetchObjectList( eZRSSImport::definition(),
                                                    null, $cond, $sorts, $limitArray,
                                                    $asObject );
    }

    /**
     * The columns the list can be sorted by.
     *
     * @return array of field name.
     */
    static function sortableFields()
    {
        return array( 'id', 'name', 'url', 'active', 'modifier_id', 'modified' );
    }

    /**
     * How many RSS imports there are, without fetching any of them.
     *
     * @param int|false $status the status to filter on, false for every status.
     * @return int
     */
    static function fetchListCount( $status = eZRSSImport::STATUS_VALID )
    {
        $cond = null;
        if ( $status !== false )
        {
            $cond = array( 'status' => $status );
        }
        return (int) eZPersistentObject::count( eZRSSImport::definition(), $cond );
    }

    /*!
     \static
      Fetches complete list of active RSS Imports.
    */
    static function fetchActiveList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZRSSImport::definition(),
                                                    null,
                                                    array( 'status' => self::STATUS_VALID,
                                                           'active' => 1 ),
                                                    null,
                                                    null,
                                                    $asObject );
    }


    function objectOwner()
    {
        if ( isset( $this->ObjectOwnerID ) and $this->ObjectOwnerID )
        {
            return eZUser::fetch( $this->ObjectOwnerID );
        }
        return null;
    }

    function modifier()
    {
        if ( isset( $this->ModifierID ) and $this->ModifierID )
        {
            return eZUser::fetch( $this->ModifierID );
        }
        return null;
    }

    function classAttributes()
    {
        if ( isset( $this->ClassID ) and $this->ClassID )
        {
            $contentClass = eZContentClass::fetch( $this->ClassID );
            if ( $contentClass )
            {
                return $contentClass->fetchAttributes();
            }
        }
        return null;
    }

    function destinationPath()
    {
        $retValue = null;
        if ( isset( $this->DestinationNodeID ) and $this->DestinationNodeID )
        {
            $objectNode = eZContentObjectTreeNode::fetch( $this->DestinationNodeID );
            if ( isset( $objectNode ) )
            {
                $path_array = $objectNode->attribute( 'path_array' );
                $path_array_count = count( $path_array );
                for ( $i = 0; $i < $path_array_count; ++$i )
                {
                    $treenode = eZContentObjectTreeNode::fetch( $path_array[$i], false, false );
                    if ( is_array( $treenode ) && array_key_exists( 'name', $treenode ) )
                    {
                        if ( $i == 0 )
                        {
                            $retValue = $treenode['name'];
                        }
                        else
                        {
                            $retValue .= '/' . $treenode['name'];
                        }
                    }
                }
            }
        }
        return $retValue;
    }

    /**
     * Whether an address is one this server should be fetching.
     *
     * Only http and https, and only with a host in them. Everything else -
     * file:, ftp:, gopher:, php://, a bare path - is refused: the fetch happens
     * on the server, with whatever the server can reach, and a feed address is
     * not a reason to go looking at the local disk.
     *
     * @param string $url
     * @return bool
     */
    static function isFetchableURL( $url )
    {
        return self::fetchableURL( $url ) !== false;
    }

    /**
     * The address to actually fetch, or false if it is not one to fetch.
     *
     * Returns the trimmed address rather than a yes or no, so the caller hands
     * curl exactly the string that was checked. Checking one string and
     * fetching another is how a trailing newline - harmless to the check -
     * reaches a library that may treat it as the start of something else.
     *
     * @param mixed $url
     * @return string|false
     */
    static function fetchableURL( $url )
    {
        if ( !is_string( $url ) )
            return false;

        // A null byte is refused before anything else, and wherever it sits.
        // trim() would quietly remove one from the end, and a string that has
        // been through a C library with a null in it is not the string anybody
        // looked at.
        if ( strpos( $url, "\0" ) !== false )
            return false;

        $url = trim( $url );
        if ( $url === '' || strlen( $url ) > 2048 )
            return false;

        // No control characters or whitespace left inside: both are used to
        // slip a scheme past a check that only looks at the beginning.
        if ( preg_match( '/[\x00-\x20\x7F]/', $url ) )
            return false;

        $parts = @parse_url( $url );
        if ( !is_array( $parts ) || !isset( $parts['scheme'], $parts['host'] ) )
            return false;

        if ( !in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) )
            return false;

        return trim( $parts['host'] ) !== '' ? $url : false;
    }

    /*!
     \static
     Analize RSS import, and get RSS version number

     \param URL

     \return RSS version number, false if invalid URL
    */
    static function getRSSVersion( $url )
    {
        // The address is typed into the admin interface and then fetched by the
        // server, so it has to be an address the server should be fetching. curl
        // will happily open file://, and follows redirects into whatever they
        // point at, which turns a feed address into a way of reading the disk.
        $url = self::fetchableURL( $url );
        if ( $url === false )
        {
            eZDebug::writeError( 'Refusing to fetch a feed from an address that is not http or https', __METHOD__ );
            return false;
        }

        $xmlData = eZHTTPTool::getDataByURL( $url );

        if ( $xmlData === false || !is_string( $xmlData ) || trim( $xmlData ) === '' )
            return false;

        // Create DomDocument from http data
        //
        // The document comes from somewhere else, so it is parsed with external
        // entities refused and the network switched off: a feed that declares a
        // DTD pointing at a local file must not be able to read that file, and
        // must not be able to make this server fetch anything on its behalf.
        $domDocument = new DOMDocument( '1.0', 'utf-8' );
        $domDocument->resolveExternals = false;
        $domDocument->substituteEntities = false;

        $previousErrors = libxml_use_internal_errors( true );
        $success = $domDocument->loadXML( $xmlData, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $previousErrors );

        if ( !$success || !$domDocument->documentElement instanceof DOMElement )
        {
            return false;
        }

        $root = $domDocument->documentElement;

        switch( $root->getAttribute( 'version' ) )
        {
            default:
            case '1.0':
            {
                return '1.0';
            } break;

            case '0.91':
            case '0.92':
            case '2.0':
            {
                return $root->getAttribute( 'version' );
            } break;
        }
    }

    /*!
     \static
     Object attribute list
    */
    static function objectAttributeList()
    {
        return array( 'published' => 'Published',
                      'modified' => 'Modified' );
    }

    /*!
     \static

     Return default RSS field definition

     \param RSS version

     \return RSS field definition array.
    */
    static function rssFieldDefinition( $version = '2.0' )
    {
        switch ( $version )
        {
            case '1.0':
            {
                return array( 'item' => array( 'attributes' => array( 'about' ),
                                               'elements' => array( 'title',
                                                                    'link',
                                                                    'description' ) ),
                              'channel' => array( 'attributes' => array( 'about' ),
                                                  'elements' => array( 'title',
                                                                       'link',
                                                                       'description'.
                                                                       'image' => array( 'attributes' => array( 'resource' ) ) ) ) );
            } break;

            case '2.0':
            case '0.91':
            case '0.92':
            {
                return array( 'item' => array( 'elements' => array( 'title',
                                                                    'link',
                                                                    'description',
                                                                    'author',
                                                                    'category',
                                                                    'comments',
                                                                    'guid',
                                                                    'pubDate' ) ),
                              'channel' => array( 'elements' => array( 'title',
                                                                       'link',
                                                                       'description',
                                                                       'copyright',
                                                                       'managingEditor',
                                                                       'webMaster',
                                                                       'pubDate',
                                                                       'lastBuildDate',
                                                                       'category',
                                                                       'generator',
                                                                       'docs',
                                                                       'cloud',
                                                                       'ttl' ) ) );
            }
        }
    }

    /*!
     \static

     \param RSS version

     \return Ordered array of field definitions
    */
    static function fieldMap( $version = '2.0' )
    {
        $fieldDefinition = eZRSSImport::rssFieldDefinition();

        $ini = eZINI::instance();
        foreach( $ini->variable( 'RSSSettings', 'ActiveExtensions' ) as $activeExtension )
        {
            $extensionPath = eZExtension::extensionPath( $activeExtension );
            if ( $extensionPath === false )
                continue;

            $rssImportFile = $extensionPath . '/rss/' . $activeExtension . 'rssimport.php';
            if ( file_exists( $rssImportFile ) )
            {
                include_once( $rssImportFile );
                $fieldDefinition = eZRSSImport::arrayMergeRecursive( $fieldDefinition, call_user_func( array(  $activeExtension . 'rssimport', 'rssFieldDefinition' ), array() ) );
            }
        }

        $returnArray = array();
        eZRSSImport::recursiveFieldMap( $fieldDefinition, '', '', $returnArray, 0 );

        return $returnArray;
    }

    /*!
     \static

     Recursivly build field map

     \param array
    */
    static function recursiveFieldMap( $definitionArray, $globalKey, $value, &$returnArray, $count )
    {
        foreach( $definitionArray as $key => $definition )
        {
            if ( is_string( $definition ) )
            {
                $returnArray[$globalKey . ' - ' . $definition ] = $value . ' - ' . ucfirst( $definition );
            }
            else
            {
                eZRSSImport::recursiveFieldMap( $definition,
                                                $globalKey . ( strlen( $globalKey ) ? ' - ' : '' ) . $key ,
                                                $value . ( strlen( $value ) && ( $count % 2 == 0 ) ? ' - ' : '' ) . ( $count % 2 == 0 ? ucfirst( $key ) : '' ),
                                                $returnArray, $count + 1 );
            }
        }
    }

    /*!
     Set import description

     Import definition must be set as an multidimentional array.

     Example : array( 'rss_version' => <version>,
                      'object_attributes' => array( ... ),
                      'class_attributes' => array( <content class attribute id> => <RSS import field>,  ... ) )
    */
    function setImportDescription( $definition = array() )
    {
        $this->setAttribute( 'import_description', serialize( $definition ) );
    }

    /*!
     Get import description

     \return import description
    */
    function importDescription()
    {
        $description = @unserialize( $this->attribute( 'import_description' ) );
        if ( !$description )
        {
            $description = array();
        }
        return $description;
    }

    static function arrayMergeRecursive( $arr1, $arr2 )
    {
        if ( !is_array( $arr1 ) ||
             !is_array( $arr2 ) )
        {
            return $arr2;
        }
        foreach ($arr2 AS $key => $value )
        {
            $arr1[$key] = eZRSSImport::arrayMergeRecursive( @$arr1[$key], $value);
        }

        return $arr1;
    }
}

?>
