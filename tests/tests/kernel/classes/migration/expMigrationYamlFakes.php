<?php
/**
 * The step types of the YAML engine tests, registered by the tests the way an extension registers its own
 * (migration.ini [StepSettings] StepTypeList): wp9_record keeps what it is given in memory (expMigrationYamlTest),
 * wp9_note writes rows into a table of the database and wp9_node resolves a node identifier
 * (expMigrationYamlEngineChecks), and the step types with broken definitions the registry refuses.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/**
 * Records every call in a static list: plan, apply and revert with the step it got. A step with fail: true throws.
 */
class expMigrationYamlRecordStep extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    /** @var array[] array( call, step ) in order */
    public static $log = array();

    public function name()
    {
        return 'wp9_record';
    }

    public function schema()
    {
        return array(
            'description' => 'Records the step (a test)',
            'modes' => array( 'create', 'ensure', 'delete' ),
            'mode' => 'create',
            'keys' => array(
                'name' => array( 'type' => 'string', 'required' => true, 'description' => 'a name' ),
                'value' => array( 'type' => 'any', 'description' => 'a value' ),
                'count' => array( 'type' => 'int', 'default' => 1, 'description' => 'a number' ),
                'parent' => array( 'type' => 'identifier', 'kind' => 'node', 'description' => 'a node' ),
                'color' => array( 'type' => 'string', 'values' => array( 'red', 'green' ), 'description' => 'one of two' ),
                'keep' => array( 'type' => 'bool', 'modes' => array( 'delete' ), 'description' => 'only when deleting' ),
                'fail' => array( 'type' => 'bool', 'default' => false, 'description' => 'throw' ),
            ),
            'values' => array( 'name' => 'the name', 'value' => 'the value', 'count' => 'the number' ),
            'example' => "- type: wp9_record\n  name: one\n",
        );
    }

    public function validate( array $step )
    {
        return isset( $step['name'] ) && $step['name'] === 'invalid' ? array( 'the name invalid is refused by the type' ) : array();
    }

    public function caches( array $step )
    {
        return $step['mode'] === 'delete' ? array( 'content', 'template-block' ) : array( 'content' );
    }

    public function plan( array $step, $context )
    {
        self::$log[] = array( 'plan', $step );
        return array( $step['mode'] . ' ' . $step['name'] );
    }

    public function apply( array $step, $context )
    {
        self::$log[] = array( 'apply', $step );
        if ( !empty( $step['fail'] ) )
            throw new RuntimeException( 'failed on purpose: ' . $step['name'] );
        return self::result( array( $step['mode'] . 'd ' . $step['name'] ),
                             array( 'name' => $step['name'], 'value' => isset( $step['value'] ) ? $step['value'] : null, 'count' => $step['count'] ) );
    }

    public function revert( array $step, $context )
    {
        self::$log[] = array( 'revert', $step );
        return array( 'reverted ' . $step['name'] );
    }

    /** @return string[] the names of the calls of one kind, in order */
    public static function names( $call )
    {
        $names = array();
        foreach ( self::$log as $entry )
            if ( $entry[0] === $call )
                $names[] = $entry[1]['name'];
        return $names;
    }
}

/** A step type that cannot revert its step. */
class expMigrationYamlOneWayStep extends expMigrationStepTypeBase
{
    public function name()
    {
        return 'wp9_oneway';
    }

    public function schema()
    {
        return array( 'description' => 'Cannot be undone (a test)', 'keys' => array() );
    }

    public function apply( array $step, $context )
    {
        return array( 'one way' );
    }
}

/** A step type whose schema is broken. */
class expMigrationYamlBrokenStep extends expMigrationStepTypeBase
{
    public function name()
    {
        return 'wp9_broken';
    }

    public function schema()
    {
        return array( 'keys' => array( 'if' => array( 'type' => 'string' ), 'size' => array( 'type' => 'huge' ) ) );
    }

    public function apply( array $step, $context )
    {
        return array();
    }
}

/**
 * Writes notes into the table exp_test_wp9_note (made by the first note): mode create (the default) writes one row
 * with the next id and gives the values id and text; mode clear removes every row. Reverting a created note removes
 * it.
 */
class expMigrationYamlNoteStep extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    const TABLE = 'exp_test_wp9_note';

    public function name()
    {
        return 'wp9_note';
    }

    public function schema()
    {
        return array(
            'description' => 'Writes a note into exp_test_wp9_note (a test)',
            'modes' => array( 'create', 'clear' ),
            'mode' => 'create',
            'keys' => array(
                'text' => array( 'type' => 'string', 'required' => true, 'modes' => array( 'create' ), 'description' => 'the note' ),
            ),
            'values' => array( 'id' => 'the id of the note', 'text' => 'its text' ),
        );
    }

    public function caches( array $step )
    {
        return array( 'content' );
    }

    public function plan( array $step, $context )
    {
        return array( $step['mode'] === 'clear' ? 'remove every note' : 'write the note ' . $step['text'] );
    }

    public function apply( array $step, $context )
    {
        $db = $context->db();
        if ( $step['mode'] === 'clear' )
        {
            expKernelTableSchema::reset();
            if ( expKernelTableSchema::exists( self::TABLE, $db, true ) === true )
                $db->query( 'DELETE FROM ' . self::TABLE . ' WHERE id > 0' );
            return self::result( array( 'removed every note' ) );
        }
        self::ensureTable( $context );
        $id = self::count( $db ) + 1;
        $db->query( 'INSERT INTO ' . self::TABLE . ' ( id, note_text ) VALUES ( ' . (int)$id . ", '" . $db->escapeString( $step['text'] ) . "' )" );
        return self::result( array( "wrote the note $id" ), array( 'id' => $id, 'text' => $step['text'] ) );
    }

    public function revert( array $step, $context )
    {
        if ( $step['mode'] === 'clear' )
            return array();
        $db = $context->db();
        $db->query( 'DELETE FROM ' . self::TABLE . " WHERE note_text = '" . $db->escapeString( $step['text'] ) . "'" );
        return array( 'removed the note ' . $step['text'] );
    }

    /**
     * @param expMigrationContext $context
     */
    public static function ensureTable( $context )
    {
        expKernelTableSchema::reset();
        if ( expKernelTableSchema::exists( self::TABLE, $context->db(), true ) === true )
            return;
        $schema = array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array( 'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                               'note_text' => array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ) ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
        $messages = array();
        if ( !expKernelTableSchema::install( array( self::TABLE ), $context->db(), $schema, $messages ) )
            throw new RuntimeException( 'could not create the table: ' . implode( '; ', $messages ) );
        expKernelTableSchema::reset();
    }

    /** @return int the rows of the table */
    public static function count( $db )
    {
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ' . self::TABLE );
        return (int)( isset( $rows[0]['n'] ) ? $rows[0]['n'] : ( $rows ? reset( $rows[0] ) : 0 ) );
    }
}

/** Resolves a node identifier (expMigrationIdentifier) and gives its node id and object id. */
class expMigrationYamlNodeStep extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    public function name()
    {
        return 'wp9_node';
    }

    public function schema()
    {
        return array(
            'description' => 'Looks up a node (a test)',
            'keys' => array( 'node' => array( 'type' => 'identifier', 'kind' => 'node', 'required' => true, 'description' => 'the node' ) ),
            'values' => array( 'node_id' => 'the node id', 'object_id' => 'the object id' ),
        );
    }

    public function apply( array $step, $context )
    {
        $nodeId = expMigrationIdentifier::resolve( 'node', $step['node'] );
        $objectId = expMigrationIdentifier::resolve( 'object', 'path:' . self::path( $nodeId ) );
        return self::result( array( "found the node $nodeId" ), array( 'node_id' => $nodeId, 'object_id' => $objectId ) );
    }

    public function revert( array $step, $context )
    {
        return array();
    }

    /** @return string the url path of a node */
    private static function path( $nodeId )
    {
        $node = eZContentObjectTreeNode::fetch( $nodeId, false, false );
        return is_array( $node ) ? (string)$node['path_identification_string'] : '';
    }
}
