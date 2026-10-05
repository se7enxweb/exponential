<?php
/**
 * A kickstarter step that stops without an error of its own says why on the command line, instead of
 * "Unknown failure": a missing kickstart.ini section, Continue other than true, a language without a locale.
 * Stand-in step objects; no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class KickstarterFailureReasonTest extends PHPUnit\Framework\TestCase
{
    private static function step( $identifier, $data, $languageErrors = null )
    {
        $step = new stdClass();
        $step->Identifier = $identifier;
        $step->KickstartData = $data;
        if ( $languageErrors !== null )
            $step->LanguageErrors = $languageErrors;
        return $step;
    }

    public function testMissingSection()
    {
        $reasons = expKickstarter::failureReasons( self::step( 'site_admin', false ) );
        $this->assertStringContainsString( 'no [site_admin] section', $reasons[0] );
    }

    public function testContinueFalse()
    {
        $reasons = expKickstarter::failureReasons( self::step( 'site_access', array( 'Continue' => 'false', 'Access' => 'url' ) ) );
        $this->assertStringContainsString( '[site_access] has Continue=false', $reasons[0] );
        $reasons = expKickstarter::failureReasons( self::step( 'site_access', array( 'Access' => 'url' ) ) );
        $this->assertStringContainsString( 'no Continue=', $reasons[0] );
    }

    public function testInvalidLanguage()
    {
        $reasons = expKickstarter::failureReasons( self::step( 'language_options', array( 'Continue' => 'true', 'Primary' => 'xxx-XX' ),
                                                               array( 'No locale for xxx-XX' ) ) );
        $this->assertSame( array( 'kickstart.ini [language_options]: No locale for xxx-XX' ), $reasons );
    }

    public function testUnknownStaysTheLastResort()
    {
        $this->assertSame( array( 'Unknown failure' ), expKickstarter::failureReasons( self::step( 'security', array( 'Continue' => 'true' ) ) ) );
    }
}
