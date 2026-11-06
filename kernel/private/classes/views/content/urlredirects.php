<?php
/**
 * The redirects checked on the full request (content/urlredirects), policy content/urltranslator: the rows of
 * exp_url_redirect that URL map imports made (an old address with a query, a temporary status, 308, a target with a
 * query or on another site), read only apart from removing them:
 *
 *   /content/urlredirects[/(offset)/<n>]
 *
 * RemoveRedirectButton (value: one id) removes one, RemoveSelectedRedirectsButton the ticked ones
 * (RedirectIDList[]); the redirects' cache is cleared at once and the change is audited (content.urlalias.change).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 * @since 6.0.15
 */

namespace Exponential\View\Kernel\Content
{

class UrlRedirects extends \Exponential\Runnable\ModuleView
{
    const PAGE_SIZE = 50;

    /**
     * The ids a removal asks for: the one button's, else the ticked ones; whole positive numbers, each once. Pure.
     *
     * @param array $post
     * @return int[]
     */
    public static function removeIDs( array $post )
    {
        if ( isset( $post['RemoveRedirectButton'] ) )
            $ids = array( $post['RemoveRedirectButton'] );
        elseif ( isset( $post['RemoveSelectedRedirectsButton'] ) && isset( $post['RedirectIDList'] ) )
            $ids = (array)$post['RedirectIDList'];
        else
            return array();
        $out = array();
        foreach ( $ids as $id )
        {
            if ( is_scalar( $id ) && preg_match( '/^[1-9][0-9]{0,9}$/', (string)$id ) )
                $out[(int)$id] = (int)$id;
        }
        return array_values( $out );
    }

    public function run( array $scope )
    {
        $Params = $scope['Params'];

        $Module = $Params['Module'];
        $offset = isset( $Params['Offset'] ) && is_numeric( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;
        $tables = \expUrlRedirect::tableExists();

        if ( $tables && \expViewParameters::isPost() )
        {
            $ids = self::removeIDs( $_POST );
            $removed = 0;
            if ( $ids )
            {
                $db = \eZDB::instance();
                $db->begin();
                foreach ( $ids as $id )
                {
                    $redirect = \expUrlRedirect::fetch( $id );
                    if ( $redirect )
                    {
                        $redirect->remove();
                        $removed++;
                    }
                }
                $db->commit();
                \expUrlRedirect::expireCache();
                if ( $removed && class_exists( 'expAuditHook' ) )
                    \expAuditHook::emit( 'content.urlalias.change', array( 'object' => array( 'type' => 'redirect', 'id' => count( $ids ) === 1 ? $ids[0] : 'selected' ),
                                                                          'target' => null, 'verb' => 'remove',
                                                                          'before' => array( 'removed' => $ids ), 'after' => null ) );
            }
            \expViewParameters::redirect( 'content/urlredirects' . ( $offset ? '/(offset)/' . $offset : '' ) . ( $ids ? '/(removed)/' . $removed : '' ) );
        }

        $count = $tables ? (int)\eZPersistentObject::count( \expUrlRedirect::definition() ) : 0;
        if ( $offset >= $count )
            $offset = 0;
        $rows = array();
        foreach ( $tables ? \expUrlRedirect::fetchList( array(), $offset, self::PAGE_SIZE ) : array() as $redirect )
        {
            $row = $redirect->asArray();
            $row['source'] = $redirect->source();
            $rows[] = $row;
        }
        $userParameters = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'redirect_tables', $tables );
        $tpl->setVariable( 'redirects', $rows );
        $tpl->setVariable( 'redirect_count', $count );
        $tpl->setVariable( 'redirect_limit', self::PAGE_SIZE );
        $tpl->setVariable( 'redirect_removed', isset( $userParameters['removed'] ) && is_numeric( $userParameters['removed'] ) ? (int)$userParameters['removed'] : -1 );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );

        $title = \ezpI18n::tr( 'design/admin/content/urlimport', 'Redirects' );
        $Module->setTitle( $title );
        return $this->viewResult( array( 'content' => $tpl->fetch( 'design:content/urlredirects.tpl' ),
                                         'path' => array( array( 'url' => '/content/urltranslator', 'text' => \ezpI18n::tr( 'kernel/content/urlalias_global', 'Global URL aliases' ) ),
                                                          array( 'url' => false, 'text' => $title ) ) ), null );
    }
}

}
