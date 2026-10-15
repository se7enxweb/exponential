<?php
/**
 * The quick select of the current node on content/browse (content/browse_current_node.tpl) is pre-selected only
 * where one item can be chosen. With check boxes (Add locations, related objects ...) a pre-checked current node
 * was sent along with the items the user checked, and the object got a location there too.
 *
 *  BC-01 - every copy pre-checks the current node only for radio buttons
 *  BC-02 - the browse page chooses check boxes by default and radio buttons for a single selection
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminBrowseCurrentNodeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminBrowseCurrentNodeTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** BC-01 */
    public function testTheCurrentNodeIsPreCheckedOnlyForRadioButtons()
    {
        foreach ( array( 'admin3', 'admin4' ) as $design )
        {
            $path = "design/$design/templates/content/browse_current_node.tpl";
            $tpl = self::file( $path );
            $inputs = preg_match_all( '/<input type="\{\$select_type\}" name="\{\$select_name\}\[\]"[^\n]*\/>/', $tpl, $m );
            $this->assertSame( 2, $inputs, $path . ': the list and the thumbnail input' );
            foreach ( $m[0] as $input )
                $this->assertStringContainsString( '{if eq( $select_type, \'radio\' )} checked="checked"{/if}', $input, $path );
            // no checked attribute outside that condition
            $this->assertSame( 2, substr_count( $tpl, 'checked="checked"' ), $path );
        }
    }

    /** BC-02 */
    public function testCheckBoxesAreTheDefaultAndRadioButtonsTheSingleSelection()
    {
        $tpl = self::file( 'design/admin4/templates/content/browse.tpl' );
        $this->assertStringContainsString( "select_type='checkbox'", $tpl );
        $this->assertStringContainsString( "{set select_type='radio'}", $tpl );
    }
}
