<?php
/**
 * expPhpSupport: supported PHP 8.0 to 8.5, PHP 7.4 usable without support, older refused; and the setup's PHP check.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expPhpSupportTest extends PHPUnit\Framework\TestCase
{
    public function testStatusPerVersion()
    {
        $this->assertSame( 'refused', expPhpSupport::status( '7.3.33' ) );
        $this->assertSame( 'unsupported', expPhpSupport::status( '7.4.0' ) );
        $this->assertSame( 'unsupported', expPhpSupport::status( '7.4.33' ) );
        $this->assertSame( 'supported', expPhpSupport::status( '8.0.0' ) );
        $this->assertSame( 'supported', expPhpSupport::status( '8.5.12' ) );
    }

    public function testTheNoticeOfPhp74SaysUnsupportedAndCarriesTheStandardSentence()
    {
        $notice = expPhpSupport::notice( '7.4.33' );
        $this->assertStringContainsString( 'PHP 7.4.33 is not supported', $notice );
        $this->assertStringContainsString( 'without support', $notice );
        $this->assertStringContainsString( 'Supported: PHP 8.0 to 8.5. Usable without support: PHP 7.4 (Exponential 6.0.15 and later).', $notice );
        $this->assertSame( '', expPhpSupport::notice( '8.0.30' ) );
        $this->assertStringContainsString( 'too old', expPhpSupport::notice( '7.3.0' ) );
    }

    public function testTheShippedSetupSettingsAgree()
    {
        $ini = file_get_contents( __DIR__ . '/../../../../settings/setup.ini' );
        $this->assertMatchesRegularExpression( '/^MinimumVersion=7\.4\.0$/m', $ini );
        $this->assertMatchesRegularExpression( '/^SupportedVersion=8\.0\.0$/m', $ini );
        $this->assertSame( '8.0.0', expPhpSupport::SUPPORTED_MINIMUM );
        $this->assertSame( '7.4.0', expPhpSupport::USABLE_MINIMUM );
    }
}
