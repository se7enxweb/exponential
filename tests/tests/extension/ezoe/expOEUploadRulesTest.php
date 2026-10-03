<?php
/** The file types the embed dialog's upload accepts, and the check the upload view makes on the server. */

require_once __DIR__ . '/expOETestCase.php';

class expOEUploadRulesTest extends expOETestCase
{
    public static function allowed()
    {
        return array( array( 'photo.jpg' ), array( 'PHOTO.JPG' ), array( 'a.png' ), array( 'a.gif' ), array( 'doc.pdf' ), array( 'sheet.xlsx' ),
                      array( 'slides.odp' ), array( 'notes.txt' ), array( 'data.csv' ), array( 'pack.zip' ), array( 'song.mp3' ), array( 'clip.webm' ),
                      array( 'my.report.v2.pdf' ), array( 'with space.png' ), array( 'ünï.jpeg' ), array( 'a.b.c.docx' ) );
    }

    public static function refused()
    {
        return array( array( 'shell.php' ), array( 'shell.PHP' ), array( 'shell.php.jpg' ), array( 'shell.jpg.php' ), array( 'x.phtml' ), array( 'x.phar' ),
                      array( 'x.pht' ), array( 'run.sh' ), array( 'run.exe' ), array( 'x.cgi' ), array( 'x.asp' ), array( 'x.aspx' ), array( 'x.jsp' ),
                      array( 'x.pl' ), array( 'noextension' ), array( '' ), array( 'tool.xyz' ), array( 'archive.tar.gz' ), array( '.htaccess' ),
                      array( '../../x.exe' ), array( 'a\\b\\c.php' ), array( 'x.jpg.php5' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'allowed' )]
    public function testAllowedNamesPassWhenTheCheckIsOn( $name )
    {
        $this->ini( 'UploadExtensionCheck', 'always' );
        $this->assertTrue( expOEEditor::uploadExtensionAllowed( $name ), $name );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'refused' )]
    public function testRefusedNamesFailWhenTheCheckIsOn( $name )
    {
        $this->ini( 'UploadExtensionCheck', 'always' );
        $this->assertFalse( expOEEditor::uploadExtensionAllowed( $name ), $name );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'refused' )]
    public function testNothingIsRefusedWhenTheCheckIsDisabled( $name )
    {
        $this->ini( 'UploadExtensionCheck', 'disabled' );
        $this->assertTrue( expOEEditor::uploadExtensionAllowed( $name ), $name );
    }

    public function testTinyMCE3UsersAreNotLimitedInTheDefaultMode()
    {
        $this->ini( 'UploadExtensionCheck', 'engine' );
        $this->preference( 'tinymce3' );
        $this->assertTrue( expOEEditor::uploadExtensionAllowed( 'tool.xyz' ) );
    }

    public function testTinyMCE8UsersAreLimitedInTheDefaultMode()
    {
        $this->ini( 'UploadExtensionCheck', 'engine' );
        $this->preference( 'tinymce8' );
        $this->assertFalse( expOEEditor::uploadExtensionAllowed( 'tool.xyz' ) );
        $this->assertTrue( expOEEditor::uploadExtensionAllowed( 'a.png' ) );
    }

    public function testAnUnsetModeBehavesAsEngine()
    {
        $this->ini( 'UploadExtensionCheck', 'engine' );
        $this->preference( 'tinymce8' );
        $this->assertFalse( expOEEditor::uploadExtensionAllowed( 'shell.php' ) );
    }

    public function testTheAlwaysModeLimitsTinyMCE3Too()
    {
        $this->ini( 'UploadExtensionCheck', 'always' );
        $this->preference( 'tinymce3' );
        $this->assertFalse( expOEEditor::uploadExtensionAllowed( 'tool.xyz' ) );
    }

    public function testTheListComesFromTheIniLowerCasedWithoutDots()
    {
        $this->ini( 'UploadFileExtensions', array( 'PNG', '.Gif', ' pdf ', '', 'png' ) );
        $this->assertSame( array( 'png', 'gif', 'pdf' ), expOEEditor::uploadExtensions() );
    }

    public function testAnotherListChangesWhatIsAccepted()
    {
        $this->ini( 'UploadFileExtensions', array( 'png' ) );
        $this->ini( 'UploadExtensionCheck', 'always' );
        $this->assertTrue( expOEEditor::uploadExtensionAllowed( 'a.png' ) );
        $this->assertFalse( expOEEditor::uploadExtensionAllowed( 'a.pdf' ) );
    }

    public function testAnEmptyListAcceptsNothingWhenTheCheckIsOn()
    {
        $this->ini( 'UploadFileExtensions', array() );
        $this->ini( 'UploadExtensionCheck', 'always' );
        $this->assertFalse( expOEEditor::uploadExtensionAllowed( 'a.png' ) );
    }

    public function testTheShippedListHasNoExecutableType()
    {
        foreach ( array( 'php', 'phtml', 'phar', 'sh', 'exe', 'cgi', 'pl', 'asp', 'jsp', 'html', 'htm', 'js', 'svgz' ) as $bad )
            $this->assertNotContains( $bad, expOEEditor::uploadExtensions(), $bad );
    }

    public function testTheUploadViewCallsTheCheckBeforeTheKernelUpload()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/upload.php' );
        $check = strpos( $source, 'uploadExtensionAllowed' );
        $upload = strpos( $source, '$upload->handleUpload' );
        $this->assertNotFalse( $check );
        $this->assertLessThan( $upload, $check );
    }

    public function testTheUploadViewStillChecksThePolicyAndEditRights()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/upload.php' );
        $this->assertStringContainsString( "hasAccessTo( 'ezoe', 'relations' )", $source );
        $this->assertStringContainsString( 'canEdit()', $source );
    }

    public function testAnonymousHasNoAccessToTheEditorViews()
    {
        $this->loginAnonymous();
        $result = eZUser::currentUser()->hasAccessTo( 'ezoe', 'relations' );
        $this->assertSame( 'no', $result['accessWord'] );
    }
}
