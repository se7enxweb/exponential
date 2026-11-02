<?php
/**
 * The migration generators without a database: the YAML writer, recorded steps as the steps of the step types, the
 * skeletons of every kind, the secrets of a user step, and a generated migration written to a folder.
 *
 *  MG-1 the YAML writer: plain and quoted texts, numbers, booleans, null, flow and block style, texts of several
 *       lines; what it writes is read back as the same value (when the YAML library is there)
 *  MG-2 recorded steps of every kind as steps of the step types, which pass the check of the step types: content with
 *       its translations and further locations, location moves, sorts, priorities, class, role (assign, ensure),
 *       user by secrets, section, state group, URL alias, settings, trash; "${" written as "$${"
 *  MG-3 what no step type repeats is manual, with the reason: a swap, a workflow trigger, settings of an extension, a
 *       secret setting, a removed global address; ensure turns a create into an ensure
 *  MG-4 skeletons: yml, graphql, ini, sql of one engine, mongodb.json; --engine without sql, an unknown engine
 *  MG-5 the user step's secrets: login and e-mail by secrets satisfy create; a key and its secret, an unknown key
 *  MG-6 a migration of events: the file (header, the manual list), written with apply, never over a file; a step type
 *       missing writes nothing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationGeneratorTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir = '';

    protected function tearDown(): void
    {
        if ( $this->dir !== '' && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        parent::tearDown();
    }

    /** MG-1 */
    public function testYamlWriter()
    {
        $this->assertSame( 'news_article', expMigrationYamlWriter::scalar( 'news_article' ) );
        $this->assertSame( 'eng-GB', expMigrationYamlWriter::scalar( 'eng-GB' ) );
        foreach ( array( 'true', 'No', 'null', '~', '12', '1.5', '', ' x', 'a: b', 'a #b', 'remote:abc', '${ref.x}', '-x', "a\"b", 'Grüße' ) as $text )
            $this->assertStringStartsWith( '"', expMigrationYamlWriter::scalar( $text ), "quoted: $text" );
        $this->assertSame( '"Grüße"', expMigrationYamlWriter::scalar( 'Grüße' ) );
        $this->assertSame( '[ 1, 2.5, true, null, x ]', expMigrationYamlWriter::inline( array( 1, 2.5, true, null, 'x' ) ) );
        $this->assertSame( '{ a: 1, "b c": "d e" }', expMigrationYamlWriter::inline( array( 'a' => 1, 'b c' => 'd e' ) ) );
        $this->assertSame( '3.0', expMigrationYamlWriter::inline( 3.0 ) );

        $value = array( 'steps' => array(
            array( 'type' => 'content', 'mode' => 'create', 'fields' => array( 'title' => 'Hello: world', 'body' => "<p>one</p>\n<p>two</p>", 'count' => 3 ),
                   'translations' => array( 'ger-DE' => array( 'title' => 'Hallo' ) ), 'empty' => array() ),
            array( 'type' => 'role', 'mode' => 'ensure', 'policies' => array( array( 'module' => 'content', 'function' => 'read',
                   'limitations' => array( 'Class' => array( 'article' ), 'Subtree' => array( 'remote:abc' ) ) ) ) ),
        ) );
        $text = expMigrationYamlWriter::document( $value, array( 'description: A test', 'author: Me' ) );
        $this->assertStringStartsWith( "# description: A test\n# author: Me\nsteps:\n    - type: content\n", $text );
        $this->assertStringContainsString( "body: |-\n", $text );
        if ( !expMigrationYamlParser::available() )
            return;
        $errors = array();
        $this->assertSame( $value, expMigrationYamlParser::parse( $text, $errors ), implode( '; ', $errors ) . "\n$text" );
        $odd = array( 'a' => array( "trailing \nspace", "\ttab\nx", "x\n\n", "line\n", '"q"', "back\\slash", 'é' ) );
        $this->assertSame( $odd, expMigrationYamlParser::parse( expMigrationYamlWriter::document( $odd ), $errors ) );
    }

    /** The plan entries of every kind of recorded step. */
    protected static function plan()
    {
        $entry = function ( array $step, array $audit = array(), array $notes = array(), array $omitted = array() )
        {
            return array( 'subject' => $step['type'] . ' x', 'step' => $step, 'audit' => $audit, 'notes' => $notes, 'omitted' => $omitted );
        };
        return array(
            'content' => $entry( array( 'type' => 'content', 'mode' => 'create', 'class' => 'folder', 'parent' => 'remote:root', 'remote_id' => 'news',
                                        'section' => 'standard', 'always_available' => true, 'initial_language' => 'eng-GB', 'states' => array( 'review' => 'draft' ),
                                        'fields' => array( 'ger-DE' => array( 'name' => 'Nachrichten' ), 'eng-GB' => array( 'name' => 'News ${not a ref}' ) ),
                                        'locations' => array( array( 'parent' => 'remote:other', 'remote_id' => 'loc2' ) ) ),
                                 array( 'content.object.create' ), array( 'a note' ) ),
            'update' => $entry( array( 'type' => 'content', 'mode' => 'update', 'match' => 'remote:news', 'fields' => array( 'eng-GB' => array( 'name' => 'N' ) ),
                                       'remove_translations' => array( 'fre-FR' ) ) ),
            'node' => $entry( array( 'type' => 'location', 'mode' => 'create', 'object' => 'remote:news', 'parent' => 'remote:p', 'remote_id' => 'l3' ) ),
            'move' => $entry( array( 'type' => 'location', 'mode' => 'move', 'match' => 'remote:l3', 'parent' => 'remote:q' ) ),
            'hide' => $entry( array( 'type' => 'location', 'mode' => 'hide', 'match' => 'remote:l3' ) ),
            'reveal' => $entry( array( 'type' => 'location', 'mode' => 'reveal', 'match' => 'remote:l3' ) ),
            'sort' => $entry( array( 'type' => 'location', 'mode' => 'update', 'match' => 'remote:l3', 'sort_field' => 'name', 'sort_order' => 'desc',
                                     'child_priorities' => array( 'remote:c1' => 10, 'remote:c2' => 20 ) ) ),
            'copy' => $entry( array( 'type' => 'content', 'mode' => 'copy', 'node' => 'remote:l3', 'parent' => 'remote:q', 'remote_id' => 'x', 'location_remote_id' => 'y' ) ),
            'trash' => $entry( array( 'type' => 'trash', 'mode' => 'move', 'match' => 'remote:l3' ) ),
            'restore' => $entry( array( 'type' => 'trash', 'mode' => 'restore', 'object' => 'remote:news', 'parent' => 'remote:p' ) ),
            'class' => $entry( array( 'type' => 'class', 'mode' => 'create', 'identifier' => 'news_article', 'names' => array( 'eng-GB' => 'News article' ),
                                      'contentobject_name' => '<title>', 'url_alias_name' => '', 'is_container' => false, 'always_available' => false,
                                      'sort_field' => 'published', 'sort_order' => 'desc', 'groups' => array( 'Content' ), 'remote_id' => 'abc123',
                                      'attributes' => array( 'title' => array( 'datatype' => 'ezstring', 'names' => array( 'eng-GB' => 'Title' ), 'placement' => 1,
                                                                               'required' => true, 'searchable' => true, 'translatable' => true,
                                                                               'information_collector' => false, 'settings' => array( 'data_int1' => 255 ) ) ) ) ),
            'class_change' => $entry( array( 'type' => 'class', 'mode' => 'update', 'match' => 'news_article', 'names' => array( 'eng-GB' => 'News' ),
                                             'sort_field' => 'modified_subnode', 'sort_order' => 'asc', 'groups' => array( 'Content' ), 'attributes' => array() ) ),
            'role' => $entry( array( 'type' => 'role', 'mode' => 'update', 'match' => 'Editor',
                                     'policies' => array( array( 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Class' => array( 'article' ) ) ) ) ),
                              array( 'access.role.change', 'access.policy.remove' ) ),
            'assign' => $entry( array( 'type' => 'role', 'mode' => 'assign', 'match' => 'Editor', 'group' => 'remote:editors', 'limitation' => array( 'subtree' => 'remote:l3' ) ) ),
            'user' => $entry( array( 'type' => 'user', 'mode' => 'create', 'remote_id' => 'ab-cd/ef', 'class' => 'user', 'groups' => array( 'remote:editors' ),
                                     'enabled' => true, 'fields' => array( 'eng-GB' => array( 'signature' => 'Hi' ) ) ),
                              array(), array(), array( 'login', 'email', 'password', 'first_name' ) ),
            'section' => $entry( array( 'type' => 'section', 'mode' => 'ensure', 'match' => 'news', 'identifier' => 'news', 'name' => 'News',
                                        'navigation_part' => 'ezcontentnavigationpart', 'component' => 'x' ) ),
            'section_assign' => $entry( array( 'type' => 'section', 'mode' => 'assign', 'match' => 'news', 'subtree' => 'remote:l3' ) ),
            'state' => $entry( array( 'type' => 'object_state_group', 'mode' => 'ensure', 'match' => 'review', 'identifier' => 'review',
                                      'names' => array( 'eng-GB' => 'Review' ), 'states' => array( 'draft' => array( 'eng-GB' => 'Draft' ) ) ) ),
            'alias' => $entry( array( 'type' => 'url_alias', 'mode' => 'ensure', 'path' => 'about-us', 'node' => 'remote:l3' ) ),
            'alias_all' => $entry( array( 'type' => 'url_alias', 'mode' => 'delete', 'match' => 'remote:l3', 'which' => 'all' ) ),
            'setting' => $entry( array( 'type' => 'ini', 'mode' => 'set', 'file' => 'site.ini', 'scope' => 'siteaccess:admin', 'block' => 'SiteSettings',
                                        'variable' => 'DefaultPage', 'value' => 'content/dashboard' ) ),
            'setting_list' => $entry( array( 'type' => 'ini', 'mode' => 'set', 'file' => 'site.ini', 'scope' => 'override', 'block' => 'RegionalSettings',
                                             'variable' => 'SiteLanguageList', 'value' => array( 'eng-GB', 'ger-DE' ) ) ),
            'setting_remove' => $entry( array( 'type' => 'ini', 'mode' => 'remove', 'file' => 'site.ini', 'scope' => 'override', 'block' => 'DebugSettings',
                                               'variable' => 'DebugByIP' ) ),
        );
    }

    /** MG-2 */
    public function testRecordedStepsAsStepsOfTheStepTypes()
    {
        $made = expMigrationRecordingSteps::fromPlan( array_values( self::plan() ) );
        $this->assertSame( array(), $made['manual'] );
        $warnings = array();
        $problems = expMigrationYaml::validate( array( 'steps' => $made['steps'], 'down' => null, 'transactional' => true ), expMigrationStepRegistry::fromSettings(), $warnings );
        $this->assertSame( array(), $problems );
        $by = array();
        foreach ( $made['steps'] as $step )
            $by[$step['type'] . '/' . ( isset( $step['mode'] ) ? $step['mode'] : '' )][] = $step;

        $content = $by['content/create'][0];
        $this->assertSame( 'eng-GB', $content['language'], 'the initial language first' );
        $this->assertSame( array( 'name' => 'News $${not a ref}' ), $content['fields'], 'a recorded ${ is no placeholder' );
        $this->assertSame( array( 'ger-DE' => array( 'name' => 'Nachrichten' ) ), $content['translations'] );
        $this->assertStringContainsString( 'object states not set by the step: review/draft', $content['comment'] );
        $this->assertStringContainsString( 'a note', $content['comment'] );
        $this->assertSame( array( 'type' => 'location', 'mode' => 'create', 'object' => 'remote:news', 'parent' => 'remote:other' ), $by['location/create'][0] );
        $this->assertStringContainsString( 'no step removes a translation', $by['content/update'][0]['comment'] );
        $this->assertSame( 'remote:news', $by['content/update'][0]['object'] );
        $this->assertSame( array( 'type' => 'location', 'mode' => 'unhide', 'node' => 'remote:l3' ), $by['location/unhide'][0] );
        $this->assertCount( 3, $by['location/update'], 'the sort and one step per child priority' );
        $this->assertSame( array( 'name', 'desc' ), array( $by['location/update'][0]['sort_field'], $by['location/update'][0]['sort_order'] ) );
        $this->assertSame( array( 'remote:c2', 20 ), array( $by['location/update'][2]['node'], $by['location/update'][2]['priority'] ) );
        $this->assertSame( 'remote:l3', $by['location/copy'][0]['node'] );
        $this->assertSame( array( 'remote:news', 'remote:p' ), array( $by['trash/restore'][0]['object'], $by['trash/restore'][0]['parent'] ) );

        $class = $by['content_class/create'][0];
        $this->assertSame( array( 'news_article', 'abc123', '<title>', false ), array( $class['identifier'], $class['remote_id'], $class['name_pattern'], $class['container'] ) );
        $this->assertArrayNotHasKey( 'url_alias_pattern', $class, 'an empty pattern is left out' );
        $this->assertSame( array( 'identifier' => 'title', 'type' => 'ezstring', 'names' => array( 'eng-GB' => 'Title' ), 'required' => true, 'searchable' => true,
                                  'translatable' => true, 'information_collector' => false, 'placement' => 1, 'settings' => array( 'data_int1' => 255 ) ),
                           $class['attributes'][0] );
        $this->assertArrayNotHasKey( 'sort_field', $by['content_class/ensure'][0], 'a sort field no step sets' );
        $this->assertStringContainsString( 'modified_subnode', $by['content_class/ensure'][0]['comment'] );

        $this->assertSame( 'Editor', $by['role/ensure'][0]['name'] );
        $this->assertStringContainsString( 'a policy was removed', $by['role/ensure'][0]['comment'] );
        $this->assertSame( array( 'type' => 'role', 'mode' => 'assign', 'role' => 'Editor', 'groups' => array( 'remote:editors' ), 'subtree' => 'remote:l3' ),
                           $by['role/assign'][0] );

        $user = $by['user/create'][0];
        $this->assertSame( array( 'login' => 'user_abcdef_login', 'email' => 'user_abcdef_email', 'password' => 'user_abcdef_password' ), $user['secrets'] );
        foreach ( array( 'login', 'email', 'password' ) as $key )
            $this->assertArrayNotHasKey( $key, $user, "no $key in the step" );
        $this->assertArrayNotHasKey( 'class', $user, 'the default class is not named' );
        $this->assertSame( array( 'signature' => 'Hi' ), $user['fields'] );
        $this->assertStringContainsString( 'left out: login, email, password, first_name', $user['comment'] );

        $this->assertSame( array( 'type' => 'section', 'mode' => 'assign', 'section' => 'news', 'node' => 'remote:l3', 'scope' => 'subtree' ), $by['section/assign'][0] );
        $this->assertStringContainsString( 'component', $by['section/ensure'][0]['comment'] );
        $this->assertSame( array( array( 'identifier' => 'draft', 'names' => array( 'eng-GB' => 'Draft' ) ) ), $by['object_state_group/ensure'][0]['states'] );
        $this->assertSame( array( 'type' => 'url_alias', 'mode' => 'delete', 'node' => 'remote:l3', 'which' => 'all' ), $by['url_alias/delete'][0] );
        $settings = $by['settings/'];
        $this->assertSame( array( 'type' => 'settings', 'file' => 'site.ini', 'siteaccess' => 'admin', 'set' => array( 'SiteSettings' => array( 'DefaultPage' => 'content/dashboard' ) ) ), $settings[0] );
        $this->assertSame( array( 'eng-GB', 'ger-DE' ), $settings[1]['set']['RegionalSettings']['SiteLanguageList'] );
        $this->assertSame( array( 'DebugSettings' => array( 'DebugByIP' ) ), $settings[2]['remove'] );
    }

    /** MG-3 */
    public function testManualChangesAndEnsure()
    {
        $plan = array(
            array( 'subject' => 's', 'step' => array( 'type' => 'location', 'mode' => 'swap', 'match' => 'remote:a', 'with' => 'remote:b' ) ),
            array( 'subject' => 'w', 'step' => array( 'type' => 'workflow_trigger', 'mode' => 'ensure', 'triggers' => array() ) ),
            array( 'subject' => 'e', 'step' => array( 'type' => 'ini', 'mode' => 'set', 'file' => 'x.ini', 'scope' => 'extension:expnews', 'block' => 'B', 'variable' => 'V', 'value' => 1 ) ),
            array( 'subject' => 'p', 'step' => array( 'type' => 'ini', 'mode' => 'set', 'file' => 'site.ini', 'scope' => 'override', 'block' => 'B', 'variable' => 'Password', 'secret' => true ) ),
            array( 'subject' => 'g', 'step' => array( 'type' => 'url_alias', 'mode' => 'delete', 'match' => 'global', 'which' => 'selected' ) ),
            array( 'subject' => 'c', 'step' => array( 'type' => 'content', 'mode' => 'create', 'class' => 'folder', 'parent' => 'remote:root', 'remote_id' => 'n',
                                                      'fields' => array( 'eng-GB' => array( 'name' => 'N' ) ) ) ),
            array( 'subject' => 'r', 'step' => array( 'type' => 'role', 'mode' => 'create', 'name' => 'R', 'policies' => array() ) ),
        );
        $made = expMigrationRecordingSteps::fromPlan( $plan );
        $this->assertCount( 5, $made['manual'] );
        foreach ( array( 'swap', 'workflow triggers', 'settings of an extension', 'a secret', 'does not name the addresses' ) as $i => $text )
            $this->assertStringContainsString( $text, $made['manual'][$i] );
        $this->assertSame( array( 'create', 'create' ), array_column( $made['steps'], 'mode' ) );
        $ensured = expMigrationRecordingSteps::fromPlan( $plan, array( 'ensure' => true ) );
        $this->assertSame( array( 'ensure', 'ensure' ), array_column( $ensured['steps'], 'mode' ) );
        $this->assertSame( array( 'location' ), expMigrationRecordingSteps::typesOf( 'content', 'copy' ) );
        $this->assertSame( array( 'settings' ), expMigrationRecordingSteps::typesOf( 'ini', 'set' ) );
        $this->assertSame( array(), expMigrationRecordingSteps::typesOf( 'workflow_trigger', 'ensure' ) );
    }

    /** MG-4 */
    public function testSkeletonsOfEveryKind()
    {
        $base = array( 'directory' => '/nowhere/migrations', 'time' => 1798761600, 'root' => '/nowhere' );
        $id = date( 'YmdHis', 1798761600 ) . '_news';
        $files = array( 'yml' => "$id.yml", 'graphql' => "$id.yml", 'ini' => "$id.ini", 'sql' => "$id.sql" );
        foreach ( $files as $type => $file )
        {
            $s = expMigrationGenerator::skeleton( 'news', array( 'type' => $type ) + $base );
            $this->assertTrue( $s['ok'], $type . ': ' . implode( '; ', $s['errors'] ) );
            $this->assertSame( "/nowhere/migrations/$file", $s['file'] );
            $this->assertSame( '', $s['class'] );
            $errors = array();
            $header = expMigrationDefinition::parseHeader( $s['content'], $errors );
            $this->assertSame( array(), $errors, "$type: the header reads without problems" );
            $this->assertSame( 'News', $header['description'], $type );
        }
        $this->assertStringContainsString( '#       query: |', expMigrationGenerator::skeleton( 'news', array( 'type' => 'graphql' ) + $base )['content'] );
        $ini = expMigrationGenerator::skeleton( 'news', array( 'type' => 'ini', 'description' => 'News settings' ) + $base )['content'];
        $this->assertStringStartsWith( "#?ini charset=\"utf-8\"?\n# description: News settings\n", $ini );
        $sql = expMigrationGenerator::skeleton( 'news', array( 'type' => 'sql', 'engine' => 'pgsql' ) + $base );
        $this->assertSame( "/nowhere/migrations/$id.pgsql.sql", $sql['file'] );
        $this->assertStringContainsString( "-- down\n", $sql['content'] );
        $this->assertStringContainsString( 'postgresql only', $sql['content'] );
        $mongo = expMigrationGenerator::skeleton( 'news', array( 'type' => 'sql', 'engine' => 'mongodb' ) + $base );
        $this->assertSame( "/nowhere/migrations/$id.mongodb.json", $mongo['file'] );
        $this->assertSame( array( 'up' => array(), 'down' => array() ), json_decode( $mongo['content'], true ) );
        $this->assertSame( "$id.mysql.sql", basename( expMigrationGenerator::skeleton( 'news', array( 'type' => 'sql', 'engine' => 'mysql' ) + $base )['file'] ) );
        $this->assertStringContainsString( 'goes with --type=sql', implode( ' ', expMigrationGenerator::skeleton( 'news', array( 'type' => 'yml', 'engine' => 'mysql' ) + $base )['errors'] ) );
        $this->assertStringContainsString( 'is not one of', implode( ' ', expMigrationGenerator::skeleton( 'news', array( 'type' => 'sql', 'engine' => 'db2' ) + $base )['errors'] ) );
        $this->assertNotEmpty( expMigrationGenerator::skeleton( 'news', array( 'type' => 'xml' ) + $base )['errors'] );
        // what the definitions read back: the id and the engine of each file name
        foreach ( array( "$id.pgsql.sql", "$id.mongodb.json", "$id.ini" ) as $name )
            $this->assertArrayNotHasKey( 'error', expMigrationDefinition::parseName( $name ), $name );
    }

    /** MG-5 */
    public function testUserSecrets()
    {
        $step = new expMigrationStepUser();
        $ok = array( 'mode' => 'create', 'secrets' => array( 'login' => 'user_x_login', 'email' => 'env:USER_X_EMAIL', 'password' => 'user_x_password' ),
                     'groups' => array( 'remote:editors' ) );
        $this->assertSame( array(), $step->validate( $ok ) );
        $this->assertContains( 'secrets.login and login: exclude each other', $step->validate( array( 'login' => 'x' ) + $ok ) );
        $this->assertNotEmpty( preg_grep( '/^secrets\.name: secrets names login, email, password$/', $step->validate( array( 'secrets' => array( 'name' => 'y' ) + $ok['secrets'] ) + $ok ) ) );
        $this->assertContains( 'login is missing (mode create)', $step->validate( array( 'secrets' => array( 'email' => 'e' ) ) + $ok ) );
        $this->assertContains( 'secrets.email: not the name of a secret', $step->validate( array( 'secrets' => array( 'email' => 'bad name!' ) + $ok['secrets'] ) + $ok ) );
        $this->assertSame( array(), $step->validate( array( 'mode' => 'update', 'user' => 'remote:x', 'secrets' => array( 'password' => 'p' ) ) ) );
        $this->assertSame( array( 'create the user of the secret user_x_login in the group(s) "remote:editors"' ), $step->plan( $ok, null ) );
        $this->assertSame( array( 'x' => 1 ), expMigrationStepUser::withSecrets( array( 'x' => 1 ) ) );
    }

    /** MG-6 */
    public function testMigrationOfEvents()
    {
        $this->dir = eZSys::cacheDirectory() . '/expMigrationGeneratorTest-' . getmypid();
        $events = array(
            array( 'audit' => 'content.class.create', 'subject' => 'class news', 'step' => array( 'type' => 'class', 'mode' => 'create', 'identifier' => 'news',
                   'names' => array( 'eng-GB' => 'News' ), 'groups' => array( 'Content' ), 'attributes' => array() ), 'omitted' => array(), 'notes' => array() ),
            array( 'audit' => 'content.node.swap', 'subject' => 'location remote:a', 'step' => array( 'type' => 'location', 'mode' => 'swap', 'match' => 'remote:a', 'with' => 'remote:b' ),
                   'omitted' => array(), 'notes' => array() ),
        );
        $options = array( 'name' => 'news setup', 'directory' => $this->dir, 'time' => 1798761600, 'author' => 'Team' );
        $report = expMigrationRecordingGenerator::migration( $events, $options, array( 'plan' => array(), 'omitted' => array(), 'errors' => array(), 'warnings' => array(),
                                                                                       'exit' => 0, 'written' => false ) );
        $this->assertSame( expUpgrade::EXIT_OK, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertFalse( $report['written'] );
        $this->assertFileDoesNotExist( $report['generated']['file'] );
        $content = $report['generated']['content'];
        $this->assertStringStartsWith( "# description: News setup\n# author: Team\n", $content );
        $this->assertStringContainsString( "# Not generated, to be done by hand:\n# - ", $content );
        $this->assertStringContainsString( "    - type: content_class\n      mode: create\n      identifier: news\n", $content );
        $this->assertCount( 1, $report['manual'] );

        $written = expMigrationRecordingGenerator::migration( $events, $options + array( 'apply' => true ), $report );
        $this->assertTrue( $written['written'], implode( '; ', $written['errors'] ) );
        $this->assertSame( $content, file_get_contents( $written['generated']['file'] ) );
        $again = expMigrationRecordingGenerator::migration( $events, $options + array( 'apply' => true ), $report );
        $this->assertSame( expUpgrade::EXIT_FAILED, $again['exit'] );
        $this->assertStringContainsString( 'exists already', implode( ' ', $again['errors'] ) );

        $registry = new expMigrationStepRegistry();
        $registry->register( 'content', 'expMigrationStepContent' );
        $missing = expMigrationRecordingGenerator::migration( $events, array( 'registry' => $registry, 'apply' => true, 'name' => 'other' ) + $options, $report );
        $this->assertSame( array( 'content_class' ), $missing['missing_step_types'] );
        $this->assertFileDoesNotExist( $this->dir . '/' . date( 'YmdHis', 1798761600 ) . '_other.yml' );
    }
}
