<?php
/**
 * The checks of the structure step types (content_class, content_class_group, user, user_group, role, section,
 * language, object_state_group) that run on every engine the same way: SQLite in process
 * (expMigrationStructureStepsSqliteTest), MySQL/MariaDB and PostgreSQL in the database job
 * (expMigrationStructureStepsDatabaseTest), MongoDB in the live MongoDB suite (expMigrationStructureStepsMongoDBTest).
 * Each gives a connection with the kernel schema and the clean data.
 *
 *  SE-1 two migrations through the runner: a language, a class group and a class with two attributes (a German name,
 *       a datatype setting) and an object of it; then the class changed with objects (an attribute added, one
 *       renamed, one removed), a section created and assigned, a user group, a user with a password in it and in a
 *       second group, a role with limited policies assigned to the group in a subtree, an object state group with two
 *       states. Ensured again: nothing changes. The second migration updates, so it cannot undo itself
 *  SE-2 a migration whose steps undo themselves (creates and an assign) runs and is rolled back: everything is gone;
 *       the user is named by secrets (environment variables)
 *  SE-3 the deletes: a class with objects only with with_objects, a role, a section, a user, a group, a state group,
 *       a class group and a language
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationContentStepsEngineChecks.php';

trait expMigrationStructureStepsEngineChecks
{
    use expMigrationContentStepsEngineChecks;

    /**
     * Removes what an earlier run of the checks left (MongoDB keeps its database for the class).
     *
     * @param eZDBInterface $db
     */
    protected function structureClean( $db )
    {
        // the objects, users and roles kept in memory by an earlier test (on SQLite a new database reuses their ids)
        eZContentObject::clearCache();
        eZUser::cleanupCache();
        eZRole::expireCache();
        eZContentObjectState::cleanDefaultsCache();
        if ( class_exists( 'expMemberManagement' ) )
            expMemberManagement::resetCache();
        unset( $GLOBALS['eZContentLanguageList'], $GLOBALS['eZContentLanguageMask'] );
        eZContentLanguage::expireCache();
        eZContentClass::expireCache();
        // nobody is signed in here, so the objects are purged directly (the delete operation would refuse them)
        $this->contentClean( $db, array( 'wp10b-note', 'wp10b-editors', 'wp10b-reverted-group' ) );
        foreach ( array( 'wp10b_note', 'wp10b_revert' ) as $identifier )
        {
            $class = eZContentClass::fetchByIdentifier( $identifier );
            if ( $class instanceof eZContentClass )
            {
                $db->begin();
                foreach ( (array)eZContentObject::fetchSameClassList( (int)$class->attribute( 'id' ) ) as $object )
                    $object->purge();
                eZContentClassOperations::remove( (int)$class->attribute( 'id' ) );
                $db->commit();
            }
        }
        foreach ( array( 'wp10b-editor', 'wp10b-reverted' ) as $login )
        {
            $user = eZUser::fetchByName( $login );
            if ( $user instanceof eZUser )
            {
                $object = eZContentObject::fetch( (int)$user->attribute( 'contentobject_id' ) );
                $db->begin();
                if ( $object )
                    $object->purge();
                eZUser::removeUser( (int)$user->attribute( 'contentobject_id' ) );
                $db->commit();
            }
        }
        $this->contentClean( $db, array( 'wp10b-note', 'wp10b-editors', 'wp10b-reverted-group' ) );
        foreach ( array( 'WP10b editors', 'WP10b reverted' ) as $name )
        {
            $role = eZRole::fetchByName( $name );
            if ( $role instanceof eZRole )
            {
                $db->begin();
                $role->removeThis();
                $db->commit();
            }
        }
        foreach ( array( 'wp10b_news', 'wp10b_reverted' ) as $identifier )
        {
            $section = eZSection::fetchByIdentifier( $identifier );
            if ( $section instanceof eZSection )
            {
                $db->begin();
                $section->removeThis();
                $db->commit();
            }
        }
        foreach ( array( 'wp10b_review', 'wp10b_reverted' ) as $identifier )
        {
            $group = eZContentObjectStateGroup::fetchByIdentifier( $identifier );
            if ( $group instanceof eZContentObjectStateGroup )
                eZContentObjectStateGroup::removeByID( (int)$group->attribute( 'id' ) );
        }
        foreach ( array( 'WP10b classes', 'WP10b reverted' ) as $name )
        {
            $group = eZContentClassGroup::fetchByName( $name );
            if ( $group instanceof eZContentClassGroup )
            {
                eZContentClassClassGroup::removeGroupMembers( (int)$group->attribute( 'id' ) );
                eZContentClassGroup::removeSelected( (int)$group->attribute( 'id' ) );
            }
        }
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261103%wp10b%'" );
        eZRole::expireCache();
        eZContentClass::expireCache();
    }

    /**
     * SE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkStructure( $db )
    {
        $this->structureClean( $db );
        $migrations = $this->contentMigrations( 'structure', array(
            '20261103100000_wp10b_class.yml' => <<<'YAML'
# description: A language, a class group, a class and a note
steps:
    - type: language
      mode: ensure
      locale: ger-DE
      name: Deutsch
    - type: content_class_group
      mode: create
      name: WP10b classes
      references: { class_group: group_id }
    - type: content_class
      mode: create
      identifier: wp10b_note
      language: eng-US
      name: WP10b note
      names: { ger-DE: WP10b-Notiz }
      name_pattern: "<title>"
      container: false
      groups: [ WP10b classes ]
      attributes:
          - { identifier: title, type: ezstring, name: Title, names: { ger-DE: Titel }, required: true, settings: { data_int1: 120 } }
          - { identifier: body, type: eztext, name: Body }
      references: { note_class: class_id }
    - type: content
      mode: create
      class: wp10b_note
      parent: 2
      remote_id: wp10b-note
      fields: { title: First note, body: Some text }
      references: { note: node_id }
YAML
            , '20261103100100_wp10b_structure.yml' => <<<'YAML'
# description: The class changed with its objects, a section, a group with a user, a role, states
# requires: 20261103100000_wp10b_class
steps:
    - type: content_class
      mode: update
      class: wp10b_note
      name_pattern: "<title> <intro>"
      add_attributes:
          - { identifier: intro, type: ezstring, name: Intro, searchable: false }
      update_attributes:
          title: { name: Heading, names: { ger-DE: Überschrift } }
      remove_attributes: [ body ]
    - type: section
      mode: create
      identifier: wp10b_news
      name: WP10b news
    - type: section
      mode: assign
      section: wp10b_news
      node: object-remote:wp10b-note
    - type: user_group
      mode: create
      parent: 5
      remote_id: wp10b-editors
      name: WP10b editors
      names: { ger-DE: WP10b-Redaktion }
      references: { editors: node_id }
    - type: user
      mode: create
      login: wp10b-editor
      email: wp10b-editor@example.com
      password: Wp10b-secret!
      groups: [ "${ref.editors}", 12 ]
      fields: { first_name: Wp, last_name: Editor }
      references: { editor: object_id }
    - type: role
      mode: create
      name: WP10b editors
      policies:
          - { module: content, function: read }
          - { module: content, function: edit, limitations: { Class: [ wp10b_note ], Section: [ wp10b_news ] } }
    - type: role
      mode: assign
      role: WP10b editors
      groups: [ remote:wp10b-editors ]
      subtree: 2
    - type: object_state_group
      mode: create
      identifier: wp10b_review
      name: WP10b review
      names: { ger-DE: WP10b-Prüfung }
      states:
          - { identifier: open, name: Open }
          - { identifier: done, name: Done, names: { ger-DE: Erledigt } }
      references: { states: state_ids }
YAML
        ) );
        $this->assertFalse( $migrations[1]->reversible(), 'an update cannot undo itself' );
        $dry = ( new expMigrationRunner( $this->runnerContext( $db, false ) ) )->run( $migrations );
        $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
        $this->assertNull( eZContentClass::fetchByIdentifier( 'wp10b_note' ), 'the dry run created nothing' );
        $this->assertStringNotContainsString( 'Wp10b-secret!', json_encode( $dry ), 'the password is in no plan' );

        $context = $this->runnerContext( $db );
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $messages = array_merge( $report['migrations'][0]['messages'], isset( $report['migrations'][1] ) ? $report['migrations'][1]['messages'] : array() );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( $messages ) );
        $this->assertStringNotContainsString( 'Wp10b-secret!', json_encode( $report ), 'the password is in no message' );
        eZContentClass::expireCache();

        $class = eZContentClass::fetchByIdentifier( 'wp10b_note' );
        $this->assertInstanceOf( 'eZContentClass', $class );
        $this->assertSame( (int)$class->attribute( 'id' ), $context->reference( 'note_class' ) );
        $this->assertSame( 'WP10b-Notiz', $class->name( 'ger-DE' ) );
        $this->assertSame( '<title> <intro>', $class->attribute( 'contentobject_name' ) );
        $identifiers = array();
        foreach ( $class->fetchAttributes() as $attribute )
            $identifiers[(int)$attribute->attribute( 'placement' )] = $attribute->attribute( 'identifier' );
        ksort( $identifiers );
        $this->assertSame( array( 1 => 'title', 2 => 'intro' ), $identifiers );
        $title = $class->fetchAttributeByIdentifier( 'title' );
        $this->assertSame( 120, (int)$title->attribute( 'data_int1' ) );
        $this->assertSame( 1, (int)$title->attribute( 'is_required' ) );
        $this->assertSame( 'Heading', $title->name( 'eng-US' ) );
        $this->assertSame( 'Überschrift', $title->name( 'ger-DE' ) );
        $this->assertSame( 0, (int)$class->fetchAttributeByIdentifier( 'intro' )->attribute( 'is_searchable' ) );
        $groups = eZContentClassClassGroup::fetchGroupList( (int)$class->attribute( 'id' ), 0 );
        $this->assertSame( array( 'WP10b classes' ), array_map( function ( $g ) { return (string)$g->attribute( 'group_name' ); }, $groups ) );

        $note = self::contentObject( 'wp10b-note' );
        $map = $note->fetchDataMap();
        $this->assertArrayHasKey( 'intro', $map, 'the object got the added attribute' );
        $this->assertArrayNotHasKey( 'body', $map, 'the removed attribute left the object' );
        $section = eZSection::fetchByIdentifier( 'wp10b_news' );
        $this->assertSame( (int)$section->attribute( 'id' ), (int)$note->attribute( 'section_id' ) );

        $user = eZUser::fetchByName( 'wp10b-editor' );
        $this->assertInstanceOf( 'eZUser', $user );
        $this->assertSame( 'wp10b-editor@example.com', $user->attribute( 'email' ) );
        $this->assertSame( eZUser::createHash( 'wp10b-editor', 'Wp10b-secret!', eZUser::site(), (int)$user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) ),
                           $user->attribute( 'password_hash' ) );
        $this->assertTrue( (bool)$user->isEnabled() );
        $parents = array();
        foreach ( eZContentObjectTreeNode::fetchByContentObjectID( (int)$user->attribute( 'contentobject_id' ) ) as $node )
            $parents[] = (int)$node->attribute( 'parent_node_id' );
        sort( $parents );
        $expected = array( (int)$context->reference( 'editors' ), 12 );
        sort( $expected );
        $this->assertSame( $expected, $parents, 'the user is in both groups' );

        $role = eZRole::fetchByName( 'WP10b editors' );
        $this->assertInstanceOf( 'eZRole', $role );
        $limits = array();
        foreach ( $role->policyList() as $policy )
            foreach ( $policy->attribute( 'limitations' ) as $limitation )
                foreach ( $limitation->attribute( 'values' ) as $value )
                    $limits[$limitation->attribute( 'identifier' )][] = (int)$value->attribute( 'value' );
        $this->assertSame( array( 'Class' => array( (int)$class->attribute( 'id' ) ), 'Section' => array( (int)$section->attribute( 'id' ) ) ), $limits );
        $assigned = array();
        foreach ( $role->fetchUserByRole() as $row )
            $assigned[] = array( (string)$row['user_object']->attribute( 'remote_id' ), (string)$row['limit_ident'], (string)$row['limit_value'] );
        $this->assertSame( array( array( 'wp10b-editors', 'Subtree', '/1/2/' ) ), $assigned );

        $group = eZContentObjectStateGroup::fetchByIdentifier( 'wp10b_review' );
        $this->assertInstanceOf( 'eZContentObjectStateGroup', $group );
        $states = $context->reference( 'states' );
        $this->assertSame( array( 'open', 'done' ), array_keys( $states ) );
        $done = eZPersistentObject::fetchObject( eZContentObjectState::definition(), null, array( 'id' => (int)$states['done'] ) );
        $this->assertSame( 'Erledigt', $done->translationByLocale( 'ger-DE' )->attribute( 'name' ) );

        // ensured again: nothing changes
        $again = $this->contentMigrations( 'structure-again', array(
            '20261103100200_wp10b_again.yml' => <<<'YAML'
# description: The same ensured again
steps:
    - { type: language, mode: ensure, locale: ger-DE, name: Deutsch, references: { l: changed } }
    - { type: content_class_group, mode: ensure, name: WP10b classes, references: { g: changed } }
    - type: content_class
      mode: ensure
      identifier: wp10b_note
      language: eng-US
      names: { ger-DE: WP10b-Notiz }
      attributes:
          - { identifier: title, type: ezstring, name: Heading, settings: { data_int1: 120 } }
      references: { c: changed }
    - { type: section, mode: ensure, identifier: wp10b_news, name: WP10b news, references: { s: changed } }
    - { type: user, mode: ensure, login: wp10b-editor, email: wp10b-editor@example.com, groups: [ 12 ], references: { u: changed } }
    - { type: role, mode: ensure, name: WP10b editors, policies: [ { module: content, function: read } ], references: { r: changed } }
    - { type: role, mode: assign, role: WP10b editors, groups: [ remote:wp10b-editors ], subtree: 2, references: { a: changed } }
    - type: object_state_group
      mode: ensure
      identifier: wp10b_review
      name: WP10b review
      states: [ { identifier: open, name: Open } ]
      references: { o: changed }
YAML
        ) );
        $context = $this->runnerContext( $db );
        $report = ( new expMigrationRunner( $context ) )->run( $again );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        foreach ( array( 'l', 'g', 'c', 's', 'u', 'r', 'a', 'o' ) as $name )
            $this->assertFalse( $context->reference( $name ), "$name: ensured again without a change " . json_encode( $report['migrations'][0]['messages'] ) );

        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( array( $migrations[1] ) );
        $this->assertNotSame( 0, $rollback['exit'], 'a migration that cannot undo itself is not rolled back' );
        $this->structureClean( $db );
    }

    /**
     * SE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkStructureRevert( $db )
    {
        $this->structureClean( $db );
        $hadNorwegian = (bool)eZContentLanguage::fetchByLocale( 'nor-NO' );
        $migrations = $this->contentMigrations( 'structure-revert', array(
            '20261103110000_wp10b_revert.yml' => <<<'YAML'
# description: Creates that undo themselves
steps:
    - { type: content_class_group, mode: create, name: WP10b reverted }
    - type: content_class
      mode: create
      identifier: wp10b_revert
      name: WP10b reverted
      groups: [ WP10b reverted ]
      attributes: [ { identifier: name, type: ezstring, name: Name } ]
    - { type: section, mode: create, identifier: wp10b_reverted, name: WP10b reverted }
    - { type: user_group, mode: create, parent: 5, remote_id: wp10b-reverted-group, name: WP10b reverted }
    - { type: user, mode: create, secrets: { login: "env:WP10B_LOGIN", email: "env:WP10B_EMAIL", password: "env:WP10B_PASSWORD" }, groups: [ object-remote:wp10b-reverted-group ] }
    - { type: role, mode: create, name: WP10b reverted, policies: [ { module: user, function: login } ] }
    - { type: role, mode: assign, role: WP10b reverted, users: [ wp10b-reverted ] }
    - { type: object_state_group, mode: create, identifier: wp10b_reverted, name: WP10b reverted, states: [ { identifier: one, name: One } ] }
YAML
            . ( $hadNorwegian ? "\n" : "\n    - { type: language, mode: create, locale: nor-NO, name: Norsk }\n" ),
        ) );
        $this->assertTrue( $migrations[0]->reversible() );
        putenv( 'WP10B_LOGIN=wp10b-reverted' );
        putenv( 'WP10B_EMAIL=wp10b-reverted@example.com' );
        putenv( 'WP10B_PASSWORD=x-Wp10b-1' );
        $report =( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $this->assertInstanceOf( 'eZRole', eZRole::fetchByName( 'WP10b reverted' ) );
        $this->assertInstanceOf( 'eZUser', eZUser::fetchByName( 'wp10b-reverted' ) );

        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $migrations );
        $this->assertSame( 0, $rollback['exit'], implode( '; ', $rollback['errors'] ) . ' ' . json_encode( array_column( $rollback['migrations'], 'messages' ) ) );
        $this->assertSame( array( 'undone' ), array_column( $rollback['migrations'], 'action' ) );
        eZContentClass::expireCache();
        eZRole::expireCache();
        $this->assertNull( eZContentClass::fetchByIdentifier( 'wp10b_revert' ) );
        $this->assertNull( eZContentClassGroup::fetchByName( 'WP10b reverted' ) );
        $this->assertNull( eZSection::fetchByIdentifier( 'wp10b_reverted' ) );
        $this->assertNull( self::contentObject( 'wp10b-reverted-group' ) );
        $this->assertNull( eZUser::fetchByName( 'wp10b-reverted' ) );
        $this->assertNull( eZRole::fetchByName( 'WP10b reverted' ) );
        $this->assertEmpty( eZContentObjectStateGroup::fetchByIdentifier( 'wp10b_reverted' ) );
        if ( !$hadNorwegian )
        {
            unset( $GLOBALS['eZContentLanguageList'] );
            $this->assertFalse( (bool)eZContentLanguage::fetchByLocale( 'nor-NO' ) );
        }
        $this->structureClean( $db );
    }

    /**
     * SE-3
     *
     * @param eZDBInterface $db
     */
    protected function checkStructureDeletes( $db )
    {
        $this->structureClean( $db );
        $setup = $this->contentMigrations( 'structure-deletes', array(
            '20261103120000_wp10b_setup.yml' => <<<'YAML'
# description: What the deletes remove
steps:
    - { type: content_class_group, mode: create, name: WP10b classes }
    - { type: content_class, mode: create, identifier: wp10b_note, name: Note, groups: [ WP10b classes ], attributes: [ { identifier: title, type: ezstring } ] }
    - { type: content, mode: create, class: wp10b_note, parent: 2, remote_id: wp10b-note, fields: { title: Doomed } }
    - { type: section, mode: create, identifier: wp10b_news, name: WP10b news }
    - { type: user_group, mode: create, parent: 5, remote_id: wp10b-editors, name: WP10b editors }
    - { type: user, mode: create, login: wp10b-editor, email: wp10b-editor@example.com, password: x-Wp10b-2, groups: [ object-remote:wp10b-editors ] }
    - { type: role, mode: create, name: WP10b editors, policies: [ { module: content, function: read, limitations: { Section: [ wp10b_news ] } } ] }
    - { type: object_state_group, mode: create, identifier: wp10b_review, name: Review, states: [ { identifier: open, name: Open } ] }
YAML
            , '20261103120100_wp10b_class_with_objects.yml' => "# description: refused\n# requires: 20261103120000_wp10b_setup\n- { type: content_class, mode: delete, class: wp10b_note }\n",
        ) );
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $setup );
        $this->assertSame( 'applied', $report['migrations'][0]['action'], json_encode( $report['migrations'][0]['messages'] ) . implode( '; ', $report['errors'] ) );
        $this->assertSame( 'failed', $report['migrations'][1]['action'] );
        $this->assertStringContainsString( 'has 1 object(s): with_objects: true deletes them', implode( "\n", $report['errors'] ) . json_encode( $report['migrations'][1] ) );

        $deletes = $this->contentMigrations( 'structure-deletes-2', array(
            '20261103120200_wp10b_deletes.yml' => <<<'YAML'
# description: Everything deleted again
steps:
    - { type: role, mode: delete, role: WP10b editors }
    - { type: content_class, mode: delete, class: wp10b_note, with_objects: true }
    - { type: section, mode: delete, section: wp10b_news }
    - { type: user, mode: delete, user: wp10b-editor }
    - { type: user_group, mode: delete, group: remote:wp10b-editors }
    - { type: object_state_group, mode: delete, group: wp10b_review }
    - { type: content_class_group, mode: delete, group: WP10b classes }
    - { type: role, mode: delete, role: no such role, missing: skip }
YAML
        ) );
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $deletes );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        eZContentClass::expireCache();
        eZRole::expireCache();
        $this->assertNull( eZRole::fetchByName( 'WP10b editors' ) );
        $this->assertNull( eZContentClass::fetchByIdentifier( 'wp10b_note' ) );
        $this->assertNull( self::contentObject( 'wp10b-note' ), 'the object went with its class' );
        $this->assertNull( eZSection::fetchByIdentifier( 'wp10b_news' ) );
        $this->assertNull( eZUser::fetchByName( 'wp10b-editor' ) );
        $this->assertNull( self::contentObject( 'wp10b-editors' ), 'the group is deleted after the user: deleting an account keeps the user the migration runs as' );
        $this->assertEmpty( eZContentObjectStateGroup::fetchByIdentifier( 'wp10b_review' ) );
        $this->assertNull( eZContentClassGroup::fetchByName( 'WP10b classes' ) );
        $this->structureClean( $db );
    }
}
