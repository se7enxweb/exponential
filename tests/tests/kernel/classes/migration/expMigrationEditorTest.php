<?php
/**
 * The migration editor of the administration without a database: the step forms made from the step types' schemas,
 * a step from its form and back, adding, changing, moving and removing steps in a text with its header kept, the
 * check through the step types, and saving only inside the migration folders.
 *
 *  ME-1 every registered step type has a form: its keys with type, required, default as text, modes, structured
 *  ME-2 a step from a form: numbers, true/false, YAML lists and maps, the mode, keys of another mode left out,
 *       references, environments, engines, comment; a text that is no YAML is a problem; the form of a step gives the
 *       same step back
 *  ME-3 a step added, changed, moved and removed; the header lines stay; a step of down
 *  ME-4 the check: a step that its type refuses, an unknown step type, a missing description warned
 *  ME-5 saving: a new migration into the folder (never over a file), an existing file in the folder, an applied one
 *       refused, a text that does not pass refused; find() only inside the searched folders; the view's step target
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationEditorTest extends PHPUnit\Framework\TestCase
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

    protected function registry()
    {
        return expMigrationStepRegistry::fromSettings();
    }

    /** ME-1 */
    public function testEveryStepTypeHasAForm()
    {
        $registry = $this->registry();
        $types = expMigrationEditor::stepTypes( $registry );
        $this->assertSame( $registry->names(), array_keys( $types ) );
        $this->assertArrayHasKey( 'content', $types );
        $content = $types['content'];
        $this->assertContains( 'create', $content['modes'] );
        $keys = array_column( $content['keys'], null, 'name' );
        $this->assertSame( 'map', $keys['fields']['type'] );
        $this->assertTrue( $keys['fields']['structured'] );
        $this->assertSame( 'fail', $keys['missing']['default'] );
        $this->assertSame( array( 'fail', 'skip' ), $keys['missing']['values'] );
        $this->assertSame( 'node', $keys['parent']['kind'] );
        $this->assertContains( 'object_id', $content['values'] );
        $this->assertSame( 'false', expMigrationEditor::text( false ) );
        $this->assertSame( '[ a, b ]', expMigrationEditor::text( array( 'a', 'b' ) ) );
    }

    /** ME-2 */
    public function testStepFromFormAndBack()
    {
        $schema = $this->registry()->get( 'content' )->schema();
        $errors = array();
        $step = expMigrationEditor::stepFromForm( 'content', $schema, array(
            'mode' => 'create',
            'keys' => array( 'class' => 'folder', 'parent' => '2', 'fields' => '{ name: News, short_name: N }', 'always_available' => 'true',
                             'object' => '55', 'remote_id' => ' ', 'language' => 'eng-GB' ),
            'references' => 'news=node_id, news_object=object_id', 'environments' => 'staging, production', 'comment' => "The news\nfolder",
        ), $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( 'type' => 'content', 'mode' => 'create', 'class' => 'folder', 'parent' => 2, 'language' => 'eng-GB',
                                  'fields' => array( 'name' => 'News', 'short_name' => 'N' ), 'always_available' => true,
                                  'environments' => array( 'staging', 'production' ), 'references' => array( 'news' => 'node_id', 'news_object' => 'object_id' ),
                                  'comment' => 'The news folder' ), $step, 'object is a key of update and delete only, so it is left out' );

        $form = expMigrationEditor::formFromStep( $schema, $step );
        $this->assertSame( 'create', $form['mode'] );
        $this->assertSame( '{ name: News, short_name: "N" }', $form['keys']['fields'] );
        $this->assertSame( 'news=node_id, news_object=object_id', $form['references'] );
        $again = expMigrationEditor::stepFromForm( 'content', $schema, $form, $errors );
        $this->assertSame( $step, $again );

        $errors = array();
        expMigrationEditor::stepFromForm( 'content', $schema, array( 'mode' => 'create', 'keys' => array( 'fields' => '{ name: [', 'always_available' => 'maybe' ),
                                                                     'references' => 'broken' ), $errors );
        $this->assertCount( 3, $errors );
        $this->assertStringStartsWith( 'fields: not YAML', $errors[0] );

        $unknown = expMigrationEditor::formFromStep( $schema, array( 'type' => 'content', 'mode' => 'update', 'object' => 5, 'colour' => 'red' ) );
        $this->assertSame( array( 'colour' ), $unknown['unknown'] );
    }

    /** ME-3 */
    public function testStepsInAText()
    {
        $text = expMigrationEditor::newText( 'The news folder', 'Web team' );
        $this->assertSame( "# description: The news folder\n# author: Web team\nsteps: []\n", $text );
        $errors = array();
        $one = array( 'type' => 'reference', 'set' => array( 'a' => 1 ) );
        $two = array( 'type' => 'reference', 'set' => array( 'b' => 2 ) );
        $text = expMigrationEditor::withStep( $text, $one, null, 'steps', $errors );
        $text = expMigrationEditor::withStep( $text, $two, null, 'steps', $errors );
        $this->assertSame( array(), $errors );
        $this->assertStringStartsWith( "# description: The news folder\n# author: Web team\nsteps:\n", $text );
        $document = expMigrationEditor::document( $text, $errors );
        $this->assertSame( array( $one, $two ), $document['steps'] );

        $text = expMigrationEditor::changeStep( $text, 1, 'up', 'steps', $errors );
        $this->assertSame( array( $two, $one ), expMigrationEditor::document( $text, $errors )['steps'] );
        $changed = array( 'type' => 'reference', 'set' => array( 'c' => 3 ) );
        $text = expMigrationEditor::withStep( $text, $changed, 0, 'steps', $errors );
        $text = expMigrationEditor::changeStep( $text, 1, 'remove', 'steps', $errors );
        $text = expMigrationEditor::withStep( $text, $one, null, 'down', $errors );
        $this->assertSame( array(), $errors );
        $document = expMigrationEditor::document( $text, $errors );
        $this->assertSame( array( $changed ), $document['steps'] );
        $this->assertSame( array( $one ), $document['down'] );
        $this->assertSame( array( 'description: The news folder', 'author: Web team' ), expMigrationEditor::split( $text )['header'] );

        $this->assertNull( expMigrationEditor::changeStep( $text, 7, 'remove', 'steps', $errors ) );
        $this->assertNotEmpty( $errors );
        $errors = array();
        $this->assertNull( expMigrationEditor::withStep( "steps: [\n", $one, null, 'steps', $errors ) );
        $this->assertNotEmpty( $errors );
    }

    /** ME-4 */
    public function testCheckThroughTheStepTypes()
    {
        $registry = $this->registry();
        $good = expMigrationEditor::check( "# description: x\nsteps:\n    - type: reference\n      set: { a: 1 }\n", $registry );
        $this->assertTrue( $good['ok'], implode( '; ', $good['errors'] ) );
        $this->assertSame( 'reference', $good['steps'][0]['type'] );
        $this->assertTrue( $good['steps'][0]['form'] );
        $this->assertSame( 'set: {"a":1}', $good['steps'][0]['summary'] );

        $bad = expMigrationEditor::check( "steps:\n    - type: content\n      mode: create\n    - type: no_such_type\n", $registry );
        $this->assertFalse( $bad['ok'] );
        $this->assertNotEmpty( preg_grep( '/class is missing|parent is missing/', $bad['errors'] ) );
        $this->assertNotEmpty( preg_grep( '/unknown step type no_such_type/', $bad['errors'] ) );
        $this->assertNotEmpty( preg_grep( '/no description/', $bad['warnings'] ) );
        $this->assertFalse( $bad['steps'][1]['form'] );
    }

    /** ME-5 */
    public function testSaveOnlyInsideTheFolders()
    {
        $this->dir = eZSys::rootDir() . '/var/tmp/expmigrationeditortest-' . getmypid();
        $folder = $this->dir . '/migrations';
        eZDir::mkdir( $folder, false, true );
        $registry = $this->registry();
        $text = expMigrationEditor::newText( 'A folder' );
        $text = expMigrationEditor::withStep( $text, array( 'type' => 'reference', 'set' => array( 'a' => 1 ) ) );
        $time = mktime( 12, 0, 0, 11, 2, 2026 );
        $saved = expMigrationEditor::save( $text, array( 'name' => 'News folder', 'directory' => $folder, 'registry' => $registry, 'root' => $this->dir, 'time' => $time ) );
        $this->assertTrue( $saved['ok'], implode( '; ', $saved['errors'] ) );
        $this->assertSame( '20261102120000_news_folder', $saved['id'] );
        $this->assertSame( 'migrations/20261102120000_news_folder.yml', $saved['path'] );
        $this->assertSame( $text, file_get_contents( "$folder/20261102120000_news_folder.yml" ) );
        $again = expMigrationEditor::save( $text, array( 'name' => 'News folder', 'directory' => $folder, 'registry' => $registry, 'root' => $this->dir, 'time' => $time ) );
        $this->assertFalse( $again['ok'], 'never over a file' );

        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $folder ) ) );
        $found = expMigrationEditor::find( $locator, '20261102120000_news_folder' );
        $this->assertNotNull( $found );
        $this->assertSame( realpath( "$folder/20261102120000_news_folder.yml" ), $found['file'] );
        $this->assertNull( expMigrationEditor::find( $locator, '../20261102120000_news_folder' ) );
        $this->assertNull( expMigrationEditor::find( $locator, '20261102120001_missing' ) );
        $this->assertFalse( expMigrationEditor::inside( $this->dir . '/other/x.yml', array( $folder . '/' ) ) );
        $this->assertFalse( expMigrationEditor::inside( $folder . '/sub/x.yml', array( $folder . '/' ) ) );

        $changed = expMigrationEditor::withStep( $text, array( 'type' => 'reference', 'set' => array( 'b' => 2 ) ) );
        $ok = expMigrationEditor::save( $changed, array( 'file' => $found['file'], 'registry' => $registry, 'root' => $this->dir ) );
        $this->assertTrue( $ok['ok'], implode( '; ', $ok['errors'] ) );
        $this->assertSame( $changed, file_get_contents( $found['file'] ) );
        $refused = expMigrationEditor::save( $text, array( 'file' => $found['file'], 'applied' => true, 'registry' => $registry, 'root' => $this->dir ) );
        $this->assertFalse( $refused['ok'] );
        $this->assertStringContainsString( 'has run already', $refused['errors'][0] );
        $invalid = expMigrationEditor::save( "steps:\n    - type: no_such_type\n", array( 'file' => $found['file'], 'registry' => $registry, 'root' => $this->dir ) );
        $this->assertFalse( $invalid['ok'] );
        $this->assertSame( $changed, file_get_contents( $found['file'] ), 'a text that does not pass is not written' );

        $files = expMigrationEditor::files( $locator, array( 'site/20261102120000_news_folder' => true ), $this->dir );
        $this->assertSame( 'migrations/20261102120000_news_folder.yml', $files[0]['path'] );
        $this->assertTrue( $files[0]['applied'] );
        $this->assertFalse( $files[0]['editable'] );

        $this->assertSame( array( 'list' => 'down', 'index' => 3 ), \Exponential\View\Kernel\Migration\Editor::stepTarget( 'down:3' ) );
        $this->assertNull( \Exponential\View\Kernel\Migration\Editor::stepTarget( 'steps:x' ) );
        $this->assertNull( \Exponential\View\Kernel\Migration\Editor::stepTarget( 'other:1' ) );
    }
}
