<?php
// description: A table for notes, with a first note
// author: Exponential tests
namespace ExpTests\Migrations\Runner;

/**
 * Creates the table exp_test_wp3_note on every engine (expKernelTableSchema::install(): SQL on MySQL, PostgreSQL,
 * SQLite and Oracle, a collection on MongoDB) and writes a first note. down() drops the table again.
 */
final class NoteTable extends \expMigrationPhp
{
    const TABLE = 'exp_test_wp3_note';

    /** @return array the table's definition in the format of share/db_schema.dba */
    public static function schema()
    {
        return array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array(
                'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'title' => array( 'length' => 150, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
            ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
    }

    public function plan( $context )
    {
        return array( 'create the table ' . self::TABLE, 'write the first note' );
    }

    public function up( $context )
    {
        $messages = array();
        if ( !\expKernelTableSchema::install( array( self::TABLE ), $context->db(), self::schema(), $messages ) )
            throw new \RuntimeException( 'the table could not be created: ' . implode( '; ', $messages ) );
        $context->db()->query( 'INSERT INTO ' . self::TABLE . " ( id, title ) VALUES ( 1, 'First note' )" );
        $context->setReference( 'note_table', self::TABLE );
        return array( 'created ' . self::TABLE, 'wrote the note 1' );
    }

    public function down( $context )
    {
        $context->db()->query( 'DROP TABLE ' . self::TABLE );
        \expKernelTableSchema::reset();
        return array( 'dropped ' . self::TABLE );
    }
}
