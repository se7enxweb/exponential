<?php
/**
 * File containing the eZContentBrowseEditReturnTargetTest class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\TestCase;

/**
 * Browse parameters left in the session by an old edit session point back to content/edit of a draft that
 * may be gone; kernel/content/browse.php recognises the return page through eZContentBrowse::editReturnTarget()
 * (admin4 review 15.10.2026: "Cancel" on a directly opened browse page ended on "not available").
 */
class eZContentBrowseEditReturnTargetTest extends TestCase
{
    public function testEditPageWithVersionAndLanguageNamesObjectAndVersion()
    {
        $this->assertSame( array( 7024, 2 ), eZContentBrowse::editReturnTarget( '/content/edit/7024/2/ger-DE' ) );
    }

    public function testEditPageWithoutVersionNamesOnlyTheObject()
    {
        $this->assertSame( array( 7024, false ), eZContentBrowse::editReturnTarget( '/content/edit/7024' ) );
        $this->assertSame( array( 7024, false ), eZContentBrowse::editReturnTarget( 'content/edit/7024/' ) );
    }

    public function testSiteaccessPrefixIsIgnored()
    {
        $this->assertSame( array( 12, 3 ), eZContentBrowse::editReturnTarget( '/site_admin/content/edit/12/3' ) );
    }

    public function testOtherPagesAreNoEditReturnTarget()
    {
        $this->assertFalse( eZContentBrowse::editReturnTarget( '/content/view/full/2' ) );
        $this->assertFalse( eZContentBrowse::editReturnTarget( '/role/assign/5' ) );
        $this->assertFalse( eZContentBrowse::editReturnTarget( '/content/edit/abc' ) );
        $this->assertFalse( eZContentBrowse::editReturnTarget( false ) );
        $this->assertFalse( eZContentBrowse::editReturnTarget( '' ) );
    }

    /**
     * The view's check reads only an edit page as a return page: parameters of any other browse (role assignment,
     * a node choice, none at all) are never dropped, and no object is fetched for them.
     */
    public function testBrowseViewKeepsParametersThatDoNotReturnToAnEditPage()
    {
        $this->assertFalse( \Exponential\View\Kernel\Content\Browse::returnsToMissingDraft( array( 'from_page' => '/role/assign/5' ) ) );
        $this->assertFalse( \Exponential\View\Kernel\Content\Browse::returnsToMissingDraft( array( 'from_page' => '/content/view/full/2' ) ) );
        $this->assertFalse( \Exponential\View\Kernel\Content\Browse::returnsToMissingDraft( array( 'action_name' => 'AddRelatedObject' ) ) );
        $this->assertFalse( \Exponential\View\Kernel\Content\Browse::returnsToMissingDraft( 'not an array' ) );
    }
}
