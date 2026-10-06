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
}
