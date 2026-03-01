<?php

class xrowSiteUpdater
{
    public static function convert( $class_identifier, $from_identifier, $to_identifier )
    {
        if ( isset( $class_identifier ) )
        {
            $class = eZContentClass::fetchByIdentifier( $class_identifier );
        }
        if ( ! is_object( $class ) )
        {

            return;
        }
        $count = eZContentObject::fetchSameClassListCount( $class->ID );
        $output = new ezcConsoleOutput();
        $bar = new ezcConsoleProgressbar( $output, (int) $count );
        $offset = 0;
        $limit = 50;
        while ( true )
        {
            if ( $offset > $count )
            {
                break;
            }
            $objects = eZContentObject::fetchSameClassList( $class->ID, true, $offset, $limit );
            foreach ( $objects as $object )
            {

                $datamap = $object->dataMap();
                $content = $datamap[$from_identifier]->content();
                if ( isset( $content->KeywordArray ) and count( $content->KeywordArray ) > 0 and isset( $datamap[$to_identifier] ) )
                {

                    $meta = new xrowMetaData( false, $content->KeywordArray );

                    $datamap[$to_identifier]->setContent( $meta );

                    $datamap[$to_identifier]->store();

                }
                $bar->advance();
            }
            eZContentObject::clearCache();
            $offset += $limit;
        }
        $bar->finish();
    }

    public static function removeClassAttribute( $class_identifier, $attribute_identifier )
    {
        if ( isset( $class_identifier ) )
        {
            $class = eZContentClass::fetchByIdentifier( $class_identifier );
        }
        if ( ! is_object( $class ) )
        {

            return;
        }

        $classAttributeIdentifier = $attribute_identifier;

        // get attributes of 'temporary' version as well
        $classAttributeList = eZContentClassAttribute::fetchFilteredList( array(
            'contentclass_id' => $class->ID ,
            'identifier' => $classAttributeIdentifier
        ), true );

        $validation = array();
        foreach ( $classAttributeList as $classAttribute )
        {
            $dataType = $classAttribute->dataType();
            if ( $dataType->isClassAttributeRemovable( $classAttribute ) )
            {
                $objectAttributes = eZContentObjectAttribute::fetchSameClassAttributeIDList( $classAttribute->attribute( 'id' ) );
                foreach ( $objectAttributes as $objectAttribute )
                {
                    $objectAttributeID = $objectAttribute->attribute( 'id' );
                    $objectAttribute->removeThis( $objectAttributeID );
                }

                $classAttribute->removeThis();
            }
            else
            {
                $removeInfo = $dataType->classAttributeRemovableInformation( $classAttribute );

            }
        }

    }

    public static function addClassAttributes( $class_identifier, $attributesInfo )
    {
        if ( isset( $class_identifier ) )
        {
            $class = eZContentClass::fetchByIdentifier( $class_identifier );
        }

        if ( ! is_object( $class ) )
        {

            return;
        }

        $classID = $class->attribute( 'id' );

        foreach ( $attributesInfo as $attributeInfo )
        {
            $classAttributeIdentifier = $attributeInfo['identifier'];
            $classAttributeName = $attributeInfo['name'];
            $datatype = $attributeInfo['data_type_string'];
            $defaultValue = isset( $attributeInfo['default_value'] ) ? $attributeInfo['default_value'] : false;
            $canTranslate = isset( $attributeInfo['can_translate'] ) ? $attributeInfo['can_translate'] : 1;
            $isRequired = isset( $attributeInfo['is_required'] ) ? $attributeInfo['is_required'] : 0;
            $isSearchable = isset( $attributeInfo['is_searchable'] ) ? $attributeInfo['is_searchable'] : 1;
            $attrContent = isset( $attributeInfo['content'] ) ? $attributeInfo['content'] : false;

            $attrCreateInfo = array(
                'identifier' => $classAttributeIdentifier ,
                'name' => $classAttributeName ,
                'can_translate' => $canTranslate ,
                'is_required' => $isRequired ,
                'is_searchable' => $isSearchable
            );
            $newAttribute = eZContentClassAttribute::create( $classID, $datatype, $attrCreateInfo );

            $dataType = $newAttribute->dataType();
            $dataType->initializeClassAttribute( $newAttribute );

            // not all datatype can have 'default_value'. do check here.
            if ( $defaultValue !== false )
            {
                switch ( $datatype )
                {
                    case 'ezboolean':
                        {
                            $newAttribute->setAttribute( 'data_int3', $defaultValue );
                        }
                        break;

                    default:
                        break;
                }
            }

            if ( $attrContent )
                $newAttribute->setContent( $attrContent );

     // store attribute, update placement, etc...
            $attributes = $class->fetchAttributes();
            $attributes[] = $newAttribute;

            // remove temporary version
            if ( $newAttribute->attribute( 'id' ) !== null )
            {
                $newAttribute->remove();
            }

            $newAttribute->setAttribute( 'version', eZContentClass::VERSION_STATUS_DEFINED );
            $newAttribute->setAttribute( 'placement', count( $attributes ) );

            $class->adjustAttributePlacements( $attributes );
            foreach ( $attributes as $attribute )
            {
                $attribute->storeDefined();
            }

            // update objects
            $classAttributeID = $newAttribute->attribute( 'id' );
            $count = eZContentObject::fetchSameClassListCount( $class->ID );
            $output = new ezcConsoleOutput();
            $bar = new ezcConsoleProgressbar( $output, (int) $count );
            $offset = 0;
            $limit = 50;
            while ( true )
            {
                if ( $offset > $count )
                {
                    break;
                }
                $objects = eZContentObject::fetchSameClassList( $classID, true, $offset, $limit );
                foreach ( $objects as $object )
                {
                    $contentobjectID = $object->attribute( 'id' );
                    $objectVersions = $object->versions();
                    foreach ( $objectVersions as $objectVersion )
                    {
                        $translations = $objectVersion->translations( false );
                        $version = $objectVersion->attribute( 'version' );
                        foreach ( $translations as $translation )
                        {
                            $objectAttribute = eZContentObjectAttribute::create( $classAttributeID, $contentobjectID, $version );
                            $objectAttribute->setAttribute( 'language_code', $translation );
                            $objectAttribute->initialize();
                            $objectAttribute->store();
                            $objectAttribute->postInitialize();
                        }
                    }
                    $bar->advance();
                }
                eZContentObject::clearCache();
                $offset += $limit;
            }
            $bar->finish();
        }
    }
}
