<?php
/**
 * The datatype and template operator wizards (setup/datatype, setup/templateoperator) are one page each since they
 * write an extension: the views fetch setup/datatype.tpl and setup/templateoperator.tpl and nothing else. The step
 * templates of the old three step wizards (setup/datatype_basic, _describe, _code and the same for templateoperator)
 * are gone from every design, and no view, template or script asks for them or for their step and restart buttons
 * (DatatypeStepButton, DatatypeRestartButton, TemplateOperatorStepButton, TemplateOperatorRestartButton).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSetupWizardTemplatesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSetupWizardTemplatesTest extends PHPUnit\Framework\TestCase
{
    const STEPS = array( 'datatype_basic', 'datatype_describe', 'datatype_code',
                         'templateoperator_basic', 'templateoperator_describe', 'templateoperator_code' );
    const BUTTONS = array( 'DatatypeStepButton', 'DatatypeRestartButton', 'TemplateOperatorStepButton', 'TemplateOperatorRestartButton' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    public function testTheViewsFetchOneTemplateEach()
    {
        foreach ( array( 'datatype', 'templateoperator' ) as $view )
        {
            $path = self::root() . "/kernel/private/classes/views/setup/$view.php";
            $this->assertFileExists( $path );
            $code = (string)file_get_contents( $path );
            preg_match_all( "#fetch\\( 'design:([^']+)' \\)#", $code, $m );
            $this->assertSame( array( "setup/$view.tpl" ), $m[1], $view );
            foreach ( self::BUTTONS as $button )
                $this->assertStringNotContainsString( $button, $code, "$view handles no $button" );
        }
    }

    public function testTheOldStepTemplatesAreGone()
    {
        foreach ( glob( self::root() . '/design/*/templates/setup', GLOB_ONLYDIR ) as $dir )
        {
            foreach ( self::STEPS as $step )
                $this->assertFileDoesNotExist( "$dir/$step.tpl" );
        }
        // the wizards themselves are still there
        foreach ( array( 'admin', 'admin4', 'standard' ) as $design )
        {
            $this->assertFileExists( self::root() . "/design/$design/templates/setup/datatype.tpl" );
            $this->assertFileExists( self::root() . "/design/$design/templates/setup/templateoperator.tpl" );
        }
    }

    public function testNothingAsksForTheStepTemplatesOrTheirButtons()
    {
        $pattern = '#\b(?:' . implode( '|', self::STEPS ) . ')\.tpl\b|\b(?:' . implode( '|', self::BUTTONS ) . ')\b#';
        $found = array();
        foreach ( array( 'design', 'kernel', 'settings' ) as $top )
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . "/$top", FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                if ( !preg_match( '#\.(?:tpl|php|ini|js)$#', $file->getFilename() ) )
                    continue;
                if ( preg_match( $pattern, (string)file_get_contents( $file->getPathname() ) ) )
                    $found[] = substr( $file->getPathname(), strlen( self::root() ) + 1 );
            }
        }
        $this->assertSame( array(), $found );
    }
}
