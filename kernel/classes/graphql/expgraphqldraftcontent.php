<?php
/**
 * A content object read in one of its versions (a draft, an archived version) instead of the published one: the
 * fields and the name of that version, in one language. The answer of preview(token:), of content(id:, version:)
 * and of a mutation that leaves a draft (publish: false). Its location is the main location of the published
 * object, or none for an object that was never published.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLDraftContent extends expGraphQLContent
{
    /** @var int */
    protected $version;

    /**
     * @param eZContentObject $object
     * @param int $version
     * @param string|false $language
     */
    public function __construct( $object, $version, $language = false )
    {
        $node = $object instanceof eZContentObject ? $object->attribute( 'main_node' ) : null;
        parent::__construct( $object, $node instanceof eZContentObjectTreeNode ? $node : null, $language );
        $this->version = (int)$version;
    }

    /**
     * @return int the version read
     */
    public function versionNumber()
    {
        return $this->version;
    }

    /**
     * @return eZContentObjectVersion|null
     */
    public function versionObject()
    {
        $version = $this->object->version( $this->version );
        return $version instanceof eZContentObjectVersion ? $version : null;
    }

    public function languages()
    {
        $version = $this->versionObject();
        if ( !$version instanceof eZContentObjectVersion )
            return parent::languages();
        return array_values( array_map( 'strval', (array)$version->translationList( false, false ) ) );
    }

    public function language()
    {
        $languages = $this->languages();
        if ( $this->language && in_array( $this->language, $languages, true ) )
            return $this->language;
        $version = $this->versionObject();
        $initial = $version instanceof eZContentObjectVersion ? $version->initialLanguageCode() : '';
        return $initial !== '' ? (string)$initial : parent::language();
    }

    public function name()
    {
        $version = $this->versionObject();
        if ( $version instanceof eZContentObjectVersion )
        {
            $name = (string)$version->versionName( $this->language() );
            if ( $name !== '' )
                return $name;
        }
        return parent::name();
    }

    public function modified()
    {
        $version = $this->versionObject();
        return $version instanceof eZContentObjectVersion ? (int)$version->attribute( 'modified' ) : parent::modified();
    }

    public function dataMap()
    {
        if ( $this->dataMap === null )
            $this->dataMap = (array)$this->object->fetchDataMap( $this->version, $this->language() );
        return $this->dataMap;
    }
}

?>
