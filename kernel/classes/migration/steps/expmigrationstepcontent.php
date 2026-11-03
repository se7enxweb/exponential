<?php
/**
 * File containing the expMigrationStepContent class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "content": creates, updates, ensures and deletes content objects.
 *
 *   - type: content
 *     mode: ensure                     create, update, ensure (create when missing, update when different), delete
 *     class: article
 *     parent: "${idmap.node.news}"
 *     remote_id: news-welcome
 *     language: eng-GB
 *     fields:
 *         title: Welcome
 *         intro: "<p>The first article.</p>"
 *     translations:                    one block per further language: a version of its own in that language
 *         ger-DE: { title: Willkommen }
 *     references: { welcome: node_id }
 *
 * Each value of "fields:" goes through the fromString() of the attribute's datatype, as in eZContentFunctions and the
 * package import. A create or an update makes a new version and publishes it through the publish operation (one per
 * language block); "in_place: true" writes into the published version instead, without a new version (a correction
 * that the version history does not show). Workflows run only with "workflows: true", notifications only with
 * "notify: true".
 *
 * A step of the mode create with a remote_id can undo itself (the object is removed again); every other step needs a
 * "down:" of its migration.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepContent extends expMigrationStepContentBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'content';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a content object, with its fields in one or more languages',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'class' => array( 'type' => 'identifier', 'kind' => 'class', 'modes' => array( 'create', 'ensure' ),
                                  'description' => 'the class of a new object: its identifier, id or remote:<remote id> (create, ensure)' ),
                'parent' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => array( 'create', 'ensure' ),
                                   'description' => 'the parent node of a new object (create, ensure)' ),
                'object' => array( 'type' => 'identifier', 'kind' => 'object', 'modes' => array( 'update', 'delete' ),
                                   'description' => 'the object to update or delete (or node:)' ),
                'node' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => array( 'update', 'delete' ),
                                 'description' => 'a node of the object to update or delete (or object:)' ),
                'remote_id' => array( 'type' => 'string', 'modes' => array( 'create', 'ensure' ),
                                      'description' => 'the remote id of a new object; ensure finds the object by it' ),
                'language' => array( 'type' => 'string', 'modes' => $writing,
                                     'description' => 'the language of fields: (default: the default language of content, ContentObjectLocale)' ),
                'fields' => array( 'type' => 'map', 'modes' => $writing,
                                   'description' => 'attribute identifier => value, in the language of language:; each value goes through the datatype\'s fromString()' ),
                'translations' => array( 'type' => 'map', 'modes' => $writing,
                                         'description' => 'language => a map of fields: one block per further language, each published as a version of its own' ),
                'section' => array( 'type' => 'identifier', 'kind' => 'section', 'modes' => array( 'create', 'ensure' ),
                                    'description' => 'the section of a new object: its identifier or id (default: the parent\'s)' ),
                'always_available' => array( 'type' => 'bool', 'modes' => array( 'create', 'ensure' ),
                                             'description' => 'whether a new object is shown in a language it does not have (default: the class\'s setting)' ),
                'in_place' => array( 'type' => 'bool', 'default' => false, 'modes' => array( 'update', 'ensure' ),
                                     'description' => 'true: the published version is changed in place, without a new version' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ), 'modes' => array( 'update', 'delete' ),
                                    'description' => 'what happens when the object is not there: fail (the default) or skip' ),
                'trash' => array( 'type' => 'bool', 'default' => false, 'modes' => array( 'delete' ),
                                  'description' => 'true: the object goes to the trash; false (the default): it is removed for good' ),
            ) + self::operationKeys(),
            'values' => array(
                'object_id' => 'the id of the object',
                'node_id' => 'the id of its main node',
                'remote_id' => 'its remote id',
                'version' => 'its current version',
                'name' => 'its name',
                'path' => 'the url path of its main node (path_identification_string)',
                'created' => 'true when the step created the object',
                'changed' => 'true when the step changed it (created, updated or deleted)',
            ),
            'example' => "- type: content\n  mode: ensure\n  class: folder\n  parent: 2\n  remote_id: news-folder\n  fields:\n    name: News\n"
                       . "  translations:\n    ger-DE: { name: Nachrichten }\n  references: { news: node_id }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) )
        {
            foreach ( array( 'class', 'parent' ) as $key )
                if ( !isset( $step[$key] ) )
                    $errors[] = "$key is missing (mode $mode)";
            if ( $mode === 'ensure' && ( !isset( $step['remote_id'] ) || (string)$step['remote_id'] === '' ) )
                $errors[] = 'remote_id is missing: ensure finds the object by it';
        }
        if ( in_array( $mode, array( 'update', 'delete' ), true ) && isset( $step['object'] ) === isset( $step['node'] ) )
            $errors[] = "name the object by object: or by node: (one of them, mode $mode)";
        if ( $mode === 'update' && !isset( $step['fields'] ) && !isset( $step['translations'] ) )
            $errors[] = 'nothing to update: fields: or translations: is missing';
        if ( isset( $step['fields'] ) )
            $errors = array_merge( $errors, self::fieldProblems( $step['fields'], 'fields' ) );
        if ( isset( $step['language'] ) && !expMigrationReferences::contains( $step['language'] ) )
        {
            $problem = expMigrationIdentifier::problem( 'language', $step['language'] );
            if ( $problem !== null )
                $errors[] = "language: $problem";
        }
        if ( isset( $step['translations'] ) && is_array( $step['translations'] ) )
        {
            foreach ( $step['translations'] as $language => $fields )
            {
                $problem = expMigrationIdentifier::problem( 'language', (string)$language );
                if ( $problem !== null || is_int( $language ) )
                    $errors[] = "translations: $language is not a language code (ger-DE)";
                else if ( isset( $step['language'] ) && $language === $step['language'] )
                    $errors[] = "translations.$language: the language of fields: (write the fields there)";
                else
                    $errors = array_merge( $errors, self::fieldProblems( $fields, "translations.$language" ) );
            }
        }
        return $errors;
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && $step['mode'] === 'create' && isset( $step['remote_id'] ) && (string)$step['remote_id'] !== '';
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $mode = $step['mode'];
        $languages = $this->blocks( $step, false );
        $what = array();
        foreach ( $languages as $language => $fields )
            $what[] = ( $language === '' ? 'the default language' : $language ) . ( $fields ? ': ' . implode( ', ', array_keys( $fields ) ) : '' );
        $in = $what ? ' (' . implode( '; ', $what ) . ')' : '';
        $flags = ( !empty( $step['in_place'] ) ? ', in place' : '' ) . ( !empty( $step['workflows'] ) ? ', with workflows' : '' );
        switch ( $mode )
        {
            case 'create':
                return array( 'create a ' . self::named( $step['class'] ) . ' below node ' . self::named( $step['parent'] )
                              . ( isset( $step['remote_id'] ) ? ' with the remote id ' . $step['remote_id'] : '' ) . $in . $flags );
            case 'ensure':
                return array( 'ensure the ' . self::named( $step['class'] ) . ' ' . $step['remote_id'] . ' below node ' . self::named( $step['parent'] )
                              . ': create it when missing, update what differs' . $in . $flags );
            case 'update':
                return array( 'update ' . $this->target( $step ) . $in . $flags );
            case 'delete':
                return array( 'delete ' . $this->target( $step ) . ( !empty( $step['trash'] ) ? ' to the trash' : ' for good' ) );
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
        switch ( $step['mode'] )
        {
            case 'create':
                return $this->create( $step );
            case 'ensure':
                $existing = eZContentObject::fetchByRemoteID( (string)$step['remote_id'] );
                return $existing instanceof eZContentObject ? $this->update( $existing, $step, true ) : $this->create( $step );
            case 'update':
                $object = self::findObject( $step );
                if ( $object === null )
                    return $this->missing( $step );
                return $this->update( $object, $step, false );
            case 'delete':
                $object = self::findObject( $step );
                if ( $object === null )
                    return $this->missing( $step );
                return $this->delete( $object, $step );
        }
        throw new RuntimeException( 'the mode ' . $step['mode'] . ' is not one of create, update, ensure, delete' );
    }

    /**
     * Removes the object a create made, found by its remote id.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create with a remote_id can be undone by itself; give the migration a down:' );
        $object = eZContentObject::fetchByRemoteID( (string)$step['remote_id'] );
        if ( !$object instanceof eZContentObject )
            return array( 'the object ' . $step['remote_id'] . ' is not there: nothing to undo' );
        $this->delete( $object, array( 'trash' => false ) + $step );
        return array( 'removed the object ' . $step['remote_id'] . ' (' . $object->attribute( 'id' ) . ')' );
    }

    // ---- the modes ---------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @return array
     */
    protected function create( array $step )
    {
        $parentId = expMigrationIdentifier::resolve( 'node', $step['parent'] );
        $class = eZContentClass::fetch( expMigrationIdentifier::resolve( 'class', $step['class'] ) );
        if ( !$class instanceof eZContentClass )
            throw new RuntimeException( 'no class ' . self::named( $step['class'] ) );
        if ( isset( $step['remote_id'] ) && eZContentObject::fetchByRemoteID( (string)$step['remote_id'], false ) )
            throw new RuntimeException( 'an object with the remote id ' . $step['remote_id'] . ' is there already (mode ensure updates it)' );
        $blocks = $this->blocks( $step, true );
        $main = (string)key( $blocks );
        $sectionId = isset( $step['section'] ) ? expMigrationIdentifier::resolve( 'section', $step['section'] ) : 0;

        $db = eZDB::instance();
        $db->begin();
        try
        {
            $object = $class->instantiate( eZUser::currentUserID(), $sectionId, false, $main );
            if ( isset( $step['remote_id'] ) && (string)$step['remote_id'] !== '' )
                $object->setAttribute( 'remote_id', (string)$step['remote_id'] );
            $object->store();
            $assignment = eZNodeAssignment::create( array( 'contentobject_id' => $object->attribute( 'id' ),
                                                           'contentobject_version' => $object->attribute( 'current_version' ),
                                                           'parent_node' => $parentId,
                                                           'is_main' => 1,
                                                           'sort_field' => $class->attribute( 'sort_field' ),
                                                           'sort_order' => $class->attribute( 'sort_order' ) ) );
            $assignment->store();
            $version = $object->version( 1 );
            $version->setAttribute( 'modified', time() );
            $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
            $version->store();
            self::writeFields( $version->contentObjectAttributes( $main ), $blocks[$main], "fields ($main)" );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();

        $object = self::publish( $object->attribute( 'id' ), 1, $step );
        if ( isset( $step['always_available'] ) )
        {
            $db->begin();
            try
            {
                $object->setAlwaysAvailableLanguageID( $step['always_available'] ? (int)$object->attribute( 'initial_language_id' ) : false );
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
            eZContentObject::clearCache( array( (int)$object->attribute( 'id' ) ) );
            $object = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
        }
        $lines = array( 'created the ' . $class->attribute( 'identifier' ) . ' "' . $object->attribute( 'name' ) . '" (object ' . $object->attribute( 'id' )
                        . ', node ' . (int)$object->attribute( 'main_node_id' ) . ") in $main" );
        unset( $blocks[$main] );
        foreach ( $blocks as $language => $fields )
        {
            $object = $this->newVersion( $object, (string)$language, $fields, $step );
            $lines[] = "added the translation $language (version " . $object->attribute( 'current_version' ) . ')';
        }
        return self::result( $lines, self::objectValues( $object ) + array( 'created' => true, 'changed' => true ) );
    }

    /**
     * Updates an object: a new version per language block, or in place; with $onlyChanges only the fields whose text
     * differs (ensure).
     *
     * @param eZContentObject $object
     * @param array $step
     * @param bool $onlyChanges
     * @return array
     */
    protected function update( eZContentObject $object, array $step, $onlyChanges )
    {
        $blocks = $this->blocks( $step, true );
        $label = 'the object ' . $object->attribute( 'id' ) . ' "' . $object->attribute( 'name' ) . '"';
        $lines = array();
        $changed = false;
        foreach ( $blocks as $language => $fields )
        {
            $language = (string)$language;
            $has = in_array( $language, self::locales( $object ), true );
            if ( $onlyChanges && $has )
                $fields = $this->differences( $object, $language, $fields );
            if ( $has && !$fields )
            {
                $lines[] = "$label in $language is as the step has it";
                continue;
            }
            if ( !empty( $step['in_place'] ) )
            {
                if ( !$has )
                    throw new RuntimeException( "$label has no translation $language to change in place (in_place: false adds it)" );
                $this->inPlace( $object, $language, $fields );
                $lines[] = "changed $label in place in $language: " . implode( ', ', array_keys( $fields ) );
            }
            else
            {
                $object = $this->newVersion( $object, $language, $fields, $step );
                $lines[] = ( $has ? 'updated' : 'added the translation ' . $language . ' to' ) . " $label (version " . $object->attribute( 'current_version' ) . ')'
                         . ( $has ? " in $language: " . implode( ', ', array_keys( $fields ) ) : '' );
            }
            $changed = true;
        }
        eZContentObject::clearCache( array( (int)$object->attribute( 'id' ) ) );
        $object = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
        return self::result( $lines, self::objectValues( $object ) + array( 'created' => false, 'changed' => $changed ) );
    }

    /**
     * A new version of an object in one language, filled and published.
     *
     * @param eZContentObject $object
     * @param string $language
     * @param array $fields
     * @param array $step
     * @return eZContentObject the object after the publish
     */
    protected function newVersion( eZContentObject $object, $language, array $fields, array $step )
    {
        $has = in_array( $language, self::locales( $object ), true );
        $from = $has ? false : $object->initialLanguageCode();
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $version = $object->createNewVersionIn( $language, $from );
            if ( !$version instanceof eZContentObjectVersion )
            {
                $db->rollback();
                throw new RuntimeException( 'no new version of the object ' . $object->attribute( 'id' ) . " in $language could be made" );
            }
            $version->setAttribute( 'modified', time() );
            $version->store();
            self::writeFields( $version->contentObjectAttributes( $language ), $fields, "fields ($language)" );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        return self::publish( $object->attribute( 'id' ), $version->attribute( 'version' ), $step );
    }

    /**
     * Writes fields into the published version of an object, without a new version, and renames it.
     *
     * @param eZContentObject $object
     * @param string $language
     * @param array $fields
     */
    protected function inPlace( eZContentObject $object, $language, array $fields )
    {
        $versionNumber = (int)$object->attribute( 'current_version' );
        $version = $object->version( $versionNumber );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            self::writeFields( $version->contentObjectAttributes( $language ), $fields, "fields ($language)" );
            $version->resetDataMap();
            $object->resetDataMap();
            $class = $object->contentClass();
            $object->setName( $class->contentObjectName( $object, $versionNumber, $language ), $versionNumber, $language );
            $object->store();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        eZContentCacheManager::clearContentCacheIfNeeded( (int)$object->attribute( 'id' ) );
        eZContentOperationCollection::registerSearchObject( (int)$object->attribute( 'id' ) );
    }

    /**
     * The fields whose text differs from the object's in a language.
     *
     * @param eZContentObject $object
     * @param string $language
     * @param array $fields
     * @return array
     */
    protected function differences( eZContentObject $object, $language, array $fields )
    {
        $dataMap = $object->fetchDataMap( false, $language );
        $changed = array();
        foreach ( $fields as $identifier => $value )
        {
            if ( $value === null )
                continue;
            if ( !isset( $dataMap[$identifier] ) )
            {
                $changed[$identifier] = $value;
                continue;
            }
            if ( (string)$dataMap[$identifier]->toString() !== self::fieldText( $value ) )
                $changed[$identifier] = $value;
        }
        return $changed;
    }

    /**
     * @param eZContentObject $object
     * @param array $step
     * @return array
     */
    protected function delete( eZContentObject $object, array $step )
    {
        $values = self::objectValues( $object );
        $runAs = eZUser::currentUser();
        $runAsId = (int)eZUser::currentUserID();
        $nodeIds = array();
        foreach ( (array)eZContentObjectTreeNode::fetchByContentObjectID( (int)$object->attribute( 'id' ) ) as $node )
            $nodeIds[] = (int)$node->attribute( 'node_id' );
        if ( $nodeIds )
        {
            self::operation( 'delete', array( 'node_id_list' => $nodeIds, 'move_to_trash' => !empty( $step['trash'] ) ), $step );
        }
        else
        {
            $db = eZDB::instance();
            $db->begin();
            try
            {
                $object->purge();
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
        }
        // removing an account ends the sessions of that account (eZUser::removeSessionData()), which in a console run
        // forgets who is signed in: the migration goes on as the user it runs as
        if ( (int)eZUser::currentUserID() !== $runAsId && $runAsId !== (int)$values['object_id'] && $runAs instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $runAs, $runAsId, eZUser::NO_SESSION_REGENERATE );
        eZContentObject::clearCache( array( (int)$values['object_id'] ) );
        // the delete operation leaves a node it may not remove (content/remove of the user running the migration) without a word
        $row = eZContentObject::fetch( (int)$values['object_id'], false );
        if ( is_array( $row ) && (int)$row['status'] === eZContentObject::STATUS_PUBLISHED )
            throw new RuntimeException( 'the object ' . $values['object_id'] . ' "' . $values['name'] . '" was not deleted (may the user the migration runs as remove it?)' );
        return self::result( array( 'deleted the object ' . $values['object_id'] . ' "' . $values['name'] . '"' . ( !empty( $step['trash'] ) ? ' to the trash' : '' ) ),
                             $values + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * @param array $step
     * @return array
     * @throws RuntimeException unless missing: skip
     */
    protected function missing( array $step )
    {
        if ( !isset( $step['missing'] ) || $step['missing'] !== 'skip' )
            throw new RuntimeException( $this->target( $step ) . ' is not on this installation (missing: skip lets the step pass)' );
        return self::result( array( $this->target( $step ) . ' is not there: skipped (missing: skip)' ),
                             array( 'object_id' => null, 'node_id' => null, 'remote_id' => null, 'version' => null, 'name' => null, 'path' => null,
                                    'created' => false, 'changed' => false ) );
    }

    /**
     * The language blocks of a step: language => fields, the language of fields: first.
     *
     * @param array $step
     * @param bool $resolve true: the languages are checked and the default filled in (reads the database)
     * @return array
     */
    protected function blocks( array $step, $resolve )
    {
        $main = isset( $step['language'] ) ? (string)$step['language'] : '';
        if ( $resolve )
            $main = self::locale( $main );
        $blocks = array( $main => isset( $step['fields'] ) && is_array( $step['fields'] ) ? $step['fields'] : array() );
        if ( isset( $step['translations'] ) && is_array( $step['translations'] ) )
        {
            foreach ( $step['translations'] as $language => $fields )
            {
                $language = $resolve ? self::locale( (string)$language ) : (string)$language;
                $blocks[$language] = is_array( $fields ) ? $fields : array();
            }
        }
        if ( !$resolve && $main === '' && !$blocks[''] )
            unset( $blocks[''] );
        return $blocks;
    }

    /**
     * @param array $step
     * @return string
     */
    protected function target( array $step )
    {
        return isset( $step['object'] ) ? 'the object ' . self::named( $step['object'] ) : 'the object of node ' . self::named( isset( $step['node'] ) ? $step['node'] : null );
    }
}
