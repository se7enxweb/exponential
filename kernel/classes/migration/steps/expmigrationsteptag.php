<?php
/**
 * File containing the expMigrationStepTag class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "tag": the tags of the extension eztags. Creates, updates, ensures, deletes, moves and merges tags, with
 * their translations and synonyms.
 *
 *   - type: tag
 *     mode: ensure                     create, update, ensure, delete, move, merge
 *     parent: "path:Topics"            the parent tag (none, or 0: at the top)
 *     remote_id: topic-sport
 *     language: eng-GB
 *     keyword: Sport
 *     keywords: { ger-DE: Sport, fre-FR: Sport }
 *     synonyms: [ Sports, Athletics ]  further keywords for the same tag, in the language of keyword:
 *   - type: tag
 *     mode: merge
 *     tag: "path:Topics/Athletics"     the tag that goes ...
 *     into: "remote:topic-sport"       ... its objects and children go to this one
 *
 * A tag is named by its id, remote:<remote id> or path:<keyword path> (expMigrationIdentifier, kind tag). Every change
 * is made the way the tags pages of eztags make it (tags/add, tags/edit, tags/translation, tags/addsynonym, tags/delete,
 * tags/merge), with eZTagsObject and eZTagsKeyword. Without the extension the step fails with a message that says so
 * (missing: skip lets it pass).
 *
 * A create undoes itself (the tag is removed again while no object uses it); the other modes do not.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepTag extends expMigrationStepStructureBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'tag';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures, deletes, moves or merges a tag of eztags, with its translations and synonyms',
            'modes' => array( 'create', 'update', 'ensure', 'delete', 'move', 'merge' ),
            'keys' => array(
                'tag' => array( 'type' => 'identifier', 'kind' => 'tag', 'modes' => array( 'update', 'delete', 'move', 'merge' ),
                                'description' => 'the tag: its id, remote:<remote id> or path:<keyword path>' ),
                'parent' => array( 'type' => 'scalar', 'modes' => array( 'create', 'ensure', 'move' ),
                                   'description' => 'the parent tag (id, remote:, path:); none or 0: at the top. move: the new parent' ),
                'remote_id' => array( 'type' => 'string', 'modes' => $writing,
                                      'description' => 'the remote id of the tag; ensure finds the tag by it (without it: by keyword: below parent:)' ),
                'language' => array( 'type' => 'string', 'modes' => $writing,
                                     'description' => 'the language of keyword: and synonyms: (default: the default language of content); a new tag\'s main language' ),
                'keyword' => array( 'type' => 'string', 'modes' => $writing, 'description' => 'the keyword in the language of language:' ),
                'keywords' => array( 'type' => 'map', 'modes' => $writing, 'description' => 'language => the keyword in it: the translations' ),
                'main_language' => array( 'type' => 'string', 'modes' => array( 'update', 'ensure' ),
                                          'description' => 'the language of the tag\'s main translation (it must have one in it)' ),
                'always_available' => array( 'type' => 'bool', 'modes' => $writing,
                                             'description' => 'whether the tag is shown in languages it has no translation in (a new tag: false)' ),
                'synonyms' => array( 'type' => 'list', 'modes' => $writing,
                                     'description' => 'keywords that are synonyms of the tag, in the language of language:; the missing ones are added' ),
                'into' => array( 'type' => 'identifier', 'kind' => 'tag', 'modes' => array( 'merge' ),
                                 'description' => 'the tag that keeps the objects, children and synonyms of tag:' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ),
                                    'description' => 'what happens when the tag, or the extension eztags, is not there: fail (the default) or skip' ),
            ),
            'values' => array(
                'tag_id' => 'the id of the tag',
                'remote_id' => 'its remote id',
                'keyword' => 'its keyword in its main language',
                'path' => 'its keyword path (Topics/Sport)',
                'created' => 'true when the step created the tag',
                'changed' => 'true when the step changed something',
            ),
            'example' => "- type: tag\n  mode: ensure\n  parent: \"path:Topics\"\n  remote_id: topic-sport\n  keyword: Sport\n"
                       . "  keywords: { ger-DE: Sport }\n  synonyms: [ Sports ]\n  references: { sport: tag_id }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        $errors = self::translationProblems( $step, 'keyword', 'keywords' );
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) && ( !isset( $step['keyword'] ) || trim( (string)$step['keyword'] ) === '' ) )
            $errors[] = "keyword is missing (mode $mode)";
        if ( in_array( $mode, array( 'update', 'delete', 'move', 'merge' ), true ) && !isset( $step['tag'] ) )
            $errors[] = "tag is missing (mode $mode)";
        if ( $mode === 'move' && !array_key_exists( 'parent', $step ) )
            $errors[] = 'parent is missing: the new parent tag (0: the top)';
        if ( $mode === 'merge' && !isset( $step['into'] ) )
            $errors[] = 'into is missing: the tag that keeps the objects (mode merge)';
        if ( $mode === 'update' && !array_intersect( array( 'keyword', 'keywords', 'main_language', 'always_available', 'synonyms', 'remote_id' ), array_keys( $step ) ) )
            $errors[] = 'nothing to update: keyword:, keywords:, main_language:, always_available:, synonyms: or remote_id: is missing';
        if ( isset( $step['parent'] ) && !self::isTop( $step['parent'] ) )
        {
            $problem = expMigrationIdentifier::problem( 'tag', $step['parent'] );
            if ( $problem !== null )
                $errors[] = "parent: $problem";
        }
        if ( isset( $step['synonyms'] ) && is_array( $step['synonyms'] ) )
            foreach ( $step['synonyms'] as $i => $synonym )
                if ( !is_scalar( $synonym ) || trim( (string)$synonym ) === '' )
                    $errors[] = "synonyms[$i]: a keyword";
        if ( isset( $step['main_language'] ) && !expMigrationReferences::contains( $step['main_language'] ) )
        {
            $problem = expMigrationIdentifier::problem( 'language', $step['main_language'] );
            if ( $problem !== null )
                $errors[] = "main_language: $problem";
        }
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'content' );
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && $step['mode'] === 'create';
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $named = self::namesText( $step, 'keyword', 'keywords' );
        $below = !isset( $step['parent'] ) || self::isTop( $step['parent'] ) ? 'at the top' : 'below ' . expMigrationStepSchema::show( $step['parent'] );
        $synonyms = isset( $step['synonyms'] ) && is_array( $step['synonyms'] ) && $step['synonyms'] ? ', synonyms ' . implode( ', ', array_map( 'strval', $step['synonyms'] ) ) : '';
        $tag = isset( $step['tag'] ) ? expMigrationStepSchema::show( $step['tag'] ) : '';
        $note = expMigrationIdentifier::tagsInstalled() ? '' : ' (eztags is not installed here)';
        switch ( $step['mode'] )
        {
            case 'create':
                return array( "create a tag $below$named$synonyms$note" );
            case 'ensure':
                return array( "ensure the tag$named $below" . ( isset( $step['remote_id'] ) ? ' (remote id ' . $step['remote_id'] . ')' : '' ) . ": create it when missing, change what differs$synonyms$note" );
            case 'update':
                return array( "update the tag $tag$named$synonyms$note" );
            case 'delete':
                return array( "delete the tag $tag with the tags below it and its synonyms$note" );
            case 'move':
                return array( "move the tag $tag $below$note" );
            case 'merge':
                return array( "merge the tag $tag into " . expMigrationStepSchema::show( $step['into'] ) . ": its objects, children and synonyms go there$note" );
        }
        return parent::plan( $step, $context );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $mode = $step['mode'];
        if ( !expMigrationIdentifier::tagsInstalled() )
        {
            if ( isset( $step['missing'] ) && $step['missing'] === 'skip' )
                return self::result( array( 'the extension eztags is not installed: skipped (missing: skip)' ), self::emptyValues() );
            throw new RuntimeException( 'the step type tag needs the extension eztags, which is not installed here' );
        }
        if ( $mode === 'create' || $mode === 'ensure' )
        {
            $parent = self::parentTag( $step );
            $existing = $this->findNew( $step, $parent );
            if ( $existing !== null && $mode === 'create' )
                throw new RuntimeException( 'the tag ' . self::pathOf( $existing ) . ' is there already (mode ensure changes it)' );
            if ( $existing !== null )
                return $this->update( $existing, $step );
            return $this->create( $step, $parent );
        }
        $id = expMigrationIdentifier::find( 'tag', $step['tag'] );
        $tag = $id !== null ? eZTagsObject::fetchWithMainTranslation( $id ) : null;
        if ( !$tag instanceof eZTagsObject )
            return self::missingResult( $step, 'the tag ' . expMigrationStepSchema::show( $step['tag'] ), self::emptyValues() );
        switch ( $mode )
        {
            case 'update':
                return $this->update( $tag, $step );
            case 'delete':
                return $this->delete( $tag );
            case 'move':
                return $this->move( $tag, self::parentTag( $step ) );
            case 'merge':
                return $this->merge( $tag, $step );
        }
        throw new RuntimeException( "the mode $mode is not one of create, update, ensure, delete, move, merge" );
    }

    /**
     * Removes the tag a create made, while no object uses it.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        if ( !expMigrationIdentifier::tagsInstalled() )
            return array( 'the extension eztags is not installed: nothing to undo' );
        $tag = $this->findNew( $step, self::parentTag( $step ) );
        if ( $tag === null )
            return array( 'the tag "' . $step['keyword'] . '" is not there: nothing to undo' );
        if ( eZPersistentObject::fetchObjectList( eZTagsAttributeLinkObject::definition(), array( 'id' ), array( 'keyword_id' => (int)$tag->attribute( 'id' ) ),
                                                  null, array( 'limit' => 1, 'offset' => 0 ), false ) )
            throw new RuntimeException( 'the tag ' . self::pathOf( $tag ) . ' is used by objects: it is not removed' );
        $result = $this->delete( $tag );
        return $result['lines'];
    }

    // ---- the modes ----------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @param eZTagsObject|null $parent
     * @return array
     */
    protected function create( array $step, $parent )
    {
        $texts = self::translations( $step, 'keyword', 'keywords' );
        $main = (string)key( $texts );
        $language = eZContentLanguage::fetchByLocale( $main );
        $always = !empty( $step['always_available'] );
        $remoteId = isset( $step['remote_id'] ) ? trim( (string)$step['remote_id'] ) : '';
        if ( $remoteId !== '' && eZTagsObject::fetchByRemoteID( $remoteId, true ) instanceof eZTagsObject )
            throw new RuntimeException( "another tag has the remote id $remoteId" );
        $parentId = $parent instanceof eZTagsObject ? (int)$parent->attribute( 'id' ) : 0;
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $tag = new eZTagsObject( array( 'parent_id' => $parentId, 'main_tag_id' => 0, 'keyword' => $texts[$main],
                                            'depth' => $parent instanceof eZTagsObject ? (int)$parent->attribute( 'depth' ) + 1 : 1,
                                            'path_string' => $parent instanceof eZTagsObject ? (string)$parent->attribute( 'path_string' ) : '/',
                                            'main_language_id' => (int)$language->attribute( 'id' ),
                                            'language_mask' => eZContentLanguage::maskByLocale( array_keys( $texts ), $always ),
                                            'remote_id' => $remoteId ), $main );
            $tag->store();
            foreach ( $texts as $locale => $keyword )
                self::storeTranslation( $tag, (string)$locale, $keyword, $always && $locale === $main );
            $tag->setAttribute( 'path_string', $tag->attribute( 'path_string' ) . $tag->attribute( 'id' ) . '/' );
            $tag->store();
            $tag->updateModified();
            $added = self::addSynonyms( $tag, $step, $main );
            if ( class_exists( 'ezpEvent', false ) )
                ezpEvent::getInstance()->filter( 'tag/add', array( 'tag' => $tag, 'parentTag' => $parent ?: false ) );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $tag = self::fresh( (int)$tag->attribute( 'id' ) );
        $lines = array( 'created the tag ' . self::pathOf( $tag ) . ' (' . $tag->attribute( 'id' ) . ') in ' . implode( ', ', array_keys( $texts ) ) );
        if ( $added )
            $lines[] = 'added the synonyms ' . implode( ', ', $added );
        return self::result( $lines, self::values( $tag ) + array( 'created' => true, 'changed' => true ) );
    }

    /**
     * Changes what differs: translations, the main language, always available, the remote id, missing synonyms.
     *
     * @param eZTagsObject $tag
     * @param array $step
     * @return array
     */
    protected function update( eZTagsObject $tag, array $step )
    {
        if ( (int)$tag->attribute( 'main_tag_id' ) !== 0 )
            throw new RuntimeException( 'the tag ' . self::pathOf( $tag ) . ' is a synonym: change its main tag' );
        $id = (int)$tag->attribute( 'id' );
        $texts = self::translations( $step, 'keyword', 'keywords' );
        $changed = array();
        $db = eZDB::instance();
        $db->begin();
        try
        {
            foreach ( $texts as $locale => $keyword )
            {
                $translation = eZTagsKeyword::fetch( $id, (string)$locale, true );
                if ( $translation instanceof eZTagsKeyword && (string)$translation->attribute( 'keyword' ) === $keyword
                     && (int)$translation->attribute( 'status' ) === eZTagsKeyword::STATUS_PUBLISHED )
                    continue;
                if ( self::clash( (int)$tag->attribute( 'parent_id' ), $keyword, $id ) )
                    throw new RuntimeException( "another tag below the same parent has the keyword \"$keyword\"" );
                self::storeTranslation( $tag, (string)$locale, $keyword, false, $translation instanceof eZTagsKeyword ? $translation : null );
                $changed[] = "$locale \"$keyword\"";
            }
            if ( $changed )
            {
                $tag = self::fresh( $id );
                $tag->updateLanguageMask();
            }
            if ( isset( $step['main_language'] ) )
            {
                $locale = expMigrationStepContentBase::localeOf( $step['main_language'] );
                $language = eZContentLanguage::fetchByLocale( $locale );
                if ( (int)$tag->attribute( 'main_language_id' ) !== (int)$language->attribute( 'id' ) )
                {
                    if ( !$tag->updateMainTranslation( $locale ) )
                        throw new RuntimeException( 'the tag ' . self::pathOf( $tag ) . " has no translation $locale to make its main one" );
                    $changed[] = "main language $locale";
                }
            }
            $tag = self::fresh( $id );
            $mainKeyword = (string)$tag->getKeyword( self::mainLocale( $tag ) );
            if ( (string)$tag->attribute( 'keyword' ) !== $mainKeyword )
            {
                $tag->setAttribute( 'keyword', $mainKeyword );
                $tag->store();
            }
            if ( isset( $step['always_available'] ) && $tag->isAlwaysAvailable() !== (bool)$step['always_available'] )
            {
                $tag->setAlwaysAvailable( (bool)$step['always_available'] );
                $changed[] = 'always available ' . ( $step['always_available'] ? 'on' : 'off' );
            }
            if ( isset( $step['remote_id'] ) && trim( (string)$step['remote_id'] ) !== '' && (string)$tag->attribute( 'remote_id' ) !== trim( (string)$step['remote_id'] ) )
            {
                $remoteId = trim( (string)$step['remote_id'] );
                if ( eZTagsObject::fetchByRemoteID( $remoteId, true ) instanceof eZTagsObject )
                    throw new RuntimeException( "another tag has the remote id $remoteId" );
                $tag->setAttribute( 'remote_id', $remoteId );
                $tag->store();
                $changed[] = "remote id $remoteId";
            }
            $added = self::addSynonyms( $tag, $step, isset( $step['language'] ) ? expMigrationStepContentBase::localeOf( $step['language'] ) : self::mainLocale( $tag ) );
            if ( $added )
                $changed[] = 'synonyms ' . implode( ', ', $added );
            if ( $changed )
            {
                $tag->updateModified();
                self::reindex( $tag );
            }
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $tag = self::fresh( $id );
        $label = 'the tag ' . self::pathOf( $tag );
        if ( !$changed )
            return self::result( array( "$label is as the step has it" ), self::values( $tag ) + array( 'created' => false, 'changed' => false ) );
        return self::result( array( "updated $label: " . implode( ', ', $changed ) ), self::values( $tag ) + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * @param eZTagsObject $tag
     * @return array
     */
    protected function delete( eZTagsObject $tag )
    {
        $values = self::values( $tag );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $parent = $tag->getParent( true );
            if ( $parent instanceof eZTagsObject )
                $parent->updateModified();
            if ( class_exists( 'ezpEvent', false ) )
                ezpEvent::getInstance()->filter( 'tag/delete', $tag );
            $tag->recursivelyDeleteTag();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        if ( self::fresh( (int)$values['tag_id'] ) !== null )
            throw new RuntimeException( 'the tag ' . $values['path'] . ' was not deleted' );
        return self::result( array( 'deleted the tag ' . $values['path'] . ' (' . $values['tag_id'] . ') with the tags below it and its synonyms' ),
                             $values + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * Moves a tag (with its synonyms and the tags below it) below another parent, as tags/edit does.
     *
     * @param eZTagsObject $tag
     * @param eZTagsObject|null $parent
     * @return array
     */
    protected function move( eZTagsObject $tag, $parent )
    {
        $id = (int)$tag->attribute( 'id' );
        $parentId = $parent instanceof eZTagsObject ? (int)$parent->attribute( 'id' ) : 0;
        $label = 'the tag ' . self::pathOf( $tag );
        if ( (int)$tag->attribute( 'main_tag_id' ) !== 0 )
            throw new RuntimeException( "$label is a synonym: it moves with its main tag" );
        if ( (int)$tag->attribute( 'parent_id' ) === $parentId )
            return self::result( array( "$label is below " . ( $parent ? self::pathOf( $parent ) : 'the top' ) . ' already' ),
                                 self::values( $tag ) + array( 'created' => false, 'changed' => false ) );
        if ( $parent instanceof eZTagsObject && ( $parentId === $id || strpos( (string)$parent->attribute( 'path_string' ), "/$id/" ) !== false ) )
            throw new RuntimeException( "$label cannot be moved below itself" );
        if ( $parent instanceof eZTagsObject && (int)$parent->attribute( 'main_tag_id' ) !== 0 )
            throw new RuntimeException( 'the parent ' . self::pathOf( $parent ) . ' is a synonym' );
        foreach ( eZTagsKeyword::fetchByTagID( $id ) as $translation )
            if ( self::clash( $parentId, (string)$translation->attribute( 'keyword' ), $id ) )
                throw new RuntimeException( 'below the new parent a tag has the keyword "' . $translation->attribute( 'keyword' ) . '" already' );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $oldParent = $tag->getParent( true );
            if ( $oldParent instanceof eZTagsObject )
                $oldParent->updateModified();
            foreach ( $tag->getSynonyms( true ) as $synonym )
            {
                $synonym->setAttribute( 'parent_id', $parentId );
                $synonym->store();
            }
            $tag->setAttribute( 'parent_id', $parentId );
            $tag->store();
            if ( class_exists( 'ezpEvent', false ) )
                ezpEvent::getInstance()->filter( 'tag/edit', array( 'tag' => $tag, 'oldParentTag' => $oldParent ?: false, 'newParentTag' => $parent ?: false, 'move' => true ) );
            $tag->updatePathString();
            $tag->updateDepth();
            $tag->updateModified();
            self::reindex( $tag );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        $tag = self::fresh( $id );
        if ( $tag === null || (int)$tag->attribute( 'parent_id' ) !== $parentId )
            throw new RuntimeException( "$label was not moved" );
        return self::result( array( "moved $label to " . self::pathOf( $tag ) ), self::values( $tag ) + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * Merges a tag into another, as tags/merge does: its children move there, its objects and those of its synonyms
     * are linked to the other tag, and it is removed.
     *
     * @param eZTagsObject $tag
     * @param array $step
     * @return array
     */
    protected function merge( eZTagsObject $tag, array $step )
    {
        $into = eZTagsObject::fetchWithMainTranslation( expMigrationIdentifier::resolve( 'tag', $step['into'] ) );
        if ( !$into instanceof eZTagsObject )
            throw new RuntimeException( 'no tag ' . expMigrationStepSchema::show( $step['into'] ) . ' on this installation' );
        $id = (int)$tag->attribute( 'id' );
        $label = 'the tag ' . self::pathOf( $tag );
        if ( $id === (int)$into->attribute( 'id' ) )
            throw new RuntimeException( "$label cannot be merged into itself" );
        if ( (int)$tag->attribute( 'main_tag_id' ) !== 0 || (int)$into->attribute( 'main_tag_id' ) !== 0 )
            throw new RuntimeException( 'a synonym is not merged: name main tags' );
        if ( strpos( (string)$into->attribute( 'path_string' ), "/$id/" ) !== false )
            throw new RuntimeException( "$label cannot be merged into a tag below it" );
        $intoLabel = self::pathOf( $into );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $oldParent = (int)$tag->attribute( 'parent_id' ) !== (int)$into->attribute( 'parent_id' ) ? $tag->getParent( true ) : false;
            if ( $oldParent instanceof eZTagsObject )
                $oldParent->updateModified();
            if ( class_exists( 'ezpEvent', false ) )
                ezpEvent::getInstance()->filter( 'tag/merge', array( 'tag' => $tag, 'newParentTag' => $into, 'oldParentTag' => $oldParent ) );
            $tag->moveChildrenBelowAnotherTag( $into );
            foreach ( $tag->getSynonyms( true ) as $synonym )
            {
                self::reindex( $synonym );
                $synonym->transferObjectsToAnotherTag( $into );
                $synonym->remove();
            }
            self::reindex( $tag );
            $tag->transferObjectsToAnotherTag( $into );
            $tag->remove();
            $into->updateModified();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        if ( self::fresh( $id ) !== null )
            throw new RuntimeException( "$label was not merged" );
        $into = self::fresh( (int)$into->attribute( 'id' ) );
        return self::result( array( "merged $label into $intoLabel" ), self::values( $into ) + array( 'created' => false, 'changed' => true ) );
    }

    // ---- helpers ------------------------------------------------------------------------------------------------

    /**
     * Whether a parent value means the top. Pure.
     *
     * @param mixed $value
     * @return bool
     */
    protected static function isTop( $value )
    {
        return $value === null || $value === 0 || $value === '0' || $value === '/' || $value === '';
    }

    /**
     * The parent tag of parent:, or null for the top.
     *
     * @param array $step
     * @return eZTagsObject|null
     * @throws RuntimeException
     */
    protected static function parentTag( array $step )
    {
        if ( !isset( $step['parent'] ) || self::isTop( $step['parent'] ) )
            return null;
        $parent = eZTagsObject::fetchWithMainTranslation( expMigrationIdentifier::resolve( 'tag', $step['parent'] ) );
        if ( !$parent instanceof eZTagsObject )
            throw new RuntimeException( 'no tag ' . expMigrationStepSchema::show( $step['parent'] ) );
        if ( (int)$parent->attribute( 'main_tag_id' ) !== 0 )
            throw new RuntimeException( 'the parent ' . self::pathOf( $parent ) . ' is a synonym' );
        return $parent;
    }

    /**
     * The tag a create or ensure means when it is there: by remote_id, else by keyword: below the parent.
     *
     * @param array $step
     * @param eZTagsObject|null $parent
     * @return eZTagsObject|null
     */
    protected function findNew( array $step, $parent )
    {
        if ( isset( $step['remote_id'] ) && trim( (string)$step['remote_id'] ) !== '' )
        {
            $tag = eZTagsObject::fetchByRemoteID( trim( (string)$step['remote_id'] ), true );
            return $tag instanceof eZTagsObject ? $tag : null;
        }
        $id = self::childId( $parent instanceof eZTagsObject ? (int)$parent->attribute( 'id' ) : 0, (string)$step['keyword'], 0, true );
        return $id !== null ? self::fresh( $id ) : null;
    }

    /**
     * The id of a tag or synonym below a parent that has a keyword (in any language, case not minded), or null. Simple
     * queries of both tables, as on every engine.
     *
     * @param int $parentId
     * @param string $keyword
     * @param int $not a tag not counted
     * @param bool $mainOnly only main tags (no synonyms)
     * @return int|null
     */
    protected static function childId( $parentId, $keyword, $not = 0, $mainOnly = false )
    {
        $conditions = array( 'parent_id' => (int)$parentId );
        if ( $mainOnly )
            $conditions['main_tag_id'] = 0;
        $rows = eZPersistentObject::fetchObjectList( eZTagsObject::definition(), array( 'id' ), $conditions, null, null, false );
        $wanted = mb_strtolower( trim( $keyword ), 'UTF-8' );
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            if ( (int)$row['id'] === (int)$not )
                continue;
            foreach ( eZTagsKeyword::fetchByTagID( (int)$row['id'] ) as $translation )
                if ( mb_strtolower( trim( (string)$translation->attribute( 'keyword' ) ), 'UTF-8' ) === $wanted )
                    return (int)$row['id'];
        }
        return null;
    }

    /**
     * Whether a keyword is taken below a parent by another tag or synonym (tags/edit: "already exists in selected
     * location").
     *
     * @param int $parentId
     * @param string $keyword
     * @param int $not
     * @return bool
     */
    protected static function clash( $parentId, $keyword, $not )
    {
        $other = self::childId( $parentId, $keyword, $not );
        if ( $other === null )
            return false;
        // a synonym of the tag itself is no clash
        $row = eZPersistentObject::fetchObject( eZTagsObject::definition(), array( 'main_tag_id' ), array( 'id' => $other ), false );
        return !( is_array( $row ) && (int)$row['main_tag_id'] === (int)$not );
    }

    /**
     * Writes a translation (a new one, or the one given) as published.
     *
     * @param eZTagsObject $tag
     * @param string $locale
     * @param string $keyword
     * @param bool $alwaysAvailable the always-available bit on this translation (the main one of an always available tag)
     * @param eZTagsKeyword|null $translation
     */
    protected static function storeTranslation( eZTagsObject $tag, $locale, $keyword, $alwaysAvailable, $translation = null )
    {
        $language = eZContentLanguage::fetchByLocale( $locale );
        if ( $translation === null )
            $translation = new eZTagsKeyword( array( 'keyword_id' => (int)$tag->attribute( 'id' ),
                                                     'language_id' => (int)$language->attribute( 'id' ) + ( $alwaysAvailable ? 1 : 0 ),
                                                     'keyword' => $keyword, 'locale' => $locale, 'status' => eZTagsKeyword::STATUS_PUBLISHED ) );
        $translation->setAttribute( 'keyword', $keyword );
        $translation->setAttribute( 'status', eZTagsKeyword::STATUS_PUBLISHED );
        $translation->store();
    }

    /**
     * Adds the synonyms of synonyms: that the tag does not have yet, as tags/addsynonym does.
     *
     * @param eZTagsObject $tag
     * @param array $step
     * @param string $locale the language of the synonyms
     * @return string[] the keywords added
     */
    protected static function addSynonyms( eZTagsObject $tag, array $step, $locale )
    {
        if ( !isset( $step['synonyms'] ) || !is_array( $step['synonyms'] ) )
            return array();
        $id = (int)$tag->attribute( 'id' );
        $parentId = (int)$tag->attribute( 'parent_id' );
        $language = eZContentLanguage::fetchByLocale( $locale );
        $always = $tag->isAlwaysAvailable();
        $added = array();
        foreach ( $step['synonyms'] as $keyword )
        {
            $keyword = trim( (string)$keyword );
            $other = self::childId( $parentId, $keyword );
            if ( $other !== null )
            {
                $row = eZPersistentObject::fetchObject( eZTagsObject::definition(), array( 'main_tag_id' ), array( 'id' => $other ), false );
                if ( $other === $id || ( is_array( $row ) && (int)$row['main_tag_id'] === $id ) )
                    continue;
                throw new RuntimeException( "the synonym \"$keyword\" is another tag below the same parent" );
            }
            $synonym = new eZTagsObject( array( 'parent_id' => $parentId, 'main_tag_id' => $id, 'keyword' => $keyword,
                                                'depth' => (int)$tag->attribute( 'depth' ),
                                                'path_string' => preg_replace( '#[0-9]+/$#', '', (string)$tag->attribute( 'path_string' ) ),
                                                'main_language_id' => (int)$language->attribute( 'id' ),
                                                'language_mask' => eZContentLanguage::maskByLocale( array( $locale ), $always ) ), $locale );
            $synonym->store();
            self::storeTranslation( $synonym, $locale, $keyword, $always );
            $synonym->setAttribute( 'path_string', $synonym->attribute( 'path_string' ) . $synonym->attribute( 'id' ) . '/' );
            $synonym->store();
            $synonym->updateModified();
            $added[] = $keyword;
        }
        return $added;
    }

    /**
     * Tells the search engine about the objects of a tag (eZTagsObject::registerSearchObjects() reads them with a join
     * and falls back to a session message in a console run): the objects of its links, by simple queries.
     *
     * @param eZTagsObject $tag
     */
    protected static function reindex( eZTagsObject $tag )
    {
        $rows = eZPersistentObject::fetchObjectList( eZTagsAttributeLinkObject::definition(), array( 'object_id' ),
                                                     array( 'keyword_id' => (int)$tag->attribute( 'id' ) ), null, null, false );
        $ids = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
            $ids[(int)$row['object_id']] = true;
        foreach ( array_keys( $ids ) as $objectId )
            eZContentOperationCollection::registerSearchObject( $objectId );
    }

    /**
     * A tag with its main translation, fresh from the database, or null.
     *
     * @param int $id
     * @return eZTagsObject|null
     */
    protected static function fresh( $id )
    {
        $tag = eZTagsObject::fetchWithMainTranslation( (int)$id );
        return $tag instanceof eZTagsObject ? $tag : null;
    }

    /**
     * @param eZTagsObject $tag
     * @return string the locale of the tag's main translation
     */
    protected static function mainLocale( eZTagsObject $tag )
    {
        $language = eZContentLanguage::fetch( (int)$tag->attribute( 'main_language_id' ) );
        return $language instanceof eZContentLanguage ? (string)$language->attribute( 'locale' ) : '';
    }

    /**
     * The keyword path of a tag (its main keywords from the top): what path:<keyword path> names.
     *
     * @param eZTagsObject $tag
     * @return string
     */
    protected static function pathOf( eZTagsObject $tag )
    {
        $parts = array();
        $current = $tag;
        for ( $depth = 0; $current instanceof eZTagsObject && $depth < 100; $depth++ )
        {
            array_unshift( $parts, (string)$current->getKeyword( self::mainLocale( $current ) ) );
            $parentId = (int)$current->attribute( 'parent_id' );
            $current = $parentId ? self::fresh( $parentId ) : null;
        }
        return implode( '/', $parts );
    }

    /**
     * @param eZTagsObject $tag
     * @return array
     */
    protected static function values( eZTagsObject $tag )
    {
        return array( 'tag_id' => (int)$tag->attribute( 'id' ), 'remote_id' => (string)$tag->attribute( 'remote_id' ),
                      'keyword' => (string)$tag->getKeyword( self::mainLocale( $tag ) ), 'path' => self::pathOf( $tag ) );
    }

    /** @return array */
    protected static function emptyValues()
    {
        return array( 'tag_id' => null, 'remote_id' => null, 'keyword' => null, 'path' => null, 'created' => false, 'changed' => false );
    }
}
