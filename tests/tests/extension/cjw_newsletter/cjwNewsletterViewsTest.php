<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * Every view of the newsletter module, called in-process with valid, missing and invalid input. A view must end in
 * content, a redirect or a clean module error, never in a PHP fatal, warning or deprecation inside the extension.
 */
class cjwNewsletterViewsTest extends cjwNewsletterTestCase
{
    /** view => parameters that exist (filled in at run time where an id is needed) */
    public static function viewProvider()
    {
        return array(
            'index' => array( 'index' ), 'settings' => array( 'settings' ),
            'mailbox_item_list' => array( 'mailbox_item_list' ), 'mailbox_list' => array( 'mailbox_list' ),
            'blacklist_item_list' => array( 'blacklist_item_list' ), 'blacklist_item_add' => array( 'blacklist_item_add' ),
            'import_list' => array( 'import_list' ), 'user_list' => array( 'user_list' ),
            'user_create' => array( 'user_create' ), 'subscribe' => array( 'subscribe' ),
            'subscribe_infomail' => array( 'subscribe_infomail' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'viewProvider' )]
    public function testListAndFormViewsRenderWithoutParameters( $view )
    {
        $this->loginAdmin();
        $r = $this->runView( $view );
        $this->assertContains( $r['exit'], array( eZModule::STATUS_OK, eZModule::STATUS_REDIRECT ), $view );
        if ( $r['exit'] === eZModule::STATUS_OK )
            $this->assertNotEmpty( $r['content'], $view );
    }

    public static function idViewProvider()
    {
        return array(
            'mailbox_edit' => array( 'mailbox_edit' ), 'mailbox_item_view' => array( 'mailbox_item_view' ),
            'blacklist_item_remove' => array( 'blacklist_item_remove' ), 'import_view' => array( 'import_view' ),
            'user_view' => array( 'user_view' ), 'user_remove' => array( 'user_remove' ), 'user_edit' => array( 'user_edit' ),
            'subscription_list' => array( 'subscription_list' ), 'subscription_view' => array( 'subscription_view' ),
            'subscription_list_csvimport' => array( 'subscription_list_csvimport' ),
            'subscription_list_csvexport' => array( 'subscription_list_csvexport' ),
            'configure' => array( 'configure' ), 'unsubscribe' => array( 'unsubscribe' ),
            'preview_archive' => array( 'preview_archive' ), 'send' => array( 'send' ), 'send_abort' => array( 'send_abort' ),
            'archive' => array( 'archive' ), 'preview' => array( 'preview' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'idViewProvider' )]
    public function testViewsWithoutTheirIdEndCleanly( $view )
    {
        $r = $this->runView( $view );
        $this->assertViewClean( $r, $view );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'idViewProvider' )]
    public function testViewsWithUnknownIdsEndCleanly( $view )
    {
        foreach ( array( array( 999999999 ), array( 'nosuchhash' ), array( '0' ), array( -5 ), array( "a' OR '1'='1" ), array( '<script>' ) ) as $params )
        {
            $r = $this->runView( $view, array_pad( $params, 3, $params[0] ), array(), array( 'Debug' => '1' ) );
            $this->assertViewClean( $r, $view . ' ' . json_encode( $params ) );
        }
    }
}
