<?php
/**
 * The SQLite driver knows the keys of the tables extensions ship.
 *
 * insertOrReplaceIsSafe() compares a table's keys with the shipped schema. It read the kernel's
 * share/db_schema.dba only, so every table of an extension (expqueue_job and its key (uuid)) was
 * reported as a table whose schema lost a key. The shipped schema now includes the
 * share/db_schema.dba of each active extension, the kernel's definition first.
 *
 * Database: none.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */
class eZSQLite3ShippedSchemaTest extends PHPUnit\Framework\TestCase
{
    /** @var string[] the schema files written by the test */
    private $files = array();

    protected function tearDown(): void
    {
        foreach ( $this->files as $file )
        {
            if ( is_file( $file ) )
                unlink( $file );
        }
        $this->files = array();
    }

    /**
     * @param array $schema
     * @return string the path of a schema file holding $schema
     */
    private function schemaFile( array $schema )
    {
        $file = tempnam( sys_get_temp_dir(), 'dba' );
        file_put_contents( $file, "<?php\n// Schema file\n\$schema = " . var_export( $schema, true ) . ";\n" );
        $this->files[] = $file;
        return $file;
    }

    public function testExtensionTablesAreKnown(): void
    {
        $kernel = $this->schemaFile( array(
            'ezcontentobject' => array( 'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) ),
        ) );
        $extension = $this->schemaFile( array(
            'expqueue_job' => array( 'indexes' => array(
                'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                'expqueue_job_uuid' => array( 'type' => 'unique', 'fields' => array( 'uuid' ) ),
                'expqueue_job_state' => array( 'type' => 'non-unique', 'fields' => array( 'state' ) ),
            ) ),
            // an extension never redefines a kernel table
            'ezcontentobject' => array( 'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'remote_id' ) ) ) ),
        ) );

        $schema = eZSQLite3DB::shippedSchema( array( $kernel, $extension, '/nonexistent/share/db_schema.dba' ) );

        $this->assertSame( array( array( 'id' ), array( 'uuid' ) ), eZSQLite3DB::schemaUniqueKeys( $schema, 'expqueue_job' ) );
        $this->assertSame( array( array( 'id' ) ), eZSQLite3DB::schemaUniqueKeys( $schema, 'ezcontentobject' ) );
        $this->assertSame( array(), eZSQLite3DB::schemaUniqueKeys( $schema, 'unknown_table' ) );
    }

    public function testFieldsGivenAsArraysAreRead(): void
    {
        $schema = array( 't' => array( 'indexes' => array(
            'k' => array( 'type' => 'unique', 'fields' => array( array( 'name' => 'a', 'length' => 10 ), 'b' ) ),
        ) ) );

        $this->assertSame( array( array( 'a', 'b' ) ), eZSQLite3DB::schemaUniqueKeys( $schema, 't' ) );
    }
}
