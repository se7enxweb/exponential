<?php
/**
 * The two reports of the upgrade check (setup/systemupgrade) and of bin/php/checkmanifest.php, without a database:
 * the file consistency report reads manifests and files written to a throwaway directory under var/tmp, the schema
 * report takes differences as eZDbSchemaChecker::diff() returns them and a stand-in SQL writer.
 *
 *  UC-01 - Matching files: status ok, nothing to list
 *  UC-02 - A modified file: both checksums, the wording of the command line check
 *  UC-03 - A missing file
 *  UC-04 - Malformed manifest lines: not "<md5>  <path>", a path outside the installation, a file listed twice
 *  UC-05 - A manifest with CRLF line ends reads like one with LF
 *  UC-06 - Unreadable files: a directory where a file is listed, a file whose checksum cannot be read
 *  UC-07 - An extension's own manifest: its header, paths under its directory, found only where there is one
 *  UC-08 - The manifest of Exponential missing or empty fails the check; a missing extension manifest does not
 *  UC-09 - Files git tracks that are not listed, the paths that never belong in it, lines out of order
 *  UC-10 - The CSV and text reports; cells a spreadsheet would run are quoted
 *  UC-11 - The summary of a manifest: entries per area, header, malformed lines
 *  UC-12 - The page groups findings by state and lists at most ITEMS_LIMIT of them
 *  SC-01 - No differences: ok, no SQL
 *  SC-02 - An unreadable database: failed
 *  SC-03 - Missing, extra and changed tables, by table, with their SQL, sources and what removes something
 *  SC-04 - A changed table the engine writes no SQL for is an engine note and passes
 *  SC-05 - Field and index definitions in words; the SQL download marks what removes something
 *  SC-06 - A document store: missing collections grouped by feature, the mongosh command
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../../../../../kernel/private/classes/expfileconsistencyreport.php';
require_once __DIR__ . '/../../../../../kernel/private/classes/expschemaconsistencyreport.php';

class expUpgradeCheckReportsTest extends PHPUnit\Framework\TestCase
{
    /** @var string the throwaway installation root */
    private $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-upgrade-check-' . getmypid() . '-' . mt_rand();
        mkdir( $this->root . '/share', 0777, true );
    }

    protected function tearDown(): void
    {
        $this->remove( $this->root );
    }

    private function remove( $path )
    {
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    $this->remove( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else if ( file_exists( $path ) || is_link( $path ) )
        {
            unlink( $path );
        }
    }

    private function file( $path, $content )
    {
        $full = $this->root . '/' . $path;
        if ( !is_dir( dirname( $full ) ) )
            mkdir( dirname( $full ), 0777, true );
        file_put_contents( $full, $content );
        return md5( $content );
    }

    private function manifest( array $lines, $path = 'share/filelist.md5', $eol = "\n" )
    {
        $this->file( $path, implode( $eol, $lines ) . $eol );
    }

    private function report()
    {
        return expFileConsistencyReport::forInstallation( $this->root );
    }

    /** UC-01 */
    public function testMatchingFiles()
    {
        $a = $this->file( 'a.php', "<?php\n" );
        $b = $this->file( 'lib/b.php', "b\n" );
        $this->manifest( array( "$a  a.php", "$b  lib/b.php" ) );

        $report = $this->report();
        $result = $report->run();
        $this->assertSame( 'ok', $result['status'] );
        $this->assertSame( 2, $result['checked'] );
        $this->assertSame( 2, $result['counts']['matching'] );
        $this->assertSame( 0, $result['problems'] );
        $this->assertSame( array(), $result['items'] );
        $this->assertSame( array(), $report->problemPaths() );
        $this->assertSame( 'root', $result['manifests'][0]['kind'] );
        $this->assertGreaterThan( 0, $result['manifests'][0]['mtime'] );
    }

    /** UC-02 */
    public function testModifiedFile()
    {
        $a = $this->file( 'a.php', "old\n" );
        $this->file( 'a.php', "new\n" );
        $this->manifest( array( "$a  a.php" ) );

        $report = $this->report();
        $result = $report->run();
        $this->assertSame( 'differences', $result['status'] );
        $this->assertSame( 1, $result['counts']['modified'] );
        $item = $result['items'][0];
        $this->assertSame( 'modified', $item['state'] );
        $this->assertSame( $a, $item['expected'] );
        $this->assertSame( md5( "new\n" ), $item['actual'] );
        $this->assertSame( 1, $item['line'] );
        $this->assertSame( 'kernel', $item['area'] );
        $this->assertSame( array( 'a.php' ), $report->problemPaths() );
        $this->assertSame( "a.php has checksum $a in the manifest, the file has " . md5( "new\n" ), expFileConsistencyReport::describe( $item ) );
    }

    /** UC-03 */
    public function testMissingFile()
    {
        $a = $this->file( 'a.php', "a\n" );
        $this->manifest( array( "$a  a.php", "$a  gone/b.php" ) );

        $result = $this->report()->run();
        $this->assertSame( 'differences', $result['status'] );
        $this->assertSame( 1, $result['counts']['missing'] );
        $this->assertSame( 'gone/b.php', $result['items'][0]['path'] );
        $this->assertSame( '', $result['items'][0]['actual'] );
        $this->assertSame( 'gone/b.php is listed but does not exist', expFileConsistencyReport::describe( $result['items'][0] ) );
    }

    /** UC-04 */
    public function testMalformedLines()
    {
        $a = $this->file( 'a.php', "a\n" );
        $secret = $this->file( 'outside.txt', "x\n" );
        $this->manifest( array( "$a  a.php",
                                'this is no manifest line at all, it is just text',
                                str_repeat( 'z', 32 ) . '  a.php',
                                "$a a.php",
                                "$secret  ../outside.txt",
                                "$secret  /etc/hostname",
                                "$a  a.php",
                                '' ) );

        $result = $this->report()->run();
        // the good line still checks, the bad ones are notes
        $this->assertSame( 'ok', $result['status'] );
        $this->assertSame( 1, $result['checked'] );
        $this->assertSame( 6, $result['counts']['malformed'] );
        $reasons = array();
        foreach ( $result['items'] as $item )
        {
            $this->assertSame( 'malformed', $item['state'] );
            $this->assertSame( 'share/filelist.md5', $item['path'] );
            $reasons[$item['line']] = $item['detail'];
        }
        $this->assertSame( array( 2 => 'format', 3 => 'format', 4 => 'format', 5 => 'unsafe_path', 6 => 'unsafe_path', 7 => 'duplicate' ), $reasons );

        $this->assertTrue( expFileConsistencyReport::isSafePath( 'a/b..c/d.php' ) );
        $this->assertFalse( expFileConsistencyReport::isSafePath( 'a/../../d.php' ) );
        $this->assertFalse( expFileConsistencyReport::isSafePath( 'C:/x' ) );
        $this->assertFalse( expFileConsistencyReport::isSafePath( '' ) );
        $this->assertFalse( expFileConsistencyReport::isSafePath( "a\0b" ) );
    }

    /** UC-05 */
    public function testCrlfManifest()
    {
        $a = $this->file( 'a.php', "a\n" );
        $b = $this->file( 'dir/b.php', "b\r\n" );
        $this->manifest( array( "$a  a.php", "$b  dir/b.php" ), 'share/filelist.md5', "\r\n" );

        $result = $this->report()->run();
        $this->assertSame( 'ok', $result['status'] );
        $this->assertSame( 2, $result['counts']['matching'] );
        $this->assertSame( 0, $result['counts']['malformed'] );

        $parsed = expFileConsistencyReport::parseManifest( "name: x\r\nversion: 1.2\r\n\r\n$a  a.php\r\n" );
        $this->assertSame( array( 'name' => 'x', 'version' => '1.2' ), $parsed['header'] );
        $this->assertSame( 'a.php', $parsed['entries'][0]['path'] );
        $this->assertSame( 4, $parsed['entries'][0]['line'] );
    }

    /** UC-06 */
    public function testUnreadableFiles()
    {
        mkdir( $this->root . '/adir' );
        $a = $this->file( 'a.php', "a\n" );
        $locked = $this->file( 'locked.php', "secret\n" );
        $this->manifest( array( "$a  a.php", md5( '' ) . '  adir', "$locked  locked.php" ) );

        // a directory where a file is listed
        $result = $this->report()->run();
        $this->assertSame( 1, $result['counts']['unreadable'] );
        $this->assertSame( 'adir', $result['items'][0]['path'] );
        $this->assertSame( 'differences', $result['status'] );

        // a file whose content cannot be read (permissions; root reads everything, so the hasher stands in)
        $report = new expFileConsistencyReport( $this->root, function ( $file ) {
            return substr( $file, -10 ) === 'locked.php' || is_dir( $file ) ? false : md5_file( $file );
        } );
        $report->addManifest( 'exponential', 'share/filelist.md5', '', 'Exponential', 'root', true );
        $result = $report->run();
        $this->assertSame( 2, $result['counts']['unreadable'] );
        $this->assertSame( array( 'adir', 'locked.php' ), $report->problemPaths() );
        $this->assertSame( 'locked.php is listed but cannot be read', expFileConsistencyReport::describe( $result['items'][1] ) );
    }

    /** UC-07 */
    public function testExtensionManifest()
    {
        $root = $this->file( 'index.php', "i\n" );
        $this->manifest( array( "$root  index.php" ) );
        $x = $this->file( 'extension/news/classes/x.php', "x\n" );
        $y = $this->file( 'extension/news/y.tpl', "y\n" );
        $this->file( 'extension/news/y.tpl', "changed\n" );
        $this->manifest( array( 'name: news', 'version: 4.2.1', 'files_count: 2', '', "$x  classes/x.php", "$y  y.tpl" ),
                         'extension/news/share/filelist.md5' );
        $this->file( 'extension/plain/ezinfo.php', "<?php\n" );

        $this->assertSame( array( 'news' => 'extension/news' ), expFileConsistencyReport::extensionDirectoriesWithManifest( $this->root ) );

        $report = expFileConsistencyReport::forInstallation( $this->root, array( 'news' => 'extension/news', 'plain' => 'extension/plain/' ) );
        $result = $report->run();
        $this->assertCount( 2, $result['manifests'], 'an extension without a manifest is not read' );
        $news = $result['manifests'][1];
        $this->assertSame( 'news', $news['id'] );
        $this->assertSame( 'extension', $news['kind'] );
        $this->assertSame( 'extension/news/', $news['base'] );
        $this->assertSame( array( 'name' => 'news', 'version' => '4.2.1', 'files_count' => '2' ), $news['header'] );
        $this->assertSame( 2, $news['entries'] );
        $this->assertSame( 1, $news['problems'] );
        $this->assertSame( 0, $news['counts']['malformed'], 'the header lines are no malformed lines' );
        $this->assertSame( 'extension/news/y.tpl', $result['items'][0]['path'] );
        $this->assertSame( 'news', $result['items'][0]['manifest'] );
        $this->assertSame( 'extension/news', $result['items'][0]['area'] );
        $this->assertSame( array( array( 'name' => 'extension/news', 'count' => 1 ) ), $result['areas'] );
        $this->assertSame( 3, $result['checked'] );
    }

    /** UC-08 */
    public function testManifestMissingOrEmpty()
    {
        $result = $this->report()->run();
        $this->assertSame( 'failed', $result['status'] );
        $this->assertSame( 'missing_manifest', $result['failure'] );
        $this->assertFalse( $result['manifests'][0]['exists'] );

        $this->manifest( array( '' ) );
        $result = $this->report()->run();
        $this->assertSame( 'failed', $result['status'] );
        $this->assertSame( 'empty_manifest', $result['failure'] );

        $a = $this->file( 'a.php', "a\n" );
        $this->manifest( array( "$a  a.php" ) );
        $report = $this->report();
        $report->addManifest( 'gone', 'extension/gone/share/filelist.md5', 'extension/gone', 'gone', 'extension' );
        $result = $report->run();
        $this->assertSame( 'differences', $result['status'], 'an unreadable extension manifest is something to look at, not a failure' );
        $this->assertSame( '', $result['failure'] );
        $this->assertFalse( $result['manifests'][1]['readable'] );
    }

    /** UC-09 */
    public function testUnlistedAndUnordered()
    {
        $a = $this->file( 'a.php', "a\n" );
        $b = $this->file( 'b.php', "b\n" );
        $this->file( 'c.php', "c\n" );
        $this->file( 'var/cache/x.php', "x\n" );
        $this->file( 'bin/__pycache__/x.pyc', "x\n" );
        $this->manifest( array( "$b  b.php", "$a  a.php" ) );

        $report = $this->report();
        $report->setTrackedFiles( 'exponential', array( 'a.php', 'b.php', 'c.php', 'var/cache/x.php', 'bin/__pycache__/x.pyc',
                                                        'share/filelist.md5', 'deleted.php' ),
                                  expFileConsistencyReport::ROOT_EXCLUDES );
        $result = $report->run();
        $this->assertSame( 'ok', $result['status'], 'notes do not fail the check' );
        $this->assertSame( 1, $result['counts']['unlisted'] );
        $this->assertSame( 1, $result['counts']['unordered'] );
        $this->assertSame( 2, $result['notes'] );
        $this->assertTrue( $result['manifests'][0]['unlisted_checked'] );
        $states = array();
        foreach ( $result['items'] as $item )
            $states[] = $item['state'] . ' ' . $item['path'];
        $this->assertSame( array( 'unordered a.php', 'unlisted c.php' ), $states );
        $this->assertSame( 'a.php is out of order (after b.php)', expFileConsistencyReport::describe( $result['items'][0] ) );
        $this->assertSame( 'c.php is in git but not listed', expFileConsistencyReport::describe( $result['items'][1] ) );

        $this->assertFalse( expFileConsistencyReport::covers( 'extension/ezoe/x.js', expFileConsistencyReport::ROOT_EXCLUDES ) );
        $this->assertTrue( expFileConsistencyReport::covers( 'extension/ezjscore/x.js', expFileConsistencyReport::ROOT_EXCLUDES ) );
    }

    /** UC-10 */
    public function testCsvAndText()
    {
        $a = $this->file( '=cmd.php', "a\n" );
        $this->file( '=cmd.php', "b\n" );
        $this->manifest( array( "$a  =cmd.php", "$a  missing.php" ) );
        $report = $this->report();

        $handle = fopen( 'php://memory', 'w+' );
        $report->writeCsv( $handle );
        rewind( $handle );
        $csv = stream_get_contents( $handle );
        $lines = explode( "\r\n", $csv );
        $this->assertSame( "\xEF\xBB\xBF" . '"manifest","area","state","path","expected_md5","actual_md5","line","detail"', $lines[0] );
        $this->assertStringContainsString( '"modified","\'=cmd.php"', $lines[1] );
        $this->assertStringContainsString( '"missing","missing.php","' . $a . '","","2",""', $lines[2] );

        $text = $report->text( 'Report' );
        $this->assertStringStartsWith( "Report\n======\n", $text );
        $this->assertStringContainsString( 'Modified: 1', $text );
        $this->assertStringContainsString( '  missing.php is listed but does not exist', $text );
    }

    /** UC-11 */
    public function testSummarize()
    {
        $this->manifest( array( 'version: 1', md5( 'a' ) . '  a.php', md5( 'b' ) . '  extension/ezoe/b.js', md5( 'c' ) . '  extension/ezoe/c.js', 'junk' ) );
        $summary = expFileConsistencyReport::summarize( $this->root . '/share/filelist.md5' );
        $this->assertTrue( $summary['readable'] );
        $this->assertSame( 3, $summary['entries'] );
        $this->assertSame( 1, $summary['malformed'] );
        $this->assertSame( array( 'version' => '1' ), $summary['header'] );
        $this->assertSame( array( 'kernel' => 1, 'extension/ezoe' => 2 ), $summary['areas'] );
        $this->assertFalse( expFileConsistencyReport::summarize( $this->root . '/nothing' )['exists'] );
    }

    /** UC-12 */
    public function testPageGroups()
    {
        $limit = Exponential\View\Kernel\Setup\Systemupgrade::ITEMS_LIMIT;
        $items = array();
        for ( $i = 0; $i < $limit + 5; $i++ )
            $items[] = array( 'state' => 'modified', 'path' => "f$i" );
        $items[] = array( 'state' => 'unlisted', 'path' => 'u' );
        $counts = array_fill_keys( expFileConsistencyReport::STATES, 0 );
        $counts['modified'] = $limit + 5;
        $counts['unlisted'] = 1;
        $groups = Exponential\View\Kernel\Setup\Systemupgrade::groups( array( 'items' => $items, 'counts' => $counts ) );
        $this->assertCount( 2, $groups );
        $this->assertSame( 'modified', $groups[0]['state'] );
        $this->assertSame( $limit + 5, $groups[0]['count'] );
        $this->assertSame( $limit, $groups[0]['shown'] );
        $this->assertTrue( $groups[0]['problem'] );
        $this->assertSame( 'unlisted', $groups[1]['state'] );
        $this->assertSame( 0, $groups[1]['shown'], 'the limit is for all findings together' );
        $this->assertFalse( $groups[1]['problem'] );
    }

    /** SC-01 */
    public function testSchemaNoDifferences()
    {
        $result = expSchemaConsistencyReport::fromDifferences( array(), function () { return 'never'; }, 'mysql' )->result();
        $this->assertSame( 'ok', $result['status'] );
        $this->assertSame( '', $result['sql'] );
        $this->assertSame( array(), $result['tables'] );
        $this->assertSame( 0, $result['counts']['tables'] );
        $this->assertTrue( $result['relational'] );
    }

    /** SC-02 */
    public function testSchemaUnreadable()
    {
        $result = expSchemaConsistencyReport::fromDifferences( false, null, 'sqlite' )->result();
        $this->assertSame( 'failed', $result['status'] );
        $this->assertSame( 'unreadable_database', $result['failure'] );
    }

    private function writer()
    {
        // writes one line per thing to change, as a schema handler would write a statement
        return function ( array $diff ) {
            $sql = '';
            foreach ( isset( $diff['new_tables'] ) ? $diff['new_tables'] : array() as $name => $def )
                $sql .= "CREATE TABLE $name;\n";
            foreach ( isset( $diff['removed_tables'] ) ? $diff['removed_tables'] : array() as $name => $def )
                $sql .= "DROP TABLE $name;\n";
            foreach ( isset( $diff['table_changes'] ) ? $diff['table_changes'] : array() as $name => $changes )
            {
                foreach ( isset( $changes['added_fields'] ) ? $changes['added_fields'] : array() as $field => $def )
                    $sql .= "ALTER TABLE $name ADD $field;\n";
                foreach ( isset( $changes['removed_fields'] ) ? $changes['removed_fields'] : array() as $field => $def )
                    $sql .= "ALTER TABLE $name DROP $field;\n";
                foreach ( isset( $changes['added_indexes'] ) ? $changes['added_indexes'] : array() as $index => $def )
                    $sql .= "CREATE INDEX $index ON $name;\n";
            }
            return $sql;
        };
    }

    /** SC-03 */
    public function testSchemaTables()
    {
        $differences = array(
            'new_tables' => array( 'ezneeded' => array( 'fields' => array(), 'indexes' => array() ) ),
            'removed_tables' => array( 'custom_data' => array( 'fields' => array(), 'indexes' => array() ) ),
            'table_changes' => array(
                'ezcontentobject' => array(
                    'added_fields' => array( 'flags' => array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 0 ) ),
                    'removed_fields' => array( 'legacy' => true ),
                    'added_indexes' => array( 'ezcontentobject_flags' => array( 'type' => 'non-unique', 'fields' => array( 'flags' ) ) ),
                ),
            ),
        );
        $current = array( 'ezcontentobject' => array( 'fields' => array( 'legacy' => array( 'type' => 'varchar', 'length' => 20 ) ), 'indexes' => array() ) );
        $report = expSchemaConsistencyReport::fromDifferences( $differences, $this->writer(), 'mysql', $current,
                                                               array( 'ezneeded' => 'exponential', 'ezcontentobject' => 'exponential' ) );
        $result = $report->result();
        $this->assertSame( 'differences', $result['status'] );
        $this->assertSame( array( 'custom_data', 'ezcontentobject', 'ezneeded' ), array_column( $result['tables'], 'name' ) );
        $this->assertSame( 3, $result['counts']['tables'] );
        $this->assertSame( 1, $result['counts']['missing_table'] );
        $this->assertSame( 1, $result['counts']['extra_table'] );
        $this->assertSame( 1, $result['counts']['changed_table'] );
        $this->assertSame( 1, $result['counts']['missing_field'] );
        $this->assertSame( 1, $result['counts']['extra_field'] );
        $this->assertSame( 1, $result['counts']['missing_index'] );

        list( $extra, $changed, $missing ) = $result['tables'];
        $this->assertSame( 'extra_table', $extra['kind'] );
        $this->assertTrue( $extra['destructive'] );
        $this->assertSame( '', $extra['source'] );
        $this->assertSame( "DROP TABLE custom_data;\n", $extra['sql'] );
        $this->assertSame( 'missing_table', $missing['kind'] );
        $this->assertFalse( $missing['destructive'] );
        $this->assertSame( 'exponential', $missing['source'] );
        $this->assertSame( 'changed_table', $changed['kind'] );
        $this->assertTrue( $changed['destructive'], 'it drops a field' );
        $this->assertSame( array( 'missing_field', 'extra_field', 'missing_index' ), array_column( $changed['items'], 'kind' ) );
        $this->assertSame( 'int(11) NOT NULL DEFAULT 0', $changed['items'][0]['expected'] );
        $this->assertSame( 'varchar(20)', $changed['items'][1]['actual'] );
        $this->assertSame( 'non-unique (flags)', $changed['items'][2]['expected'] );
        $this->assertStringContainsString( "ALTER TABLE ezcontentobject ADD flags;\n", $changed['sql'] );
        $this->assertStringContainsString( 'CREATE TABLE ezneeded', $result['sql'] );
        $this->assertStringContainsString( 'DROP TABLE custom_data', $result['sql'] );
    }

    /** SC-04 */
    public function testSchemaEngineNotes()
    {
        $differences = array( 'table_changes' => array(
            'expmail_pending' => array( 'changed_fields' => array(
                'data' => array( 'different-options' => array( 'type' ), 'field-def' => array( 'type' => 'longtext' ) ) ) ) ) );
        $current = array( 'expmail_pending' => array( 'fields' => array( 'data' => array( 'type' => 'text' ) ), 'indexes' => array() ) );
        $result = expSchemaConsistencyReport::fromDifferences( $differences, function () { return ''; }, 'sqlite', $current )->result();
        $this->assertSame( 'ok', $result['status'] );
        $this->assertSame( 0, $result['counts']['tables'] );
        $this->assertSame( 1, $result['counts']['noise'] );
        $this->assertSame( 1, $result['counts']['changed_field'] );
        $this->assertTrue( $result['tables'][0]['noise'] );
        $this->assertFalse( $result['tables'][0]['destructive'] );
        $this->assertSame( 'type', $result['tables'][0]['items'][0]['detail'] );
        $this->assertSame( 'longtext', $result['tables'][0]['items'][0]['expected'] );
        $this->assertSame( 'text', $result['tables'][0]['items'][0]['actual'] );

        $report = expSchemaConsistencyReport::fromDifferences( $differences, function () { return ''; }, 'sqlite', $current );
        $this->assertStringContainsString( '-- expmail_pending: engine note, nothing to run', $report->sqlText() );

        // without a SQL writer nothing can be called a note
        $result = expSchemaConsistencyReport::fromDifferences( $differences, null, 'sqlite' )->result();
        $this->assertSame( 'differences', $result['status'] );
        $this->assertSame( 0, $result['counts']['noise'] );
    }

    /** SC-05 */
    public function testSchemaWordsAndDownload()
    {
        $this->assertSame( "varchar(255) NOT NULL DEFAULT ''", expSchemaConsistencyReport::describeField( array( 'type' => 'varchar', 'length' => 255, 'not_null' => '1', 'default' => '' ) ) );
        $this->assertSame( 'longtext', expSchemaConsistencyReport::describeField( array( 'type' => 'longtext', 'default' => false ) ) );
        $this->assertSame( '', expSchemaConsistencyReport::describeField( true ) );
        $this->assertSame( 'unique (a, b)', expSchemaConsistencyReport::describeIndex( array( 'type' => 'unique', 'fields' => array( 'a', array( 'name' => 'b', 'mysql:length' => 10 ) ) ) ) );
        $this->assertSame( array( 'a', 'b' ), expSchemaConsistencyReport::tableNames( array( '_info' => array(), 'a' => 1, 'b' => 2 ) ) );

        $report = expSchemaConsistencyReport::fromDifferences( array( 'removed_tables' => array( 'custom_data' => array() ) ), $this->writer(), 'postgresql' );
        $text = $report->sqlText( 'Check' );
        $this->assertStringStartsWith( "-- Check\n-- Engine: postgresql\n-- Result: differences\n", $text );
        $this->assertStringContainsString( "-- custom_data: extra table DESTRUCTIVE\nDROP TABLE custom_data;\n", $text );
    }

    /** SC-06 */
    public function testSchemaDocumentStore()
    {
        $result = expSchemaConsistencyReport::mongoResult( array( 'ezorder', 'ezmedia', 'ezcustomthing', 'ezcontentobject', 'notez' ),
                                                           array( 'ezcontentobject', 'leftover' ), 'mongo' );
        $this->assertFalse( $result['relational'] );
        $this->assertSame( 'differences', $result['status'] );
        $this->assertSame( array( 'ezorder', 'ezmedia', 'ezcustomthing' ), $result['mongo']['missing'] );
        $this->assertSame( array( 'leftover' ), $result['mongo']['extra'] );
        $this->assertSame( array( 'Shop / Commerce', 'Media / Binary', 'Other' ), array_column( $result['mongo']['groups'], 'name' ) );
        $this->assertStringContainsString( "db.createCollection(c);", $result['mongo']['create_command'] );
        $this->assertSame( 3, $result['counts']['missing_table'] );

        $ok = expSchemaConsistencyReport::mongoResult( array( 'ezcontentobject' ), array( 'ezcontentobject' ) );
        $this->assertSame( 'ok', $ok['status'] );
        $this->assertSame( '', $ok['mongo']['create_command'] );
    }
}
