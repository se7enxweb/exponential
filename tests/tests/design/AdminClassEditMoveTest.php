<?php
/**
 * The class edit page moves an attribute in place: the script must find the move buttons admin4's template draws
 * (<button name="MoveUp" value="<attribute id>"> in div.listbutton) and post the attribute id as the server reads it
 * (MoveUp=<id>, kernel/private/classes/views/class/edit.php). The script looked for input[name^=Move] with the id in
 * the name only, so on admin4 nothing was bound and every move submitted the whole form.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminClassEditMoveTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminClassEditMoveTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    public function testTheTemplateDrawsMoveButtonsWithTheAttributeIdAsValue()
    {
        $tpl = AdminFiles::read( self::root() . '/design/admin4/templates/class/edit.tpl' );
        $this->assertStringContainsString( '<div class="listbutton">', $tpl );
        foreach ( array( 'MoveTop', 'MoveUp', 'MoveDown', 'MoveBottom' ) as $name )
            $this->assertStringContainsString( '<button type="submit" class="a4-icon-btn" name="' . $name . '" value="{$Attributes.item.id}"', $tpl );
    }

    public function testTheScriptBindsThoseButtonsAndPostsTheirValue()
    {
        $js = AdminFiles::read( self::root() . '/design/admin4/javascript/page/class_edit.js' );
        $this->assertStringContainsString( "div.listbutton button[name^=Move]", $js );
        $this->assertStringContainsString( "var id = param.length > 1 ? param[1] : this.value;", $js );
        $this->assertStringContainsString( "data.append( action, id );", $js );
    }

    public function testTheServerReadsTheIdFromTheButtonsName()
    {
        $php = AdminFiles::read( self::root() . '/kernel/private/classes/views/class/edit.php' );
        foreach ( array( 'MoveUp', 'MoveDown' ) as $name )
            $this->assertStringContainsString( "\$http->postVariable( '$name' )", $php );
        $this->assertStringContainsString( "header( 'X-Class-Attribute-Moved: 1' );", $php );
    }
}
