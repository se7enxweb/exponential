<?php
/**
 * The installation's own requests carry the request shield's own-request signature (exp_request_shield
 * expRequestShield::selfHeader(), doc/how-it-works.md of the extension). No database.
 *
 *  OR-01  the preloader's request function (Setup > Preload sites, static publishing) adds the header when the
 *         extension is there
 *  OR-02  the upgrade's smoke check adds it to its request
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expOwnRequestsSignedForTheShieldTest extends PHPUnit\Framework\TestCase
{
    private static function source( $file )
    {
        return (string)file_get_contents( dirname( __DIR__, 4 ) . '/' . $file );
    }

    /** OR-01 */
    public function testThePreloaderSignsItsRequests()
    {
        $code = self::source( 'kernel/setup/exppreloadrunner.php' );
        $this->assertStringContainsString( "expRequestShield::selfHeader( \$url, \$headOnly ? 'HEAD' : 'GET' )", $code );
        $this->assertStringContainsString( "method_exists( 'expRequestShield', 'selfHeader' )", $code, 'an older extension without it: no header' );
    }

    /** OR-02 */
    public function testTheUpgradeSmokeCheckSignsItsRequest()
    {
        $code = self::source( 'kernel/classes/upgrade/steps/expupgradestepfinishsmoke.php' );
        $this->assertStringContainsString( 'expRequestShield::selfHeader( $url )', $code );
        $this->assertMatchesRegularExpression( '/User-Agent: exp-upgrade-smoke\\\\r\\\\n" \. \( \$own !== null/', $code );
    }
}
