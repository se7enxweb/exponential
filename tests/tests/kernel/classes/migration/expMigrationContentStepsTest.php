<?php
/**
 * The content step types (content, location, content_version) without a database: their schemas, the checks of
 * their steps, their plans, which steps can undo themselves, and the identifier kinds they use.
 *
 *  MC-01 the kernel registers content, location and content_version from migration.ini; their schemas are valid
 *  MC-02 content: the keys each mode needs, one of object/node, fields and translations checked
 *  MC-03 location: the keys each mode needs, sort_order only with sort_field, a value outside the list refused
 *  MC-04 content_version: object or node, the version of restore
 *  MC-05 plans name what would change, placeholders as written
 *  MC-06 which steps can undo themselves: content create with a remote id; location create, hide, unhide;
 *        a migration of such steps is reversible, one with an update is not
 *  MC-07 identifiers of the kinds class, section, language, user, role and state: names and forms
 *  MC-08 the caches the steps touch
 *  MC-09 every registered step type has its generated reference page, English and German
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationContentStepsTest extends PHPUnit\Framework\TestCase
{
    /**
     * The errors of one step in a YAML text, checked by the engine as exp:migration:check does.
     *
     * @param string $yaml
     * @return string[]
     */
    private static function errors( $yaml )
    {
        $errors = array();
        expMigrationYaml::fromText( $yaml, expMigrationStepRegistry::fromSettings(), $errors );
        return $errors;
    }

    protected function setUp(): void
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        expMigrationStepRegistry::forget();
    }

    /** MC-01 */
    public function testRegistered()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array(), $registry->errors() );
        foreach ( array( 'content' => 'expMigrationStepContent', 'location' => 'expMigrationStepLocation',
                         'content_version' => 'expMigrationStepContentVersion' ) as $name => $class )
        {
            $this->assertInstanceOf( $class, $registry->get( $name ), $name );
            $this->assertSame( array(), expMigrationStepSchema::problems( $registry->get( $name )->schema() ), $name );
            $schema = $registry->get( $name )->schema();
            $this->assertNotEmpty( $schema['values'], "$name gives values to references:" );
            $this->assertSame( array(), self::errors( $schema['example'] ), "the example of $name is a valid step" );
        }
    }

    /** MC-02 */
    public function testContentChecks()
    {
        $this->assertSame( array(), self::errors( "- type: content\n  mode: create\n  class: folder\n  parent: 2\n  fields: { name: News }\n" ) );
        $errors = implode( "\n", self::errors( "- type: content\n  mode: create\n  fields: { name: News }\n" ) );
        $this->assertStringContainsString( 'steps[0] (content): class is missing (mode create)', $errors );
        $this->assertStringContainsString( 'steps[0] (content): parent is missing (mode create)', $errors );
        $this->assertStringContainsString( 'remote_id is missing: ensure finds the object by it',
                                           implode( "\n", self::errors( "- type: content\n  mode: ensure\n  class: folder\n  parent: 2\n" ) ) );
        $this->assertStringContainsString( 'name the object by object: or by node:',
                                           implode( "\n", self::errors( "- type: content\n  mode: update\n  object: 5\n  node: 2\n  fields: { name: X }\n" ) ) );
        $this->assertStringContainsString( 'nothing to update', implode( "\n", self::errors( "- type: content\n  mode: update\n  object: 5\n" ) ) );
        $this->assertStringContainsString( 'mode is missing', implode( "\n", self::errors( "- type: content\n  class: folder\n  parent: 2\n" ) ) );
        // keys of other modes, bad identifiers, field maps and language blocks
        $this->assertStringContainsString( 'steps[0] (content).trash: only in the mode delete',
                                           implode( "\n", self::errors( "- type: content\n  mode: create\n  class: folder\n  parent: 2\n  trash: true\n" ) ) );
        $this->assertStringContainsString( 'steps[0] (content).class: "Not a class!" is no identifier of a class',
                                           implode( "\n", self::errors( "- type: content\n  mode: create\n  class: 'Not a class!'\n  parent: 2\n" ) ) );
        $errors = implode( "\n", self::errors( "- type: content\n  mode: create\n  class: folder\n  parent: 2\n  language: english\n"
                                               . "  fields: { Name: X, tags: [ a, b ] }\n  translations: { german: { name: Y }, ger-DE: { name: [ x ] } }\n" ) );
        foreach ( array( 'language: "english" is no identifier of a language', 'fields: Name is not an attribute identifier',
                         'fields.tags: a value is a text, a number, true/false or null', 'translations: german is not a language code',
                         'translations.ger-DE.name: a value is a text' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'translations.eng-US: the language of fields:',
                                           implode( "\n", self::errors( "- type: content\n  mode: create\n  class: folder\n  parent: 2\n  language: eng-US\n"
                                                                        . "  translations: { eng-US: { name: Y } }\n" ) ) );
        $this->assertStringContainsString( '"gone" is not one of "fail", "skip"',
                                           implode( "\n", self::errors( "- type: content\n  mode: delete\n  object: 5\n  missing: gone\n" ) ) );
        // placeholders are checked when the step runs
        $this->assertSame( array(), self::errors( "- type: content\n  mode: update\n  node: \"\${ref.news}\"\n  language: \"\${ref.lang}\"\n  fields: \"\${ref.fields}\"\n" ) );
    }

    /** MC-03 */
    public function testLocationChecks()
    {
        $this->assertSame( array(), self::errors( "- type: location\n  mode: create\n  object: remote:abc\n  parent: path:media\n  priority: 3\n  main: true\n" ) );
        $this->assertStringContainsString( 'parent is missing (mode create)', implode( "\n", self::errors( "- type: location\n  mode: create\n  object: 5\n" ) ) );
        $this->assertStringContainsString( 'name the location by node:, or by object: and parent:',
                                           implode( "\n", self::errors( "- type: location\n  mode: delete\n  object: 5\n" ) ) );
        $this->assertSame( array(), self::errors( "- type: location\n  mode: delete\n  object: 5\n  parent: 2\n" ) );
        $this->assertStringContainsString( 'node is missing (mode move)', implode( "\n", self::errors( "- type: location\n  mode: move\n  parent: 2\n" ) ) );
        $this->assertStringContainsString( 'nothing to update', implode( "\n", self::errors( "- type: location\n  mode: update\n  node: 5\n" ) ) );
        $this->assertStringContainsString( 'sort_order needs sort_field',
                                           implode( "\n", self::errors( "- type: location\n  mode: update\n  node: 5\n  sort_order: asc\n" ) ) );
        $this->assertStringContainsString( '"title" is not one of',
                                           implode( "\n", self::errors( "- type: location\n  mode: update\n  node: 5\n  sort_field: title\n" ) ) );
        $this->assertStringContainsString( 'steps[0] (location).object: object-remote: names a node',
                                           implode( "\n", self::errors( "- type: location\n  mode: create\n  object: object-remote:x\n  parent: 2\n" ) ) );
    }

    /** MC-04 */
    public function testVersionChecks()
    {
        $this->assertSame( array(), self::errors( "- type: content_version\n  mode: cleanup\n  node: 5\n  keep: 3\n" ) );
        $this->assertStringContainsString( 'version is missing (mode restore)', implode( "\n", self::errors( "- type: content_version\n  mode: restore\n  object: 5\n" ) ) );
        $this->assertStringContainsString( 'name the object by object: or by node:', implode( "\n", self::errors( "- type: content_version\n  mode: cleanup\n" ) ) );
        $this->assertStringContainsString( 'keep: 0 or more', implode( "\n", self::errors( "- type: content_version\n  mode: cleanup\n  object: 5\n  keep: -1\n" ) ) );
        $this->assertStringContainsString( '(content_version).version: only in the mode restore',
                                           implode( "\n", self::errors( "- type: content_version\n  mode: cleanup\n  object: 5\n  version: 2\n" ) ) );
    }

    /** MC-05 */
    public function testPlans()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $plan = function ( $name, array $step ) use ( $registry )
        {
            $type = $registry->get( $name );
            return $type->plan( expMigrationStepSchema::withDefaults( $type->schema(), $step ), null );
        };
        $this->assertSame( array( 'create a "folder" below node 2 with the remote id news (eng-US: name; ger-DE: name)' ),
                           $plan( 'content', array( 'mode' => 'create', 'class' => 'folder', 'parent' => 2, 'remote_id' => 'news', 'language' => 'eng-US',
                                                    'fields' => array( 'name' => 'News' ), 'translations' => array( 'ger-DE' => array( 'name' => 'Nachrichten' ) ) ) ) );
        $this->assertSame( array( 'update the object of node "${ref.news}" (the default language: name), in place, with workflows' ),
                           $plan( 'content', array( 'mode' => 'update', 'node' => '${ref.news}', 'fields' => array( 'name' => 'X' ), 'in_place' => true, 'workflows' => true ) ) );
        $this->assertSame( array( 'delete the object "remote:old" to the trash' ), $plan( 'content', array( 'mode' => 'delete', 'object' => 'remote:old', 'trash' => true ) ) );
        $this->assertSame( array( 'add a location of the object 5 below node "path:media", priority 3, the main location' ),
                           $plan( 'location', array( 'mode' => 'create', 'object' => 5, 'parent' => 'path:media', 'priority' => 3, 'main' => true ) ) );
        $this->assertSame( array( 'hide node 7 and its subtree' ), $plan( 'location', array( 'mode' => 'hide', 'node' => 7 ) ) );
        $this->assertSame( array( 'remove the archived versions of the object 5 but the newest 2, and its drafts' ),
                           $plan( 'content_version', array( 'mode' => 'cleanup', 'object' => 5, 'keep' => 2, 'drafts' => true ) ) );
    }

    /** MC-06 */
    public function testRevertible()
    {
        $content = new expMigrationStepContent();
        $this->assertTrue( $content->revertible( array( 'mode' => 'create', 'remote_id' => 'x' ) ) );
        $this->assertFalse( $content->revertible( array( 'mode' => 'create' ) ) );
        foreach ( array( 'update', 'ensure', 'delete' ) as $mode )
            $this->assertFalse( $content->revertible( array( 'mode' => $mode, 'remote_id' => 'x' ) ), $mode );
        $location = new expMigrationStepLocation();
        foreach ( array( 'create' => true, 'hide' => true, 'unhide' => true, 'update' => false, 'delete' => false, 'move' => false, 'copy' => false ) as $mode => $can )
            $this->assertSame( $can, $location->revertible( array( 'mode' => $mode ) ), $mode );
        $this->assertNotInstanceOf( 'expMigrationRevertibleStepType', new expMigrationStepContentVersion() );

        $definition = function ( $text )
        {
            $errors = array();
            $document = expMigrationYaml::fromText( $text, expMigrationStepRegistry::fromSettings(), $errors );
            $this->assertSame( array(), $errors );
            return new expMigrationYaml( new expMigrationDefinition( '20261102090000_wp10a_test', 'site', 'yml' ), $document, expMigrationStepRegistry::fromSettings() );
        };
        $this->assertTrue( $definition( "- type: content\n  mode: create\n  class: folder\n  parent: 2\n  remote_id: a\n- type: location\n  mode: hide\n  node: 2\n" )->reversible() );
        $this->assertFalse( $definition( "- type: content\n  mode: create\n  class: folder\n  parent: 2\n" )->reversible(), 'no remote id: it cannot be found again' );
        $this->assertFalse( $definition( "- type: content\n  mode: update\n  object: 5\n  fields: { name: X }\n" )->reversible() );
    }

    /** MC-07 */
    public function testIdentifierKinds()
    {
        $this->assertSame( array( 'form' => 'name', 'value' => 'folder' ), expMigrationIdentifier::parse( 'folder', $error, 'class' ) );
        $this->assertSame( array( 'form' => 'remote', 'value' => 'a1' ), expMigrationIdentifier::parse( 'remote:a1', $error, 'class' ) );
        $this->assertSame( array( 'form' => 'id', 'value' => 3 ), expMigrationIdentifier::parse( '3', $error, 'section' ) );
        $this->assertSame( array( 'form' => 'name', 'value' => 'ger-DE' ), expMigrationIdentifier::parse( 'ger-DE', $error, 'language' ) );
        $this->assertSame( array( 'form' => 'name', 'value' => 'ez_lock/locked' ), expMigrationIdentifier::parse( 'ez_lock/locked', $error, 'state' ) );
        $this->assertSame( array( 'form' => 'name', 'value' => 'Anonymous' ), expMigrationIdentifier::parse( 'Anonymous', $error, 'role' ) );
        $this->assertSame( array( 'form' => 'name', 'value' => 'admin' ), expMigrationIdentifier::parse( 'admin', $error, 'user' ) );
        foreach ( array( array( 'section', 'remote:x' ), array( 'class', 'path:x' ), array( 'language', 'german' ), array( 'state', 'locked' ),
                         array( 'node', 'folder' ) ) as $bad )
            $this->assertNotNull( expMigrationIdentifier::problem( $bad[0], $bad[1] ), json_encode( $bad ) );
        $this->assertNull( expMigrationIdentifier::problem( 'section', '${ref.section}' ) );
        $this->assertSame( array( 'node', 'object', 'class', 'section', 'language', 'user', 'role', 'state', 'class_group', 'state_group', 'tag', 'url_alias' ),
                           expMigrationIdentifier::KINDS );
    }

    /**
     * MC-09 every step type of the shipped migration.ini has its generated reference page in English and German, with
     * every key of its schema (exp:help:examples --area=migration-steps, and --language=ger-de)
     */
    public function testReferencePages()
    {
        $root = dirname( __DIR__, 5 );
        foreach ( expMigrationStepRegistry::fromSettings()->types() as $name => $type )
        {
            foreach ( array( "doc/eng-us/examples/migration-steps/$name.md", "doc/ger-de/beispiele/migrationsschritte/$name.md" ) as $page )
            {
                $this->assertFileExists( "$root/$page", "the reference page of the step type $name: ./console exp:help:examples --area=migration-steps" );
                $text = (string)file_get_contents( "$root/$page" );
                $this->assertStringContainsString( "`type: $name`", $text, $page );
                $schema = $type->schema();
                foreach ( array_keys( isset( $schema['keys'] ) ? $schema['keys'] : array() ) as $key )
                    $this->assertStringContainsString( "| `$key` |", $text, "$page lists the key $key: generate it again" );
            }
        }
    }

    /** MC-08 */
    public function testCaches()
    {
        $this->assertSame( array( 'content' ), ( new expMigrationStepContent() )->caches( array( 'mode' => 'create' ) ) );
        $this->assertSame( array( 'content', 'template-block' ), ( new expMigrationStepLocation() )->caches( array( 'mode' => 'move' ) ) );
        $this->assertSame( array( 'content' ), ( new expMigrationStepContentVersion() )->caches( array( 'mode' => 'cleanup' ) ) );
    }
}
