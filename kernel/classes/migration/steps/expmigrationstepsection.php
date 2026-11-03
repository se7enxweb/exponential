<?php
/**
 * File containing the expMigrationStepSection class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "section": creates, updates, ensures and deletes sections, and assigns a section to content.
 *
 *   - type: section
 *     mode: ensure                     create, update, ensure, delete, assign
 *     identifier: news
 *     name: News
 *     navigation_part: ezcontentnavigationpart
 *   - type: section
 *     mode: assign
 *     section: news
 *     node: "path:news"                the node whose object gets the section ...
 *     scope: subtree                   ... with the objects below it (subtree), only those that had its section
 *                                      (inherited), or the object alone (object)
 *
 * A section is created with eZSiteInstaller::createContentSection(), as the site packages create theirs, and assigned
 * the way section/assign of the administration assigns it (eZContentObjectTreeNode::assignSectionToSubTree()). A
 * section in use (by objects, roles or policies) is not deleted. A create undoes itself (the section is removed again
 * while nothing uses it); an assign cannot know the sections before it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepSection extends expMigrationStepStructureBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'section';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a section, or assigns a section to a part of the content tree',
            'modes' => array( 'create', 'update', 'ensure', 'delete', 'assign' ),
            'keys' => array(
                'section' => array( 'type' => 'identifier', 'kind' => 'section', 'modes' => array( 'update', 'delete', 'assign' ),
                                    'description' => 'the section: its identifier or id' ),
                'identifier' => array( 'type' => 'string', 'modes' => $writing,
                                       'description' => 'the identifier of the section; ensure finds the section by it (update: a new identifier)' ),
                'name' => array( 'type' => 'string', 'modes' => $writing, 'description' => 'the name of the section' ),
                'navigation_part' => array( 'type' => 'string', 'modes' => $writing,
                                            'description' => 'the navigation part of the administration the section belongs to (default: ezcontentnavigationpart)' ),
                'node' => array( 'type' => 'identifier', 'kind' => 'node', 'modes' => array( 'assign' ),
                                 'description' => 'the node whose object (and the objects below it, by scope:) gets the section' ),
                'object' => array( 'type' => 'identifier', 'kind' => 'object', 'modes' => array( 'assign' ),
                                   'description' => 'the object that gets the section (or node:), at its main node' ),
                'scope' => array( 'type' => 'string', 'default' => 'subtree', 'values' => array( 'subtree', 'inherited', 'object' ), 'modes' => array( 'assign' ),
                                  'description' => 'subtree (the default): the objects below too; inherited: those below that had the same section; object: the object alone' ),
            ) + self::missingKey( array( 'update', 'delete', 'assign' ), 'the section' ),
            'values' => array(
                'section_id' => 'the id of the section',
                'identifier' => 'its identifier',
                'name' => 'its name',
                'created' => 'true when the step created the section',
                'changed' => 'true when the step changed it (created, updated, deleted or assigned)',
            ),
            'example' => "- type: section\n  mode: ensure\n  identifier: news\n  name: News\n"
                       . "- type: section\n  mode: assign\n  section: news\n  node: \"path:news\"\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        $errors = array();
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) )
            foreach ( array( 'identifier', 'name' ) as $key )
                if ( !isset( $step[$key] ) || trim( (string)$step[$key] ) === '' )
                    $errors[] = "$key is missing (mode $mode)";
        if ( isset( $step['identifier'] ) && !expMigrationReferences::contains( $step['identifier'] ) && !preg_match( '/^[A-Za-z_][A-Za-z0-9_\-]*$/', (string)$step['identifier'] ) )
            $errors[] = 'identifier: ' . expMigrationStepSchema::show( $step['identifier'] ) . ' is not a section identifier (letters, digits, _ and -)';
        if ( in_array( $mode, array( 'update', 'delete', 'assign' ), true ) && !isset( $step['section'] ) )
            $errors[] = "section is missing (mode $mode)";
        if ( $mode === 'assign' && isset( $step['node'] ) === isset( $step['object'] ) )
            $errors[] = 'name what gets the section by node: or by object: (one of them)';
        if ( $mode === 'update' && !isset( $step['identifier'] ) && !isset( $step['name'] ) && !isset( $step['navigation_part'] ) )
            $errors[] = 'nothing to update: identifier:, name: or navigation_part: is missing';
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return isset( $step['mode'] ) && $step['mode'] === 'assign' ? array( 'content', 'user_info_cache' ) : array( 'content' );
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
        switch ( $step['mode'] )
        {
            case 'create':
                return array( 'create the section ' . $step['identifier'] . ' "' . $step['name'] . '"' );
            case 'ensure':
                return array( 'ensure the section ' . $step['identifier'] . ' "' . $step['name'] . '": create it when missing, change what differs' );
            case 'update':
                return array( 'update the section ' . expMigrationStepSchema::show( $step['section'] ) . ': '
                              . implode( ', ', array_intersect( array( 'identifier', 'name', 'navigation_part' ), array_keys( $step ) ) ) );
            case 'delete':
                return array( 'delete the section ' . expMigrationStepSchema::show( $step['section'] ) . ' (only when nothing uses it)' );
            case 'assign':
                return array( 'assign the section ' . expMigrationStepSchema::show( $step['section'] ) . ' to '
                              . ( isset( $step['node'] ) ? 'node ' . expMigrationStepSchema::show( $step['node'] ) : 'the object ' . expMigrationStepSchema::show( $step['object'] ) )
                              . ' (' . $step['scope'] . ')' );
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
        if ( $mode === 'create' || $mode === 'ensure' )
        {
            $section = eZSection::fetchByIdentifier( (string)$step['identifier'] );
            if ( $section instanceof eZSection && $mode === 'create' )
                throw new RuntimeException( 'the section ' . $step['identifier'] . ' is there already (mode ensure changes it)' );
            if ( $section instanceof eZSection )
                return $this->update( $section, $step );
            $db = eZDB::instance();
            $db->begin();
            try
            {
                $section = self::installer()->createContentSection( array( 'name' => (string)$step['name'],
                                                                           'navigation_part_identifier' => isset( $step['navigation_part'] ) ? (string)$step['navigation_part'] : 'ezcontentnavigationpart' ) );
                $section->setAttribute( 'identifier', (string)$step['identifier'] );
                $section->store();
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
            return self::result( array( 'created the section ' . $step['identifier'] . ' "' . $step['name'] . '" (' . $section->attribute( 'id' ) . ')' ),
                                 self::values( $section ) + array( 'created' => true, 'changed' => true ) );
        }
        $id = expMigrationIdentifier::find( 'section', $step['section'] );
        $section = $id !== null ? eZSection::fetch( $id ) : null;
        if ( !$section instanceof eZSection )
            return self::missingResult( $step, 'the section ' . expMigrationStepSchema::show( $step['section'] ), array( 'section_id' => null, 'identifier' => null, 'name' => null ) );
        switch ( $mode )
        {
            case 'update':
                return $this->update( $section, $step );
            case 'delete':
                return $this->delete( $section );
            case 'assign':
                return $this->assign( $section, $step );
        }
        throw new RuntimeException( "the mode $mode is not one of create, update, ensure, delete, assign" );
    }

    /**
     * Removes the section a create made, while nothing uses it.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        $section = eZSection::fetchByIdentifier( (string)$step['identifier'] );
        if ( !$section instanceof eZSection )
            return array( 'the section ' . $step['identifier'] . ' is not there: nothing to undo' );
        $result = $this->delete( $section );
        return $result['lines'];
    }

    /**
     * @param eZSection $section
     * @param array $step
     * @return array
     */
    protected function update( eZSection $section, array $step )
    {
        $columns = array();
        if ( isset( $step['identifier'] ) )
            $columns['identifier'] = (string)$step['identifier'];
        if ( isset( $step['name'] ) )
            $columns['name'] = (string)$step['name'];
        if ( isset( $step['navigation_part'] ) )
            $columns['navigation_part_identifier'] = (string)$step['navigation_part'];
        $changed = array();
        foreach ( $columns as $column => $value )
        {
            if ( (string)$section->attribute( $column ) === $value )
                continue;
            if ( $column === 'identifier' && eZSection::fetchByIdentifier( $value ) instanceof eZSection )
                throw new RuntimeException( "another section has the identifier $value" );
            $section->setAttribute( $column, $value );
            $changed[] = $column;
        }
        $label = 'the section ' . $section->attribute( 'identifier' );
        if ( !$changed )
            return self::result( array( "$label is as the step has it" ), self::values( $section ) + array( 'created' => false, 'changed' => false ) );
        $section->store();
        return self::result( array( "updated $label: " . implode( ', ', $changed ) ), self::values( $section ) + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * @param eZSection $section
     * @return array
     */
    protected function delete( eZSection $section )
    {
        $values = self::values( $section );
        if ( !$section->canBeRemoved() )
            throw new RuntimeException( 'the section ' . $values['identifier'] . ' is in use (by objects, role assignments or policies): it is not deleted' );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $section->removeThis();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        return self::result( array( 'deleted the section ' . $values['identifier'] . ' (' . $values['section_id'] . ')' ), $values + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * @param eZSection $section
     * @param array $step
     * @return array
     */
    protected function assign( eZSection $section, array $step )
    {
        if ( isset( $step['node'] ) )
        {
            $nodeId = expMigrationIdentifier::resolve( 'node', $step['node'] );
        }
        else
        {
            $object = eZContentObject::fetch( expMigrationIdentifier::resolve( 'object', $step['object'] ) );
            $nodeId = $object instanceof eZContentObject ? (int)$object->attribute( 'main_node_id' ) : 0;
            if ( !$nodeId )
                throw new RuntimeException( 'the object ' . expMigrationStepSchema::show( $step['object'] ) . ' has no location to assign the section at' );
        }
        $node = eZContentObjectTreeNode::fetch( $nodeId );
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new RuntimeException( "no node $nodeId on this installation" );
        $sectionId = (int)$section->attribute( 'id' );
        $scope = isset( $step['scope'] ) ? (string)$step['scope'] : eZSection::SCOPE_SUBTREE;
        $db = eZDB::instance();
        $db->begin();
        try
        {
            if ( expKernelTableSchema::type( $db ) === 'mongodb' )
                self::assignOneByOne( $node, $sectionId, $scope );
            else
                eZContentObjectTreeNode::assignSectionToSubTree( $nodeId, $sectionId, false, true, $scope, false );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( (int)$node->attribute( 'contentobject_id' ) );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'section_id' ) !== $sectionId )
            throw new RuntimeException( 'the section ' . $section->attribute( 'identifier' ) . " was not assigned at node $nodeId" );
        return self::result( array( 'assigned the section ' . $section->attribute( 'identifier' ) . " to node $nodeId ($scope)" ),
                             self::values( $section ) + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * The assignment of assignSectionToSubTree() for MongoDB, whose driver does not run its UPDATE ... WHERE id IN
     * over the objects of a subtree: the node's object and, by the scope, those whose main location is below it
     * (inherited: those of them that have the section the node's object had; an object below with another section
     * does not keep its subtree out here), each stored with the section, and the search index told.
     *
     * @param eZContentObjectTreeNode $node
     * @param int $sectionId
     * @param string $scope
     */
    protected static function assignOneByOne( eZContentObjectTreeNode $node, $sectionId, $scope )
    {
        $top = eZContentObject::fetch( (int)$node->attribute( 'contentobject_id' ) );
        $oldSection = $top instanceof eZContentObject ? (int)$top->attribute( 'section_id' ) : null;
        $ids = array( (int)$node->attribute( 'contentobject_id' ) );
        if ( $scope !== eZSection::SCOPE_OBJECT )
        {
            $rows = eZContentObjectTreeNode::subTreeByNodeID( array( 'IgnoreVisibility' => true, 'Limitation' => array(), 'MainNodeOnly' => true,
                                                                       'AsObject' => false ), (int)$node->attribute( 'node_id' ) );
            foreach ( is_array( $rows ) ? $rows : array() as $row )
                $ids[] = (int)( isset( $row['contentobject_id'] ) ? $row['contentobject_id'] : $row['id'] );
        }
        $changed = array();
        foreach ( array_unique( $ids ) as $id )
        {
            $object = eZContentObject::fetch( $id );
            if ( !$object instanceof eZContentObject )
                continue;
            if ( $scope === eZSection::SCOPE_INHERITED && $id !== $ids[0] && (int)$object->attribute( 'section_id' ) !== $oldSection )
                continue;
            $object->setAttribute( 'section_id', (int)$sectionId );
            $object->store();
            $changed[] = $id;
        }
        if ( $changed )
            eZSearch::updateObjectsSection( $changed, (int)$sectionId );
    }

    /**
     * @param eZSection $section
     * @return array
     */
    protected static function values( eZSection $section )
    {
        return array( 'section_id' => (int)$section->attribute( 'id' ), 'identifier' => (string)$section->attribute( 'identifier' ),
                      'name' => (string)$section->attribute( 'name' ) );
    }
}
