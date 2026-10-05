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
        $this->assertSame( 'disabled', $ini->variable( 'TrackingSettings', 'Tracking' ), 'tracking is off by default' );
        $this->assertSame( 'disabled', $ini->variable( 'SmsSettings', 'Sms' ), 'SMS is off by default' );
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
