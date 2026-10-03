<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** The cron jobs and command line scripts of the extension. */
class cjwNewsletterCronjobTest extends cjwNewsletterTestCase
{
    public function testCronjobPartsAreDeclaredAndTheirFilesExist()
    {
        $ini = eZINI::instance( 'cronjob.ini' );
        $this->assertContains( 'cjw_newsletter', $ini->variable( 'CronjobSettings', 'ExtensionDirectories' ) );
        $this->assertSame( array( 'cjw_newsletter_mailqueue_create.php', 'cjw_newsletter_mailqueue_process.php' ), $ini->variable( 'CronjobPart-cjw_newsletter', 'Scripts' ) );
        foreach ( array( 'cjw_newsletter_mailqueue_create', 'cjw_newsletter_mailqueue_process' ) as $name )
        {
            $this->assertFileExists( 'extension/cjw_newsletter/cronjobs/' . $name . '.php' );
            $this->assertFileExists( 'extension/cjw_newsletter/classes/runnable/cronjobs/' . $name . '.php' );
        }
    }

    public function testCronjobsWithNothingToDoSendNothing()
    {
        $create = $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->assertStringContainsString( 'START: cjw_newsletter_mailqueue_create', $create );
        $this->assertStringContainsString( 'END: cjw_newsletter_mailqueue_create', $create );
        $process = $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $this->assertStringContainsString( 'START: cjw_newsletter_mailqueue_process', $process );
        $this->assertStringContainsString( 'END: cjw_newsletter_mailqueue_process', $process );
        $this->assertCount( 0, $this->outbox() );
    }

    public function testCreateJobConfirmsUsersWaitingForAnEnabledEzUserAndResetsOthers()
    {
        $waiting = $this->newSubscriber( 'wait', CjwNewsletterUser::STATUS_PENDING_EZ_USER_REGISTER, CjwNewsletterSubscription::STATUS_PENDING );
        $waiting->setAttribute( 'ez_user_id', 999999999 );
        eZPersistentObject::storeObject( $waiting );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->assertSame( CjwNewsletterUser::STATUS_PENDING, (int)CjwNewsletterUser::fetch( $waiting->attribute( 'id' ) )->attribute( 'status' ), 'an ez user that is gone resets the status' );
    }

    public function testCreateJobEscalatesAScheduledSendWhoseTimeHasCome()
    {
        $this->newSubscriber( 'esc' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_CREATED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertGreaterThanOrEqual( 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
    }

    public function testProcessJobPersonalisesWhenTheListAsksForIt()
    {
        $user = $this->newSubscriber( 'pers' );
        $edition = $this->newEdition();
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() - 5 );
        // an output xml with every placeholder, personalisation switched on
        $xml = new DOMDocument();
        $xml->loadXML( $send->attribute( 'output_xml' ) );
        foreach ( $xml->getElementsByTagName( 'type' ) as $type )
            if ( $type->getAttribute( 'name' ) === 'text' )
                $type->nodeValue = 'Hello [[first_name]] [[last_name]] [[name]] [[salutation_name]] | #_hash_unsubscribe_# | #_hash_configure_# | #_hash_item_# | #_hash_edition_#';
        $send->setAttribute( 'output_xml', $xml->saveXML() );
        $send->setAttribute( 'personalize_content', 1 );
        $send->store();
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $found = false;
        foreach ( $this->outbox() as $file )
        {
            $text = $this->mailText( $file );
            if ( strpos( $text, $user->attribute( 'email' ) ) === false )
                continue;
            $found = true;
            $this->assertStringContainsString( 'Hello Test Subscriber', $text, 'name placeholders' );
            $this->assertStringNotContainsString( '[[', $text );
            $this->assertStringNotContainsString( '#_hash_', $text );
            $sub = $this->subscriptionOf( $user );
            $this->assertStringContainsString( $sub->attribute( 'hash' ), $text );
            $this->assertStringContainsString( $user->attribute( 'hash' ), $text );
            $this->assertStringContainsString( $send->attribute( 'hash' ), $text );
        }
        $this->assertTrue( $found );
    }

    public function testProcessJobKeepsPlaceholdersOfNamesWhenPersonalisationIsOff()
    {
        $user = $this->newSubscriber( 'nopers' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $xml = new DOMDocument();
        $xml->loadXML( $send->attribute( 'output_xml' ) );
        foreach ( $xml->getElementsByTagName( 'type' ) as $type )
            if ( $type->getAttribute( 'name' ) === 'text' )
                $type->nodeValue = 'Hello [[first_name]]';
        $send->setAttribute( 'output_xml', $xml->saveXML() );
        $send->setAttribute( 'personalize_content', 0 );
        $send->store();
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        foreach ( $this->outbox() as $file )
            if ( strpos( $this->mailText( $file ), $user->attribute( 'email' ) ) !== false )
                $this->assertStringContainsString( 'Hello [[first_name]]', $this->mailText( $file ) );
    }

    public function testProcessJobAbortsItemsWhoseMailCannotBeWritten()
    {
        $user = $this->newSubscriber( 'failsend' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'FileTransportMailDir', '/proc/nltest-no-such-dir' );
        $out = $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $this->assertStringContainsString( '[FAILED]', $out );
        $items = CjwNewsletterEditionSendItem::fetchListSendIdAndStatus( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_ABORT, 0, 0 );
        $this->assertNotEmpty( $items, 'the item is aborted' );
        $this->assertGreaterThan( 0, (int)$items[0]->attribute( 'bounced' ) );
        $this->assertSame( 1, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'bounce_count' ), 'and the user counts a bounce' );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testProcessJobAbortsItemsOfAUserThatIsGone()
    {
        $user = $this->newSubscriber( 'gone' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        eZDB::instance()->query( 'DELETE FROM cjwnl_user WHERE id = ' . (int)$user->attribute( 'id' ) );
        $out = $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $this->assertStringContainsString( 'newsletter_user_object not available', $out );
        $this->assertCount( 0, $this->outbox() );
    }

    public function testProcessJobAbortsItemsOfASubscriptionThatIsGone()
    {
        $user = $this->newSubscriber( 'nosub' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->subscriptionOf( $user )->remove();
        $out = $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $this->assertStringContainsString( 'newsletter_subscription_object not available', $out );
    }

    public function testRemovedAndBlacklistedSubscribersAreNotMailed()
    {
        $in = $this->newSubscriber( 'in' );
        $removed = $this->newSubscriber( 'out' );
        $this->subscriptionOf( $removed )->unsubscribe();
        $black = $this->newSubscriber( 'black' );
        $black->setBlacklisted();
        $pending = $this->newSubscriber( 'pend', null, CjwNewsletterSubscription::STATUS_PENDING );
        $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $all = '';
        foreach ( $this->outbox() as $file )
            $all .= $this->mailText( $file );
        $this->assertStringContainsString( $in->attribute( 'email' ), $all );
        foreach ( array( $removed, $black, $pending ) as $other )
            $this->assertStringNotContainsString( $other->attribute( 'email' ), $all, $other->attribute( 'email' ) );
    }

    public function testCommandLineOutputScriptPrintsASerializedResult()
    {
        $edition = $this->newEdition();
        $cmd = 'php extension/cjw_newsletter/bin/php/createoutput.php --object_id=' . (int)$edition->attribute( 'id' )
             . ' --object_version=1 --output_format_id=1 --current_hostname=alpha.se7enx.com --skin_name=default -s site --allow-root-user';
        $out = shell_exec( $cmd . ' 2>&1' );
        $this->assertNotFalse( strpos( (string)$out, 'a:' ) );
        $result = @unserialize( substr( $out, strpos( $out, 'a:' ) ) );
        $this->assertIsArray( $result );
        foreach ( array( 'subject', 'body', 'content_type', 'output_format', 'ez_root', 'html_mail_image_include' ) as $key )
            $this->assertArrayHasKey( $key, $result );
        $this->assertSame( 'text/plain', $result['content_type'] );
        $this->assertStringContainsString( 'NLTEST article body line', $result['body']['text'] );
    }

    public function testCommandLineIniScriptPrintsTheSiteIni()
    {
        $out = shell_exec( 'php extension/cjw_newsletter/bin/php/iniloader.php -s site site.ini --allow-root-user 2>&1' );
        $this->assertNotFalse( strpos( (string)$out, 'O:' ) );
        $ini = @unserialize( substr( $out, strpos( $out, 'O:' ) ), array( 'allowed_classes' => array( 'eZINI' ) ) );
        $this->assertInstanceOf( 'eZINI', $ini );
    }

    public function testCommandLineScriptsRefuseToRunAsRootWithoutTheFlag()
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            $this->markTestSkipped( 'not running as root' );
        $out = shell_exec( 'php extension/cjw_newsletter/bin/php/iniloader.php -s site site.ini 2>&1' );
        $this->assertStringContainsString( 'Running scripts as root may be dangerous', (string)$out );
    }

    public function testTextHelpersOfTheOutputScript()
    {
        $this->assertTrue( function_exists( 'formatText' ) || class_exists( 'Exponential\\Command\\Extension\\CjwNewsletter\\Createoutput' ) );
    }
}
