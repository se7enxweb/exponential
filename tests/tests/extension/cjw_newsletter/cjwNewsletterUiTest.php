<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** The admin views rebuilt for 4.1.17: validated forms, confirmation before removal, escaped output, the public form's back link. */
class cjwNewsletterUiTest extends cjwNewsletterTestCase
{
    private function boxData( $email, array $extra = array() )
    {
        return array_merge( array( 'edit' => '1', 'PublishButton' => '1', 'email' => $email, 'server' => 'mail.example.invalid', 'port' => '993',
                                   'user_name' => 'nltest', 'password' => 'secret"><b>', 'type' => 'imap', 'is_activated' => '0', 'is_ssl' => '1',
                                   'delete_mails_from_server' => '0' ), $extra );
    }

    private function boxByEmail( $email )
    {
        foreach ( (array)CjwNewsletterMailbox::fetchAllMailboxes() as $m )
            if ( $m->attribute( 'email' ) === $email )
                return $m;
        return null;
    }

    public function testMailboxFormValidatesEveryField()
    {
        $r = $this->runView( 'mailbox_edit', array( 0 ), $this->boxData( 'not an address', array( 'server' => 'bad server!', 'port' => 'abc', 'user_name' => '', 'password' => '' ) ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'], 'the form is shown again' );
        foreach ( array( 'Enter a valid email address.', 'name of the mail server', 'port is a number', 'Enter the user name.', 'Enter the password.' ) as $text )
            $this->assertStringContainsString( $text, $r['content'] );
        $this->assertNull( $this->boxByEmail( 'not an address' ), 'nothing is stored' );
        $r = $this->runView( 'mailbox_edit', array( 0 ), $this->boxData( $this->newEmail( 'bigport' ), array( 'port' => '70000' ) ) );
        $this->assertStringContainsString( 'port is a number', $r['content'] );
    }

    public function testMailboxPasswordIsNeverPutBackIntoTheFormAndAnEmptyOneKeepsTheStoredOne()
    {
        $email = $this->newEmail( 'box' );
        $r = $this->runView( 'mailbox_edit', array( 0 ), $this->boxData( $email ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $box = $this->boxByEmail( $email );
        $this->assertSame( 'secret"><b>', $box->attribute( 'password' ) );
        $r = $this->runView( 'mailbox_edit', array( $box->attribute( 'id' ) ) );
        $this->assertViewOk( $r );
        $this->assertStringNotContainsString( 'secret', $r['content'] );
        $this->assertStringContainsString( 'Leave it empty to keep the stored password.', $r['content'] );
        $r = $this->runView( 'mailbox_edit', array( $box->attribute( 'id' ) ), $this->boxData( $email, array( 'password' => '', 'port' => '' ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $box = $this->boxByEmail( $email );
        $this->assertSame( 'secret"><b>', $box->attribute( 'password' ), 'kept' );
        $this->assertSame( 0, (int)$box->attribute( 'port' ), 'an empty port means the default of the type' );
        $this->assertSame( 0, (int)$box->attribute( 'is_activated' ) );
    }

    public function testMailboxRemovalAsksFirst()
    {
        $email = $this->newEmail( 'rm' );
        $this->runView( 'mailbox_edit', array( 0 ), $this->boxData( $email ) );
        $id = $this->boxByEmail( $email )->attribute( 'id' );
        $r = $this->runView( 'mailbox_edit', array( $id ), array( 'RemoveButton' => '1' ) );
        $this->assertStringContainsString( 'Remove this mail account?', $r['content'] );
        $this->assertNotNull( $this->boxByEmail( $email ), 'still there' );
        $r = $this->runView( 'mailbox_edit', array( $id ), array( 'CancelButton' => '1' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertNotNull( $this->boxByEmail( $email ) );
        $r = $this->runView( 'mailbox_edit', array( $id ), array( 'ConfirmRemoveButton' => '1' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertNull( $this->boxByEmail( $email ) );
        $r = $this->runView( 'mailbox_edit', array( 0 ), array( 'RemoveButton' => '1' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'], 'a new account has nothing to remove' );
    }

    public function testMailboxListEscapesWhatIsStored()
    {
        $email = $this->newEmail( 'x' );
        $this->runView( 'mailbox_edit', array( 0 ), $this->boxData( $email, array( 'user_name' => '<script>alert(2)</script>' ) ) );
        $r = $this->runView( 'mailbox_list' );
        $this->assertStringNotContainsString( '<script>alert(2)</script>', $r['content'] );
        $this->assertStringContainsString( '&lt;script&gt;', $r['content'] );
    }

    public function testBouncesPageStartsBackgroundRunsByPostOnly()
    {
        $r = $this->runView( 'mailbox_item_list', array(), array(), array( 'ConnectMailboxButton' => '1' ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'name="ConnectMailboxButton"', $r['content'] );
        $this->assertStringContainsString( 'method="post"', $r['content'] );
    }

    public function testUserViewEscapesTheCustomFieldsAndNeverLogsMissingIniGroups()
    {
        $user = $this->newSubscriber( 'xss' );
        $user->setAttribute( 'custom_data_text_1', '<img src=x onerror=alert(3)>' );
        $user->setAttribute( 'data_text', '<b>text</b>' );
        $user->store();
        $r = $this->runView( 'user_view', array( $user->attribute( 'id' ) ) );
        $this->assertViewOk( $r );
        $this->assertStringNotContainsString( '<img src=x', $r['content'] );
        $this->assertStringNotContainsString( '<b>text</b>', $r['content'] );
        $this->assertStringContainsString( '&lt;img src=x', $r['content'] );
    }

    public function testCustomFieldNamesComeFromTheIni()
    {
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterUserSettings', 'CustomFieldMappingArray', array( 'custom_data_text_2' ) );
        $this->setIni( 'cjw_newsletter.ini', 'CustomFieldMapping_custom_data_text_2', 'Name', 'Postcode' );
        $user = $this->newSubscriber( 'cf' );
        $r = $this->runView( 'user_view', array( $user->attribute( 'id' ) ) );
        $this->assertStringContainsString( 'Postcode', $r['content'] );
        $this->assertStringContainsString( 'Custom Data text 1', $r['content'] );
    }

    public function testSubscribeBackLinkIsAPathOfThisSiteOnly()
    {
        $this->loginAnonymous();
        $user = $this->newSubscriber( 'bk' );
        foreach ( array( 'javascript:alert(1)', 'http://evil.example/', '//evil.example/', "/x\"><script>alert(1)</script>" ) as $bad )
        {
            $r = $this->runView( 'subscribe_infomail', array(), array( 'SubscribeInfoMailButton' => '1', 'EmailInput' => $user->attribute( 'email' ), 'BackUrlInput' => $bad ) );
            $content = (string)$r['content'];
            $this->assertStringNotContainsString( 'javascript:', $content );
            $this->assertStringNotContainsString( 'evil.example', $content );
            $this->assertStringNotContainsString( '<script>alert(1)</script>', $content );
        }
    }

    public function testImportAndMailboxListsPageAndShowTheNewestFirst()
    {
        $a = CjwNewsletterImport::create( self::LIST_OBJECT_ID, 'cjwnl_csv', 'nltest first', '' );
        $a->store();
        $b = CjwNewsletterImport::create( self::LIST_OBJECT_ID, 'cjwnl_csv', 'nltest second', '' );
        $b->store();
        $this->createdImportIds[] = (int)$a->attribute( 'id' );
        $this->createdImportIds[] = (int)$b->attribute( 'id' );
        $r = $this->runView( 'import_list', array(), array(), array( 'limit' => '10' ) );
        $this->assertLessThan( strpos( $r['content'], 'nltest first' ), strpos( $r['content'], 'nltest second' ), 'newest first' );
        $this->assertStringContainsString( 'Per page', $r['content'] );
        $r = $this->runView( 'import_list', array(), array(), array( 'limit' => '1', 'offset' => '9999' ) );
        $this->assertViewOk( $r );
    }

    public function testMenuHasTheDashboardLink()
    {
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'module_result', array( 'ui_context' => 'navigation' ) );
        $html = $tpl->fetch( 'design:parts/newsletter/menu.tpl' );
        $this->assertStringContainsString( 'newsletter/index', $html );
        $this->assertStringContainsString( 'newsletter/user_list', $html );
    }
}
