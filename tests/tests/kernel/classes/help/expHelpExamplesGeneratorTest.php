<?php
/**
 * The generated reference examples (expHelpExamplesGenerator, ./console exp:help:examples), without a database.
 *
 *  HE-01  INI files are read with their comments: blocks, scalars, lists, hashes, the reset line
 *  HE-02  a command's help text and options are read from its code without running it
 *  HE-03  pages carry the help's front matter and the generated marker; the same sources give the same bytes
 *  HE-04  write() writes changed pages only and removes generated pages whose source is gone, never other files;
 *         compare() reports missing, different and stale pages
 *  HE-05  file names are safe; module views and policies come from module.php
 *  HE-06  comments quoted from shipped files are text (escaped), and the link check honors the escapes
 *  HE-07  pageTitles() (included by every admin page for its Help menu) leaves the generated pages out; pageCount()
 *         counts them all
 *  HE-08  a translation (expHelpExamplesTranslation) writes deterministic pages below doc/<language>/ with translated
 *         directories, fixed texts, descriptions and keywords, keeps INI keys and values, uses a description only
 *         while its English text is current, links the language's pages and lists them in its path map
 *  HE-09  the English pages are byte for byte those of the generator before translations, with or without a
 *         translation built in the same run
 *  HE-10  a source that asks eZINI while it is read (module.php, a template autoload file) gets the shipped settings
 *         only: no override, no extension outside the repository, nothing injected, no INI cache; eZINI is put back
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
        return new expHelpExamplesGenerator( $this->root, 'doc/eng-us/examples', array( 'bin/php/sample.php', 'kernel/sample/module.php', 'settings/sample.ini' ) );
    }

    /** HE-01 */
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

    /** HE-02 */
    public function testScriptHelpFromCode()
    {
        $help = expHelpExamplesGenerator::scriptHelp( (string)file_get_contents( $this->root . '/bin/php/sample.php' ) );
        $this->assertSame( "Sample\nsecond line", $help['description'] );
        $this->assertSame( array( 'dry-run', 'limit' ), array_keys( $help['options'] ) );
        $this->assertSame( array( 'value' => '', 'help' => 'Change nothing' ), $help['options']['dry-run'] );
        $this->assertSame( 'required', $help['options']['limit']['value'] );
        $this->assertSame( '[FILE]', $help['arguments'] );

        // a repeatable option ([exclude:*]) keeps its name, its value and its help
        $help = expHelpExamplesGenerator::scriptHelp( '<?php $script->startup(); $options = $script->getOptions( "[apply][exclude:*][file:+]", "", array( "exclude" => "Never these", "file" => "A file" ) );' );
        $this->assertSame( array( 'apply', 'exclude', 'file' ), array_keys( $help['options'] ) );
        $this->assertSame( array( 'value' => 'required', 'help' => 'Never these' ), $help['options']['exclude'] );
        $this->assertSame( array( 'value' => 'required', 'help' => 'A file' ), $help['options']['file'] );
    }

    /** HE-03 */
    public function testPagesAreDeterministicAndMarked()
    {
        $one = $this->generator()->build( array( 'ini-settings', 'commands' ) );
        $two = $this->generator()->build( array( 'ini-settings', 'commands' ) );
        $this->assertSame( $one, $two );
        $this->assertArrayHasKey( 'doc/eng-us/examples/ini-settings/sample/First.md', $one );
        $this->assertArrayHasKey( 'doc/eng-us/examples/ini-settings/sample/README.md', $one );
        $this->assertArrayHasKey( 'doc/eng-us/examples/commands/sample.md', $one );
        $page = $one['doc/eng-us/examples/ini-settings/sample/First.md'];
        $this->assertStringStartsWith( "---\ntitle: sample.ini [First]\n", $page );
        $this->assertStringContainsString( "\ntype: generated\ngenerated: exp:help:examples\n---\n", $page );
        $this->assertStringContainsString( "## Count\n", $page );
        $this->assertStringContainsString( "Count=5\n", $page );
        $this->assertStringContainsString( "Names[]\nNames[]=a\nNames[]=b\n", $page );
        $command = $one['doc/eng-us/examples/commands/sample.md'];
        $this->assertStringContainsString( './console exp:sample:run --help', $command );
        $this->assertStringContainsString( '| `--limit` | required | At most this many |', $command );
        $this->assertDoesNotMatchRegularExpression( '/\b20\d\d-\d\d-\d\d\b/', implode( '', $one ), 'no dates' );
    }

    /** HE-04 */
    public function testWriteCompareAndStale()
    {
        $generator = $this->generator();
        $pages = $generator->build( array( 'commands' ) );
        $stats = $generator->write( $pages, array( 'commands' ) );
        $this->assertSame( count( $pages ), $stats['written'] );
        $this->assertSame( 0, $generator->write( $pages, array( 'commands' ) )['written'], 'nothing changed, nothing written' );

        $dir = $this->root . '/doc/eng-us/examples/commands';
        file_put_contents( "$dir/gone.md", "---\ntitle: Gone\ntype: generated\ngenerated: exp:help:examples\n---\n# Gone\n" );
        file_put_contents( "$dir/mine.md", "---\ntitle: Mine\n---\n# Written by hand\n" );
        $diff = $generator->compare( $pages, array( 'commands' ) );
        $this->assertSame( array( 'doc/eng-us/examples/commands/gone.md' ), $diff['stale'] );
        $this->assertSame( array(), $diff['missing'] );

        file_put_contents( "$dir/sample.md", 'edited' );
        $this->assertSame( array( 'doc/eng-us/examples/commands/sample.md' ), $generator->compare( $pages, array( 'commands' ) )['different'] );

        $stats = $generator->write( $pages, array( 'commands' ) );
        $this->assertSame( 1, $stats['removed'] );
        $this->assertFileDoesNotExist( "$dir/gone.md" );
        $this->assertFileExists( "$dir/mine.md", 'a page without the marker is never removed' );
        $this->assertSame( $pages['doc/eng-us/examples/commands/sample.md'], file_get_contents( "$dir/sample.md" ) );
    }

    /** HE-05 */
    public function testModuleViewsPoliciesAndFileNames()
    {
        $this->assertSame( 'CronjobPart-infrequent', expHelpExamplesGenerator::fileName( 'CronjobPart-infrequent' ) );
        $this->assertSame( 'a-b', expHelpExamplesGenerator::fileName( '../a b' ) );
        $this->assertSame( 'item', expHelpExamplesGenerator::fileName( '..' ) );

        $pages = $this->generator()->build( array( 'module-views', 'policies' ) );
        $show = $pages['doc/eng-us/examples/module-views/sample/show.md'];
        $this->assertStringContainsString( "/sample/show/<ItemID>/(offset)/<Offset>\n", $show );
        $this->assertStringContainsString( '[`sample/read`](../../policies/sample.md#read)', $show );
        $open = $pages['doc/eng-us/examples/module-views/sample/open.md'];
        $this->assertStringContainsString( 'a policy for the whole module: `sample/*`', $open );
        $policies = $pages['doc/eng-us/examples/policies/sample.md'];
        $this->assertStringContainsString( '| Section | `eZSection::fetchList()` |', $policies );
        $this->assertStringContainsString( 'Checked by the views: [show]', $policies );
    }

    /** HE-06 a comment quoted from a shipped file is text: its Markdown characters are escaped, the link check skips them */
    public function testQuotedCommentsAreText()
    {
        $text = "[Block]\n# load it with {ezscript[_require]('some.js')} or *this*\nName=1\n";
        file_put_contents( $this->root . '/settings/sample.ini', $text );
        $pages = $this->generator()->build( array( 'ini-settings' ) );
        $page = $pages['doc/eng-us/examples/ini-settings/sample/Block.md'];
        $this->assertStringContainsString( "> load it with {ezscript\\[\\_require\\]('some.js')} or \\*this\\*", $page );
        if ( !class_exists( 'expHelpLinkCheck' ) )
            require_once __DIR__ . '/../../../../../kernel/classes/help/exphelplinkcheck.php';
        $targets = array_map( function ( $l ) { return $l['target']; }, expHelpLinkCheck::links( $page ) );
        $this->assertNotContains( "'some.js'", $targets, 'the comment holds no link' );
        $this->assertContains( 'README.md', $targets, 'the page\'s own links are found' );
        $this->assertCount( 1, expHelpLinkCheck::links( "[a](b.md) \\[c\\](d.md)" ) );
    }

    /**
     * A German translation of the fixture: tables as doc/ger-de/.examples/ holds them.
     *
     * @return expHelpExamplesTranslation
     */
    protected function german( array $descriptions = array() )
    {
        if ( !class_exists( 'expHelpExamplesTranslation' ) )
            require_once __DIR__ . '/../../../../../kernel/classes/help/exphelpexamplestranslation.php';
        return new expHelpExamplesTranslation( 'ger-de', array(
            'strings'      => array( '## Related' => '## Siehe auch', 'Default: {value}.' => 'Standard: {value}.',
                                     '- [All blocks of {ini}](README.md)' => '- [Alle Blöcke von {ini}](README.md)',
                                     '- [The settings page](../../../guides/settings-page.md)' => '- [Die Einstellungsseite](../../../guides/settings-page.md)',
                                     '- [All console commands](README.md)' => '- [Alle Konsolenbefehle](README.md)' ),
            'keywords'     => array( 'setting' => 'Einstellung' ),
            'descriptions' => $descriptions,
            'dirnames'     => array( 'examples' => 'beispiele', 'ini-settings' => 'ini-einstellungen', 'commands' => 'befehle', 'guides' => 'anleitungen' ),
            'paths'        => array( 'anleitungen/einstellungsseite.md' => 'guides/settings-page.md' ),
        ) );
    }

    /** HE-08 a translation: deterministic pages below doc/ger-de/, translated text, local links and path map entries */
    public function testGermanPagesAreDeterministicAndTranslated()
    {
        $descriptions = array(
            'ini:settings/sample.ini [First] Count' => array( 'en' => 'How many', 'de' => 'Wie viele' ),
            // the source text changed since it was translated: the English text is shown
            'ini:settings/sample.ini [First] Names' => array( 'en' => 'Names, in an old order', 'de' => 'Namen, veraltet' ),
            // an item without an entry of its own takes the translation of the same English text
            'ini:settings/other.ini [X] Y' => array( 'en' => 'The first block', 'de' => 'Der erste Block' ),
        );
        $make = function () use ( $descriptions )
        {
            return new expHelpExamplesGenerator( $this->root, null, array( 'bin/php/sample.php', 'kernel/sample/module.php', 'settings/sample.ini' ), $this->german( $descriptions ) );
        };
        $generator = $make();
        $one = $generator->build( array( 'ini-settings', 'commands' ) );
        $two = $make()->build( array( 'ini-settings', 'commands' ) );
        $this->assertSame( $one, $two, 'the same sources and tables give the same bytes' );
        $this->assertSame( 'doc/ger-de/beispiele', $generator->outputDirectory() );

        $page = $one['doc/ger-de/beispiele/ini-einstellungen/sample/First.md'];
        $this->assertStringContainsString( "> Wie viele  \n", $page );
        $this->assertStringContainsString( "> Names, in order  \n", $page, 'a stale translation is not used' );
        $this->assertStringContainsString( "> Der erste Block  \n", $page, 'the translation of the same text is reused' );
        $this->assertStringContainsString( "Standard: `5`.\n", $page );
        $this->assertStringContainsString( "Count=5\n", $page, 'INI keys and values stay as they are' );
        $this->assertStringContainsString( "## Siehe auch\n", $page );
        $this->assertStringContainsString( '- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)', $page, 'a translated page is linked' );
        $this->assertStringContainsString( 'keywords: Einstellung, sample.ini', $page, 'the German keywords come first' );
        $this->assertStringContainsString( ', setting, ', $page, 'the English keywords stay' );
        // a page the translation does not have links the English file
        $this->assertStringContainsString( '](../../../../eng-us/examples/README.md#how-the-generated-pages-are-made)', $page );

        $missing = $generator->translation()->missingDescriptions();
        $this->assertArrayHasKey( 'ini:settings/sample.ini [First] Names', $missing );
        $this->assertArrayNotHasKey( 'ini:settings/sample.ini [First] Count', $missing );
        $this->assertContains( '## Help text', $generator->translation()->missingStrings(), 'a text the table lacks is noted and shown in English' );
        $this->assertStringContainsString( "## Help text\n", $one['doc/ger-de/beispiele/befehle/sample.md'] );

        $map = $generator->pathMap();
        $this->assertSame( 'examples/ini-settings/sample/First.md', $map['beispiele/ini-einstellungen/sample/First.md'] );
        $this->assertSame( 'examples/commands/sample.md', $map['beispiele/befehle/sample.md'] );
        $this->assertCount( count( $one ), $map );
    }

    /** HE-09 the English pages are the same with or without a translation in the same run */
    public function testEnglishOutputUnchangedByTranslation()
    {
        $before = $this->generator()->build();
        $german = new expHelpExamplesGenerator( $this->root, null, array( 'bin/php/sample.php', 'kernel/sample/module.php', 'settings/sample.ini' ), $this->german() );
        $german->build();
        $after = $this->generator()->build();
        $this->assertSame( $before, $after );
        $this->assertSame( array(), $this->generator()->pathMap(), 'the original has no path map entries' );
        // the English page as the generator wrote it before translations existed
        $this->assertSame( self::ENGLISH_FIRST_PAGE_SHA1, sha1( $before['doc/eng-us/examples/ini-settings/sample/First.md'] ) );
        $this->assertSame( self::ENGLISH_PAGES_SHA1, sha1( serialize( $before ) ) );
    }

    /** sha1 of the fixture's English pages, built with the generator of before the translations (6.0.15) */
    const ENGLISH_FIRST_PAGE_SHA1 = '9e51fd7b90d1b1a57e16ec7925d390aeb12095c3';
    const ENGLISH_PAGES_SHA1 = 'b646d6a31b50120e40f7abb92ecc3959df7cbb11';

    /** HE-10 a source that asks eZINI while it is read gets the shipped settings only, and eZINI is put back afterwards */
    public function testSourcesReadTheShippedSettingsOnly()
    {
        if ( !class_exists( 'eZINI' ) )
            $this->markTestSkipped( 'eZINI is not loadable here' );
        $files = array(
            'extension/helpsample/settings/helpsample.ini' => "[Sample]\nValues[]\nValues[]=shipped\n",
            'kernel/inisample/module.php' => "<?php\n\$Module = array( 'name' => 'Inisample' );\n\$ViewList = array();\n\$values = array();\nforeach ( (array)eZINI::instance( 'helpsample.ini' )->variable( 'Sample', 'Values' ) as \$value )\n    \$values[] = array( 'Name' => \$value, 'value' => \$value );\n\$FunctionList = array( 'use' => array( 'Kind' => array( 'name' => 'Kind', 'values' => \$values ) ) );\n",
        );
        foreach ( $files as $path => $content )
        {
            mkdir( dirname( $this->root . '/' . $path ), 0777, true );
            file_put_contents( $this->root . '/' . $path, $content );
        }
        $generator = new expHelpExamplesGenerator( $this->root, 'doc/eng-us/examples', array_keys( $files ) );

        // an installation's own value (an override, an active extension outside the repository) is not shipped
        eZINI::injectMergeSettings( array( 'helpsample.ini' => array( 'Sample' => array( 'Values' => array( 'local' ) ) ) ) );
        try
        {
            $before = eZINI::instance( 'helpsample.ini' );
            $pages = $generator->build( array( 'policies' ) );
            $policy = $pages['doc/eng-us/examples/policies/inisample.md'];
            $this->assertStringContainsString( '| Kind | fixed: shipped (shipped) |', $policy );
            $this->assertStringNotContainsString( 'local', $policy );
            // the instances built before are the ones handed out after
            $this->assertSame( $before, eZINI::instance( 'helpsample.ini' ) );
            $this->assertSame( $pages, $generator->build( array( 'policies' ) ) );
        }
        finally
        {
            eZINI::injectMergeSettings( array() );
            eZINI::resetInstance( 'helpsample.ini' );
        }
    }

    /** HE-07 the page titles every admin page includes for its Help menu leave the generated pages out */
    public function testPageTitlesLeaveGeneratedPagesOut()
    {
        if ( !class_exists( 'expHelpDocumentIndex' ) )
            $this->markTestSkipped( 'expHelpDocumentIndex is not loadable here' );
        $dir = $this->root . '/cache';
        mkdir( $dir );
        $index = new expHelpDocumentIndex( null, array( 'cache_dir' => $dir ) );
        $data = new ReflectionProperty( 'expHelpDocumentIndex', 'data' );
        if ( PHP_VERSION_ID < 80100 )
            $data->setAccessible( true );
        $data->setValue( $index, array( 'signature' => 'sig', 'documents' => array(
            'help/start.md' => array( 'title' => 'Start', 'type' => 'help' ),
            'examples/README.md' => array( 'title' => 'Examples', 'type' => 'example' ),
            'examples/datatypes/ezstring.md' => array( 'title' => 'ezstring datatype', 'type' => 'generated' ),
        ) ) );
        $store = new ReflectionMethod( 'expHelpDocumentIndex', 'storePageTitles' );
        if ( PHP_VERSION_ID < 80100 )
            $store->setAccessible( true );
        $this->assertTrue( $store->invoke( $index ) );
        $this->assertSame( array( 'help/start.md' => 'Start', 'examples/README.md' => 'Examples' ), expHelpDocumentIndex::pageTitles( $dir ) );
        // the number of pages counts them all, as ./console exp:help index does (the system card shows it)
        $this->assertSame( array( 'pages' => 3, 'signature' => 'sig' ), expHelpDocumentIndex::pageCount( $dir ) );
        // a list written before it carried the number is written again, even for the same index
        file_put_contents( $dir . '/pages.php', '<?php return ' . var_export( array( 'signature' => 'sig', 'titles' => array() ), true ) . ';' );
        if ( function_exists( 'opcache_invalidate' ) )
            opcache_invalidate( $dir . '/pages.php', true );
        $this->assertNull( expHelpDocumentIndex::pageCount( $dir ) );
        $this->assertTrue( $store->invoke( $index ) );
        $this->assertSame( 3, expHelpDocumentIndex::pageCount( $dir )['pages'] );
    }
}
