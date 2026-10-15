<?php
/**
 * Where content/hide goes after its confirmation form: the page the form names (RedirectURI, fixed when the form
 * was shown), never the page last viewed in the session, which may be another tab's by then.
 *
 *  HR-01 - a local RedirectURI of a page is taken
 *  HR-02 - another host, a scheme, a protocol-relative address, content/hide itself or a disallowed view: the node
 *  HR-03 - a POST without RedirectURI (popup menu, Locations tab) keeps the old way (false here)
 *  HR-04 - the session's last page is not read for the confirmation's POST
 *  HR-05 - both confirmation pages of the view carry RedirectURI, and the redirect is made before the session's
 *
 * No database: the view's helpers are called with $_POST and $_SESSION set.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Content\Hide;

class expContentHideReturnTest extends PHPUnit\Framework\TestCase
{
    private $session;

    protected function setUp(): void
    {
        $this->session = isset( $_SESSION ) ? $_SESSION : null;
        $_SESSION = array();
        $_POST = array();
    }

    protected function tearDown(): void
    {
        $_POST = array();
        $_SESSION = $this->session;
    }

    private function posted( $uri )
    {
        $_POST = array( 'RedirectURI' => $uri );
        return Hide::postedReturnURI( 42 );
    }

    /** HR-01 */
    public function testALocalPageIsTaken()
    {
        $this->assertSame( '/content/view/full/2', $this->posted( '/content/view/full/2' ) );
        $this->assertSame( '/content/view/full/2/(offset)/10', $this->posted( '/content/view/full/2/(offset)/10' ) );
    }

    /** HR-02 */
    public function testOtherAddressesFallBackToTheNode()
    {
        foreach ( array( 'https://evil.example/x', '//evil.example/x', 'javascript:alert(1)', '/content/hide/42', '/user/logout', '', "/content/view/full/2\r\nLocation: /x" ) as $uri )
            $this->assertSame( '/content/view/full/42', $this->posted( $uri ), var_export( $uri, true ) );
    }

    /** HR-03 */
    public function testAPostWithoutRedirectURIKeepsTheOldWay()
    {
        $this->assertFalse( Hide::postedReturnURI( 42 ) );
    }

    /** HR-04 */
    public function testTheSessionsLastPageIsNotRead()
    {
        // a started session whose last page is one of another tab
        $started = new ReflectionProperty( 'eZSession', 'hasStarted' );
        $wasStarted = $started->getValue();
        $started->setValue( null, true );
        $_SESSION['LastAccessesURI'] = '/other-tab';
        try
        {
            $this->assertTrue( eZHTTPTool::instance()->hasSessionVariable( 'LastAccessesURI', false ), 'the session is read here' );
            $this->assertSame( '/other-tab', eZRedirectManager::returnURI( null, '/content/view/full/42' ), 'with the session the other tab would win' );
            $this->assertSame( '/content/view/full/2', $this->posted( '/content/view/full/2' ) );
            $this->assertSame( '/content/view/full/42', $this->posted( 'https://evil.example/x' ) );
        }
        finally
        {
            $started->setValue( null, $wasStarted );
        }
    }

    /** HR-05 */
    public function testTheViewUsesTheFormsAddress()
    {
        $code = (string)file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/views/content/hide.php' );
        // both confirmation pages: the content job's and the view's own (confirmationPage())
        $this->assertSame( 1, substr_count( $code, "'RedirectURI' => self::confirmationReturnURI( \$Module, (int) \$NodeID )" ), 'the content job page' );
        $this->assertSame( 1, substr_count( $code, "'RedirectURI' => self::confirmationReturnURI( \$module, \$nodeID )" ), 'the view\'s own page' );
        $posted = strpos( $code, '$postedReturn = self::postedReturnURI(' );
        $this->assertNotFalse( $posted );
        $this->assertLessThan( strpos( $code, '\eZRedirectManager::redirectTo( $Module, false )' ), $posted );
        foreach ( array( 'admin', 'admin4', 'standard' ) as $design )
            $this->assertStringContainsString( '{foreach $hidden as $name => $value}',
                                               (string)file_get_contents( dirname( __DIR__, 4 ) . "/design/$design/templates/content/job_confirm.tpl" ), $design );
    }
}
