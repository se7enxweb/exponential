<?php
/**
 * The REST administration (oauthadmin/list, view, edit, keys and the two confirmations) in the admin designs: the
 * same templates in design/admin and design/admin4, drawn with the shared components and the setup module's
 * stylesheet (.a4-setup-oauth) instead of the old private stylesheet with its own colours and no dark mode, and the
 * form fields, buttons and actions the module views read still in place.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminOauthadminPagesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminOauthadminPagesTest extends PHPUnit\Framework\TestCase
{
    const TEMPLATES = array( 'list.tpl', 'view.tpl', 'edit.tpl', 'delete_confirmation.tpl', 'keys.tpl',
                             'key_revoke_confirmation.tpl', 'parts/tabs.tpl' );

    private static function tpl( $design, $name )
    {
        return AdminFiles::read( dirname( __DIR__, 3 ) . "/design/$design/templates/oauthadmin/$name" );
    }

    public function testBothAdminDesignsHaveTheSameTemplates()
    {
        foreach ( self::TEMPLATES as $name )
            $this->assertSame( self::tpl( 'admin4', $name ), self::tpl( 'admin', $name ), "oauthadmin/$name differs between design/admin4 and design/admin" );
    }

    public function testPagesUseTheSharedComponentsAndNotTheOldStylesheet()
    {
        foreach ( self::TEMPLATES as $name )
        {
            $tpl = self::tpl( 'admin4', $name );
            $this->assertStringNotContainsString( 'oauthadmin/parts/style.tpl', $tpl, "$name still includes the old stylesheet" );
            $this->assertStringNotContainsString( 'oauthadmin.css', $tpl, "$name still links the old stylesheet" );
            $this->assertDoesNotMatchRegularExpression( '/class="[^"]*\bexp-(?!js-only)/', $tpl, "$name still uses a class of the old stylesheet" );
            $this->assertDoesNotMatchRegularExpression( '/<style\b|\sstyle="|\son[a-z]+="|<script\b/i', $tpl, "$name has inline CSS or script" );
            if ( $name !== 'parts/tabs.tpl' )
                $this->assertStringContainsString( 'class="context-block a4-page a4-setup-oauth"', $tpl, "$name is not an a4 page" );
        }
    }

    public function testTheModuleStylesheetDrawsWhatTheComponentsCannot()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $css = AdminFiles::read( dirname( __DIR__, 3 ) . "/design/$design/stylesheets/modules/setup.css" );
            foreach ( array( '.a4-setup-oauth-value', '.a4-setup-oauth-keys', '.a4-setup-oauth-select', '.a4-setup-oauth-all' ) as $rule )
                $this->assertStringContainsString( $rule, $css, "$design modules/setup.css has no $rule" );
        }
    }

    public function testTheFormsStillPostWhatTheViewsRead()
    {
        $list = self::tpl( 'admin4', 'list.tpl' );
        foreach ( array( 'name="DeleteIDArray[]"', 'name="DeleteApplicationListButton"', 'name="NewApplicationButton"', 'oauthadmin_list.js', 'id="oauth-select-all"' ) as $part )
            $this->assertStringContainsString( $part, $list );
        $edit = self::tpl( 'admin4', 'edit.tpl' );
        foreach ( array( 'name="Name"', 'name="Description"', 'name="EndPointURI"', 'name="StoreButton"', 'name="DiscardButton"' ) as $part )
            $this->assertStringContainsString( $part, $edit );
        $this->assertStringContainsString( 'name="ConfirmDelete"', self::tpl( 'admin4', 'delete_confirmation.tpl' ) );
        $keys = self::tpl( 'admin4', 'keys.tpl' );
        foreach ( array( 'name="RevokeKeyIDArray[]"', 'name="RevokeOneKeyButton"', 'name="RevokeKeyListButton"', 'name="RedirectURI"', 'name="q"', 'name="Status"' ) as $part )
            $this->assertStringContainsString( $part, $keys );
        $this->assertStringContainsString( 'name="ConfirmRevoke"', self::tpl( 'admin4', 'key_revoke_confirmation.tpl' ) );
        // the select-all script finds the boxes by the form, not by the old page scope
        $js = AdminFiles::read( dirname( __DIR__, 3 ) . '/design/standard/javascript/oauthadmin_list.js' );
        $this->assertStringContainsString( 'form[name="ApplicationList"] input[name="DeleteIDArray[]"]', $js );
    }
}
