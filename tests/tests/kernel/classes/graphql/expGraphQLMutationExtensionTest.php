<?php
/**
 * Mutations of schema extensions (expGraphQLMutationExtension): expGraphQLTypes::extensionMutationFields() collects the
 * fields of the extensions that implement it, in their order, the first name winning; Mutation lists them after the
 * kernel's own.
 *
 * No database; the fields are plain arrays, so no GraphQL library is needed.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/graphql/expGraphQLMutationExtensionTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group graphql
 */

class expGraphQLMutationExtensionTestReadOnly implements expGraphQLSchemaExtension
{
    public function isAvailable() { return true; }
    public function name() { return 'test/read'; }
    public function queryFields( expGraphQLTypes $types ) { return array( 'testRead' => array( 'description' => 'read' ) ); }
    public function locationFields( expGraphQLTypes $types ) { return array(); }
    public function datatypes() { return array(); }
    public function fieldMapping( array $attribute, expGraphQLTypes $types ) { return null; }
}

class expGraphQLMutationExtensionTestWrites extends expGraphQLMutationExtensionTestReadOnly implements expGraphQLMutationExtension
{
    /** @var array */
    public $fields;

    public function __construct( array $fields ) { $this->fields = $fields; }
    public function mutationFields( expGraphQLTypes $types ) { return $this->fields; }
}

class expGraphQLMutationExtensionTest extends PHPUnit\Framework\TestCase
{
    public function testFieldsOfTheExtensionsThatWrite()
    {
        $types = new expGraphQLTypes( function () { return null; }, array(
            new expGraphQLMutationExtensionTestReadOnly(),
            new expGraphQLMutationExtensionTestWrites( array( 'first' => array( 'description' => 'a' ), 'shared' => array( 'description' => 'from a' ) ) ),
            new expGraphQLMutationExtensionTestWrites( array( 'shared' => array( 'description' => 'from b' ), 'second' => array( 'description' => 'b' ) ) ),
        ) );
        $fields = $types->extensionMutationFields();
        $this->assertSame( array( 'first', 'shared', 'second' ), array_keys( $fields ) );
        $this->assertSame( 'from a', $fields['shared']['description'], 'the first extension keeps a name' );
    }

    public function testNoExtensionNoFields()
    {
        $this->assertSame( array(), ( new expGraphQLTypes( function () { return null; } ) )->extensionMutationFields() );
    }

    public function testMutationTypeAddsThemAfterTheKernelsOwn()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/classes/graphql/expgraphqlmutations.php' );
        $this->assertStringContainsString( 'return $kernel + $types->extensionMutationFields();', $source );
    }
}
