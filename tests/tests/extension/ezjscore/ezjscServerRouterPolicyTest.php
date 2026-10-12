<?php
/**
 * ezjscnode behind a policy: [ezjscServer_<name>] RequirePolicy in ezjscServerRouter::getInstance().
 *
 * The shipped ezjscore.ini is loaded into the global ezjscore.ini instance in memory (no file and no
 * siteaccess changes), and the policy check is a stand-in (ezjscServerRouterPolicyTestRouter), so no
 * kernel, database or user is needed. Cases: the upgrade default (disabled) lets every call through
 * without asking the policy system; enabled asks it for ezjscore/call with the function list
 * ezjscnode, refuses without it (reported as 'policy-required', answered with 403) and allows with
 * it; a group without the setting keeps today's check and today's 200 answer.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerRouterPolicyTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit aa307a7ff3), Copyright (C) Mugo Web, GPL v2
 */

$ezjscoreRoot = dirname( __DIR__, 4 ) . '/extension/ezjscore';
require_once $ezjscoreRoot . '/classes/ezjscserverrouter.php';
require_once $ezjscoreRoot . '/classes/ezjscserverfunctions.php';
require_once $ezjscoreRoot . '/classes/ezjscserverfunctionsnode.php';
unset( $ezjscoreRoot );

/**
 * A router whose policy check answers from a fixed value and records what it was asked.
 */
class ezjscServerRouterPolicyTestRouter extends ezjscServerRouter
{
    /** @var bool what hasAccess() answers */
    public static $access = false;

    /** @var array the required functions of every hasAccess() call */
    public static $asked = array();

    public static function hasAccess( $requiredFunctions, $functionName = null )
    {
        self::$asked[] = $requiredFunctions;
        return self::$access;
    }
}

class ezjscServerRouterPolicyTest extends PHPUnit\Framework\TestCase
{
    /** @var eZINI */
    protected $ini;

    /** @var string the working directory before the test */
    protected $cwd;

    protected function setUp(): void
    {
        // eZINI reads paths relative to the installation
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $shipped = eZINI::fetchFromFile( 'extension/ezjscore/settings/ezjscore.ini' );
        $this->ini = eZINI::instance( 'ezjscore.ini' );
        $this->ini->setReadOnlySettingsCheck( false );
        $this->ini->setVariables( $shipped->groups() );
        ezjscServerRouterPolicyTestRouter::$access = false;
        ezjscServerRouterPolicyTestRouter::$asked = array();
    }

    protected function tearDown(): void
    {
        eZINI::resetInstance( 'ezjscore.ini' );
        chdir( $this->cwd );
    }

    /**
     * @param string $call e.g. ezjscnode::load::2
     * @param string|null $refusal
     * @return ezjscServerRouter|null
     */
    protected static function route( $call, &$refusal = null )
    {
        return ezjscServerRouterPolicyTestRouter::getInstance( explode( '::', $call ), true, true, $refusal );
    }

    public function testShippedDefaultIsDisabledAndDeclaresTheFunction()
    {
        $this->assertSame( 'disabled', $this->ini->variable( 'ezjscServer_ezjscnode', 'RequirePolicy' ) );
        $this->assertSame( array( 'ezjscnode' ), $this->ini->variable( 'ezjscServer_ezjscnode', 'Functions' ) );
        // the real hasAccess() refuses a function missing from FunctionList, and the role editor lists it from there
        $this->assertContains( 'ezjscnode', $this->ini->variable( 'ezjscServer', 'FunctionList' ) );
    }

    public static function nodeCalls()
    {
        return array(
            'load' => array( 'ezjscnode::load::2' ),
            'subtree' => array( 'ezjscnode::subtree::2' ),
            'updatepriority' => array( 'ezjscnode::updatepriority' ),
        );
    }

    /**
     * @dataProvider nodeCalls
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'nodeCalls' )]
    public function testDisabledLeavesTheCallOpenWithoutAskingThePolicySystem( $call )
    {
        $router = self::route( $call, $refusal );
        $this->assertInstanceOf( 'ezjscServerRouter', $router );
        $this->assertNull( $refusal );
        $this->assertSame( array(), ezjscServerRouterPolicyTestRouter::$asked );
    }

    /**
     * @dataProvider nodeCalls
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'nodeCalls' )]
    public function testEnabledRefusesWithoutThePolicy( $call )
    {
        $this->ini->setVariable( 'ezjscServer_ezjscnode', 'RequirePolicy', 'enabled' );
        $router = self::route( $call, $refusal );
        $this->assertNull( $router );
        $this->assertSame( 'policy-required', $refusal );
        $this->assertSame( array( array( 'ezjscnode' ) ), ezjscServerRouterPolicyTestRouter::$asked );
        $this->assertSame( 403, ezjscServerRouter::refusedStatus( array( $refusal ) ) );
    }

    /**
     * @dataProvider nodeCalls
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'nodeCalls' )]
    public function testEnabledAllowsWithThePolicy( $call )
    {
        $this->ini->setVariable( 'ezjscServer_ezjscnode', 'RequirePolicy', 'enabled' );
        ezjscServerRouterPolicyTestRouter::$access = true;
        $router = self::route( $call, $refusal );
        $this->assertInstanceOf( 'ezjscServerRouter', $router );
        $this->assertNull( $refusal );
        $this->assertSame( 'ezjscServerFunctionsNode::' . explode( '::', $call )[1], $router->getName() );
    }

    public function testGroupWithoutTheSettingKeepsTodaysCheckAndAnswer()
    {
        // ezjsctemplate has Functions[] and no RequirePolicy: checked as always, refused with a 200 answer
        $this->ini->setVariable( 'ezjscServer_ezjsctemplatetest', 'Class', 'ezjscServerFunctionsNode' );
        $this->ini->setVariable( 'ezjscServer_ezjsctemplatetest', 'Functions', array( 'ezjsctemplate' ) );
        $router = self::route( 'ezjsctemplatetest::load::2', $refusal );
        $this->assertNull( $router );
        $this->assertSame( 'access', $refusal );
        $this->assertSame( 200, ezjscServerRouter::refusedStatus( array( $refusal ) ) );

        ezjscServerRouterPolicyTestRouter::$access = true;
        $this->assertInstanceOf( 'ezjscServerRouter', self::route( 'ezjsctemplatetest::load::2' ) );
    }

    public function testUnknownGroupOrFunctionIsInvalidNotForbidden()
    {
        $this->assertNull( self::route( 'nosuchserver::load::2', $refusal ) );
        $this->assertSame( 'invalid', $refusal );
        $this->ini->setVariable( 'ezjscServer_ezjscnode', 'RequirePolicy', 'enabled' );
        $this->assertNull( self::route( 'ezjscnode::nosuchfunction', $refusal ) );
        $this->assertSame( 'invalid', $refusal );
    }

    public function testRefusedStatusIs403OnlyWhenEveryCallNeededTheMissingPolicy()
    {
        $this->assertSame( 200, ezjscServerRouter::refusedStatus( array() ) );
        $this->assertSame( 403, ezjscServerRouter::refusedStatus( array( 'policy-required', 'policy-required' ) ) );
        $this->assertSame( 200, ezjscServerRouter::refusedStatus( array( 'policy-required', null ) ) );
        $this->assertSame( 200, ezjscServerRouter::refusedStatus( array( 'policy-required', 'invalid' ) ) );
        $this->assertSame( 200, ezjscServerRouter::refusedStatus( array( 'access' ) ) );
    }
}
