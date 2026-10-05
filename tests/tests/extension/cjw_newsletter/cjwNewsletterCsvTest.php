<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** CSV parser, CSV export, the import object and the import and export views. */
class cjwNewsletterCsvTest extends cjwNewsletterTestCase
{
    private $files = array();

    public function tearDown(): void
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
        $this->files = array();
        parent::tearDown();
    }

    private function csvFile( $text, $dir = 'var/tmp' )
    {
        $path = eZSys::siteDir() . $dir . '/nltest-' . getmypid() . '-' . ( ++self::$counter ) . '.csv';
        if ( !is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0775, true );
        file_put_contents( $path, $text );
        $this->files[] = $path;
        return $path;
    }

    private function mapping()
    {
        return array( 'email' => 'email', 'first_name' => 'first_name', 'last_name' => 'last_name', 'salutation' => 'salutation' );
    }

    public function testParserReadsRowsAndSkipsTheLabelRow()
    {
        $file = $this->csvFile( "email;first_name;last_name;salutation\na@example.invalid;Ann;Tester;1\nb@example.invalid;Bob;Test;2\n" );
        $rows = ( new CjwNewsletterCsvParser( $file, ';', true, $this->mapping() ) )->getCsvDataArray();
        $this->assertCount( 2, $rows );
        $this->assertSame( 'Ann', $rows[1]['first_name'] );
        $all = ( new CjwNewsletterCsvParser( $file, ';', false, $this->mapping() ) )->getCsvDataArray();
        $this->assertCount( 3, $all );
        $this->assertSame( 'email', $all[0]['email'] );
    }

    public function testParserHandlesEveryDelimiterTheViewOffers()
    {
        foreach ( array( ',' => ',', ';' => ';', '|' => '|', "\t" => '\t' ) as $real => $given )
        {
            $file = $this->csvFile( "x@example.invalid{$real}First{$real}Last{$real}1\n" );
            $rows = ( new CjwNewsletterCsvParser( $file, $given, false, $this->mapping() ) )->getCsvDataArray();
            $this->assertSame( 'First', $rows[0]['first_name'], 'delimiter ' . json_encode( $given ) );
        }
    }

    public function testParserAcceptsLinesLongerThanAThousandCharacters()
    {
        $file = $this->csvFile( 'x@example.invalid;' . str_repeat( 'N', 3000 ) . ";Last;1\n" );
        $rows = ( new CjwNewsletterCsvParser( $file, ';', false, $this->mapping() ) )->getCsvDataArray();
        $this->assertSame( 3000, strlen( $rows[0]['first_name'] ) );
        $this->assertSame( 'Last', $rows[0]['last_name'] );
    }

    public function testParserConvertsLatin1WhenAsked()
    {
        $file = $this->csvFile( "x@example.invalid;J\xFCrgen;M\xFCller;1\n" );
        $rows = ( new CjwNewsletterCsvParser( $file, ';', false, $this->mapping(), true ) )->getCsvDataArray();
        $this->assertSame( 'Jürgen', $rows[0]['first_name'] );
    }

    public function testParserWithAMissingFileOrBadDelimiterDoesNotFatal()
    {
        $this->assertSame( array(), ( new CjwNewsletterCsvParser( '/no/such/file.csv', ';', true, $this->mapping() ) )->getCsvDataArray() );
        $file = $this->csvFile( "x@example.invalid;A;B;1\n" );
        $this->assertCount( 1, ( new CjwNewsletterCsvParser( $file, 'two chars', false, $this->mapping() ) )->getCsvDataArray(), 'a delimiter that is no single character falls back' );
    }

    public function testParserKeepsShortRowsAndQuotedFields()
    {
        $file = $this->csvFile( "x@example.invalid;\"Quoted; semicolon\"\ny@example.invalid\n" );
        $rows = ( new CjwNewsletterCsvParser( $file, ';', false, $this->mapping() ) )->getCsvDataArray();
        $this->assertSame( 'Quoted; semicolon', $rows[0]['first_name'] );
        $this->assertArrayNotHasKey( 'first_name', $rows[1] );
    }

    public function testExportWritesHeaderAndRowsInColumnOrder()
    {
        $data = array( array( 'email', 'first_name', 'last_name' ),
                       array( 'email' => 'a@example.invalid', 'first_name' => 'Ann', 'last_name' => 'T' ),
                       array( 'email' => 'b@example.invalid', 'first_name' => null, 'last_name' => 'U' ) );
        $export = new CjwNewsletterCsvExport( $data, ';', array( 'email', 'first_name', 'last_name' ) );
        $export->writeCsv();
        $lines = preg_split( '/\r\n/', $export->CsvResult );
        $this->assertSame( 'email;first_name;last_name', $lines[0] );
        $this->assertSame( 'a@example.invalid;Ann;T', $lines[1] );
        $this->assertSame( 'b@example.invalid;;U', $lines[2], 'a null value is an empty column, the later columns stay in place' );
    }

    public function testExportEscapesTheDelimiterNewlinesAndFormulas()
    {
        $data = array( array( 'email', 'note' ),
                       array( 'email' => 'a@example.invalid', 'note' => "a;b\nc" ),
                       array( 'email' => 'b@example.invalid', 'note' => '=HYPERLINK("http://x")' ),
                       array( 'email' => 'c@example.invalid', 'note' => '-5' ) );
        $export = new CjwNewsletterCsvExport( $data, ';', array( 'email', 'note' ) );
        $export->writeCsv();
        $lines = preg_split( '/\r\n/', $export->CsvResult );
        $this->assertCount( 4, $lines, 'a newline inside a value does not add a line' );
        $this->assertStringContainsString( '[c59]', $lines[1] );
        $this->assertStringContainsString( ";'=HYPERLINK", $lines[2], 'a formula is written as text' );
        $this->assertSame( 'c@example.invalid;-5', $lines[3], 'a plain number is left alone' );
    }

    public function testExportOfNoDataAndOfOtherDelimiters()
    {
        $export = new CjwNewsletterCsvExport( array(), ';' );
        $export->writeCsv();
        $this->assertSame( '', $export->CsvResult );
        $data = array( array( 'a', 'b' ), array( 'a' => '1', 'b' => '2' ) );
        $export = new CjwNewsletterCsvExport( $data, ',', array( 'a', 'b' ) );
        $export->writeCsv();
        $this->assertSame( "a,b\r\n1,2", $export->CsvResult );
    }

    public function testParserReadsOldMacLineEndingsWithoutTheDeprecatedIniSetting()
    {
        $one = $this->newEmail( 'cr1' );
        $two = $this->newEmail( 'cr2' );
        $file = $this->csvFile( "email;first_name\r$one;A\r$two;B\r" );
        $parser = new CjwNewsletterCsvParser( $file, ';', true, array( 'email' => 'email', 'first_name' => 'first_name' ) );
        $rows = array_values( $parser->getCsvDataArray() );
        $this->assertCount( 2, $rows );
        $this->assertSame( $two, $rows[1]['email'] );
        $this->assertSame( 'A', $rows[0]['first_name'] );
        $this->assertStringNotContainsString( "ini_set( 'auto_detect_line_endings'", file_get_contents( 'extension/cjw_newsletter/classes/cjwnewslettercsvparser.php' ), 'the setting is deprecated since PHP 8.1' );
    }

    public function testExportViewPreviewListsTheSubscribers()
    {
        $user = $this->newSubscriber( 'exp' );
        $r = $this->runView( 'subscription_list_csvexport', array( self::LIST_NODE_ID ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( $user->attribute( 'email' ), $r['content'] );
        $this->assertTrue( function_exists( 'getDataForCsv' ) || function_exists( '\\getDataForCsv' ) );
        $data = getDataForCsv( self::LIST_OBJECT_ID, 0 );
        $this->assertIsArray( $data );
        $this->assertContains( 'email', $data[0] );
        $this->assertCount( 1, array_slice( getDataForCsv( self::LIST_OBJECT_ID, 1 ), 1 ), 'the limit applies' );
    }

    public function testImportViewShowsTheUploadFormAndRefusesAnUnknownList()
    {
        $this->assertViewOk( $this->runView( 'subscription_list_csvimport', array( self::LIST_NODE_ID, 0 ) ) );
        $r = $this->runView( 'subscription_list_csvimport', array( 999999999, 0 ) );
        $this->assertSame( eZModule::STATUS_FAILED, $r['exit'] );
        $r = $this->runView( 'subscription_list_csvimport', array( self::LIST_NODE_ID, 999999999 ) );
        $this->assertSame( eZModule::STATUS_FAILED, $r['exit'] );
    }

    public function testImportCreatesUsersAndSubscriptionsAndSkipsBadRows()
    {
        // the view imports inside the request here: the background run is tested through the command
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterCsvImportSettings', 'ImportInBackground', 'disabled' );
        $good1 = $this->newEmail( 'imp1' );
        $good2 = $this->newEmail( 'imp2' );
        $blocked = $this->newSubscriber( 'impbl' );
        CjwNewsletterBlacklistItem::create( $blocked->attribute( 'email' ), '' )->store();
        $dir = eZSys::varDirectory() . '/cjw_newsletter/csvimport';
        $file = $this->csvFile( "$good1;Ann;Tester;1\nnot-an-address;X;Y;1\n$good2;Bob;Test;2\n" . $blocked->attribute( 'email' ) . ";Blocked;User;1\n", $dir );
        $import = CjwNewsletterImport::create( self::LIST_OBJECT_ID, 'cjwnl_csv', 'nltest', $file );
        $import->store();
        $this->createdImportIds[] = (int)$import->attribute( 'id' );
        $this->files[] = eZSys::siteDir() . $dir . '/' . $import->attribute( 'id' ) . '-import_result.serialize';

        $r = $this->runView( 'subscription_list_csvimport', array( self::LIST_NODE_ID, $import->attribute( 'id' ) ),
            array( 'ImportButton' => 'Import', 'CsvDelimiter' => ';', 'SelectedOutputFormatArray' => array( 0 ) ) );
        $this->assertViewOk( $r );
        foreach ( array( $good1 => 'Ann', $good2 => 'Bob' ) as $email => $first )
        {
            $user = CjwNewsletterUser::fetchByEmail( $email );
            $this->assertNotFalse( $user, $email );
            $this->assertSame( $first, $user->attribute( 'first_name' ) );
            $this->assertSame( CjwNewsletterUser::STATUS_CONFIRMED, (int)$user->attribute( 'status' ) );
            $this->assertSame( (int)$import->attribute( 'id' ), (int)$user->attribute( 'import_id' ) );
            $this->assertSame( CjwNewsletterSubscription::STATUS_APPROVED, (int)$this->subscriptionOf( $user )->attribute( 'status' ) );
        }
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( 'not-an-address' ) );
        $this->assertSame( CjwNewsletterSubscription::STATUS_BLACKLISTED, (int)$this->subscriptionOf( CjwNewsletterUser::fetchByEmail( $blocked->attribute( 'email' ) ) )->attribute( 'status' ), 'a blacklisted address stays blacklisted' );
        $import = CjwNewsletterImport::fetch( $import->attribute( 'id' ) );
        $this->assertTrue( $import->isImported() );
        $this->assertSame( 2, (int)$import->getImportedUserCountLive() );
        $this->assertSame( 2, (int)$import->getImportedSubscriptionCountLive() );
        $this->assertSame( 2, (int)$import->getImportedSubscriptionCountLiveApproved() );
        $this->assertSame( 2, (int)$import->getImportedUserCountLiveConfirmed() );

        // the import is shown, and an import list and view know it
        $this->assertViewOk( $this->runView( 'import_view', array( $import->attribute( 'id' ) ) ) );
        $list = $this->runView( 'import_list' );
        $this->assertViewOk( $list );
        $this->assertStringContainsString( 'nltest', $list['content'] );

        // and the administrator can take the subscriptions back
        $this->runView( 'import_view', array( $import->attribute( 'id' ) ), array( 'RemoveSubsciptionsByAdminButton' => '1' ) );
        $this->assertTrue( $this->subscriptionOf( CjwNewsletterUser::fetchByEmail( $good1 ) )->isRemoved() );
    }

    public function testImportViewIgnoresAPostedPathOutsideTheImportDirectory()
    {
        $secret = $this->csvFile( "secret-address@example.invalid;Secret;Person;1\n" );
        $r = $this->runView( 'subscription_list_csvimport', array( self::LIST_NODE_ID, 0 ), array( 'CsvFilePath' => $secret, 'CsvDelimiter' => ';' ) );
        $this->assertViewOk( $r );
        $this->assertStringNotContainsString( 'secret-address@example.invalid', $r['content'] );
    }

    public function testImportViewRefusesAnUnsupportedDelimiter()
    {
        $r = $this->runView( 'subscription_list_csvimport', array( self::LIST_NODE_ID, 0 ), array( 'CsvDelimiter' => 'x' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
    }

    public function testImportObjectFetchesAndCounts()
    {
        $import = CjwNewsletterImport::create( self::LIST_OBJECT_ID, 'cjwnl_csv', 'nltest note', '/x.csv', 'nltest-remote' );
        $import->store();
        $this->createdImportIds[] = (int)$import->attribute( 'id' );
        $this->assertFalse( $import->isImported() );
        $this->assertSame( $import->attribute( 'id' ), CjwNewsletterImport::fetch( $import->attribute( 'id' ) )->attribute( 'id' ) );
        $this->assertSame( $import->attribute( 'id' ), CjwNewsletterImport::fetchByRemoteId( 'nltest-remote' )->attribute( 'id' ) );
        $this->assertGreaterThanOrEqual( 1, CjwNewsletterImport::fetchAllImportItemsCount() );
        $this->assertNotEmpty( CjwNewsletterImport::fetchAllImportItems( 0 ) );
        $this->assertNotEmpty( CjwNewsletterImport::fetchAllImportItems( 5, 0 ) );
        $import->setImported();
        $this->assertTrue( CjwNewsletterImport::fetch( $import->attribute( 'id' ) )->isImported() );
        $this->assertInstanceOf( 'eZContentObject', $import->getListContentObject() );
        $this->assertSame( 0, (int)$import->getImportedUserCountLive() );
    }
}
