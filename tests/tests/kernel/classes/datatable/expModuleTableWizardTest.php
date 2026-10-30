<?php
/**
 * The module table wizard (expModuleTableWizard, expModuleTableCode; doc/eng-us/features/6.0/data-tables.md, "The module
 * table wizard"). No installation: the site's database is SQLite in memory and the generated table lives on a SQLite
 * connection under var/datatable/ (expDataTableSqliteFixture); an extension is written below extension/t691test_<pid>
 * and removed afterwards.
 *
 *  MW-01 - settings(): the module from the name, the table from the module, the class from the table, site as ''
 *  MW-02 - fields(): contentobject_id, node_id, created and modified as asked, never id, a source's fields
 *  MW-03 - problems(): bad names, a kernel table name on the site's database, an unknown connection, no fields, the
 *          license
 *  MW-04 - files() of a new extension: every path; of an existing one: no ezinfo, extension.xml or LICENSE, the
 *          README under doc/
 *  MW-05 - every generated PHP file passes php -l; no template holds a <style> block or a style attribute
 *  MW-06 - the generated definition: relations of object, node and user fields, unique keys, the connection only when
 *          not the site's database, the sort, the function attributes <field>_target
 *  MW-07 - expModuleTableCode::export() gives the value back when the code is read in; php() and tpl() quoting
 *  MW-08 - write() into an existing extension: new files written, an existing file left, settings blocks appended
 *          once, share/db_schema.dba holds both tables, sql/<engine>/schema.sql appended once
 *  MW-09 - the generated class on a SQLite connection: the table from the wizard's schema, fromInput() typing and its
 *          problems (required, unique, number, JSON, length), store, fetch, toArray(), the relation methods
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableSqliteFixture.php';

class expModuleTableWizardTest extends PHPUnit\Framework\TestCase
{
    use expDataTableSqliteFixture;

    /** @var string[] directories to remove */
    private $remove = array();

    /** @var bool */
    private $fixture = false;

    const FIELDS = "title string(120) required\nremote_id string(64) unique\nprice float\nactive boolean\npublished date\n"
                 . "author user\narticle object\nnotes text\nextra json";

    protected function setUp(): void
    {
        expDataConnections::setConnections( array( 'archive' => array( 'engine' => 'sqlite', 'database' => 'var/datatable/t691-wizard-' . getmypid() . '.sqlite' ) ) );
    }

    protected function tearDown(): void
    {
        if ( $this->fixture )
            $this->tearDownFixture();
        expDataConnections::setConnections( null );
        foreach ( $this->remove as $dir )
            if ( is_dir( $dir ) )
                eZDir::recursiveDelete( $dir );
        $file = expDataTableTestLoader::root() . '/var/datatable/t691-wizard-' . getmypid() . '.sqlite';
        if ( is_file( $file ) )
            unlink( $file );
    }

    private static function input( array $more = array() )
    {
        return $more + array( 'name' => 't691test_' . getmypid(), 'title' => 'Demo rows', 'connection' => 'archive', 'source' => 'list',
                              'fields_text' => self::FIELDS, 'licence' => 'GPL-2.0-or-later', 'content' => 'none' );
    }

    /** MW-01 */
    public function testSettingsFillTheGaps()
    {
        $s = expModuleTableWizard::settings( array( 'name' => 'Shop Orders!' ) );
        $this->assertSame( 'shop_orders', $s['name'] );
        $this->assertSame( 'shop_orders', $s['module'] );
        $this->assertSame( 'shop_orders_row', $s['table'] );
        $this->assertSame( 'shopOrdersRow', $s['class'] );
        $this->assertSame( 'Shop orders row', $s['title'] );
        $this->assertSame( '', $s['connection'], 'no connection is the site\'s database' );
        $this->assertSame( 'list', $s['source'] );
        $this->assertSame( 'none', $s['content'] );
        $this->assertSame( '1.0.0', $s['version'] );
        $this->assertSame( 'exponential', $s['vendor'] );
        $this->assertFalse( $s['existing'] );
        $this->assertSame( '', expModuleTableWizard::settings( array( 'name' => 'x', 'connection' => 'site' ) )['connection'] );
        $this->assertSame( 'archive', expModuleTableWizard::settings( array( 'name' => 'x', 'connection' => 'archive' ) )['connection'] );
        $this->assertSame( '', expModuleTableWizard::settings( array( 'name' => 'x', 'connection' => 'Bad Name' ) )['connection'] );
        $this->assertSame( 'list', expModuleTableWizard::settings( array( 'source' => '../evil' ) )['source'] );
        $this->assertSame( 'none', expModuleTableWizard::settings( array( 'content' => 'everything' ) )['content'] );
        $s = expModuleTableWizard::settings( array( 'name' => 'x', 'class' => '9 lives', 'table' => 'Über Tabelle' ) );
        $this->assertSame( 'x9lives', $s['class'] );
        $this->assertMatchesRegularExpression( '/^[a-z][a-z0-9_]*tabelle$/', $s['table'] );
        foreach ( expModuleTableWizard::parts() as $key => $part )
            $this->assertSame( $part['default'], $s['parts'][$key], $key );
        $s = expModuleTableWizard::settings( array( 'name' => 'x', 'parts' => array( 'class', 'schema' ) ) );
        $this->assertTrue( $s['parts']['class'] && $s['parts']['schema'] );
        $this->assertFalse( $s['parts']['module'] || $s['parts']['templates'] );
    }

    /** MW-02 */
    public function testFieldsAddWhatIsAskedFor()
    {
        $s = expModuleTableWizard::settings( self::input( array( 'fields_text' => "id integer\ntitle string\ntitle text", 'content' => 'both', 'stamps' => true ) ) );
        $errors = array();
        $fields = expModuleTableWizard::fields( $s, $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( 'title', 'contentobject_id', 'node_id', 'created', 'modified' ), array_column( $fields, 'name' ), 'id and a second title are left out' );
        $byName = array_column( $fields, null, 'name' );
        $this->assertSame( 'object', $byName['contentobject_id']['type'] );
        $this->assertSame( 'node', $byName['node_id']['type'] );
        $this->assertSame( 'datetime', $byName['created']['type'] );
        $this->assertSame( 'a list of fields', $s['source_label'] );

        $s = expModuleTableWizard::settings( self::input( array( 'fields_text' => "contentobject_id integer", 'content' => 'object' ) ) );
        $this->assertSame( array( 'contentobject_id' ), array_column( expModuleTableWizard::fields( $s ), 'name' ), 'a field of that name is kept as given' );
        $this->assertSame( 'integer', expModuleTableWizard::fields( $s )[0]['type'] );

        $s = expModuleTableWizard::settings( self::input( array( 'source' => 'nosuchsource' ) ) );
        $errors = array();
        $this->assertSame( array(), expModuleTableWizard::fields( $s, $errors ) );
        $this->assertStringContainsString( 'nosuchsource', implode( ' ', $errors ) );

        $s = expModuleTableWizard::settings( self::input( array( 'source' => 'table', 'ref' => 'archive:no_such_table' ) ) );
        $errors = array();
        expModuleTableWizard::fields( $s, $errors );
        $this->assertStringContainsString( 'no_such_table', implode( ' ', $errors ) );

        $s = expModuleTableWizard::settings( self::input( array( 'fields_text' => implode( "\n", array_map( function ( $i ) { return "f$i string"; }, range( 1, 201 ) ) ) ) ) );
        $errors = array();
        $this->assertSame( array(), expModuleTableWizard::fields( $s, $errors ) );
        $this->assertStringContainsString( '200', implode( ' ', $errors ) );
    }

    /** MW-03 */
    public function testProblems()
    {
        $this->assertSame( array(), expModuleTableWizard::problems( expModuleTableWizard::settings( self::input() ) ) );
        $p = expModuleTableWizard::problems( expModuleTableWizard::settings( array( 'fields_text' => '' ) ) );
        $this->assertArrayHasKey( 'name', $p );
        $this->assertArrayHasKey( 'fields', $p );
        $p = expModuleTableWizard::problems( expModuleTableWizard::settings( self::input( array( 'connection' => 'site', 'table' => 'ezcontentobject_copy' ) ) ) );
        $this->assertArrayHasKey( 'table_kernel', $p );
        $p = expModuleTableWizard::problems( expModuleTableWizard::settings( self::input( array( 'table' => 'exp_mine' ) ) ) );
        $this->assertArrayNotHasKey( 'table_kernel', $p, 'on a named connection the kernel prefixes are free' );
        $settings = expModuleTableWizard::settings( self::input() );
        $settings['connection'] = 'nosuch';
        $this->assertArrayHasKey( 'connection', expModuleTableWizard::problems( $settings ) );
        $settings = expModuleTableWizard::settings( self::input( array( 'class' => 'eZContentObject' ) ) );
        $this->assertArrayHasKey( 'class_exists', expModuleTableWizard::problems( $settings ) );
        $settings = expModuleTableWizard::settings( self::input() );
        $settings['table'] = 'Bad-Name';
        $this->assertArrayHasKey( 'table', expModuleTableWizard::problems( $settings ) );
        $settings = expModuleTableWizard::settings( self::input( array( 'licence' => 'no-such-licence' ) ) );
        $this->assertArrayHasKey( 'licence_licence', expModuleTableWizard::problems( $settings ) );
        $settings = expModuleTableWizard::settings( self::input( array( 'fields_text' => "a hexcolor" ) ) );
        $this->assertArrayHasKey( 'fields_0', expModuleTableWizard::problems( $settings ), 'a field error is a problem' );
        $this->assertFalse( expModuleTableWizard::classTaken( 't691NoSuchClass' . getmypid() ) );
        $this->assertTrue( expModuleTableWizard::classTaken( 'eZContentObject' ) );
    }

    /** MW-04 */
    public function testTheFiles()
    {
        $s = expModuleTableWizard::settings( self::input( array( 'module' => 'demo', 'table' => 't691_demo', 'class' => 't691DemoRow' ) ) );
        $files = expModuleTableWizard::files( $s );
        $expected = array( 'LICENSE', 'README.md', 'classes/t691demorow.php', 'classes/t691demorowfunctioncollection.php',
                           'design/admin/templates/demo/edit.tpl', 'design/admin/templates/demo/list.tpl', 'design/admin/templates/demo/remove.tpl',
                           'design/admin4/templates/demo/edit.tpl', 'design/admin4/templates/demo/list.tpl', 'design/admin4/templates/demo/remove.tpl',
                           'extension.xml', 'ezinfo.php', 'modules/demo/edit.php', 'modules/demo/function_definition.php', 'modules/demo/json.php',
                           'modules/demo/list.php', 'modules/demo/module.php', 'modules/demo/remove.php', 'settings/design.ini.append.php',
                           'settings/menu.ini.append.php', 'settings/module.ini.append.php', 'share/db_schema.dba', 'sql/mysql/schema.sql',
                           'sql/oracle/schema.sql', 'sql/postgresql/schema.sql', 'sql/sqlite/schema.sql', 'sql/sqlserver/schema.sql' );
        $this->assertSame( $expected, array_keys( $files ) );
        $this->assertSame( $files['design/admin/templates/demo/list.tpl'], $files['design/admin4/templates/demo/list.tpl'] );
        $this->assertStringContainsString( 'ModuleList[]=demo', $files['settings/module.ini.append.php'] );
        $this->assertStringContainsString( 'GroupLinks_data[]=datatable_demo', $files['settings/menu.ini.append.php'] );
        $this->assertStringContainsString( 'PolicyList_datatable_demo[]=demo/read', $files['settings/menu.ini.append.php'] );
        $this->assertStringContainsString( 'DesignExtensions[]=' . $s['name'], $files['settings/design.ini.append.php'] );
        $this->assertStringContainsString( 'IDENTITY(1,1)', $files['sql/sqlserver/schema.sql'] );
        $this->assertStringContainsString( 'CREATE SEQUENCE', $files['sql/oracle/schema.sql'] );
        $this->assertStringContainsString( '$FunctionList[\'edit\']', $files['modules/demo/module.php'] );
        // the texts every generated module shares are in the kernel context, which the kernel catalogues translate;
        // the title stays in the extension's own context
        $this->assertStringContainsString( "{'New row'|i18n( 'kernel/datatable/module' )}", $files['design/admin4/templates/demo/list.tpl'] );
        $this->assertStringContainsString( "{'Save'|i18n( 'kernel/datatable/module' )}", $files['design/admin4/templates/demo/edit.tpl'] );
        $this->assertStringContainsString( "{'Remove'|i18n( 'kernel/datatable/module' )}", $files['design/admin4/templates/demo/remove.tpl'] );
        $this->assertStringContainsString( "ezpI18n::tr( 'kernel/datatable/module', 'Setup' )", $files['modules/demo/list.php'] );
        $this->assertStringContainsString( "|i18n( 'extension/" . $s['name'] . "' )}</h1>", $files['design/admin4/templates/demo/list.tpl'] );
        $this->assertStringNotContainsString( "'New row'|i18n( 'extension/", $files['design/admin4/templates/demo/list.tpl'] );

        // only some parts; an existing extension
        $s = expModuleTableWizard::settings( self::input( array( 'module' => 'demo', 'table' => 't691_demo', 'class' => 't691DemoRow', 'parts' => array( 'class', 'readme', 'ezinfo', 'licence' ) ) ) );
        $s['existing'] = true;
        $this->assertSame( array( 'classes/t691demorow.php', 'doc/t691_demo.md' ), array_keys( expModuleTableWizard::files( $s ) ) );
        $s['name'] = '';
        $this->assertSame( array(), expModuleTableWizard::files( $s ) );
    }

    /** MW-05 */
    public function testTheGeneratedCodeIsValid()
    {
        $s = expModuleTableWizard::settings( self::input( array( 'content' => 'both', 'stamps' => true, 'title' => "O'Reilly's {rows}", 'parts' => array_keys( expModuleTableWizard::parts() ) ) ) );
        $files = expModuleTableWizard::files( $s );
        $dir = expDataTableTestLoader::root() . '/var/tmp/t691-lint-' . getmypid();
        $this->remove[] = $dir;
        @mkdir( $dir, 0775, true );
        $php = 0;
        foreach ( $files as $path => $contents )
        {
            if ( substr( $path, -4 ) === '.php' )
            {
                $file = $dir . '/' . str_replace( '/', '_', $path );
                file_put_contents( $file, $contents );
                $output = array();
                exec( escapeshellarg( PHP_BINARY ) . ' -n -l ' . escapeshellarg( $file ) . ' 2>&1', $output, $status );
                $this->assertSame( 0, $status, $path . ': ' . implode( "\n", $output ) );
                $php++;
            }
            if ( substr( $path, -4 ) === '.tpl' )
            {
                $this->assertDoesNotMatchRegularExpression( '/<style\b/i', $contents, $path );
                $this->assertDoesNotMatchRegularExpression( '/\sstyle\s*=/i', $contents, $path );
                $this->assertSame( substr_count( $contents, '{' ), substr_count( $contents, '}' ), "$path: braces balanced" );
            }
        }
        $this->assertGreaterThanOrEqual( 12, $php );
        // the settings files are PHP-wrapped INI
        foreach ( array( 'settings/module.ini.append.php', 'settings/menu.ini.append.php', 'settings/design.ini.append.php' ) as $path )
        {
            $this->assertStringStartsWith( '<?php /* #?ini charset="utf-8"?', $files[$path] );
            $this->assertStringEndsWith( "*/ ?>\n", $files[$path] );
        }
    }

    /** MW-06 */
    public function testTheDefinition()
    {
        $s = expModuleTableWizard::settings( self::input( array( 'table' => 't691_demo', 'class' => 't691DemoRow', 'content' => 'node' ) ) );
        $fields = expModuleTableWizard::fields( $s );
        $def = expModuleTableCode::definition( $s, $fields );
        $this->assertSame( 'archive', $def['connection'] );
        $this->assertSame( array( 'id' ), $def['keys'] );
        $this->assertSame( 'id', $def['increment_key'] );
        $this->assertSame( array( 'id' => 'desc' ), $def['sort'] );
        $this->assertSame( 't691DemoRow', $def['class_name'] );
        $this->assertSame( 't691_demo', $def['name'] );
        $this->assertSame( array( 'author' => array( 'class' => 'eZUser', 'field' => 'contentobject_id' ),
                                  'article' => array( 'class' => 'eZContentObject', 'field' => 'id' ),
                                  'node_id' => array( 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id' ) ), $def['relations'] );
        $this->assertSame( array( 't691_demo_remote_id' => array( 'remote_id' ) ), $def['unique'] );
        $this->assertSame( 'float', $def['fields']['price']['datatype'] );
        $this->assertSame( 'text', $def['fields']['extra']['datatype'] );
        $this->assertSame( 'integer', $def['fields']['active']['datatype'] );
        $this->assertSame( 'string', $def['fields']['title']['datatype'] );
        $this->assertTrue( $def['fields']['title']['required'] );
        $s['connection'] = '';
        $this->assertArrayNotHasKey( 'connection', expModuleTableCode::definition( $s, $fields ) );
        $code = expModuleTableCode::persistentClass( $s, $fields, '' );
        foreach ( array( 'author' => 'authorTarget', 'article' => 'articleTarget', 'node_id' => 'nodeIdTarget' ) as $field => $method )
        {
            $this->assertStringContainsString( "'{$field}_target' => '$method'", $code );
            $this->assertStringContainsString( "public function $method()", $code );
        }
        $this->assertSame( array( 'title' => 120, 'remote_id' => 64 ), expModuleTableCode::lengths( $fields ) );
        $schema = expModuleTableWizard::schema( $s, $fields );
        $this->assertSame( 120, $schema['t691_demo']['fields']['title']['length'] );
        $this->assertSame( 'auto_increment', $schema['t691_demo']['fields']['id']['type'] );
        $this->assertSame( 'unique', $schema['t691_demo']['indexes']['t691_demo_remote_id']['type'] );
    }

    /** MW-07 */
    public function testExportAndQuoting()
    {
        $values = array( array(), array( 'a' => 1, 'b' => array( 1, 2, 3 ), 'c' => "it's \\ \"x\"", 'd' => 1.5, 'e' => 2.0, 'f' => true, 'g' => null ),
                         array( 'x', 'y' ), 0.0, 'text' );
        $dir = expDataTableTestLoader::root() . '/var/tmp/t691-export-' . getmypid();
        $this->remove[] = $dir;
        @mkdir( $dir, 0775, true );
        foreach ( $values as $i => $value )
        {
            $file = "$dir/v$i.php";
            file_put_contents( $file, '<?php return ' . expModuleTableCode::export( $value, 1 ) . ";\n" );
            $this->assertSame( var_export( $value, true ), var_export( require $file, true ), "value $i" );
        }
        $this->assertSame( "O\\'Reilly \\\\ x", expModuleTableCode::php( "O'Reilly \\ x" ) );
        $this->assertSame( "O\\'Reilly x", expModuleTableCode::tpl( "O'Reilly {x}" ) );
        $this->assertSame( 'FieldNameHere', expModuleTableCode::camel( 'field_name_here' ) );
    }

    /** MW-08 */
    public function testWriteIntoAnExistingExtension()
    {
        $name = 't691test_' . getmypid();
        $root = expModuleTableWizard::extensionPath( $name );
        $this->remove[] = $root;
        foreach ( array( 'settings', 'share', 'sql/mysql', 'classes' ) as $dir )
            mkdir( "$root/$dir", 0775, true );
        file_put_contents( "$root/settings/module.ini.append.php", "<?php /* #?ini charset=\"utf-8\"?\n\n[ModuleSettings]\nExtensionRepositories[]=$name\nModuleList[]=other\n*/ ?>\n" );
        file_put_contents( "$root/share/db_schema.dba", "<?php\n\$schema = " . var_export( array( 'other_table' => array( 'name' => 'other_table',
            'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ) ), 'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) ) ), true ) . ";\n" );
        file_put_contents( "$root/sql/mysql/schema.sql", "CREATE TABLE other_table ( id int );\n" );
        file_put_contents( "$root/classes/keepme.php", "<?php // mine\n" );
        // a file of the wizard that is there already is left alone
        file_put_contents( "$root/classes/t691demorow.php", "<?php // hand made\n" );

        $s = expModuleTableWizard::settings( self::input( array( 'name' => $name, 'module' => 'demo', 'table' => 't691_demo', 'class' => 't691DemoRow' ) ) );
        $this->assertTrue( $s['existing'] );
        $result = expModuleTableWizard::write( $s );
        $this->assertTrue( $result['ok'], $result['message'] );
        $this->assertContains( 'modules/demo/list.php', $result['written'] );
        $this->assertContains( 'doc/t691_demo.md', $result['written'] );
        $this->assertNotContains( 'ezinfo.php', $result['written'] );
        $this->assertSame( array( 'classes/t691demorow.php' ), $result['skipped'] );
        $this->assertSame( "<?php // hand made\n", file_get_contents( "$root/classes/t691demorow.php" ) );
        foreach ( array( 'settings/module.ini.append.php', 'share/db_schema.dba', 'sql/mysql/schema.sql' ) as $path )
            $this->assertContains( $path, $result['appended'] );
        $this->assertContains( 'sql/postgresql/schema.sql', $result['written'], 'a file that was not there is written' );

        $module = file_get_contents( "$root/settings/module.ini.append.php" );
        $this->assertStringContainsString( 'ModuleList[]=other', $module );
        $this->assertStringContainsString( 'ModuleList[]=demo', $module );
        $this->assertSame( 1, substr_count( $module, '*/ ?>' ) );
        $this->assertStringStartsWith( '<?php /*', $module );
        $schema = expKernelTableSchema::read( "$root/share/db_schema.dba" );
        $this->assertSame( array( 'other_table', 't691_demo' ), array_keys( $schema ) );
        $sql = file_get_contents( "$root/sql/mysql/schema.sql" );
        $this->assertStringStartsWith( 'CREATE TABLE other_table', $sql );
        $this->assertSame( 1, substr_count( $sql, 'CREATE TABLE t691_demo' ) );

        // again: nothing appended twice
        $again = expModuleTableWizard::write( $s );
        $this->assertTrue( $again['ok'], $again['message'] );
        $this->assertSame( array(), $again['written'] );
        $this->assertNotContains( 'settings/module.ini.append.php', $again['appended'] );
        $this->assertNotContains( 'sql/mysql/schema.sql', $again['appended'] );
        $this->assertSame( $module, file_get_contents( "$root/settings/module.ini.append.php" ) );
        $this->assertSame( $sql, file_get_contents( "$root/sql/mysql/schema.sql" ) );
        $this->assertSame( array( 'other_table', 't691_demo' ), array_keys( expKernelTableSchema::read( "$root/share/db_schema.dba" ) ) );
        $this->assertSame( "<?php // mine\n", file_get_contents( "$root/classes/keepme.php" ) );
    }

    /** MW-09 */
    public function testTheGeneratedClassOnAConnection()
    {
        $this->fixture = true;
        $this->setUpFixture( false );
        $class = 't691WizRow' . getmypid();
        $s = expModuleTableWizard::settings( self::input( array( 'connection' => 't691m2', 'table' => 't691_wiz', 'class' => $class, 'content' => 'node',
                                                                 'parts' => array( 'class' ) ) ) );
        $fields = expModuleTableWizard::fields( $s );
        $files = expModuleTableWizard::files( $s );
        $dir = expDataTableTestLoader::root() . '/var/tmp/t691-class-' . getmypid();
        $this->remove[] = $dir;
        @mkdir( $dir, 0775, true );
        $file = $dir . '/' . strtolower( $class ) . '.php';
        file_put_contents( $file, $files['classes/' . strtolower( $class ) . '.php'] );
        require_once $file;
        $result = expDataTableSchema::apply( expModuleTableWizard::schema( $s, $fields ), expDataConnections::db( 't691m2' ) );
        $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        $this->assertFalse( expKernelTableSchema::exists( 't691_wiz', eZDB::instance(), true ), 'not on the site\'s database' );

        $row = $class::create();
        $this->assertNull( $row->attribute( 'id' ) );
        $problems = $row->fromInput( array( 'title' => "Tea 'n' \\cakes", 'remote_id' => 'r1', 'price' => '3.5', 'active' => 'on',
                                            'published' => '2026-10-22', 'author' => '14', 'article' => '1', 'node_id' => '2',
                                            'extra' => '{"a":1}', 'notes' => 'long text' ) );
        $this->assertSame( array(), $problems );
        $row->store();
        $id = (int)$row->attribute( 'id' );
        $this->assertGreaterThan( 0, $id );
        $read = $class::fetch( $id );
        $array = $read->toArray();
        $this->assertSame( $id, $array['id'] );
        $this->assertSame( "Tea 'n' \\cakes", $array['title'] );
        $this->assertSame( 3.5, $array['price'] );
        $this->assertTrue( $array['active'] );
        $this->assertSame( (int)strtotime( '2026-10-22' ), $array['published'] );
        $this->assertSame( array( 'a' => 1 ), $array['extra'] );
        $this->assertSame( 2, $array['node_id'] );
        $this->assertSame( 1, $class::fetchListCount() );
        $this->assertCount( 1, $class::fetchList( array( 'remote_id' => 'r1' ) ) );
        $this->assertTrue( $read->hasAttribute( 'node_id_target' ) && $read->hasAttribute( 'author_target' ) && $read->hasAttribute( 'article_target' ) );
        $this->assertTrue( method_exists( $read, 'nodeIdTarget' ) && method_exists( $read, 'authorTarget' ) && method_exists( $read, 'articleTarget' ) );
        $this->assertNull( $read->target( 'title' ), 'a field without a relation points at nothing' );

        $bad = $class::create();
        $problems = $bad->fromInput( array( 'title' => '', 'remote_id' => 'r1', 'price' => 'abc', 'extra' => '{bad', 'author' => 'x' ) );
        $text = implode( ' | ', $problems );
        foreach ( array( 'title: required', 'remote_id: another row has this value', 'price: a number', 'extra: JSON', 'author: a whole number' ) as $message )
            $this->assertStringContainsString( $message, $text );
        $long = $class::create();
        $this->assertStringContainsString( 'at most 120 characters', implode( ' ', $long->fromInput( array( 'title' => str_repeat( 'x', 121 ) ) ) ) );
        // changing the row itself keeps its unique value
        $this->assertSame( array(), $read->fromInput( array( 'remote_id' => 'r1', 'title' => 'Changed' ) ) );
        $read->store();
        $this->assertSame( 'Changed', $class::fetch( $id )->attribute( 'title' ) );
        $read->remove();
        $this->assertNull( $class::fetch( $id ) );
    }
}
