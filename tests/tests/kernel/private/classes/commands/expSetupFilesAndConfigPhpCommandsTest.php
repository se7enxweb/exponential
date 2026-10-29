<?php
/**
 * exp:setup:htaccess, exp:setup:config, exp:setup:status (bin/php/setupfiles.php) and exp:config:list, get, set,
 * unset (bin/php/configphp.php): expSetupFileCopy, expConfigPhpEditor, expConfigPhpCatalogue and the two commands.
 * No database: fixture files in a throwaway directory below var/tmp/, removed after each test.
 *
 *  SC-01 - The editor finds set, commented and conditional define()s, in any spelling, and skips what is no define
 *  SC-02 - Values are read with their type: bool, null, int, octal, hex, float, negative, strings with escapes, expression
 *  SC-03 - set changes only the value of the one define(): comments, order and odd formatting stay
 *  SC-04 - set takes the comment off a documented "// define( ... );" line, never off a commented condition
 *  SC-05 - set adds an unknown name to the BEGIN/END block, which the first addition creates in the right place
 *  SC-06 - unset comments a define() out where it is, removes it from the block, refuses conditions and duplicates
 *  SC-07 - The value of the command line is typed by --type, the current value, the documented value, or its text
 *  SC-08 - Strings with quotes and backslashes are written as literals that read back as the same string
 *  SC-09 - Syntax errors are found without running the file; secrets are recognized and masked
 *  SC-10 - The catalogue of the shipped config.php-RECOMMENDED: every setting with section, type and description,
 *          and every one of them can be set and read back
 *  SC-11 - Copying a template: new, same, differs, missing, not a file; modes kept or taken from the template
 *  SC-12 - A backup keeps content and mode, in var/backup/<kind>/<time>/ with directories of mode 0700
 *  SC-13 - A write whose check fails changes nothing and leaves no temporary file; lint() finds a broken file
 *  SC-14 - The commands: --apply with --dry-run is exit 2, the dry run writes nothing, --apply writes with a backup,
 *          an existing different file is refused without --force, a config.php that does not compile is refused
 *  SC-15 - autoload.php skips a config.php that does not compile only for the scripts that tolerate it
 *  SC-16 - The aliases and the documentation of the commands
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSetupFilesAndConfigPhpCommandsTest extends PHPUnit\Framework\TestCase
{
    /** @var string a throwaway installation root */
    private $dir;

    protected function setUp(): void
    {
        $this->dir = eZSys::rootDir() . '/var/tmp/expsetupfilestest-' . getmypid() . '-' . mt_rand( 1000, 9999 );
        mkdir( $this->dir, 0755, true );
    }

    protected function tearDown(): void
    {
        self::removeTree( $this->dir );
    }

    private static function removeTree( $path )
    {
        if ( is_link( $path ) || is_file( $path ) )
        {
            unlink( $path );
            return;
        }
        if ( !is_dir( $path ) )
            return;
        foreach ( scandir( $path ) as $entry )
            if ( $entry !== '.' && $entry !== '..' )
                self::removeTree( $path . '/' . $entry );
        rmdir( $path );
    }

    private static function editor( $code )
    {
        return new expConfigPhpEditor( $code );
    }

    private static function states( $code )
    {
        $states = array();
        foreach ( self::editor( $code )->entries() as $entry )
            $states[] = $entry['name'] . ':' . $entry['state'];
        return $states;
    }

    // ------------------------------------------------------------------ editor

    /** SC-01 */
    public function testTheEditorFindsEveryKindOfDefine()
    {
        $code = <<<'PHP'
<?php
define( 'PLAIN', 1 );
DEFINE("UPPER", 2);
\define( 'QUALIFIED', 3 );
//define( 'COMMENTED', 4 );
// define( 'SPACED', 5 );
if ( true )
    define( 'IF_NO_BRACES', 6 );
if ( !defined( 'IN_BLOCK' ) )
{
    define( 'IN_BLOCK', 7 );
}
defined( 'OR_DEFINED' ) || define( 'OR_DEFINED', 8 );
define( 'OR_DIE', 9 ) or die( 'x' );
define( $dynamic, 10 );
$object->define( 'METHOD', 11 );
function define_something() {}
define( 'AFTER_BLOCK', 12 );
//if ( PHP_SAPI === 'cli' )
//    define( 'COMMENTED_CONDITION', 13 );
define( 'CASE_ARG', 14, true );
PHP;
        $this->assertSame( array(
            'PLAIN:set', 'UPPER:set', 'QUALIFIED:set', 'COMMENTED:commented', 'SPACED:commented',
            'IF_NO_BRACES:conditional', 'IN_BLOCK:conditional', 'OR_DEFINED:conditional', 'OR_DIE:conditional',
            'AFTER_BLOCK:set', 'COMMENTED_CONDITION:commented', 'CASE_ARG:set',
        ), self::states( $code ) );
        $editor = self::editor( $code );
        $this->assertTrue( $editor->entriesOf( 'COMMENTED_CONDITION' )[0]['conditional'] );
        $this->assertFalse( $editor->entriesOf( 'COMMENTED' )[0]['conditional'] );
        $this->assertSame( 'conditional', $editor->find( 'IN_BLOCK' )['state'] );
        $this->assertNull( $editor->find( 'COMMENTED' ), 'a commented define is not set' );
        $this->assertSame( array( 'PLAIN', 'UPPER', 'QUALIFIED', 'IF_NO_BRACES', 'IN_BLOCK', 'OR_DEFINED', 'OR_DIE', 'AFTER_BLOCK', 'CASE_ARG' ),
                           $editor->definedNames() );
        $this->assertSame( 4, $editor->find( 'QUALIFIED' )['line'] );
    }

    /** SC-02 */
    public function testValuesAreReadWithTheirType()
    {
        $code = "<?php\n"
              . "define( 'T', true ); define( 'F', FALSE ); define( 'N', null );\n"
              . "define( 'I', 42 ); define( 'NEG', -3 ); define( 'O', 0640 ); define( 'H', 0x1F ); define( 'FL', 1.5 );\n"
              . "define( 'S', 'it\\'s a \\\\ path' ); define( 'D', \"tab\\there\" ); define( 'E', __DIR__ . '/x' );\n"
              . "define( 'V', \"has \$var\" ); define( 'U', 1_000 );\n";
        $editor = self::editor( $code );
        $expect = array(
            'T' => array( 'bool', true ), 'F' => array( 'bool', false ), 'N' => array( 'null', null ),
            'I' => array( 'int', 42 ), 'NEG' => array( 'int', -3 ), 'O' => array( 'octal', 0640 ), 'H' => array( 'int', 31 ),
            'FL' => array( 'float', 1.5 ), 'S' => array( 'string', "it's a \\ path" ), 'D' => array( 'string', "tab\there" ),
            'E' => array( 'expression', "__DIR__ . '/x'" ), 'V' => array( 'expression', '"has $var"' ), 'U' => array( 'int', 1000 ),
        );
        foreach ( $expect as $name => $typeValue )
        {
            $entry = $editor->find( $name );
            $this->assertSame( $typeValue[0], $entry['type'], $name );
            $this->assertSame( $typeValue[1], $entry['value'], $name );
        }
        $this->assertSame( '0640', expConfigPhpEditor::plainValue( $editor->find( 'O' ) ) );
        $this->assertSame( "__DIR__ . '/x'", expConfigPhpEditor::plainValue( $editor->find( 'E' ) ) );
        $this->assertSame( 'false', expConfigPhpEditor::plainValue( $editor->find( 'F' ) ) );
    }

    /** SC-03 */
    public function testSetChangesOnlyTheValue()
    {
        $code = "<?php\n// a comment that stays\ndefine(  'ODD'  ,  /* inline */  1  ) ;  // after\n\ndefine( 'OTHER', 'x' );\n/* the end */\n";
        $result = self::editor( $code )->withValue( 'ODD', '2' );
        $this->assertSame( 'changed', $result['how'] );
        $this->assertSame( 3, $result['line'] );
        $this->assertSame( str_replace( '/* inline */  1', '/* inline */  2', $code ), $result['code'] );
        $this->assertNull( expConfigPhpEditor::syntaxError( $result['code'] ) );

        $same = self::editor( "<?php\ndefine( 'A', false );\n" )->withValue( 'A', 'false' );
        $this->assertSame( 'unchanged', $same['how'] );
        $this->assertSame( "<?php\ndefine( 'A', false );\n", $same['code'] );

        $expression = self::editor( "<?php\ndefine( 'P', __DIR__ . '/x' );\n" )->withValue( 'P', "'/abs/x'" );
        $this->assertSame( "<?php\ndefine( 'P', '/abs/x' );\n", $expression['code'] );
    }

    /** SC-04 */
    public function testSetTakesTheCommentOffTheDocumentedLine()
    {
        $code = "<?php\n/* docs */\n    //define( 'X', 0750 );  // the note\n//  define( 'Y', 1 );\n//if ( \$a )\n//    define( 'Z', 1 );\n";
        $x = self::editor( $code )->withValue( 'X', '0700' );
        $this->assertSame( 'uncommented', $x['how'] );
        $this->assertStringContainsString( "\n    define( 'X', 0700 );  // the note\n", $x['code'], 'indentation and note stay' );
        $y = self::editor( $code )->withValue( 'Y', '2' );
        $this->assertStringContainsString( "\ndefine( 'Y', 2 );\n", $y['code'] );
        $z = self::editor( $code )->withValue( 'Z', '3' );
        $this->assertSame( 'added', $z['how'], 'a commented condition is never uncommented' );
        $this->assertStringContainsString( "//    define( 'Z', 1 );", $z['code'] );
        foreach ( array( $x, $y, $z ) as $result )
            $this->assertNull( expConfigPhpEditor::syntaxError( $result['code'] ) );
    }

    /** SC-05 */
    public function testSetAddsToTheBlock()
    {
        $code = "<?php\ndefine( 'A', 1 );\n\n/*\n  The cache\n*/\nif ( is_file( 'x' ) )\n    require 'x';\n?>\n";
        $first = self::editor( $code )->withValue( 'NEW_ONE', "'v'" );
        $this->assertSame( 'added', $first['how'] );
        $expected = "<?php\ndefine( 'A', 1 );\n\n" . expConfigPhpEditor::BLOCK_BEGIN . "\ndefine( 'NEW_ONE', 'v' );\n"
                  . expConfigPhpEditor::BLOCK_END . "\n\n/*\n  The cache\n*/\nif ( is_file( 'x' ) )\n    require 'x';\n?>\n";
        $this->assertSame( $expected, $first['code'], 'before the first statement that is no define, and before its comment' );
        $this->assertSame( 5, $first['line'], 'the line of the added define()' );

        $second = self::editor( $first['code'] )->withValue( 'NEW_TWO', 'true' );
        $this->assertStringContainsString( "define( 'NEW_ONE', 'v' );\ndefine( 'NEW_TWO', true );\n" . expConfigPhpEditor::BLOCK_END, $second['code'] );
        $this->assertTrue( self::editor( $second['code'] )->find( 'NEW_TWO' )['in_block'] );

        $this->assertSame( "<?php\n" . expConfigPhpEditor::BLOCK_BEGIN . "\ndefine( 'A', 1 );\n" . expConfigPhpEditor::BLOCK_END . "\n",
                           self::editor( '' )->withValue( 'A', '1' )['code'], 'an empty file' );
        $this->assertSame( "<?php\n" . expConfigPhpEditor::BLOCK_BEGIN . "\ndefine( 'A', 1 );\n" . expConfigPhpEditor::BLOCK_END . "\n",
                           self::editor( '<?php' )->withValue( 'A', '1' )['code'], 'only the open tag' );
        $closing = self::editor( "<?php\n// nothing\n?>" )->withValue( 'A', '1' )['code'];
        $this->assertSame( "<?php\n// nothing\n" . expConfigPhpEditor::BLOCK_BEGIN . "\ndefine( 'A', 1 );\n" . expConfigPhpEditor::BLOCK_END . "\n?>", $closing );
        foreach ( array( $first['code'], $second['code'], $closing ) as $result )
            $this->assertNull( expConfigPhpEditor::syntaxError( $result ) );
    }

    /** SC-06 */
    public function testUnset()
    {
        $code = "<?php\n/* docs */\ndefine( 'A', 1 );\n" . expConfigPhpEditor::BLOCK_BEGIN . "\ndefine( 'B', 2 );\n" . expConfigPhpEditor::BLOCK_END . "\n";
        $a = self::editor( $code )->without( 'A' );
        $this->assertSame( 'commented', $a['how'] );
        $this->assertStringContainsString( "/* docs */\n//define( 'A', 1 );\n", $a['code'] );
        $this->assertNull( self::editor( $a['code'] )->find( 'A' ) );
        $this->assertSame( 'uncommented', self::editor( $a['code'] )->withValue( 'A', '5' )['how'], 'set after unset finds the line again' );

        $b = self::editor( $code )->without( 'B' );
        $this->assertSame( 'removed', $b['how'] );
        $this->assertSame( "<?php\n/* docs */\ndefine( 'A', 1 );\n", $b['code'], 'the last one removed takes the empty block with it' );
        $two = self::editor( self::editor( $code )->withValue( 'C', '3' )['code'] )->without( 'B' );
        $this->assertStringContainsString( expConfigPhpEditor::BLOCK_BEGIN . "\ndefine( 'C', 3 );\n" . expConfigPhpEditor::BLOCK_END, $two['code'] );

        $multi = self::editor( "<?php\ndefine(\n    'M',\n    1\n);\n" )->without( 'M' );
        $this->assertSame( "<?php\n//define(\n//    'M',\n//    1\n//);\n", $multi['code'] );

        $this->assertSame( 'absent', self::editor( $code )->without( 'C' )['how'] );
        $this->assertArrayHasKey( 'error', self::editor( "<?php\nif ( 1 ) { define( 'A', 1 ); }\n" )->without( 'A' ) );
        $this->assertArrayHasKey( 'error', self::editor( "<?php\ndefine( 'A', 1 );\ndefine( 'A', 2 );\n" )->without( 'A' ) );
        $this->assertArrayHasKey( 'error', self::editor( "<?php\ndefine( 'A', 1 );\ndefine( 'A', 2 );\n" )->withValue( 'A', '3' ) );
        $this->assertArrayHasKey( 'error', self::editor( "<?php\nif ( 1 ) define( 'A', 1 );\n" )->withValue( 'A', '3' ) );
        foreach ( array( $a, $b, $multi ) as $result )
            $this->assertNull( expConfigPhpEditor::syntaxError( $result['code'] ) );
    }

    /** SC-07 */
    public function testCommandLineValuesAreTyped()
    {
        $t = function ( $text, $type = null, $doc = null ) { return expConfigPhpEditor::typedValue( $text, $type, $doc ); };
        $this->assertSame( array( 'type' => 'bool', 'value' => false ), $t( 'false', null, 'int' ), 'false is false for a number too' );
        $this->assertSame( array( 'type' => 'null', 'value' => null ), $t( 'NULL' ) );
        $this->assertSame( array( 'type' => 'int', 'value' => 3306 ), $t( '3306', null, 'int' ) );
        $this->assertSame( array( 'type' => 'string', 'value' => '3306' ), $t( '3306', null, 'string' ), 'a documented string stays a string' );
        $this->assertSame( array( 'type' => 'octal', 'value' => 0640 ), $t( '640', null, 'octal' ) );
        $this->assertSame( array( 'type' => 'octal', 'value' => 0640 ), $t( '0640', null, 'octal' ) );
        $this->assertSame( array( 'type' => 'octal', 'value' => 0640 ), $t( '0640' ), 'undocumented: a leading 0 is octal' );
        $this->assertSame( array( 'type' => 'int', 'value' => 12 ), $t( '12' ) );
        $this->assertSame( array( 'type' => 'float', 'value' => 1.5 ), $t( '1.5' ) );
        $this->assertSame( array( 'type' => 'string', 'value' => 'site.ini' ), $t( 'site.ini' ) );
        $this->assertSame( array( 'type' => 'string', 'value' => 'false' ), $t( 'false', 'string' ) );
        $this->assertSame( array( 'type' => 'string', 'value' => 'site.ini' ), $t( 'site.ini', 'string', 'bool' ) );
        $this->assertSame( array( 'type' => 'bool', 'value' => true ), $t( 'yes', 'bool' ) );
        $this->assertStringContainsString( 'is not true or false', $t( 'site.ini', null, 'bool' )['error'] );
        $this->assertStringContainsString( '--type=<type>', $t( 'site.ini', null, 'bool' )['error'] );
        $this->assertStringContainsString( 'is not an integer', $t( 'abc', null, 'int' )['error'] );
        $this->assertStringContainsString( '--type=octal', $t( '0640', 'int' )['error'] );
        $this->assertStringContainsString( 'not an octal number', $t( '0980', 'octal' )['error'] );
        $this->assertStringContainsString( 'unknown type', $t( '1', 'array' )['error'] );
        $this->assertSame( array( 'type' => 'string', 'value' => "/x" ), $t( '/x', null, 'expression' ) );
    }

    /** SC-08 */
    public function testLiteralsReadBackAsWritten()
    {
        $values = array( "it's", 'back\\slash', "both \\' mixed", '$not_a_var', "new\nline", '', 'ünïcödé' );
        foreach ( $values as $value )
        {
            $literal = expConfigPhpEditor::literal( $value, 'string' );
            $code = self::editor( "<?php\n//define( 'S', 'x' );\n" )->withValue( 'S', $literal )['code'];
            $this->assertNull( expConfigPhpEditor::syntaxError( $code ), $literal );
            $entry = self::editor( $code )->find( 'S' );
            $this->assertSame( 'string', $entry['type'] );
            $this->assertSame( $value, $entry['value'], $literal );
        }
        $this->assertSame( '0640', expConfigPhpEditor::literal( 0640, 'octal' ) );
        $this->assertSame( 'false', expConfigPhpEditor::literal( false, 'bool' ) );
        $this->assertSame( 'null', expConfigPhpEditor::literal( null, 'null' ) );
        $this->assertSame( '-3', expConfigPhpEditor::literal( -3, 'int' ) );
        $this->assertSame( '1.5', expConfigPhpEditor::literal( 1.5, 'float' ) );
    }

    /** SC-09 */
    public function testSyntaxErrorsAndSecrets()
    {
        $this->assertNull( expConfigPhpEditor::syntaxError( "<?php\ndefine( 'A', 1 );\n" ) );
        $this->assertNull( expConfigPhpEditor::syntaxError( '' ) );
        $this->assertStringContainsString( 'on line 3', (string)expConfigPhpEditor::syntaxError( "<?php\ndefine( 'A', 1 )\ndefine( 'B', 2 );\n" ) );
        foreach ( array( 'CLUSTER_STORAGE_PASS', 'MY_PASSWORD', 'API_SECRET', 'X_API_KEY', 'AUTH_TOKEN' ) as $name )
            $this->assertTrue( expConfigPhpEditor::isSecret( $name ), $name );
        foreach ( array( 'CLUSTER_STORAGE_HOST', 'EZP_INI_FILEMTIME_CHECK' ) as $name )
            $this->assertFalse( expConfigPhpEditor::isSecret( $name ), $name );
        $diff = "-// define( 'CLUSTER_STORAGE_PASS', 'old' );\n+define( 'CLUSTER_STORAGE_PASS', 'n3w-s3cret' );\n define( 'CLUSTER_STORAGE_USER', 'u' );";
        $masked = expConfigPhpEditor::maskSecrets( $diff );
        $this->assertStringNotContainsString( 'n3w-s3cret', $masked );
        $this->assertStringNotContainsString( "'old'", $masked );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_PASS', '********' );", $masked );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_USER', 'u' );", $masked );
    }

    // ------------------------------------------------------------------ catalogue

    /** SC-10 */
    public function testTheShippedCatalogue()
    {
        $file = eZSys::rootDir() . '/config.php-RECOMMENDED';
        $catalogue = expConfigPhpCatalogue::fromFile( $file );
        $settings = $catalogue->settings();
        $this->assertGreaterThanOrEqual( 30, count( $settings ) );
        $this->assertSame( 'INI FILES OPTIMIZATIONS', $settings['EZP_INI_FILEMTIME_CHECK']['section'] );
        $this->assertSame( 'bool', $settings['EZP_INI_FILEMTIME_CHECK']['type'] );
        $this->assertStringContainsString( 'eZINI should check', $settings['EZP_INI_FILEMTIME_CHECK']['description'] );
        $this->assertSame( 'FILE PERMISSIONS', $settings['EZP_FILE_MODE_MAX']['section'] );
        $this->assertSame( 'octal', $settings['EZP_FILE_MODE_MAX']['type'] );
        $this->assertSame( 'CLUSTERING', $settings['CLUSTER_STORAGE_PASS']['section'] );
        $this->assertSame( 'Cluster storage password', expConfigPhpCatalogue::summary( $settings['CLUSTER_STORAGE_PASS']['description'] ) );
        $this->assertSame( 'int', $settings['CLUSTER_STORAGE_PORT']['type'] );
        $this->assertTrue( $settings['EXP_REQUEST_PREPEND_FILE']['conditional'] );
        $this->assertSame( 'LOG HANDLING', $settings['EZPUBLISH_LOG_ROTATE_FILES']['section'] );
        foreach ( $settings as $name => $setting )
        {
            $this->assertNotSame( '', $setting['description'], "$name has a description" );
            $this->assertNotSame( '', $setting['section'], "$name has a section" );
        }

        // every documented setting can be set from the shipped file and reads back; the result compiles
        $code = (string)file_get_contents( $file );
        foreach ( $settings as $name => $setting )
        {
            $type = $setting['type'] === 'expression' ? 'string' : $setting['type'];
            $value = $setting['type'] === 'expression' ? '/srv/prepend.php' : $setting['value'];
            $result = self::editor( $code )->withValue( $name, expConfigPhpEditor::literal( $value, $type ) );
            $this->assertArrayNotHasKey( 'error', $result, $name );
            $this->assertSame( $setting['conditional'] ? 'added' : 'uncommented', $result['how'], $name );
            $this->assertNull( expConfigPhpEditor::syntaxError( $result['code'] ), $name );
            $entry = self::editor( $result['code'] )->find( $name );
            $this->assertSame( 'set', $entry['state'], $name );
            $this->assertSame( $value, $entry['value'], $name );
            $back = self::editor( $result['code'] )->without( $name );
            if ( $result['how'] === 'added' )
                $this->assertSame( $code, $back['code'], "$name: added and unset again gives the file back byte for byte" );
            $this->assertNull( self::editor( $back['code'] )->find( $name ), "$name unset again" );
        }
        $this->assertSame( array(), self::editor( $code )->definedNames(), 'the template sets nothing' );
    }

    // ------------------------------------------------------------------ copying and writing

    private function put( $name, $content, $mode = 0644 )
    {
        file_put_contents( $this->dir . '/' . $name, $content );
        chmod( $this->dir . '/' . $name, $mode );
    }

    /** SC-11 */
    public function testPlanAndCopy()
    {
        $this->assertSame( 'missing-source', expSetupFileCopy::plan( $this->dir, '.htaccess_root', '.htaccess' )['state'] );
        $this->put( '.htaccess_root', "RewriteEngine On\nRewriteRule .* index.php\n", 0604 );
        $plan = expSetupFileCopy::plan( $this->dir, '.htaccess_root', '.htaccess' );
        $this->assertSame( 'new', $plan['state'] );
        $result = expSetupFileCopy::apply( $this->dir, $plan, 'setup' );
        $this->assertTrue( $result['ok'] );
        $this->assertTrue( $result['created'] );
        $this->assertSame( '', $result['backup'] );
        $this->assertSame( 0604, fileperms( $this->dir . '/.htaccess' ) & 07777, 'a new file gets the mode of its template, as cp -a' );
        $this->assertFileEquals( $this->dir . '/.htaccess_root', $this->dir . '/.htaccess' );
        $this->assertSame( 'same', expSetupFileCopy::plan( $this->dir, '.htaccess_root', '.htaccess' )['state'] );
        $this->assertSame( 'root', expSetupFileCopy::htaccessState( $this->dir ) );

        $this->put( '.htaccess', "# changed by hand\nRewriteEngine On\nRewriteRule .* index.php\n", 0640 );
        $plan = expSetupFileCopy::plan( $this->dir, '.htaccess_root', '.htaccess' );
        $this->assertSame( 'differs', $plan['state'] );
        $this->assertStringContainsString( "-# changed by hand", $plan['diff'] );
        $this->assertStringContainsString( "--- .htaccess\n+++ .htaccess_root", $plan['diff'] );
        $this->assertSame( 'changed', expSetupFileCopy::htaccessState( $this->dir ) );
        $result = expSetupFileCopy::apply( $this->dir, $plan, 'setup' );
        $this->assertTrue( $result['ok'] );
        $this->assertFalse( $result['created'] );
        $this->assertSame( 0640, fileperms( $this->dir . '/.htaccess' ) & 07777, 'an existing file keeps its mode' );
        $this->assertFileEquals( $this->dir . '/.htaccess_root', $this->dir . '/.htaccess' );

        $result = expSetupFileCopy::write( $this->dir, $this->dir . '/.htaccess', "x\n", 'setup', array( 'mode' => '0600' ) );
        $this->assertSame( 0600, fileperms( $this->dir . '/.htaccess' ) & 07777, '--mode' );
        $this->assertFalse( expSetupFileCopy::write( $this->dir, $this->dir . '/.htaccess', "x\n", 'setup', array( 'mode' => '0999' ) )['ok'] );
        $this->assertStringContainsString( 'owner read and write', expSetupFileCopy::write( $this->dir, $this->dir . '/.htaccess', "x\n", 'setup', array( 'mode' => '0444' ) )['message'] );

        mkdir( $this->dir . '/config.php' );
        $this->put( 'config.php-RECOMMENDED', "<?php\n" );
        $this->assertSame( 'not-a-file', expSetupFileCopy::plan( $this->dir, 'config.php-RECOMMENDED', 'config.php' )['state'] );
        rmdir( $this->dir . '/config.php' );
        symlink( $this->dir . '/config.php-RECOMMENDED', $this->dir . '/config.php' );
        $this->assertSame( 'not-a-file', expSetupFileCopy::plan( $this->dir, 'config.php-RECOMMENDED', 'config.php' )['state'], 'a link is never replaced' );
        unlink( $this->dir . '/config.php' );

        $this->assertArrayHasKey( 'error', expSetupFileCopy::parseOwner( 'no-such-user-' . mt_rand() ) );
        $this->assertArrayHasKey( 'error', expSetupFileCopy::parseOwner( ':www-data' ) );
        $this->assertSame( 0, expSetupFileCopy::parseOwner( 'root' )['uid'] );
        if ( !expSetupFileCopy::isRoot() )
        {
            $refused = expSetupFileCopy::write( $this->dir, $this->dir . '/other', "x\n", 'setup', array( 'owner' => 'root' ) );
            $this->assertFalse( $refused['ok'] );
            $this->assertStringContainsString( 'only root', $refused['message'] );
        }
    }

    /** SC-12 */
    public function testBackups()
    {
        $this->put( 'config.php', "<?php\n// mine\n", 0640 );
        $path = expSetupFileCopy::backup( $this->dir, $this->dir . '/config.php', 'config', '20261029-120000' );
        $this->assertSame( $this->dir . '/var/backup/config/20261029-120000/config.php', $path );
        $this->assertFileEquals( $this->dir . '/config.php', $path );
        $this->assertSame( 0640, fileperms( $path ) & 07777, 'the mode is kept' );
        $this->assertSame( 0755, fileperms( $this->dir . '/var' ) & 07777, 'a new var/ is an ordinary directory' );
        foreach ( array( 'var/backup', 'var/backup/config', 'var/backup/config/20261029-120000' ) as $d )
            $this->assertSame( 0700, fileperms( $this->dir . '/' . $d ) & 07777, "$d is 0700" );

        $first = expSetupFileCopy::backup( $this->dir, $this->dir . '/config.php', 'config' );
        $second = expSetupFileCopy::backup( $this->dir, $this->dir . '/config.php', 'config' );
        $this->assertNotSame( $first, $second, 'two backups in one second do not overwrite each other' );

        $result = expSetupFileCopy::write( $this->dir, $this->dir . '/config.php', "<?php\n// new\n", 'config' );
        $this->assertTrue( $result['ok'] );
        $this->assertStringStartsWith( $this->dir . '/var/backup/config/', $result['backup'] );
        $this->assertSame( "<?php\n// mine\n", file_get_contents( $result['backup'] ) );
        $this->assertSame( "<?php\n// new\n", file_get_contents( $this->dir . '/config.php' ) );
    }

    /** SC-13 */
    public function testAFailedCheckChangesNothing()
    {
        $this->put( 'config.php', "<?php\ndefine( 'A', 1 );\n" );
        $result = expSetupFileCopy::write( $this->dir, $this->dir . '/config.php', "<?php\ndefine( 'A', 1 )\ndefine( 'B', 1 );\n", 'config', array(
            'check' => function ( $tmp ) { return expSetupFileCopy::lint( $tmp ); },
        ) );
        $this->assertFalse( $result['ok'] );
        $this->assertStringContainsString( 'nothing was changed', $result['message'] );
        $this->assertSame( "<?php\ndefine( 'A', 1 );\n", file_get_contents( $this->dir . '/config.php' ) );
        $this->assertSame( array(), glob( $this->dir . '/.config.php.exp-*' ), 'no temporary file is left' );

        $this->put( 'good.php', "<?php\necho 1;\n" );
        $this->assertNull( expSetupFileCopy::lint( $this->dir . '/good.php' ) );
        $this->put( 'bad.php', "<?php\necho 1\necho 2;\n" );
        $this->assertNotNull( expSetupFileCopy::lint( $this->dir . '/bad.php' ) );
    }

    // ------------------------------------------------------------------ the commands

    /** Runs bin/php/<script>.php with --root=<the throwaway root>; array( exit code, output, standard output only ). */
    private function runScript( $script, array $arguments )
    {
        $line = array_merge( array( PHP_BINARY, 'bin/php/' . $script . '.php', '--allow-root-user', '--root=' . $this->dir ), $arguments );
        $process = proc_open( $line, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, eZSys::rootDir(),
                              array( 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin' ) );
        $stdout = stream_get_contents( $pipes[1] );
        $out = $stdout . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $process ), $out, $stdout );
    }

    /** SC-14 */
    public function testTheCommands()
    {
        $this->put( '.htaccess_root', "RewriteEngine On\n" );
        $this->put( 'config.php-RECOMMENDED', "<?php\n/*\n  LOGS\n  ----\n  How many files.\n*/\n//define( 'CUSTOM_LOG_ROTATE_FILES', 3 );\n/**\n * Password\n */\n// define( 'CLUSTER_STORAGE_PASS', 'x' );\n" );

        list( $code, $out ) = $this->runScript( 'setupfiles', array( 'htaccess', '--apply', '--dry-run' ) );
        $this->assertSame( 2, $code, $out );
        $this->assertStringContainsString( '--apply and --dry-run exclude each other', $out );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'set', 'A', '1', '--apply', '--dry-run' ) );
        $this->assertSame( 2, $code, $out );

        list( $code, $out ) = $this->runScript( 'setupfiles', array( 'htaccess' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Would copy .htaccess_root to .htaccess', $out );
        $this->assertFileDoesNotExist( $this->dir . '/.htaccess', 'the dry run writes nothing' );
        list( $code, $out ) = $this->runScript( 'setupfiles', array( 'htaccess', '--apply', '-v' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( "'.htaccess_root' -> '.htaccess'", $out );
        $this->assertFileEquals( $this->dir . '/.htaccess_root', $this->dir . '/.htaccess' );

        $this->put( '.htaccess', "# mine\n" );
        list( $code, $out ) = $this->runScript( 'setupfiles', array( 'htaccess', '--apply' ) );
        $this->assertSame( 1, $code, $out );
        $this->assertStringContainsString( '--force --apply replaces it', $out );
        $this->assertStringContainsString( '-# mine', $out );
        $this->assertSame( "# mine\n", file_get_contents( $this->dir . '/.htaccess' ), 'refused without --force' );
        list( $code, $out ) = $this->runScript( 'setupfiles', array( 'htaccess', '--apply', '--force' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Backup: var/backup/setup/', $out );
        $this->assertCount( 1, glob( $this->dir . '/var/backup/setup/*/.htaccess' ) );

        list( $code, $out ) = $this->runScript( 'setupfiles', array( 'config', '--apply' ) );
        $this->assertSame( 0, $code, $out );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'set', 'CUSTOM_LOG_ROTATE_FILES', '5' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( "+define( 'CUSTOM_LOG_ROTATE_FILES', 5 );", $out );
        $this->assertStringContainsString( 'dry run', $out );
        $this->assertFileEquals( $this->dir . '/config.php-RECOMMENDED', $this->dir . '/config.php' );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'set', 'CUSTOM_LOG_ROTATE_FILES', '5', '--apply' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'checked with php -l', $out );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'get', 'CUSTOM_LOG_ROTATE_FILES' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertMatchesRegularExpression( '/^5$/m', $out, 'the plain value on a line of its own' );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'set', 'CLUSTER_STORAGE_PASS', 'Top-s3cret', '--apply' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringNotContainsString( 'Top-s3cret', $out, 'the secret is masked in the diff' );
        list( $code, $out, $stdout ) = $this->runScript( 'configphp', array( 'list', '--format=json' ) );
        $this->assertSame( 0, $code, $out );
        $json = json_decode( substr( $stdout, strpos( $stdout, '{' ) ), true );
        $this->assertSame( 2, count( $json['settings'] ) );
        $this->assertSame( '********', $json['settings'][1]['value'] );
        $this->assertSame( 'LOGS', $json['settings'][0]['section'] );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'get', 'CLUSTER_STORAGE_PASS', '--show-secrets' ) );
        $this->assertStringContainsString( 'Top-s3cret', $out );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'set', 'NOT_DOCUMENTED', '1' ) );
        $this->assertSame( 1, $code, $out );
        $this->assertStringContainsString( '--allow-unknown', $out );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'unset', 'CUSTOM_LOG_ROTATE_FILES', '--apply' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( "//define( 'CUSTOM_LOG_ROTATE_FILES', 5 );", file_get_contents( $this->dir . '/config.php' ) );
        $this->assertGreaterThanOrEqual( 3, count( glob( $this->dir . '/var/backup/config/*/config.php' ) ), 'a backup before each write' );

        $this->put( 'config.php', "<?php\ndefine( 'A', 1 )\ndefine( 'B', 2 );\n" );
        list( $code, $out ) = $this->runScript( 'configphp', array( 'set', 'CUSTOM_LOG_ROTATE_FILES', '5', '--apply' ) );
        $this->assertSame( 1, $code, $out );
        $this->assertStringContainsString( 'does not compile', $out );
        $this->assertSame( "<?php\ndefine( 'A', 1 )\ndefine( 'B', 2 );\n", file_get_contents( $this->dir . '/config.php' ) );
        list( $code, $out ) = $this->runScript( 'setupfiles', array( 'status' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'does not compile', $out );
    }

    /** SC-15 */
    public function testABrokenConfigPhpIsSkippedOnlyWhereTolerated()
    {
        $root = eZSys::rootDir();
        $autoload = (string)file_get_contents( $root . '/autoload.php' );
        $this->assertStringContainsString( "defined( 'EXP_CONFIG_PHP_TOLERATE' )", $autoload );
        $this->assertStringContainsString( "token_get_all( (string)file_get_contents( __DIR__ . '/config.php' ), TOKEN_PARSE );", $autoload );
        $this->assertStringContainsString( "if ( file_exists( __DIR__ . '/config.php' ) && !defined( 'EXP_CONFIG_PHP_SKIP' ) )", $autoload );
        foreach ( array( 'bin/php/console', 'bin/php/setupfiles.php', 'bin/php/configphp.php' ) as $file )
        {
            $code = (string)file_get_contents( $root . '/' . $file );
            $this->assertLessThan( strpos( $code, "require_once 'autoload.php';" ), strpos( $code, "define( 'EXP_CONFIG_PHP_TOLERATE', true );" ), $file );
        }
        foreach ( array( 'index.php', 'index_rest.php', 'runcronjobs.php', 'bin/php/ezcache.php' ) as $file )
            $this->assertStringNotContainsString( 'EXP_CONFIG_PHP_TOLERATE', (string)file_get_contents( $root . '/' . $file ), "$file reads config.php as before" );
    }

    /** SC-16 */
    public function testAliasesAndDocumentation()
    {
        $root = eZSys::rootDir();
        $setup = (string)file_get_contents( $root . '/kernel/private/classes/commands/setupfiles.php' );
        foreach ( array( 'exp:setup:htaccess', 'exp:setup:config', 'exp:setup:status' ) as $alias )
            $this->assertStringContainsString( ' * @alias ' . $alias . "\n", $setup );
        $config = (string)file_get_contents( $root . '/kernel/private/classes/commands/configphp.php' );
        foreach ( array( 'exp:config:list', 'exp:config:get', 'exp:config:set', 'exp:config:unset' ) as $alias )
            $this->assertStringContainsString( ' * @alias ' . $alias . "\n", $config );
        foreach ( array( $setup, $config ) as $code )
        {
            $this->assertStringContainsString( '$apply = self::applyRequested( $options );', $code );
            $this->assertStringContainsString( '$script->shutdown( 2 );', $code );
        }
        foreach ( array( 'doc/eng-us/guides/setup-files-from-the-console.md', 'doc/ger-de/anleitungen/setup-dateien-von-der-konsole.md',
                         'doc/eng-us/examples/commands/setupfiles.md', 'doc/eng-us/examples/commands/configphp.md',
                         'doc/ger-de/beispiele/befehle/setupfiles.md', 'doc/ger-de/beispiele/befehle/configphp.md' ) as $page )
            $this->assertFileExists( $root . '/' . $page );
    }
}
