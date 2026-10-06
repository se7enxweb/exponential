<?php
/**
 * Tests of eZCodeTemplate, which writes the blocks of a code template (.ctpl) into the PHP files that ask for them
 * with a "code-template::create-block:" comment: plain text, blocks for one parameter, blocks for several
 * parameters joined with & (all of them), indentation, removal of trailing whitespace,
 * replacing the code generated before, the check-only mode, the backup file and the errors that leave the file
 * untouched. The kernel's own generated code is checked against the shipped templates (on copies).
 *
 * Files are written under var/tmp and removed in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZCodeTemplateTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $backtrackLimit;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = 'var/tmp/phpunit-codetemplate-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0775, true );
        $this->backtrackLimit = ini_get( 'pcre.backtrack_limit' );
    }

    protected function tearDown(): void
    {
        ini_set( 'pcre.backtrack_limit', $this->backtrackLimit );
        if ( $this->dir && is_dir( $this->dir ) )
        {
            foreach ( scandir( $this->dir ) as $file )
            {
                if ( $file !== '.' && $file !== '..' )
                    unlink( $this->dir . '/' . $file );
            }
            rmdir( $this->dir );
        }
    }

    private function codeTemplate( $templateText, $name = 'k1' )
    {
        file_put_contents( "$this->dir/$name.ctpl", $templateText );
        $codeTemplate = new eZCodeTemplate();
        $codeTemplate->Templates = array( $name => array( 'filepath' => "$this->dir/$name.ctpl" ) );
        return $codeTemplate;
    }

    private function codeFile( $text, $name = 'code.php' )
    {
        file_put_contents( "$this->dir/$name", $text );
        return "$this->dir/$name";
    }

    private function generated( $filePath )
    {
        $text = file_get_contents( $filePath );
        $this->assertSame( 1, preg_match( '#DO NOT EDIT THIS CODE DIRECTLY, CHANGE THE TEMPLATE FILE INSTEAD\n\n(.*)\n[ \t]*// This code is automatically generated from#s', $text, $m ), $text );
        return $m[1];
    }

    public function testTheShippedTemplatesAreConfigured()
    {
        $codeTemplate = new eZCodeTemplate();
        $this->assertSame( 'templates/classcreatelist.ctpl', $codeTemplate->templateFile( 'can-instantiate-class-list' ) );
        $this->assertSame( 'templates/classlistfrompolicy.ctpl', $codeTemplate->templateFile( 'class-list-from-policy' ) );
        $this->assertFalse( $codeTemplate->templateFile( 'no-such-template' ) );
        $this->assertContains( 'kernel/classes/ezcontentclass.php', $codeTemplate->allCodeFiles() );
    }

    public function testTheShippedTemplatesApplyToTheKernelFilesWithoutErrors()
    {
        // The generated code in these files has since been edited by hand (the MongoDB paths), so it is not compared
        // with the template output: only that every block the files ask for exists and parses.
        $codeTemplate = new eZCodeTemplate();
        foreach ( $codeTemplate->allCodeFiles() as $i => $file )
        {
            $copy = $this->codeFile( file_get_contents( $file ), "kernel$i.php" );
            $this->assertNotSame( eZCodeTemplate::STATUS_FAILED, $codeTemplate->apply( $copy, true ), $file );
            $this->assertSame( file_get_contents( $file ), file_get_contents( $copy ), 'check only must not write' );
        }
    }

    public function testPlainTemplateIsInsertedWithMarkers()
    {
        $codeTemplate = $this->codeTemplate( "\$a = 1;\n\$b = 2;" );
        $file = $this->codeFile( "<?php\n// code-template::create-block: k1\n// after\n" );
        $this->assertSame( eZCodeTemplate::STATUS_OK, $codeTemplate->apply( $file ) );
        $text = file_get_contents( $file );
        $this->assertStringStartsWith( "<?php\n// code-template::create-block: k1\n// code-template::auto-generated:START k1\n", $text );
        $this->assertStringEndsWith( "// code-template::auto-generated:END k1\n// after\n", $text );
        $this->assertSame( "\$a = 1;\n\$b = 2;", $this->generated( $file ) );
        $this->assertFileExists( $file . eZSys::backupFilename() );
        $this->assertFileDoesNotExist( "$this->dir/#code.php#" );
    }

    public function testApplyingTwiceChangesNothingTheSecondTime()
    {
        $codeTemplate = $this->codeTemplate( "x();\n" );
        $file = $this->codeFile( "<?php\n    // code-template::create-block: k1\n}\n" );
        $this->assertSame( eZCodeTemplate::STATUS_OK, $codeTemplate->apply( $file ) );
        $first = file_get_contents( $file );
        $this->assertSame( eZCodeTemplate::STATUS_NO_CHANGE, $codeTemplate->apply( $file ) );
        $this->assertSame( $first, file_get_contents( $file ) );
    }

    public function testAChangedTemplateReplacesTheOldGeneratedCode()
    {
        $file = $this->codeFile( "<?php\n// code-template::create-block: k1\nend();\n" );
        $this->codeTemplate( "old();\n" )->apply( $file );
        $this->assertSame( eZCodeTemplate::STATUS_OK, $this->codeTemplate( "new();\n" )->apply( $file ) );
        $text = file_get_contents( $file );
        $this->assertStringNotContainsString( 'old();', $text );
        $this->assertSame( 1, substr_count( $text, 'auto-generated:START k1' ) );
        $this->assertStringEndsWith( "auto-generated:END k1\nend();\n", $text );
    }

    public function testCheckOnlyLeavesTheFileAlone()
    {
        $codeTemplate = $this->codeTemplate( "y();\n" );
        $file = $this->codeFile( "<?php\n// code-template::create-block: k1\n" );
        $before = file_get_contents( $file );
        $this->assertSame( eZCodeTemplate::STATUS_OK, $codeTemplate->apply( $file, true ) );
        $this->assertSame( $before, file_get_contents( $file ) );
        $this->assertFileDoesNotExist( $file . eZSys::backupFilename() );
        $this->assertFileDoesNotExist( "$this->dir/#code.php#" );
    }

    public function testTheGeneratedCommentsFollowTheIndentationOfTheCreateLine()
    {
        $file = $this->codeFile( "<?php\nclass A {\n\t    // code-template::create-block: k1\n}\n" );
        $this->codeTemplate( "z();\n" )->apply( $file );
        $this->assertStringContainsString( "\t    // code-template::auto-generated:START k1\n\t    // This code is automatically generated from", file_get_contents( $file ) );
        $this->assertStringContainsString( "\t    // code-template::auto-generated:END k1\n}\n", file_get_contents( $file ) );
    }

    public static function parameterBlockProvider()
    {
        $template = "always;\n" .
                    "/*START:code-template::one*/one;\n/*END:code-template::one*/" .
                    "/*START:code-template::one&two*/both;\n/*END:code-template::one&two*/" .
                    "<START:code-template::three>three;\n<END:code-template::three>" .
                    "last;";
        return array(
            'no parameters' => array( $template, '', "always;\nlast;" ),
            'one' => array( $template, ', one', "always;\none;\nlast;" ),
            'one and two' => array( $template, ', one, two', "always;\none;\nboth;\nlast;" ),
            'three' => array( $template, ',three', "always;\nthree;\nlast;" ),
            'unknown parameter' => array( $template, ', four', "always;\nlast;" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('parameterBlockProvider')]
    public function testParameterBlocks( $template, $parameters, $expected )
    {
        $file = $this->codeFile( "<?php\n// code-template::create-block: k1$parameters\n" );
        $this->assertSame( eZCodeTemplate::STATUS_OK, $this->codeTemplate( $template )->apply( $file ) );
        $this->assertSame( $expected, $this->generated( $file ) );
    }

    public function testTrailingWhitespaceIsRemovedUnlessAsked()
    {
        $template = "a();   \nb();\t\n";
        $file = $this->codeFile( "<?php\n// code-template::create-block: k1\n" );
        $this->codeTemplate( $template )->apply( $file );
        $this->assertSame( "a();\nb();\n", $this->generated( $file ) );

        $file = $this->codeFile( "<?php\n// code-template::create-block: k1, keep-whitespace\n", 'keep.php' );
        $this->codeTemplate( $template )->apply( $file );
        $this->assertSame( "a();   \nb();\t\n", $this->generated( $file ) );
    }

    public function testSeveralCreateBlocksInOneFile()
    {
        $codeTemplate = $this->codeTemplate( "/*START:code-template::a*/A;\n/*END:code-template::a*//*START:code-template::b*/B;\n/*END:code-template::b*/" );
        $file = $this->codeFile( "<?php\n// code-template::create-block: k1, a\nmiddle();\n// code-template::create-block: k1, b\n" );
        $this->assertSame( eZCodeTemplate::STATUS_OK, $codeTemplate->apply( $file ) );
        $text = file_get_contents( $file );
        $this->assertSame( 2, substr_count( $text, 'auto-generated:START k1' ) );
        $this->assertMatchesRegularExpression( "#create-block: k1, a\n.*\nA;\n.*middle\(\);\n// code-template::create-block: k1, b\n.*\nB;\n#s", $text );
    }

    public function testCreateLineAtTheEndOfTheFileWithoutNewline()
    {
        $file = $this->codeFile( "<?php\n// code-template::create-block: k1" );
        $this->assertSame( eZCodeTemplate::STATUS_OK, $this->codeTemplate( "q();" )->apply( $file ) );
        $this->assertSame( 'q();', $this->generated( $file ) );
    }

    public function testFileWithoutCreateBlocksIsUnchanged()
    {
        $file = $this->codeFile( "<?php\necho 1;\n" );
        $this->assertSame( eZCodeTemplate::STATUS_NO_CHANGE, $this->codeTemplate( "q();" )->apply( $file ) );
        $this->assertSame( "<?php\necho 1;\n", file_get_contents( $file ) );
    }

    public static function failureProvider()
    {
        return array(
            'unknown template name' => array( "<?php\n// code-template::create-block: other\n", "q();" ),
            'end before start' => array( "<?php\n// code-template::create-block: k1\n", "/*END:code-template::a*/q();" ),
            'end does not match' => array( "<?php\n// code-template::create-block: k1, a\n", "/*START:code-template::a*/q();/*END:code-template::b*/" ),
            'start inside start' => array( "<?php\n// code-template::create-block: k1, a\n", "/*START:code-template::a*//*START:code-template::b*/q();/*END:code-template::a*/" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('failureProvider')]
    public function testErrorsLeaveTheFileUntouched( $code, $template )
    {
        $file = $this->codeFile( $code );
        $this->assertSame( eZCodeTemplate::STATUS_FAILED, $this->codeTemplate( $template )->apply( $file ) );
        $this->assertSame( $code, file_get_contents( $file ) );
        $this->assertFileDoesNotExist( "$this->dir/#code.php#" );
    }

    public function testMissingTemplateFileOrCodeFileFails()
    {
        $codeTemplate = new eZCodeTemplate();
        $codeTemplate->Templates = array( 'k1' => array( 'filepath' => "$this->dir/missing.ctpl" ) );
        $file = $this->codeFile( "<?php\n// code-template::create-block: k1\n" );
        $this->assertSame( eZCodeTemplate::STATUS_FAILED, $codeTemplate->apply( $file ) );
        $this->assertSame( eZCodeTemplate::STATUS_FAILED, $codeTemplate->apply( "$this->dir/missing.php" ) );
    }
}
