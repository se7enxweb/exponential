<?php
/**
 * The upload page's use of the ZIP importer (ezmultiupload 6.1): expMultiuploadZipImporter lists and extracts an
 * archive (its own tests are in the extension's tests/), and expMultiuploadService turns what it returns into the
 * rows of the page and checks each as an upload. Archives are made by the test; nodes, classes and folders are held
 * in memory.
 *
 *  MZ-01 - zipImporterEntries(): a nested ZIP file that can be read is left out and its files are listed with its
 *          name (without .zip) as a folder; skipped entries keep the importer's reason, translated
 *  MZ-02 - zipEntries(): the class each entry becomes, a type outside the location's list is skipped, with "keep"
 *          the entries go into folder items (made only on import, after the create permission check), with "flat"
 *          into the target; no server path goes to the browser
 *  MZ-03 - with extract()'s answer each row gets its extracted file; an unticked entry is not extracted
 *  MZ-04 - zipReason() names every reason code of the importer; isZipFile() checks the name and the first bytes
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezmultiupload/expMultiuploadZipTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expMultiuploadRoot = dirname( __DIR__, 4 ) . '/extension/ezmultiupload/classes';
if ( is_file( $expMultiuploadRoot . '/expmultiuploadzipimporter.php' ) )
{
    require_once $expMultiuploadRoot . '/expmultiuploadservice.php';
    require_once $expMultiuploadRoot . '/expmultiuploadzipimporter.php';
}
unset( $expMultiuploadRoot );

if ( class_exists( 'expMultiuploadZipImporter' ) )
{
    /** A node held in memory */
    class expMultiuploadZipTestNode extends eZContentObjectTreeNode
    {
        public $values = array();

        public function __construct( array $values )
        {
            $this->values = $values;
        }

        function attribute( $name, $noFunction = false )
        {
            return isset( $this->values[$name] ) ? $this->values[$name] : null;
        }

        function canRead()
        {
            return true;
        }

        function canCreate()
        {
            return true;
        }
    }

    /** A content class held in memory */
    class expMultiuploadZipTestClass extends eZContentClass
    {
        public $values = array();

        public function __construct( array $values )
        {
            $this->values = $values;
        }

        function attribute( $name, $noFunction = false )
        {
            return isset( $this->values[$name] ) ? $this->values[$name] : null;
        }
    }

    /** The service with the database parts in memory; folders are made in memory */
    class expMultiuploadZipTestService extends expMultiuploadService
    {
        public static $folders = array();      // "parent id/name" => node
        public static $made = array();
        public static $folderAllowed = true;
        public static $next = 900;

        protected static function fetchClassByIdentifier( $identifier )
        {
            return new expMultiuploadZipTestClass( array( 'id' => 1, 'identifier' => $identifier, 'name' => ucfirst( $identifier ) ) );
        }

        protected static function canCreateClass( $node, $class )
        {
            return true;
        }

        protected static function altTextRequired( $class )
        {
            return false;
        }

        protected static function classMaxSize( $class )
        {
            return 0;
        }

        protected static function childFiles( $node )
        {
            return array();
        }

        protected static function childFolder( $parent, $name )
        {
            $key = $parent->attribute( 'node_id' ) . '/' . $name;
            return isset( self::$folders[$key] ) ? self::$folders[$key] : null;
        }

        protected static function makeFolder( $parent, $name )
        {
            if ( !self::$folderAllowed )
                return 'no folder';
            $node = new expMultiuploadZipTestNode( array( 'node_id' => ++self::$next, 'name' => $name, 'class_identifier' => 'folder' ) );
            self::$folders[$parent->attribute( 'node_id' ) . '/' . $name] = $node;
            self::$made[] = $name;
            return $node;
        }
    }
}

class expMultiuploadZipTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $cwd;

    /** @var string the test's scratch directory */
    private $dir;

    /** @var string[] directories extract() made */
    private $extracted = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'expMultiuploadZipImporter' ) || !class_exists( 'ZipArchive' ) )
            self::markTestSkipped( 'ezmultiupload 6.1 or later, or the zip extension, is not installed.' );
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-expmultiupload-zip-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0700, true );
        $ini = eZINI::instance( 'ezmultiupload.ini' );
        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariable( 'MultiUploadSettings', 'MaxFileSize', '64M' );
        $ini->setVariable( 'FileTypeSettings_folder', 'FileType', array( '*.jpg', '*.png', '*.txt', '*.pdf' ) );
        expMultiuploadZipTestService::$folders = array();
        expMultiuploadZipTestService::$made = array();
        expMultiuploadZipTestService::$folderAllowed = true;
    }

    protected function tearDown(): void
    {
        if ( $this->cwd === null )
            return;
        $importer = new expMultiuploadZipImporter();
        foreach ( $this->extracted as $dir )
            $importer->cleanup( $dir );
        $importer->cleanup( $this->dir );
        eZINI::resetInstance( 'ezmultiupload.ini' );
        chdir( $this->cwd );
    }

    /** A ZIP file with $entries (name => content) */
    private function zip( $name, array $entries )
    {
        $path = $this->dir . '/' . $name;
        $zip = new ZipArchive();
        $zip->open( $path, ZipArchive::CREATE | ZipArchive::OVERWRITE );
        foreach ( $entries as $entry => $content )
            $zip->addFromString( $entry, $content );
        $zip->close();
        return $path;
    }

    private function png()
    {
        $im = imagecreatetruecolor( 4, 4 );
        ob_start();
        imagepng( $im );
        return ob_get_clean();
    }

    /** the test archive: a top file, a picture in a folder, a nested ZIP file with a file and a disallowed type */
    private function mixed()
    {
        $inner = file_get_contents( $this->zip( 'inner.zip', array( 'inner.txt' => 'inner', 'pics/sub/b.png' => $this->png() ) ) );
        return $this->zip( 'mixed.zip', array( 'pics/a.png' => $this->png(), 'notes.txt' => 'text', 'tool.exe' => 'MZ',
                                               'inner.zip' => $inner, '../evil.txt' => 'x' ) );
    }

    private function byName( array $rows )
    {
        $out = array();
        foreach ( $rows as $row )
            $out[$row['name']] = $row;
        return $out;
    }

    private function target()
    {
        return new expMultiuploadZipTestNode( array( 'node_id' => 100, 'name' => 'Folder A', 'class_identifier' => 'folder' ) );
    }

    /** MZ-01 */
    public function testImporterEntriesBecomeRows()
    {
        $importer = new expMultiuploadZipImporter();
        $rows = $this->byName( expMultiuploadService::zipImporterEntries( $importer->inspect( $this->mixed() ) ) );
        $this->assertArrayNotHasKey( 'inner.zip', $rows, 'a readable nested ZIP file is listed by its files' );
        $this->assertSame( array( 'inner' ), $rows['inner.txt']['dirs'] );
        $this->assertSame( 'inner.zip/inner.txt', $rows['inner.txt']['key'] );
        $this->assertSame( array( 'inner', 'pics', 'sub' ), $rows['b.png']['dirs'] );
        $this->assertSame( 'skipped', $rows['evil.txt']['status'] );
        $this->assertSame( 'unsafe_path', $rows['evil.txt']['reasonCode'] );
        $this->assertStringContainsString( 'not safe', $rows['evil.txt']['reason'] );
        $this->assertSame( 'skipped', $rows['tool.exe']['status'], 'a denied extension' );
        $this->assertNull( $rows['notes.txt']['file'], 'an inspection extracts nothing' );
    }

    /** MZ-02 */
    public function testRowsGetTheirClassAndLocation()
    {
        $importer = new expMultiuploadZipImporter();
        $entries = expMultiuploadService::zipImporterEntries( $importer->inspect( $this->mixed() ) );
        $target = $this->target();

        $cache = array();
        $rows = $this->byName( expMultiuploadZipTestService::zipEntries( $target, $entries, 'keep', false, $cache ) );
        $this->assertSame( 'image', $rows['a.png']['class'] );
        $this->assertSame( 'ok', $rows['a.png']['status'] );
        $this->assertSame( 'file', $rows['notes.txt']['class'] );
        $this->assertSame( 'inner / pics / sub', $rows['b.png']['folder'] );
        $this->assertSame( array(), expMultiuploadZipTestService::$made, 'an inspection makes no folder' );
        $summary = expMultiuploadService::zipSummary( array_values( $rows ) );
        $this->assertSame( 4, $summary['ok'] );
        $this->assertArrayNotHasKey( 'file', $summary['entries'][0], 'no server path goes to the browser' );
        $this->assertArrayNotHasKey( 'location', $summary['entries'][0] );

        $cache = array();
        $rows = $this->byName( expMultiuploadZipTestService::zipEntries( $target, $entries, 'keep', true, $cache ) );
        $this->assertSame( array( 'pics', 'inner', 'pics', 'sub' ), expMultiuploadZipTestService::$made );
        $this->assertSame( 'sub', $rows['b.png']['location']->attribute( 'name' ) );

        $cache = array();
        $rows = $this->byName( expMultiuploadZipTestService::zipEntries( $target, $entries, 'flat', true, $cache ) );
        $this->assertSame( 100, $rows['b.png']['location']->attribute( 'node_id' ) );

        expMultiuploadZipTestService::$folders = array();
        expMultiuploadZipTestService::$folderAllowed = false;
        $cache = array();
        $rows = $this->byName( expMultiuploadZipTestService::zipEntries( $target, $entries, 'keep', true, $cache ) );
        $this->assertSame( 'skipped', $rows['a.png']['status'], 'no permission to make the folder' );
        $this->assertSame( 'ok', $rows['notes.txt']['status'], 'at the top level no folder is needed' );

        // the location's type list: a gallery takes images only
        eZINI::instance( 'ezmultiupload.ini' )->setVariable( 'FileTypeSettings_folder', 'FileType', array( '*.png' ) );
        $cache = array();
        $rows = $this->byName( expMultiuploadZipTestService::zipEntries( $target, $entries, 'flat', false, $cache ) );
        $this->assertSame( 'skipped', $rows['notes.txt']['status'] );
        $this->assertSame( 'ok', $rows['a.png']['status'] );
    }

    /** MZ-03 */
    public function testExtractedFilesAreAttached()
    {
        $importer = new expMultiuploadZipImporter();
        $zip = $this->mixed();
        $inspect = $importer->inspect( $zip );
        $extract = $importer->extract( $zip, array( 'pics/a.png', 'inner.zip/inner.txt' ) );
        $this->extracted[] = $extract['tmpDir'];
        $rows = $this->byName( expMultiuploadService::zipImporterEntries( $inspect, $extract ) );
        $this->assertFileExists( $rows['a.png']['file'] );
        $this->assertSame( 'inner', file_get_contents( $rows['inner.txt']['file'] ) );
        $this->assertNull( $rows['notes.txt']['file'], 'not ticked, not extracted' );
        $this->assertSame( 'skipped', $rows['notes.txt']['status'] );
        $importer->cleanup( $extract['tmpDir'] );
        $this->assertDirectoryDoesNotExist( $extract['tmpDir'] );
    }

    /** MZ-04 */
    public function testReasonsAndTheZipCheck()
    {
        foreach ( array( 'type_not_allowed', 'too_big', 'unsafe_path', 'symlink', 'device', 'too_deep', 'encrypted',
                         'ratio_exceeded', 'too_many_entries', 'total_size_exceeded', 'system_file', 'unreadable' ) as $code )
        {
            $text = expMultiuploadService::zipReason( $code );
            $this->assertNotSame( '', $text, $code );
            $this->assertNotSame( expMultiuploadService::zipReason( 'something new' ), $text, $code . ' has a text of its own' );
        }
        $this->assertSame( '', expMultiuploadService::zipReason( '' ) );
        $this->assertTrue( expMultiuploadService::isZipFile( 'x.ZIP', $this->zip( 'x.zip', array( 'a.txt' => 'a' ) ) ) );
        file_put_contents( $this->dir . '/fake.zip', 'not a zip' );
        $this->assertFalse( expMultiuploadService::isZipFile( 'fake.zip', $this->dir . '/fake.zip' ) );
        $this->assertFalse( expMultiuploadService::isZipFile( 'x.txt', $this->dir . '/x.zip' ), 'the name must say .zip' );
    }
}
