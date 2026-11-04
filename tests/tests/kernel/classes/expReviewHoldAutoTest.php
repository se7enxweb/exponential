<?php
/**
 * The auto-hold of content submitted by visitors (expReviewHoldAuto): its settings, the decision whether a publication
 * is held, who is exempt, the reason as it is kept, the step of the publish operation and its place, and the audit
 * events of the hold.
 *
 * No database, no files.
 *
 *  RA-1  the settings: AutoHold only "enabled" (and its synonyms) switches it on; lists are trimmed and unique; a
 *        malformed exempt policy is dropped
 *  RA-2  holds(): only the first publication of a listed class by a user who is not exempt, and not held already
 *  RA-3  exemptBy(): the first policy the user has, in the order of the setting
 *  RA-4  reason(): one line, no control characters, at most 500 characters
 *  RA-5  the step auto-review-hold runs after the object is published and before the content edit handlers (expwt's
 *        HideNew), the commit, the view cache and the search index
 *  RA-6  the audit events of the hold are in the catalogue, on by default, with their verbs
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expReviewHoldAutoTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expReviewHoldAutoTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        expReviewHoldAuto::$settings = null;
        parent::tearDown();
    }

    /** RA-1 */
    public function testSettings()
    {
        expReviewHoldAuto::$settings = array( 'AutoHold' => ' Enabled ', 'AutoHoldClassList' => array( 'comment', ' review ', 'comment', '' ),
                                              'ExemptPolicyList' => array( 'state/assign', 'not a policy', 'moderation/act', 'content/*' ) );
        $s = expReviewHoldAuto::settings();
        $this->assertTrue( $s['enabled'] );
        $this->assertSame( array( 'comment', 'review' ), $s['classes'] );
        $this->assertSame( array( 'state/assign', 'moderation/act', 'content/*' ), $s['exempt'] );

        foreach ( array( 'disabled', '', 'no', 'off', null ) as $off )
        {
            expReviewHoldAuto::$settings = array( 'AutoHold' => $off, 'AutoHoldClassList' => array( 'comment' ), 'ExemptPolicyList' => array() );
            $this->assertFalse( expReviewHoldAuto::settings()['enabled'], var_export( $off, true ) );
        }
    }

    /** RA-1: the shipped settings */
    public function testShippedSettings()
    {
        $ini = file_get_contents( dirname( __DIR__, 4 ) . '/settings/content.ini' );
        $this->assertMatchesRegularExpression( '/^\[ReviewHoldSettings\]$/m', $ini );
        $this->assertMatchesRegularExpression( '/^AutoHold=disabled$/m', $ini, 'off until the owner switches it on' );
        $this->assertMatchesRegularExpression( '/^AutoHoldClassList\[\]=comment$/m', $ini );
        $this->assertMatchesRegularExpression( '/^ExemptPolicyList\[\]=state\/assign$/m', $ini );
        $this->assertMatchesRegularExpression( '/^ExemptPolicyList\[\]=moderation\/act$/m', $ini );
    }

    /** RA-2 */
    public function testHolds()
    {
        $classes = array( 'comment', 'review' );
        $this->assertTrue( expReviewHoldAuto::holds( true, 'comment', $classes, true, false, false ) );
        $this->assertTrue( expReviewHoldAuto::holds( true, 'review', $classes, true, false, false ) );
        $this->assertFalse( expReviewHoldAuto::holds( false, 'comment', $classes, true, false, false ), 'switched off' );
        $this->assertFalse( expReviewHoldAuto::holds( true, 'article', $classes, true, false, false ), 'another class' );
        $this->assertFalse( expReviewHoldAuto::holds( true, 'comment', $classes, false, false, false ), 'a later version' );
        $this->assertFalse( expReviewHoldAuto::holds( true, 'comment', $classes, true, true, false ), 'an exempt user' );
        $this->assertFalse( expReviewHoldAuto::holds( true, 'comment', $classes, true, false, true ), 'held already' );
        $this->assertFalse( expReviewHoldAuto::holds( true, 'comment', array(), true, false, false ), 'no class listed' );
    }

    /** RA-3 */
    public function testExemptBy()
    {
        $has = array( 'content/hide' => true, 'moderation/act' => true );
        $ask = function ( $module, $function ) use ( $has, &$asked ) {
            $asked[] = "$module/$function";
            return !empty( $has["$module/$function"] );
        };
        $asked = array();
        $this->assertSame( 'moderation/act', expReviewHoldAuto::exemptBy( array( 'state/assign', 'moderation/act', 'content/hide' ), $ask ) );
        $this->assertSame( array( 'state/assign', 'moderation/act' ), $asked, 'stops at the first' );
        $this->assertNull( expReviewHoldAuto::exemptBy( array( 'state/assign' ), $ask ) );
        $this->assertNull( expReviewHoldAuto::exemptBy( array(), $ask ), 'nobody is exempt without the setting' );
        $this->assertNull( expReviewHoldAuto::exemptBy( array( 'noslash' ), $ask ) );
    }

    /** RA-4 */
    public function testReason()
    {
        $this->assertSame( 'Spam, check the link', expReviewHoldAuto::reason( "  Spam,\n\tcheck   the link \x07" ) );
        $this->assertSame( 500, mb_strlen( expReviewHoldAuto::reason( str_repeat( 'ä', 900 ) ), 'UTF-8' ) );
        $this->assertSame( '', expReviewHoldAuto::reason( " \n " ) );
    }

    /** RA-5 */
    public function testStepOfThePublishOperation()
    {
        $OperationList = array();
        include dirname( __DIR__, 4 ) . '/kernel/content/operation_definition.php';
        $names = array();
        foreach ( $OperationList['publish']['body'] as $step )
            $names[] = $step['name'];
        $auto = array_search( 'auto-review-hold', $names, true );
        $this->assertNotFalse( $auto );
        $this->assertGreaterThan( array_search( 'set-object-published', $names, true ), $auto );
        $this->assertLessThan( array_search( 'publish-object-extension-handler', $names, true ), $auto, 'before expwt\'s HideNew' );
        $commits = array_keys( $names, 'commit-transaction', true );
        $this->assertLessThan( end( $commits ), $auto );
        $this->assertLessThan( array_search( 'clear-object-view-cache', $names, true ), $auto );
        $this->assertLessThan( array_search( 'register-search-object', $names, true ), $auto );
        $step = $OperationList['publish']['body'][$auto];
        $this->assertSame( 'autoReviewHold', $step['method'] );
        $this->assertSame( array( 'object_id', 'version' ), array_column( $step['parameters'], 'name' ) );
        $this->assertTrue( method_exists( 'eZContentOperationCollection', 'autoReviewHold' ) );
    }

    /** RA-6 */
    public function testAuditEvents()
    {
        $catalogue = expAuditTaxonomy::catalogue();
        foreach ( array( 'content.object.hold.auto' => 'hold', 'content.object.hold.place' => 'hold', 'content.object.hold.release' => 'release',
                         'content.moderation.action' => 'change' ) as $name => $verb )
        {
            $this->assertArrayHasKey( $name, $catalogue );
            $this->assertSame( 'on', $catalogue[$name]['default'], $name );
            $this->assertSame( 'content', $catalogue[$name]['channel'], $name );
        }
        $doc = file_get_contents( dirname( __DIR__, 4 ) . '/doc/eng-us/bc/6.0/audit.md' );
        foreach ( array( 'content.object.hold.auto', 'content.object.hold.place', 'content.object.hold.release', 'content.moderation.action' ) as $name )
            $this->assertStringContainsString( '`' . $name . '`', $doc, "$name is in the audit catalogue's documentation" );
    }
}
