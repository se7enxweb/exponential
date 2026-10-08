<?php
/**
 * eZSection::fetch() without an id (fetch( 'section', 'object', hash( 'section_id', $node.object.section_id ) ) on a
 * page that has no node) returns null without a query and without "Using null as an array offset is deprecated".
 *
 * No database: the guard returns before eZPersistentObject is reached.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZSectionFetchWithoutIdTest extends PHPUnit\Framework\TestCase
{
    public function testFetchWithoutIdReturnsNull()
    {
        foreach ( array( null, '', false ) as $id )
        {
            $deprecations = array();
            set_error_handler( function ( $no, $str ) use ( &$deprecations ) { $deprecations[] = $str; return true; }, E_DEPRECATED );
            try
            {
                $this->assertNull( eZSection::fetch( $id ), var_export( $id, true ) );
                $this->assertNull( eZSection::fetch( $id, false ), var_export( $id, true ) );
            }
            finally
            {
                restore_error_handler();
            }
            $this->assertSame( array(), $deprecations, var_export( $id, true ) );
        }
        $this->assertFalse( isset( $GLOBALS['eZContentSectionObjectCache'][''] ), 'nothing cached under an empty key' );
    }
}
