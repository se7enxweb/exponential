<?php
/**
 * expVelocity::phpRefusal(): the Velocity engine (qbix) needs PHP 8.0, the kernel runs on PHP 7.4. On 7.4,
 * exp:velocity start refuses with a message instead of failing on code PHP 7.4 cannot run.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expVelocityPhpRefusalTest extends PHPUnit\Framework\TestCase
{
    public function testTheQbixEngineIsRefusedOnPhp74WithAMessage()
    {
        $refusal = expVelocity::phpRefusal( 'qbix', 70433 );
        $this->assertStringContainsString( 'needs PHP 8.0 or later, this is PHP 7.4', $refusal );
        $this->assertStringContainsString( '--engine=php', $refusal, 'the message names what does run' );
    }

    public function testTheQbixEngineRunsFromPhp80()
    {
        $this->assertSame( '', expVelocity::phpRefusal( 'qbix', 80000 ) );
        $this->assertSame( '', expVelocity::phpRefusal( 'qbix', 80500 ) );
    }

    public function testThePhpAndFrankenphpEnginesAreNeverRefused()
    {
        $this->assertSame( '', expVelocity::phpRefusal( 'php', 70400 ) );
        $this->assertSame( '', expVelocity::phpRefusal( 'frankenphp', 70400 ) );
    }

    public function testTheRunningPhpIsTheDefault()
    {
        $this->assertSame( PHP_VERSION_ID >= 80000 ? '' : expVelocity::phpRefusal( 'qbix', PHP_VERSION_ID ), expVelocity::phpRefusal( 'qbix' ) );
    }
}
