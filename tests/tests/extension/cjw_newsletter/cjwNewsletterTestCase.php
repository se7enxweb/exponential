<?php
/**
 * The base of the cjw_newsletter tests.
 *
 * Live-database style: the kernel is started once on the admin siteaccess against the installation's own database.
 * Every test works on throwaway data: subscribers use the address pattern nltest-<n>@example.invalid, which tearDown
 * removes together with everything that hangs on them (subscriptions, send items, imports, blacklist entries,
 * mailbox items), as well as the editions and edition sends a test created. Mail is never sent: the transports of
 * cjw_newsletter.ini are switched to the file transport for the test and write into a directory of their own under
 * var/tmp, which tearDown empties. There is never a test database.
 *
 * Run: php vendor/bin/phpunit --testsuite cjw_newsletter
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

abstract class cjwNewsletterTestCase extends PHPUnit\Framework\TestCase
{
    const LIST_OBJECT_ID = 18150;
    const LIST_NODE_ID = 18830;
    const MAIL_DOMAIN = 'example.invalid';

    protected static $script = null;
    protected static $bootError = null;
    protected static $counter = 0;

    protected $mailDir = null;
    protected $savedIni = array();
    protected $createdEditionObjects = array();
    protected $createdImportIds = array();
    protected $createdObjectIds = array();
    protected $extraEmails = array();
    protected $savedPost = array();
    /** @var string[] warnings, notices and deprecations raised inside the extension during the test */
    protected $phpIssues = array();
    protected $previousHandler = null;
    protected $collector = null;
    /** @var array the recorded last runs of the cronjob parts and commands before the test: a test must not leave its runs on the start page */
    protected $savedRuns = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$script !== null || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 4 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
            $script->startup();
            $script->setUseSiteAccess( 'admin' );
            $script->initialize();
            eZExecution::setCleanExit();
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            if ( !class_exists( 'CjwNewsletterUser' ) )
                throw new RuntimeException( 'cjw_newsletter is not active' );
            if ( !eZContentObject::fetch( self::LIST_OBJECT_ID ) )
                throw new RuntimeException( 'the newsletter list object ' . self::LIST_OBJECT_ID . ' does not exist' );
            self::$script = $script;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        $this->loginAdmin();
        $this->savedRuns = eZDB::instance()->arrayQuery( "SELECT name, value FROM ezsite_data WHERE name LIKE 'cjw_newsletter_last_%'" );
        $this->savedPost = $_POST;
        $_POST = array();
        $this->useFileTransport();
        $this->phpIssues = array();
        $this->collector = function ( $no, $str, $file, $line ) {
            $own = strpos( (string)$file, 'extension/cjw_newsletter/' ) !== false;
            // a compiled template of this extension's views (the file name of a compiled template starts with the template name)
            $template = strpos( (string)$file, 'cache/template/compiled/' ) !== false
                && preg_match( '/(subscription|subscribe|unsubscribe|configure|user_|blacklist|mailbox|import|send|preview|archive|newsletter|cjw_)[a-z_]*-[0-9a-f]{32}\.php$/', basename( (string)$file ) );
            if ( ( error_reporting() & $no ) && ( $own || $template ) )
                $this->phpIssues[] = $str . ' at ' . substr( $file, $own ? strpos( $file, 'extension/cjw_newsletter/' ) : strpos( $file, 'cache/template' ) ) . ':' . $line;
            return true;
        };
        $this->previousHandler = set_error_handler( $this->collector );
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $this->loginAdmin();
            $this->removeTestData();
            $this->restoreRuns();
            $this->restoreIni();
            $this->removeMailDir();
            $_POST = $this->savedPost;
            for ( $i = 0; $i < 20; $i++ )
            {
                $current = set_error_handler( function () { return false; } );
                restore_error_handler();
                if ( $current === $this->previousHandler )
                    break;
                restore_error_handler();
            }
            $issues = $this->phpIssues;
            $this->phpIssues = array();
            $this->assertSame( array(), array_values( array_unique( $issues ) ), 'PHP warnings or deprecations inside cjw_newsletter' );
        }
        parent::tearDown();
    }

    /** Puts the last runs back as they were before the test. */
    protected function restoreRuns()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ezsite_data WHERE name LIKE 'cjw_newsletter_last_%'" );
        foreach ( $this->savedRuns as $row )
        {
            $data = eZSiteData::create( $row['name'], $row['value'] );
            $data->store();
        }
        $this->savedRuns = array();
    }

    // ------------------------------------------------------------------ environment

    protected function loginAdmin()
    {
        $admin = eZUser::fetchByName( 'admin' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
    }

    protected function loginAnonymous()
    {
        $id = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $id ), $id );
    }

    /** Overrides an INI value in memory for this test only, remembered for tearDown. */
    protected function setIni( $file, $block, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $key = $file . '|' . $block . '|' . $name;
        if ( !array_key_exists( $key, $this->savedIni ) )
            $this->savedIni[$key] = $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : null;
        $ini->setVariable( $block, $name, $value );
    }

    protected function restoreIni()
    {
        foreach ( $this->savedIni as $key => $value )
        {
            list( $file, $block, $name ) = explode( '|', $key );
            $ini = eZINI::instance( $file );
            if ( $value === null )
                $ini->removeSetting( $block, $name );
            else
                $ini->setVariable( $block, $name, $value );
        }
        $this->savedIni = array();
    }

    /** Switches every transport to the file transport, in a directory of this test. */
    protected function useFileTransport()
    {
        $this->mailDir = 'var/tmp/nltest-outbox-' . getmypid() . '-' . ( ++self::$counter );
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', 'FileTransportMailDir', $this->mailDir );
        foreach ( array( 'TransportMethodCronjob', 'TransportMethodPreview', 'TransportMethodDirectly' ) as $name )
            $this->setIni( 'cjw_newsletter.ini', 'NewsletterMailSettings', $name, 'file' );
    }

    /** @return string[] the full paths of the mail files written so far, oldest first */
    protected function outbox()
    {
        $dir = CjwNewsletterTransportFile::resolveMailDir( $this->mailDir );
        $files = is_dir( $dir ) ? glob( $dir . '/*' ) : array();
        sort( $files );
        return $files ? $files : array();
    }

    /** @return string the content of a mail file with the header folding undone */
    protected function mailText( $file )
    {
        $text = file_get_contents( $file );
        return preg_replace( "/\r?\n[ \t]+/", ' ', $text );
    }

    protected function removeMailDir()
    {
        $dir = CjwNewsletterTransportFile::resolveMailDir( $this->mailDir );
        if ( is_dir( $dir ) )
        {
            foreach ( glob( $dir . '/*' ) as $file )
                if ( is_file( $file ) )
                    unlink( $file );
            @rmdir( $dir );
        }
    }

    // ------------------------------------------------------------------ test data

    /** @return string a fresh throwaway address */
    protected function newEmail( $label = '' )
    {
        $email = 'nltest-' . getmypid() . '-' . ( ++self::$counter ) . ( $label !== '' ? '-' . $label : '' ) . '@' . self::MAIL_DOMAIN;
        $this->extraEmails[] = $email;
        return $email;
    }

    /** Creates a confirmed newsletter user with an approved subscription to the list. @return CjwNewsletterUser */
    protected function newSubscriber( $label = '', $status = null, $subscriptionStatus = null, array $formats = array( 0 ) )
    {
        $email = $this->newEmail( $label );
        $user = CjwNewsletterUser::create( $email, 1, 'Test', 'Subscriber', 0, CjwNewsletterUser::STATUS_PENDING, 'default', '', '', '', '' );
        // through setAttribute, so that the status carries its timestamp as it does for a real subscriber
        $user->setAttribute( 'status', $status === null ? CjwNewsletterUser::STATUS_CONFIRMED : $status );
        $user->store();
        $sub = CjwNewsletterSubscription::create( self::LIST_OBJECT_ID, $user->attribute( 'id' ), $formats,
            $subscriptionStatus === null ? CjwNewsletterSubscription::STATUS_APPROVED : $subscriptionStatus );
        $sub->store();
        return $user;
    }

    /** @return CjwNewsletterSubscription */
    protected function subscriptionOf( CjwNewsletterUser $user )
    {
        return CjwNewsletterSubscription::fetchByListIdAndNewsletterUserId( self::LIST_OBJECT_ID, $user->attribute( 'id' ) );
    }

    /**
     * Creates and publishes an edition under the list with one article child. The datatype row is written by hand
     * (the admin form does that in the browser). @return eZContentObject
     */
    protected function newEdition( $title = null, $withArticle = true )
    {
        $title = $title === null ? 'NLTEST edition ' . getmypid() . '-' . ( ++self::$counter ) : $title;
        $class = eZContentClass::fetchByIdentifier( 'cjw_newsletter_edition' );
        $object = $class->instantiate( (int)eZUser::currentUserID(), 0, 0 );
        $object->store();
        $node = eZNodeAssignment::create( array( 'contentobject_id' => $object->attribute( 'id' ),
            'contentobject_version' => 1, 'parent_node' => self::LIST_NODE_ID, 'is_main' => 1, 'sort_field' => 2, 'sort_order' => 0 ) );
        $node->store();
        $version = $object->currentVersion();
        $map = $version->dataMap();
        $map['title']->setAttribute( 'data_text', $title );
        $map['title']->store();
        $map['short_title']->setAttribute( 'data_text', $title );
        $map['short_title']->store();
        $attr = $map['newsletter_edition'];
        $row = new CjwNewsletterEdition( array(
            'contentobject_attribute_id' => $attr->attribute( 'id' ),
            'contentobject_attribute_version' => $attr->attribute( 'version' ),
            'contentobject_id' => $object->attribute( 'id' ),
            'contentclass_id' => $class->attribute( 'id' ) ) );
        $row->store();
        $this->createdObjectIds[] = (int)$object->attribute( 'id' );
        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ), 'version' => 1 ) );
        $object = eZContentObject::fetch( $object->attribute( 'id' ) );
        if ( $withArticle )
            $this->newArticle( $object, 'NLTEST article body line' );
        return $object;
    }

    /** Creates and publishes an article as the child of an edition. */
    protected function newArticle( eZContentObject $edition, $text )
    {
        $class = eZContentClass::fetchByIdentifier( 'cjw_newsletter_article' );
        $object = $class->instantiate( (int)eZUser::currentUserID(), 0, 0 );
        $object->store();
        $node = eZNodeAssignment::create( array( 'contentobject_id' => $object->attribute( 'id' ),
            'contentobject_version' => 1, 'parent_node' => $edition->attribute( 'main_node_id' ), 'is_main' => 1, 'sort_field' => 2, 'sort_order' => 0 ) );
        $node->store();
        $map = $object->currentVersion()->dataMap();
        $map['title']->setAttribute( 'data_text', 'NLTEST article title' );
        $map['title']->store();
        $map['short_description']->setAttribute( 'data_text',
            '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>' . $text . '</paragraph></section>' );
        $map['short_description']->store();
        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ), 'version' => 1 ) );
        $this->createdObjectIds[] = (int)$object->attribute( 'id' );
        return eZContentObject::fetch( $object->attribute( 'id' ) );
    }

    /** @return CjwNewsletterEdition the datatype content of the edition's published version */
    protected function editionContent( eZContentObject $object )
    {
        $object = eZContentObject::fetch( $object->attribute( 'id' ) );
        $map = $object->dataMap();
        return $map['newsletter_edition']->attribute( 'content' );
    }

    protected function removeTestData()
    {
        $db = eZDB::instance();
        // editions and their sends
        foreach ( array_reverse( $this->createdObjectIds ) as $id )
        {
            foreach ( $db->arrayQuery( 'SELECT id FROM cjwnl_edition_send WHERE edition_contentobject_id = ' . (int)$id ) as $row )
            {
                $db->query( 'DELETE FROM cjwnl_edition_send_item WHERE edition_send_id = ' . (int)$row['id'] );
                $db->query( 'DELETE FROM cjwnl_edition_send WHERE id = ' . (int)$row['id'] );
            }
            eZContentObjectOperations::remove( $id );
            $db->query( 'DELETE FROM cjwnl_edition WHERE contentobject_id = ' . (int)$id );
            if ( (int)$id !== self::LIST_OBJECT_ID )
                $db->query( 'DELETE FROM cjwnl_list WHERE contentobject_id = ' . (int)$id );
        }
        $this->createdObjectIds = array();
        // users by address pattern (also those a view created)
        $rows = $db->arrayQuery( "SELECT id, email FROM cjwnl_user WHERE email LIKE 'nltest-%@" . self::MAIL_DOMAIN . "'" );
        foreach ( $rows as $row )
        {
            $uid = (int)$row['id'];
            $db->query( 'DELETE FROM cjwnl_edition_send_item WHERE newsletter_user_id = ' . $uid );
            $db->query( 'DELETE FROM cjwnl_subscription WHERE newsletter_user_id = ' . $uid );
            $db->query( 'DELETE FROM cjwnl_user WHERE id = ' . $uid );
        }
        // 4.2.0 N1: a hard bounce or a complaint in a test suppresses the address in the kernel list: lift it again
        if ( class_exists( 'expMailSuppression' ) && class_exists( 'CjwNewsletterMailPreferences' ) && CjwNewsletterMailPreferences::available() )
        {
            $emails = $this->extraEmails;
            foreach ( $rows as $row )
                $emails[] = (string)$row['email'];
            foreach ( array_unique( $emails ) as $email )
                if ( strpos( $email, 'nltest-' ) === 0 && expMailSuppression::isSuppressed( $email ) )
                    expMailSuppression::lift( $email );
        }
        // end 4.2.0 N1
        $db->query( "DELETE FROM cjwnl_blacklist_item WHERE email LIKE 'nltest-%@" . self::MAIL_DOMAIN . "'" );
        foreach ( $this->createdImportIds as $importId )
        {
            $db->query( 'DELETE FROM cjwnl_subscription WHERE import_id = ' . (int)$importId );
            $db->query( 'DELETE FROM cjwnl_user WHERE import_id = ' . (int)$importId );
            $db->query( 'DELETE FROM cjwnl_import WHERE id = ' . (int)$importId );
        }
        $this->createdImportIds = array();
        $db->query( "DELETE FROM cjwnl_mailbox_item WHERE message_identifier LIKE 'nltest-%'" );
        $db->query( "DELETE FROM cjwnl_mailbox WHERE email LIKE 'nltest-%'" );
        eZContentObject::clearCache();
    }

    // ------------------------------------------------------------------ views

    /**
     * Runs a view of the newsletter module in-process, as the dispatcher does.
     *
     * @param string $view the view name
     * @param array $params the positional view parameters
     * @param array $post POST variables (selects the single_post_action)
     * @return array content (string|null), result (array|null), exit (int), redirect (string|null), error (mixed)
     */
    protected function runView( $view, array $params = array(), array $post = array(), array $userParams = array() )
    {
        $_POST = $post;
        $_GET = $userParams;
        $module = eZModule::exists( 'newsletter' );
        $this->assertInstanceOf( 'eZModule', $module, 'newsletter module exists' );
        $module->setExitStatus( eZModule::STATUS_OK );
        $module->setRedirectURI( false );
        $module->setCurrentAction( null );
        $module->setViewResult( null );
        $result = $module->run( $view, $params, false, $userParams );
        $this->reinstallCollector();
        $_POST = array();
        $_GET = array();
        return array(
            'result' => is_array( $result ) ? $result : null,
            'content' => ( is_array( $result ) && isset( $result['content'] ) ) ? $result['content'] : null,
            'exit' => $module->exitStatus(),
            'redirect' => $module->redirectURI(),
            'module' => $module );
    }

    /**
     * Runs a cronjob part of the extension in-process, as eZRunCronjobs::runScript() does.
     * @return string what the part printed
     */
    protected function runCronjob( $name )
    {
        $file = 'extension/cjw_newsletter/cronjobs/' . $name . '.php';
        $cli = eZCLI::instance();
        $cli->setIsQuiet( false );
        $runner = function ( $file, $cli ) { return include $file; };
        ob_start();
        try
        {
            $runner( $file, $cli );
        }
        finally
        {
            $output = ob_get_clean();
            $this->reinstallCollector();
        }
        return $output;
    }

    /** @return string[] every match of the first group in the text */
    protected function linkHashes( $text, $path )
    {
        preg_match_all( '#' . preg_quote( $path, '#' ) . '/([0-9a-zA-Z]+)#', $text, $m );
        return $m[1];
    }

    /** The kernel installs error handlers of its own while it runs; ours goes back on top afterwards. */
    protected function reinstallCollector()
    {
        $current = set_error_handler( function () { return false; } );
        restore_error_handler();
        if ( $current !== $this->collector )
            set_error_handler( $this->collector );
    }

    protected function assertViewOk( array $r, $message = '' )
    {
        $this->assertSame( eZModule::STATUS_OK, $r['exit'], $message . ' exit status' );
        $this->assertTrue( $r['content'] !== null && $r['content'] !== '' || $r['redirect'], $message . ' produced content or a redirect' );
    }

    /** A view called with bad input must end in a clean module error or a redirect, never in content that is a fatal. */
    protected function assertViewClean( array $r, $message = '' )
    {
        $ok = $r['exit'] !== eZModule::STATUS_OK || $r['redirect'] || ( $r['content'] !== null );
        $this->assertTrue( $ok, $message . ' ended without error, redirect or content' );
    }
}
