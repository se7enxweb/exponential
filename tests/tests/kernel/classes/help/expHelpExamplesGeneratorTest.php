<?php
/**
 * The generated reference examples (expHelpExamplesGenerator, ./console exp:help:examples), without a database.
 *
 *  HX-01  INI files are read with their comments: blocks, scalars, lists, hashes, the reset line
 *  HX-02  a command's help text and options are read from its code without running it
 *  HX-03  pages carry the help's front matter and the generated marker; the same sources give the same bytes
 *  HX-04  write() writes changed pages only and removes generated pages whose source is gone, never other files;
 *         compare() reports missing, different and stale pages
 *  HX-05  file names are safe; module views and policies come from module.php
 *  HX-06  comments quoted from shipped files are text (escaped), and the link check honors the escapes
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// a checkout whose class map was built before the class existed
if ( !class_exists( 'expHelpExamplesGenerator' ) )
    require_once __DIR__ . '/../../../../../kernel/classes/help/exphelpexamplesgenerator.php';

class expHelpExamplesGeneratorTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/exphelpexamples-' . getmypid() . '-' . mt_rand();
        $files = array(
            'settings/sample.ini' => "#?ini charset=\"utf-8\"?\n# The sample file.\n\n# The first block\n[First]\n# How many\nCount=5\nEmpty=\n# Names, in order\nNames[]\nNames[]=a\nNames[]=b\n\n[Second]\nMap[]\nMap[x]=1\nMap[y]=2\n",
            'bin/php/sample.php' => "<?php\n/**\n * @description Does the sample thing.\n * @alias exp:sample:run\n */\n\$script = eZScript::instance( array( 'description' => \"Sample\\n\" .\n    'second line', 'use-session' => false ) );\n\$options = \$script->getOptions( '[dry-run][limit:]', '[FILE]', array( 'dry-run' => 'Change nothing', 'limit' => 'At most this many' ) );\n",
            'kernel/sample/module.php' => "<?php\n\$Module = array( 'name' => 'Sample' );\n\$ViewList = array();\n\$ViewList['show'] = array( 'script' => 'show.php', 'functions' => array( 'read' ), 'params' => array( 'ItemID' ), 'unordered_params' => array( 'offset' => 'Offset' ) );\n\$ViewList['open'] = array( 'script' => 'open.php', 'params' => array() );\n\$FunctionList = array( 'read' => array( 'Section' => array( 'name' => 'Section', 'values' => array(), 'class' => 'eZSection', 'function' => 'fetchList' ) ) );\n",
        );
        foreach ( $files as $path => $content )
        {
            if ( !is_dir( dirname( $this->root . '/' . $path ) ) )
                mkdir( dirname( $this->root . '/' . $path ), 0777, true );
            file_put_contents( $this->root . '/' . $path, $content );
        }
    }

    protected function tearDown(): void
    {
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $iterator as $file )
            $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
        rmdir( $this->root );
    }

    protected function generator()
    {
        return new expHelpExamplesGenerator( $this->root, 'doc/examples', array( 'bin/php/sample.php', 'kernel/sample/module.php', 'settings/sample.ini' ) );
    }

    /** HX-01 */
    public function testIniParsing()
    {
        $ini = expHelpExamplesGenerator::parseIniText( (string)file_get_contents( $this->root . '/settings/sample.ini' ) );
        $this->assertSame( 'The sample file.', $ini['comment'] );
        $this->assertSame( array( 'First', 'Second' ), array_keys( $ini['blocks'] ) );
        $this->assertSame( 'The first block', $ini['blocks']['First']['comment'] );
        $first = $ini['blocks']['First']['settings'];
        $this->assertSame( array( 'kind' => 'scalar', 'value' => '5', 'items' => array(), 'reset' => false, 'comment' => 'How many' ), $first['Count'] );
        $this->assertSame( '', $first['Empty']['value'] );
        $this->assertSame( 'list', $first['Names']['kind'] );
        $this->assertTrue( $first['Names']['reset'] );
        $this->assertSame( array( array( null, 'a' ), array( null, 'b' ) ), $first['Names']['items'] );
        $this->assertSame( 'hash', $ini['blocks']['Second']['settings']['Map']['kind'] );
        $this->assertSame( array( array( 'x', '1' ), array( 'y', '2' ) ), $ini['blocks']['Second']['settings']['Map']['items'] );
    }

    /** HX-02 */
    public function testScriptHelpFromCode()
    {
        $help = expHelpExamplesGenerator::scriptHelp( (string)file_get_contents( $this->root . '/bin/php/sample.php' ) );
        $this->assertSame( "Sample\nsecond line", $help['description'] );
        $this->assertSame( array( 'dry-run', 'limit' ), array_keys( $help['options'] ) );
        $this->assertSame( array( 'value' => '', 'help' => 'Change nothing' ), $help['options']['dry-run'] );
        $this->assertSame( 'required', $help['options']['limit']['value'] );
        $this->assertSame( '[FILE]', $help['arguments'] );
    }

    /** HX-03 */
    public function testPagesAreDeterministicAndMarked()
    {
        $one = $this->generator()->build( array( 'ini-settings', 'commands' ) );
        $two = $this->generator()->build( array( 'ini-settings', 'commands' ) );
        $this->assertSame( $one, $two );
        $this->assertArrayHasKey( 'doc/examples/ini-settings/sample/First.md', $one );
        $this->assertArrayHasKey( 'doc/examples/ini-settings/sample/README.md', $one );
        $this->assertArrayHasKey( 'doc/examples/commands/sample.md', $one );
        $page = $one['doc/examples/ini-settings/sample/First.md'];
        $this->assertStringStartsWith( "---\ntitle: sample.ini [First]\n", $page );
        $this->assertStringContainsString( "\ntype: generated\ngenerated: exp:help:examples\n---\n", $page );
        $this->assertStringContainsString( "## Count\n", $page );
        $this->assertStringContainsString( "Count=5\n", $page );
        $this->assertStringContainsString( "Names[]\nNames[]=a\nNames[]=b\n", $page );
        $command = $one['doc/examples/commands/sample.md'];
        $this->assertStringContainsString( './console exp:sample:run --help', $command );
        $this->assertStringContainsString( '| `--limit` | required | At most this many |', $command );
        $this->assertDoesNotMatchRegularExpression( '/\b20\d\d-\d\d-\d\d\b/', implode( '', $one ), 'no dates' );
    }

    /** HX-04 */
    public function testWriteCompareAndStale()
    {
        $generator = $this->generator();
        $pages = $generator->build( array( 'commands' ) );
        $stats = $generator->write( $pages, array( 'commands' ) );
        $this->assertSame( count( $pages ), $stats['written'] );
        $this->assertSame( 0, $generator->write( $pages, array( 'commands' ) )['written'], 'nothing changed, nothing written' );

        $dir = $this->root . '/doc/examples/commands';
        file_put_contents( "$dir/gone.md", "---\ntitle: Gone\ntype: generated\ngenerated: exp:help:examples\n---\n# Gone\n" );
        file_put_contents( "$dir/mine.md", "---\ntitle: Mine\n---\n# Written by hand\n" );
        $diff = $generator->compare( $pages, array( 'commands' ) );
        $this->assertSame( array( 'doc/examples/commands/gone.md' ), $diff['stale'] );
        $this->assertSame( array(), $diff['missing'] );

        file_put_contents( "$dir/sample.md", 'edited' );
        $this->assertSame( array( 'doc/examples/commands/sample.md' ), $generator->compare( $pages, array( 'commands' ) )['different'] );

        $stats = $generator->write( $pages, array( 'commands' ) );
        $this->assertSame( 1, $stats['removed'] );
        $this->assertFileDoesNotExist( "$dir/gone.md" );
        $this->assertFileExists( "$dir/mine.md", 'a page without the marker is never removed' );
        $this->assertSame( $pages['doc/examples/commands/sample.md'], file_get_contents( "$dir/sample.md" ) );
    }

    /** HX-05 */
    public function testModuleViewsPoliciesAndFileNames()
    {
        $this->assertSame( 'CronjobPart-infrequent', expHelpExamplesGenerator::fileName( 'CronjobPart-infrequent' ) );
        $this->assertSame( 'a-b', expHelpExamplesGenerator::fileName( '../a b' ) );
        $this->assertSame( 'item', expHelpExamplesGenerator::fileName( '..' ) );

        $pages = $this->generator()->build( array( 'module-views', 'policies' ) );
        $show = $pages['doc/examples/module-views/sample/show.md'];
        $this->assertStringContainsString( "/sample/show/<ItemID>/(offset)/<Offset>\n", $show );
        $this->assertStringContainsString( '[`sample/read`](../../policies/sample.md#read)', $show );
        $open = $pages['doc/examples/module-views/sample/open.md'];
        $this->assertStringContainsString( 'a policy for the whole module: `sample/*`', $open );
        $policies = $pages['doc/examples/policies/sample.md'];
        $this->assertStringContainsString( '| Section | `eZSection::fetchList()` |', $policies );
        $this->assertStringContainsString( 'Checked by the views: [show]', $policies );
    }

    /** HX-06 a comment quoted from a shipped file is text: its Markdown characters are escaped, the link check skips them */
    public function testQuotedCommentsAreText()
    {
        $text = "[Block]\n# load it with {ezscript[_require]('some.js')} or *this*\nName=1\n";
        file_put_contents( $this->root . '/settings/sample.ini', $text );
        $pages = $this->generator()->build( array( 'ini-settings' ) );
        $page = $pages['doc/examples/ini-settings/sample/Block.md'];
        $this->assertStringContainsString( "> load it with {ezscript\\[\\_require\\]('some.js')} or \\*this\\*", $page );
        if ( !class_exists( 'expHelpLinkCheck' ) )
            require_once __DIR__ . '/../../../../../kernel/classes/help/exphelplinkcheck.php';
        $targets = array_map( function ( $l ) { return $l['target']; }, expHelpLinkCheck::links( $page ) );
        $this->assertNotContains( "'some.js'", $targets, 'the comment holds no link' );
        $this->assertContains( 'README.md', $targets, 'the page\'s own links are found' );
        $this->assertCount( 1, expHelpLinkCheck::links( "[a](b.md) \\[c\\](d.md)" ) );
    }
}
