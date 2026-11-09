<?php
/**
 * A member's own account never offers the "meta" attributes (kernel/classes/user/expuserownaccountattributes.php).
 * DB-less: the decision functions are pure; the wiring is read from the source files.
 *
 *  OAA-01  Only the "meta" category is left out, only on a user account, only for its own user or a visitor
 *  OAA-02  The owner check: the account's own user, or the anonymous user (signing up); never another signed-in user
 *  OAA-03  The edit flow takes them out right after the attributes are fetched, before validation, input and storing
 *  OAA-04  The standard sign-up form renders through user/register_attribute.tpl, which skips them too; admin and
 *          admin4 keep their accessible edit form, kept clean by OAA-03
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/user/expUserOwnAccountAttributesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */
class expUserOwnAccountAttributesTest extends PHPUnit\Framework\TestCase
{
    private $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 );
        require_once $this->root . '/kernel/classes/user/expuserownaccountattributes.php';
    }

    public function testOnlyTheMetaCategoryOfAnOwnAccountIsHidden()
    {
        $this->assertTrue( expUserOwnAccountAttributes::hidden( 'meta', true, true ) );
        $this->assertFalse( expUserOwnAccountAttributes::hidden( '', true, true ), 'the default category stays' );
        $this->assertFalse( expUserOwnAccountAttributes::hidden( 'content', true, true ) );
        $this->assertFalse( expUserOwnAccountAttributes::hidden( 'meta', false, true ), 'not a user account: editors edit meta' );
        $this->assertFalse( expUserOwnAccountAttributes::hidden( 'meta', true, false ), 'an editor on another person\'s account' );
    }

    public function testTheOwnerIsTheAccountItselfOrAVisitorSigningUp()
    {
        $this->assertTrue( expUserOwnAccountAttributes::byOwnerOrVisitor( 42, 42, 10 ), 'own account' );
        $this->assertTrue( expUserOwnAccountAttributes::byOwnerOrVisitor( 42, 10, 10 ), 'a visitor signing up' );
        $this->assertFalse( expUserOwnAccountAttributes::byOwnerOrVisitor( 42, 14, 10 ), 'another signed-in user (an editor)' );
        $this->assertTrue( expUserOwnAccountAttributes::byOwnerOrVisitor( '42', 42, '10' ), 'ids compared as numbers' );
    }

    public function testTheEditFlowFiltersBeforeAnythingUsesTheList()
    {
        $src = file_get_contents( $this->root . '/kernel/content/attribute_edit.php' );
        $filter = strpos( $src, '$contentObjectAttributes = expUserOwnAccountAttributes::filter( $contentObjectAttributes, $object );' );
        $this->assertNotFalse( $filter, 'attribute_edit.php filters the list' );
        foreach ( array( 'validateInput(', 'fetchInput(', 'storeInput(', "setVariable( 'content_attributes'" ) as $use )
        {
            $at = strpos( $src, $use );
            $this->assertNotFalse( $at, "$use is in attribute_edit.php" );
            $this->assertGreaterThan( $filter, $at, "the list is filtered before $use" );
        }
    }

    public function testTheKernelSignUpFormsSkipTheMetaCategory()
    {
        // the standard design's form renders its fields through the include, which skips the meta category itself
        $include = file_get_contents( $this->root . '/design/standard/templates/user/register_attribute.tpl' );
        $this->assertStringContainsString( "{if \$attribute.contentclass_attribute.category|ne( 'meta' )}", $include );
        $this->assertStringContainsString( "design:user/register_attribute.tpl",
            file_get_contents( $this->root . '/design/standard/templates/user/register.tpl' ) );
        // admin and admin4 keep their accessible edit form (content/edit_attribute.tpl); the edit flow's filter
        // (OAA-03) is what keeps the meta category out of it
        foreach ( array( 'admin', 'admin4' ) as $design )
            $this->assertStringContainsString( "design:content/edit_attribute.tpl",
                file_get_contents( $this->root . "/design/$design/templates/user/register.tpl" ), "$design register.tpl" );
    }
}
