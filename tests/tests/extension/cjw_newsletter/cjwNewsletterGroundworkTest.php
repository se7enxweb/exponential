<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * The 4.2.0 groundwork the six feature areas build on: every area's module views are declared in its own block of
 * module.php with a policy function, each view answers "not found" until the area implements it (a view that an
 * area has implemented is skipped here), every area has its INI groups and a context in every translation file.
 */
class cjwNewsletterGroundworkTest extends cjwNewsletterTestCase
{
    private static $views = array(
        'N1' => array( 'test_group_list', 'test_group_edit', 'mailin_address_list', 'mailin_address_edit', 'throttle', 'suppression_import' ),
        'N2' => array( 'schedule_list', 'schedule_edit', 'article_pool_list', 'article_pool_edit', 'article_pool', 'approval' ),
        'N3' => array( 'preview_as', 'interest_list', 'interest_edit', 'skin_preview' ),
        'N4' => array( 'r', 'o', 'report', 'statistics_export', 'ab_test' ),
        'N5' => array( 'sms_send', 'sms_confirm', 'sms_inbound' ),
        'N6' => array( 'import_mapping', 'subscriber_export', 'migration_log' ),
    );

    private static $ini = array(
        'N1' => array( 'DeliverabilitySettings', 'ThrottleSettings', 'TestSendSettings', 'MailInSettings' ),
        'N2' => array( 'ScheduleSettings', 'ArticlePoolSettings', 'ApprovalSettings' ),
        'N3' => array( 'PlaceholderSettings', 'ConditionTagSettings', 'InterestSettings', 'LanguageSettings', 'TextViewSettings' ),
        'N4' => array( 'TrackingSettings', 'ABTestSettings', 'StatisticsSettings' ),
        'N5' => array( 'SmsSettings', 'SmsTransport_file' ),
        'N6' => array( 'ImportMappingSettings', 'EznewsletterImportSettings' ),
    );

    public function testEveryAreaHasItsBlockOfViewsAndFunctionsInModulePhp()
    {
        $source = file_get_contents( 'extension/cjw_newsletter/modules/newsletter/module.php' );
        $module = eZModule::exists( 'newsletter' );
        $views = $module->attribute( 'views' );
        $functions = $module->attribute( 'available_functions' );
        foreach ( self::$views as $area => $list )
        {
            $this->assertMatchesRegularExpression( "#// ---- 4\\.2\\.0 $area .*?// ---- end $area views#s", $source, "the views block of $area" );
            $this->assertMatchesRegularExpression( "#// ---- 4\\.2\\.0 $area .*?: functions.*?// ---- end $area functions#s", $source, "the functions block of $area" );
            foreach ( $list as $view )
            {
                $this->assertArrayHasKey( $view, $views, "view $view of $area" );
                $this->assertFileExists( 'extension/cjw_newsletter/modules/newsletter/' . $views[$view]['script'] );
                foreach ( $views[$view]['functions'] as $function )
                    $this->assertArrayHasKey( $function, $functions, "the policy function $function of $view" );
            }
        }
    }

    public function testAViewThatIsStillAStubAnswersNotFound()
    {
        $checked = 0;
        foreach ( self::$views as $area => $list )
            foreach ( $list as $view )
            {
                $script = file_get_contents( "extension/cjw_newsletter/modules/newsletter/$view.php" );
                if ( strpos( $script, 'not implemented yet' ) === false )
                    continue;
                $r = $this->runView( $view );
                $this->assertSame( eZModule::STATUS_FAILED, $r['exit'], "$view ($area) is a stub and fails" );
                $this->assertEquals( eZError::KERNEL_NOT_FOUND, $r['module']->errorCode(), "$view answers not found" );
                $checked++;
            }
        $this->assertGreaterThanOrEqual( 0, $checked );
    }

    public function testEveryAreaHasItsIniGroups()
    {
        $ini = eZINI::instance( 'cjw_newsletter.ini' );
        foreach ( self::$ini as $area => $groups )
            foreach ( $groups as $group )
                $this->assertTrue( $ini->hasGroup( $group ), "[$group] of $area" );
        // the shipped file only: an installation may switch tracking on in its override
        $shipped = new eZINI( 'cjw_newsletter.ini', 'extension/cjw_newsletter/settings', null, false, false, true );
        $this->assertSame( 'disabled', $shipped->variable( 'TrackingSettings', 'Tracking' ), 'tracking is off by default' );
        $this->assertSame( 'disabled', $shipped->variable( 'SmsSettings', 'Sms' ), 'SMS is off by default' );
    }

    private function useHandler()
    {
        cjwNewsletterGroundworkTestHandler::$calls = array();
        cjwNewsletterGroundworkTestHandler::$mode = '';
        $this->setIni( 'cjw_newsletter.ini', 'ExtensionPointSettings', 'Handlers', array( 'NoSuchClassPhpunit', 'cjwNewsletterGroundworkTestHandler' ) );
    }

    public function testExtensionPointsCallOnlyExistingHandlersAndTheirMethods()
    {
        $this->setIni( 'cjw_newsletter.ini', 'ExtensionPointSettings', 'Handlers', array() );
        $this->assertSame( array(), CjwNewsletterExtensionPoints::handlers(), 'no handler in the shipped settings' );
        $this->useHandler();
        $this->assertSame( array( 'cjwNewsletterGroundworkTestHandler' ), CjwNewsletterExtensionPoints::handlers() );
        $this->assertSame( array(), CjwNewsletterExtensionPoints::call( 'noSuchPoint' ) );
        cjwNewsletterGroundworkTestHandler::$mode = 'hold';
        $this->assertFalse( CjwNewsletterExtensionPoints::allows( 'sendProcessAllowed', array( null ) ) );
        $this->assertSame( array( 'bad input' ), CjwNewsletterExtensionPoints::errors( 'sendFormValidate', array( null, null ) ) );
        $this->assertSame( 'a@example.invalid;group@example.invalid', CjwNewsletterExtensionPoints::filter( 'testSendRecipients', 'a@example.invalid', array( null, null ) ) );
        $this->setIni( 'cjw_newsletter.ini', 'ExtensionPointSettings', 'DashboardBlocks', array( 'design:newsletter/dashboard/x.tpl', 'javascript:x', '../../etc.tpl' ) );
        $this->assertSame( array( 'design:newsletter/dashboard/x.tpl' ), CjwNewsletterExtensionPoints::templates( 'DashboardBlocks' ), 'only design: template names' );
    }

    public function testTheDashboardCarriesWhatTheHandlersReturn()
    {
        $this->useHandler();
        $summary = CjwNewsletterDashboard::summary();
        $this->assertSame( 42, $summary['areas']['cjwNewsletterGroundworkTestHandler']['answer'] );
        $codes = array_map( function ( $p ) { return $p['code']; }, $summary['problems'] );
        $this->assertContains( 'phpunit_problem', $codes );
    }

    public function testTheRunnerCallsThePointsAndAHandlerChangesTheMailOfAnItem()
    {
        $this->useHandler();
        $user = $this->newSubscriber( 'ephook' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertContains( 'queueCreateBefore', cjwNewsletterGroundworkTestHandler::$calls );
        $this->assertContains( 'sendQueueCreated', cjwNewsletterGroundworkTestHandler::$calls );
        cjwNewsletterGroundworkTestHandler::$mode = 'subject';
        CjwNewsletterRunner::queueProcess( new CjwNewsletterJobOutput( false ), 'nltest' );
        foreach ( array( 'queueProcessBefore', 'sendProcessAllowed', 'itemBeforeSend', 'itemSent' ) as $point )
            $this->assertContains( $point, cjwNewsletterGroundworkTestHandler::$calls );
        $found = false;
        foreach ( $this->outbox() as $file )
            if ( strpos( $this->mailText( $file ), $user->attribute( 'email' ) ) !== false )
            {
                $found = true;
                $this->assertStringContainsString( 'PHPUNIT-SUBJECT', $this->mailText( $file ) );
            }
        $this->assertTrue( $found );
    }

    public function testADeferredItemStaysInTheQueueAndAnAbortedOneIsClosed()
    {
        $this->useHandler();
        $user = $this->newSubscriber( 'epdefer' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
        cjwNewsletterGroundworkTestHandler::$mode = 'defer';
        $totals = CjwNewsletterRunner::queueProcess( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertGreaterThanOrEqual( 1, $totals['deferred'] );
        $this->assertGreaterThanOrEqual( 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
        cjwNewsletterGroundworkTestHandler::$mode = 'abort';
        CjwNewsletterRunner::queueProcess( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertSame( 0, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
        $this->assertCount( 0, array_filter( $this->outbox(), function ( $f ) use ( $user ) { return strpos( file_get_contents( $f ), $user->attribute( 'email' ) ) !== false; } ), 'no mail to the aborted item' );
    }

    public function testTheListAttributeKeepsThe42ColumnsOnEdit()
    {
        $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, eZContentObject::fetch( self::LIST_OBJECT_ID )->attribute( 'current_version' ) );
        if ( !is_object( $list ) )
            $this->markTestSkipped( 'no list attribute row for the test list' );
        $attribute = eZContentObjectAttribute::fetch( $list->attribute( 'contentobject_attribute_id' ), $list->attribute( 'contentobject_attribute_version' ) );
        $saved = $list->attribute( 'main_language' );
        $list->setAttribute( 'main_language', 'ger-DE' );
        $list->store();
        try
        {
            $this->useHandler();
            $fresh = new CjwNewsletterList( array( 'contentobject_attribute_id' => $list->attribute( 'contentobject_attribute_id' ),
                'contentobject_attribute_version' => $list->attribute( 'contentobject_attribute_version' ) ) );
            $errors = CjwNewsletterExtensionPoints::listAttributeInput( $fresh, eZHTTPTool::instance(), 'ContentObjectAttribute_CjwNewsletterList_', '_' . $attribute->attribute( 'id' ), $attribute );
            $this->assertSame( 'ger-DE', $fresh->attribute( 'main_language' ), 'taken over from the stored version' );
            $this->assertSame( 2, (int)$fresh->attribute( 'tracking_mode' ), 'set by the handler' );
            $this->assertSame( array(), $errors );
        }
        finally
        {
            $list->setAttribute( 'main_language', $saved );
            $list->store();
        }
    }

    public function testEveryTranslationFileHasAContextPerArea()
    {
        foreach ( glob( 'extension/cjw_newsletter/translations/*/translation.ts' ) as $file )
        {
            $doc = new DOMDocument();
            $this->assertTrue( $doc->load( $file ), "$file is XML" );
            $names = array();
            foreach ( $doc->getElementsByTagName( 'name' ) as $name )
                $names[] = $name->textContent;
            foreach ( array( 'deliverability', 'editorial', 'rendering', 'statistics', 'sms', 'importexport' ) as $context )
                $this->assertContains( "cjw_newsletter/$context", $names, "$file: context $context" );
        }
    }
}

/**
 * An extension point handler for the tests: records the points it was called at; $mode chooses what it does.
 */
class cjwNewsletterGroundworkTestHandler
{
    public static $calls = array();
    public static $mode = '';

    static function queueCreateBefore( $cli ) { self::$calls[] = 'queueCreateBefore'; }
    static function sendQueueCreated( $send, $cli ) { self::$calls[] = 'sendQueueCreated'; }
    static function queueProcessBefore( $cli ) { self::$calls[] = 'queueProcessBefore'; }
    static function sendProcessAllowed( $send )
    {
        self::$calls[] = 'sendProcessAllowed';
        return self::$mode !== 'hold';
    }
    static function itemBeforeSend( $message, $item, $send, $user )
    {
        self::$calls[] = 'itemBeforeSend';
        if ( self::$mode === 'subject' )
            $message['subject'] = 'PHPUNIT-SUBJECT ' . $message['subject'];
        else if ( self::$mode === 'defer' )
            $message['defer'] = true;
        else if ( self::$mode === 'abort' )
            $message['abort'] = 'closed by the test';
    }
    static function itemSent( $item, $send, $result ) { self::$calls[] = 'itemSent'; }
    static function sendFormValidate( $http, $version ) { return array( 'bad input', '' ); }
    static function testSendRecipients( $emails, $http, $version ) { return $emails . ';group@example.invalid'; }
    static function dashboardSummary( $summary )
    {
        return array( 'answer' => 42, 'problems' => array( array( 'level' => 'info', 'code' => 'phpunit_problem', 'text' => 'test', 'url' => '' ) ) );
    }
    static function listAttributeInput( $list, $http, $prefix, $postfix, $attribute )
    {
        $list->setAttribute( 'tracking_mode', 2 );
        return array();
    }
}
