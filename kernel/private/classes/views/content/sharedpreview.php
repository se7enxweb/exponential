<?php
/**
 * File containing the Exponential\View\Kernel\Content\Sharedpreview class: the view content/sharedpreview/<token>.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Content
{

/**
 * The preview of a draft through a share link (expContentPreviewLink), for a reviewer without an account: the
 * version the link names, in its language, in this siteaccess's design, with a notice that it is a draft and until
 * when the link works. No login (site.ini [RoleSettings] PolicyOmitList[]): the signed token is the permission, and
 * it shows that one version and nothing else; everything else on the page is what the visitor may see anyway.
 *
 * Never kept in a cache (the role-aware page cache, Velocity's response cache, proxies), never indexed, and the
 * address is not sent on as a referrer. A link that does not work answers 404 (not a link) or 410 (expired, revoked,
 * or the version is no draft any more) with a page that says so.
 */
class Sharedpreview extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        $token = isset( $scope['Params']['Token'] ) ? (string)$scope['Params']['Token'] : '';

        if ( class_exists( 'ezpRequestRuleKernel' ) )
            \ezpRequestRuleKernel::keepOutOfSharedCaches();
        else
            $GLOBALS['ezpRequestRuleNoStore'] = true;
        if ( !headers_sent() )
        {
            header( 'Cache-Control: private, no-store' );
            header( 'X-Robots-Tag: noindex, nofollow' );
            header( 'Referrer-Policy: no-referrer' );
        }

        $open = \expContentPreviewLink::open( $token );
        if ( isset( $open['error'] ) )
            return $this->unavailable( (string)$open['error'] );

        /** @var \eZContentObject $contentObject */
        $contentObject = $open['object'];
        /** @var \eZContentObjectVersion $versionObject */
        $versionObject = $open['version'];
        $link = $open['link'];
        $language = (string)$link['language'];
        $versionNumber = (int)$link['version'];
        $objectID = (int)$contentObject->attribute( 'id' );

        // the placement: the main node assignment of the version, as content/versionview has it
        $parentNodeID = false;
        $parentNodeObject = null;
        foreach ( (array)$versionObject->attribute( 'node_assignments' ) as $assignment )
        {
            if ( $assignment->attribute( 'is_main' ) == 1 || $parentNodeID === false )
            {
                $parentNodeID = (int)$assignment->attribute( 'parent_node' );
                $parentNodeObject = \eZContentObjectTreeNode::fetch( $parentNodeID );
            }
        }
        $virtualNodeID = $contentObject->attribute( 'main_node_id' ) ? (int)$contentObject->attribute( 'main_node_id' ) : null;

        $GLOBALS['eZContentObjectDefaultLanguage'] = $language;
        \eZContentObject::clearCache();
        $contentObject = \eZContentObject::fetch( $objectID );
        $contentObject->setAttribute( 'current_version', $versionNumber );
        $class = \eZContentClass::fetch( $contentObject->attribute( 'contentclass_id' ) );
        $objectName = $class->contentObjectName( $contentObject, $versionNumber, $language );
        $contentObject->setCachedName( $objectName );

        $node = new \eZContentObjectTreeNode();
        $node->setAttribute( 'contentobject_version', $versionNumber );
        $node->setAttribute( 'contentobject_id', $objectID );
        $node->setAttribute( 'parent_node_id', $parentNodeID );
        $node->setAttribute( 'main_node_id', $virtualNodeID );
        $node->setAttribute( 'node_id', $virtualNodeID );
        $depth = 2;
        if ( $parentNodeObject instanceof \eZContentObjectTreeNode )
        {
            $node->setAttribute( 'path_string', $parentNodeObject->attribute( 'path_string' ) . $virtualNodeID . '/' );
            $node->setAttribute( 'path_identification_string', $parentNodeObject->attribute( 'path_identification_string' ) );
            $depth = (int)$parentNodeObject->attribute( 'depth' ) + 1;
        }
        $node->setAttribute( 'depth', $depth );
        $node->setAttribute( 'sort_field', $class->attribute( 'sort_field' ) );
        $node->setAttribute( 'sort_order', $class->attribute( 'sort_order' ) );
        $node->setAttribute( 'remote_id', \eZRemoteIdUtility::generate( 'node' ) );
        $node->setName( $objectName );
        $node->setContentObject( $contentObject );

        $res = \eZTemplateDesignResource::instance();
        $designKeys = array( array( 'object', $objectID ), array( 'node', $virtualNodeID ),
                             array( 'remote_id', $contentObject->attribute( 'remote_id' ) ),
                             array( 'class', $class->attribute( 'id' ) ),
                             array( 'class_identifier', $class->attribute( 'identifier' ) ),
                             array( 'viewmode', 'full' ) );
        if ( $parentNodeID !== false )
            $designKeys[] = array( 'parent_node', $parentNodeID );
        if ( $parentNodeObject instanceof \eZContentObjectTreeNode )
            $designKeys[] = array( 'depth', $depth );
        $res->setKeys( $designKeys );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'view_mode', 'full' );
        $Result = \eZNodeviewfunctions::generateNodeViewData( $tpl, $node, $contentObject, $language, 'full', 0, array( 'offset' => 0 ) );

        // the notice above the content: a draft, not published, and until when the link works
        $notice = \eZTemplate::factory();
        $notice->setVariable( 'state', 'active' );
        $notice->setVariable( 'expires', (int)$link['expires'] );
        $notice->setVariable( 'object_name', $objectName );
        $Result['content'] = $notice->fetch( 'design:content/sharedpreview.tpl' ) . $Result['content'];
        $Result['requested_uri_string'] = 'content/sharedpreview';
        $Result['ui_context'] = 'view';
        unset( $Module );
        return $Result;
    }

    /**
     * The page of a link that does not work: 404 for no link, 410 for one that ended.
     *
     * @param string $reason invalid, expired, revoked, gone
     * @return array
     */
    protected function unavailable( $reason )
    {
        if ( !headers_sent() )
            http_response_code( $reason === 'invalid' ? 404 : 410 );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'state', $reason );
        $tpl->setVariable( 'expires', 0 );
        $tpl->setVariable( 'object_name', '' );
        $title = \ezpI18n::tr( 'kernel/content/sharedpreview', 'Preview not available' );
        return array( 'content' => $tpl->fetch( 'design:content/sharedpreview.tpl' ),
                      'path' => array( array( 'text' => $title, 'url' => false ) ),
                      'requested_uri_string' => 'content/sharedpreview' );
    }
}

}
