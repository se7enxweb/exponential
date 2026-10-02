<?php
/**
 * Subitems list columns read from the content object: class, section, owner, languages,
 * states and the size of its main text.
 *
 * Field= picks the column: class_identifier, class_id, class_groups, section_identifier, section_id,
 * owner, owner_id, initial_language, language_count, language_codes, missing_translations,
 * always_available, language_mask, state_identifiers, locked, word_count, text_length, name_length,
 * attribute_count. Most read the loaded object; class_groups, owner and section_identifier cost one
 * cached fetch, the text fields read the data map (one query per object, shared with other columns).
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsObjectColumn extends expSubitemsFieldColumn
{
    /** The datatypes whose stored text counts as the object's main text, in order of preference. */
    const TEXT_TYPES = array( 'ezxmltext', 'ezrichtext', 'eztext' );

    protected function fieldClassIdentifier( eZContentObjectTreeNode $node )
    {
        $identifier = $node->attribute( 'class_identifier' );
        return $identifier ? (string)$identifier : null;
    }

    protected function fieldClassId( eZContentObjectTreeNode $node )
    {
        return self::objectInt( $node, 'contentclass_id' );
    }

    /** The names of the class groups the object's class is in ("Content", "Media" ...). */
    protected function fieldClassGroups( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        $classID = (int)$object->attribute( 'contentclass_id' );
        return self::memo( 'classgroups', $classID, function () use ( $classID )
        {
            $class = eZContentClass::fetch( $classID );
            if ( !$class instanceof eZContentClass )
                return null;
            $names = array();
            foreach ( $class->fetchGroupList() as $group )
                $names[] = (string)$group->attribute( 'group_name' );
            return $names ? $names : null;
        } );
    }

    protected function fieldSectionIdentifier( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        $sectionID = (int)$object->attribute( 'section_id' );
        return self::memo( 'section', $sectionID, function () use ( $sectionID )
        {
            $section = eZSection::fetch( $sectionID );
            return $section instanceof eZSection ? (string)$section->attribute( 'identifier' ) : null;
        } );
    }

    protected function fieldSectionId( eZContentObjectTreeNode $node )
    {
        return self::objectInt( $node, 'section_id' );
    }

    /** The owner's name (the user who created the object), null when the owner is gone. */
    protected function fieldOwner( eZContentObjectTreeNode $node )
    {
        $ownerID = self::objectInt( $node, 'owner_id' );
        return $ownerID === null ? null : self::objectName( $ownerID );
    }

    protected function fieldOwnerId( eZContentObjectTreeNode $node )
    {
        $id = (int)self::objectInt( $node, 'owner_id' );
        return $id > 0 ? $id : null;
    }

    /** The locale the object was first written in, e.g. eng-GB. */
    protected function fieldInitialLanguage( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        $code = $object->initialLanguageCode();
        return $code ? (string)$code : null;
    }

    protected function fieldLanguageCount( eZContentObjectTreeNode $node )
    {
        $codes = $this->fieldLanguageCodes( $node );
        return $codes === null ? null : count( $codes );
    }

    /** The locales the object is translated into, in the site's language priority. */
    protected function fieldLanguageCodes( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        $codes = $object->availableLanguages();
        return is_array( $codes ) ? array_values( $codes ) : null;
    }

    /** The languages of the installation the object has no translation in. */
    protected function fieldMissingTranslations( eZContentObjectTreeNode $node )
    {
        $codes = $this->fieldLanguageCodes( $node );
        if ( $codes === null )
            return null;
        return array_values( array_diff( eZContentLanguage::fetchLocaleList(), $codes ) );
    }

    /** Shown in every language, also those it is not translated into (language_mask bit 0). */
    protected function fieldAlwaysAvailable( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        return $object ? (bool)$object->isAlwaysAvailable() : null;
    }

    protected function fieldLanguageMask( eZContentObjectTreeNode $node )
    {
        return self::objectInt( $node, 'language_mask' );
    }

    /** Every object state as group/state identifiers: "ez_lock/not_locked". */
    protected function fieldStateIdentifiers( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        $states = $object->stateIdentifierArray();
        return is_array( $states ) && $states ? array_values( $states ) : null;
    }

    /** Whether the object is in the "locked" state of the ez_lock group; null when there is no such group. */
    protected function fieldLocked( eZContentObjectTreeNode $node )
    {
        $states = $this->fieldStateIdentifiers( $node );
        if ( $states === null )
            return null;
        foreach ( $states as $state )
        {
            if ( strpos( $state, 'ez_lock/' ) === 0 )
                return $state === 'ez_lock/locked';
        }
        return null;
    }

    /** Words in the main text (the first rich text, XML text or text block with content). */
    protected function fieldWordCount( eZContentObjectTreeNode $node )
    {
        $text = $this->mainText( $node );
        if ( $text === null )
            return null;
        return (int)preg_match_all( '/[\p{L}\p{N}][\p{L}\p{N}\'\-]*/u', $text );
    }

    /** Characters in the main text, markup left out. */
    protected function fieldTextLength( eZContentObjectTreeNode $node )
    {
        $text = $this->mainText( $node );
        return $text === null ? null : mb_strlen( $text, 'UTF-8' );
    }

    /** Characters in the object's name. */
    protected function fieldNameLength( eZContentObjectTreeNode $node )
    {
        $name = (string)$node->getName();
        return $name === '' ? null : mb_strlen( $name, 'UTF-8' );
    }

    /** Attributes the object has in its current version and language. */
    protected function fieldAttributeCount( eZContentObjectTreeNode $node )
    {
        $map = self::dataMap( $node );
        return $map ? count( $map ) : null;
    }

    protected static function prefetchSets()
    {
        return array( 'DataMap' => array( 'word_count', 'text_length', 'attribute_count' ) );
    }

    /** The plain text of the main text attribute (tags stripped, entities decoded, spaces folded), or null. */
    protected function mainText( eZContentObjectTreeNode $node )
    {
        $identifiers = $this->listSetting( 'Attributes' );
        $attribute = self::firstAttribute( $node, self::TEXT_TYPES,
                                           $identifiers ? $identifiers : array( 'body', 'description', 'intro', 'short_description' ) );
        if ( !$attribute )
            return null;
        return self::plainText( (string)$attribute->attribute( 'data_text' ) );
    }

    /** Markup (XML text, rich text, HTML) as one line of plain text, words kept apart where blocks end. */
    public static function plainText( $markup )
    {
        return self::oneLine( self::markupToText( $markup, true ) );
    }

    /** The name of the content object $id (users mostly), memoised; null when it does not exist. */
    public static function objectName( $id )
    {
        $id = (int)$id;
        if ( $id <= 0 )
            return null;
        return self::memo( 'objectname', $id, function () use ( $id )
        {
            $object = eZContentObject::fetch( $id );
            return $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : null;
        } );
    }
}
