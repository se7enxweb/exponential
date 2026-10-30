<?php
/**
 * Texts the admin templates use are current messages of the German catalogue, not obsolete ones. The translator
 * reads an obsolete message like any other, so the page is German either way; but Qt Linguist hides obsolete
 * messages, and an update of the catalogue that drops them (lupdate -no-obsolete) would leave these texts English.
 * Each case names the template that uses the text in that context.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminUsedTranslationsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminUsedTranslationsTest extends PHPUnit\Framework\TestCase
{
    public static function textProvider()
    {
        return array(
            'extensions: Active'        => array( 'design/admin4/templates/setup/extensions.tpl', 'design/admin/setup/extensions', 'Active' ),
            'extensions: Activate'      => array( 'design/admin4/templates/setup/extensions.tpl', 'design/admin/setup/extensions', 'Activate' ),
            'extensions: Apply changes' => array( 'design/admin4/templates/setup/extensions.tpl', 'design/admin/setup/extensions', 'Apply changes' ),
            'session: Sessions'         => array( 'design/admin4/templates/setup/session.tpl', 'design/admin/setup/session', 'Sessions' ),
            'package list: Repository'  => array( 'design/admin4/templates/package/list.tpl', 'design/admin/package/list', 'Repository' ),
            'edit conflict: Rejected'   => array( 'design/admin4/templates/content/edit_conflict.tpl', 'design/admin/content/versions', 'Rejected' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'textProvider' )]
    public function testAUsedTextIsACurrentGermanMessage( $template, $context, $source )
    {
        $root = dirname( __DIR__, 3 );
        $this->assertStringContainsString( "'$source'|i18n( '$context' )", (string)file_get_contents( "$root/$template" ), "$template uses it" );

        $ts = new DOMDocument();
        $this->assertTrue( $ts->load( "$root/share/translations/ger-DE/translation.ts" ) );
        $found = ( new DOMXPath( $ts ) )->query( "//context[name='$context']/message[source='$source']/translation" );
        $this->assertSame( 1, $found->length, "$context: $source" );
        $this->assertNotSame( '', trim( $found->item( 0 )->textContent ) );
        $this->assertNotSame( 'obsolete', $found->item( 0 )->getAttribute( 'type' ), "$context: $source is marked obsolete" );
    }
}
