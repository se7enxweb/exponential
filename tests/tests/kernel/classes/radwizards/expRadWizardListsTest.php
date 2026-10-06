<?php
/**
 * The lists and names the extension wizards of Setup > RAD read out of their forms, beyond the module and content
 * wizards: the settings wizard (image aliases and their filters, view cache rules, collected information forms,
 * trigger operations, siteaccess settings, extension roots, icon sizes, taken aliases and themes), the template
 * wizard (names, parameters, usage), the workflow event wizard (statuses, settings and their columns, triggers),
 * the handler wizard (kinds, aliases, class paths, the calls passed on to a parent), the kernel override wizard (the
 * kernel's classes, search, readiness), the datatype wizard (columns, methods) and the design wizard.
 *
 * No database: an empty one stands in.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expRadWizardListsTest extends PHPUnit\Framework\TestCase
{
    private $hadPathList;
    private $pathList;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        expRadWizardTestHelper::useEmptyDatabase();
        $this->hadPathList = array_key_exists( 'eZModuleGlobalPathList', $GLOBALS );
        $this->pathList = $this->hadPathList ? $GLOBALS['eZModuleGlobalPathList'] : null;
        eZModule::setGlobalPathList( array( 'kernel' ) );
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreDatabase();
        if ( $this->hadPathList )
            $GLOBALS['eZModuleGlobalPathList'] = $this->pathList;
        else
            unset( $GLOBALS['eZModuleGlobalPathList'] );
    }

    // ---------------------------------------------------------------- settings wizard

    public function testImageAliasesKeepOnlyFiltersThatAreNamesAndNumbers()
    {
        $aliases = expSettingsExtensionWizard::aliasList( "thumb: geometry/scale=100;100, colorspace/gray\nwide: geometry/scale=800;600 -quality 1; rm -rf, sharpen=0.5\n\n9bad: x\nplain" );
        $this->assertSame( array( 'thumb', 'wide', 'plain' ), array_column( $aliases, 'name' ) );
        $this->assertSame( array( array( 'name' => 'geometry/scale', 'args' => '100;100', 'known' => isset( expSettingsExtensionWizard::filters()['geometry/scale'] ) ),
                                  array( 'name' => 'colorspace/gray', 'args' => '', 'known' => isset( expSettingsExtensionWizard::filters()['colorspace/gray'] ) ) ),
                           $aliases[0]['filters'] );
        $this->assertSame( array( 'sharpen' ), array_column( $aliases[1]['filters'], 'name' ), 'a filter with anything but numbers after = is dropped' );
        $this->assertSame( array(), $aliases[2]['filters'] );
        $this->assertSame( array(), expSettingsExtensionWizard::aliasList( array() ) );
    }

    public function testViewCacheRules()
    {
        $rules = expSettingsExtensionWizard::ruleList( "article: siblings, parent, siblings, folder\nfolder\n: x\nblog_post: ALL" );
        $this->assertSame( array(
            array( 'class' => 'article', 'methods' => array( 'siblings', 'parent' ), 'depends' => array( 'folder' ) ),
            array( 'class' => 'folder', 'methods' => array(), 'depends' => array() ),
            array( 'class' => 'blog_post', 'methods' => array( 'all' ), 'depends' => array() ),
        ), $rules );
        $this->assertArrayHasKey( 'relating', expSettingsExtensionWizard::clearMethods() );
    }

    public function testCollectedInformationForms()
    {
        $this->assertSame( array(
            array( 'class' => 'feedback_form', 'type' => 'feedback', 'mail' => true ),
            array( 'class' => 'poll', 'type' => 'poll', 'mail' => false ),
            array( 'class' => 'contact', 'type' => 'form', 'mail' => true ),
        ), expSettingsExtensionWizard::formList( "feedback_form: Feedback\npoll poll nomail\ncontact: nonsense" ) );
        $this->assertSame( array( 'form', 'poll', 'feedback' ), array_keys( expSettingsExtensionWizard::collectTypes() ) );
    }

    public function testOperations()
    {
        $this->assertSame( array( 'content_publish', 'user_login' ), expSettingsExtensionWizard::operationList( "content_publish; user_login, content_publish\n9x" ) );
        $this->assertSame( array( 'a_b' ), expSettingsExtensionWizard::operationList( array( 'a-b', array( 'x' ) ) ) );
        $this->assertSame( array(), expSettingsExtensionWizard::operationList( null ) );
    }

    public function testSiteaccessSettings()
    {
        $overrides = expSettingsExtensionWizard::overrideList( "site.ini [SiteSettings] DefaultPage=content/view/full/2\nSITE.INI[DesignSettings]AdditionalSiteDesignList[]=k1e\nno ini here\nsite.ini [Bad Section] X=1\ncontent.ini [VersionView] AvailableSiteDesignList[k1e]= a */ b " );
        $this->assertSame( array(
            array( 'ini' => 'site.ini', 'section' => 'SiteSettings', 'variable' => 'DefaultPage', 'value' => 'content/view/full/2' ),
            array( 'ini' => 'site.ini', 'section' => 'DesignSettings', 'variable' => 'AdditionalSiteDesignList[]', 'value' => 'k1e' ),
            array( 'ini' => 'content.ini', 'section' => 'VersionView', 'variable' => 'AvailableSiteDesignList[k1e]', 'value' => 'a * b' ),
        ), $overrides );
    }

    public function testASiteaccessSettingNeverEndsTheIniComment()
    {
        $overrides = expSettingsExtensionWizard::overrideList( "site.ini [SiteSettings] List[*/ k1e_injected(); /*]=x\nsite.ini [SiteSettings] Other[?><?php k1e_injected(); ?>]=y" );
        foreach ( $overrides as $override )
        {
            $this->assertStringNotContainsString( '*/', $override['variable'] );
            $this->assertStringNotContainsString( '<?', $override['variable'] );
            $this->assertStringNotContainsString( '?>', $override['variable'] );
        }
        $settings = expSettingsExtensionWizard::settings( array( 'name' => 'k1e_settings_ext', 'licence' => 'MIT', 'siteaccess' => 'k1e_site',
                                                                 'overrides' => "site.ini [SiteSettings] List[*/ k1e_injected(); /*]=x",
                                                                 'parts' => array( 'siteaccess' ) ) );
        $files = expSettingsExtensionWizard::files( $settings );
        $this->assertNotEmpty( $files );
        expRadWizardTestHelper::assertFilesAreSafe( $this, $files );
    }

    public function testRootsAreRelativePathsInside()
    {
        $this->assertSame( array( 'extension_extra', 'a/b-c.d' ),
                           expSettingsExtensionWizard::rootList( "extension_extra/\n/etc\n\\\\server\nC:\\x\n../up\na/../b\na/b-c.d, extension_extra\n.hidden" ) );
        $this->assertCount( 10, expSettingsExtensionWizard::rootList( implode( ',', array_map( function ( $i ) { return "r$i"; }, range( 1, 15 ) ) ) ) );
    }

    public function testIconSizes()
    {
        $this->assertSame( array( array( 'name' => 'normal', 'directory' => '32x32' ), array( 'name' => 'tiny', 'directory' => '8x8' ) ),
                           expSettingsExtensionWizard::sizeList( "normal: 32x32\ntiny=8x8, huge: ../x\nbad" ) );
        $defaults = expSettingsExtensionWizard::settings( array( 'name' => 'k1e_s' ) )['sizes'];
        $this->assertSame( array( 'normal', 'small' ), array_column( $defaults, 'name' ) );
    }

    public function testListenersOfTheAccessFiltersNameTheirArguments()
    {
        $class = 'K1eAccessListener' . getmypid();
        $events = array( 'content/edit/access', 'content/download/access', 'collaboration/item/access',
                         'content/notification/create', 'content/view/cachekeys' );
        $methods = array();
        foreach ( $events as $event )
        {
            $signature = expSettingsExtensionWizard::eventSignature( $event );
            $this->assertNotNull( $signature, $event );
            $method = expSettingsExtensionWizard::listenerMethod( $class, $event, 'filter', 'What it is for.' );
            $this->assertStringContainsString( '( ' . $signature['params'] . ' )', $method, $event );
            $this->assertStringContainsString( "eZINI::instance()->hasVariable( '" . $class . "'", $method, $event . ': the example reads the settings of the extension' );
            $this->assertStringContainsString( 'return ' . $signature['returns'] . ';', $method, $event );
            $this->assertSame( $signature['only_true'], strpos( $method, 'Only true allows' ) !== false, $event );
            $methods[] = $method;
        }
        $this->assertNull( expSettingsExtensionWizard::eventSignature( 'content/cache' ) );
        $this->assertStringContainsString( '( $value = null )', expSettingsExtensionWizard::listenerMethod( $class, 'content/cache', 'notify', 'x' ) );

        // The generated class parses, and with empty settings every method hands the value back unchanged
        $code = "class " . $class . "\n{\n" . implode( "\n\n", $methods ) . "\n}\n";
        $this->assertNotEmpty( token_get_all( '<?php ' . $code, TOKEN_PARSE ) );
        eval( $code );
        $this->assertFalse( $class::contentEditAccess( false, null, null, 10, false ) );
        $this->assertTrue( $class::contentDownloadAccess( true, null, null, 3 ) );
        $this->assertFalse( $class::collaborationItemAccess( false, null, null ) );
        $this->assertTrue( $class::contentNotificationCreate( true, 0, 0 ) );
        $this->assertSame( array( 'node_id' => 2 ), $class::contentViewCachekeys( array( 'node_id' => 2 ), array() ) );
    }

    public function testEventsOnlyKnownOnes()
    {
        $this->assertSame( array(), expSettingsExtensionWizard::chosenEvents( 'content/cache' ) );
        $this->assertSame( array(), expSettingsExtensionWizard::chosenEvents( null ) );
    }

    public function testTakenAliasesAndThemes()
    {
        $existing = (array)eZINI::instance( 'image.ini' )->variable( 'AliasSettings', 'AliasList' );
        $settings = expSettingsExtensionWizard::settings( array( 'name' => 'k1e_s', 'aliases' => ( $existing ? $existing[0] : 'x' ) . "\nk1e_fresh_alias", 'parts' => array( 'image', 'icons' ) ) );
        $this->assertSame( $existing ? array( $existing[0] ) : array(), expSettingsExtensionWizard::takenAliases( $settings ) );
        $this->assertSame( array(), expSettingsExtensionWizard::takenAliases( expSettingsExtensionWizard::settings( array( 'name' => 'k1e_s', 'parts' => array( 'icons' ) ) ) ) );
        $theme = (string)eZINI::instance( 'icon.ini' )->variable( 'IconSettings', 'Theme' );
        if ( $theme !== '' && expSettingsExtensionWizard::safeIdentifier( $theme ) === $theme )
            $this->assertSame( array( $theme ), expSettingsExtensionWizard::takenThemes( expSettingsExtensionWizard::settings( array( 'name' => 'k1e_s', 'theme' => $theme, 'parts' => array( 'icons' ) ) ) ) );
        $this->assertSame( array(), expSettingsExtensionWizard::takenThemes( expSettingsExtensionWizard::settings( array( 'name' => 'k1e_s', 'parts' => array( 'image' ) ) ) ) );
    }

    // ---------------------------------------------------------------- template wizard

    public function testTemplateNames()
    {
        $this->assertSame( array( 'shout', 'whisper', 'my_op' ), expTemplateExtensionWizard::nameList( "shout, whisper;shout\nMy_Op 9x" ) );
        $this->assertSame( array( 'a', 'b' ), expTemplateExtensionWizard::nameList( array( 'a', 'b', array( 'c' ) ) ) );
        $this->assertCount( 40, expTemplateExtensionWizard::nameList( implode( ' ', array_map( function ( $i ) { return "n$i"; }, range( 1, 50 ) ) ) ) );
        $this->assertSame( array(), expTemplateExtensionWizard::nameList( null ) );
    }

    public function testTemplateParameters()
    {
        $this->assertSame( array(
            array( 'name' => 'limit', 'type' => 'integer', 'required' => true ),
            array( 'name' => 'label', 'type' => 'string', 'required' => false ),
            array( 'name' => 'items', 'type' => 'array', 'required' => false ),
            array( 'name' => 'flag', 'type' => 'boolean', 'required' => true ),
        ), expTemplateExtensionWizard::parameterList( "limit: INTEGER required\nlabel\nitems array no\nflag, boolean, yes\n9bad integer" ) );
        $this->assertSame( array( 'string', 'integer', 'float', 'boolean', 'array', 'none' ), array_keys( expTemplateExtensionWizard::parameterTypes() ) );
    }

    public function testTemplateUsage()
    {
        $settings = expTemplateExtensionWizard::settings( array( 'name' => 'k1e_tpl_ext', 'operators' => 'shout', 'functions' => 'box', 'fetches' => 'list',
                                                                 'aliases' => 'latest', 'parameters' => "limit integer", 'submitted' => 1, 'input' => 1, 'children' => 1 ) );
        $usage = expTemplateExtensionWizard::usage( $settings );
        $this->assertSame( array( 'operator', 'function', 'fetch', 'alias' ), array_column( $usage, 'kind' ) );
        $this->assertStringStartsWith( '{$value|shout( limit=', $usage[0]['example'] );
        $this->assertStringNotContainsString( '|wash', $usage[0]['example'] );
        $this->assertStringContainsString( '{/box}', $usage[1]['example'] );
        $this->assertSame( '{def $thing=fetch( k1e_tpl_ext, list, hash( id, 42 ) )}', $usage[2]['example'] );
        $this->assertSame( '{def $thing=fetch_alias( latest )}', $usage[3]['example'] );

        // the first visit: input and output on
        $first = expTemplateExtensionWizard::settings( array( 'name' => 'k1e_tpl_ext', 'operators' => 'shout' ) );
        $this->assertTrue( $first['input'] );
        $this->assertTrue( $first['output'] );
        $this->assertSame( 'k1etplextOperators', $first['class'] );
        $this->assertSame( 'k1etplextOperatorsFunctions', $first['function_class'] );
        $this->assertSame( '{$value|shout|wash}', expTemplateExtensionWizard::usage( $first )[0]['example'] );
        $this->assertNotEmpty( expTemplateExtensionWizard::hints() );
    }

    public function testTemplateNamesTheEngineHasAreTaken()
    {
        $this->assertSame( array( 'wash' ), expTemplateExtensionWizard::taken( array( 'wash', 'k1e_never_registered' ) ) );
    }

    // ---------------------------------------------------------------- workflow event wizard

    public function testWorkflowStatuses()
    {
        $common = array();
        foreach ( expWorkflowEventWizard::statuses() as $key => $status )
            if ( $status['common'] )
                $common[] = $key;
        $this->assertSame( $common, expWorkflowEventWizard::safeStatuses( null ) );
        $this->assertSame( array( 'STATUS_ACCEPTED' ), expWorkflowEventWizard::safeStatuses( 'STATUS_REJECTED' ) );
        $this->assertSame( array( 'STATUS_ACCEPTED', 'STATUS_REJECTED' ), expWorkflowEventWizard::safeStatuses( array( 'STATUS_REJECTED', 'STATUS_NOPE', 'STATUS_REJECTED', 1 ) ) );
    }

    public function testWorkflowSettingsGetColumnsOfTheirKind()
    {
        $attributes = expWorkflowEventWizard::safeAttributes( array(
            array( 'name' => 'limit', 'type' => 'integer', 'label' => 'Limit' ),
            array( 'name' => 'mode', 'type' => 'select', 'choices' => 'fast, slow, fast,  ' ),
            array( 'name' => 'limit', 'type' => 'text' ),
            array( 'name' => 'note', 'type' => 'nonsense', 'help' => 'Typed */ help' ),
            'not an array',
            array( 'name' => '' ),
        ) );
        $this->assertSame( array( 'limit', 'mode', 'note' ), array_column( $attributes, 'name' ) );
        $this->assertSame( array( 'data_int1', 'data_text1', 'data_text2' ), array_column( $attributes, 'column' ) );
        $this->assertSame( array( 'fast', 'slow' ), $attributes[1]['choices'] );
        $this->assertSame( 'Mode', $attributes[1]['label'] );
        $this->assertSame( 'text', $attributes[2]['type'] );
        $this->assertSame( 'Typed * help', $attributes[2]['help'] );
        $this->assertSame( array(), expWorkflowEventWizard::safeAttributes( 'x' ) );

        // five text columns, four integer ones: the sixth text setting has nowhere to go
        $many = array();
        foreach ( range( 1, 7 ) as $i )
            $many[] = array( 'name' => "text$i", 'type' => 'text' );
        $this->assertCount( 5, expWorkflowEventWizard::safeAttributes( $many ) );
    }

    public function testWorkflowNamesTriggersAndProblems()
    {
        $this->assertSame( 'myevent', expWorkflowEventWizard::safeEventName( ' My-Event ' ) );
        $this->assertSame( '', expWorkflowEventWizard::safeEventName( 'ab' ) );
        $this->assertSame( '', expWorkflowEventWizard::safeEventName( array() ) );
        $this->assertSame( array(), expWorkflowEventWizard::safeTriggers( 'content/publish/before' ) );
        $triggers = expWorkflowEventWizard::triggers();
        $this->assertArrayHasKey( 'content', $triggers );
        $this->assertArrayHasKey( 'publish', $triggers['content'] );
        $chosen = expWorkflowEventWizard::safeTriggers( array( 'content/publish/before', 'content/publish/after', 'content/publish/before', 'content/nope/before', 'x', 5 ) );
        $this->assertSame( array( 'content' => array( 'publish' => array( 'before', 'after' ) ) ), $chosen );

        $settings = expWorkflowEventWizard::settings( array( 'name' => 'k1e_event_ext', 'licence' => 'MIT', 'triggers' => array( 'content/publish/after', 'content/publish/before' ),
                                                             'attributes' => array( array( 'name' => 'mode', 'type' => 'select' ) ) ) );
        $this->assertSame( 'k1eeventext', $settings['event'] );
        $this->assertSame( 'K1eeventext', $settings['label'] );
        $this->assertSame( 'k1eeventextType', expWorkflowEventWizard::className( $settings ) );
        $this->assertSame( array( 'after content/publish', 'before content/publish' ), expWorkflowEventWizard::triggerSentences( $settings ) );
        $problems = implode( ' ', expWorkflowEventWizard::problems( $settings ) );
        $this->assertStringContainsString( '"Mode" is a list', $problems );
        $this->assertSame( "a\\'b\\\\", expWorkflowEventWizard::phpStringPublic( "a'b\\" ) );
        $this->assertArrayHasKey( 'select', expWorkflowEventWizard::attributeTypes() );
    }

    public function testWorkflowSettingsWithQuotesAndCommentEndsWriteSoundFiles()
    {
        $settings = expWorkflowEventWizard::settings( array( 'name' => 'k1e_event_ext', 'licence' => 'MIT', 'triggers' => array( 'content/publish/before' ),
            'attributes' => array( array( 'name' => 'mode', 'type' => 'select', 'label' => "It's */ k1e_injected(); /*", 'choices' => "o'ne, t\"wo, */ k1e_injected(); /*", 'help' => '?> <?php k1e_injected();' ),
                                   array( 'name' => 'count', 'type' => 'integer', 'label' => 'Count \\' ),
                                   array( 'name' => 'ids', 'type' => 'idlist' ), array( 'name' => 'yes', 'type' => 'checkbox' ),
                                   array( 'name' => 'attrs', 'type' => 'classattribute' ) ),
            'parts' => array_keys( expWorkflowEventWizard::parts() ) ) );
        $files = expWorkflowEventWizard::files( $settings );
        $this->assertNotEmpty( $files );
        expRadWizardTestHelper::assertFilesAreSafe( $this, $files );
    }

    // ---------------------------------------------------------------- handler wizard

    public function testHandlerKinds()
    {
        foreach ( expHandlerWizard::kinds() as $key => $recipe )
        {
            $this->assertSame( $recipe, expHandlerWizard::kind( $key ) );
            $this->assertArrayHasKey( 'title', $recipe, $key );
            $this->assertArrayHasKey( 'methods', $recipe, $key );
            if ( !empty( $recipe['source'] ) )
                $this->assertFileExists( expRadWizardTestHelper::root() . '/' . $recipe['source'], "$key names a source that is not there" );
            if ( !empty( $recipe['base'] ) )
                $this->assertTrue( class_exists( $recipe['base'] ) || interface_exists( $recipe['base'] ), "$key: base {$recipe['base']}" );
        }
        $this->assertFalse( expHandlerWizard::kind( 'nonsense' ) );
        $this->assertFalse( expHandlerWizard::kind( array() ) );
    }

    public function testHandlerAliasesAndClassNames()
    {
        $this->assertSame( 'mytransport', expHandlerWizard::safeAlias( 'My-Transport' ) );
        $this->assertSame( '', expHandlerWizard::safeAlias( 'x' ) );
        $this->assertSame( '', expHandlerWizard::safeAlias( null ) );
        $this->assertSame( 'MyClass_1', expHandlerWizard::safeClass( 'My Class_1!' ) );
        $this->assertSame( '', expHandlerWizard::safeClass( '1abc' ) );

        $settings = expHandlerWizard::settings( array( 'name' => 'k1e_handler_ext', 'kind' => 'mail' ) );
        $this->assertSame( 'k1ehandlerexttransport', $settings['class'] );
        $this->assertSame( 'k1e_handler_ext', $settings['alias'] );
        $this->assertSame( 'classes/k1ehandlerexttransport.php', expHandlerWizard::classPath( $settings, expHandlerWizard::kind( 'mail' ) ) );
        $this->assertSame( 'x/k1e_handler_ext/k1ehandlerexttransport.php', expHandlerWizard::classPath( $settings, array( 'path' => 'x/%alias%/%class%.php' ) ) );
        $first = array_keys( expHandlerWizard::kinds() )[0];
        $this->assertSame( $first, expHandlerWizard::settings( array( 'kind' => 'nonsense' ) )['kind'] );
        $this->assertStringContainsString( 'That is not a kind', implode( ' ', expHandlerWizard::problems( array( 'kind' => 'nonsense' ) + expHandlerWizard::settings( array( 'name' => 'k1e_handler_ext' ) ) ) ) );
        $problems = expHandlerWizard::problems( expHandlerWizard::settings( array( 'name' => 'k1e_handler_ext', 'kind' => 'mail', 'class' => 'eZMailTransport' ) ) );
        $this->assertArrayHasKey( 'exists_class', $problems );
    }

    public static function callProvider()
    {
        return array(
            array( 'read( $sessionId )', 'read( $sessionId )' ),
            array( 'cleanup()', 'cleanup()' ),
            array( 'regenerate( $updateBackendData = true )', 'regenerate( $updateBackendData )' ),
            array( 'deleteByUserIDs( array $userIDArray )', 'deleteByUserIDs( $userIDArray )' ),
            array( 'install( $package, &$installParameters, &$installData )', 'install( $package, $installParameters, $installData )' ),
            array( 'sendMail( eZMail $mail )', 'sendMail( $mail )' ),
            array( 'noParentheses', 'noParentheses' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('callProvider')]
    public function testHandlerCallOf( $signature, $call )
    {
        $this->assertSame( $call, expHandlerWizard::callOf( $signature ) );
    }

    // ---------------------------------------------------------------- kernel override wizard

    public function testKernelClassesAndSearch()
    {
        $classes = expKernelOverrideWizard::kernelClasses();
        $this->assertArrayHasKey( 'eZURI', $classes );
        foreach ( $classes as $name => $path )
            $this->assertTrue( strpos( $path, 'kernel/' ) === 0 || strpos( $path, 'lib/' ) === 0, "$name: $path" );
        $keys = array_keys( $classes );
        $sorted = $keys;
        sort( $sorted );
        $this->assertSame( $sorted, $keys );

        $this->assertSame( array(), expKernelOverrideWizard::matching( '  ' ) );
        $found = expKernelOverrideWizard::matching( 'ezuri lib/ezutils' );
        $this->assertArrayHasKey( 'eZURI', $found );
        foreach ( $found as $name => $path )
        {
            $this->assertStringContainsString( 'ezuri', strtolower( $name . ' ' . $path ) );
            $this->assertStringContainsString( 'lib/ezutils', strtolower( $name . ' ' . $path ) );
        }
        $this->assertCount( 3, expKernelOverrideWizard::matching( 'ez', 3 ) );
        $this->assertSame( array(), expKernelOverrideWizard::matching( 'k1e-no-such-class-anywhere' ) );
    }

    public function testKernelOverrideSettingsAndProblems()
    {
        $settings = expKernelOverrideWizard::settings( array( 'name' => 'k1e_override_ext', 'class' => 'eZURI', 'licence' => 'MIT', 'reason' => 'Testing.' ) );
        $this->assertSame( 'lib/ezutils/classes/ezuri.php', $settings['source'] );
        $this->assertSame( 'eZURI override', $settings['title'] );
        $this->assertSame( 'Replaces eZURI in the kernel.', $settings['summary'] );
        $this->assertSame( md5_file( 'lib/ezutils/classes/ezuri.php' ), expKernelOverrideWizard::checksum( $settings ) );
        $this->assertSame( array(), array_values( expKernelOverrideWizard::problems( $settings ) ) );
        $this->assertIsArray( expKernelOverrideWizard::alreadyOverridden( $settings ) );

        $unknown = expKernelOverrideWizard::settings( array( 'name' => 'k1e_override_ext', 'class' => 'NoSuchKernelClass' ) );
        $this->assertSame( '', $unknown['class'] );
        $this->assertSame( '', expKernelOverrideWizard::checksum( $unknown ) );
        $this->assertSame( array(), expKernelOverrideWizard::alreadyOverridden( $unknown ) );
        $text = implode( ' ', expKernelOverrideWizard::problems( $unknown ) );
        $this->assertStringContainsString( 'Choose a kernel class', $text );

        $readiness = expKernelOverrideWizard::readiness();
        $this->assertSame( array( 'allowed', 'generated', 'message' ), array_keys( $readiness ) );
        if ( !$readiness['allowed'] )
            $this->assertStringContainsString( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', $readiness['message'] );
    }

    // ---------------------------------------------------------------- datatype and design wizards

    public function testDatatypeColumnsAndMethods()
    {
        $this->assertCount( 13, expDatatypeWizard::settingColumns() );
        $this->assertSame( 'float', expDatatypeWizard::settingColumns()['data_float2']['kind'] );
        $settings = expDatatypeWizard::settings( array( 'name' => 'k1e_dt_ext', 'storage' => array( 'data_int', 'nonsense' ), 'capabilities' => array(),
                                                        'class_setting_names' => array( 'data_int1' => 'Max Value', 'data_nonsense' => 'x', 'data_text1' => array( 'x' ), 'data_text2' => '!!' ) ) );
        $this->assertSame( 'k1edtext', $settings['type'] );
        $this->assertSame( 'k1edtextType', $settings['class'] );
        $this->assertSame( array( 'data_int' ), expDatatypeWizard::usedStorage( $settings ) );
        $this->assertSame( array( 'data_int1' => 'max_value' ), $settings['settings_used'] );
        $required = array();
        foreach ( expDatatypeWizard::capabilities() as $key => $capability )
            if ( !empty( $capability['required'] ) )
                $required[] = $key;
        $this->assertSame( $required, array_keys( array_filter( $settings['capabilities'] ) ) );
        $methods = array_column( expDatatypeWizard::chosenMethods( $settings ), 'name' );
        $this->assertContains( 'validateObjectAttributeHTTPInput', $methods );
        $this->assertSame( array( 'no column' ), expDatatypeWizard::usedStorage( array( 'storage' => array() ) ) );

        $none = expDatatypeWizard::settings( array( 'name' => 'k1e_dt_ext', 'storage' => array() ) );
        $this->assertStringContainsString( 'at least one column', implode( ' ', expDatatypeWizard::problems( $none ) ) );
        $sorting = expDatatypeWizard::settings( array( 'name' => 'k1e_dt_ext', 'storage' => array( 'data_text' ), 'capabilities' => array( 'sorting' ) ) );
        if ( isset( expDatatypeWizard::capabilities()['sorting'] ) )
            $this->assertStringContainsString( 'Sorting needs', implode( ' ', expDatatypeWizard::problems( $sorting ) ) );
        $taken = expDatatypeWizard::settings( array( 'name' => 'k1e_dt_ext', 'type' => 'ezstring' ) );
        $this->assertStringContainsString( 'ezstring is already installed', implode( ' ', expDatatypeWizard::problems( $taken ) ) );
        $this->assertContains( 'ezstring', expDatatypeWizard::existingTypes() );
        $this->assertSame( 'ezstring', expDatatypeWizard::safeType( 'EZ-String' ) );
        $this->assertSame( '', expDatatypeWizard::safeType( 'ab' ) );
        $this->assertSame( 'max_value', expDatatypeWizard::safeIdentifier( 'Max Value' ) );
        $this->assertNotEmpty( expDatatypeWizard::templates() );
    }

    public function testDesignWizard()
    {
        $designs = expDesignExtensionWizard::baseDesigns();
        $this->assertArrayHasKey( 'standard', $designs );
        $settings = expDesignExtensionWizard::settings( array( 'name' => 'k1e_design_ext', 'base_design' => 'no-such-design' ) );
        $this->assertSame( 'standard', $settings['base_design'] );
        $this->assertSame( 'The K1e Design Ext design.', $settings['summary'] );
        $problems = expDesignExtensionWizard::problems( expDesignExtensionWizard::settings( array( 'name' => 'k1e_design_ext', 'licence' => 'MIT', 'parts' => array( 'siteaccess' ) ) ) );
        $this->assertNotEmpty( $problems, 'a siteaccess part needs a siteaccess name' );
    }
}
