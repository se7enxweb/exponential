<?php
/**
 * eZWebDAVContentBackendAuth::authorize() allowed every read before it looked at a policy, and its
 * "second level" rule compared with an assignment. Reading now needs the same user/login policy (with the
 * siteaccess limit) as writing; only the root (/) stays open. A user marked "change password at next sign-in"
 * is refused at the sign-in, as with SOAP.
 *
 * No database: the policy answer of the current user is replaced.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expWebdavAuthorizeProbe extends eZWebDAVContentBackendAuth
{
    public $answer = array( 'accessWord' => 'no' );

    protected function loginAccess()
    {
        return $this->answer;
    }
}

class expWebdavAuthorizeTest extends PHPUnit\Framework\TestCase
{
    private function auth( array $answer )
    {
        $auth = new expWebdavAuthorizeProbe();
        $auth->answer = $answer;
        return $auth;
    }

    public function testRootIsOpenForReadingOnly()
    {
        $auth = $this->auth( array( 'accessWord' => 'no' ) );
        $this->assertTrue( $auth->authorize( 'u', '/', ezcWebdavAuthorizer::ACCESS_READ ) );
        $this->assertTrue( $auth->authorize( 'u', '', ezcWebdavAuthorizer::ACCESS_READ ) );
        $this->assertFalse( $auth->authorize( 'u', '/', ezcWebdavAuthorizer::ACCESS_WRITE ) );
    }

    public function testReadingBelowTheRootNeedsThePolicy()
    {
        $auth = $this->auth( array( 'accessWord' => 'no' ) );
        $this->assertFalse( $auth->authorize( 'u', '/site/content/a', ezcWebdavAuthorizer::ACCESS_READ ) );
        $this->assertFalse( $auth->authorize( 'u', '/site', ezcWebdavAuthorizer::ACCESS_READ ) );

        $auth = $this->auth( array( 'accessWord' => 'yes' ) );
        $this->assertTrue( $auth->authorize( 'u', '/site/content/a', ezcWebdavAuthorizer::ACCESS_READ ) );
    }

    public function testReadingHonoursTheSiteaccessLimit()
    {
        $auth = $this->auth( array(
            'accessWord' => 'limited',
            'policies' => array( array( 'SiteAccess' => array( eZSys::ezcrc32( 'allowed' ) ) ) ),
        ) );
        $this->assertTrue( $auth->authorize( 'u', '/allowed/content/a', ezcWebdavAuthorizer::ACCESS_READ ) );
        $this->assertFalse( $auth->authorize( 'u', '/other/content/a', ezcWebdavAuthorizer::ACCESS_READ ) );
    }

    public function testWritingToTheFirstLevelsIsRefusedWhateverThePolicy()
    {
        $auth = $this->auth( array( 'accessWord' => 'yes' ) );
        $this->assertFalse( $auth->authorize( 'u', '/site', ezcWebdavAuthorizer::ACCESS_WRITE ) );
        $this->assertFalse( $auth->authorize( 'u', '/site/content', ezcWebdavAuthorizer::ACCESS_WRITE ) );
        $this->assertTrue( $auth->authorize( 'u', '/site/content/a', ezcWebdavAuthorizer::ACCESS_WRITE ) );
        // reading the second level is not a write
        $this->assertTrue( $auth->authorize( 'u', '/site/content', ezcWebdavAuthorizer::ACCESS_READ ) );
    }

    public function testNoAssignmentToAccess()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/webdav/ezwebdavcontentbackendauth.php' );
        $this->assertDoesNotMatchRegularExpression( '/\$access\s*=[^=]/', preg_replace( '/function authorize\([^)]*\)/', '', $source ) );
    }

    public function testSignInOfAMarkedAccountIsRefused()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/webdav/ezwebdavcontentbackendauth.php' );
        $refusal = strpos( $source, "expPasswordChangeRequired::refusesSignIn( \$user, 'webdav' )" );
        $accepted = strpos( $source, 'appendLogEntry( "AuthenticatedBasic" )' );
        $this->assertNotFalse( $refusal );
        $this->assertLessThan( $accepted, $refusal, 'refused before the sign-in counts as done' );
    }
}
