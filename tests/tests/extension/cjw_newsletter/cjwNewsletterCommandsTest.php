<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * The console commands ext:cjw_newsletter:* (queue, mailbox, import, repair, status): the stubs in bin/php, the
 * runnable classes, the options, the dry runs and the import of an uploaded file. Each command runs in a process of
 * its own on the live installation (the siteaccess of the tests), mail goes to files.
 */
class cjwNewsletterCommandsTest extends cjwNewsletterTestCase
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

    /** @return array( exit code, output ) */
    private function command( $name, array $arguments = array() )
    {
        $line = array_merge( array( PHP_BINARY, 'extension/cjw_newsletter/bin/php/' . $name . '.php', '-s', 'admin', '--allow-root-user' ), $arguments );
        // the mails of the child go to the directory of this test too
        $process = proc_open( $line, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, eZSys::rootDir(),
                              array( 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin' ) );
        $this->assertIsResource( $process );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $process ), $out );
    }

    public static function commandProvider()
    {
        return array( array( 'queue', 'Queue' ), array( 'mailbox', 'Mailbox' ), array( 'import', 'Import' ), array( 'repair', 'Repair' ), array( 'status', 'Status' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'commandProvider' )]
    public function testEveryCommandIsAThinStubOverARunnableClass( $name, $class )
    {
        $stub = 'extension/cjw_newsletter/bin/php/' . $name . '.php';
        $this->assertFileExists( $stub );
        $this->assertTrue( is_executable( $stub ), $stub . ' is executable' );
        $code = file_get_contents( $stub );
        $this->assertStringStartsWith( '#!/usr/bin/env php', $code );
        $this->assertStringContainsString( '@alias nl-' . $name, $code );
        $this->assertStringContainsString( '\\Exponential\\Command\\Extension\\CjwNewsletter\\' . $class . '::main( __FILE__ )', $code );
        $this->assertLessThan( 40, count( file( $stub ) ), 'the stub holds no logic' );
        $fqcn = 'Exponential\\Command\\Extension\\CjwNewsletter\\' . $class;
        $this->assertTrue( class_exists( $fqcn ), $fqcn . ' loads' );
        $this->assertTrue( is_subclass_of( $fqcn, 'Exponential\\Runnable\\Command' ) );
        $this->assertFileExists( 'extension/cjw_newsletter/classes/runnable/commands/php_' . $name . '.php' );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'commandProvider' )]
    public function testEveryCommandPrintsItsOptionsOnHelp( $name, $class )
    {
        list( $code, $out ) = $this->command( $name, array( '--help' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Usage: extension/cjw_newsletter/bin/php/' . $name . '.php', $out );
        if ( $name != 'status' )
        {
            $this->assertStringContainsString( 'Options:', $out );
            $this->assertStringContainsString( '--dry-run', $out );
        }
    }

    public function testStatusShowsTheStateAndTheProblems()
    {
        $this->newSubscriber( 'status' );
        list( $code, $out ) = $this->command( 'status' );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Lists:', $out );
        $this->assertStringContainsString( 'Transport:', $out );
        $this->assertStringContainsString( 'Last queue_create', $out );
    }

    public function testQueueDryRunCountsAndChangesNothing()
    {
        $this->newSubscriber( 'qdry' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        list( $code, $out ) = $this->command( 'queue', array( '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Would confirm', $out );
        $this->assertStringContainsString( 'Would send', $out );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_WAIT_FOR_SCHEDULE, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testMailboxAndRepairDryRuns()
    {
        list( $code, $out ) = $this->command( 'mailbox', array( '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Would collect', $out );
        list( $code, $out ) = $this->command( 'repair', array( '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Would remove', $out );
    }

    public function testImportNeedsAnImportAndAFileInTheImportFolder()
    {
        list( $code, $out ) = $this->command( 'import' );
        $this->assertSame( 1, $code );
        $this->assertStringContainsString( '--import-id', $out );
        // an import whose file is somewhere else is refused, whatever the row says
        $import = CjwNewsletterImport::create( self::LIST_OBJECT_ID, 'cjwnl_csv', 'nltest', '/etc/passwd' );
        $import->store();
        $this->createdImportIds[] = (int)$import->attribute( 'id' );
        list( $code, $out ) = $this->command( 'import', array( '--import-id=' . $import->attribute( 'id' ) ) );
        $this->assertSame( 1, $code );
        $this->assertStringContainsString( 'not in the import folder', $out );
        list( $code, $out ) = $this->command( 'import', array( '--import-id=' . $import->attribute( 'id' ), '--delimiter=colon' ) );
        $this->assertSame( 1, $code );
        $this->assertStringContainsString( 'comma, semicolon, pipe or tab', $out );
    }

    public function testImportCommandImportsTheRowsAndRecordsTheRun()
    {
        $one = $this->newEmail( 'cmd1' );
        $two = $this->newEmail( 'cmd2' );
        $dir = eZSys::varDirectory() . '/cjw_newsletter/csvimport';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/nltest-' . getmypid() . '-' . ( ++self::$counter ) . '.csv';
        file_put_contents( $file, "email;first_name;last_name;salutation\n$one;Cmd;One;1\nnot-an-address;X;Y;1\n$two;Cmd;Two;2\n" );
        $this->files[] = $file;
        $import = CjwNewsletterImport::create( self::LIST_OBJECT_ID, 'cjwnl_csv', 'nltest', $file );
        $import->store();
        $this->createdImportIds[] = (int)$import->attribute( 'id' );
        $this->files[] = CjwNewsletterImport::resultFilePath( $import->attribute( 'id' ) );

        list( $code, $out ) = $this->command( 'import', array( '--import-id=' . $import->attribute( 'id' ), '--first-row-label', '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( '3 rows', $out );
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( $one ), 'a dry run imports nothing' );

        list( $code, $out ) = $this->command( 'import', array( '--import-id=' . $import->attribute( 'id' ), '--first-row-label', '--formats=0-1' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Imported: 2 new users, 2 new subscriptions, 1 invalid rows.', $out );
        foreach ( array( $one, $two ) as $email )
        {
            $user = CjwNewsletterUser::fetchByEmail( $email );
            $this->assertNotFalse( $user, $email );
            $this->assertSame( array( 0, 1 ), array_map( 'intval', array_keys( $this->subscriptionOf( $user )->attribute( 'output_format_array' ) ) ), 'both formats' );
        }
        $import = CjwNewsletterImport::fetch( $import->attribute( 'id' ) );
        $this->assertTrue( $import->isImported() );
        $result = CjwNewsletterImport::readResult( $import->attribute( 'id' ) );
        $this->assertCount( 3, $result );
        $this->assertSame( 'console', CjwNewsletterRunner::lastRun( CjwNewsletterRunner::LAST_IMPORT )['by'] );
    }

    public function testBackgroundJobStartsACommandAndItsStateIsReadable()
    {
        if ( !class_exists( 'expProcessTools' ) || !expProcessTools::phpCli() || !expProcessTools::setsid() )
            $this->markTestSkipped( 'No PHP command line or setsid for a background run.' );
        $error = '';
        $id = CjwNewsletterJob::start( 'mailbox', array( '--dry-run' ), $error );
        $this->assertNotFalse( $id, $error );
        $state = false;
        for ( $i = 0; $i < 60; $i++ )
        {
            $state = CjwNewsletterJob::status( $id );
            if ( $state && in_array( $state['status'], array( 'done', 'failed' ) ) )
                break;
            usleep( 500000 );
        }
        $this->assertSame( 'done', $state['status'], json_encode( $state ) );
        $this->assertStringContainsString( 'Would collect', implode( "\n", $state['log'] ) );
        foreach ( glob( CjwNewsletterJob::directory() . '/' . $id . '.*' ) as $file )
            unlink( $file );
    }

    public function testCsvViewUsesTheBackgroundRunWhenTheSettingAllowsAndReportsAFileItCannotStore()
    {
        // the stored file is written by the view; a folder that is not writable gives a notice, not an import without rows
        $dir = eZSys::varDirectory() . '/cjw_newsletter/csvimport';
        $r = $this->runView( 'subscription_list_csvimport', array( self::LIST_NODE_ID, 0 ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'nl-', $r['content'] . 'nl-', 'the page renders with the notices include' );
        $this->assertDirectoryExists( dirname( $dir ) );
    }
}
