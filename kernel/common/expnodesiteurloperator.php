<?php
/**
 * File containing the expNodeSiteURLOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Template operator exp_node_site_url: the address a node is published at on the site it belongs to, for the
 * "View on site" link of the administration.
 *
 *   {exp_node_site_url( $node )}              in the language the node is shown in
 *   {exp_node_site_url( $node, 'eng-GB' )}    in a given language
 *
 * - The siteaccess is the one the node belongs to (expSiteAccessForNode: the most specific content root that
 *   contains it, in the language), on its own host when it is matched by host; the path is the node's URL alias in
 *   that siteaccess's languages without its PathPrefix (expOrderSiteaccess::nodeURL()).
 * - A hidden node on a siteaccess that does not show hidden nodes is not on the site: the link is the preview of its
 *   current version in the administration (content/versionview, which checks content/versionread).
 * - site.ini [SiteAccessSettings] PreviewInOwningSiteAccess=disabled, or a node no siteaccess contains: the front
 *   page of the administration's site (siteaccess_url()) and the node's URL alias, as before 6.0.15.
 * Guide: doc/eng-us/features/6.0/preview-in-owning-siteaccess.md
 *
 * @since 6.0.15
 * @package kernel
 */
class expNodeSiteURLOperator
{
    public function operatorList()
    {
        return array( 'exp_node_site_url' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array( 'exp_node_site_url' => array(
            'node' => array( 'type' => 'object', 'required' => true, 'default' => null ),
            'language' => array( 'type' => 'string', 'required' => false, 'default' => '' ) ) );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $operatorValue = self::url( $namedParameters['node'], (string)$namedParameters['language'] );
    }

    /**
     * @param eZContentObjectTreeNode|mixed $node
     * @param string $language '' for the language the node is shown in
     * @return string '' for no node
     */
    public static function url( $node, $language = '' )
    {
        if ( !$node instanceof eZContentObjectTreeNode )
            return '';
        $before = rtrim( (string)ezpSiteAccessURL::front(), '/' ) . '/' . ltrim( (string)$node->attribute( 'url_alias' ), '/' );
        if ( !class_exists( 'expSiteAccessForNode' ) || !expSiteAccessForNode::previewInOwningSiteAccess() )
            return $before;
        if ( $language === '' )
            $language = (string)$node->currentLanguage();
        $object = $node->attribute( 'object' );
        if ( $language === '' && $object instanceof eZContentObject )
            $language = (string)$object->attribute( 'initial_language_code' );

        $owning = expSiteAccessForNode::forNode( $node, $language );
        if ( $owning['reason'] !== expSiteAccessForNode::REASON_TREE )
            return $before;
        $siteaccess = $owning['siteaccess'];

        if ( $node->attribute( 'is_invisible' ) && $object instanceof eZContentObject )
        {
            $siteIni = eZSiteAccess::getIni( $siteaccess, 'site.ini' );
            $showHidden = $siteIni->hasVariable( 'SiteAccessSettings', 'ShowHiddenNodes' )
                        && $siteIni->variable( 'SiteAccessSettings', 'ShowHiddenNodes' ) === 'true';
            if ( !$showHidden )
            {
                $preview = 'content/versionview/' . (int)$object->attribute( 'id' ) . '/' . (int)$object->attribute( 'current_version' )
                         . '/' . $language . '/site_access/' . $siteaccess;
                eZURI::transformURI( $preview );
                return $preview;
            }
        }

        $url = expOrderSiteaccess::nodeURL( $node, $siteaccess );
        return is_string( $url ) && $url !== '' ? $url : $before;
    }
}

?>
