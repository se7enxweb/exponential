<?php
/**
 * The view mode of content/versionview, without the database: \Exponential\View\Kernel\Content\Versionview::viewMode()
 * and the (view_mode) parameter of the view.
 *
 *  VM-01 - Without the parameter the version is shown in full
 *  VM-02 - A view mode listed in content.ini [VersionView] ViewModes[] is used
 *  VM-03 - A view mode that is not listed is not available, whatever template it would name
 *  VM-04 - Without the setting only full is listed
 *  VM-05 - content/versionview takes (view_mode) as a parameter
 *  VM-06 - "Update view" takes the posted view mode when it is listed and keeps the current one otherwise
 *  VM-07 - Only a name of letters, digits, _ and - is a view mode, even when the setting lists something else;
 *          the list has no empty or double entries
 *  VM-08 - The admin, admin3 and admin4 previews send the view mode with "Update view", and design/standard has
 *          node/view/print.tpl
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZContentVersionviewViewModeTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
    }

    private function viewMode( $requested )
    {
        return \Exponential\View\Kernel\Content\Versionview::viewMode( $requested );
    }

    /** VM-01 */
    public function testWithoutTheParameterTheVersionIsShownInFull()
    {
        $this->assertSame( 'full', $this->viewMode( null ) );
        $this->assertSame( 'full', $this->viewMode( '' ) );
        $this->assertSame( 'full', $this->viewMode( false ) );
    }

    /** VM-02 */
    public function testAListedViewModeIsUsed()
    {
        ezpINIHelper::setINISetting( 'content.ini', 'VersionView', 'ViewModes', array( 'full', 'print' ) );
        $this->assertSame( 'print', $this->viewMode( 'print' ) );
        $this->assertSame( 'full', $this->viewMode( 'full' ) );
    }

    /** VM-03 */
    public function testAViewModeThatIsNotListedIsNotAvailable()
    {
        ezpINIHelper::setINISetting( 'content.ini', 'VersionView', 'ViewModes', array( 'full', 'print' ) );
        $this->assertNull( $this->viewMode( 'line' ) );
        $this->assertNull( $this->viewMode( 'Print' ) );
        $this->assertNull( $this->viewMode( '..' ) );
        $this->assertNull( $this->viewMode( 'print.tpl' ) );
    }

    /** VM-04 */
    public function testWithoutTheSettingOnlyFullIsListed()
    {
        $this->assertSame( array( 'full' ), eZINI::instance( 'content.ini' )->variable( 'VersionView', 'ViewModes' ), 'the shipped default' );
        ezpINIHelper::setINISetting( 'content.ini', 'VersionView', 'ViewModes', array( 'full' ) );
        $this->assertNull( $this->viewMode( 'print' ) );
    }

    /** VM-05 */
    public function testTheViewTakesTheViewModeParameter()
    {
        // Read from the source: loading the content module asks the database for the state groups
        $source = file_get_contents( 'kernel/content/module.php' );
        $this->assertSame( 1, preg_match( '/\$ViewList\[\'versionview\'\] = array\((.*?)\);/s', $source, $view ) );
        $this->assertStringContainsString( "'view_mode' => 'ViewMode'", $view[1] );
    }

    /** VM-06 */
    public function testUpdateViewTakesAListedViewModeOnly()
    {
        ezpINIHelper::setINISetting( 'content.ini', 'VersionView', 'ViewModes', array( 'full', 'print' ) );
        $versionview = '\Exponential\View\Kernel\Content\Versionview';
        $this->assertSame( 'print', $versionview::changedViewMode( 'print', 'full' ) );
        $this->assertSame( 'full', $versionview::changedViewMode( 'full', 'print' ) );
        $this->assertSame( 'print', $versionview::changedViewMode( 'line', 'print' ), 'not listed: the current one stays' );
        $this->assertSame( 'print', $versionview::changedViewMode( '', 'print' ) );
        $this->assertSame( 'print', $versionview::changedViewMode( null, 'print' ) );
        $this->assertSame( 'print', $versionview::changedViewMode( array( 'full' ), 'print' ) );

        $source = file_get_contents( 'kernel/content/module.php' );
        $this->assertSame( 1, preg_match( '/\$ViewList\[\'versionview\'\] = array\((.*?)\);/s', $source, $view ) );
        $this->assertStringContainsString( "'ViewMode' => 'SelectedViewMode'", $view[1] );
    }

    /** VM-07 */
    public function testOnlyANameIsAViewMode()
    {
        ezpINIHelper::setINISetting( 'content.ini', 'VersionView', 'ViewModes', array( 'full', '../full', 'print', 'print', '', 'pdf_layout' ) );
        $this->assertNull( $this->viewMode( '../full' ) );
        $this->assertNull( $this->viewMode( array( 'print' ) ) );
        $this->assertSame( 'pdf_layout', $this->viewMode( 'pdf_layout' ) );
        $this->assertSame( array( 'full', '../full', 'print', 'pdf_layout' ), \Exponential\View\Kernel\Content\Versionview::viewModes() );
    }

    /** VM-08 */
    public function testThePreviewsSendTheViewMode()
    {
        foreach ( array( 'admin', 'admin3', 'admin4' ) as $design )
        {
            $template = file_get_contents( "design/$design/templates/content/view/versionview.tpl" );
            $this->assertSame( 2, substr_count( $template, 'name="SelectedViewMode"' ), "$design: the choice and the hidden field" );
            $this->assertStringContainsString( "ezini( 'VersionView', 'ViewModes', 'content.ini' )", $template, $design );
            $this->assertStringContainsString( '$view_mode_uri', $template, $design );
        }
        $this->assertFileExists( 'design/standard/templates/node/view/print.tpl' );
    }
}
