<?php
/**
 * Tests of the workflow event types' logic that needs no database: the type registry of eZWorkflowType
 * (createType(), registerType(), the trigger check isAllowed(), status names), and for the approve, multiplexer
 * and payment gateway events the decoding of their stored settings (attributeDecoder() through the event's own
 * attribute()) and the reading of their edit forms (fetchHTTPInput()), including the browse results that add
 * approvers and excluded groups.
 *
 * The POST and session variables come from a stand-in for eZHTTPTool (fixtures/k1workflowtesthttp.php).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/fixtures/k1workflowtesthttp.php';

class eZWorkflowEventTypesTest extends PHPUnit\Framework\TestCase
{
    const EVENT_ID = 999999901;

    private $exceptionHandler;

    private static function currentExceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->exceptionHandler = self::currentExceptionHandler();
    }

    protected function tearDown(): void
    {
        // loading a type or a module starts the kernel's shutdown handling, which installs an exception handler
        for ( $i = 0; $i < 5 && self::currentExceptionHandler() !== $this->exceptionHandler; ++$i )
            restore_exception_handler();
        // the registries are filled when a type's file is included (once per process): remove only this test's entries
        foreach ( array( 'eZWorkflowTypes', 'eZWorkflowTypeObjects', 'eZPaymentGateways' ) as $name )
        {
            foreach ( array_keys( $GLOBALS[$name] ?? array() ) as $key )
            {
                if ( strpos( $key, 'k1' ) !== false )
                    unset( $GLOBALS[$name][$key] );
            }
        }
    }

    private function event( $typeString, array $row = array() )
    {
        return new eZWorkflowEvent( $row + array( 'id' => self::EVENT_ID, 'version' => 0, 'workflow_id' => 1,
                                                  'workflow_type_string' => $typeString, 'description' => '', 'placement' => 1,
                                                  'data_int1' => 0, 'data_int2' => 0, 'data_int3' => 0, 'data_int4' => 0,
                                                  'data_text1' => '', 'data_text2' => '', 'data_text3' => '', 'data_text4' => '', 'data_text5' => '' ) );
    }

    // ---------------------------------------------------------- registry

    public function testCreateTypeReturnsOneSharedObjectPerType()
    {
        $type = eZWorkflowType::createType( 'event_ezapprove' );
        $this->assertInstanceOf( 'eZApproveType', $type );
        $this->assertSame( $type, eZWorkflowType::createType( 'event_ezapprove' ) );
        $this->assertSame( 'event', $type->attribute( 'group' ) );
        $this->assertSame( 'ezapprove', $type->attribute( 'type' ) );
        $this->assertSame( 'event_ezapprove', $type->attribute( 'type_string' ) );
        $this->assertSame( $type->attribute( 'name' ), $type->attribute( 'description' ) );
    }

    public static function unknownTypeProvider()
    {
        return array( 'no group separator' => array( 'ezapprove' ), 'unknown type' => array( 'event_k1nosuchtype' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('unknownTypeProvider')]
    public function testUnknownTypesGiveNull( $typeString )
    {
        $this->assertNull( eZWorkflowType::createType( $typeString ) );
    }

    public function testRegisterTypeKeepsTheFirstRegistration()
    {
        eZWorkflowType::registerType( 'event', 'k1first', 'eZWorkflowEventType' );
        eZWorkflowType::registerType( 'event', 'k1first', 'eZApproveType' );
        $this->assertSame( array( 'class_name' => 'eZWorkflowEventType' ), $GLOBALS['eZWorkflowTypes']['event_k1first'] );
    }

    public function testEveryAvailableEventTypeOfTheShippedSettingsLoads()
    {
        foreach ( eZWorkflowType::allowedTypes() as $typeString )
        {
            $this->assertInstanceOf( 'eZWorkflowType', eZWorkflowType::createType( $typeString ), $typeString );
        }
        $this->assertContains( 'event_ezapprove', eZWorkflowType::allowedTypes() );
    }

    public function testIsAllowed()
    {
        $any = new eZWorkflowEventType( 'k1any', 'Any' );
        $this->assertTrue( $any->isAllowed( 'shop', 'checkout', 'after' ), 'a type without trigger types allows every trigger' );

        $approve = eZWorkflowType::createType( 'event_ezapprove' );
        $this->assertTrue( $approve->isAllowed( 'content', 'publish', 'before' ) );
        $this->assertFalse( $approve->isAllowed( 'content', 'publish', 'after' ) );
        $this->assertFalse( $approve->isAllowed( 'content', 'read', 'before' ) );
        $this->assertFalse( $approve->isAllowed( 'shop', 'checkout', 'before' ) );
        $this->assertSame( array( 'content' => array( 'publish' => array( 'before' ) ) ), $approve->attribute( 'allowed_triggers' ) );
    }

    public function testSetAttributeChangesOnlyKnownAttributes()
    {
        $type = new eZWorkflowEventType( 'k1set', 'Before' );
        $type->setAttribute( 'name', 'After' );
        $type->setAttribute( 'no_such_attribute', 'x' );
        $this->assertSame( 'After', $type->attribute( 'name' ) );
        $this->assertSame( 'After', $type->Name );
        $this->assertFalse( $type->hasAttribute( 'no_such_attribute' ) );
        $type->setInformation( 'info' );
        $type->setActivationDate( 1234 );
        $this->assertSame( 'info', $type->attribute( 'information' ) );
        $this->assertSame( 1234, $type->attribute( 'activation_date' ) );
    }

    public function testBaseTypeDefaults()
    {
        $type = new eZWorkflowEventType( 'k1base', 'Base' );
        $event = $this->event( 'event_k1base' );
        $validation = array();
        $this->assertSame( eZWorkflowType::STATUS_NONE, $type->execute( null, $event ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED, $type->validateHTTPInput( null, 'x', $event, $validation ) );
        $this->assertTrue( $type->fixupHTTPInput( null, 'x', $event ) );
        $this->assertFalse( $type->needCleanup() );
        $this->assertNull( $type->attributeDecoder( $event, 'x' ) );
        $this->assertSame( array(), $type->typeFunctionalAttributes() );
        $this->assertSame( '', $type->workflowEventContent( $event ) );
    }

    public function testUnknownStatusHasNoName()
    {
        $this->assertFalse( eZWorkflowType::statusName( 999 ) );
    }

    // ---------------------------------------------------------- approve

    public function testApproveDecodesItsStoredLists()
    {
        $event = $this->event( 'event_ezapprove', array( 'data_text1' => '1,3', 'data_text2' => '12', 'data_text3' => '14, 15', 'data_text4' => '', 'data_int3' => 7 ) );
        $this->assertSame( array( '1', '3' ), $event->attribute( 'selected_sections' ) );
        $this->assertSame( array( '12' ), $event->attribute( 'selected_usergroups' ) );
        $this->assertSame( array( '14', ' 15' ), $event->attribute( 'approve_users' ) );
        $this->assertSame( array(), $event->attribute( 'approve_groups' ) );
        $this->assertSame( eZApproveType::VERSION_OPTION_ALL, $event->attribute( 'version_option' ), 'only the two option bits are kept' );
        $this->assertSame( array(), $event->attribute( 'language_list' ), 'no language mask means every language' );
    }

    public function testApproveWithoutSectionsMeansAnySection()
    {
        $event = $this->event( 'event_ezapprove', array( 'data_text1' => '  ' ) );
        $this->assertSame( array( -1 ), $event->attribute( 'selected_sections' ) );
    }

    public function testApproveReadsItsForm()
    {
        $event = $this->event( 'event_ezapprove' );
        $id = self::EVENT_ID;
        $http = new k1WorkflowTestHTTP( array(
            "WorkflowEvent_event_ezapprove_section_$id" => array( '1', '6' ),
            "WorkflowEvent_event_ezapprove_languages_$id" => array( '2', '4', '8' ),
            "WorkflowEvent_event_ezapprove_version_option_$id" => array( '1', '2', '8' ),
        ) );
        eZWorkflowType::createType( 'event_ezapprove' )->fetchHTTPInput( $http, 'WorkflowEvent', $event );
        $this->assertSame( '1,6', $event->attribute( 'data_text1' ) );
        $this->assertSame( 14, $event->attribute( 'data_int2' ) );
        $this->assertSame( 3, $event->attribute( 'data_int3' ) );
    }

    public function testApproveAnySectionAndAnyLanguageWinOverTheRest()
    {
        $event = $this->event( 'event_ezapprove', array( 'data_int2' => 6 ) );
        $id = self::EVENT_ID;
        $http = new k1WorkflowTestHTTP( array(
            "WorkflowEvent_event_ezapprove_section_$id" => array( '1', '-1', '6' ),
            "WorkflowEvent_event_ezapprove_languages_$id" => array( '2', '-1' ),
        ) );
        eZWorkflowType::createType( 'event_ezapprove' )->fetchHTTPInput( $http, 'WorkflowEvent', $event );
        $this->assertSame( '-1', $event->attribute( 'data_text1' ) );
        $this->assertSame( 0, $event->attribute( 'data_int2' ) );
    }

    public function testApproveLeavesFieldsThatWereNotPosted()
    {
        $event = $this->event( 'event_ezapprove', array( 'data_text1' => '3', 'data_int2' => 2, 'data_int3' => 1 ) );
        eZWorkflowType::createType( 'event_ezapprove' )->fetchHTTPInput( new k1WorkflowTestHTTP(), 'WorkflowEvent', $event );
        $this->assertSame( '3', $event->attribute( 'data_text1' ) );
        $this->assertSame( 2, $event->attribute( 'data_int2' ) );
        $this->assertSame( 1, $event->attribute( 'data_int3' ) );
    }

    public static function browseProvider()
    {
        return array(
            'approver groups are added once' => array( 'AddApproveGroups', 'data_text4', '12,13', array( '13', '14', '12' ), '12,13,14' ),
            'excluded groups are added once' => array( 'AddExcludeUser', 'data_text2', '', array( '20', '20' ), '20' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('browseProvider')]
    public function testApproveTakesTheBrowseResult( $action, $field, $before, $selected, $after )
    {
        $event = $this->event( 'event_ezapprove', array( $field => $before ) );
        $http = new k1WorkflowTestHTTP( array( 'SelectedObjectIDArray' => $selected ),
                                        array( 'BrowseParameters' => array( 'custom_action_data' => array( 'event_id' => self::EVENT_ID, 'browse_action' => $action ) ) ) );
        eZWorkflowType::createType( 'event_ezapprove' )->fetchHTTPInput( $http, 'WorkflowEvent', $event );
        $this->assertSame( $after, $event->attribute( $field ) );
        $this->assertFalse( $http->hasSessionVariable( 'BrowseParameters' ), 'the browse result is used once' );
    }

    public function testApproveIgnoresABrowseResultForAnotherEventOrACancelledBrowse()
    {
        $browse = array( 'BrowseParameters' => array( 'custom_action_data' => array( 'event_id' => self::EVENT_ID + 1, 'browse_action' => 'AddApproveGroups' ) ) );
        $event = $this->event( 'event_ezapprove', array( 'data_text4' => '12' ) );
        $http = new k1WorkflowTestHTTP( array( 'SelectedObjectIDArray' => array( '13' ) ), $browse );
        eZWorkflowType::createType( 'event_ezapprove' )->fetchHTTPInput( $http, 'WorkflowEvent', $event );
        $this->assertSame( '12', $event->attribute( 'data_text4' ) );
        $this->assertTrue( $http->hasSessionVariable( 'BrowseParameters' ) );

        $browse['BrowseParameters']['custom_action_data']['event_id'] = self::EVENT_ID;
        $http = new k1WorkflowTestHTTP( array( 'SelectedObjectIDArray' => array( '13' ), 'BrowseCancelButton' => 1 ), $browse );
        eZWorkflowType::createType( 'event_ezapprove' )->fetchHTTPInput( $http, 'WorkflowEvent', $event );
        $this->assertSame( '12', $event->attribute( 'data_text4' ) );
    }

    public function testApproveValidationNeedsAnApprover()
    {
        $event = $this->event( 'event_ezapprove' );
        $validation = array();
        $type = eZWorkflowType::createType( 'event_ezapprove' );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateHTTPInput( new k1WorkflowTestHTTP(), 'WorkflowEvent', $event, $validation ) );
        $this->assertTrue( $validation['processed'] );
        $this->assertSame( self::EVENT_ID, $validation['events'][0]['id'] );
        $this->assertStringContainsString( 'at least one valid user or user group', $validation['events'][0]['reason']['text'] );
    }

    public function testApproveValidationLetsAnApproverBeRemoved()
    {
        $event = $this->event( 'event_ezapprove' );
        $validation = array();
        $http = new k1WorkflowTestHTTP( array( 'DeleteApproveUserIDArray_' . self::EVENT_ID => array( '14' ) ) );
        $this->assertSame( eZInputValidator::STATE_ACCEPTED,
                           eZWorkflowType::createType( 'event_ezapprove' )->validateHTTPInput( $http, 'WorkflowEvent', $event, $validation ) );
        $this->assertSame( array(), $validation );
    }

    public function testApproveUserIDsThatAreNotNumbersAreRefused()
    {
        $reason = array();
        $type = eZWorkflowType::createType( 'event_ezapprove' );
        $this->assertSame( eZInputValidator::STATE_INVALID, $type->validateUserIDList( array( 'x', '' ), $reason ) );
        $this->assertSame( array( 'x', '' ), $reason['list'] );
    }

    // ------------------------------------------------------- multiplexer

    public function testMultiplexerDecodesItsSettings()
    {
        $event = $this->event( 'event_ezmultiplexer', array( 'data_text1' => '', 'data_text2' => '11,12', 'data_text5' => '2,16', 'data_int1' => 5, 'data_int3' => 2 ) );
        $this->assertSame( array( -1 ), $event->attribute( 'selected_sections' ) );
        $this->assertSame( array( '11', '12' ), $event->attribute( 'selected_usergroups' ) );
        $this->assertSame( array( '2', '16' ), $event->attribute( 'selected_classes' ) );
        $this->assertSame( 5, $event->attribute( 'selected_workflow' ) );
        $this->assertSame( eZMultiplexerType::VERSION_OPTION_EXCEPT_FIRST, $event->attribute( 'version_option' ) );
    }

    public function testMultiplexerReadsItsFormOnlyWhenStored()
    {
        $id = self::EVENT_ID;
        $post = array(
            "WorkflowEvent_event_ezmultiplexer_section_ids_$id" => array( '1' ),
            "WorkflowEvent_event_ezmultiplexer_class_ids_$id" => array( '2', '-1' ),
            "WorkflowEvent_event_ezmultiplexer_languages_$id" => array( '2', '8' ),
            "WorkflowEvent_event_ezmultiplexer_workflow_id_$id" => '7',
            "WorkflowEvent_event_ezmultiplexer_version_option_$id" => array( '1' ),
        );
        $type = eZWorkflowType::createType( 'event_ezmultiplexer' );

        $event = $this->event( 'event_ezmultiplexer', array( 'data_text2' => '11' ) );
        $type->fetchHTTPInput( new k1WorkflowTestHTTP( $post ), 'WorkflowEvent', $event );
        $this->assertSame( '', $event->attribute( 'data_text1' ), 'without StoreButton nothing is read' );
        $this->assertSame( '11', $event->attribute( 'data_text2' ) );

        $type->fetchHTTPInput( new k1WorkflowTestHTTP( $post + array( 'StoreButton' => 1 ) ), 'WorkflowEvent', $event );
        $this->assertSame( '1', $event->attribute( 'data_text1' ) );
        $this->assertSame( '-1', $event->attribute( 'data_text5' ) );
        $this->assertSame( 10, $event->attribute( 'data_int2' ) );
        $this->assertSame( '7', $event->attribute( 'data_int1' ) );
        $this->assertSame( 1, $event->attribute( 'data_int3' ) );
        $this->assertSame( '', $event->attribute( 'data_text2' ), 'no user groups posted means none are excluded' );

        $type->fetchHTTPInput( new k1WorkflowTestHTTP( array( 'StoreButton' => 1, "WorkflowEvent_event_ezmultiplexer_not_run_ids_$id" => array( '11', '12' ) ) ), 'WorkflowEvent', $event );
        $this->assertSame( '11,12', $event->attribute( 'data_text2' ) );
    }

    // --------------------------------------------------- payment gateway

    public function testPaymentGatewaysAreListedByType()
    {
        $type = eZWorkflowType::createType( 'event_ezpaymentgateway' );
        eZPaymentGatewayType::registerGateway( 'k1one', 'K1OneGateway', 'One' );
        eZPaymentGatewayType::registerGateway( 'k1two', 'K1TwoGateway', 'Two' );
        eZPaymentGatewayType::registerGateway( 'k1one', 'K1Other', 'Other' );

        $this->assertSame( array( array( 'class_name' => 'K1TwoGateway', 'description' => 'Two', 'Name' => 'Two', 'value' => 'k1two' ) ),
                           $type->getGateways( array( 'k1two' ) ) );
        $this->assertSame( array_keys( $GLOBALS['eZPaymentGateways'] ), array_column( $type->getGateways( array( '-1' ) ), 'value' ), 'any means every registered gateway' );
        $this->assertContains( 'k1two', array_column( $type->attribute( 'available_gateways' ), 'value' ) );
        $this->assertSame( 'K1OneGateway', $type->getGateways( array( 'k1one' ) )[0]['class_name'], 'the first registration is kept' );
    }

    public function testPaymentGatewayDecodesItsSettings()
    {
        eZWorkflowType::createType( 'event_ezpaymentgateway' );
        eZPaymentGatewayType::registerGateway( 'k1one', 'K1OneGateway', 'One' );
        $event = $this->event( 'event_ezpaymentgateway', array( 'data_text1' => 'k1one', 'data_text2' => 'k1one' ) );
        $this->assertSame( array( 'k1one' ), $event->attribute( 'selected_gateways_types' ) );
        $this->assertSame( array( 'k1one' ), array_column( $event->attribute( 'selected_gateways' ), 'value' ) );
        $this->assertSame( 'k1one', $event->attribute( 'current_gateway' ) );
    }

    public function testPaymentGatewayReadsItsForm()
    {
        $type = eZWorkflowType::createType( 'event_ezpaymentgateway' );
        $var = 'WorkflowEvent_event_ezpaymentgateway_gateways_' . self::EVENT_ID;
        $event = $this->event( 'event_ezpaymentgateway' );
        $type->fetchHTTPInput( new k1WorkflowTestHTTP( array( $var => array( 'a', 'b' ) ) ), 'WorkflowEvent', $event );
        $this->assertSame( 'a,b', $event->attribute( 'data_text1' ) );
        $type->fetchHTTPInput( new k1WorkflowTestHTTP( array( $var => array( 'a', '-1' ) ) ), 'WorkflowEvent', $event );
        $this->assertSame( '-1', $event->attribute( 'data_text1' ) );
    }

    public function testPaymentLoggerWritesItsLines()
    {
        $file = 'var/tmp/phpunit-paymentlogger-' . getmypid() . '-' . mt_rand() . '.log';
        try
        {
            $logger = eZPaymentLogger::CreateNew( $file );
            $logger->writeString( 'first', 'label' );
            $logger->writeString( 'second' );
            $this->assertSame( "label: first\r\nsecond\r\n", file_get_contents( $file ), 'a new log is truncated once, then appended to' );

            $logger = eZPaymentLogger::CreateNew( $file );
            $logger->writeTimedString( 'third', 'x' );
            $this->assertMatchesRegularExpression( '#^\d\d-\d\d-\d{4} \d\d-\d\d  x: third\n$#', file_get_contents( $file ) );

            eZPaymentLogger::CreateForAdd( $file )->writeString( array( 'k' => 'v' ) );
            $this->assertStringContainsString( '["k"]=>', file_get_contents( $file ) );
        }
        finally
        {
            if ( file_exists( $file ) )
                unlink( $file );
        }
    }
}
