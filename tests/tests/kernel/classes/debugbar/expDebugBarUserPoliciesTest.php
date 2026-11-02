<?php
/**
 * Who sees the debug bar's User tab and its policies, continuing expDebugBarPoliciesTest (UP-01 to UP-04):
 *   - UP-05 the tab: setup/setup, or an anonymous visitor with debug output; never a logged-in user without
 *           setup/setup, even with debug output (expDebugBarUser::mayView())
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarUserPoliciesTest extends PHPUnit\Framework\TestCase
{
    /** UP-05 */
    public function testWhoMaySeeTheTab()
    {
        $this->assertTrue( expDebugBarUser::mayView( true, true, false ), 'setup/setup, with or without debug output' );
        $this->assertTrue( expDebugBarUser::mayView( true, true, true ) );
        $this->assertTrue( expDebugBarUser::mayView( false, false, true ), 'an anonymous visitor with debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, false, false ), 'an anonymous visitor without debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, true, true ), 'a logged-in user without setup/setup, even with debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, true, false ) );
    }
}
