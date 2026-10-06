<?php
/**
 * Where Cancel in user/edit goes, without the database: \Exponential\View\Kernel\User\Edit::cancelURI().
 *
 *  UC-01 - The page the form names in RedirectIfDiscarded
 *  UC-02 - Without one (or with an empty or invalid one) and without a page viewed before, the sitemap of the users,
 *          where Cancel always went
 *
 * The page viewed before (the session variable LastAccessesURI) is eZRedirectManager's; without a session the test
 * cannot have one.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZUserEditCancelTest extends PHPUnit\Framework\TestCase
{
    private $post;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->post = $_POST;
    }

    protected function tearDown(): void
    {
        $_POST = $this->post;
    }

    private function cancelURI()
    {
        return \Exponential\View\Kernel\User\Edit::cancelURI( null );
    }

    /** UC-01 */
    public function testTheFormNamesThePage()
    {
        $_POST['RedirectIfDiscarded'] = '/content/view/full/42';
        $this->assertSame( '/content/view/full/42', $this->cancelURI() );
        $_POST['RedirectIfDiscarded'] = ' /user/preferences ';
        $this->assertSame( '/user/preferences', $this->cancelURI() );
    }

    /** UC-02 */
    public function testWithoutAPageItIsTheSitemapOfTheUsers()
    {
        unset( $_POST['RedirectIfDiscarded'] );
        $this->assertSame( '/content/view/sitemap/5/', $this->cancelURI() );
        $_POST['RedirectIfDiscarded'] = '';
        $this->assertSame( '/content/view/sitemap/5/', $this->cancelURI() );
        $_POST['RedirectIfDiscarded'] = array( '/somewhere' );
        $this->assertSame( '/content/view/sitemap/5/', $this->cancelURI() );
    }
}
