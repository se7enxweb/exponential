<?php
/**
 * The edit-conflict page (content/edit of an object that already has a draft) of the admin designs, checked on the
 * files alone (no database): its "New draft" button opens the editor of the new draft. The designs no longer post
 * DoNotEditAfterNewDraft, which made content/edit create the draft and redirect back to the same conflict page, so
 * the button seemed to do nothing. The kernel still honors the field for a design that wants the old behavior.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminEditConflictNewDraftTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminEditConflictNewDraftTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin3' => array( 'admin3' ), 'admin4' => array( 'admin4' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testNewDraftOpensTheEditorOfTheNewDraft( $design )
    {
        $tpl = self::read( 'design/' . $design . '/templates/content/edit_draft.tpl' );
        $this->assertStringContainsString( 'name="NewDraftButton"', $tpl );
        $this->assertDoesNotMatchRegularExpression( '/<input[^>]+name="DoNotEditAfterNewDraft"/', $tpl,
            'with DoNotEditAfterNewDraft content/edit redirects back to the conflict page instead of the editor' );
    }

    public function testTheKernelStillHonorsDoNotEditAfterNewDraft()
    {
        $view = self::read( 'kernel/private/classes/views/content/edit.php' );
        $this->assertStringContainsString( "hasPostVariable( 'DoNotEditAfterNewDraft' )", $view );
        $this->assertStringContainsString( "redirectToView( 'edit', array( \$ObjectID, \$version->attribute( 'version' ), \$EditLanguage ) )", $view );
    }
}
