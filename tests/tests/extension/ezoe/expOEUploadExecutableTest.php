<?php
/**
 * The executable types the upload of the online editor refuses: expOEEditor::uploadExtensionAllowed().
 *
 *  UX-01 - a name with an executable type anywhere in it is refused in every UploadExtensionCheck mode, "disabled"
 *          included, before the engine of the user is looked at (TinyMCE 3 users skip only the list of types)
 *  UX-02 - with the check disabled, every other name is still accepted
 *
 * Only ezoe.ini is changed, in memory, and put back. No database: the executable check comes before the engine.
 * The engine rules with a user are in expOEUploadRulesTest.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/expOEUploadExecutableTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOEUploadExecutableTest extends PHPUnit\Framework\TestCase
{
    private $savedCheck = null;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( !class_exists( 'expOEEditor' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
        $ini = eZINI::instance( 'ezoe.ini' );
        $this->savedCheck = $ini->hasVariable( 'EditorSettings', 'UploadExtensionCheck' ) ? array( $ini->variable( 'EditorSettings', 'UploadExtensionCheck' ) ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        if ( $this->savedCheck === null )
            $ini->removeSetting( 'EditorSettings', 'UploadExtensionCheck' );
        else
            $ini->setVariable( 'EditorSettings', 'UploadExtensionCheck', $this->savedCheck[0] );
    }

    public static function executables()
    {
        $names = array( 'x.php', 'x.PHP', 'shell.php.jpg', 'x.phtml', 'x.phar', 'x.pht', 'x.php5', 'x.php8', 'run.sh', 'run.exe', 'x.cgi',
                        'x.pl', 'x.asp', 'x.aspx', 'x.jsp', '.htaccess', '../../x.php', 'a\\b\\c.php',
                        'x.php.', 'x.php ', "x.php\t.jpg", 'X.PhTmL', 'x.phtm', 'x.php6', 'x.shtml' );
        $cases = array();
        foreach ( array( 'engine', 'always', 'disabled' ) as $mode )
            foreach ( $names as $name )
                $cases["$mode $name"] = array( $mode, $name );
        return $cases;
    }

    /** UX-01 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'executables' )]
    public function testAnExecutableTypeIsRefusedInEveryMode( $mode, $name )
    {
        eZINI::instance( 'ezoe.ini' )->setVariable( 'EditorSettings', 'UploadExtensionCheck', $mode );
        $this->assertFalse( expOEEditor::uploadExtensionAllowed( $name ), "$mode: $name" );
    }

    /** UX-02 */
    public function testTheDisabledCheckAcceptsEveryOtherName()
    {
        eZINI::instance( 'ezoe.ini' )->setVariable( 'EditorSettings', 'UploadExtensionCheck', 'disabled' );
        foreach ( array( 'tool.xyz', 'noextension', 'a.png', 'php.png', 'archive.tar.gz' ) as $name )
            $this->assertTrue( expOEEditor::uploadExtensionAllowed( $name ), $name );
    }
}
