<?php
/**
 * eZAutoloadGenerator (bin/php/ezpgenerateautoloads.php) on an extension made for the test: the run mode chosen by
 * the options, which files are scanned (tests, settings and the .autoloadignore entries left out, blank lines and
 * comments in it ignored; the tests/ of every extension and of every package vendored in one left out of the
 * extension and kernel override arrays, also under a relative extension root, and only that directory, never
 * tests_old/), which declarations become entries (classes, interfaces, traits and enums, namespaced or not, never
 * "Foo::class" or an anonymous class), duplicates and kernel classes refused with a warning, the written file, the
 * option checks and the tests autoload (eZAutoloadGenerator::testsClassMap(), ezpAutoloader::enableTests()).
 *
 * No database. The extension lives under var/tmp and is removed in tearDown(); the autoload files are written only
 * there.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZAutoloadGeneratorTestGenerator extends eZAutoloadGenerator
{
    /** @var array|null extension roots used instead of the INI setting, when set */
    public $roots = null;

    protected function getExtensionRoots()
    {
        return $this->roots !== null ? $this->roots : parent::getExtensionRoots();
    }

    public function arrays()
    {
        return $this->autoloadArrays;
    }

    public function mask()
    {
        return $this->mask;
    }

    public function files()
    {
        return $this->fetchFiles();
    }

    public function classes( array $files, $mode )
    {
        return $this->getClassFileList( $files, $mode );
    }
}

class eZAutoloadGeneratorTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $name;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->name = 'k1autoload' . getmypid() . mt_rand( 1000, 9999 );
        $this->dir = 'var/tmp/phpunit-k1b-autoload-' . getmypid() . '-' . mt_rand();
        mkdir( "$this->dir/$this->name", 0755, true );
    }

    protected function tearDown(): void
    {
        if ( is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    private function file( $path, $code )
    {
        $full = "$this->dir/$this->name/$path";
        if ( !is_dir( dirname( $full ) ) )
            mkdir( dirname( $full ), 0755, true );
        file_put_contents( $full, $code );
    }

    private function generator( array $options = array() )
    {
        $options += array( 'basePath' => "$this->dir/$this->name", 'writeFiles' => false );
        $generator = new eZAutoloadGeneratorTestGenerator( new ezpAutoloadGeneratorOptions( $options ) );
        return $generator;
    }

    private function generated( array $options = array() )
    {
        $generator = $this->generator( $options );
        $generator->buildAutoloadArrays();
        $arrays = $generator->arrays();
        $entries = array();
        if ( isset( $arrays[eZAutoloadGenerator::MODE_SINGLE_EXTENSION] ) )
        {
            preg_match_all( "/'(.+?)'\s+=> '(.+?)',/", $arrays[eZAutoloadGenerator::MODE_SINGLE_EXTENSION], $m, PREG_SET_ORDER );
            foreach ( $m as $match )
                $entries[stripslashes( $match[1] )] = $match[2];
        }
        return array( $generator, $entries );
    }

    // ---------------------------------------------------------------- modes

    public function testModeFollowsTheOptions()
    {
        $cwd = getcwd();
        $this->assertSame( eZAutoloadGenerator::MODE_SINGLE_EXTENSION, $this->generator()->mask() );
        $this->assertSame( eZAutoloadGenerator::MODE_EXTENSION, $this->generator( array( 'basePath' => $cwd ) )->mask() );
        $this->assertSame( eZAutoloadGenerator::MODE_EXTENSION,
                           $this->generator( array( 'basePath' => $cwd, 'searchExtensionFiles' => false ) )->mask(), 'nothing chosen: extensions' );
        $this->assertSame( eZAutoloadGenerator::MODE_KERNEL | eZAutoloadGenerator::MODE_EXTENSION | eZAutoloadGenerator::MODE_TESTS,
                           $this->generator( array( 'basePath' => $cwd, 'searchKernelFiles' => true, 'searchTestFiles' => true ) )->mask() );
        $this->assertSame( eZAutoloadGenerator::MODE_KERNEL_OVERRIDE,
                           $this->generator( array( 'basePath' => $cwd, 'searchKernelOverride' => true, 'searchKernelFiles' => true ) )->mask() );
        $generator = $this->generator( array( 'basePath' => $cwd ) );
        $generator->setMode( eZAutoloadGenerator::MODE_TESTS );
        $this->assertSame( eZAutoloadGenerator::MODE_TESTS, $generator->mask() );
        $generator->setMode( 0 );
        $generator->setMode( 'x' );
        $this->assertSame( eZAutoloadGenerator::MODE_TESTS, $generator->mask() );
    }

    public function testOptionsAreChecked()
    {
        $options = new ezpAutoloadGeneratorOptions();
        $this->assertSame( getcwd(), $options->basePath );
        $this->assertTrue( $options->searchExtensionFiles );
        $this->assertTrue( $options->writeFiles );
        foreach ( array( array( 'basePath', 1 ), array( 'outputDir', array() ), array( 'writeFiles', 'yes' ), array( 'excludeDirs', 'a' ) ) as $bad )
        {
            try
            {
                $options->{$bad[0]} = $bad[1];
                $this->fail( "{$bad[0]} accepted a wrong type" );
            }
            catch ( ezcBaseValueException $e )
            {
            }
        }
        $this->expectException( 'ezcBasePropertyNotFoundException' );
        $options->k1NoSuchOption = true;
    }

    // ---------------------------------------------------------------- declarations

    public function testDeclarationsBecomeEntries()
    {
        $p = $this->name;
        $this->file( 'classes/plain.php', "<?php\nclass {$p}Plain {}\nfinal class {$p}Final {}\nabstract class {$p}Abstract {}\ninterface {$p}Interface {}\ntrait {$p}Trait {}\n" );
        $this->file( 'classes/ns.php', "<?php\nnamespace K1\\{$p}\\Sub;\nclass Inside { function f() { return Other::class; } }\n" );
        $this->file( 'classes/braced.php', "<?php\nnamespace K1\\{$p}Braced {\n    class InBraces {}\n}\n" );
        $this->file( 'classes/anon.php', "<?php\nclass {$p}Factory { function make() { return new class {}; } }\n" );
        list( $generator, $entries ) = $this->generated();
        $this->assertSame( array(
            "K1\\{$p}Braced\\InBraces" => 'classes/braced.php',
            "K1\\{$p}\\Sub\\Inside" => 'classes/ns.php',
            "{$p}Abstract" => 'classes/plain.php',
            "{$p}Factory" => 'classes/anon.php',
            "{$p}Final" => 'classes/plain.php',
            "{$p}Interface" => 'classes/plain.php',
            "{$p}Plain" => 'classes/plain.php',
            "{$p}Trait" => 'classes/plain.php',
        ), $entries );
        $this->assertSame( array(), $generator->getWarnings() );
    }

    public function testClassInTheGlobalBracedNamespaceHasNoLeadingBackslash()
    {
        $p = $this->name;
        $this->file( 'classes/mixed.php', "<?php\nnamespace K1\\{$p} {\n    class Named {}\n}\nnamespace {\n    class {$p}Global {}\n}\n" );
        list( , $entries ) = $this->generated();
        $this->assertSame( array( "K1\\{$p}\\Named" => 'classes/mixed.php', "{$p}Global" => 'classes/mixed.php' ), $entries );
    }

    public function testEnumsBecomeEntries()
    {
        if ( PHP_VERSION_ID < 80100 )
            $this->markTestSkipped( 'enums need PHP 8.1' );
        $p = $this->name;
        $this->file( 'classes/enum.php', "<?php\nenum {$p}Suit: string { case Hearts = 'H'; }\nenum {$p}Plain { case A; }\n" );
        list( , $entries ) = $this->generated();
        $this->assertSame( array( "{$p}Plain" => 'classes/enum.php', "{$p}Suit" => 'classes/enum.php' ), $entries );
    }

    public function testDuplicatesAndKernelClassesAreRefused()
    {
        $p = $this->name;
        $this->file( 'classes/a.php', "<?php\nclass {$p}Twice {}\n" );
        $this->file( 'classes/b.php', "<?php\nclass {$p}Twice {}\nclass eZINI {}\n" );
        $messages = array();
        $generator = $this->generator();
        $generator->setOutputCallback( function ( $message, $type ) use ( &$messages ) { $messages[] = $type; } );
        $generator->buildAutoloadArrays();
        preg_match_all( "/'(.+?)'\s+=>/", $generator->arrays()[eZAutoloadGenerator::MODE_SINGLE_EXTENSION], $m );
        $this->assertSame( array( "{$p}Twice" ), $m[1] );
        $warnings = $generator->getWarnings();
        $this->assertCount( 2, $warnings );
        $this->assertStringContainsString( "Class {$p}Twice in file classes/b.php is already defined in:\nclasses/a.php", $warnings[0] );
        $this->assertStringContainsString( 'Class eZINI in file classes/b.php is already defined in:', $warnings[1] );
        $this->assertStringContainsString( '(autoload/ezp_kernel.php)', $warnings[1] );
        $this->assertSame( array( 'warning', 'warning' ), $messages );
    }

    // ---------------------------------------------------------------- files

    public function testOnlyPhpFilesOutsideSettingsAndTemplatesAreScanned()
    {
        $p = $this->name;
        $this->file( 'classes/keep.php', "<?php\nclass {$p}Keep {}\n" );
        $this->file( 'tests/kept.php', "<?php\nclass {$p}InTests {}\n" );
        $this->file( 'settings/skip.php', "<?php\nclass {$p}InSettings {}\n" );
        $this->file( 'templates/skip.php', "<?php\nclass {$p}InTemplates {}\n" );
        $this->file( 'notes.txt', "class {$p}Text {}\n" );
        $files = $this->generator()->files();
        $this->assertSame( array( eZAutoloadGenerator::MODE_SINGLE_EXTENSION => array( 'classes/keep.php', 'tests/kept.php' ) ), $files );
        list( , $entries ) = $this->generated();
        $this->assertSame( array( "{$p}InTests", "{$p}Keep" ), array_keys( $entries ) );
    }

    public function testIgnoreFileAndExcludeDirsApplyToAnExtensionGivenByItsPath()
    {
        $p = $this->name;
        $this->file( 'classes/keep.php', "<?php\nclass {$p}Keep {}\n" );
        $this->file( 'vendorish/skip.php', "<?php\nclass {$p}Ignored {}\n" );
        $this->file( 'old/skip.php', "<?php\nclass {$p}Old {}\n" );
        $this->file( '.autoloadignore', "\n# a comment, then a blank line\n\nvendorish\n" );
        list( , $entries ) = $this->generated( array( 'excludeDirs' => array( 'old' ) ) );
        $this->assertSame( array( "{$p}Keep" ), array_keys( $entries ) );
        list( , $entries ) = $this->generated();
        $this->assertSame( array( "{$p}Keep", "{$p}Old" ), array_keys( $entries ) );
    }

    public function testTestsOfAnExtensionAreLeftOutUnderARelativeExtensionRoot()
    {
        $p = $this->name;
        $this->file( 'classes/keep.php', "<?php\nclass {$p}Keep {}\n" );
        $this->file( 'tests/bootstrap.php', "<?php\nclass eZDB {}\nclass {$p}InTests {}\n" );
        foreach ( array( eZAutoloadGenerator::MODE_EXTENSION, eZAutoloadGenerator::MODE_KERNEL_OVERRIDE ) as $mode )
        {
            // The installation's .autoloadignore leaves out var/, so the base is the test directory.
            $generator = $this->generator( array( 'basePath' => $this->dir ) );
            $generator->setMode( $mode );
            // A relative root, as the default "extension" is.
            $generator->roots = array( $this->dir );
            $files = $generator->files();
            $this->assertSame( array( "$this->name/classes/keep.php" ), $files[$mode], "mode $mode" );
        }
    }

    public function testOnlyTheTestsDirectoryItselfIsLeftOut()
    {
        $p = $this->name;
        $this->file( 'classes/keep.php', "<?php\nclass {$p}Keep {}\n" );
        $this->file( 'tests/unit/FooTest.php', "<?php\nclass {$p}FooTest {}\n" );
        $this->file( 'tests_old/old.php', "<?php\nclass {$p}Old {}\n" );
        $this->file( 'testsuite/suite.php', "<?php\nclass {$p}Suite {}\n" );
        $this->file( 'tests.php', "<?php\nclass {$p}TestsFile {}\n" );
        $this->file( 'classes/tests/helper.php', "<?php\nclass {$p}Helper {}\n" );
        foreach ( array( eZAutoloadGenerator::MODE_EXTENSION, eZAutoloadGenerator::MODE_KERNEL_OVERRIDE ) as $mode )
        {
            $generator = $this->generator( array( 'basePath' => $this->dir ) );
            $generator->setMode( $mode );
            $generator->roots = array( $this->dir );
            $files = $generator->files()[$mode];
            sort( $files );
            $this->assertSame( array( "$p/classes/keep.php", "$p/classes/tests/helper.php", "$p/tests.php",
                                      "$p/tests_old/old.php", "$p/testsuite/suite.php" ), $files, "mode $mode" );
        }
    }

    public function testTestsOfAPackageVendoredInAnExtensionAreLeftOut()
    {
        $p = $this->name;
        $this->file( 'classes/keep.php', "<?php\nclass {$p}Keep {}\n" );
        $this->file( 'vendor/acme/tool/src/Tool.php', "<?php\nnamespace Acme\\Tool;\nclass Tool {}\n" );
        $this->file( 'vendor/acme/tool/src/tests/Inner.php', "<?php\nnamespace Acme\\Tool\\tests;\nclass Inner {}\n" );
        $this->file( 'vendor/acme/tool/tests/ToolTest.php', "<?php\nnamespace Acme\\Tool\\Tests;\nclass ToolTest {}\n" );
        $this->file( 'vendor/acme/tool/Tests/Fixtures/Fixture.php', "<?php\nnamespace Acme\\Tool\\Tests\\Fixtures;\nclass Fixture {}\n" );
        $this->file( 'vendor/acme/tool/tests_data/Data.php', "<?php\nnamespace Acme\\Tool;\nclass Data {}\n" );
        $this->file( 'vendor/tests/x/y.php', "<?php\nclass {$p}VendorNamedTests {}\n" );
        $generator = $this->generator( array( 'basePath' => $this->dir ) );
        $generator->setMode( eZAutoloadGenerator::MODE_EXTENSION );
        $generator->roots = array( $this->dir );
        $files = $generator->files()[eZAutoloadGenerator::MODE_EXTENSION];
        sort( $files );
        $this->assertSame( array( "$p/classes/keep.php", "$p/vendor/acme/tool/src/Tool.php", "$p/vendor/acme/tool/src/tests/Inner.php",
                                  "$p/vendor/acme/tool/tests_data/Data.php", "$p/vendor/tests/x/y.php" ), $files );
    }

    public function testTestsClassMapReadsATestsDirectoryWithoutWriting()
    {
        $p = $this->name;
        $this->file( 'tests/bootstrap.php', "<?php\nclass eZDB {}\nclass {$p}Fixture {}\n" );
        $this->file( 'tests/unit/ns.php', "<?php\nnamespace K1\\{$p};\nclass MockServer {}\n" );
        $this->file( 'tests/templates/skip.php', "<?php\nclass {$p}InTemplates {}\n" );
        $tests = realpath( "$this->dir/$p/tests" );
        $map = eZAutoloadGenerator::testsClassMap( "$this->dir/$p/tests" );
        $this->assertSame( array( "K1\\{$p}\\MockServer" => "$tests/unit/ns.php", "{$p}Fixture" => "$tests/bootstrap.php" ), $map,
                           'real paths, one backslash, no kernel class (eZDB), the directories always skipped left out' );
        $this->assertSame( array(), eZAutoloadGenerator::testsClassMap( "$this->dir/$p/nothing" ) );
        $this->assertFalse( is_dir( "$this->dir/$p/tests/autoload" ) );
    }

    public function testTheTestsAutoloadFindsTheClassesOfATestsDirectory()
    {
        $p = $this->name;
        $this->file( 'tests/support/helper.php', "<?php\nclass {$p}TestHelper { const OK = 1; }\n" );
        $this->file( 'tests/unit/bar_test.php', "<?php\nreturn 1;\n" );
        $this->assertFalse( class_exists( "{$p}TestHelper" ), 'not in the runtime arrays' );
        $tests = ezpAutoloader::enableTestsFor( "$this->dir/$p/tests/unit/bar_test.php" );
        $this->assertSame( realpath( "$this->dir/$p/tests" ), $tests );
        $this->assertSame( 1, ezpAutoloader::testsDirectories()[$tests] );
        $this->assertTrue( class_exists( "{$p}TestHelper" ) );
        $this->assertSame( 0, ezpAutoloader::enableTests( "$this->dir/$p/tests" ), 'read once' );
        $this->assertNull( ezpAutoloader::enableTestsFor( "$this->dir/$p/tests.php" ) );
        $this->assertNull( ezpAutoloader::enableTestsFor( 'kernel/private/classes/ezautoloadgenerator.php' ) );
    }

    public function testExtensionGivenByAnAbsolutePath()
    {
        $p = $this->name;
        $this->file( 'classes/keep.php', "<?php\nclass {$p}Keep {}\n" );
        $this->file( 'old/skip.php', "<?php\nclass {$p}Old {}\n" );
        list( , $entries ) = $this->generated( array( 'basePath' => getcwd() . "/$this->dir/$this->name", 'excludeDirs' => array( 'old' ) ) );
        $this->assertSame( array( "{$p}Keep" => 'classes/keep.php' ), $entries );
    }

    public function testFileIsWrittenToTheOutputDirectory()
    {
        $p = $this->name;
        $this->file( 'classes/keep.php', "<?php\nclass {$p}Keep {}\n" );
        $out = "$this->dir/out/nested";
        $generator = $this->generator( array( 'writeFiles' => true, 'outputDir' => $out . '/' ) );
        $generator->buildAutoloadArrays();
        $file = "$out/{$this->name}_autoload.php";
        $this->assertFileExists( $file );
        $this->assertSame( array( "{$p}Keep" => 'classes/keep.php' ), include $file );
        $generator->printAutoloadArray( eZAutoloadGenerator::MODE_SINGLE_EXTENSION );
        $generator->printAutoloadArray( eZAutoloadGenerator::MODE_TESTS );
        $messages = $generator->getMessages();
        $this->assertStringStartsWith( "<?php\n", end( $messages ) );
        $this->assertStringContainsString( "\nreturn array(\n      '{$p}Keep' => 'classes/keep.php',\n    );\n", end( $messages ) );
    }

    public function testOutputDirThatIsAFileIsRefused()
    {
        $p = $this->name;
        $this->file( 'classes/keep.php', "<?php\nclass {$p}Keep {}\n" );
        file_put_contents( "$this->dir/afile", 'x' );
        $generator = $this->generator( array( 'writeFiles' => true, 'outputDir' => "$this->dir/afile" ) );
        $this->expectException( Exception::class );
        $this->expectExceptionMessage( "Specified target: $this->dir/afile is not a directory." );
        $generator->buildAutoloadArrays();
    }
}
