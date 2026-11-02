<?php
/**
 * exp:migration:convert without a database: migrations of the migration bundle of the Symfony based platform as YAML
 * migrations of Exponential.
 *
 *  MV-1 placeholders: "reference:<name>" and "[reference:<name>]" as ${ref.<name>}
 *  MV-2 content create, update and delete with match, attributes as fields and references; every converted step
 *       passes the check of its step type
 *  MV-3 content types with attributes, locations (update split into update, move and hide), roles with policies and
 *       assignments, sections, users, user groups, languages, tags, sql, reference set, trash purge
 *  MV-4 what does not convert is listed, never guessed: unknown step types, unknown modes, keys without a
 *       counterpart, structured field values; a file that is not a list of steps
 *  MV-5 run(): a folder converted, the date of the old name kept, the manual list in the header, written with apply,
 *       never over a file
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationConverterTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir = '';

    protected function setUp(): void
    {
        parent::setUp();
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( 'the YAML library (symfony/yaml) is not installed' );
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== '' && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        parent::tearDown();
    }

    protected function convert( $text )
    {
        return expMigrationConverter::convert( $text, expMigrationStepRegistry::fromSettings() );
    }

    /** MV-1 */
    public function testPlaceholders()
    {
        $this->assertSame( '${ref.news}', expMigrationConverter::placeholders( 'reference:news' ) );
        $this->assertSame( 'Node ${ref.news} of ${ref.site}', expMigrationConverter::placeholders( 'Node [reference:news] of [reference:site]' ) );
        $this->assertSame( array( 'a' => array( '${ref.x}', 3 ) ), expMigrationConverter::placeholders( array( 'a' => array( 'reference:x', 3 ) ) ) );
        $this->assertSame( 'reference: x', expMigrationConverter::placeholders( 'reference: x' ) );
    }

    /** MV-2 */
    public function testContent()
    {
        $r = $this->convert( <<<'YAML'
-
    type: content
    mode: create
    content_type: folder
    parent_location: 2
    remote_id: news
    lang: eng-GB
    attributes:
        name: News
        short_name: { value: N }
    references:
        -
            identifier: news_node
            attribute: location_id
-
    type: content
    mode: update
    match:
        content_remote_id: news
    attributes:
        name: The news
-
    type: content
    mode: delete
    match:
        location_id: reference:news_node
YAML
        );
        $this->assertSame( array(), $r['errors'] );
        $this->assertSame( array(), $r['manual'] );
        $this->assertSame( 3, $r['converted'] );
        $this->assertSame( array( 'type' => 'content', 'mode' => 'create', 'class' => 'folder', 'parent' => 2, 'remote_id' => 'news', 'language' => 'eng-GB',
                                  'fields' => array( 'name' => 'News', 'short_name' => 'N' ), 'references' => array( 'news_node' => 'node_id' ) ), $r['steps'][0] );
        $this->assertSame( array( 'type' => 'content', 'mode' => 'update', 'object' => 'remote:news', 'fields' => array( 'name' => 'The news' ) ), $r['steps'][1] );
        $this->assertSame( array( 'type' => 'content', 'mode' => 'delete', 'node' => '${ref.news_node}' ), $r['steps'][2] );
    }

    /** MV-3 */
    public function testOtherStepTypes()
    {
        $r = $this->convert( <<<'YAML'
- { type: content_type, mode: create, identifier: news_article, content_type_group: Content, name: { eng-GB: News article }, name_pattern: "<title>", is_container: false,
    attributes: [ { identifier: title, type: ezstring, name: Title, required: true, searchable: true, disable-translation: false } ] }
- { type: content_type, mode: delete, match: { identifier: old_article } }
- { type: location, mode: update, match: { location_id: 60 }, priority: 5, parent_location: 2, is_hidden: true }
- { type: location, mode: create, match: { content_id: 70 }, parent_location: [ 2, 43 ] }
- { type: role, mode: create, name: News editor, policies: [ { module: content, function: read, limitations: [ { identifier: Class, values: [ news_article ] } ] } ],
    assign: [ { type: group, ids: [ 12 ], limitations: [ { identifier: Subtree, values: [ /1/2/60/ ] } ] } ] }
- { type: section, mode: create, identifier: news, name: News }
- { type: user, mode: create, username: editor, email: editor@example.com, password: secret, first_name: Ed, last_name: Itor, groups: [ 12 ] }
- { type: user_group, mode: delete, match: { id: 99 } }
- { type: language, mode: create, lang: ger-DE, name: Deutsch }
- { type: sql, mysql: "UPDATE ezsection SET name = 'N' WHERE id = 1", sqlite: "UPDATE ezsection SET name = 'N' WHERE id = 1" }
- { type: reference, mode: set, identifier: news_parent, value: 60 }
- { type: trash, mode: purge }
YAML
        );
        $this->assertSame( array(), $r['errors'] );
        $steps = $r['steps'];
        $this->assertSame( array( 'type' => 'content_class', 'mode' => 'create', 'identifier' => 'news_article', 'names' => array( 'eng-GB' => 'News article' ),
                                  'name_pattern' => '<title>', 'container' => false, 'groups' => array( 'Content' ),
                                  'attributes' => array( array( 'identifier' => 'title', 'type' => 'ezstring', 'required' => true, 'searchable' => true,
                                                                'name' => 'Title', 'translatable' => true ) ) ), $steps[0] );
        $this->assertSame( array( 'type' => 'content_class', 'mode' => 'delete', 'class' => 'old_article' ), $steps[1] );
        $this->assertSame( array( 'type' => 'location', 'mode' => 'update', 'node' => 60, 'priority' => 5 ), $steps[2] );
        $this->assertSame( array( 'type' => 'location', 'mode' => 'move', 'node' => 60, 'parent' => 2 ), $steps[3] );
        $this->assertSame( array( 'type' => 'location', 'mode' => 'hide', 'node' => 60 ), $steps[4] );
        $this->assertSame( 43, $steps[6]['parent'] );
        $this->assertSame( array( 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Class' => array( 'news_article' ) ) ), $steps[7]['policies'][0] );
        $this->assertSame( array( 'type' => 'role', 'mode' => 'assign', 'role' => 'News editor', 'groups' => array( 12 ), 'subtree' => 60 ), $steps[8] );
        $this->assertSame( array( 'type' => 'section', 'mode' => 'create', 'identifier' => 'news', 'name' => 'News' ), $steps[9] );
        $this->assertSame( array( 'first_name' => 'Ed', 'last_name' => 'Itor' ), $steps[10]['fields'] );
        $this->assertSame( 'editor', $steps[10]['login'] );
        $this->assertSame( array( 'type' => 'user_group', 'mode' => 'delete', 'group' => 99 ), $steps[11] );
        $this->assertSame( array( 'type' => 'language', 'mode' => 'create', 'locale' => 'ger-DE', 'name' => 'Deutsch' ), $steps[12] );
        $this->assertArrayHasKey( 'mysql', $steps[13] );
        $this->assertSame( array( 'type' => 'reference', 'set' => array( 'news_parent' => 60 ) ), $steps[14] );
        $this->assertSame( array( 'type' => 'trash', 'mode' => 'purge', 'all' => true ), $steps[15] );
        $this->assertSame( 16, $r['converted'] );
    }

    /** MV-4 */
    public function testWhatDoesNotConvertIsListed()
    {
        $r = $this->convert( <<<'YAML'
- { type: php, class: App\Migration\Something }
- { type: content, mode: load, match: { content_id: 1 } }
- { type: content, mode: create, content_type: folder, parent_location: 2, priority: 4, attributes: { image: { path: a.png, alt: x } }, owner: 14 }
- { type: user, mode: update, match: { email: someone@example.com }, enabled: false }
- not a step
YAML
        );
        $this->assertSame( 1, $r['converted'] );
        $this->assertSame( 4, $r['skipped'] );
        $manual = implode( "\n", $r['manual'] );
        $this->assertStringContainsString( 'step 1 (php): the step type php has no counterpart', $manual );
        $this->assertStringContainsString( 'step 2 (content/load): the mode load does not convert', $manual );
        $this->assertStringContainsString( 'priority: set it with a location', $manual );
        $this->assertStringContainsString( 'the field image has a structured value', $manual );
        $this->assertStringContainsString( 'owner: set it with', $manual );
        $this->assertStringContainsString( 'e-mail address', $manual );
        $this->assertStringContainsString( 'step 5: not a step', $manual );
        $this->assertNotEmpty( $this->convert( "steps: []\n" )['errors'] );
        $this->assertNotEmpty( $this->convert( "- { type: x\n" )['errors'] );
    }

    /** MV-5 */
    public function testRunWritesNewMigrations()
    {
        $this->dir = eZSys::rootDir() . '/var/tmp/expmigrationconvertertest-' . getmypid();
        eZDir::mkdir( $this->dir . '/in', false, true );
        eZDir::mkdir( $this->dir . '/migrations', false, true );
        file_put_contents( $this->dir . '/in/20240115093000_create_news.yml', "- { type: section, mode: create, identifier: news, name: News }\n- { type: mail, to: x }\n" );
        file_put_contents( $this->dir . '/in/readme.txt', 'not a migration' );
        $options = array( 'path' => $this->dir . '/in', 'root' => $this->dir, 'registry' => expMigrationStepRegistry::fromSettings(), 'author' => 'Web team',
                          'directory' => $this->dir . '/migrations' );
        $dry = expMigrationConverter::run( $options );
        $this->assertSame( array(), $dry['errors'] );
        $this->assertCount( 1, $dry['files'] );
        $file = $dry['files'][0];
        $this->assertSame( '20240115093000_create_news', $file['id'] );
        $this->assertSame( 'migrations/20240115093000_create_news.yml', $file['path'] );
        $this->assertFalse( $file['written'] );
        $this->assertStringStartsWith( "# description: Converted from 20240115093000_create_news.yml\n# author: Web team\n", $file['content'] );
        $this->assertStringContainsString( '# - step 2 (mail): the step type mail has no counterpart', $file['content'] );
        $this->assertFileDoesNotExist( $this->dir . '/migrations/20240115093000_create_news.yml' );

        $applied = expMigrationConverter::run( $options + array( 'apply' => true ) );
        $this->assertTrue( $applied['files'][0]['written'], implode( '; ', $applied['errors'] ) );
        $this->assertSame( $file['content'], file_get_contents( $this->dir . '/migrations/20240115093000_create_news.yml' ) );
        $warnings = array();
        $errors = array();
        $this->assertNotNull( expMigrationYaml::fromText( $file['content'], expMigrationStepRegistry::fromSettings(), $errors, $warnings ), implode( '; ', $errors ) );
        $again = expMigrationConverter::run( $options + array( 'apply' => true ) );
        $this->assertNotEmpty( $again['errors'], 'never over a file' );
        $this->assertNotEmpty( expMigrationConverter::run( array( 'path' => $this->dir . '/missing', 'root' => $this->dir ) )['errors'] );
    }
}
