<?php
/**
 * A content object as the GraphQL schema sees it: the object, the location it was reached by (a node of a list,
 * the node a query named, else the main location) and the translation it is read in. The resolvers read only
 * through these methods; a test extends the class to answer them without a database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLContent
{
    /** @var eZContentObject|null */
    protected $object;

    /** @var eZContentObjectTreeNode|null the location the content was reached by */
    protected $node;

    /** @var string|false the translation asked for (false: the siteaccess's languages) */
    protected $language;

    /** @var array|null attribute identifier => eZContentObjectAttribute */
    protected $dataMap = null;

    /** @var callable|null run before the URL alias is read: makes the aliases of the whole page at once */
    public $urlPrimer = null;

    /**
     * @param eZContentObject|null $object
     * @param eZContentObjectTreeNode|null $node
     * @param string|false $language
     */
    public function __construct( $object, $node = null, $language = false )
    {
        $this->object = $object;
        $this->node = $node;
        $this->language = $language;
    }

    /** @return eZContentObject|null */
    public function object()
    {
        return $this->object;
    }

    /** @return int */
    public function id()
    {
        return (int)$this->object->attribute( 'id' );
    }

    /** @return string */
    public function remoteId()
    {
        return (string)$this->object->attribute( 'remote_id' );
    }

    /** @return string */
    public function classIdentifier()
    {
        return (string)$this->object->attribute( 'class_identifier' );
    }

    /** @return string */
    public function className()
    {
        return (string)$this->object->attribute( 'class_name' );
    }

    /**
     * @return string the translation the fields are read in: the one asked for when the object has it, else the
     *                first of the siteaccess's languages it has
     */
    public function language()
    {
        $languages = $this->languages();
        if ( $this->language && in_array( $this->language, $languages, true ) )
            return $this->language;
        $current = $this->object->currentLanguage();
        return $current ? (string)$current : (string)$this->object->attribute( 'initial_language_code' );
    }

    /** @return string[] the translations of the object */
    public function languages()
    {
        return array_values( array_map( 'strval', (array)$this->object->availableLanguages() ) );
    }

    /** @return string */
    public function mainLanguage()
    {
        return (string)$this->object->attribute( 'initial_language_code' );
    }

    /**
     * @return string the name in the translation read: from the object's own row when it was fetched in that
     *                translation (a list, a location), else from the names of its translations
     */
    public function name()
    {
        $language = $this->language();
        $current = $this->object->currentLanguage();
        if ( $current && $current === $language && (string)$this->object->attribute( 'name', true ) !== '' )
            return (string)$this->object->attribute( 'name', true ); // the field, not the function attribute name (a query)
        return (string)$this->object->name( false, $language );
    }

    /**
     * Makes the URL aliases of the page this content came with, once, before one of them is read.
     */
    public function primeUrl()
    {
        if ( $this->urlPrimer !== null )
        {
            $primer = $this->urlPrimer;
            $this->urlPrimer = null;
            $primer();
        }
    }

    /** @return int a Unix time */
    public function published()
    {
        return (int)$this->object->attribute( 'published' );
    }

    /** @return int a Unix time */
    public function modified()
    {
        return (int)$this->object->attribute( 'modified' );
    }

    /** @return int */
    public function ownerId()
    {
        return (int)$this->object->attribute( 'owner_id' );
    }

    /** @return string the owner's name (the name of the owner's user object) */
    public function ownerName()
    {
        $owner = eZContentObject::fetch( $this->ownerId() );
        return $owner instanceof eZContentObject ? (string)$owner->attribute( 'name' ) : '';
    }

    /** @return int */
    public function sectionId()
    {
        return (int)$this->object->attribute( 'section_id' );
    }

    /**
     * @return eZContentObjectAttribute[] attribute identifier => attribute, of the translation read
     */
    public function dataMap()
    {
        if ( $this->dataMap === null )
        {
            // The object's own data map (loaded with the list it came from) unless another translation is asked for
            $language = $this->language();
            $current = (string)$this->object->currentLanguage();
            $this->dataMap = ( $language === '' || $language === $current ) ? (array)$this->object->dataMap() : (array)$this->object->fetchDataMap( false, $language );
        }
        return $this->dataMap;
    }

    /**
     * @param string $identifier
     * @return eZContentObjectAttribute|null
     */
    public function attribute( $identifier )
    {
        $dataMap = $this->dataMap();
        return isset( $dataMap[$identifier] ) ? $dataMap[$identifier] : null;
    }

    /**
     * @return expGraphQLLocation|null the location the content was reached by, else its main location
     */
    public function location()
    {
        if ( $this->node instanceof eZContentObjectTreeNode )
            return new expGraphQLLocation( $this->node, $this->language );
        return $this->mainLocation();
    }

    /** @return expGraphQLLocation|null */
    public function mainLocation()
    {
        $node = $this->object->attribute( 'main_node' );
        return $node instanceof eZContentObjectTreeNode ? new expGraphQLLocation( $node, $this->language ) : null;
    }

    /** @return expGraphQLLocation[] */
    public function locations()
    {
        $locations = array();
        foreach ( (array)$this->object->assignedNodes( true ) as $node )
        {
            if ( $node instanceof eZContentObjectTreeNode && $node->canRead() )
                $locations[] = new expGraphQLLocation( $node, $this->language );
        }
        return $locations;
    }

    /**
     * @return string|false the translation asked for
     */
    public function requestedLanguage()
    {
        return $this->language;
    }
}

?>
