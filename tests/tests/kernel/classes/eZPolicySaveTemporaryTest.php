<?php
/**
 * eZPolicy::saveTemporary() refuses a policy that is not a temporary copy. original_id comes back
 * from the database as the string "0", which never equals the integer 0 with ===, so the guard did
 * not fire and the method went on to fetch policy "0" and call removeThis() on nothing.
 *
 * No database: the refusal happens before any query.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZPolicySaveTemporaryTest extends PHPUnit\Framework\TestCase
{
    public static function notTemporary()
    {
        return array(
            'as the database returns it' => array( '0' ),
            'as an integer'              => array( 0 ),
            'missing'                    => array( null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('notTemporary')]
    public function testSaveTemporaryRefusesAPolicyThatIsNotATemporaryCopy( $originalId )
    {
        $policy = new eZPolicy( array( 'id' => '41', 'role_id' => '1', 'module_name' => 'content',
                                       'function_name' => 'read', 'original_id' => $originalId ) );

        $this->expectException( Exception::class );
        $this->expectExceptionMessage( 'can only be used on a temporary policy' );
        $policy->saveTemporary();
    }
}
