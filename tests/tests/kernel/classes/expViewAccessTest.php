<?php
/**
 * expViewAccess (fetch user/can_open), the admin top tabs (eZTopMenuOperator) and the dashboard blocks
 * (content/dashboard: PolicyList[], ViewList[]): each shows a link only to a user who can open it.
 * Audit stage 1, "Dashboard defect" in doc/bc/6.0/audit.md.
 *
 * Live-database tests: the kernel is started once on the admin siteaccess against the installation's own
 * database (no test database, nothing written). The users are the admin user, the anonymous user and,
 * when the database has one, a signed-in user who may open the dashboard but not the Setup views
 * (looked up by policy, never by role name); the tests that need that user are skipped without one.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expViewAccessTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expViewAccessTest extends PHPUnit\Framework\TestCase
{
    /** @var eZScript|null */
    protected static $script = null;

    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

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
        $this->login( $this->admin() );
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            eZINI::resetInstance( 'dashboard.ini' );
            eZINI::resetInstance( 'menu.ini' );
            $this->login( $this->admin() );
        }
        parent::tearDown();
    }

    protected function login( eZUser $user )
    {
        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    protected function admin()
    {
        $admin = eZUser::fetchByName( 'admin' );
        $this->assertInstanceOf( 'eZUser', $admin, 'the admin user exists' );
        return $admin;
    }

    protected function anonymous()
    {
        $user = eZUser::fetch( eZUser::anonymousId() );
        $this->assertInstanceOf( 'eZUser', $user, 'the anonymous user exists' );
        return $user;
    }

    /**
     * An enabled, signed-in user who may open content/dashboard but not setup/cache, found by policy;
     * the test is skipped when the database has none.
     */
    protected function limitedUser()
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT contentobject_id FROM ezuser ORDER BY contentobject_id', array( 'limit' => 300 ) );
        $dashboard = eZModule::exists( 'content' );
        $setup = eZModule::exists( 'setup' );
        foreach ( $rows as $row )
        {
            $id = (int)$row['contentobject_id'];
            if ( $id == eZUser::anonymousId() )
                continue;
            $user = eZUser::fetch( $id );
            if ( !$user instanceof eZUser || !$user->isEnabled() || !$user->attribute( 'contentobject' ) )
                continue;
            $p = array();
            if ( $user->hasAccessToView( $dashboard, 'dashboard', $p ) && !$user->hasAccessToView( $setup, 'cache', $p ) )
            {
                $login = $user->hasAccessTo( 'user', 'login' );
                if ( $login['accessWord'] === 'yes' )
                    return $user;
            }
        }
        $this->markTestSkipped( 'no user here may open the dashboard without the Setup views' );
    }

    protected function check( $uri, $user = null )
    {
        return expViewAccess::check( $uri, $user );
    }

    public function testAdminOpensSetupDashboardAndContentRoot()
    {
        $root = eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
        foreach ( array( 'setup/cache', 'setup/info', 'content/dashboard', "content/view/full/$root", 'user/password' ) as $uri )
        {
            $c = $this->check( $uri );
            $this->assertTrue( $c['result'], "admin opens $uri ({$c['reason']})" );
        }
    }

    public function testAddressFormsAreNormalised()
    {
        foreach ( array( '/setup/cache', 'setup/cache/', 'setup/cache?x=1', 'setup/cache#top', '/setup/cache/?a=b#c' ) as $uri )
            $this->assertTrue( expViewAccess::canOpen( $uri ), "admin opens $uri" );
    }

    public function testExternalUnknownModuleAndUnknownViewAreNotOpened()
    {
        $this->assertSame( 'external', $this->check( 'https://example.com/setup/cache' )['reason'] );
        $this->assertSame( 'external', $this->check( '//example.com/setup/cache' )['reason'] );
        $this->assertSame( 'external', $this->check( 'mailto:someone@example.com' )['reason'] );
        $c = $this->check( 'nosuchmodule12345/view' );
        $this->assertFalse( $c['result'] );
        $this->assertSame( 'no-module', $c['reason'] );
        $c = $this->check( 'content/nosuchview12345' );
        $this->assertFalse( $c['result'] );
        $this->assertSame( 'no-view', $c['reason'] );
    }

    public function testMissingNodeAndObjectAreNotOpened()
    {
        $c = $this->check( 'content/view/full/999999999' );
        $this->assertFalse( $c['result'] );
        $this->assertSame( 'node', $c['reason'] );
        $c = $this->check( 'content/edit/999999999' );
        $this->assertFalse( $c['result'] );
        $this->assertSame( 'object', $c['reason'] );
    }

    public function testURLAliasIsTranslatedToItsNode()
    {
        $media = eZContentObjectTreeNode::fetch( eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' ) );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $media );
        $alias = $media->attribute( 'url_alias' );
        if ( $alias === '' || strpos( $alias, 'content/view' ) === 0 )
            $this->markTestSkipped( 'the media root has no URL alias here' );
        $c = $this->check( $alias );
        $this->assertTrue( $c['result'], "admin opens the alias $alias ({$c['reason']})" );
        $this->assertSame( 'content', $c['module'] );
        $this->assertSame( 'view', $c['view'] );
    }

    public function testUserEditAsksTheUsersOwnObject()
    {
        $c = $this->check( 'user/edit/(action)/edit' );
        $this->assertTrue( $c['result'], 'admin edits their own profile (' . $c['reason'] . ')' );
    }

    public function testAnonymousGetsTheSignInFormOnARequireUserLoginSiteAccess()
    {
        if ( eZINI::instance()->variable( 'SiteAccessSettings', 'RequireUserLogin' ) !== 'true' )
            $this->markTestSkipped( 'the admin siteaccess does not require a login here' );
        $anonymous = $this->anonymous();
        foreach ( array( 'content/dashboard', 'setup/cache', 'content/view/full/2' ) as $uri )
        {
            $c = $this->check( $uri, $anonymous );
            $this->assertFalse( $c['result'], "anonymous does not open $uri" );
            $this->assertSame( 'login', $c['reason'], "anonymous gets the sign-in form for $uri" );
        }
        $c = $this->check( 'user/login', $anonymous );
        $this->assertTrue( $c['result'], 'anonymous opens user/login (AnonymousAccessList)' );
    }

    public function testLimitedUserIsRefusedSetupByPolicyButOpensTheDashboard()
    {
        $user = $this->limitedUser();
        $this->login( $user );
        $this->assertTrue( expViewAccess::canOpen( 'content/dashboard' ), 'the limited user opens the dashboard' );
        foreach ( array( 'setup/cache', 'setup/info', 'setup/systemupgrade', 'setup/cachetoolbar' ) as $uri )
        {
            $c = $this->check( $uri );
            $this->assertFalse( $c['result'], "the limited user does not open $uri" );
            $this->assertSame( 'policy', $c['reason'], "$uri is refused by policy" );
        }
    }

    public function testAgreesWithTheKernelsViewCheckForEveryViewOfTheSetupModule()
    {
        $user = $this->limitedUser();
        foreach ( array( $this->admin(), $user ) as $u )
        {
            $this->login( $u );
            $module = eZModule::exists( 'setup' );
            foreach ( array_keys( $module->attribute( 'views' ) ) as $view )
            {
                $p = array();
                $kernel = $u->hasAccessToView( $module, $view, $p );
                $this->assertSame( $kernel, expViewAccess::canOpen( "setup/$view" ), 'setup/' . $view . ' for ' . $u->attribute( 'login' ) );
            }
        }
    }

    public function testFetchFunctionAnswersForTheCurrentUser()
    {
        $collection = new eZUserFunctionCollection();
        $this->assertSame( array( 'result' => true ), $collection->canOpen( 'setup/cache' ) );
        $this->login( $this->limitedUser() );
        $this->assertSame( array( 'result' => false ), $collection->canOpen( 'setup/cache' ) );
    }

    protected function tabs( $filter = true )
    {
        $op = new eZTopMenuOperator();
        $value = null;
        $op->modify( null, 'topmenu', array(), '', '', $value, array( 'context' => 'navigation', 'filter_on_access' => $filter ), null );
        $urls = array();
        foreach ( (array)$value as $tab )
            if ( isset( $tab['url'] ) )
                $urls[] = $tab['url'];
        return $urls;
    }

    public function testTopTabsLeaveOutWhatTheUserCannotOpen()
    {
        $adminTabs = $this->tabs();
        $this->assertContains( 'setup/cache', $adminTabs, 'admin has the Setup tab' );
        $this->login( $this->limitedUser() );
        $tabs = $this->tabs();
        $this->assertNotContains( 'setup/cache', $tabs, 'the limited user has no Setup tab' );
        foreach ( $tabs as $url )
            $this->assertTrue( expViewAccess::canOpen( $url ), "every tab shown opens: $url" );
    }

    public function testTopTabsViewCheckCanBeSwitchedOff()
    {
        $user = $this->limitedUser();
        $this->login( $user );
        $ini = eZINI::instance( 'menu.ini' );
        // a tab with no PolicyList whose view the user cannot open: shown only with the check off
        $ini->setVariable( 'TopAdminMenu', 'Tabs', array_merge( (array)$ini->variable( 'TopAdminMenu', 'Tabs' ), array( 'expviewaccesstest' ) ) );
        $ini->setVariable( 'Topmenu_expviewaccesstest', 'URL', array( 'default' => 'setup/info' ) );
        $ini->setVariable( 'Topmenu_expviewaccesstest', 'Enabled', array( 'default' => 'true' ) );
        $ini->setVariable( 'Topmenu_expviewaccesstest', 'Shown', array( 'default' => 'true' ) );
        $ini->setVariable( 'Topmenu_expviewaccesstest', 'NavigationPartIdentifier', 'ezsetupnavigationpart' );
        $ini->setVariable( 'Topmenu_expviewaccesstest', 'Name', 'Test' );
        $ini->setVariable( 'MenuAccessSettings', 'CheckViewAccess', 'enabled' );
        $this->assertNotContains( 'setup/info', $this->tabs(), 'with CheckViewAccess the tab is left out' );
        $ini->setVariable( 'MenuAccessSettings', 'CheckViewAccess', 'disabled' );
        $this->assertContains( 'setup/info', $this->tabs(), 'without it the tab is shown, as in 4.x' );
    }

    protected function blockIds( eZUser $user )
    {
        $this->login( $user );
        $ids = array();
        foreach ( \Exponential\View\Kernel\Content\Dashboard::visibleBlocks( eZINI::instance( 'dashboard.ini' ), $user ) as $block )
            $ids[] = $block['identifier'];
        return $ids;
    }

    public function testDashboardBlocksFollowPolicyListAndViewList()
    {
        $ini = eZINI::instance( 'dashboard.ini' );
        $ini->setVariable( 'DashboardSettings', 'DashboardBlocks', array( 'expviewaccess_open', 'expviewaccess_setup', 'expviewaccess_policy' ) );
        $ini->setVariable( 'DashboardBlock_expviewaccess_open', 'Priority', 10 );
        $ini->setVariable( 'DashboardBlock_expviewaccess_open', 'ViewList', array( 'content/dashboard' ) );
        $ini->setVariable( 'DashboardBlock_expviewaccess_setup', 'Priority', 20 );
        $ini->setVariable( 'DashboardBlock_expviewaccess_setup', 'ViewList', array( 'content/dashboard', 'setup/info' ) );
        $ini->setVariable( 'DashboardBlock_expviewaccess_policy', 'Priority', 5 );
        $ini->setVariable( 'DashboardBlock_expviewaccess_policy', 'PolicyList', array( 'setup/managecache' ) );

        $this->assertSame( array( 'expviewaccess_policy', 'expviewaccess_open', 'expviewaccess_setup' ), $this->blockIds( $this->admin() ),
                           'admin sees every block, in priority order' );
        $this->assertSame( array( 'expviewaccess_open' ), $this->blockIds( $this->limitedUser() ),
                           'the limited user sees only the block whose views open for them' );
    }

    public function testShippedDashboardBlocksAreAllShownToAdmin()
    {
        $ini = eZINI::instance( 'dashboard.ini' );
        $configured = array();
        foreach ( (array)$ini->variable( 'DashboardSettings', 'DashboardBlocks' ) as $id )
            if ( $ini->hasGroup( "DashboardBlock_$id" ) )
                $configured[] = $id;
        $shown = $this->blockIds( $this->admin() );
        sort( $configured );
        sort( $shown );
        $this->assertSame( $configured, $shown );
    }
}
