<?php
/**
 * The Setup > Extensions planner (expExtensionChangePlan), the rows of its list (expExtensionCatalogue::rows()) and
 * the text edit of settings/override/site.ini.append.php (ezpActiveExtensions::replaceInText()): moving, activating
 * and deactivating, the diff, dependency warnings and risks, the order the kernel really loads in, and a file round
 * trip that keeps every other byte. No database, no disk.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../../../../../kernel/classes/expextensionchangeplan.php';
require_once __DIR__ . '/../../../../../kernel/classes/expextensioncatalogue.php';
require_once __DIR__ . '/../../../../../kernel/private/classes/ezpactiveextensions.php';

class ExtensionChangePlanTest extends PHPUnit\Framework\TestCase
{
    private $facts = array(
        'core'     => array(),
        'editor'   => array( 'requires' => array( 'core' ) ),
        'toolbar'  => array( 'requires' => array( 'core' ) ),
        'theme'    => array( 'requires' => array( 'core' ), 'extends' => array( 'toolbar' ), 'designs' => array( 'shop' ) ),
        'settings' => array( 'ini_appends' => array( 'db' ) ),
        'db'       => array( 'ini_bases' => array( 'db' ) ),
        'loner'    => array(),
    );

    private function plan( array $current, array $planned, array $context = array() )
    {
        return new expExtensionChangePlan( $current, $planned, $this->facts, $context + array( 'ordering' => false ) );
    }

    // ---- moving -----------------------------------------------------------------------------------------------------

    public function testMoveUpAndDownSwapWithTheNeighbour()
    {
        $list = array( 'a', 'b', 'c' );
        $this->assertSame( array( 'a', 'c', 'b' ), expExtensionChangePlan::moveUp( $list, 'c' ) );
        $this->assertSame( array( 'b', 'a', 'c' ), expExtensionChangePlan::moveDown( $list, 'a' ) );
    }

    public function testMovingPastTheEndsOrAnUnknownNameChangesNothing()
    {
        $list = array( 'a', 'b', 'c' );
        $this->assertSame( $list, expExtensionChangePlan::moveUp( $list, 'a' ) );
        $this->assertSame( $list, expExtensionChangePlan::moveDown( $list, 'c' ) );
        $this->assertSame( $list, expExtensionChangePlan::moveUp( $list, 'nope' ) );
    }

    public function testMoveToAndTheTopAndBottomActions()
    {
        $list = array( 'a', 'b', 'c', 'd' );
        $this->assertSame( array( 'c', 'a', 'b', 'd' ), expExtensionChangePlan::moveTo( $list, 'c', 1 ) );
        $this->assertSame( array( 'a', 'b', 'd', 'c' ), expExtensionChangePlan::moveTo( $list, 'c', 99 ) );
        $this->assertSame( array( 'b', 'a', 'c', 'd' ), expExtensionChangePlan::apply( $list, 'top', 'b' ) );
        $this->assertSame( array( 'a', 'c', 'd', 'b' ), expExtensionChangePlan::apply( $list, 'bottom', 'b' ) );
        $this->assertSame( $list, expExtensionChangePlan::apply( $list, 'explode', 'b' ) );
    }

    // ---- activating and deactivating --------------------------------------------------------------------------------

    public function testAnExtensionWithoutDependenciesIsActivatedAtTheEnd()
    {
        $this->assertSame( array( 'core', 'editor', 'loner' ), expExtensionChangePlan::activate( array( 'core', 'editor' ), 'loner', $this->facts ) );
    }

    public function testActivationHonoursRequiresAndExtends()
    {
        // theme requires core and extends toolbar: after core, before toolbar
        $this->assertSame( array( 'loner', 'core', 'theme', 'toolbar', 'editor' ),
            expExtensionChangePlan::activate( array( 'loner', 'core', 'toolbar', 'editor' ), 'theme', $this->facts ) );
    }

    public function testActivationGoesBeforeTheExtensionsThatRequireIt()
    {
        $this->assertSame( array( 'loner', 'core', 'editor' ), expExtensionChangePlan::activate( array( 'loner', 'editor' ), 'core', $this->facts ) );
    }

    public function testActivationGoesAfterAnExtensionThatExtendsIt()
    {
        $this->assertSame( array( 'theme', 'loner', 'toolbar' ),
            expExtensionChangePlan::activate( array( 'theme', 'loner' ), 'toolbar', array( 'theme' => array( 'extends' => array( 'toolbar' ) ) ) ) );
    }

    public function testContradictingBoundsPutItAtTheEnd()
    {
        // theme must come after core and before toolbar, but toolbar is written before core
        $this->assertSame( array( 'toolbar', 'core', 'theme' ), expExtensionChangePlan::activate( array( 'toolbar', 'core' ), 'theme', $this->facts ) );
    }

    public function testActivatingAnActiveOneOrDeactivatingAnInactiveOneChangesNothing()
    {
        $this->assertSame( array( 'core', 'editor' ), expExtensionChangePlan::activate( array( 'core', 'editor' ), 'core', $this->facts ) );
        $this->assertSame( array( 'core', 'editor' ), expExtensionChangePlan::deactivate( array( 'core', 'editor' ), 'loner' ) );
        $this->assertSame( array( 'editor' ), expExtensionChangePlan::deactivate( array( 'core', 'editor' ), 'core' ) );
    }

    public function testAPostedListKeepsOnlyKnownNamesOnce()
    {
        $this->assertSame( array( 'core', 'editor' ),
            expExtensionChangePlan::fromPost( array( 'core', '../etc', 'editor', 'core', array( 'x' ), 'nope' ), array( 'core', 'editor' ) ) );
        $this->assertSame( array(), expExtensionChangePlan::fromPost( 'core', array( 'core' ) ) );
    }

    // ---- the diff ---------------------------------------------------------------------------------------------------

    public function testUnchangedInputIsNoChange()
    {
        $plan = $this->plan( array( 'core', 'editor' ), array( 'core', 'editor', 'core', '' ) );
        $this->assertFalse( $plan->changed() );
        $this->assertSame( array( 'added' => array(), 'removed' => array(), 'moved' => array() ), $plan->diff() );
        $this->assertSame( array(), $plan->risks() );
        $this->assertSame( expExtensionChangePlan::fingerprint( array( 'core', 'editor' ) ), expExtensionChangePlan::fingerprint( $plan->planned ) );
    }

    public function testDiffNamesAddedRemovedAndOnlyTheMovedOne()
    {
        $plan = $this->plan( array( 'a', 'b', 'c', 'd', 'e' ), array( 'a', 'd', 'b', 'c', 'f' ) );
        $this->assertTrue( $plan->changed() );
        $this->assertSame( array( 'added' => array( 'f' ), 'removed' => array( 'e' ), 'moved' => array( 'd' ) ), $plan->diff() );
    }

    public function testLinesStartWithTheResetLine()
    {
        $this->assertSame( array( 'ActiveExtensions[]', 'ActiveExtensions[]=a', 'ActiveExtensions[]=b' ), expExtensionChangePlan::lines( array( 'a', 'b', 'a' ) ) );
    }

    // ---- problems and risks -----------------------------------------------------------------------------------------

    public function testAMissingRequirementIsAProblem()
    {
        $problems = $this->plan( array(), array( 'editor' ) )->problems();
        $this->assertSame( array( array( 'requires_missing', 'bad', array( 'other' => 'core' ) ) ), $problems['editor'] );
    }

    public function testARequirementInASiteaccessCountsAsPresent()
    {
        $problems = $this->plan( array(), array( 'editor' ), array( 'access' => array( 'core' ) ) )->problems();
        $this->assertArrayNotHasKey( 'editor', $problems );
    }

    public function testARequirementWrittenLaterWarnsUnlessTheKernelSortsIt()
    {
        $list = array( 'editor', 'core' );
        $this->assertSame( 'warn', $this->plan( $list, $list )->problems()['editor'][0][1] );
        $sorted = $this->plan( $list, $list, array( 'ordering' => true ) );
        $this->assertSame( 'info', $sorted->problems()['editor'][0][1] );
        $this->assertSame( array( 'core', 'editor' ), $sorted->effectiveOrder() );
    }

    public function testExtendingAnExtensionWrittenEarlierIsAProblem()
    {
        $problems = $this->plan( array(), array( 'core', 'toolbar', 'theme' ) )->problems();
        $this->assertSame( 'extends_earlier', $problems['theme'][0][0] );
    }

    public function testDeactivatingARequiredExtensionNeedsAcknowledgement()
    {
        $plan = $this->plan( array( 'core', 'editor' ), array( 'editor' ) );
        $codes = array_map( function ( $r ) { return $r[0]; }, $plan->risks() );
        $this->assertContains( 'removes_required', $codes );
        $this->assertContains( 'new_problem', $codes );
        $this->assertTrue( expExtensionChangePlan::needsAcknowledgement( $plan->risks() ) );
    }

    public function testDeactivatingTheOnlyProviderOfAUsedDesignIsRisky()
    {
        $context = array( 'design_users' => array( 'shop' => array( 'site', 'site_de' ) ) );
        $risks = $this->plan( array( 'core', 'theme' ), array( 'core' ), $context )->risks();
        $this->assertSame( array( 'removes_design', 'bad', array( 'name' => 'theme', 'design' => 'shop', 'siteaccesses' => 'site, site_de' ) ), $risks[0] );
        // a kernel design, or one nobody uses, is not
        $this->assertSame( array(), $this->plan( array( 'core', 'theme' ), array( 'core' ), array( 'core_designs' => array( 'shop' ) ) + $context )->risks() );
        $this->assertSame( array(), $this->plan( array( 'core', 'theme' ), array( 'core' ) )->risks() );
    }

    public function testDeactivatingTheFormTokenExtensionIsRisky()
    {
        $risks = $this->plan( array( 'ezformtoken' ), array() )->risks();
        $this->assertSame( 'removes_critical', $risks[0][0] );
        $this->assertTrue( expExtensionChangePlan::needsAcknowledgement( $risks ) );
    }

    public function testMovingASettingsExtensionAfterTheOneWhoseFileItChangesWarns()
    {
        // The alpha case: its settings extension must load before the database extension it configures.
        $plan = $this->plan( array( 'settings', 'db' ), array( 'db', 'settings' ) );
        $risks = $plan->risks();
        $this->assertSame( array( 'new_problem', 'warn', array( 'name' => 'settings', 'problem' => 'settings_lose', 'other' => 'db', 'file' => 'db.ini' ) ), $risks[0] );
        $this->assertFalse( expExtensionChangePlan::needsAcknowledgement( $risks ) );
        $this->assertSame( array(), $this->plan( array( 'db', 'settings' ), array( 'settings', 'db' ) )->risks() );
    }

    public function testAReorderTheKernelUndoesSaysSo()
    {
        $plan = $this->plan( array( 'core', 'editor' ), array( 'editor', 'core' ), array( 'ordering' => true ) );
        $this->assertSame( array( array( 'no_effect', 'info', array() ) ), $plan->risks() );
    }

    // ---- rows -------------------------------------------------------------------------------------------------------

    public function testRowsListActiveOnesInOrderThenTheOthersByName()
    {
        $catalogue = new expExtensionCatalogue();
        foreach ( array( 'zeta', 'core', 'editor', 'alpha' ) as $name )
            $catalogue->info[$name] = array( 'name' => $name === 'core' ? 'The Core' : $name, 'version' => '1.0', 'info_url' => 'javascript:alert(1)' );
        $catalogue->facts = $this->facts + array( 'zeta' => array( 'git' => true ), 'alpha' => array() );
        $catalogue->accessBySiteaccess = array( 'site' => array( 'alpha' ) );
        $catalogue->ordering = false;
        $plan = $catalogue->plan( array( 'core', 'editor' ), array( 'editor', 'core' ) );
        $rows = $catalogue->rows( $plan );
        $this->assertSame( array( 'editor', 'core', 'alpha', 'zeta' ), array_column( $rows, 'name' ) );
        $this->assertSame( array( 1, 2, 0, 0 ), array_column( $rows, 'position' ) );
        $this->assertSame( 'The Core', $rows[1]['title'] );
        $this->assertSame( '', $rows[0]['title'] );
        $this->assertSame( '', $rows[1]['info_url'] );
        $this->assertSame( array( 'site' ), $rows[2]['access'] );
        $this->assertTrue( $rows[3]['git'] );
        $this->assertSame( 'warn', $rows[0]['problem_level'] );
        $this->assertSame( array( 'editor' ), $rows[1]['dependents'] );
        $this->assertContains( $rows[0]['state'], array( 'moved', '' ) );
        $summary = expExtensionCatalogue::summary( $rows );
        $this->assertSame( array( 'total' => 4, 'active' => 2, 'access' => 1, 'inactive' => 1, 'git' => 1, 'problems' => 1 ), $summary );
        $this->assertSame( array( 'alpha', 'core', 'editor', 'zeta' ), array_column( $catalogue->rows( $plan, 'name' ), 'name' ) );
    }

    public function testDependenciesAreReadFromExtensionXml()
    {
        $xml = '<?xml version="1.0"?><extension name="x"><dependencies><requires><extension name="core"/></requires>'
             . '<uses><extension name="find"/></uses><extends><extension name="toolbar"/><extension name="web"/></extends></dependencies></extension>';
        $this->assertSame( array( 'requires' => array( 'core' ), 'uses' => array( 'find' ), 'extends' => array( 'toolbar', 'web' ) ),
            expExtensionCatalogue::dependenciesFromXml( $xml ) );
        $this->assertSame( array( 'requires' => array(), 'uses' => array(), 'extends' => array() ), expExtensionCatalogue::dependenciesFromXml( '<broken' ) );
    }

    // ---- the file ---------------------------------------------------------------------------------------------------

    private $file = "<?php /* #?ini charset=\"utf-8\"?\n\n[DatabaseSettings]\nServer=db\nPassword=s3cret # not an extension\n\n[ExtensionSettings]\nActiveExtensions[]\n# listed first so its settings win\nActiveExtensions[]=settings\nActiveExtensions[]=core\nActiveExtensions[]=editor\nExtensionOrdering=enabled\n\n[SiteSettings]\nSiteName=Test\n# ActiveExtensions[]=not_me\n*/ ?>";

    public function testTheListIsReadFromTheFileText()
    {
        $this->assertSame( array( 'settings', 'core', 'editor' ), ezpActiveExtensions::fromText( $this->file ) );
    }

    public function testTheSameListGivesTheSameBytes()
    {
        $this->assertSame( $this->file, ezpActiveExtensions::replaceInText( $this->file, array( 'settings', 'core', 'editor' ) ) );
    }

    public function testOnlyTheListChangesAndTheCommentMovesWithItsExtension()
    {
        $new = ezpActiveExtensions::replaceInText( $this->file, array( 'core', 'settings', 'loner' ) );
        $this->assertSame( array( 'core', 'settings', 'loner' ), ezpActiveExtensions::fromText( $new ) );
        $this->assertStringContainsString( "[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=core\n# listed first so its settings win\nActiveExtensions[]=settings\nActiveExtensions[]=loner\nExtensionOrdering=enabled\n", $new );
        // everything outside the list as it was
        $this->assertSame( strstr( $this->file, '[ExtensionSettings]', true ), strstr( $new, '[ExtensionSettings]', true ) );
        $this->assertSame( strstr( $this->file, 'ExtensionOrdering' ), strstr( $new, 'ExtensionOrdering' ) );
        $this->assertStringStartsWith( '<?php /*', $new );
        $this->assertStringEndsWith( '*/ ?>', $new );
    }

    public function testARoundTripGivesBackTheFileByteForByte()
    {
        $changed = ezpActiveExtensions::replaceInText( $this->file, array( 'editor', 'core', 'settings' ) );
        $this->assertNotSame( $this->file, $changed );
        $this->assertSame( $this->file, ezpActiveExtensions::replaceInText( $changed, array( 'settings', 'core', 'editor' ) ) );
    }

    public function testTheCommentOfADeactivatedExtensionIsKept()
    {
        $changed = ezpActiveExtensions::replaceInText( $this->file, array( 'editor', 'loner', 'core' ) );
        $this->assertSame( array( 'editor', 'loner', 'core' ), ezpActiveExtensions::fromText( $changed ) );
        $this->assertStringContainsString( "ActiveExtensions[]=core\n# listed first so its settings win\nExtensionOrdering=enabled", $changed );
        $this->assertSame( str_replace( "\n", '', strstr( $this->file, '[SiteSettings]' ) ), str_replace( "\n", '', strstr( $changed, '[SiteSettings]' ) ) );
    }

    public function testWindowsLineEndingsAreKept()
    {
        $crlf = str_replace( "\n", "\r\n", $this->file );
        $new = ezpActiveExtensions::replaceInText( $crlf, array( 'settings', 'core', 'editor', 'loner' ) );
        $this->assertStringContainsString( "ActiveExtensions[]=editor\r\nActiveExtensions[]=loner\r\nExtensionOrdering", $new );
        $this->assertSame( 0, preg_match( "/[^\r]\n/", $new ) );
    }

    public function testAFileWithoutTheGroupGetsOneInsideTheWrapper()
    {
        $file = "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=Test\n\n*/ ?>\n";
        $new = ezpActiveExtensions::replaceInText( $file, array( 'core' ) );
        $this->assertSame( "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=Test\n\n[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=core\n*/ ?>\n", $new );
        $this->assertSame( array( 'core' ), ezpActiveExtensions::fromText( $new ) );
    }

    public function testAGroupWithoutTheListGetsItRightAfterTheHeader()
    {
        $file = "[ExtensionSettings]\nExtensionOrdering=enabled\n";
        $this->assertSame( "[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=core\nExtensionOrdering=enabled\n",
            ezpActiveExtensions::replaceInText( $file, array( 'core' ) ) );
    }

    public function testAnEmptyListLeavesTheResetLine()
    {
        $new = ezpActiveExtensions::replaceInText( $this->file, array() );
        $this->assertSame( array(), ezpActiveExtensions::fromText( $new ) );
        $this->assertStringContainsString( "[ExtensionSettings]\nActiveExtensions[]\n# listed first so its settings win\nExtensionOrdering=enabled", $new );
    }
}
