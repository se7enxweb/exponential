<?php
/**
 * The step types of an installation's structure without a database: content_class, content_class_group, user,
 * user_group, role, section, language and object_state_group. Their schemas, the checks of their steps, their plans
 * and which steps undo themselves.
 *
 *  MS-01 the kernel registers the eight types from migration.ini; their schemas are valid, their examples valid steps
 *  MS-02 content_class: the keys each mode needs; attributes checked (identifier, datatype, flags, setting columns);
 *        the datatype of update_attributes refused; groups as class group identifiers
 *  MS-03 role: policies (module, function, limitation maps), assignments (users or groups, subtree or section)
 *  MS-04 user and user_group: login, e-mail, groups, password or hash; the group as an object
 *  MS-05 section, language, object_state_group and content_class_group: the keys of each mode, identifiers
 *  MS-06 plans name what would change and never the password
 *  MS-07 which steps undo themselves: creates, role assign and unassign; updates, deletes and section assign do not
 *  MS-08 identifiers of the kinds class_group and state_group
 *  MS-09 the caches the steps touch
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationStructureStepsTest extends PHPUnit\Framework\TestCase
{
    const TYPES = array( 'content_class' => 'expMigrationStepContentClass', 'content_class_group' => 'expMigrationStepContentClassGroup',
                         'user' => 'expMigrationStepUser', 'user_group' => 'expMigrationStepUserGroup', 'role' => 'expMigrationStepRole',
                         'section' => 'expMigrationStepSection', 'language' => 'expMigrationStepLanguage',
                         'object_state_group' => 'expMigrationStepObjectStateGroup' );

    /**
     * @param string $yaml
     * @return string the errors of the steps, one per line
     */
    private static function errors( $yaml )
    {
        $errors = array();
        expMigrationYaml::fromText( $yaml, expMigrationStepRegistry::fromSettings(), $errors );
        return implode( "\n", $errors );
    }

    /**
     * @param string $name
     * @param array $step
     * @return string[]
     */
    private static function plan( $name, array $step )
    {
        $type = expMigrationStepRegistry::fromSettings()->get( $name );
        return $type->plan( expMigrationStepSchema::withDefaults( $type->schema(), $step ), null );
    }

    protected function setUp(): void
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        expMigrationStepRegistry::forget();
    }

    /** MS-01 */
    public function testRegistered()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array(), $registry->errors() );
        foreach ( self::TYPES as $name => $class )
        {
            $type = $registry->get( $name );
            $this->assertInstanceOf( $class, $type, $name );
            $schema = $type->schema();
            $this->assertSame( array(), expMigrationStepSchema::problems( $schema ), $name );
            $this->assertNotEmpty( $schema['values'], "$name gives values to references:" );
            $this->assertSame( '', self::errors( $schema['example'] ), "the example of $name is a valid step" );
            $this->assertInstanceOf( 'expMigrationPartlyRevertibleStepType', $type, "$name says which of its steps undo themselves" );
        }
    }

    /** MS-02 */
    public function testContentClassChecks()
    {
        $this->assertSame( '', self::errors( "- type: content_class\n  mode: create\n  identifier: news\n  name: News\n  groups: [ Content ]\n"
                                             . "  attributes:\n    - { identifier: title, type: ezstring, name: Title, names: { ger-DE: Titel }, required: true, settings: { data_int1: 255 } }\n" ) );
        $errors = self::errors( "- type: content_class\n  mode: create\n  name: News\n" );
        $this->assertStringContainsString( 'steps[0] (content_class): identifier is missing (mode create)', $errors );
        $errors = self::errors( "- type: content_class\n  mode: create\n  identifier: News-Class\n  names: { german: Nachrichten }\n"
                                . "  attributes:\n    - { identifier: Title, name: Title, required: yes-please, settings: { data_int9: 1 }, colour: red }\n"
                                . "    - { identifier: body, type: ezxmltext }\n    - { identifier: body, type: eztext }\n" );
        foreach ( array( 'identifier: "News-Class" is not a class identifier', 'names: german is not a language code',
                         'attributes[0]: identifier is missing or not an attribute identifier', 'attributes[0]: type (the datatype, e.g. ezstring) is missing',
                         'attributes[0].required: true or false', 'attributes[0].settings: data_int9 is not a setting column',
                         'attributes[0]: the unknown key colour', 'attributes[2]: the attribute body is there twice' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $errors = self::errors( "- type: content_class\n  mode: update\n  class: news\n  update_attributes:\n    title: { type: eztext }\n  remove_attributes: [ Body ]\n" );
        $this->assertStringContainsString( 'update_attributes.title: the datatype of an attribute cannot be changed', $errors );
        $this->assertStringContainsString( 'remove_attributes[0]: an attribute identifier', $errors );
        $this->assertStringContainsString( 'nothing to update', self::errors( "- type: content_class\n  mode: update\n  class: news\n" ) );
        $this->assertStringContainsString( 'steps[0] (content_class).attributes: only in the mode create, ensure',
                                           self::errors( "- type: content_class\n  mode: update\n  class: news\n  attributes: []\n" ) );
        $this->assertStringContainsString( 'groups[0]: "a:b" is no identifier of a class_group',
                                           self::errors( "- type: content_class\n  mode: create\n  identifier: news\n  groups: [ 'a:b' ]\n" ) );
        $this->assertStringContainsString( '.sort_order: "up" is not one of',
                                           self::errors( "- type: content_class\n  mode: create\n  identifier: news\n  sort_order: up\n" ) );
    }

    /** MS-03 */
    public function testRoleChecks()
    {
        $this->assertSame( '', self::errors( "- type: role\n  mode: create\n  name: Editors\n  policies:\n    - { module: content, function: read }\n"
                                             . "    - { module: content, function: edit, limitations: { Class: [ article ], Subtree: [ 'path:news' ] } }\n    - { module: '*', function: '*' }\n" ) );
        $errors = self::errors( "- type: role\n  mode: create\n  policies:\n    - { module: Content, limitations: [ Class ] }\n    - [ content, read ]\n" );
        foreach ( array( 'name is missing (mode create)', 'policies[0]: module is missing', 'policies[0]: function is missing',
                         'policies[0].limitations: a map of limitation => list of values', 'policies[1]: a policy is a map' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'remove_policies[0]: the unknown key limitations',
                                           self::errors( "- type: role\n  mode: update\n  role: Editors\n  remove_policies: [ { module: content, limitations: { Class: [ 1 ] } } ]\n" ) );
        $errors = self::errors( "- type: role\n  mode: assign\n  role: Editors\n  subtree: 2\n  section: standard\n" );
        $this->assertStringContainsString( 'users: or groups: is missing (mode assign)', $errors );
        $this->assertStringContainsString( 'an assignment is limited by subtree: or by section:, not both', $errors );
        $this->assertStringContainsString( 'groups[0]: object-remote: names a node',
                                           self::errors( "- type: role\n  mode: assign\n  role: Editors\n  groups: [ 'object-remote:x' ]\n" ) );
        $this->assertSame( '', self::errors( "- type: role\n  mode: unassign\n  role: 3\n  users: [ admin, 14 ]\n  groups: [ 'remote:editors' ]\n  section: standard\n" ) );
    }

    /** MS-04 */
    public function testUserChecks()
    {
        $this->assertSame( '', self::errors( "- type: user\n  mode: create\n  login: editor\n  email: editor@example.com\n  password: '\${ref.secret}'\n  groups: [ 12 ]\n"
                                             . "  fields: { first_name: A, last_name: B }\n" ) );
        $errors = self::errors( "- type: user\n  mode: create\n  login: 'a|b'\n  email: nobody\n  password: x\n  password_hash: y\n" );
        foreach ( array( 'login: a login has no |', 'email: "nobody" is not an e-mail address', 'password: and password_hash: exclude each other',
                         'password_hash: and password_hash_type: go together', 'groups is missing' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'nothing to update', self::errors( "- type: user\n  mode: update\n  user: editor\n" ) );
        $this->assertStringContainsString( 'steps[0] (user).login: only in the mode create, ensure',
                                           self::errors( "- type: user\n  mode: update\n  user: editor\n  login: other\n  enabled: false\n" ) );
        $this->assertSame( '', self::errors( "- type: user_group\n  mode: ensure\n  parent: 5\n  remote_id: editors\n  name: Editors\n  names: { ger-DE: Redaktion }\n" ) );
        $this->assertStringContainsString( 'name is missing (mode create)', self::errors( "- type: user_group\n  mode: create\n  parent: 5\n" ) );
        $this->assertStringContainsString( 'group is missing', self::errors( "- type: user_group\n  mode: delete\n" ) );
        $this->assertSame( array( 'mode' => 'create', 'parent' => 5, 'class' => 'user_group', 'fields' => array( 'name' => 'Editors', 'description' => 'They edit' ),
                                  'translations' => array( 'ger-DE' => array( 'name' => 'Redaktion' ) ) ),
                           expMigrationStepUserGroup::contentStep( array( 'mode' => 'create', 'parent' => 5, 'name' => 'Editors',
                                                                          'description' => 'They edit', 'names' => array( 'ger-DE' => 'Redaktion' ) ) ) );
        $this->assertSame( array( 'mode' => 'delete', 'object' => 'remote:editors', 'trash' => false ),
                           expMigrationStepUserGroup::contentStep( array( 'mode' => 'delete', 'group' => 'remote:editors' ) ) );
    }

    /** MS-05 */
    public function testOtherChecks()
    {
        $this->assertSame( '', self::errors( "- type: section\n  mode: ensure\n  identifier: news\n  name: News\n- type: section\n  mode: assign\n  section: news\n  node: 'path:news'\n  scope: inherited\n" ) );
        $errors = self::errors( "- type: section\n  mode: assign\n  section: news\n  node: 2\n  object: 3\n  scope: everything\n" );
        $this->assertStringContainsString( 'name what gets the section by node: or by object: (one of them)', $errors );
        $this->assertStringContainsString( '.scope: "everything" is not one of', $errors );
        $this->assertStringContainsString( 'identifier: "news section" is not a section identifier', self::errors( "- type: section\n  mode: create\n  identifier: news section\n  name: N\n" ) );
        $this->assertSame( '', self::errors( "- type: language\n  mode: ensure\n  locale: ger-DE\n  name: Deutsch\n" ) );
        $this->assertStringContainsString( 'locale: "german" is not a locale', self::errors( "- type: language\n  mode: create\n  locale: german\n" ) );
        $this->assertStringContainsString( 'nothing to update: name is missing', self::errors( "- type: language\n  mode: update\n  language: ger-DE\n" ) );
        $this->assertSame( '', self::errors( "- type: object_state_group\n  mode: create\n  identifier: review\n  name: Review\n"
                                             . "  states: [ { identifier: draft, name: Draft }, { identifier: done, names: { ger-DE: Fertig } } ]\n" ) );
        $errors = self::errors( "- type: object_state_group\n  mode: create\n  identifier: ez_mine\n  states: [ { identifier: Draft }, { name: X, colour: red } ]\n" );
        foreach ( array( 'identifier: identifiers starting with "ez" are the kernel\'s', 'name is missing: a group has a name',
                         'states[0].identifier: "Draft" is not a state identifier', 'states[0]: name is missing',
                         'states[1]: the unknown key colour', 'states[1]: identifier is missing' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertSame( '', self::errors( "- type: content_class_group\n  mode: ensure\n  name: News\n- type: content_class_group\n  mode: update\n  group: News\n  name: Media\n" ) );
        $this->assertStringContainsString( 'group is missing (mode delete)', self::errors( "- type: content_class_group\n  mode: delete\n" ) );
    }

    /** MS-06 */
    public function testPlans()
    {
        $this->assertSame( array( 'create the class news named "News", ger-DE: "Nachrichten"', 'with the attributes title (ezstring), body (ezxmltext)' ),
                           self::plan( 'content_class', array( 'mode' => 'create', 'identifier' => 'news', 'name' => 'News', 'names' => array( 'ger-DE' => 'Nachrichten' ),
                                                               'attributes' => array( array( 'identifier' => 'title', 'type' => 'ezstring' ),
                                                                                      array( 'identifier' => 'body', 'type' => 'ezxmltext' ) ) ) ) );
        $this->assertSame( array( 'update the class "news": name_pattern', 'add to the class "news" the attributes intro (eztext)',
                                  'change the attribute title of the class "news": names', 'remove from the class "news" the attributes old with their data in every object' ),
                           self::plan( 'content_class', array( 'mode' => 'update', 'class' => 'news', 'name_pattern' => '<title>',
                                                               'add_attributes' => array( array( 'identifier' => 'intro', 'type' => 'eztext' ) ),
                                                               'update_attributes' => array( 'title' => array( 'names' => array( 'ger-DE' => 'Titel' ) ) ),
                                                               'remove_attributes' => array( 'old' ) ) ) );
        $this->assertSame( array( 'delete the class "news" (only when it has no objects)' ), self::plan( 'content_class', array( 'mode' => 'delete', 'class' => 'news' ) ) );
        $this->assertSame( array( 'create the role "Editors" with the policies content/read, content/edit (Class, Subtree)' ),
                           self::plan( 'role', array( 'mode' => 'create', 'name' => 'Editors', 'policies' => array(
                               array( 'module' => 'content', 'function' => 'read' ),
                               array( 'module' => 'content', 'function' => 'edit', 'limitations' => array( 'Class' => array( 'article' ), 'Subtree' => array( 2 ) ) ) ) ) ) );
        $this->assertSame( array( 'assign the role "Editors" to the user "editor", the object "remote:editors" in the subtree of node "path:news"' ),
                           self::plan( 'role', array( 'mode' => 'assign', 'role' => 'Editors', 'users' => array( 'editor' ), 'groups' => array( 'remote:editors' ), 'subtree' => 'path:news' ) ) );
        $plan = self::plan( 'user', array( 'mode' => 'update', 'user' => 'editor', 'password' => 'Secret-123', 'email' => 'e@example.com', 'groups' => array( 12 ) ) );
        $this->assertSame( array( 'update the user "editor": email, the password', 'add the user "editor" to the group(s) 12' ), $plan );
        $this->assertStringNotContainsString( 'Secret-123', implode( "\n", $plan ), 'a plan never shows the password' );
        $this->assertSame( array( 'create a user group below node 5 with the remote id editors (the default language: name)' ),
                           self::plan( 'user_group', array( 'mode' => 'create', 'parent' => 5, 'remote_id' => 'editors', 'name' => 'Editors' ) ) );
        $this->assertSame( array( 'assign the section "news" to node "path:news" (subtree)' ), self::plan( 'section', array( 'mode' => 'assign', 'section' => 'news', 'node' => 'path:news' ) ) );
        $this->assertSame( array( 'ensure the language ger-DE "Deutsch": add it when missing' ), self::plan( 'language', array( 'mode' => 'ensure', 'locale' => 'ger-DE', 'name' => 'Deutsch' ) ) );
        $this->assertSame( array( 'create the object state group review named "Review" with the states draft, done' ),
                           self::plan( 'object_state_group', array( 'mode' => 'create', 'identifier' => 'review', 'name' => 'Review',
                                                                    'states' => array( array( 'identifier' => 'draft' ), array( 'identifier' => 'done' ) ) ) ) );
        $this->assertSame( array( 'rename the class group "News" to "Media"' ), self::plan( 'content_class_group', array( 'mode' => 'update', 'group' => 'News', 'name' => 'Media' ) ) );
    }

    /** MS-07 */
    public function testRevertible()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $cases = array(
            array( 'content_class', array( 'mode' => 'create', 'identifier' => 'x' ), true ),
            array( 'content_class', array( 'mode' => 'update', 'class' => 'x', 'name' => 'X' ), false ),
            array( 'content_class', array( 'mode' => 'ensure', 'identifier' => 'x' ), false ),
            array( 'content_class_group', array( 'mode' => 'create', 'name' => 'X' ), true ),
            array( 'content_class_group', array( 'mode' => 'delete', 'group' => 'X' ), false ),
            array( 'user', array( 'mode' => 'create', 'login' => 'x' ), true ),
            array( 'user', array( 'mode' => 'delete', 'user' => 'x' ), false ),
            array( 'user_group', array( 'mode' => 'create', 'parent' => 5, 'remote_id' => 'x', 'name' => 'X' ), true ),
            array( 'user_group', array( 'mode' => 'create', 'parent' => 5, 'name' => 'X' ), false ),
            array( 'role', array( 'mode' => 'create', 'name' => 'X' ), true ),
            array( 'role', array( 'mode' => 'assign', 'role' => 'X', 'users' => array( 'a' ) ), true ),
            array( 'role', array( 'mode' => 'unassign', 'role' => 'X', 'users' => array( 'a' ) ), true ),
            array( 'role', array( 'mode' => 'update', 'role' => 'X', 'name' => 'Y' ), false ),
            array( 'section', array( 'mode' => 'create', 'identifier' => 'x', 'name' => 'X' ), true ),
            array( 'section', array( 'mode' => 'assign', 'section' => 'x', 'node' => 2 ), false ),
            array( 'language', array( 'mode' => 'create', 'locale' => 'ger-DE' ), true ),
            array( 'language', array( 'mode' => 'delete', 'language' => 'ger-DE' ), false ),
            array( 'object_state_group', array( 'mode' => 'create', 'identifier' => 'x', 'name' => 'X' ), true ),
            array( 'object_state_group', array( 'mode' => 'update', 'group' => 'x', 'name' => 'Y' ), false ),
        );
        foreach ( $cases as $case )
            $this->assertSame( $case[2], $registry->get( $case[0] )->revertible( $case[1] ), $case[0] . ' ' . $case[1]['mode'] );
        $errors = array();
        $migration = expMigrationYaml::fromText( "steps:\n  - { type: content_class_group, mode: create, name: X }\n  - { type: role, mode: create, name: X }\n",
                                                 $registry, $errors );
        $this->assertSame( array(), $errors );
    }

    /** MS-08 */
    public function testIdentifierKinds()
    {
        $this->assertContains( 'class_group', expMigrationIdentifier::KINDS );
        $this->assertContains( 'state_group', expMigrationIdentifier::KINDS );
        $this->assertNull( expMigrationIdentifier::problem( 'class_group', 'Content' ) );
        $this->assertNull( expMigrationIdentifier::problem( 'class_group', 'Media and files' ) );
        $this->assertNull( expMigrationIdentifier::problem( 'class_group', 3 ) );
        $this->assertNotNull( expMigrationIdentifier::problem( 'class_group', 'remote:x' ) );
        $this->assertNull( expMigrationIdentifier::problem( 'state_group', 'ez_lock' ) );
        $this->assertNotNull( expMigrationIdentifier::problem( 'state_group', 'ez lock' ) );
        $this->assertSame( array( 'form' => 'name', 'value' => 'ez_lock' ), expMigrationIdentifier::parse( 'ez_lock', $error, 'state_group' ) );
    }

    /** MS-09 */
    public function testCaches()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array( 'classid', 'content', 'sortkey' ), $registry->get( 'content_class' )->caches( array( 'mode' => 'create' ) ) );
        $this->assertSame( array( 'user_info_cache', 'content' ), $registry->get( 'role' )->caches( array( 'mode' => 'assign' ) ) );
        $this->assertSame( array( 'content_language', 'content' ), $registry->get( 'language' )->caches( array( 'mode' => 'create' ) ) );
        $this->assertContains( 'state_limitations', $registry->get( 'object_state_group' )->caches( array( 'mode' => 'create' ) ) );
        $this->assertContains( 'user_info_cache', $registry->get( 'user' )->caches( array( 'mode' => 'create' ) ) );
    }
}
